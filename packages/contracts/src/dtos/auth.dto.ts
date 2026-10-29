import { z } from 'zod';
import { TerminalType, TenantType, RoleEnum } from '../enums/iam.enum.js';

export const AccountLoginSchema = z.object({
  username: z.string().min(3, '用户名至少3位').max(50),
  password: z.string().min(6, '密码至少6位'),
  terminal: z.nativeEnum(TerminalType),
  tenantType: z.nativeEnum(TenantType).optional(),
});
export type AccountLoginDto = z.infer<typeof AccountLoginSchema>;

export const WechatLoginSchema = z.object({
  code: z.string().min(1, '微信临时 code 必传'),
  phoneCode: z.string().optional(),
  inviterId: z.string().uuid().optional(),
  terminal: z.nativeEnum(TerminalType),
});
export type WechatLoginDto = z.infer<typeof WechatLoginSchema>;

export const RefreshTokenSchema = z.object({
  refreshToken: z.string().min(1, 'Refresh Token 必填'),
});
export type RefreshTokenDto = z.infer<typeof RefreshTokenSchema>;

export const AuthTokenResultSchema = z.object({
  accessToken: z.string(),
  refreshToken: z.string(),
  expiresIn: z.number(),
  user: z.object({
    id: z.string().uuid(),
    username: z.string(),
    nickname: z.string(),
    avatarUrl: z.string().nullable().optional(),
    role: z.nativeEnum(RoleEnum),
    tenantType: z.nativeEnum(TenantType),
    tenantId: z.string().uuid().nullable().optional(),
    permissions: z.array(z.string()),
  }),
});
export type AuthTokenResultDto = z.infer<typeof AuthTokenResultSchema>;
