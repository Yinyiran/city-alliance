-- ============================================================
-- 商品交易类型改造（到店消费 / 预约到店 / 到店自提 / 外卖）
-- 幂等脚本：可重复执行；执行前请先备份数据库
-- 日期：2026-10-07
-- ============================================================

-- ① 商品主表：交易类型与服务配置 -------------------------------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''交易类型 0=普通商品 1=到店消费 2=预约到店 3=到店自提 4=外卖配送''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'service_duration');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods` ADD COLUMN `service_duration` int NOT NULL DEFAULT 0 COMMENT ''服务时长(分钟)''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'min_order_amount');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods` ADD COLUMN `min_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''到店消费起付金额''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'max_order_amount');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods` ADD COLUMN `max_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''自定义金额上限，0=不限制''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'allow_custom_amount');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods` ADD COLUMN `allow_custom_amount` tinyint NOT NULL DEFAULT 0 COMMENT ''是否允许自定义金额(仅到店场景)''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ② SKU 冗余 ------------------------------------------------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods_sku' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods_sku` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''冗余商品交易类型''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods_sku' AND COLUMN_NAME = 'service_duration');
SET @sql := IF(@exist = 0, 'ALTER TABLE `goods_sku` ADD COLUMN `service_duration` int NOT NULL DEFAULT 0 COMMENT ''冗余服务时长(分钟)''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ③ 订单商品项冗余（列表筛选/统计用） --------------------------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_goods' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order_goods` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''冗余商品交易类型''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ④ 订单表：交易类型 / 自定义金额 / 预约信息 / 支付场景 ----------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''交易类型 0普通 1到店消费 2预约到店 3到店自提 4外卖配送''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'custom_amount');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `custom_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT ''自定义金额(仅到店场景)''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_id` int NOT NULL DEFAULT 0 COMMENT ''预约占用记录ID''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_date');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_date` date DEFAULT NULL COMMENT ''预约日期''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_slot');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_slot` varchar(50) DEFAULT '''' COMMENT ''预约时段文本 10:00-10:30''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_num');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_num` int NOT NULL DEFAULT 1 COMMENT ''预约人数/份数''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_user');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_user` varchar(50) DEFAULT '''' COMMENT ''预约联系人''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_mobile');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_mobile` varchar(20) DEFAULT '''' COMMENT ''预约联系电话''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_remark');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_remark` varchar(255) DEFAULT '''' COMMENT ''预约备注''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'appointment_status');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `appointment_status` tinyint NOT NULL DEFAULT -1 COMMENT ''预约状态 -1=无需预约 0=待付款 1=已预约 2=已取消 3=已到店 4=已完成''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'pay_scene');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `pay_scene` tinyint NOT NULL DEFAULT 0 COMMENT ''支付场景 0=小程序 1=顾客扫商家收款码''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'cashier_id');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `cashier_id` int NOT NULL DEFAULT 0 COMMENT ''收银员ID''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order' AND COLUMN_NAME = 'cashier_name');
SET @sql := IF(@exist = 0, 'ALTER TABLE `order` ADD COLUMN `cashier_name` varchar(50) DEFAULT '''' COMMENT ''收银员名称''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ⑤ 核销表：区分到店消费核销 ----------------------------------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'verify' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `verify` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''核销对应交易类型''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ⑥ 预约时段规则表（按 门店 + 商品 配置） ---------------------------------
CREATE TABLE IF NOT EXISTS `goods_appointment_rule` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int NOT NULL DEFAULT 0 COMMENT '商户ID',
  `site_name` varchar(50) DEFAULT '' COMMENT '商户名称',
  `website_id` int NOT NULL DEFAULT 0,
  `store_id` int NOT NULL DEFAULT 0 COMMENT '门店ID',
  `store_name` varchar(100) DEFAULT '' COMMENT '门店名称',
  `goods_id` int NOT NULL DEFAULT 0 COMMENT '商品ID',
  `sku_id` int NOT NULL DEFAULT 0 COMMENT '规格ID，0=全部规格',
  `week_days` varchar(20) NOT NULL DEFAULT '1,2,3,4,5,6,7' COMMENT '生效星期 1-7',
  `start_time` time NOT NULL DEFAULT '09:00:00',
  `end_time` time NOT NULL DEFAULT '18:00:00',
  `interval_minutes` int NOT NULL DEFAULT 30 COMMENT '时段间隔(分钟)',
  `max_num` int NOT NULL DEFAULT 1 COMMENT '同时接待数量',
  `stop_before_minutes` int NOT NULL DEFAULT 60 COMMENT '提前截止分钟数',
  `service_duration` int NOT NULL DEFAULT 0 COMMENT '服务时长(分钟)，0=取商品配置',
  `sort` int NOT NULL DEFAULT 0,
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '1=启用 0=停用',
  `create_time` int NOT NULL DEFAULT 0,
  `update_time` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_goods_store` (`goods_id`,`store_id`,`status`),
  KEY `idx_site` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='预约时段规则';

-- ⑦ 预约占用表（实时名额，超时未付自动释放） -----------------------------
CREATE TABLE IF NOT EXISTS `goods_appointment_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL DEFAULT 0,
  `order_no` varchar(32) DEFAULT '' ,
  `site_id` int NOT NULL DEFAULT 0,
  `site_name` varchar(50) DEFAULT '',
  `website_id` int NOT NULL DEFAULT 0,
  `store_id` int NOT NULL DEFAULT 0,
  `store_name` varchar(100) DEFAULT '',
  `goods_id` int NOT NULL DEFAULT 0,
  `goods_name` varchar(200) DEFAULT '',
  `sku_id` int NOT NULL DEFAULT 0,
  `sku_name` varchar(200) DEFAULT '',
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_text` varchar(50) DEFAULT '' ,
  `num` int NOT NULL DEFAULT 1,
  `user_name` varchar(50) DEFAULT '',
  `mobile` varchar(20) DEFAULT '',
  `remark` varchar(255) DEFAULT '',
  `expire_time` int NOT NULL DEFAULT 0 COMMENT '名额保留截止时间戳',
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '0=待付款 1=已预约 2=已取消 3=已过期 4=已完成',
  `create_time` int NOT NULL DEFAULT 0,
  `update_time` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_slot` (`goods_id`,`appointment_date`,`start_time`,`status`),
  KEY `idx_order` (`order_id`),
  KEY `idx_expire` (`status`,`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='预约时段占用记录';
