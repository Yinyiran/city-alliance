-- ============================================================
-- 城市合伙人/多商户系统 菜单节点补丁 SQL
-- 解决总后台与分站后台页面未显示问题
-- ============================================================

-- 1. 总后台「会员开通分润明细」
DELETE FROM `menu` WHERE `name` = 'MEMBER_LEVEL_SETTLEMENT' OR `url` = 'admin/memberlevel/settlementlist';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('admin', '', '会员开通分润明细', 'MEMBER_LEVEL_SETTLEMENT', 'MEMBER_ROOT', 2, 'admin/memberlevel/settlementlist', 1, 4, '展示会员购买开通邀约提成、主理人分成、分公司分成与退款作废冲正', 0, '', '', 1);

-- 2. 总后台「分销结算明细」（fenxiao_money 表数据）
DELETE FROM `menu` WHERE `name` = 'PROMOTION_FENXIAO_MONEY' OR `url` = 'fenxiao://admin/fxorder/lists';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('admin', 'fenxiao', '分销结算明细', 'PROMOTION_FENXIAO_MONEY', 'PROMOTION_FENXIAO_ROOT', 2, 'fenxiao://admin/fxorder/lists', 1, 4, '展示带单分润、主理人提成、员工提成、分公司提成明细', 0, '', '', 1);

-- 3. 分站后台「门店收益与业绩报表」
DELETE FROM `menu` WHERE `name` = 'ADDON_CITY_SHOP_REVENUE_REPORT' OR `url` = 'city://city/shop/revenuereport';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('city', 'city', '门店收益与业绩报表', 'ADDON_CITY_SHOP_REVENUE_REPORT', 'ADDON_CITY_SHOP_INDEX', 3, 'city://city/shop/revenuereport', 1, 4, '分站查看下辖门店带单收益与会员收益业绩报表', 0, '', '', 1);

-- 4. 分站后台「分公司账户流水」
DELETE FROM `menu` WHERE `name` = 'ADDON_CITY_ACCOUNT_ORDERLIST' OR `url` = 'city://city/account/orderlist';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('city', 'city', '分公司账户流水', 'ADDON_CITY_ACCOUNT_ORDERLIST', 'ADDON_CITY_ACCOUNT_ROOT', 2, 'city://city/account/orderlist', 1, 2, '分公司订单交易与结算流水明细', 0, '', '', 1);

-- 清理菜单缓存（如果数据库有 cache 键或通过框架清缓存）
