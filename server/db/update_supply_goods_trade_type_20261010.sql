-- ============================================================
-- 供应链商品补充交易类型字段
-- 说明：供应链端（supply / supply-admin）商品列表需要展示「交易类型」，
--       但 supply_goods 表原先没有该字段。
-- 幂等脚本：可重复执行；执行前请先备份数据库
-- 日期：2026-10-10
-- ============================================================

-- ① 供应商商品主表 ------------------------------------------------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supply_goods' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `supply_goods` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''交易类型 0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ② 供应商商品 SKU 表 --------------------------------------------------------
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supply_goods_sku' AND COLUMN_NAME = 'trade_type');
SET @sql := IF(@exist = 0, 'ALTER TABLE `supply_goods_sku` ADD COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''冗余商品交易类型''', 'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ③ 同步既有表字段注释为最新语义（0到店自提 / 3快递邮寄）----------------------
SET @sql := 'ALTER TABLE `goods` MODIFY COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''交易类型 0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送''';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := 'ALTER TABLE `goods_sku` MODIFY COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''冗余商品交易类型''';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := 'ALTER TABLE `order_goods` MODIFY COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''冗余商品交易类型''';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := 'ALTER TABLE `order` MODIFY COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''交易类型 0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送''';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := 'ALTER TABLE `verify` MODIFY COLUMN `trade_type` tinyint NOT NULL DEFAULT 0 COMMENT ''核销对应交易类型''';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;