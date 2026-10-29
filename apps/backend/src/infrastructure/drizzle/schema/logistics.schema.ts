import { pgTable, uuid, varchar, boolean, integer, jsonb, timestamp } from 'drizzle-orm/pg-core';
import { orders } from './trade.schema';

/**
 * 快递承运商字典
 */
export const expressCompanies = pgTable('express_companies', {
  id: uuid('id').primaryKey().defaultRandom(),
  code: varchar('code', { length: 30 }).unique().notNull(), // SF, ZTO, YTO, STO, JD
  name: varchar('name', { length: 50 }).notNull(),
  phone: varchar('phone', { length: 30 }),
  website: varchar('website', { length: 200 }),
  isEnabled: boolean('is_enabled').notNull().default(true),
  sortOrder: integer('sort_order').notNull().default(0),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 订单物流发货与路由轨迹表
 */
export const orderDeliveryTraces = pgTable('order_delivery_traces', {
  id: uuid('id').primaryKey().defaultRandom(),
  orderId: uuid('order_id').notNull().references(() => orders.id, { onDelete: 'cascade' }),
  expressCompanyId: uuid('express_company_id').references(() => expressCompanies.id),
  trackingNo: varchar('tracking_no', { length: 64 }),
  deliveryType: varchar('delivery_type', { length: 30 }).notNull().default('EXPRESS'), // EXPRESS, LOCAL_RIDER
  riderName: varchar('rider_name', { length: 50 }),
  riderPhone: varchar('rider_phone', { length: 20 }),
  status: varchar('status', { length: 30 }).notNull().default('SHIPPED'), // SHIPPED, IN_TRANSIT, DELIVERED, REJECTED
  traceLogs: jsonb('trace_logs').$type<Array<{ time: string; context: string; location?: string }>>().default([]),
  shippedAt: timestamp('shipped_at', { withTimezone: true }).defaultNow().notNull(),
  deliveredAt: timestamp('delivered_at', { withTimezone: true }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
