-- --------------------------------------------------------
-- 签到收费与用户返现周结算系统数据表
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sign_settlement_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `signin_id` int(11) NOT NULL DEFAULT 0 COMMENT '签到记录ID(sign_member_record.signin_id)',
  `site_id` int(11) NOT NULL DEFAULT 0 COMMENT '商家店铺ID(shop_id)',
  `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '签到会员ID',
  `shop_fee` decimal(10,2) NOT NULL DEFAULT 3.00 COMMENT '商家每次签到应付费用',
  `user_reward` decimal(10,2) NOT NULL DEFAULT 2.00 COMMENT '用户每次签到应得奖励',
  `platform_profit` decimal(10,2) NOT NULL DEFAULT 1.00 COMMENT '平台留存毛利(shop_fee - user_reward)',
  `settle_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '结算状态: 0-待周结 1-已出账 2-已结清 3-已作废',
  `shop_bill_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联商家周账单ID',
  `user_bill_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联用户周账单ID',
  `period_key` varchar(30) NOT NULL DEFAULT '' COMMENT '结算周期标识(如 2026W38)',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '签到时间',
  `settle_time` int(11) NOT NULL DEFAULT 0 COMMENT '结算时间',
  PRIMARY KEY (`id`),
  KEY `idx_site_status` (`site_id`, `settle_status`),
  KEY `idx_member_status` (`member_id`, `settle_status`),
  KEY `idx_signin_id` (`signin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到收费与返现流水明细表';

CREATE TABLE IF NOT EXISTS `sign_settlement_bill` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '账单ID',
  `bill_no` varchar(50) NOT NULL DEFAULT '' COMMENT '账单编号',
  `bill_type` varchar(20) NOT NULL DEFAULT 'shop' COMMENT '账单类型: shop-商家账单 user-用户账单',
  `relate_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联ID: 商家site_id 或 会员member_id',
  `period_key` varchar(30) NOT NULL DEFAULT '' COMMENT '周期标识',
  `sign_count` int(11) NOT NULL DEFAULT 0 COMMENT '周期内有效签到次数',
  `total_money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '账单总金额(商家为应扣金额，用户为应发金额)',
  `pay_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '状态: 0-待结算 1-已结清/已发放 2-扣款失败待补缴',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '账单说明',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '统计区间开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '统计区间结束时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '出账时间',
  `finish_time` int(11) NOT NULL DEFAULT 0 COMMENT '结算完成时间',
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `uk_bill_no` (`bill_no`),
  KEY `idx_type_relate` (`bill_type`, `relate_id`),
  KEY `idx_period` (`period_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到周结算账单汇总表';

-- 菜单注册
INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `is_control`)
SELECT 'admin', '', '签到流水明细', 'SIGN_SETTLEMENT_RECORD', 'SIGN_ROOT', 3, 'admin/sign/settlementrecords', 1, 4, 0
FROM DUAL WHERE NOT EXISTS (SELECT `id` FROM `menu` WHERE `name` = 'SIGN_SETTLEMENT_RECORD');

INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `is_control`)
SELECT 'admin', '', '签到周账单', 'SIGN_SETTLEMENT_BILL', 'SIGN_ROOT', 3, 'admin/sign/bills', 1, 5, 0
FROM DUAL WHERE NOT EXISTS (SELECT `id` FROM `menu` WHERE `name` = 'SIGN_SETTLEMENT_BILL');

INSERT INTO `menu` (`app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `is_control`)
SELECT 'admin', '', '签到结算设置', 'SIGN_SETTLEMENT_CONFIG', 'SIGN_ROOT', 3, 'admin/sign/config', 1, 6, 0
FROM DUAL WHERE NOT EXISTS (SELECT `id` FROM `menu` WHERE `name` = 'SIGN_SETTLEMENT_CONFIG');
