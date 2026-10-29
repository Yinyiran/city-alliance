/**
 * 商品目录与规格枚举 (Catalog Domain)
 */

export enum TradeType {
  PICKUP = 'PICKUP',           // 到店自提
  STORE = 'STORE',             // 线下到店核销服务
  APPOINTMENT = 'APPOINTMENT', // 预约制核销服务
  EXPRESS = 'EXPRESS',         // 快递发货实物
  TAKEOUT = 'TAKEOUT',         // 同城外卖即时配送
}

export enum GoodsStatus {
  ON_SALE = 'ON_SALE',         // 上架销售中
  OFF_SALE = 'OFF_SALE',       // 下架
  SOLD_OUT = 'SOLD_OUT',       // 售罄
  PENDING_REVIEW = 'PENDING_REVIEW', // 待平台审核
}

export enum FreightType {
  FREE = 'FREE',               // 全国包邮
  FIXED = 'FIXED',             // 固定运费
  TEMPLATE = 'TEMPLATE',       // 计费运费模板 (按重/按件)
}
