import { z } from 'zod';
import { CommissionType, MemberLevel } from '../enums/index.js';

export const CalculateCommissionSchema = z.object({
  orderId: z.string().uuid(),
  totalAmount: z.string().regex(/^\d+(\.\d{1,2})?$/),
  shopId: z.string().uuid(),
  customerId: z.string().uuid(),
  commissionType: z.nativeEnum(CommissionType),
  inviterId: z.string().uuid().optional(),
  customerMemberLevel: z.nativeEnum(MemberLevel).default(MemberLevel.NORMAL),
});
export type CalculateCommissionDto = z.infer<typeof CalculateCommissionSchema>;

export const CommissionSplitItemSchema = z.object({
  beneficiaryId: z.string().uuid(),
  beneficiaryRole: z.string(),
  ratioPercent: z.number().min(0).max(100),
  calculatedAmount: z.string().regex(/^\d+(\.\d{1,2})?$/),
  isFrozen: z.boolean(),
  freezeDays: z.number().int().default(7),
});
export type CommissionSplitItemDto = z.infer<typeof CommissionSplitItemSchema>;

export const ApplyWithdrawSchema = z.object({
  amount: z.string().regex(/^\d+(\.\d{1,2})?$/, '提现金额需为两位小数'),
  channel: z.enum(['WECHAT', 'BANK_CARD', 'ALIPAY']),
  bankCardNo: z.string().optional(),
  realName: z.string().min(2, '真实姓名必填'),
  idCardNo: z.string().regex(/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/, '身份证格式不正确'),
});
export type ApplyWithdrawDto = z.infer<typeof ApplyWithdrawSchema>;

export const TaxDeductionSchema = z.object({
  grossAmount: z.string(),
  taxableIncome: z.string(),
  taxRatePercent: z.number(),
  quickDeduction: z.string(),
  taxAmount: z.string(),
  netAmount: z.string(),
});
export type TaxDeductionDto = z.infer<typeof TaxDeductionSchema>;
