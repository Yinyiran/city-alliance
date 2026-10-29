/**
 * B2B 供应链采购枚举 (Supply Domain)
 */

export enum SupplierStatus {
  PENDING_AUDIT = 'PENDING_AUDIT', // 提交资质待审核
  ACTIVE = 'ACTIVE',               // 正常供货中
  FROZEN = 'FROZEN',               // 违规冻结
  TERMINATED = 'TERMINATED',       // 已终止合作
}

export enum PurchaseOrderStatus {
  PENDING_PAY = 'PENDING_PAY',     // 商家已下单待向供货商付款
  PAID = 'PAID',                   // 供货商已收款待发货
  SHIPPED = 'SHIPPED',             // 供货商已发出大宗货运
  RECEIVED = 'RECEIVED',           // 商家验收入库完成
  CLOSED = 'CLOSED',               // 采购单关闭
}
