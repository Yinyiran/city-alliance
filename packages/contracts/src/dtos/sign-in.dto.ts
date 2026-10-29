import { z } from 'zod';
import { SignInStatus } from '../enums/sign-in.enum.js';

/**
 * 门店扫码签到请求与规则 (一门店一月仅可签到一次；商户扣费平台分润奖用户)
 */
export const StoreSignInRequestSchema = z.object({
  storeId: z.string().uuid(),
  latitude: z.number().min(-90).max(90),
  longitude: z.number().min(-180).max(180),
});
export type StoreSignInRequestDto = z.infer<typeof StoreSignInRequestSchema>;

export const StoreSignInResultSchema = z.object({
  status: z.nativeEnum(SignInStatus),
  rewardCash: z.string().regex(/^\d+(\.\d{1,2})?$/), // 奖励现金 (默认 1.00 元)
  merchantDeductedAmount: z.string().regex(/^\d+(\.\d{1,2})?$/), // 商户扣减资金 (默认 2.00 元)
  rewardPoints: z.number().int(), // 赠送积分
  giftGoodsOrderId: z.string().uuid().nullable().optional(), // 签到赠品生成的订单 ID
  nextAvailableDate: z.string(), // 下次可签到时间 (次月1日)
  message: z.string(),
});
export type StoreSignInResultDto = z.infer<typeof StoreSignInResultSchema>;

/**
 * 短视频进度上报与防刷领积分
 */
export const VideoProgressReportSchema = z.object({
  videoId: z.string().uuid(),
  currentSeconds: z.number().nonnegative(),
  totalDurationSeconds: z.number().positive(),
  nonce: z.string().min(16), // 防重放随机盐
  timestamp: z.number().int(),
});
export type VideoProgressReportDto = z.infer<typeof VideoProgressReportSchema>;
