import { pgTable, uuid, varchar, text, numeric, integer, timestamp } from 'drizzle-orm/pg-core';
import { stores } from './store.schema';
import { users } from './iam.schema';

/**
 * 门店店员与核销员表
 */
export const storeClerks = pgTable('store_clerks', {
  id: uuid('id').primaryKey().defaultRandom(),
  storeId: uuid('store_id').notNull().references(() => stores.id, { onDelete: 'cascade' }),
  userId: uuid('user_id').notNull().references(() => users.id, { onDelete: 'cascade' }),
  clerkNo: varchar('clerk_no', { length: 30 }).notNull(),
  realName: varchar('real_name', { length: 50 }).notNull(),
  phone: varchar('phone', { length: 20 }).notNull(),
  role: varchar('role', { length: 30 }).notNull().default('CASHIER'), // CASHIER, GUIDE, MANAGER
  permissions: text('permissions').array().notNull().default([]), // 细粒度操作权限
  verifyCount: integer('verify_count').notNull().default(0), // 累计核销笔数
  status: varchar('status', { length: 20 }).notNull().default('ACTIVE'), // ACTIVE, DISABLED
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 收银交接班台账 (现金实收、扫码实收与扎帐差异)
 */
export const cashierShifts = pgTable('cashier_shifts', {
  id: uuid('id').primaryKey().defaultRandom(),
  storeId: uuid('store_id').notNull().references(() => stores.id, { onDelete: 'cascade' }),
  clerkId: uuid('clerk_id').notNull().references(() => storeClerks.id),
  shiftStart: timestamp('shift_start', { withTimezone: true }).notNull(),
  shiftEnd: timestamp('shift_end', { withTimezone: true }),
  cashTotal: numeric('cash_total', { precision: 10, scale: 2 }).notNull().default('0.00'),
  scanTotal: numeric('scan_total', { precision: 10, scale: 2 }).notNull().default('0.00'),
  orderCount: integer('order_count').notNull().default(0),
  diffAmount: numeric('diff_amount', { precision: 10, scale: 2 }).notNull().default('0.00'),
  status: varchar('status', { length: 20 }).notNull().default('OPEN'), // OPEN, CLOSED
  remark: varchar('remark', { length: 255 }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
