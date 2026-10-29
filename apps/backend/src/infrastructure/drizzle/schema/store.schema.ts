import { pgTable, uuid, varchar, text, numeric, integer, boolean, timestamp } from 'drizzle-orm/pg-core';
import { tenants, users } from './iam.schema';

/**
 * 商家/主理人品牌表 (单品牌多门店，统一LOGO聚合)
 */
export const shops = pgTable('shops', {
  id: uuid('id').primaryKey().defaultRandom(),
  tenantId: uuid('tenant_id').notNull().references(() => tenants.id),
  name: varchar('name', { length: 100 }).notNull(),
  logoUrl: varchar('logo_url', { length: 255 }).notNull(),
  cityId: uuid('city_id').references(() => tenants.id),
  ownerUserId: uuid('owner_user_id').notNull().references(() => users.id),
  salesMultiplier: numeric('sales_multiplier', { precision: 5, scale: 2 }).notNull().default('1.00'), // 曝光率系数 (比如 1:9 倍)
  wechatEnterpriseCorpId: varchar('wechat_corp_id', { length: 100 }),
  status: varchar('status', { length: 20 }).notNull().default('ACTIVE'),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 线下实体门店表 (PostGIS 空间检索支持)
 */
export const stores = pgTable('stores', {
  id: uuid('id').primaryKey().defaultRandom(),
  shopId: uuid('shop_id').notNull().references(() => shops.id, { onDelete: 'cascade' }),
  name: varchar('name', { length: 100 }).notNull(),
  phone: varchar('phone', { length: 30 }).notNull(),
  address: varchar('address', { length: 255 }).notNull(),
  latitude: numeric('latitude', { precision: 10, scale: 7 }).notNull(),
  longitude: numeric('longitude', { precision: 10, scale: 7 }).notNull(),
  status: varchar('status', { length: 20 }).notNull().default('OPEN'),
  cloudPrinterSn: varchar('cloud_printer_sn', { length: 50 }),
  cloudPrinterType: varchar('cloud_printer_type', { length: 30 }), // YILIANYUN, FEIE
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 企微私域群活码表 (阈值自动切码)
 */
export const wecomQrCodes = pgTable('wecom_qr_codes', {
  id: uuid('id').primaryKey().defaultRandom(),
  shopId: uuid('shop_id').notNull().references(() => shops.id, { onDelete: 'cascade' }),
  storeId: uuid('store_id').references(() => stores.id, { onDelete: 'cascade' }),
  qrCodeUrl: varchar('qr_code_url', { length: 255 }).notNull(),
  welcomeText: text('welcome_text').notNull(),
  maxLimit: integer('max_limit').notNull().default(200),
  currentCount: integer('current_count').notNull().default(0),
  sortOrder: integer('sort_order').notNull().default(0),
  isActive: boolean('is_active').notNull().default(true),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
