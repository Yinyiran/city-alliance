/**
 * 营销签到与积分福利枚举 (Sign-In & Promotion Domain)
 */

export enum SignInStatus {
  SUCCESS = 'SUCCESS',                     // 签到成功 (奖励到账)
  MONTHLY_LIMIT_REACHED = 'MONTHLY_LIMIT_REACHED', // 本月在该商户已完成签到 (限1次)
  DISTANCE_EXCEEDED = 'DISTANCE_EXCEEDED', // 物理距离超出商户地理围栏范围
  MERCHANT_BALANCE_LOW = 'MERCHANT_BALANCE_LOW', // 商户营销账户余额不足
}

export enum PointChangeType {
  SIGN_IN_REWARD = 'SIGN_IN_REWARD',         // 到店扫码签到奖励
  SHORT_VIDEO_REWARD = 'SHORT_VIDEO_REWARD', // 浏览逛一逛短视频累计打点
  MALL_EXCHANGE = 'MALL_EXCHANGE',           // 福利区积分商城兑换抵扣
  ADMIN_ADJUST = 'ADMIN_ADJUST',             // 平台人工奖惩调账
}
