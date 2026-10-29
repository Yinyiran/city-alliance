import { pgTable, uuid, varchar, numeric, integer, timestamp } from 'drizzle-orm/pg-core';
import { members } from './member.schema';

/**
 * 会员财务余额变动台账 (双记账审计流水)
 */
export const balanceRecords = pgTable('balance_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  memberId: uuid('member_id').notNull().references(() => members.id, { onDelete: 'cascade' }),
  changeType: varchar('change_type', { length: 30 }).notNull(), // RECHARGE, CONSUME, REFUND, COMMISSION, WITHDRAW_FREEZE, WITHDRAW_SUCCESS, WITHDRAW_FAIL
  amount: numeric('amount', { precision: 12, scale: 2 }).notNull(), // 正数入账，负数扣除
  balanceBefore: numeric('balance_before', { precision: 12, scale: 2 }).notNull(),
  balanceAfter: numeric('balance_after', { precision: 12, scale: 2 }).notNull(),
  referenceNo: varchar('reference_no', { length: 64 }), // 关联订单号/退款单号/提现单号
  remark: varchar('remark', { length: 255 }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 会员积分变动明细台账
 */
export const pointsRecords = pgTable('points_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  memberId: uuid('member_id').notNull().references(() => members.id, { onDelete: 'cascade' }),
  changeType: varchar('change_type', { length: 30 }).notNull(), // SIGN_IN, CONSUME_AWARD, CONSUME_DEDUCT, SYSTEM_ADJUST
  points: integer('points').notNull(), // 正增负减
  pointsBefore: integer('points_before').notNull(),
  pointsAfter: integer('points_after').notNull(),
  referenceNo: varchar('reference_no', { length: 64 }),
  remark: varchar('remark', { length: 255 }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
