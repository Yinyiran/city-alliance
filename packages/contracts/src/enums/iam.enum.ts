/**
 * 身份认证、组织架构与多租户枚举 (IAM Domain)
 */

export enum TenantType {
  PLATFORM = 'PLATFORM', // 平台总部
  CITY = 'CITY',         // 城市分公司
  SHOP = 'SHOP',         // 品牌/主理人商家
  STORE = 'STORE',       // 线下实体门店
  SUPPLY = 'SUPPLY',     // B2B 供货商
}

export enum RoleEnum {
  SUPER_ADMIN = 'SUPER_ADMIN',     // 平台超级管理员
  PLATFORM_STAFF = 'PLATFORM_STAFF', // 平台运营人员
  CITY_AGENT = 'CITY_AGENT',       // 城市代理负责人
  SHOP_OWNER = 'SHOP_OWNER',       // 商家主理人 (老板)
  STORE_MANAGER = 'STORE_MANAGER', // 门店店长
  STORE_CLERK = 'STORE_CLERK',     // 门店店员/收银员
  SUPPLIER = 'SUPPLIER',           // 供货商主账号
}

export enum UserStatus {
  ACTIVE = 'ACTIVE',
  DISABLED = 'DISABLED',
  PENDING_REVIEW = 'PENDING_REVIEW',
}

export enum TerminalType {
  ADMIN_WEB = 'ADMIN_WEB',         // PC 统一管理中台
  CUSTOMER_APP = 'CUSTOMER_APP',   // 微信小程序-顾客端
  MERCHANT_APP = 'MERCHANT_APP',   // 微信小程序-商家端
}
