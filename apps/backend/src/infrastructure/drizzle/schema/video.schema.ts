import { pgTable, uuid, varchar, text, integer, numeric, timestamp } from 'drizzle-orm/pg-core';
import { shops } from './store.schema';
import { goodsSpu } from './catalog.schema';

/**
 * 竖屏短视频内容流与挂载带货商品表
 * 支持 30 秒看播时钟防刷奖励与带货卡片挂载
 */
export const videos = pgTable('videos', {
  id: uuid('id').primaryKey().defaultRandom(),
  legacyId: integer('legacy_id'),
  title: varchar('title', { length: 255 }).notNull(),
  coverUrl: varchar('cover_url', { length: 500 }),
  videoUrl: varchar('video_url', { length: 500 }).notNull(),
  shopId: uuid('shop_id').references(() => shops.id),
  goodsId: uuid('goods_id').references(() => goodsSpu.id),
  goodsName: varchar('goods_name', { length: 255 }),
  goodsPrice: numeric('goods_price', { precision: 10, scale: 2 }),
  rewardPoints: numeric('reward_points', { precision: 10, scale: 2 }).notNull().default('5.00'),
  rewardSeconds: integer('reward_seconds').notNull().default(30), // 满 30 秒防刷时钟打点奖励
  viewCount: integer('view_count').notNull().default(0),
  likeCount: integer('like_count').notNull().default(0),
  shareCount: integer('share_count').notNull().default(0),
  status: varchar('status', { length: 20 }).notNull().default('APPROVED'), // PENDING, APPROVED, REJECTED, OFFLINE
  sortOrder: integer('sort_order').notNull().default(0),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});
