import { z } from 'zod';
import { ClerkRoleEnum, UserStatus } from '../enums/iam.enum.js';

export const CreateClerkSchema = z.object({
  storeId: z.string().uuid('无效的门店 ID'),
  realName: z.string().min(2, '店员姓名至少2位').max(50),
  phone: z.string().regex(/^1[3-9]\d{9}$/, '请输入合法的11位手机号'),
  clerkNo: z.string().min(2, '工号至少2位').max(30),
  role: z.nativeEnum(ClerkRoleEnum).default(ClerkRoleEnum.CASHIER),
  permissions: z.array(z.string()).default([]),
  initialPassword: z.string().min(6, '初始密码至少6位').optional(),
});
export type CreateClerkDto = z.infer<typeof CreateClerkSchema>;

export const UpdateClerkSchema = z.object({
  realName: z.string().min(2).max(50).optional(),
  phone: z.string().regex(/^1[3-9]\d{9}$/, '请输入合法的11位手机号').optional(),
  role: z.nativeEnum(ClerkRoleEnum).optional(),
  permissions: z.array(z.string()).optional(),
  status: z.nativeEnum(UserStatus).optional(),
});
export type UpdateClerkDto = z.infer<typeof UpdateClerkSchema>;

export const QueryClerkSchema = z.object({
  storeId: z.string().uuid().optional(),
  shopId: z.string().uuid().optional(),
  keyword: z.string().optional(),
  role: z.nativeEnum(ClerkRoleEnum).optional(),
  status: z.nativeEnum(UserStatus).optional(),
  page: z.coerce.number().min(1).default(1),
  pageSize: z.coerce.number().min(1).max(100).default(20),
});
export type QueryClerkDto = z.infer<typeof QueryClerkSchema>;

export const ClerkDetailSchema = z.object({
  id: z.string().uuid(),
  userId: z.string().uuid(),
  storeId: z.string().uuid(),
  storeName: z.string().optional(),
  shopId: z.string().uuid().optional(),
  shopName: z.string().optional(),
  clerkNo: z.string(),
  realName: z.string(),
  phone: z.string(),
  role: z.nativeEnum(ClerkRoleEnum),
  permissions: z.array(z.string()),
  verifyCount: z.number().default(0),
  status: z.string(),
  createdAt: z.string(),
});
export type ClerkDetailDto = z.infer<typeof ClerkDetailSchema>;

export interface MenuItemDto {
  id: string;
  title: string;
  name: string;
  path: string;
  icon?: string;
  sort: number;
  permissions: string[];
  children?: MenuItemDto[];
}
