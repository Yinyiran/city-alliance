import { pgTable, uuid, varchar, numeric, integer, timestamp } from 'drizzle-orm/pg-core';
import { stores } from './store.schema';
import { members } from './member.schema';
import { orders } from './trade.schema';

/**
 * 门店扫码签到引流记录 (一店一月签到一次，商户扣2元，用户奖1元+积分/商品)
 */
export const storeSignInRecords = pgTable('store_sign_in_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  storeId: uuid('store_id').notNull().references(() => stores.id),
  memberId: uuid('member_id').notNull().references(() => members.id),
  yearMonth: varchar('year_month', { length: 7 }).notNull(), // YYYY-MM，用于保障一月仅签一次约束
  merchantDeductedAmount: numeric('merchant_deducted_amount', { precision: 10, scale: 2 }).notNull().default('2.00'),
  rewardCash: numeric('reward_cash', { precision: 10, scale: 2 }).notNull().default('1.00'),
  rewardPoints: integer('reward_points').notNull().default(50),
  giftOrderId: uuid('gift_order_id').references(() => orders.id), // 签到赠送商品直接生成的履约订单
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 逛一逛短视频领积分防刷打点记录
 */
export const videoProgressRecords = pgTable('video_progress_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  videoId: uuid('video_id').notNull(),
  memberId: uuid('member_id').notNull().references(() => members.id),
  watchedSeconds: integer('watched_seconds').notNull(),
  pointsAwarded: integer('points_awarded').notNull().default(0),
  nonce: varchar('nonce', { length: 64 }).notNull(),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
