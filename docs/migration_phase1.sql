-- ============================================================================
-- 城市联盟系统升级改造 Phase 1 数据迁移脚本
-- ============================================================================

-- 1. 店铺等级配置表 (统一使用至尊店铺，参数全局可配置)
DROP TABLE IF EXISTS `shop_level`;
CREATE TABLE `shop_level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) NOT NULL DEFAULT '' COMMENT '等级名称: 普通店铺/金牌店铺/至尊店铺',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '购买/升级价格(元)',
  `validity_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '有效期类型: 1固定年限 2永久有效',
  `validity_years` int(11) NOT NULL DEFAULT '1' COMMENT '有效年限(1年/3年)',
  `max_partner_count` int(11) NOT NULL DEFAULT '2' COMMENT '合伙人上限(2/10/50)',
  `max_video_count` int(11) NOT NULL DEFAULT '3' COMMENT '短视频上传限制(-1为不限)',
  `base_points` int(11) NOT NULL DEFAULT '0' COMMENT '赠送品牌基础积分',
  `badge_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '徽章图标',
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='店铺会员等级配置表';

INSERT INTO `shop_level` (`level_id`, `level_name`, `price`, `validity_type`, `validity_years`, `max_partner_count`, `max_video_count`, `base_points`, `badge_icon`, `sort`, `status`, `create_time`) VALUES
(1, '普通店铺', 598.00, 1, 1, 2, 3, 0, '/upload/level/level1.png', 1, 1, UNIX_TIMESTAMP()),
(2, '金牌店铺', 980.00, 1, 3, 10, 10, 2000, '/upload/level/level2.png', 2, 1, UNIX_TIMESTAMP()),
(3, '至尊店铺', 1980.00, 2, 0, 50, -1, 10000, '/upload/level/level3.png', 3, 1, UNIX_TIMESTAMP());

-- 2. 官方物料品类与收费配置表 (后台动态可配)
DROP TABLE IF EXISTS `shop_material_type`;
CREATE TABLE `shop_material_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL COMMENT '物料名称(如: 亚克力台卡/立牌海报/收银贴)',
  `material_spec` varchar(100) NOT NULL DEFAULT '' COMMENT '材质与规格说明',
  `sample_image` varchar(255) NOT NULL DEFAULT '' COMMENT '样品质感预览图',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '工本费(后台配置)',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费包装费(后台配置)',
  `is_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否开放申领: 1是 0否',
  `sort` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='官方物料品类与收费标准配置表';

INSERT INTO `shop_material_type` (`type_id`, `type_name`, `material_spec`, `sample_image`, `cost_price`, `shipping_fee`, `is_enable`, `sort`, `create_time`) VALUES
(1, '亚克力立牌台卡 (T型)', '150mm×200mm 双面加厚亚克力', '/upload/material/taika.png', 18.00, 6.00, 1, 1, UNIX_TIMESTAMP()),
(2, '收银台高质感扫码贴', '120mm×160mm 防水防油PVC水晶覆膜', '/upload/material/tiezhi.png', 8.00, 5.00, 1, 2, UNIX_TIMESTAMP()),
(3, '门市易拉宝/展架海报', '800mm×2000mm 铝合金展架+高清相纸', '/upload/material/zhanjia.png', 45.00, 10.00, 1, 3, UNIX_TIMESTAMP());

-- 3. 扩充商户主表 (shop)
ALTER TABLE `shop` 
  ADD COLUMN `level_start_time` int(11) NOT NULL DEFAULT '0' COMMENT '等级生效/入驻时间',
  ADD COLUMN `is_permanent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否永久有效: 1是 0否',
  ADD COLUMN `custom_partner_free_num` int(11) NOT NULL DEFAULT '0' COMMENT '免抽佣自定义合伙人配额总数',
  ADD COLUMN `custom_partner_used_free_num` int(11) NOT NULL DEFAULT '0' COMMENT '已使用免抽佣名额',
  ADD COLUMN `pay_redirect_appid` varchar(64) NOT NULL DEFAULT '' COMMENT '汇付支付后跳转小程序AppId',
  ADD COLUMN `pay_redirect_path` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转小程序页面路径',
  ADD COLUMN `pay_ad_img` varchar(255) NOT NULL DEFAULT '' COMMENT '支付后广告宣传图',
  ADD COLUMN `pay_ad_url` varchar(255) NOT NULL DEFAULT '' COMMENT '广告跳转链接',
  ADD COLUMN `huifu_terminal_id` varchar(64) NOT NULL DEFAULT '' COMMENT '绑定的汇付线下扫码盒子终端号';

-- 4. 商家多二维码与物料申领订单表
DROP TABLE IF EXISTS `shop_qrcode`;
CREATE TABLE `shop_qrcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `tag_name` varchar(50) NOT NULL COMMENT '标签(如: 收银台/1号桌)',
  `scene_code` varchar(64) NOT NULL COMMENT '场景编码',
  `qrcode_url` varchar(255) NOT NULL,
  `scan_count` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_site_tag` (`site_id`, `tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家多标签二维码表';

DROP TABLE IF EXISTS `shop_material_order`;
CREATE TABLE `shop_material_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `site_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT '物料品类ID',
  `qrcode_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联二维码ID',
  `is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1首次免费 0付费制作',
  `order_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实付工本费+运费',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0待支付 1已支付/免付',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0待制作 1已发货 2已收货',
  `express_company` varchar(50) NOT NULL DEFAULT '',
  `express_no` varchar(50) NOT NULL DEFAULT '',
  `contacts_name` varchar(50) NOT NULL,
  `contacts_mobile` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `pay_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='官方物料制作申领订单表';

DROP TABLE IF EXISTS `shop_level_order`;
CREATE TABLE `shop_level_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `out_trade_no` varchar(64) NOT NULL COMMENT '商户交易流水号',
  `site_id` int(11) NOT NULL COMMENT '商户ID',
  `from_level_id` int(11) NOT NULL DEFAULT '0' COMMENT '原等级ID',
  `to_level_id` int(11) NOT NULL COMMENT '目标等级ID',
  `order_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实付支付金额',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未支付 1已支付',
  `pay_type` varchar(20) NOT NULL DEFAULT 'wechat' COMMENT '支付方式',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `pay_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='店铺等级购买与升级续费订单表';
