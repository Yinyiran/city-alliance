import { z } from 'zod';
import { TradeType, PaymentChannel, VerifyType, RefundStatus, OrderStatus } from '../enums/index.js';

export const OrderItemSchema = z.object({
  goodsId: z.string().uuid(),
  skuId: z.string().uuid(),
  goodsName: z.string().min(1),
  skuName: z.string().min(1),
  price: z.string().regex(/^\d+(\.\d{1,2})?$/, '金额格式需为两位小数'),
  quantity: z.number().int().positive('购买数量须大于0'),
  coverImage: z.string().url().optional(),
});
export type OrderItemDto = z.infer<typeof OrderItemSchema>;

export const CreateOrderSchema = z.object({
  shopId: z.string().uuid('店铺ID必须为UUID'),
  storeId: z.string().uuid('核销/发货门店ID必须为UUID').optional(),
  tradeType: z.nativeEnum(TradeType),
  paymentChannel: z.nativeEnum(PaymentChannel),
  items: z.array(OrderItemSchema).min(1, '订单商品项至少包含1项'),
  remark: z.string().max(200).optional(),
  userCouponId: z.string().uuid().optional(),
  addressId: z.string().uuid().optional(), // 快递发货实物时必填
  appointmentTime: z.string().datetime().optional(), // 预约核销类商品时必填
});
export type CreateOrderDto = z.infer<typeof CreateOrderSchema>;

/**
 * 跨店铺购物车跨店下单拆单 Schema
 */
export const CartShopGroupSchema = z.object({
  shopId: z.string().uuid(),
  storeId: z.string().uuid().optional(),
  tradeType: z.nativeEnum(TradeType),
  items: z.array(OrderItemSchema).min(1),
  remark: z.string().max(200).optional(),
  freightTemplateId: z.string().uuid().optional(),
});
export type CartShopGroupDto = z.infer<typeof CartShopGroupSchema>;

export const SplitCartOrderSchema = z.object({
  customerId: z.string().uuid(),
  paymentChannel: z.nativeEnum(PaymentChannel),
  shopGroups: z.array(CartShopGroupSchema).min(1, '购物车拆单至少包含一个店铺分组'),
  addressId: z.string().uuid().optional(),
});
export type SplitCartOrderDto = z.infer<typeof SplitCartOrderSchema>;

export const VerifyQrSchema = z.object({
  verifyCode: z.string().length(12, '12位核销凭证码'),
  storeId: z.string().uuid('门店ID必传'),
  verifyType: z.nativeEnum(VerifyType).default(VerifyType.SCAN_QR),
});
export type VerifyQrDto = z.infer<typeof VerifyQrSchema>;

export const CashierQuickPaySchema = z.object({
  storeId: z.string().uuid('门店ID必传'),
  amount: z.string().regex(/^\d+(\.\d{1,2})?$/, '买单金额格式需为两位小数'),
  paymentChannel: z.nativeEnum(PaymentChannel),
  customerId: z.string().uuid().optional(),
  remark: z.string().max(100).optional(),
});
export type CashierQuickPayDto = z.infer<typeof CashierQuickPaySchema>;

/**
 * 快递发货 DTO
 */
export const ShipOrderSchema = z.object({
  orderId: z.string().uuid(),
  expressCompany: z.string().min(1, '物流公司名称必填'),
  expressNo: z.string().min(1, '物流单号必填'),
});
export type ShipOrderDto = z.infer<typeof ShipOrderSchema>;

/**
 * 外卖同城配送调度 DTO
 */
export const TakeawayDispatchSchema = z.object({
  orderId: z.string().uuid(),
  riderName: z.string().min(1, '骑手姓名必填'),
  riderPhone: z.string().regex(/^1[3-9]\d{9}$/, '骑手联系电话需为11位手机号'),
  estimatedDeliveryMinutes: z.number().int().positive().default(30),
});
export type TakeawayDispatchDto = z.infer<typeof TakeawayDispatchSchema>;

/**
 * 售后退款申请 DTO
 */
export const ApplyRefundSchema = z.object({
  orderId: z.string().uuid(),
  refundAmount: z.string().regex(/^\d+(\.\d{1,2})?$/, '退款金额格式需为两位小数'),
  reason: z.string().min(2, '退款原因说明不可少于2个字符').max(500),
});
export type ApplyRefundDto = z.infer<typeof ApplyRefundSchema>;

/**
 * 售后退款商家/平台仲裁审核 DTO
 */
export const AuditRefundSchema = z.object({
  refundId: z.string().uuid(),
  approved: z.boolean(),
  rejectReason: z.string().max(300).optional(),
  isPlatformArbitration: z.boolean().default(false),
});
export type AuditRefundDto = z.infer<typeof AuditRefundSchema>;

/**
 * 预约改约 DTO
 */
export const RescheduleAppointmentSchema = z.object({
  orderId: z.string().uuid(),
  newAppointmentTime: z.string().datetime('预约时间需为标准 ISO 时间字符串'),
});
export type RescheduleAppointmentDto = z.infer<typeof RescheduleAppointmentSchema>;

/**
 * 动态收款码生成 DTO
 */
export const GenerateCashierQrSchema = z.object({
  shopId: z.string().uuid(),
  storeId: z.string().uuid().optional(),
  amount: z.string().regex(/^\d+(\.\d{1,2})?$/),
  subject: z.string().default('门店聚合收银买单'),
});
export type GenerateCashierQrDto = z.infer<typeof GenerateCashierQrSchema>;
