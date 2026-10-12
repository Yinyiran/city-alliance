-- 商品预约配置：可提前预约天数 + 核销迟到宽限
-- 幂等脚本：可重复执行；执行前请先备份数据库
-- 日期：2026-10-11
--
-- 背景：
--   1) 「可预约日期」此前由前端固定传 30 天，商户无法配置；
--      行业通行做法是由商家在商品后台配置「可提前预约天数」。
--   2) 核销时不校验是否在预约时段内（迟到也可核销），商户无法控制爽约；
--      增加「核销宽限」配置，超出宽限时核销接口给出改约提示，商家可选择强制核销。
--
-- 说明：
--   后端 app/model/goods/Appointment.php::getGoodsAppointmentConfig() 已做字段存在性兼容，
--   未执行本脚本时自动降级为默认值（7 天 / 60 分钟），旧库无需立即升级也不会报错。

SET @exist_days := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'appointment_days'
);
SET @sql := IF(@exist_days = 0,
    'ALTER TABLE `goods` ADD COLUMN `appointment_days` tinyint(4) NOT NULL DEFAULT 7 COMMENT ''可提前预约天数(预约到店专用，7)'' AFTER `allow_custom_amount`',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist_grace := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods' AND COLUMN_NAME = 'appointment_grace_minutes'
);
SET @sql := IF(@exist_grace = 0,
    'ALTER TABLE `goods` ADD COLUMN `appointment_grace_minutes` smallint(6) NOT NULL DEFAULT 60 COMMENT ''核销迟到宽限(分钟，预约到店专用)'' AFTER `appointment_days`',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 修正 trade_type 过期注释。
-- 现存注释为旧体系「0普通 1到店消费 2预约到店 3到店自提 4外卖配送」，
-- 与代码/全链路实际约定「0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送」相反，
-- 已导致维护者误判（DB 注释里 3 是到店自提，实际 3 是快递邮寄），此处一并纠正。
SET @exist_tt := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods'
      AND COLUMN_NAME = 'trade_type' AND COLUMN_COMMENT LIKE '%0普通%'
);
SET @sql := IF(@exist_tt = 1,
    'ALTER TABLE `goods` MODIFY COLUMN `trade_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT ''交易类型 0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送''',
    'DO 0');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 校验
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'goods'
  AND COLUMN_NAME IN ('trade_type','allow_custom_amount','appointment_days','appointment_grace_minutes');
