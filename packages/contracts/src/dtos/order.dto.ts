import { z } from 'zod';
import { TradeType, PaymentChannel, VerifyType } from '../enums/index.js';

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
