import { pgTable, uuid, varchar, numeric, integer, boolean, timestamp } from 'drizzle-orm/pg-core';
import { users } from './iam.schema';
import { shops, stores } from './store.schema';

/**
 * 会员总表 (支持6位数唯一会员编码与终身锁客关系)
 */
export const members = pgTable('members', {
  id: uuid('id').primaryKey().defaultRandom(),
  memberCode: varchar('member_code', { length: 6 }).unique().notNull(), // 6位数唯一会员ID
  userId: uuid('user_id').notNull().references(() => users.id, { onDelete: 'cascade' }),
  level: varchar('level', { length: 20 }).notNull().default('NORMAL'), // NORMAL, DIAMOND, BLACK_DIAMOND, PARTNER
  inviterId: uuid('inviter_id'), // 终身锁客上级会员
  bindShopId: uuid('bind_shop_id').references(() => shops.id), // 绑定的主理人商家
  bindStoreId: uuid('bind_store_id').references(() => stores.id), // 绑定的实体门店
  points: integer('points').notNull().default(0), // 积分
  balance: numeric('balance', { precision: 12, scale: 2 }).notNull().default('0.00'), // 可提现余额
  frozenBalance: numeric('frozen_balance', { precision: 12, scale: 2 }).notNull().default('0.00'), // 7天冻结待解冻资金
  totalEarned: numeric('total_earned', { precision: 12, scale: 2 }).notNull().default('0.00'), // 累计总收益
  realName: varchar('real_name', { length: 50 }),
  idCardNo: varchar('id_card_no', { length: 30 }),
  employmentType: varchar('employment_type', { length: 50 }).default('其它'),
  isRealNameVerified: boolean('is_real_name_verified').notNull().default(false),
  status: varchar('status', { length: 20 }).notNull().default('ACTIVE'),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 会员开通与升级记录 (邀约奖 + 主理人25% + 分公司25% + 总部剩余)
 */
export const memberUpgradeRecords = pgTable('member_upgrade_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  memberId: uuid('member_id').notNull().references(() => members.id),
  fromLevel: varchar('from_level', { length: 20 }).notNull(),
  toLevel: varchar('to_level', { length: 20 }).notNull(),
  payAmount: numeric('pay_amount', { precision: 10, scale: 2 }).notNull(),
  paymentChannel: varchar('payment_channel', { length: 30 }).notNull(),
  inviterId: uuid('inviter_id').references(() => members.id),
  inviterReward: numeric('inviter_reward', { precision: 10, scale: 2 }).notNull().default('0.00'),
  shopManagerReward: numeric('shop_manager_reward', { precision: 10, scale: 2 }).notNull().default('0.00'), // 主理人25%
  branchReward: numeric('branch_reward', { precision: 10, scale: 2 }).notNull().default('0.00'), // 城市分公司25%
  headquarterReward: numeric('headquarter_reward', { precision: 10, scale: 2 }).notNull().default('0.00'), // 总部留存
  isPartnerCustomGrant: boolean('is_partner_custom_grant').notNull().default(false), // 老板自主定价开通
  status: varchar('status', { length: 20 }).notNull().default('PAID'),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
