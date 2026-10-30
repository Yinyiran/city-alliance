import { Injectable, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { orders, members, commissionRecords, shops, tenants } from '../../infrastructure/drizzle/schema/index';
import { eq, sql, inArray, gte } from 'drizzle-orm';

@Injectable()
export class DashboardService {
  constructor(
    @Inject(DRIZZLE_DB)
    private readonly db: DrizzleDatabase,
  ) {}

  /**
   * 聚合宏观经营大盘罗盘统计
   */
  async getOverview() {
    const todayStart = new Date();
    todayStart.setHours(0, 0, 0, 0);

    // 1. 全网成交总额 GMV 与今日 GMV
    const gmvRes = await this.db
      .select({
        totalGmv: sql<string>`coalesce(sum(case when ${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED') then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
        todayGmv: sql<string>`coalesce(sum(case when (${orders.payStatus} = 'PAID' or ${orders.orderStatus} in ('PAID', 'DELIVERING', 'COMPLETED')) and ${orders.createdAt} >= CURRENT_DATE then cast(${orders.payAmount} as numeric) else 0 end), 0)`,
        totalOrders: sql<number>`count(*)`,
        todayOrders: sql<number>`count(case when ${orders.createdAt} >= CURRENT_DATE then 1 end)`,
      })
      .from(orders);

    const stats = gmvRes[0];

    // 2. 会员体量与等级分布
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
    const commRes = await this.db
      .select({
        selfCommTotal: sql<string>`coalesce(sum(case when ${commissionRecords.commissionType} = 'DAILY_CONSUMPTION_SELF' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
        crossCommTotal: sql<string>`coalesce(sum(case when ${commissionRecords.commissionType} = 'DAILY_CONSUMPTION_CROSS' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
        partnerCommTotal: sql<string>`coalesce(sum(case when ${commissionRecords.commissionType} = 'PARTNER_FEE' or ${commissionRecords.commissionType} = 'MEMBER_UPGRADE' then cast(${commissionRecords.amount} as numeric) else 0 end), 0)`,
      })
      .from(commissionRecords);

    const commStats = commRes[0];

    // 4. 履约方式构成 (自提、外卖、预约、快递)
    const tradeTypeRes = await this.db
      .select({
        tradeType: orders.tradeType,
        count: sql<number>`count(*)`,
      })
      .from(orders)
      .groupBy(orders.tradeType);

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

    // 5. 商家与城市合伙人总数
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
        totalShops: Number(shopCountRes[0]?.count || 0),
        totalCities: Number(cityCountRes[0]?.count || 0),
      },
      fulfillmentDistribution: tradeDistribution,
    };
  }
}
