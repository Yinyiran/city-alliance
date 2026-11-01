import { Injectable, NotFoundException, BadRequestException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { tenants, shops, stores, orders, commissionRecords, users } from '../../infrastructure/drizzle/schema/index';
import { eq, and, sql, desc, inArray, like } from 'drizzle-orm';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Injectable()
export class CityService {
  constructor(
    @Inject(DRIZZLE_DB)
    private readonly db: DrizzleDatabase,
  ) {}

  /**
   * 获取当前用户绑定的城市分公司 ID
   */
  private resolveCityId(user: AuthenticatedUser): string {
    const cityId = user.cityId || user.tenantId;
    if (!cityId) {
      throw new BadRequestException('未检测到关联的城市分公司节点');
    }
    return cityId;
  }

  /**
   * 城市分公司数据大屏与经营罗盘概况
   */
  async getCityOverview(user: AuthenticatedUser) {
    const cityId = this.resolveCityId(user);

    // 1. 获取城市租户基础信息
    const cityTenantRes = await this.db
      .select()
      .from(tenants)
      .where(eq(tenants.id, cityId))
      .limit(1);

    const cityTenant = cityTenantRes[0] || {
      id: cityId,
      name: '城市分公司运营中心',
      code: 'CITY_CENTER',
      config: {},
    };

    // 2. 辖区主理人商家总数
    const shopCountRes = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(shops)
      .where(eq(shops.cityId, cityId));
    const shopCount = Number(shopCountRes[0]?.count || 0);

    // 3. 辖区实体门店总数
    const storeCountRes = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(stores)
      .innerJoin(shops, eq(stores.shopId, shops.id))
      .where(eq(shops.cityId, cityId));
    const storeCount = Number(storeCountRes[0]?.count || 0);

    // 4. 辖区自签/跨店总交易流水 (GMV)
    const gmvRes = await this.db
      .select({
        totalGmv: sql<string>`coalesce(sum(case when ${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED') then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
        monthGmv: sql<string>`coalesce(sum(case when (${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED')) and ${orders.createdAt} >= date_trunc('month', CURRENT_DATE) then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
        totalOrders: sql<number>`count(*)`,
      })
      .from(orders)
      .innerJoin(shops, eq(orders.shopId, shops.id))
      .where(eq(shops.cityId, cityId));

    const gmvStats = gmvRes[0];

    // 5. 分公司累计 25% 分润账本
    const commRes = await this.db
      .select({
        totalProfit: sql<string>`coalesce(sum(cast(${commissionRecords.amount} as numeric)), 0)`,
        settledProfit: sql<string>`coalesce(sum(case when ${commissionRecords.status} = 'SETTLED' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
        frozenProfit: sql<string>`coalesce(sum(case when ${commissionRecords.status} = 'FROZEN' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
      })
      .from(commissionRecords)
      .where(
        and(
          eq(commissionRecords.beneficiaryType, 'CITY_BRANCH'),
          eq(commissionRecords.beneficiaryId, cityId),
        ),
      );

    const commStats = commRes[0];

    // 6. 辖区 TOP 主理人商家业绩排行
    const monthTurnoverSql = sql<string>`coalesce(sum(case when (${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED')) and ${orders.createdAt} >= date_trunc('month', CURRENT_DATE) then cast(${orders.payAmount} as numeric) else 0 end), 0)`;

    const topShopsRows = await this.db
      .select({
        id: shops.id,
        name: shops.name,
        logoUrl: shops.logoUrl,
        storeCount: sql<number>`count(distinct ${stores.id})`,
        monthTurnover: monthTurnoverSql,
      })
      .from(shops)
      .leftJoin(stores, eq(shops.id, stores.shopId))
      .leftJoin(orders, eq(shops.id, orders.shopId))
      .where(eq(shops.cityId, cityId))
      .groupBy(shops.id, shops.name, shops.logoUrl)
      .orderBy(desc(monthTurnoverSql))
      .limit(6);

    return {
      cityInfo: {
        id: cityTenant.id,
        name: cityTenant.name,
        code: cityTenant.code,
        config: cityTenant.config,
      },
      stats: {
        shopCount,
        storeCount,
        totalGmv: Number(gmvStats?.totalGmv || 0).toFixed(2),
        monthGmv: Number(gmvStats?.monthGmv || 0).toFixed(2),
        totalOrders: Number(gmvStats?.totalOrders || 0),
        totalProfit: Number(commStats?.totalProfit || 0).toFixed(2),
        settledProfit: Number(commStats?.settledProfit || 0).toFixed(2),
        frozenProfit: Number(commStats?.frozenProfit || 0).toFixed(2),
        withdrawableBalance: Number(commStats?.settledProfit || 0).toFixed(2),
      },
      topMerchants: topShopsRows.map(r => ({
        id: r.id,
        name: r.name,
        logoUrl: r.logoUrl,
        storeCount: Number(r.storeCount || 0),
        monthTurnover: Number(r.monthTurnover || 0).toFixed(2),
      })),
    };
  }

  /**
   * 分页查询辖区主理人商家列表
   */
  async getCityShops(
    user: AuthenticatedUser,
    query: { keyword?: string; status?: string; page?: number; pageSize?: number },
  ) {
    const cityId = this.resolveCityId(user);
    const page = Math.max(1, Number(query.page) || 1);
    const pageSize = Math.min(100, Math.max(1, Number(query.pageSize) || 20));
    const offset = (page - 1) * pageSize;

    const conditions = [eq(shops.cityId, cityId)];
    if (query.status) {
      conditions.push(eq(shops.status, query.status));
    }
    if (query.keyword?.trim()) {
      conditions.push(like(shops.name, `%${query.keyword.trim()}%`));
    }

    const whereClause = and(...conditions);

    const rows = await this.db
      .select({
        id: shops.id,
        name: shops.name,
        logoUrl: shops.logoUrl,
        status: shops.status,
        salesMultiplier: shops.salesMultiplier,
        createdAt: shops.createdAt,
        ownerUsername: users.username,
        ownerMobile: users.mobile,
        storeCount: sql<number>`count(distinct ${stores.id})`,
        totalSales: sql<string>`coalesce(sum(case when ${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED') then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
      })
      .from(shops)
      .leftJoin(users, eq(shops.ownerUserId, users.id))
      .leftJoin(stores, eq(shops.id, stores.shopId))
      .leftJoin(orders, eq(shops.id, orders.shopId))
      .where(whereClause)
      .groupBy(shops.id, shops.name, shops.logoUrl, shops.status, shops.salesMultiplier, shops.createdAt, users.username, users.mobile)
      .orderBy(desc(shops.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countRes = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(shops)
      .where(whereClause);

    return {
      items: rows.map(r => ({
        ...r,
        storeCount: Number(r.storeCount || 0),
        totalSales: Number(r.totalSales || 0).toFixed(2),
      })),
      total: Number(countRes[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 城市分公司 25% 分润账本与流水
   */
  async getCityCommissions(
    user: AuthenticatedUser,
    query: { page?: number; pageSize?: number; status?: string },
  ) {
    const cityId = this.resolveCityId(user);
    const page = Math.max(1, Number(query.page) || 1);
    const pageSize = Math.min(100, Math.max(1, Number(query.pageSize) || 20));
    const offset = (page - 1) * pageSize;

    const conditions = [
      eq(commissionRecords.beneficiaryType, 'CITY_BRANCH'),
      eq(commissionRecords.beneficiaryId, cityId),
    ];
    if (query.status) {
      conditions.push(eq(commissionRecords.status, query.status));
    }

    const whereClause = and(...conditions);

    const rows = await this.db
      .select({
        id: commissionRecords.id,
        orderId: commissionRecords.orderId,
        orderNo: orders.orderNo,
        orderAmount: orders.totalAmount,
        commissionType: commissionRecords.commissionType,
        ratioPercent: commissionRecords.ratioPercent,
        amount: commissionRecords.amount,
        status: commissionRecords.status,
        unfreezeDueDate: commissionRecords.unfreezeDueDate,
        createdAt: commissionRecords.createdAt,
      })
      .from(commissionRecords)
      .leftJoin(orders, eq(commissionRecords.orderId, orders.id))
      .where(whereClause)
      .orderBy(desc(commissionRecords.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countRes = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(commissionRecords)
      .where(whereClause);

    return {
      items: rows,
      total: Number(countRes[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 获取分站设置
   */
  async getCityConfig(user: AuthenticatedUser) {
    const cityId = this.resolveCityId(user);
    const cityTenant = await this.db
      .select()
      .from(tenants)
      .where(eq(tenants.id, cityId))
      .limit(1);

    if (!cityTenant[0]) {
      throw new NotFoundException('城市分公司不存在');
    }

    const t = cityTenant[0];
    const cfg = (t.config as any) || {};

    return {
      id: t.id,
      name: t.name,
      code: t.code,
      servicePhone: cfg.servicePhone || '400-880-2026',
      contactPerson: cfg.contactPerson || '负责人',
      banners: cfg.banners || [
        { title: '同城年货盛典', url: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1200' },
        { title: '城市生活圈精选', url: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200' },
      ],
      navCategories: cfg.navCategories || ['美食精选', '休闲娱乐', '丽人美业', '生活服务', '商超零售'],
    };
  }

  /**
   * 更新分站设置
   */
  async updateCityConfig(user: AuthenticatedUser, payload: any) {
    const cityId = this.resolveCityId(user);

    await this.db
      .update(tenants)
      .set({
        config: {
          servicePhone: payload.servicePhone,
          contactPerson: payload.contactPerson,
          banners: payload.banners,
          navCategories: payload.navCategories,
        },
        updatedAt: new Date(),
      })
      .where(eq(tenants.id, cityId));

    return {
      success: true,
      cityId,
    };
  }
}
