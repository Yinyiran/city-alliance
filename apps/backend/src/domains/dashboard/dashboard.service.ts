import { Injectable, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { orders, members, commissionRecords, shops, tenants, stores } from '../../infrastructure/drizzle/schema/index';
import { eq, and, sql } from 'drizzle-orm';
import { RoleEnum } from '@city-alliance/contracts';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Injectable()
export class DashboardService {
  constructor(
    @Inject(DRIZZLE_DB)
    private readonly db: DrizzleDatabase,
  ) {}

  /**
   * 聚合宏观经营大盘罗盘统计 (支持平台总部全网与商家主理人单店数据穿透)
   */
  async getOverview(currentUser?: AuthenticatedUser) {
    const isShopOwner = currentUser?.role === RoleEnum.SHOP_OWNER && currentUser?.shopId;
    const orderWhere = isShopOwner ? eq(orders.shopId, currentUser.shopId!) : undefined;

    // 1. 成交总额 GMV 与今日 GMV
    const gmvQuery = this.db
      .select({
        totalGmv: sql<string>`coalesce(sum(case when ${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED') then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
        todayGmv: sql<string>`coalesce(sum(case when (${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED')) and ${orders.createdAt} >= CURRENT_DATE then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
        totalOrders: sql<number>`count(*)`,
        todayOrders: sql<number>`count(case when ${orders.createdAt} >= CURRENT_DATE then 1 end)`,
      })
      .from(orders);

    const gmvRes = orderWhere ? await gmvQuery.where(orderWhere) : await gmvQuery;
    const stats = gmvRes[0];

    // 2. 会员体量与等级分布 (若为商家主理人，则统计在该商户消费或锁客会员)
    const memberRes = await this.db
      .select({
        totalMembers: sql<number>`count(*)`,
        partnerCount: sql<number>`count(case when ${members.level} = 'PARTNER' then 1 end)`,
        diamondCount: sql<number>`count(case when ${members.level} = 'DIAMOND' then 1 end)`,
        blackDiamondCount: sql<number>`count(case when ${members.level} = 'BLACK_DIAMOND' then 1 end)`,
      })
      .from(members);

    const memberStats = memberRes[0];

    // 3. 自签单 vs 跨店带单分润大盘
    let commWhere = undefined;
    if (isShopOwner) {
      commWhere = and(
        eq(commissionRecords.beneficiaryType, 'SHOP_OWNER'),
        eq(commissionRecords.beneficiaryId, currentUser.shopId!),
      );
    }

    const commQuery = this.db
      .select({
        selfCommTotal: sql<string>`coalesce(sum(case when ${commissionRecords.commissionType} = 'DAILY_CONSUMPTION_SELF' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
        crossCommTotal: sql<string>`coalesce(sum(case when ${commissionRecords.commissionType} = 'DAILY_CONSUMPTION_CROSS' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
        partnerCommTotal: sql<string>`coalesce(sum(case when ${commissionRecords.commissionType} = 'PARTNER_FEE' or ${commissionRecords.commissionType} = 'MEMBER_UPGRADE' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
      })
      .from(commissionRecords);

    const commRes = commWhere ? await commQuery.where(commWhere) : await commQuery;
    const commStats = commRes[0];

    // 4. 履约方式构成 (自提、外卖、预约、快递)
    const tradeTypeQuery = this.db
      .select({
        tradeType: orders.tradeType,
        count: sql<number>`count(*)`,
      })
      .from(orders);

    const tradeTypeRes = orderWhere
      ? await tradeTypeQuery.where(orderWhere).groupBy(orders.tradeType)
      : await tradeTypeQuery.groupBy(orders.tradeType);

    const tradeDistribution: Record<string, number> = {
      PICKUP: 0,
      APPOINTMENT: 0,
      EXPRESS: 0,
      TAKEOUT: 0,
    };
    for (const item of tradeTypeRes) {
      if (item.tradeType) {
        tradeDistribution[item.tradeType] = Number(item.count || 0);
      }
    }

    // 5. 实体门店或商家总数
    let totalStores = 0;
    if (isShopOwner) {
      const storesRes = await this.db
        .select({ count: sql<number>`count(*)` })
        .from(stores)
        .where(eq(stores.shopId, currentUser.shopId!));
      totalStores = Number(storesRes[0]?.count || 0);
    }

    const shopCountRes = await this.db.select({ count: sql<number>`count(*)` }).from(shops);
    const cityCountRes = await this.db.select({ count: sql<number>`count(*)` }).from(tenants);

    return {
      gmv: {
        total: Number(stats?.totalGmv || 0).toFixed(2),
        today: Number(stats?.todayGmv || 0).toFixed(2),
        totalOrders: Number(stats?.totalOrders || 0),
        todayOrders: Number(stats?.todayOrders || 0),
      },
      members: {
        total: Number(memberStats?.totalMembers || 0),
        partners: Number(memberStats?.partnerCount || 0),
        diamonds: Number(memberStats?.diamondCount || 0),
        blackDiamonds: Number(memberStats?.blackDiamondCount || 0),
      },
      commissions: {
        selfSignTotal: Number(commStats?.selfCommTotal || 0).toFixed(2),
        crossChainTotal: Number(commStats?.crossCommTotal || 0).toFixed(2),
        partnerTotal: Number(commStats?.partnerCommTotal || 0).toFixed(2),
      },
      merchants: {
        totalShops: isShopOwner ? 1 : Number(shopCountRes[0]?.count || 0),
        totalCities: isShopOwner ? totalStores : Number(cityCountRes[0]?.count || 0),
      },
      fulfillmentDistribution: tradeDistribution,
    };
  }
}
