/**
 * 清算分润、会员权益与财税枚举 (Settlement & Finance Domain)
 */

export enum MemberLevel {
  NORMAL = 'NORMAL',               // 普通注册用户 (0元)
  DIAMOND = 'DIAMOND',             // 钻石会员 (399元)
  BLACK_DIAMOND = 'BLACK_DIAMOND', // 黑钻会员 (699元)
  PARTNER = 'PARTNER',             // 创客合伙人 (自定价或定向开通)
}

export enum SettlementStatus {
  UNSETTLED = 'UNSETTLED',         // 未结算
  FROZEN = 'FROZEN',               // 资金在7天冻结期内
  UNFROZEN = 'UNFROZEN',           // 已达解冻条件 (7天到期且下级产生真实消费)
  SETTLED = 'SETTLED',             // 已结算入可提现余额
  REVOKED = 'REVOKED',             // 发生售后退款，佣金自动撤销冲正
}

export enum CommissionType {
  MEMBER_UPGRADE = 'MEMBER_UPGRADE',           // 开通会员分成 (邀约奖+主理人25%+分公司25%)
  DAILY_CONSUMPTION_SELF = 'DAILY_CONSUMPTION_SELF', // 商家日常自签单 (扣除平台手续费2%)
  DAILY_CONSUMPTION_CROSS = 'DAILY_CONSUMPTION_CROSS', // 跨店带单分佣 (扣除引流佣金6%)
  PARTNER_FEE = 'PARTNER_FEE',                 // 老板自主开通合伙人分成 (主理人50%+分公司50%)
}

export enum WithdrawStatus {
  AUDITING = 'AUDITING',           // 审核中
  PAYING = 'PAYING',               // 银行/微信打款中
  SUCCESS = 'SUCCESS',             // 提现成功
  REJECTED = 'REJECTED',           // 审核不通过驳回
}
