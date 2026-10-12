-- ==========================================================
-- 会员权益体系、多级分润模型、个税代扣与提现实名认证数据表更新脚本
-- 日期: 2026-09-14
-- ==========================================================

-- 1. 会员购买与开通多级分润明细表
CREATE TABLE IF NOT EXISTS `member_level_settlement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联member_level_order.order_id',
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单编号',
  `buy_member_id` int(11) NOT NULL DEFAULT 0 COMMENT '购买人会员ID',
  `level_id` int(11) NOT NULL DEFAULT 0 COMMENT '购买等级',
  `level_name` varchar(50) NOT NULL DEFAULT '' COMMENT '购买等级名称',
  `order_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员费总额',
  `open_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '开通方式: 1-自主购买 2-老板开通',
  `invite_member_id` int(11) NOT NULL DEFAULT 0 COMMENT '邀约人会员ID',
  `invite_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '邀约奖励比例%',
  `invite_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '邀约奖励金额',
  `shop_id` int(11) NOT NULL DEFAULT 0 COMMENT '归属门店ID',
  `shop_rate` decimal(10,2) NOT NULL DEFAULT '25.00' COMMENT '主理人分润比例%',
  `shop_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主理人获得金额',
  `city_id` int(11) NOT NULL DEFAULT 0 COMMENT '分公司website_id',
  `city_rate` decimal(10,2) NOT NULL DEFAULT '25.00' COMMENT '分公司分润比例%',
  `city_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '分公司获得金额',
  `platform_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '总部留存金额',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '状态: 0-冻结中(待7天+消费) 1-已解冻发放 2-已退款作废',
  `is_seven_days` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否满7天',
  `is_consume` tinyint(1) NOT NULL DEFAULT 0 COMMENT '下级是否产生消费',
  `unlock_time` int(11) NOT NULL DEFAULT 0 COMMENT '解冻时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_buy_member` (`buy_member_id`),
  KEY `idx_invite_member` (`invite_member_id`),
  KEY `idx_shop_id` (`shop_id`),
  KEY `idx_city_id` (`city_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员购买与开通多级分润明细表';

-- 2. 会员提现表增加个税、税前税后及人脸实名核验字段
ALTER TABLE `member_withdraw` 
  ADD COLUMN `id_card` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号',
  ADD COLUMN `job_type` varchar(50) NOT NULL DEFAULT '其它' COMMENT '从业类型',
  ADD COLUMN `tax_base` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应纳税所得额',
  ADD COLUMN `tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预扣税率%',
  ADD COLUMN `tax_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '代扣个税金额',
  ADD COLUMN `pre_tax_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '税前金额',
  ADD COLUMN `after_tax_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '税后实发金额',
  ADD COLUMN `face_auth_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '人脸认证状态: 0未认证 1已认证',
  ADD COLUMN `face_auth_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '人脸核验流水号';

-- 3. 会员表自增ID设为 100001 并扩充实名核身字段
ALTER TABLE `member` AUTO_INCREMENT = 100001;

ALTER TABLE `member` 
  ADD COLUMN `is_face_auth` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否完成人脸核身',
  ADD COLUMN `id_card` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号码',
  ADD COLUMN `job_type` varchar(50) NOT NULL DEFAULT '其它' COMMENT '任职从业类型';

-- 4. 扩充系统全局分润配置表 reward_config
ALTER TABLE `reward_config`
  ADD COLUMN `self_order_rate` decimal(10,2) NOT NULL DEFAULT '2.00' COMMENT '自签单服务费%',
  ADD COLUMN `self_city_rate` decimal(10,2) NOT NULL DEFAULT '0.50' COMMENT '自签单分公司%',
  ADD COLUMN `self_platform_rate` decimal(10,2) NOT NULL DEFAULT '1.50' COMMENT '自签单总部%',
  ADD COLUMN `bring_order_rate` decimal(10,2) NOT NULL DEFAULT '6.00' COMMENT '带单总佣金%',
  ADD COLUMN `bring_shop_rate` decimal(10,2) NOT NULL DEFAULT '2.00' COMMENT '带单主理人%',
  ADD COLUMN `bring_city_rate` decimal(10,2) NOT NULL DEFAULT '1.50' COMMENT '带单分公司%',
  ADD COLUMN `bring_level1_rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '普通会员带单%',
  ADD COLUMN `bring_level2_rate` decimal(10,2) NOT NULL DEFAULT '0.20' COMMENT '钻石会员带单%',
  ADD COLUMN `bring_level3_rate` decimal(10,2) NOT NULL DEFAULT '0.30' COMMENT '黑钻会员带单%',
  ADD COLUMN `bring_level4_rate` decimal(10,2) NOT NULL DEFAULT '0.50' COMMENT '合伙人带单%',
  ADD COLUMN `member_shop_rate` decimal(10,2) NOT NULL DEFAULT '25.00' COMMENT '会员费主理人%',
  ADD COLUMN `member_city_rate` decimal(10,2) NOT NULL DEFAULT '25.00' COMMENT '会员费分公司%',
  ADD COLUMN `withdraw_rate` decimal(10,2) NOT NULL DEFAULT '0.35' COMMENT '提现手续费率%',
  ADD COLUMN `withdraw_min_money` decimal(10,2) NOT NULL DEFAULT '100.00' COMMENT '最低提现金额',
  ADD COLUMN `sign_shop_fee` decimal(10,2) NOT NULL DEFAULT '2.00' COMMENT '签到扣商家费',
  ADD COLUMN `sign_user_reward` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '签到奖用户现金',
  ADD COLUMN `custom_partner_shop_rate` decimal(10,2) NOT NULL DEFAULT '50.00' COMMENT '老板开通合伙人主理人比例%',
  ADD COLUMN `custom_partner_city_rate` decimal(10,2) NOT NULL DEFAULT '50.00' COMMENT '老板开通合伙人分公司比例%';

-- 5. 初始化/校准会员等级权益设置（折上折、送积分、消费返积分倍率）
UPDATE `member_level` SET `consume_discount` = 100, `send_point` = 0, `point_feedback` = 1.00 WHERE `level_id` = 1;
UPDATE `member_level` SET `consume_discount` = 98, `send_point` = 1000, `point_feedback` = 1.20 WHERE `level_id` = 2;
UPDATE `member_level` SET `consume_discount` = 96, `send_point` = 3000, `point_feedback` = 1.30 WHERE `level_id` = 3;
UPDATE `member_level` SET `point_feedback` = 1.50 WHERE `level_id` = 4;

-- 6. 添加会员开通分润解冻自动计划任务（每日执行一次）
INSERT INTO `cron` (`type`, `period`, `period_type`, `name`, `event`, `execute_time`, `relate_id`, `create_time`)
SELECT 2, 1, 3, '会员购买奖励满7天+消费自动解冻', 'CronMemberLevelRewardUnlock', UNIX_TIMESTAMP(), 0, UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `cron` WHERE `event` = 'CronMemberLevelRewardUnlock');

