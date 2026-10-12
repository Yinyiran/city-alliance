<?php

namespace app\model\signin;

use app\model\BaseModel;
use app\model\system\Config as ConfigModel;
use app\model\shop\ShopAccount;
use app\model\member\MemberAccount;
use think\facade\Db;
use think\facade\Log;

/**
 * 商家扫码签到收费与用户返现结算模型
 */
class SignSettlement extends BaseModel
{
    const CONFIG_KEY = 'SIGN_SETTLEMENT_CONFIG';

    private static $is_initialized = false;

    public function __construct()
    {
        if (!self::$is_initialized) {
            $this->initTable();
            self::$is_initialized = true;
        }
    }

    /**
     * 自动检测并初始化结算数据表
     */
    public function initTable()
    {
        try {
            $sql_record = "CREATE TABLE IF NOT EXISTS `sign_settlement_record` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到收费与返现流水明细表';";

            $sql_bill = "CREATE TABLE IF NOT EXISTS `sign_settlement_bill` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='签到周结算账单汇总表';";

            Db::execute($sql_record);
            Db::execute($sql_bill);

            // 自动注册后台菜单项
            $menus = [
                [
                    'app_module' => 'admin',
                    'addon' => '',
                    'title' => '签到流水明细',
                    'name' => 'SIGN_SETTLEMENT_RECORD',
                    'parent' => 'SIGN_ROOT',
                    'level' => 3,
                    'url' => 'admin/sign/settlementrecords',
                    'is_show' => 1,
                    'sort' => 4,
                    'is_control' => 0
                ],
                [
                    'app_module' => 'admin',
                    'addon' => '',
                    'title' => '签到周账单',
                    'name' => 'SIGN_SETTLEMENT_BILL',
                    'parent' => 'SIGN_ROOT',
                    'level' => 3,
                    'url' => 'admin/sign/bills',
                    'is_show' => 1,
                    'sort' => 5,
                    'is_control' => 0
                ],
                [
                    'app_module' => 'admin',
                    'addon' => '',
                    'title' => '签到结算设置',
                    'name' => 'SIGN_SETTLEMENT_CONFIG',
                    'parent' => 'SIGN_ROOT',
                    'level' => 3,
                    'url' => 'admin/sign/config',
                    'is_show' => 1,
                    'sort' => 6,
                    'is_control' => 0
                ]
            ];
            foreach ($menus as $m) {
                $exist = Db::name('menu')->where('name', $m['name'])->find();
                if (empty($exist)) {
                    Db::name('menu')->insert($m);
                }
            }

            // 自动注册每周一凌晨定时任务
            $cron_exist = Db::name('cron')->where('event', 'CronSignSettlement')->find();
            if (empty($cron_exist)) {
                $next_monday = strtotime('next monday 01:00:00');
                $cron_data = [
                    'type' => 2, // 循环任务
                    'period' => 1,
                    'period_type' => 2, // 按周
                    'name' => '签到周结算',
                    'event' => 'CronSignSettlement',
                    'execute_time' => $next_monday,
                    'relate_id' => 0,
                    'create_time' => time()
                ];
                Db::name('cron')->insert($cron_data);
            }
        } catch (\Throwable $e) {
            // 静默忽略初始化异常，避免在未完成框架服务绑定或无库连接时中断业务
        }
    }

    /**
     * 获取签到结算配置
     */
    public function getConfig()
    {
        $config_model = new ConfigModel();
        $condition = [
            ['site_id', '=', 0],
            ['app_module', '=', 'admin'],
            ['config_key', '=', self::CONFIG_KEY]
        ];
        $res = $config_model->getConfig($condition);
        if (empty($res['data']) || empty($res['data']['value'])) {
            // 尝试读取 reward_config 中的 sign_shop_fee 和 sign_user_reward
            $rc = Db::name('reward_config')->find();
            $shop_fee = (!empty($rc) && isset($rc['sign_shop_fee'])) ? (string)$rc['sign_shop_fee'] : '2.00';
            $user_reward = (!empty($rc) && isset($rc['sign_user_reward'])) ? (string)$rc['sign_user_reward'] : '1.00';
            $default = [
                'is_open' => 1,
                'shop_fee' => $shop_fee,
                'user_reward' => $user_reward,
                'cycle_type' => 'week',
                'settle_day' => 1,
                'remark' => '商家门店扫码签到每次扣除商家2元，被扫会员获得1元，每周一汇总结算'
            ];
            return $this->success($default);
        }
        $value = is_array($res['data']['value']) ? $res['data']['value'] : json_decode($res['data']['value'], true);
        return $this->success($value);
    }

    /**
     * 保存签到结算配置
     */
    public function setConfig($data)
    {
        $shop_fee = isset($data['shop_fee']) ? round((float)$data['shop_fee'], 2) : 3.00;
        $user_reward = isset($data['user_reward']) ? round((float)$data['user_reward'], 2) : 2.00;
        if ($shop_fee < 0 || $user_reward < 0) {
            return $this->error(-1, '费用或奖励金额不能小于0');
        }
        if ($shop_fee < $user_reward) {
            return $this->error(-1, '商家扣费金额不能低于用户返现奖励金额（避免平台亏损）');
        }

        $value = [
            'is_open' => !empty($data['is_open']) ? 1 : 0,
            'shop_fee' => number_format($shop_fee, 2, '.', ''),
            'user_reward' => number_format($user_reward, 2, '.', ''),
            'cycle_type' => $data['cycle_type'] ?? 'week',
            'settle_day' => isset($data['settle_day']) ? (int)$data['settle_day'] : 1,
            'remark' => $data['remark'] ?? '商家门店扫码签到收费与用户返现周结算设置'
        ];

        $config_model = new ConfigModel();
        $condition = [
            ['site_id', '=', 0],
            ['app_module', '=', 'admin'],
            ['config_key', '=', self::CONFIG_KEY]
        ];
        $res = $config_model->setConfig($value, '签到结算与分润配置', 1, $condition);
        return $res;
    }

    /**
     * 扫码签到成功后生成待结算流水明细
     * @param int $signin_id 签到记录ID
     * @param int $shop_id 商家门店ID
     * @param int $member_id 签到会员ID
     */
    public function createSettlementRecord($signin_id, $shop_id, $member_id)
    {
        $config = $this->getConfig()['data'];
        if (empty($config['is_open'])) {
            return $this->success(['status' => 'disabled']);
        }

        $shop_fee = (float)($config['shop_fee'] ?? 3.00);
        $user_reward = (float)($config['user_reward'] ?? 2.00);
        $platform_profit = max(0, $shop_fee - $user_reward);

        // 如果费用与奖励均为0，无需产生资金结算流水
        if ($shop_fee <= 0 && $user_reward <= 0) {
            return $this->success(['record_id' => 0, 'shop_fee' => 0, 'user_reward' => 0]);
        }

        $period_key = date('o\WW'); // 例如 2026W38 表示2026年第38周

        $record = [
            'signin_id' => $signin_id,
            'site_id' => $shop_id,
            'member_id' => $member_id,
            'shop_fee' => $shop_fee,
            'user_reward' => $user_reward,
            'platform_profit' => $platform_profit,
            'settle_status' => 0, // 待周结
            'period_key' => $period_key,
            'create_time' => time(),
            'settle_time' => 0
        ];

        $record_id = Db::name('sign_settlement_record')->insertGetId($record);
        return $this->success(['record_id' => $record_id, 'shop_fee' => $shop_fee, 'user_reward' => $user_reward]);
    }

    /**
     * 周结算执行逻辑（定时任务或管理员手动触发）
     * @param int $end_time 结算截止时间戳（默认为上周日23:59:59；若传入则以传入为准）
     * @param bool $force_all 是否强制结算所有未结流水（包括当前周）
     */
    public function cronWeeklySettlement($end_time = 0, $force_all = false)
    {
        // 确保数据表已初始化
        $this->initTable();

        if ($force_all) {
            $end_time = time();
        } elseif (empty($end_time)) {
            // 默认截至上周日23:59:59
            $end_time = strtotime('last sunday 23:59:59');
            if ($end_time > time()) {
                $end_time = time();
            }
        }

        // 查询所有在截止时间前、待结算的流水记录
        $condition = [
            ['settle_status', '=', 0],
            ['create_time', '<=', $end_time]
        ];
        $unsettled = Db::name('sign_settlement_record')->where($condition)->select()->toArray();
        if (empty($unsettled)) {
            return $this->success([], '没有待结算的签到记录');
        }

        $period_key = date('o\WW', $end_time);
        $start_time = strtotime('-6 days 00:00:00', $end_time);

        // 按商家门店 site_id 分组
        $shop_groups = [];
        // 按会员 member_id 分组
        $user_groups = [];
        $record_ids = [];

        foreach ($unsettled as $row) {
            $record_ids[] = $row['id'];
            $site_id = (int)$row['site_id'];
            $member_id = (int)$row['member_id'];

            if (!isset($shop_groups[$site_id])) {
                $shop_groups[$site_id] = [
                    'site_id' => $site_id,
                    'sign_count' => 0,
                    'total_shop_fee' => 0.00,
                    'record_ids' => []
                ];
            }
            $shop_groups[$site_id]['sign_count']++;
            $shop_groups[$site_id]['total_shop_fee'] += (float)$row['shop_fee'];
            $shop_groups[$site_id]['record_ids'][] = $row['id'];

            if (!isset($user_groups[$member_id])) {
                $user_groups[$member_id] = [
                    'member_id' => $member_id,
                    'sign_count' => 0,
                    'total_user_reward' => 0.00,
                    'record_ids' => []
                ];
            }
            $user_groups[$member_id]['sign_count']++;
            $user_groups[$member_id]['total_user_reward'] += (float)$row['user_reward'];
            $user_groups[$member_id]['record_ids'][] = $row['id'];
        }

        $shop_bill_count = 0;
        $user_bill_count = 0;
        $total_shop_money = 0.00;
        $total_user_money = 0.00;

        $shop_account_model = new ShopAccount();
        $member_account_model = new MemberAccount();

        Db::startTrans();
        try {
            // 1. 处理商家周账单与账户划扣
            foreach ($shop_groups as $site_id => $group) {
                $total_money = round($group['total_shop_fee'], 2);
                $sign_count = $group['sign_count'];
                $bill_no = 'SSB' . date('YmdHis') . rand(1000, 9999);

                $shop_bill = [
                    'bill_no' => $bill_no,
                    'bill_type' => 'shop',
                    'relate_id' => $site_id,
                    'period_key' => $period_key,
                    'sign_count' => $sign_count,
                    'total_money' => $total_money,
                    'pay_status' => 0,
                    'remark' => "第{$period_key}期商家签到推广服务费结算（共{$sign_count}次有效签到）",
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'create_time' => time(),
                    'finish_time' => 0
                ];
                $shop_bill_id = Db::name('sign_settlement_bill')->insertGetId($shop_bill);

                // 尝试从商家店铺账户划扣
                $pay_success = false;
                if ($total_money > 0) {
                    $deduct_res = $shop_account_model->addShopAccount(
                        $site_id,
                        'account',
                        -$total_money,
                        'sign_settlement',
                        $shop_bill_id,
                        "签到推广服务费周结算扣款，账单号：{$bill_no}"
                    );
                    if ($deduct_res['code'] >= 0) {
                        $pay_success = true;
                    }
                } else {
                    $pay_success = true;
                }

                if ($pay_success) {
                    Db::name('sign_settlement_bill')->where('bill_id', $shop_bill_id)->update([
                        'pay_status' => 1,
                        'finish_time' => time()
                    ]);
                } else {
                    // 余额不足扣款失败，账单标记为待补缴
                    Db::name('sign_settlement_bill')->where('bill_id', $shop_bill_id)->update([
                        'pay_status' => 2
                    ]);
                }

                // 回填到流水记录
                Db::name('sign_settlement_record')
                    ->whereIn('id', $group['record_ids'])
                    ->update(['shop_bill_id' => $shop_bill_id]);

                $shop_bill_count++;
                $total_shop_money += $total_money;
            }

            // 2. 处理用户周奖励与发放到钱包
            foreach ($user_groups as $member_id => $group) {
                $total_reward = round($group['total_user_reward'], 2);
                $sign_count = $group['sign_count'];
                $bill_no = 'SUB' . date('YmdHis') . rand(1000, 9999);

                $user_bill = [
                    'bill_no' => $bill_no,
                    'bill_type' => 'user',
                    'relate_id' => $member_id,
                    'period_key' => $period_key,
                    'sign_count' => $sign_count,
                    'total_money' => $total_reward,
                    'pay_status' => 1, // 发放成功
                    'remark' => "第{$period_key}期会员签到现金奖励到账（共{$sign_count}次签到）",
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'create_time' => time(),
                    'finish_time' => time()
                ];
                $user_bill_id = Db::name('sign_settlement_bill')->insertGetId($user_bill);

                // 发放到用户钱包现金余额
                if ($total_reward > 0) {
                    $member_account_model->addMemberAccount(
                        $member_id,
                        'balance',
                        $total_reward,
                        'sign_settlement',
                        $user_bill_id,
                        "签到现金奖励周结算到账（共{$sign_count}次签到）"
                    );
                }

                // 回填到流水记录
                Db::name('sign_settlement_record')
                    ->whereIn('id', $group['record_ids'])
                    ->update(['user_bill_id' => $user_bill_id]);

                $user_bill_count++;
                $total_user_money += $total_reward;
            }

            // 3. 将本批次所有流水标记为已结清
            Db::name('sign_settlement_record')->whereIn('id', $record_ids)->update([
                'settle_status' => 2,
                'settle_time' => time()
            ]);

            Db::commit();

            return $this->success([
                'period_key' => $period_key,
                'settled_record_count' => count($record_ids),
                'shop_bill_count' => $shop_bill_count,
                'total_shop_money' => $total_shop_money,
                'user_bill_count' => $user_bill_count,
                'total_user_money' => $total_user_money
            ], "周结算执行成功！共处理{$shop_bill_count}家商户，发放{$user_bill_count}位会员");
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('cronWeeklySettlement error: ' . $e->getMessage(), 'error');
            return $this->error(-1, '周结算执行异常: ' . $e->getMessage());
        }
    }

    /**
     * 获取签到流水明细分页列表
     */
    public function getRecordPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'r.id desc')
    {
        $this->initTable();
        $alias = 'r';
        $join = [
            ['shop s', 's.site_id = r.site_id', 'left'],
            ['member m', 'm.member_id = r.member_id', 'left']
        ];
        $field = 'r.*, s.site_name, s.logo as shop_logo, m.nickname, m.username, m.mobile, m.headimg';

        $count = Db::name('sign_settlement_record')
            ->alias($alias)
            ->join($join)
            ->where($condition)
            ->count();

        $list = Db::name('sign_settlement_record')
            ->alias($alias)
            ->join($join)
            ->where($condition)
            ->field($field)
            ->order($order)
            ->page($page, $page_size)
            ->select()
            ->toArray();

        return $this->success([
            'count' => $count,
            'list' => $list,
            'page_count' => ceil($count / $page_size)
        ]);
    }

    /**
     * 获取周账单汇总分页列表
     */
    public function getBillPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'b.bill_id desc')
    {
        $this->initTable();
        $alias = 'b';
        $join = [
            ['shop s', 's.site_id = b.relate_id and b.bill_type = "shop"', 'left'],
            ['member m', 'm.member_id = b.relate_id and b.bill_type = "user"', 'left']
        ];
        $field = 'b.*, s.site_name, m.nickname, m.mobile as user_mobile';

        $count = Db::name('sign_settlement_bill')
            ->alias($alias)
            ->join($join)
            ->where($condition)
            ->count();

        $list = Db::name('sign_settlement_bill')
            ->alias($alias)
            ->join($join)
            ->where($condition)
            ->field($field)
            ->order($order)
            ->page($page, $page_size)
            ->select()
            ->toArray();

        return $this->success([
            'count' => $count,
            'list' => $list,
            'page_count' => ceil($count / $page_size)
        ]);
    }

    /**
     * 获取待结算与汇总统计数据
     */
    public function getSettlementStats()
    {
        $this->initTable();
        // 待周结总览
        $pending = Db::name('sign_settlement_record')
            ->where('settle_status', 0)
            ->field('count(id) as pending_count, sum(shop_fee) as pending_shop_fee, sum(user_reward) as pending_user_reward, sum(platform_profit) as pending_platform_profit')
            ->find();

        // 已结算总览
        $settled = Db::name('sign_settlement_record')
            ->where('settle_status', 2)
            ->field('count(id) as settled_count, sum(shop_fee) as settled_shop_fee, sum(user_reward) as settled_user_reward, sum(platform_profit) as settled_platform_profit')
            ->find();

        return $this->success([
            'pending_count' => (int)($pending['pending_count'] ?? 0),
            'pending_shop_fee' => number_format((float)($pending['pending_shop_fee'] ?? 0), 2, '.', ''),
            'pending_user_reward' => number_format((float)($pending['pending_user_reward'] ?? 0), 2, '.', ''),
            'pending_platform_profit' => number_format((float)($pending['pending_platform_profit'] ?? 0), 2, '.', ''),
            'settled_count' => (int)($settled['settled_count'] ?? 0),
            'settled_shop_fee' => number_format((float)($settled['settled_shop_fee'] ?? 0), 2, '.', ''),
            'settled_user_reward' => number_format((float)($settled['settled_user_reward'] ?? 0), 2, '.', ''),
            'settled_platform_profit' => number_format((float)($settled['settled_platform_profit'] ?? 0), 2, '.', '')
        ]);
    }

    /**
     * 风控操作：作废某条异常签到流水
     */
    public function invalidRecord($id, $remark = '')
    {
        $record = Db::name('sign_settlement_record')->where('id', $id)->find();
        if (empty($record)) {
            return $this->error(-1, '流水记录不存在');
        }
        if ($record['settle_status'] == 2) {
            return $this->error(-1, '该记录已完成周结算并资金划扣，无法直接作废');
        }
        Db::name('sign_settlement_record')->where('id', $id)->update([
            'settle_status' => 3 // 已作废
        ]);
        return $this->success([], '作废成功');
    }
}
