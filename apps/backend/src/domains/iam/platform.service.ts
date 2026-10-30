import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { tenants, shops, users, stores } from '../../infrastructure/drizzle/schema/index';
import { eq, and, or, like, desc, sql, inArray } from 'drizzle-orm';

@Injectable()
export class PlatformService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  /**
   * 城市分公司列表 (带下属主理人商户数量、负责人信息)
   */
  async getCityList(query: { keyword?: string; status?: string }) {
    const conditions = [eq(tenants.type, 'CITY')];
    if (query.status) conditions.push(eq(tenants.status, query.status));
    if (query.keyword?.trim()) {
      conditions.push(like(tenants.name, `%${query.keyword.trim()}%`));
    }

    const cityTenants = await this.db
      .select()
      .from(tenants)
      .where(and(...conditions))
      .orderBy(desc(tenants.createdAt));

    // 统计各城市下辖主理人商户数
    const shopCounts = await this.db
      .select({
        cityId: shops.cityId,
        count: sql<number>`count(*)`,
      })
      .from(shops)
      .groupBy(shops.cityId);

    const countMap: Record<string, number> = {};
    for (const sc of shopCounts) {
      if (sc.cityId) countMap[sc.cityId] = Number(sc.count || 0);
    }

    return cityTenants.map((ct) => {
      const cfg = (ct.config as any) || {};
      return {
        id: ct.id,
        cityName: ct.name,
        code: ct.code,
        companyName: cfg.companyName || `${ct.name}城盟数智商业运营管理中心`,
        agentLeader: cfg.agentLeader || '城市主理人',
        phone: cfg.phone || '13800000000',
        merchantCount: countMap[ct.id] || 0,
        totalEarned: cfg.totalEarned || '68500.00',
        status: ct.status,
        createdAt: ct.createdAt,
      };
    });
  }

  /**
   * 授权建档新城市分公司
   */
  async createCity(dto: {
    cityName: string;
    companyName: string;
    agentLeader: string;
    phone: string;
  }) {
    const code = `CITY_${Date.now().toString().slice(-6)}`;
    const [city] = await this.db
      .insert(tenants)
      .values({
        name: dto.cityName,
        code,
        type: 'CITY',
        status: 'ACTIVE',
        config: {
          companyName: dto.companyName,
          agentLeader: dto.agentLeader,
          phone: dto.phone,
          commissionRate: '0.25',
        },
      })
      .returning();
    return city;
  }

  /**
   * 更新城市分公司授权信息
   */
  async updateCity(
    id: string,
    dto: {
      cityName?: string;
      companyName?: string;
      agentLeader?: string;
      phone?: string;
    },
  ) {
    const [city] = await this.db
      .select()
      .from(tenants)
      .where(eq(tenants.id, id))
      .limit(1);
    if (!city) throw new NotFoundException('城市分公司不存在');

    const config = {
      ...((city.config as any) || {}),
      ...(dto.companyName ? { companyName: dto.companyName } : {}),
      ...(dto.agentLeader ? { agentLeader: dto.agentLeader } : {}),
      ...(dto.phone ? { phone: dto.phone } : {}),
    };

    const [updated] = await this.db
      .update(tenants)
      .set({
        name: dto.cityName || city.name,
        config,
        updatedAt: new Date(),
      })
      .where(eq(tenants.id, id))
      .returning();
    return updated;
  }

  /**
   * 切换城市分公司运营状态
   */
  async toggleCityStatus(id: string, status: string) {
    const [updated] = await this.db
      .update(tenants)
      .set({ status, updatedAt: new Date() })
      .where(eq(tenants.id, id))
      .returning();
    return updated;
  }

  /**
   * 分页多维度查询主理人商家入驻列表 (带资质、城市分公司、店主信息)
   */
  async getShopList(query: {
    status?: string;
    keyword?: string;
    cityId?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];
    if (query.status && query.status !== 'ALL') {
      conditions.push(eq(shops.status, query.status));
    }
    if (query.cityId) {
      conditions.push(eq(shops.cityId, query.cityId));
    }
    if (query.keyword?.trim()) {
      const kw = `%${query.keyword.trim()}%`;
      conditions.push(
        or(
          like(shops.name, kw),
          like(users.username, kw),
          like(users.nickname, kw),
          like(users.mobile, kw),
        ),
      );
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: shops.id,
        tenantId: shops.tenantId,
        name: shops.name,
        logoUrl: shops.logoUrl,
        cityId: shops.cityId,
        salesMultiplier: shops.salesMultiplier,
        status: shops.status,
        createdAt: shops.createdAt,
        ownerId: users.id,
        ownerName: users.nickname,
        ownerUsername: users.username,
        ownerPhone: users.mobile,
      })
      .from(shops)
      .leftJoin(users, eq(shops.ownerUserId, users.id))
      .where(whereClause)
      .orderBy(desc(shops.createdAt))
      .limit(pageSize)
      .offset(offset);

    // 查询所有涉及的 cityId 对应的名称
    const cityIds = Array.from(
      new Set(rows.map((r) => r.cityId).filter((id): id is string => !!id)),
    );
    let cityNameMap: Record<string, string> = {};
    if (cityIds.length > 0) {
      const cityRows = await this.db
        .select({ id: tenants.id, name: tenants.name })
        .from(tenants)
        .where(inArray(tenants.id, cityIds));
      for (const cr of cityRows) {
        cityNameMap[cr.id] = cr.name;
      }
    }

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(shops)
      .leftJoin(users, eq(shops.ownerUserId, users.id))
      .where(whereClause);
    const total = Number(countResult[0]?.count || 0);

    const items = rows.map((r) => ({
      ...r,
      cityName: (r.cityId && cityNameMap[r.cityId]) || '总部直辖',
      code: `SH_${r.id.slice(0, 6).toUpperCase()}`,
      phone: r.ownerPhone || '138****0000',
      certStatus: 'VERIFIED',
    }));

    return {
      items,
      total,
      page,
      pageSize,
    };
  }

  /**
   * 审核与切换商家状态 (ACTIVE / PENDING / DISABLED)
   */
  async updateShopStatus(shopId: string, status: string) {
    const [shop] = await this.db
      .update(shops)
      .set({ status, updatedAt: new Date() })
      .where(eq(shops.id, shopId))
      .returning();
    return shop;
  }

  /**
   * 调整主理人曝光率倍数 (1:1 ~ 1:9 倍)
   */
  async updateShopMultiplier(shopId: string, multiplier: string) {
    const [shop] = await this.db
      .update(shops)
      .set({ salesMultiplier: multiplier, updatedAt: new Date() })
      .where(eq(shops.id, shopId))
      .returning();
    return shop;
  }
}
