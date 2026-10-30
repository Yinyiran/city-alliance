import { pgTable, uuid, varchar, text, numeric, integer, jsonb, timestamp } from 'drizzle-orm/pg-core';
import { shops } from './store.schema';

/**
 * 运费模板表 (按件/按重/包邮条件阶梯计费)
 */
export const freightTemplates = pgTable('freight_templates', {
  id: uuid('id').primaryKey().defaultRandom(),
  shopId: uuid('shop_id').notNull().references(() => shops.id, { onDelete: 'cascade' }),
  name: varchar('name', { length: 100 }).notNull(),
  chargeType: varchar('charge_type', { length: 20 }).notNull().default('BY_COUNT'), // BY_COUNT, BY_WEIGHT
  firstQuantity: integer('first_quantity').notNull().default(1),
  firstFee: numeric('first_fee', { precision: 10, scale: 2 }).notNull().default('0.00'),
  continueQuantity: integer('continue_quantity').notNull().default(1),
  continueFee: numeric('continue_fee', { precision: 10, scale: 2 }).notNull().default('0.00'),
  freeShippingThreshold: numeric('free_shipping_threshold', { precision: 10, scale: 2 }), // 满额包邮
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 商品 SPU 表 (外卖 / 自提 / 预约 / 快递 多履约类型标定)
 */
export const goodsSpu = pgTable('goods_spu', {
  id: uuid('id').primaryKey().defaultRandom(),
  shopId: uuid('shop_id').notNull().references(() => shops.id, { onDelete: 'cascade' }),
  name: text('name').notNull(),
  tradeType: varchar('trade_type', { length: 30 }).notNull(), // TAKEOUT, PICKUP, APPOINTMENT, EXPRESS
  categoryId: uuid('category_id'),
  coverImage: text('cover_image').notNull(),
  albumImages: text('album_images').array().notNull().default([]),
  description: text('description'),
  status: varchar('status', { length: 20 }).notNull().default('ON_SALE'), // ON_SALE, OFF_SALE
  basePrice: numeric('base_price', { precision: 10, scale: 2 }).notNull(),
  freightTemplateId: uuid('freight_template_id').references(() => freightTemplates.id),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 商品规格 SKU 矩阵表 (独立价格、库存与规格交叉映射)
 */
export const goodsSku = pgTable('goods_sku', {
  id: uuid('id').primaryKey().defaultRandom(),
  spuId: uuid('spu_id').notNull().references(() => goodsSpu.id, { onDelete: 'cascade' }),
  skuCode: varchar('sku_code', { length: 100 }),
  specValues: jsonb('spec_values').$type<Record<string, string>>().notNull(), // 规格交叉组合键值对
  price: numeric('price', { precision: 10, scale: 2 }).notNull(),
  stock: integer('stock').notNull().default(0),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});
