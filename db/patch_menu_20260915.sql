-- ============================================================
-- 城市合伙人/多商户系统 菜单节点补丁 SQL
-- 解决总后台与分站后台页面未显示问题
-- ============================================================

-- 1. 总后台「会员开通分润明细」挂载于【会员管理】
DELETE FROM `menu` WHERE `name` = 'MEMBER_LEVEL_SETTLEMENT' OR `url` = 'admin/memberlevel/settlementlist';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('admin', '', '会员开通分润明细', 'MEMBER_LEVEL_SETTLEMENT', 'MEMBER_ROOT', 2, 'admin/memberlevel/settlementlist', 1, 4, '展示会员购买开通邀约提成、主理人分成、分公司分成与退款作废冲正', 0, '', '', 1);

-- 2. 总后台「分销结算明细」挂载于【营销 -> 分销管理】
DELETE FROM `menu` WHERE `name` = 'PROMOTION_FENXIAO_MONEY' OR `url` = 'fenxiao://admin/fxorder/lists';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('admin', 'fenxiao', '分销结算明细', 'PROMOTION_FENXIAO_MONEY', 'PROMOTION_FENXIAO_ROOT', 3, 'fenxiao://admin/fxorder/lists', 1, 4, '展示带单分润、主理人提成、员工提成、分公司提成明细', 0, '', '', 1);

-- 3. 总后台【财务管理】快捷入口（直达分销结算与会员开通分润，无需进营销）
DELETE FROM `menu` WHERE `name` IN ('ACCOUNT_FENXIAO_MONEY', 'ACCOUNT_MEMBER_LEVEL_SETTLEMENT');
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_control`) 
VALUES 
('admin', 'fenxiao', '分销结算明细', 'ACCOUNT_FENXIAO_MONEY', 'ACCOUNT_ROOT', 2, 'fenxiao://admin/fxorder/lists', 1, 5, '财务管理直达分销结算明细', 1),
('admin', '', '会员开通分润明细', 'ACCOUNT_MEMBER_LEVEL_SETTLEMENT', 'ACCOUNT_ROOT', 2, 'admin/memberlevel/settlementlist', 1, 6, '财务管理直达会员开通分润明细', 1);

-- 4. 分站后台「门店收益与业绩报表」挂载于【商家管理】
DELETE FROM `menu` WHERE `name` = 'ADDON_CITY_SHOP_REVENUE_REPORT' OR `url` = 'city://city/shop/revenuereport';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('city', 'city', '门店收益与业绩报表', 'ADDON_CITY_SHOP_REVENUE_REPORT', 'ADDON_CITY_SHOP_INDEX', 3, 'city://city/shop/revenuereport', 1, 4, '分站查看下辖门店带单收益与会员收益业绩报表', 0, '', '', 1);

-- 5. 分站后台：确保顶级【资产】菜单显示状态开启（关键修复：原来此项被设为 0 导致顶部无资产入口）
UPDATE `menu` SET `is_show` = 1 WHERE `name` = 'ADDON_CITY_ACCOUNT_ROOT' AND `app_module` = 'city';

-- 6. 分站后台「分公司账户流水」挂载于【资产】
DELETE FROM `menu` WHERE `name` = 'ADDON_CITY_ACCOUNT_ORDERLIST' OR `url` = 'city://city/account/orderlist';
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) 
VALUES ('city', 'city', '分公司账户流水', 'ADDON_CITY_ACCOUNT_ORDERLIST', 'ADDON_CITY_ACCOUNT_ROOT', 2, 'city://city/account/orderlist', 1, 2, '分公司订单交易与结算流水明细', 0, '', '', 1);

-- 7. 补齐财务组（27）、运营组（28）权限
UPDATE `group` SET menu_array = CONCAT(menu_array, ',MEMBER_LEVEL_SETTLEMENT,PROMOTION_FENXIAO_MONEY,ACCOUNT_FENXIAO_MONEY,ACCOUNT_MEMBER_LEVEL_SETTLEMENT') WHERE group_id IN (27, 28) AND menu_array NOT LIKE '%MEMBER_LEVEL_SETTLEMENT%';
