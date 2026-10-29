import { z } from 'zod';

/**
 * 门店空间检索与地理位置
 */
export const SearchStoreNearbySchema = z.object({
  latitude: z.number().min(-90).max(90),
  longitude: z.number().min(-180).max(180),
  radiusKm: z.number().positive().default(50), // 搜索半径 (公里)
  brandId: z.string().uuid().optional(),
  categoryId: z.string().uuid().optional(),
  keyword: z.string().optional(),
  page: z.number().int().positive().default(1),
  pageSize: z.number().int().positive().default(20),
});
export type SearchStoreNearbyDto = z.infer<typeof SearchStoreNearbySchema>;

/**
 * 单品牌多连锁门店信息
 */
export const StoreDetailSchema = z.object({
  id: z.string().uuid(),
  shopId: z.string().uuid(),
  shopName: z.string(),
  brandLogo: z.string().url(),
  storeName: z.string(),
  phone: z.string(),
  address: z.string(),
  latitude: z.number(),
  longitude: z.number(),
  distanceMeters: z.number().optional(),
  fanCount: z.number().int().default(0),
  totalSales: z.number().int().default(0),
  displaySalesMultiplier: z.number().default(1.0), // 曝光率系数 (比如 1:9 倍)
  isFollowed: z.boolean().default(false),
  bannerUrls: z.array(z.string().url()).default([]),
});
export type StoreDetailDto = z.infer<typeof StoreDetailSchema>;

/**
 * 企微私域群活码轮换 Schema
 */
export const WeComGroupQROperationSchema = z.object({
  shopId: z.string().uuid(),
  storeId: z.string().uuid().optional(),
  qrCodeUrl: z.string().url(),
  welcomeText: z.string().max(500),
  maxMemberLimit: z.number().int().default(200), // 企微进群人数上限，达到阈值自动切码
  currentMemberCount: z.number().int().default(0),
  sortOrder: z.number().int().default(0),
  isActive: z.boolean().default(true),
});
export type WeComGroupQROperationDto = z.infer<typeof WeComGroupQROperationSchema>;
