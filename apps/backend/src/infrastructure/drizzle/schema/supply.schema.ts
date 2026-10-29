import { pgTable, uuid, varchar, text, numeric, timestamp } from 'drizzle-orm/pg-core';
import { tenants } from './iam.schema';
import { shops } from './store.schema';

/**
 * B2B 供货商主体表
 */
export const suppliers = pgTable('suppliers', {
  id: uuid('id').primaryKey().defaultRandom(),
  tenantId: uuid('tenant_id').notNull().references(() => tenants.id),
  companyName: varchar('company_name', { length: 150 }).notNull(),
  contactName: varchar('contact_name', { length: 50 }).notNull(),
  contactPhone: varchar('contact_phone', { length: 20 }).notNull(),
  licenseImageUrl: varchar('license_image_url', { length: 255 }),
  status: varchar('status', { length: 20 }).notNull().default('PENDING_AUDIT'), // PENDING_AUDIT, ACTIVE, FROZEN
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 商家 B2B 批发采购订单表
 */
export const purchaseOrders = pgTable('purchase_orders', {
  id: uuid('id').primaryKey().defaultRandom(),
  orderNo: varchar('order_no', { length: 64 }).unique().notNull(),
  supplierId: uuid('supplier_id').notNull().references(() => suppliers.id),
  buyerShopId: uuid('buyer_shop_id').notNull().references(() => shops.id),
  totalAmount: numeric('total_amount', { precision: 10, scale: 2 }).notNull(),
  status: varchar('status', { length: 30 }).notNull().default('PENDING_PAY'), // PENDING_PAY, PAID, SHIPPED, COMPLETED, CANCELLED
  shippingTrackingNo: varchar('shipping_tracking_no', { length: 100 }),
  notes: text('notes'),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});
