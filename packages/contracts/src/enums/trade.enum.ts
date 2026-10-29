/**
 * 交易履约与核销枚举 (Trade & Cashier Domain)
 */

export enum OrderStatus {
  PENDING_PAY = 'PENDING_PAY',     // 待付款 (30分钟超时取消)
  PAID = 'PAID',                   // 已付款 / 待履约 (待核销 / 待发货 / 待接单)
  DELIVERING = 'DELIVERING',       // 履约中 (快递在途 / 外卖骑手配送中)
  COMPLETED = 'COMPLETED',         // 已完成 (已核销 / 已收货签收)
  CANCELLED = 'CANCELLED',         // 已取消 (超时未付 / 主动取消)
  REFUNDING = 'REFUNDING',         // 售后退款中
  REFUNDED = 'REFUNDED',           // 全额/部分退款完成
}

export enum PaymentChannel {
  WECHAT_PAY = 'WECHAT_PAY',       // 微信直连官方支付
  HUIFU_PAY = 'HUIFU_PAY',         // 汇付天下资金合规分账支付
  BALANCE_PAY = 'BALANCE_PAY',     // 平台余额买单
}

export enum PayStatus {
  UNPAID = 'UNPAID',
  PAYING = 'PAYING',
  PAID = 'PAID',
  CLOSED = 'CLOSED',
}

export enum RefundStatus {
  NONE = 'NONE',
  APPLIED = 'APPLIED',                         // 顾客申请退款
  MERCHANT_AGREED = 'MERCHANT_AGREED',         // 商家同意退款
  PLATFORM_ARBITRATING = 'PLATFORM_ARBITRATING', // 申请平台客服仲裁介入
  REFUNDED = 'REFUNDED',                       // 原路退款成功
  REJECTED = 'REJECTED',                       // 驳回退款
}

export enum VerifyType {
  SCAN_QR = 'SCAN_QR',             // 手机摄像头扫码核销
  MANUAL_CODE = 'MANUAL_CODE',     // 手动输入12位核销券码
  POS_CASHIER = 'POS_CASHIER',     // 收银台终端快速结单
}
