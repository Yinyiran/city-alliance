import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import {
  shops,
  stores,
  wecomQrCodes,
  storeClerks,
  cashierShifts,
  users,
  orders,
} from '../../infrastructure/drizzle/schema/index';
import { eq, and, or, like, desc, sql } from 'drizzle-orm';
import { SearchStoreNearbyDto } from '@city-alliance/contracts';

@Injectable()
export class StoreService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  /**
   * 基于 Haversine 球面大圆算法精确计算两点经纬度距离 (米)
   */
  calculateDistanceMeters(
    lat1: number,
    lon1: number,
    lat2: number,
    lon2: number,
  ): number {
    const R = 6371000; // 地球平均半径 (米)
    const radLat1 = (lat1 * Math.PI) / 180;
    const radLat2 = (lat2 * Math.PI) / 180;
    const deltaLat = ((lat2 - lat1) * Math.PI) / 180;
    const deltaLon = ((lon2 - lon1) * Math.PI) / 180;

    const a =
      Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
      Math.cos(radLat1) *
        Math.cos(radLat2) *
        Math.sin(deltaLon / 2) *
        Math.sin(deltaLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return Math.round(R * c);
  }

  /**
   * 检索附近同城门店并根据距离正序排列 (支持单品牌多门店聚合与曝光率系数)
   */
  async searchNearbyStores(dto: SearchStoreNearbyDto) {
    const storeList = await this.db
      .select({
        storeId: stores.id,
        storeName: stores.name,
        phone: stores.phone,
        address: stores.address,
        latitude: stores.latitude,
        longitude: stores.longitude,
        status: stores.status,
        shopId: shops.id,
        shopName: shops.name,
        brandLogo: shops.logoUrl,
        salesMultiplier: shops.salesMultiplier,
      })
      .from(stores)
      .innerJoin(shops, eq(stores.shopId, shops.id))
      .where(eq(stores.status, 'OPEN'));

    // 计算实际空间距离并过滤超出搜索半径的门店
    const withDistance = storeList.map((st) => {
      const lat = Number(st.latitude);
      const lon = Number(st.longitude);
      const distance = this.calculateDistanceMeters(
        dto.latitude,
        dto.longitude,
        lat,
        lon,
      );
      const multiplier = Number(st.salesMultiplier) || 1.0;

      return {
        ...st,
        distanceMeters: distance,
        distanceKm: Number((distance / 1000).toFixed(2)),
        // 按照设置的系数计算曝光/展示热度
        displayHeat: Math.round(100 * multiplier),
      };
    });

    const filtered = withDistance.filter(
      (st) => st.distanceMeters <= dto.radiusKm * 1000,
    );

    // 距离由近及远排序
    filtered.sort((a, b) => a.distanceMeters - b.distanceMeters);

    const start = (dto.page - 1) * dto.pageSize;
    const paged = filtered.slice(start, start + dto.pageSize);

    return {
      total: filtered.length,
      page: dto.page,
      pageSize: dto.pageSize,
      items: paged,
    };
  }

  /**
   * 获取单品牌详情与其所有连锁分店列表 (单品牌多门店，统一LOGO聚合展示)
   */
  async getBrandWithBranches(shopId: string, userLat?: number, userLon?: number) {
    const shopRecords = await this.db
      .select()
      .from(shops)
      .where(eq(shops.id, shopId))
      .limit(1);

    const shop = shopRecords[0];
    if (!shop) {
      throw new NotFoundException('该品牌商户不存在');
    }

    const branchStores = await this.db
      .select()
      .from(stores)
      .where(and(eq(stores.shopId, shopId), eq(stores.status, 'OPEN')));

    const branchesWithDist = branchStores.map((st) => {
      let distanceMeters: number | undefined = undefined;
      if (userLat !== undefined && userLon !== undefined) {
        distanceMeters = this.calculateDistanceMeters(
          userLat,
          userLon,
          Number(st.latitude),
          Number(st.longitude),
        );
      }
      return {
        ...st,
        distanceMeters,
      };
    });

    if (userLat !== undefined && userLon !== undefined) {
      branchesWithDist.sort(
        (a, b) => (a.distanceMeters || 0) - (b.distanceMeters || 0),
      );
    }

    return {
      brand: shop,
      branches: branchesWithDist,
    };
  }

  /**
   * 企微私域群活码自动轮换引擎
   * (当扫码入群人数达到 maxLimit 阈值时，自动切码到下一张活码)
   */
  async getActiveWeComGroupQr(shopId: string, storeId?: string) {
    // 优先匹配当前可用的群活码 (按排序权重获取 currentCount < maxLimit 的活码)
    const qrList = await this.db
      .select()
      .from(wecomQrCodes)
      .where(
        and(
          eq(wecomQrCodes.shopId, shopId),
          eq(wecomQrCodes.isActive, true),
        ),
      )
      .orderBy(wecomQrCodes.sortOrder);

    // 查找首个尚未达到入群上限的活码
    const availableQr = qrList.find((qr) => qr.currentCount < qr.maxLimit);

    if (availableQr) {
      // 记录一次进群访问曝光计数
      await this.db
        .update(wecomQrCodes)
        .set({
          currentCount: availableQr.currentCount + 1,
        })
        .where(eq(wecomQrCodes.id, availableQr.id));

      return availableQr;
    }

    // 若全部满员，降级返回排序最高的备用活码或提示
    return qrList[qrList.length - 1] || null;
  }

  /**
   * 门店全能组件：关注/取消关注门店
   */
  async toggleFollowStore(storeId: string, isFollow: boolean) {
    const storeList = await this.db
      .select()
      .from(stores)
      .where(eq(stores.id, storeId))
      .limit(1);

    if (!storeList[0]) {
      throw new NotFoundException('门店不存在');
    }

    return {
      storeId,
      isFollowed: isFollow,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * 分页多维度查询门店列表 (支持品牌检索、状态筛选)
   */
  async getStoreList(query: {
    keyword?: string;
    status?: string;
    shopId?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];

    if (query.keyword?.trim()) {
      const kw = `%${query.keyword.trim()}%`;
      conditions.push(
        or(
          like(stores.name, kw),
          like(stores.address, kw),
          like(stores.phone, kw),
          like(shops.name, kw),
        ),
      );
    }

    if (query.status) {
      conditions.push(eq(stores.status, query.status));
    }

    if (query.shopId) {
      conditions.push(eq(stores.shopId, query.shopId));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: stores.id,
        shopId: stores.shopId,
        shopName: shops.name,
        brandLogo: shops.logoUrl,
        salesMultiplier: shops.salesMultiplier,
        storeName: stores.name,
        phone: stores.phone,
        address: stores.address,
        latitude: stores.latitude,
        longitude: stores.longitude,
        status: stores.status,
        cloudPrinterSn: stores.cloudPrinterSn,
        cloudPrinterType: stores.cloudPrinterType,
        createdAt: stores.createdAt,
      })
      .from(stores)
      .leftJoin(shops, eq(stores.shopId, shops.id))
      .where(whereClause)
      .orderBy(desc(stores.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(stores)
      .leftJoin(shops, eq(stores.shopId, shops.id))
      .where(whereClause);

    const total = Number(countResult[0]?.count || 0);

    return {
      items: rows,
      total,
      page,
      pageSize,
    };
  }

  async createStore(data: {
    shopId: string;
    name: string;
    phone: string;
    address: string;
    latitude: string;
    longitude: string;
    cloudPrinterSn?: string;
    cloudPrinterType?: string;
  }) {
    const [store] = await this.db
      .insert(stores)
      .values({
        shopId: data.shopId,
        name: data.name,
        phone: data.phone,
        address: data.address,
        latitude: data.latitude,
        longitude: data.longitude,
        cloudPrinterSn: data.cloudPrinterSn || null,
        cloudPrinterType: data.cloudPrinterType || null,
        status: 'OPEN',
      })
      .returning();
    return store;
  }

  async updateStore(
    id: string,
    data: {
      name?: string;
      phone?: string;
      address?: string;
      latitude?: string;
      longitude?: string;
      cloudPrinterSn?: string;
      cloudPrinterType?: string;
    },
  ) {
    const [store] = await this.db
      .update(stores)
      .set({
        ...data,
        updatedAt: new Date(),
      })
      .where(eq(stores.id, id))
      .returning();
    return store;
  }

  async toggleStoreStatus(id: string, status: string) {
    const [store] = await this.db
      .update(stores)
      .set({
        status,
        updatedAt: new Date(),
      })
      .where(eq(stores.id, id))
      .returning();
    return store;
  }

  /**
   * 分页查询门店店员与核销员列表
   */
  async getClerksList(query: {
    storeId?: string;
    shopId?: string;
    keyword?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];

    if (query.storeId) {
      conditions.push(eq(storeClerks.storeId, query.storeId));
    }
    if (query.shopId) {
      conditions.push(eq(stores.shopId, query.shopId));
    }
    if (query.keyword?.trim()) {
      const kw = `%${query.keyword.trim()}%`;
      conditions.push(
        or(
          like(storeClerks.realName, kw),
          like(storeClerks.phone, kw),
          like(storeClerks.clerkNo, kw),
        ),
      );
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: storeClerks.id,
        storeId: storeClerks.storeId,
        storeName: stores.name,
        userId: storeClerks.userId,
        username: users.username,
        clerkNo: storeClerks.clerkNo,
        realName: storeClerks.realName,
        phone: storeClerks.phone,
        role: storeClerks.role,
        permissions: storeClerks.permissions,
        verifyCount: storeClerks.verifyCount,
        status: storeClerks.status,
        createdAt: storeClerks.createdAt,
      })
      .from(storeClerks)
      .innerJoin(stores, eq(storeClerks.storeId, stores.id))
      .leftJoin(users, eq(storeClerks.userId, users.id))
      .where(whereClause)
      .orderBy(desc(storeClerks.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(storeClerks)
      .innerJoin(stores, eq(storeClerks.storeId, stores.id))
      .where(whereClause);

    return {
      items: rows,
      total: Number(countResult[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 添加门店店员/核销员
   */
  async createClerk(data: {
    storeId: string;
    realName: string;
    phone: string;
    clerkNo?: string;
    role: string;
    permissions?: string[];
  }) {
    const clerkNo = data.clerkNo || `CK${Date.now().toString().slice(-6)}`;

    let userRow = (
      await this.db
        .select()
        .from(users)
        .where(eq(users.username, data.phone))
        .limit(1)
    )[0];

    if (!userRow) {
      const crypto = await import('crypto');
      const salt = 'city_alliance_salt_2026_xyz';
      const defaultHash = crypto
        .createHash('sha256')
        .update('123456' + salt)
        .digest('hex');

      const [newUser] = await this.db
        .insert(users)
        .values({
          username: data.phone,
          passwordHash: defaultHash,
          nickname: data.realName,
          mobile: data.phone,
          status: 'ACTIVE',
        })
        .returning();
      userRow = newUser;
    }

    const [clerk] = await this.db
      .insert(storeClerks)
      .values({
        storeId: data.storeId,
        userId: userRow.id,
        clerkNo,
        realName: data.realName,
        phone: data.phone,
        role: data.role || 'CASHIER',
        permissions: data.permissions || ['trade:verify', 'shift:manage'],
        status: 'ACTIVE',
      })
      .returning();

    return clerk;
  }

  /**
   * 分页查询交接班对账流水
   */
  async getShiftsList(query: {
    storeId?: string;
    status?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];
    if (query.storeId) {
      conditions.push(eq(cashierShifts.storeId, query.storeId));
    }
    if (query.status) {
      conditions.push(eq(cashierShifts.status, query.status));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: cashierShifts.id,
        storeId: cashierShifts.storeId,
        storeName: stores.name,
        clerkId: cashierShifts.clerkId,
        clerkName: storeClerks.realName,
        clerkNo: storeClerks.clerkNo,
        shiftStart: cashierShifts.shiftStart,
        shiftEnd: cashierShifts.shiftEnd,
        cashTotal: cashierShifts.cashTotal,
        scanTotal: cashierShifts.scanTotal,
        orderCount: cashierShifts.orderCount,
        diffAmount: cashierShifts.diffAmount,
        status: cashierShifts.status,
        remark: cashierShifts.remark,
        createdAt: cashierShifts.createdAt,
      })
      .from(cashierShifts)
      .innerJoin(stores, eq(cashierShifts.storeId, stores.id))
      .leftJoin(storeClerks, eq(cashierShifts.clerkId, storeClerks.id))
      .where(whereClause)
      .orderBy(desc(cashierShifts.shiftStart))
      .limit(pageSize)
      .offset(offset);

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(cashierShifts)
      .where(whereClause);

    return {
      items: rows,
      total: Number(countResult[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 获取门店当前生效的班次及今日核销统计
   */
  async getCurrentShift(storeId: string) {
    const [activeShift] = await this.db
      .select({
        id: cashierShifts.id,
        storeId: cashierShifts.storeId,
        storeName: stores.name,
        clerkId: cashierShifts.clerkId,
        clerkName: storeClerks.realName,
        clerkNo: storeClerks.clerkNo,
        shiftStart: cashierShifts.shiftStart,
        cashTotal: cashierShifts.cashTotal,
        scanTotal: cashierShifts.scanTotal,
        orderCount: cashierShifts.orderCount,
        status: cashierShifts.status,
        remark: cashierShifts.remark,
      })
      .from(cashierShifts)
      .innerJoin(stores, eq(cashierShifts.storeId, stores.id))
      .leftJoin(storeClerks, eq(cashierShifts.clerkId, storeClerks.id))
      .where(and(eq(cashierShifts.storeId, storeId), eq(cashierShifts.status, 'OPEN')))
      .orderBy(desc(cashierShifts.shiftStart))
      .limit(1);

    const todayStats = await this.db
      .select({
        todayVerifyCount: sql<number>`count(*)`,
        todayVerifyAmount: sql<string>`coalesce(sum(cast(${orders.payAmount} as numeric)), 0)`,
      })
      .from(orders)
      .where(
        and(
          eq(orders.storeId, storeId),
          eq(orders.orderStatus, 'COMPLETED'),
          sql`${orders.updatedAt} >= CURRENT_DATE`,
        ),
      );

    return {
      currentShift: activeShift || null,
      todaySummary: {
        verifyCount: Number(todayStats[0]?.todayVerifyCount || 0),
        verifyAmount: Number(todayStats[0]?.todayVerifyAmount || 0).toFixed(2),
      },
    };
  }

  /**
   * 开班打卡签到
   */
  async openShift(data: { storeId: string; clerkId: string; remark?: string }) {
    const existing = await this.db
      .select()
      .from(cashierShifts)
      .where(and(eq(cashierShifts.storeId, data.storeId), eq(cashierShifts.status, 'OPEN')))
      .limit(1);

    if (existing[0]) {
      return existing[0];
    }

    const [shift] = await this.db
      .insert(cashierShifts)
      .values({
        storeId: data.storeId,
        clerkId: data.clerkId,
        shiftStart: new Date(),
        cashTotal: '0.00',
        scanTotal: '0.00',
        orderCount: 0,
        diffAmount: '0.00',
        status: 'OPEN',
        remark: data.remark || '开班打卡',
      })
      .returning();

    return shift;
  }

  /**
   * 交班轧账打单
   */
  async closeShift(
    shiftId: string,
    data: {
      cashTotal: number;
      scanTotal: number;
      diffAmount?: number;
      remark?: string;
    },
  ) {
    const [closed] = await this.db
      .update(cashierShifts)
      .set({
        cashTotal: String(data.cashTotal || 0),
        scanTotal: String(data.scanTotal || 0),
        diffAmount: String(data.diffAmount || 0),
        status: 'CLOSED',
        shiftEnd: new Date(),
        remark: data.remark || null,
      })
      .where(eq(cashierShifts.id, shiftId))
      .returning();

    return closed;
  }

  /**
   * 获取指定门店概况统计
   */
  async getStoreStats(storeId: string) {
    const storeInfo = (
      await this.db
        .select({
          id: stores.id,
          name: stores.name,
          phone: stores.phone,
          address: stores.address,
          status: stores.status,
          cloudPrinterSn: stores.cloudPrinterSn,
          shopName: shops.name,
        })
        .from(stores)
        .leftJoin(shops, eq(stores.shopId, shops.id))
        .where(eq(stores.id, storeId))
        .limit(1)
    )[0];

    const clerkCount =
      (
        await this.db
          .select({ count: sql<number>`count(*)` })
          .from(storeClerks)
          .where(eq(storeClerks.storeId, storeId))
      )[0]?.count || 0;

    const orderStats = (
      await this.db
        .select({
          totalVerified: sql<number>`count(*)`,
          totalAmount: sql<string>`coalesce(sum(cast(${orders.payAmount} as numeric)), 0)`,
        })
        .from(orders)
        .where(and(eq(orders.storeId, storeId), eq(orders.orderStatus, 'COMPLETED')))
    )[0];

    return {
      store: storeInfo,
      clerkCount: Number(clerkCount),
      totalVerified: Number(orderStats?.totalVerified || 0),
      totalAmount: Number(orderStats?.totalAmount || 0).toFixed(2),
    };
  }
}
