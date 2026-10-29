import { pgTable, uuid, varchar, text, numeric, integer, timestamp } from 'drizzle-orm/pg-core';
import { members } from './member.schema';
import { shops, stores } from './store.schema';
import { goodsSpu, goodsSku } from './catalog.schema';
import { users } from './iam.schema';

/**
 * 全渠道交易主订单表 (覆盖自提/到店/预约/快递/外卖)
 */
export const orders = pgTable('orders', {
  id: uuid('id').primaryKey().defaultRandom(),
  orderNo: varchar('order_no', { length: 64 }).unique().notNull(),
  customerId: uuid('customer_id').notNull().references(() => members.id),
  shopId: uuid('shop_id').notNull().references(() => shops.id),
  storeId: uuid('store_id').references(() => stores.id),
  tradeType: varchar('trade_type', { length: 30 }).notNull(), // TAKEOUT, PICKUP, APPOINTMENT, EXPRESS, QUICK_PAY
  orderStatus: varchar('order_status', { length: 30 }).notNull().default('PENDING_PAY'),
  payStatus: varchar('pay_status', { length: 20 }).notNull().default('UNPAID'),
  totalAmount: numeric('total_amount', { precision: 10, scale: 2 }).notNull(),
  discountAmount: numeric('discount_amount', { precision: 10, scale: 2 }).notNull().default('0.00'),
  payAmount: numeric('pay_amount', { precision: 10, scale: 2 }).notNull(),
  paymentChannel: varchar('payment_channel', { length: 30 }),
  verifyCode: varchar('verify_code', { length: 20 }), // 原子防重扫码核销码
  verifiedAt: timestamp('verified_at', { withTimezone: true }),
  verifyUserId: uuid('verify_user_id').references(() => users.id),
  appointmentTime: timestamp('appointment_time', { withTimezone: true }),
  deliveryInfo: text('delivery_info'), // 地址与收货人 JSON / 配送信息
  paidAt: timestamp('paid_at', { withTimezone: true }),
  completedAt: timestamp('completed_at', { withTimezone: true }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 订单明细项表
 */
export const orderItems = pgTable('order_items', {
  id: uuid('id').primaryKey().defaultRandom(),
  orderId: uuid('order_id').notNull().references(() => orders.id, { onDelete: 'cascade' }),
  spuId: uuid('spu_id').notNull().references(() => goodsSpu.id),
  skuId: uuid('sku_id').notNull().references(() => goodsSku.id),
  goodsName: varchar('goods_name', { length: 200 }).notNull(),
  specText: varchar('spec_text', { length: 200 }),
  price: numeric('price', { precision: 10, scale: 2 }).notNull(),
  quantity: integer('quantity').notNull().default(1),
  totalAmount: numeric('total_amount', { precision: 10, scale: 2 }).notNull(),
});

/**
 * 售后退款单表
 */
export const orderRefunds = pgTable('order_refunds', {
  id: uuid('id').primaryKey().defaultRandom(),
  orderId: uuid('order_id').notNull().references(() => orders.id),
  refundNo: varchar('refund_no', { length: 64 }).unique().notNull(),
  refundAmount: numeric('refund_amount', { precision: 10, scale: 2 }).notNull(),
  reason: text('reason').notNull(),
  status: varchar('status', { length: 20 }).notNull().default('PENDING_AUDIT'),
  arbitratedBy: uuid('arbitrated_by').references(() => users.id),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});
