import { z } from 'zod';
import { MemberLevel, PaymentChannel } from '../enums/index.js';

/**
 * 会员档案 Schema (支持6位数会员编号与终身锁客拓扑)
 */
export const MemberProfileSchema = z.object({
  id: z.string().uuid(),
  memberCode: z.string().regex(/^\d{6}$/, '会员编号必须为6位纯数字'),
  userId: z.string().uuid(),
  nickname: z.string(),
  avatarUrl: z.string().nullable().optional(),
  mobile: z.string().regex(/^1[3-9]\d{9}$/, '手机号格式不正确'),
  level: z.nativeEnum(MemberLevel).default(MemberLevel.NORMAL),
  levelExpiryDate: z.string().nullable().optional(), // 会员到期时间 (永久或特定周期)
  inviterId: z.string().uuid().nullable().optional(), // 终身锁客上级
  inviterCode: z.string().regex(/^\d{6}$/).nullable().optional(),
  bindShopId: z.string().uuid().nullable().optional(), // 绑定的归属商家/主理人
  bindStoreId: z.string().uuid().nullable().optional(), // 绑定的实体门店
  points: z.number().int().default(0), // 会员积分
  balance: z.string().regex(/^\d+(\.\d{1,2})?$/).default('0.00'), // 可提现可用余额
  frozenBalance: z.string().regex(/^\d+(\.\d{1,2})?$/).default('0.00'), // 7天消费解冻中收益
  totalEarned: z.string().regex(/^\d+(\.\d{1,2})?$/).default('0.00'), // 累计总收益
  realName: z.string().nullable().optional(),
  idCardNo: z.string().nullable().optional(),
  isRealNameVerified: z.boolean().default(false),
  status: z.enum(['ACTIVE', 'DISABLED']).default('ACTIVE'),
  createdAt: z.string(),
  updatedAt: z.string(),
});
export type MemberProfileDto = z.infer<typeof MemberProfileSchema>;

/**
 * 会员四级权益配置表定义
 */
export const MemberEquitySchema = z.object({
  level: z.nativeEnum(MemberLevel),
  levelName: z.string(),
  price: z.string(), // 官方标准指导价
  discountRate: z.number().min(0).max(1), // 消费折上折: 1.0 (无), 0.98, 0.96, 0.95
  giftPoints: z.number().int(), // 开通赠送积分: 0, 1000, 3000, 10000
  pointsRatio: z.number().min(1), // 消费送积分比例: 1.0, 1.2, 1.3, 1.5
  inviteDiamondRewardPercent: z.number(), // 邀约钻石奖励: 10%, 15%, 20%, 25%
  inviteBlackDiamondRewardPercent: z.number(), // 邀约黑钻奖励: 15%, 20%, 25%, 30%
  invitePartnerRewardPercent: z.number(), // 邀约合伙人奖励: 20%, 25%, 30%, 35%
  consumptionCommissionPercent: z.number(), // 消费带单佣金比例: 0%, 0.2%, 0.3%, 0.5%
  minWithdrawAmount: z.number().default(100), // 提现门槛: 100元
  freezeDays: z.number().default(7), // 解冻等待天数: 7天
});
export type MemberEquityDto = z.infer<typeof MemberEquitySchema>;

/**
 * 购买/升级会员请求
 */
export const UpgradeMemberOrderSchema = z.object({
  targetLevel: z.nativeEnum(MemberLevel),
  paymentChannel: z.nativeEnum(PaymentChannel),
  shopId: z.string().uuid().optional(),
  inviterId: z.string().uuid().optional(),
});
export type UpgradeMemberOrderDto = z.infer<typeof UpgradeMemberOrderSchema>;

/**
 * 店铺老板主动定价添加合伙人 (此金额只走主理人50%与分公司50%分佣)
 */
export const PartnerDirectGrantSchema = z.object({
  targetMemberId: z.string().uuid(),
  customPrice: z.string().regex(/^\d+(\.\d{1,2})?$/, '自定义开通金额格式需为两位小数'),
  notes: z.string().max(200).optional(),
});
export type PartnerDirectGrantDto = z.infer<typeof PartnerDirectGrantSchema>;

/**
 * 实名认证核身请求
 */
export const MemberRealNameAuthSchema = z.object({
  realName: z.string().min(2, '真实姓名最少2字').max(20),
  idCardNo: z.string().regex(/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/, '身份证格式不正确'),
  mobile: z.string().regex(/^1[3-9]\d{9}$/, '手机号格式不正确'),
  employmentType: z.string().default('其它'),
  faceAuthToken: z.string().optional(),
});
export type MemberRealNameAuthDto = z.infer<typeof MemberRealNameAuthSchema>;
