import { pgTable, uuid, varchar, text, numeric, timestamp } from 'drizzle-orm/pg-core';
import { orders } from './trade.schema';
import { members } from './member.schema';
import { users } from './iam.schema';

/**
 * 佣金清算账本表 (自签单2% vs 带单6% vs 会员分成，7天状态机延迟解冻)
 */
export const commissionRecords = pgTable('commission_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  orderId: uuid('order_id').references(() => orders.id),
  commissionType: varchar('commission_type', { length: 50 }).notNull(), // MEMBER_UPGRADE, DAILY_CONSUMPTION_SELF, DAILY_CONSUMPTION_CROSS, PARTNER_FEE
  beneficiaryId: uuid('beneficiary_id').notNull(), // 会员ID 或 租户ID (城市分公司/平台总部)
  beneficiaryType: varchar('beneficiary_type', { length: 30 }).notNull(), // MEMBER, SHOP_OWNER, CITY_BRANCH, HEADQUARTERS
  ratioPercent: numeric('ratio_percent', { precision: 6, scale: 3 }).notNull(),
  amount: numeric('amount', { precision: 10, scale: 2 }).notNull(),
  status: varchar('status', { length: 20 }).notNull().default('FROZEN'), // FROZEN, UNFROZEN, SETTLED, REVOKED
  unfreezeDueDate: timestamp('unfreeze_due_date', { withTimezone: true }), // 7天消费保护到期时间
  unfrozenAt: timestamp('unfrozen_at', { withTimezone: true }),
  revokedAt: timestamp('revoked_at', { withTimezone: true }),
  revokeReason: text('revoke_reason'),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 提现审核与放款记录 (手续费 0.35% + 阶梯劳务个税代扣代缴)
 */
export const withdrawRecords = pgTable('withdraw_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  applicantId: uuid('applicant_id').notNull(), // 会员 ID 或 商家租户 ID
  applicantType: varchar('applicant_type', { length: 20 }).notNull(), // MEMBER, MERCHANT
  grossAmount: numeric('gross_amount', { precision: 10, scale: 2 }).notNull(), // 提现申请毛额
  platformFee: numeric('platform_fee', { precision: 10, scale: 2 }).notNull().default('0.00'), // 0.35% 手续费
  taxAmount: numeric('tax_amount', { precision: 10, scale: 2 }).notNull().default('0.00'), // 劳务报酬个税
  netAmount: numeric('net_amount', { precision: 10, scale: 2 }).notNull(), // 实际到账净额
  channel: varchar('channel', { length: 30 }).notNull(), // WECHAT, BANK_CARD, ALIPAY
  realName: varchar('real_name', { length: 50 }).notNull(),
  idCardNo: varchar('id_card_no', { length: 30 }).notNull(),
  bankCardNo: varchar('bank_card_no', { length: 50 }),
  status: varchar('status', { length: 20 }).notNull().default('AUDITING'), // AUDITING, PAYING, SUCCESS, REJECTED
  auditNote: text('audit_note'),
  auditedBy: uuid('audited_by').references(() => users.id),
  auditedAt: timestamp('audited_at', { withTimezone: true }),
  paidAt: timestamp('paid_at', { withTimezone: true }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 劳务报酬个税代扣代缴档案表 (合规税务凭证)
 */
export const taxWithholdingRecords = pgTable('tax_withholding_records', {
  id: uuid('id').primaryKey().defaultRandom(),
  withdrawId: uuid('withdraw_id').notNull().references(() => withdrawRecords.id),
  memberId: uuid('member_id').notNull().references(() => members.id),
  grossAmount: numeric('gross_amount', { precision: 10, scale: 2 }).notNull(),
  costDeduction: numeric('cost_deduction', { precision: 10, scale: 2 }).notNull().default('0.00'), // 20% 法定扣除成本
  taxableIncome: numeric('taxable_income', { precision: 10, scale: 2 }).notNull(), // 应纳税所得额
  taxRatePercent: numeric('tax_rate_percent', { precision: 5, scale: 2 }).notNull(), // 20% / 30% / 40%
  quickDeduction: numeric('quick_deduction', { precision: 10, scale: 2 }).notNull().default('0.00'), // 0 / 2000 / 7000
  taxWithheld: numeric('tax_withheld', { precision: 10, scale: 2 }).notNull(), // 实扣税额
  taxPeriod: varchar('tax_period', { length: 7 }).notNull(), // 纳税所属月份 YYYY-MM
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
