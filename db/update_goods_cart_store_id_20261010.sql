-- 购物车补充分店门店上下文（到店自提/到店消费/预约到店 下单需带 store_id）
-- 幂等脚本：可重复执行；执行前请先备份数据库
-- 日期：2026-10-10
--
-- 背景：
--   goods_cart 原有字段仅有 cart_id/site_id/member_id/sku_id/num/goods_type，
--   加购接口丢弃了商品详情页已选的核销门店，导致「购物车 → 结算」链路丢失门店上下文：
--   结算页拿不到 store_id，后端只能静默改用第一家可用门店，
--   顾客实际到店门店与核销门店可能不符。
--
-- 说明：
--   后端 app/model/goods/Cart.php 已做字段存在性兼容，
--   未执行本脚本时 store_id 降级为 0，行为与修复前一致（自动取第一家门店），不会报错。

SET @exist := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods_cart' AND COLUMN_NAME = 'store_id'
);

SET @sql := IF(
    @exist = 0,
    'ALTER TABLE `goods_cart` ADD COLUMN `store_id` int(11) NOT NULL DEFAULT 0 COMMENT ''核销门店id（到店类商品，0表示未选择）'' AFTER `goods_type`',
    'DO 0'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 校验
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods_cart' AND COLUMN_NAME = 'store_id';
