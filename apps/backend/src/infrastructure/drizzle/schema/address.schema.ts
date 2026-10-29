import { pgTable, uuid, varchar, numeric, boolean, timestamp } from 'drizzle-orm/pg-core';
import { members } from './member.schema';

/**
 * 会员收货地址簿
 */
export const memberAddresses = pgTable('member_addresses', {
  id: uuid('id').primaryKey().defaultRandom(),
  memberId: uuid('member_id').notNull().references(() => members.id, { onDelete: 'cascade' }),
  receiverName: varchar('receiver_name', { length: 50 }).notNull(),
  phone: varchar('phone', { length: 20 }).notNull(),
  province: varchar('province', { length: 50 }).notNull(),
  city: varchar('city', { length: 50 }).notNull(),
  district: varchar('district', { length: 50 }).notNull(),
  detailAddress: varchar('detail_address', { length: 255 }).notNull(),
  postalCode: varchar('postal_code', { length: 10 }),
  latitude: numeric('latitude', { precision: 10, scale: 6 }),
  longitude: numeric('longitude', { precision: 10, scale: 6 }),
  isDefault: boolean('is_default').notNull().default(false),
  createdAt: timestamp('created_at', { withTimezone: true }).defaultNow().notNull(),
  updatedAt: timestamp('updated_at', { withTimezone: true }).defaultNow().notNull(),
});
