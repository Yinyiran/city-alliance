import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { shops, stores, wecomQrCodes } from '../../infrastructure/drizzle/schema/index';
import { eq, and, sql } from 'drizzle-orm';
import { SearchStoreNearbyDto, WeComGroupQROperationDto } from '@city-alliance/contracts';

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
}
