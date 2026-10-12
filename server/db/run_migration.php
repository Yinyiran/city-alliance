<?php
$dbConfig = include __DIR__ . '/../config/database.php';
$conn = $dbConfig['connections']['mysql'];
$host = $conn['hostname'] == '127.0.0.1' ? '8.149.138.113' : $conn['hostname'];
$dbname = $conn['database'];
$user = $conn['username'];
$pass = $conn['password'];
$port = $conn['hostport'] ?? 3306;

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected successfully to {$dbname}\n";

    // 1. Create member_level_settlement
    $sql1 = "CREATE TABLE IF NOT EXISTS `member_level_settlement` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员购买与开通多级分润明细表';";
    $pdo->exec($sql1);
    echo "1. Table member_level_settlement created/verified.\n";

    // 2. Helper to add column if not exists
    $addColumn = function($pdo, $table, $column, $definition) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
            echo "Added `{$column}` to `{$table}`\n";
        } else {
            echo "Column `{$column}` already exists in `{$table}`\n";
        }
    };

    // Columns for member_withdraw
    $addColumn($pdo, 'member_withdraw', 'id_card', "varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号'");
    $addColumn($pdo, 'member_withdraw', 'job_type', "varchar(50) NOT NULL DEFAULT '其它' COMMENT '从业类型'");
    $addColumn($pdo, 'member_withdraw', 'tax_base', "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应纳税所得额'");
    $addColumn($pdo, 'member_withdraw', 'tax_rate', "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '预扣税率%'");
    $addColumn($pdo, 'member_withdraw', 'tax_money', "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '代扣个税金额'");
    $addColumn($pdo, 'member_withdraw', 'pre_tax_money', "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '税前金额'");
    $addColumn($pdo, 'member_withdraw', 'after_tax_money', "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '税后实发金额'");
    $addColumn($pdo, 'member_withdraw', 'face_auth_status', "tinyint(1) NOT NULL DEFAULT 0 COMMENT '人脸认证状态: 0未认证 1已认证'");
    $addColumn($pdo, 'member_withdraw', 'face_auth_sn', "varchar(100) NOT NULL DEFAULT '' COMMENT '人脸核验流水号'");

    // Columns for member
    $pdo->exec("ALTER TABLE `member` AUTO_INCREMENT = 100001;");
    echo "Set member table AUTO_INCREMENT to 100001.\n";
    $addColumn($pdo, 'member', 'is_face_auth', "tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否完成人脸核身'");
    $addColumn($pdo, 'member', 'id_card', "varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号码'");
    $addColumn($pdo, 'member', 'job_type', "varchar(50) NOT NULL DEFAULT '其它' COMMENT '任职从业类型'");

    // Columns for reward_config
    $addColumn($pdo, 'reward_config', 'self_order_rate', "decimal(10,2) NOT NULL DEFAULT '2.00' COMMENT '自签单服务费%'");
    $addColumn($pdo, 'reward_config', 'self_city_rate', "decimal(10,2) NOT NULL DEFAULT '0.50' COMMENT '自签单分公司%'");
    $addColumn($pdo, 'reward_config', 'self_platform_rate', "decimal(10,2) NOT NULL DEFAULT '1.50' COMMENT '自签单总部%'");
    $addColumn($pdo, 'reward_config', 'bring_order_rate', "decimal(10,2) NOT NULL DEFAULT '6.00' COMMENT '带单总佣金%'");
    $addColumn($pdo, 'reward_config', 'bring_shop_rate', "decimal(10,2) NOT NULL DEFAULT '2.00' COMMENT '带单主理人%'");
    $addColumn($pdo, 'reward_config', 'bring_city_rate', "decimal(10,2) NOT NULL DEFAULT '1.50' COMMENT '带单分公司%'");
    $addColumn($pdo, 'reward_config', 'bring_level1_rate', "decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '普通会员带单%'");
    $addColumn($pdo, 'reward_config', 'bring_level2_rate', "decimal(10,2) NOT NULL DEFAULT '0.20' COMMENT '钻石会员带单%'");
    $addColumn($pdo, 'reward_config', 'bring_level3_rate', "decimal(10,2) NOT NULL DEFAULT '0.30' COMMENT '黑钻会员带单%'");
    $addColumn($pdo, 'reward_config', 'bring_level4_rate', "decimal(10,2) NOT NULL DEFAULT '0.50' COMMENT '合伙人带单%'");
    $addColumn($pdo, 'reward_config', 'member_shop_rate', "decimal(10,2) NOT NULL DEFAULT '25.00' COMMENT '会员费主理人%'");
    $addColumn($pdo, 'reward_config', 'member_city_rate', "decimal(10,2) NOT NULL DEFAULT '25.00' COMMENT '会员费分公司%'");
    $addColumn($pdo, 'reward_config', 'withdraw_rate', "decimal(10,2) NOT NULL DEFAULT '0.35' COMMENT '提现手续费率%'");
    $addColumn($pdo, 'reward_config', 'withdraw_min_money', "decimal(10,2) NOT NULL DEFAULT '100.00' COMMENT '最低提现金额'");
    $addColumn($pdo, 'reward_config', 'sign_shop_fee', "decimal(10,2) NOT NULL DEFAULT '2.00' COMMENT '签到扣商家费'");
    $addColumn($pdo, 'reward_config', 'sign_user_reward', "decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '签到奖用户现金'");
    $addColumn($pdo, 'reward_config', 'custom_partner_shop_rate', "decimal(10,2) NOT NULL DEFAULT '50.00' COMMENT '老板开通合伙人主理人比例%'");
    $addColumn($pdo, 'reward_config', 'custom_partner_city_rate', "decimal(10,2) NOT NULL DEFAULT '50.00' COMMENT '老板开通合伙人分公司比例%'");

    // Update member_level discounts & points to match new requirements
    $pdo->exec("UPDATE member_level SET consume_discount = 100.00, point_feedback = 1.00, send_point = 0 WHERE level_id = 1;");
    $pdo->exec("UPDATE member_level SET consume_discount = 98.00, point_feedback = 1.20, send_point = 1000 WHERE level_id = 2;");
    $pdo->exec("UPDATE member_level SET consume_discount = 96.00, point_feedback = 1.30, send_point = 3000 WHERE level_id = 3;");
    $pdo->exec("UPDATE member_level SET consume_discount = 95.00, point_feedback = 1.50, send_point = 10000 WHERE level_id = 4;");
    echo "Updated member_level defaults (98/96/95 discounts and 1000/3000/10000 points).\n";

    // Insert Cron task if not exists
    $cronExists = $pdo->query("SELECT count(*) FROM cron WHERE event = 'CronMemberLevelRewardUnlock'")->fetchColumn();
    if (!$cronExists) {
        $now = time();
        $pdo->exec("INSERT INTO cron (`type`, `period`, `period_type`, `name`, `event`, `execute_time`, `relate_id`, `create_time`) VALUES (2, 1, 3, '会员购买奖励满7天+消费自动解冻', 'CronMemberLevelRewardUnlock', {$now}, 0, {$now});");
        echo "Added CronMemberLevelRewardUnlock task to cron table.\n";
    }

    echo "ALL MIGRATIONS COMPLETED SUCCESSFULLY!\n";
} catch (\Throwable $e) {
    echo "MIGRATION ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
