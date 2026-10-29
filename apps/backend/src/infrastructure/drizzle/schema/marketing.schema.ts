import { pgTable, uuid, varchar, numeric, integer, timestamp } from 'drizzle-orm/pg-core';
import { shops } from './store.schema';
import { members } from './member.schema';
import { orders } from './trade.schema';

/**
 * 优惠券定义主表 (满减券、折扣券、通用/店铺专属)
 */
export const coupons = pgTable('coupons', {
  id: uuid('id').primaryKey().defaultRandom(),
  shopId: uuid('shop_id').references(() => shops.id, { onDelete: 'cascade' }), // null 为全平台通用券
  title: varchar('title', { length: 100 }).notNull(),
  couponType: varchar('coupon_type', { length: 30 }).notNull().default('CASH'), // CASH, DISCOUNT
  faceValue: numeric('face_value', { precision: 10, scale: 2 }).notNull(), // 面额或折扣 (如 0.85 = 85折)
  minOrderAmount: numeric('min_order_amount', { precision: 10, scale: 2 }).notNull().default('0.00'), // 满额门槛
  totalQuota: integer('total_quota').notNull().default(0), // 发放总量 (0为不限)
  issuedCount: integer('issued_count').notNull().default(0), // 已领用张数
  usedCount: integer('used_count').notNull().default(0), // 已核销张数
  validDays: integer('valid_days'), // 领券后有效天数
  validStartTime: timestamp('valid_start_time', { withTimezone: true }),
  validEndTime: timestamp('valid_end_time', { withTimezone: true }),
  status: varchar('status', { length: 20 }).notNull().default('ACTIVE'), // ACTIVE, DISABLED, EXPIRED
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 会员个人优惠券资产与核销表
 */
export const memberCoupons = pgTable('member_coupons', {
  id: uuid('id').primaryKey().defaultRandom(),
  memberId: uuid('member_id').notNull().references(() => members.id, { onDelete: 'cascade' }),
  couponId: uuid('coupon_id').notNull().references(() => coupons.id, { onDelete: 'cascade' }),
  couponCode: varchar('coupon_code', { length: 32 }).unique().notNull(),
  status: varchar('status', { length: 20 }).notNull().default('UNUSED'), // UNUSED, USED, EXPIRED
  usedOrderId: uuid('used_order_id').references(() => orders.id),
  usedAt: timestamp('used_at', { withTimezone: true }),
  expireAt: timestamp('expire_at', { withTimezone: true }).notNull(),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
