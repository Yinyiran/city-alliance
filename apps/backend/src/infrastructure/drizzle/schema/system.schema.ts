import { pgTable, uuid, varchar, boolean, integer, jsonb, timestamp } from 'drizzle-orm/pg-core';
import { tenants, users } from './iam.schema';

/**
 * 商品分类树 (支持多级层级与图标展示)
 */
export const goodsCategories = pgTable('goods_categories', {
  id: uuid('id').primaryKey().defaultRandom(),
  parentId: uuid('parent_id'),
  name: varchar('name', { length: 100 }).notNull(),
  icon: varchar('icon', { length: 255 }),
  sortOrder: integer('sort_order').notNull().default(0),
  isVisible: boolean('is_visible').notNull().default(true),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 平台与租户动态系统配置表 (微信支付密钥/短信网关/分佣费率)
 */
export const systemConfigs = pgTable('system_configs', {
  id: uuid('id').primaryKey().defaultRandom(),
  configKey: varchar('config_key', { length: 100 }).unique().notNull(),
  configValue: jsonb('config_value').notNull(),
  group: varchar('group', { length: 50 }).notNull().default('SYSTEM'), // SYSTEM, WECHAT, PAYMENT, SETTLEMENT, SMS
  description: varchar('description', { length: 255 }),
  isEncrypted: boolean('is_encrypted').notNull().default(false),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 全局操作与合规审计日志
 */
export const systemAuditLogs = pgTable('system_audit_logs', {
  id: uuid('id').primaryKey().defaultRandom(),
  userId: uuid('user_id').references(() => users.id),
  tenantId: uuid('tenant_id').references(() => tenants.id),
  action: varchar('action', { length: 100 }).notNull(),
  targetEntity: varchar('target_entity', { length: 50 }).notNull(),
  targetId: varchar('target_id', { length: 64 }),
  ipAddress: varchar('ip_address', { length: 50 }),
  userAgent: varchar('user_agent', { length: 255 }),
  changes: jsonb('changes'),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});

/**
 * 富媒体素材与附件库 (OSS/COS/本地文件管理)
 */
export const mediaAttachments = pgTable('media_attachments', {
  id: uuid('id').primaryKey().defaultRandom(),
  tenantId: uuid('tenant_id').references(() => tenants.id),
  fileName: varchar('file_name', { length: 255 }).notNull(),
  fileExt: varchar('file_ext', { length: 20 }).notNull(),
  fileSize: integer('file_size').notNull(),
  mimeType: varchar('mime_type', { length: 100 }).notNull(),
  storageDriver: varchar('storage_driver', { length: 20 }).notNull().default('LOCAL'), // LOCAL, OSS, COS
  fileUrl: varchar('file_url', { length: 500 }).notNull(),
  fileHash: varchar('file_hash', { length: 64 }),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
});
