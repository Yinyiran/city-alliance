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

export const CreateUserSchema = z.object({
  username: z.string().min(3, '用户名至少3位').max(50),
  password: z.string().min(6, '密码至少6位'),
  nickname: z.string().min(2, '姓名/昵称至少2位').max(50),
  mobile: z.string().regex(/^1[3-9]\d{9}$/, '请输入合法的11位手机号').optional().or(z.literal('')),
  tenantId: z.string().uuid('无效的组织租户 ID').optional().nullable(),
  roleId: z.string().uuid('请选择分配的角色'),
});
export type CreateUserDto = z.infer<typeof CreateUserSchema>;

export const UpdateUserSchema = z.object({
  nickname: z.string().min(2).max(50).optional(),
  mobile: z.string().regex(/^1[3-9]\d{9}$/, '请输入合法的11位手机号').optional().or(z.literal('')),
  tenantId: z.string().uuid('无效的组织租户 ID').optional().nullable(),
  roleId: z.string().uuid().optional(),
  status: z.nativeEnum(UserStatus).optional(),
});
export type UpdateUserDto = z.infer<typeof UpdateUserSchema>;

export const QueryUserSchema = z.object({
  keyword: z.string().optional(),
  roleCode: z.string().optional(),
  tenantType: z.string().optional(),
  status: z.string().optional(),
  page: z.coerce.number().min(1).default(1),
  pageSize: z.coerce.number().min(1).max(100).default(20),
});
export type QueryUserDto = z.infer<typeof QueryUserSchema>;

export const ResetPasswordSchema = z.object({
  newPassword: z.string().min(6, '新密码至少6位'),
});
export type ResetPasswordDto = z.infer<typeof ResetPasswordSchema>;

export const CreateRoleSchema = z.object({
  code: z.string().min(2, '角色标识至少2位').max(50),
  name: z.string().min(2, '角色名称至少2位').max(50),
  tenantType: z.string().min(2, '组织租户类型不能为空'),
  permissions: z.array(z.string()).default([]),
});
export type CreateRoleDto = z.infer<typeof CreateRoleSchema>;

export const UpdateRoleSchema = z.object({
  name: z.string().min(2).max(50).optional(),
  permissions: z.array(z.string()).optional(),
});
export type UpdateRoleDto = z.infer<typeof UpdateRoleSchema>;
