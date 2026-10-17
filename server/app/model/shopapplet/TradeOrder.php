<?php
/**

 */

namespace app\model\shopapplet;

use app\model\BaseModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\OrderCreate;
use think\facade\Db;

/**
 * 商家端订单管理（按交易类型/配送链路分组处理订单）
 *
 * 设计要点：
 *  - 分组依据使用「实际配送链路」order_type，而非商品交易类型 trade_type。
 *    因为到店自提(trade_type=0)不强制配送方式：顾客选门店自提则生成自提订单(order_type=2, 走核销)，
 *    选快递则生成普通订单(order_type=1, 走物流)，按 order_type 分组才能准确给出可执行操作。
 *  - 待处理(pending) 的定义按链路区分：
 *      快递邮寄(order_type=1)：待发货(1)
 *      到店核销(order_type=2)：待核销(1/2)
 *      外卖自配送(order_type=3)：待接单(1) / 备餐中(2)
 *  - 状态文案统一走 OrderCommon::orderTypeStatusName()，避免各端文案漂移。
 */
class TradeOrder extends BaseModel
{
    /** 分组：全部 */
    const BIZ_ALL = 'all';
    /** 分组：快递邮寄（普通订单 order_type=1） */
    const BIZ_EXPRESS = 'express';
    /** 分组：到店核销（自提订单 order_type=2） */
    const BIZ_STORE = 'store';
    /** 分组：外卖自配送（外卖订单 order_type=3） */
    const BIZ_LOCAL = 'local';

    /** 处理阶段：待处理 */
    const STAGE_PENDING = 'pending';
    /** 处理阶段：进行中 */
    const STAGE_DOING = 'doing';
    /** 处理阶段：已完成 */
    const STAGE_FINISHED = 'finished';
    /** 处理阶段：全部 */
    const STAGE_ALL = 'all';

    /**
     * 分组 -> order_type 映射
     * @return array
     */
    public static function bizTypeMap()
    {
        return [
            self::BIZ_EXPRESS => 1,
            self::BIZ_STORE => 2,
            self::BIZ_LOCAL => 3,
        ];
    }

    /**
     * 列表查询字段
     */
    private function orderField()
    {
        return 'o.order_id,o.order_no,o.site_id,o.site_name,o.member_id,o.order_type,o.order_type_name,'
            . 'o.order_status,o.order_status_name,o.pay_status,o.pay_type_name,o.trade_type,'
            . 'o.delivery_type,o.delivery_type_name,o.delivery_store_id,o.delivery_store_name,'
            . 'o.name,o.mobile,o.full_address,o.address,o.goods_money,o.delivery_money,o.order_money,o.pay_money,'
            . 'o.buyer_message,o.remark,o.delivery_code,o.create_time,o.pay_time,o.delivery_time,'
            . 'o.appointment_date,o.appointment_slot,o.appointment_user,o.appointment_mobile,o.appointment_status,'
            . 'o.custom_amount';
    }

    /**
     * 按处理阶段拼接查询条件
     * @param \think\db\Query $query
     * @param string $stage
     */
    private function applyStage($query, $stage)
    {
        switch ($stage) {
            case self::STAGE_PENDING:
                //待处理的条件必须与状态文案矩阵的「未完成态」集合对齐，否则会出现
                //「文案显示待发货/待核销，但不在待处理列表里」的漏单：
                //  快递邮寄(order_type=1)：待发货(1/2)
                //  到店核销(order_type=2)：待核销(1/2/3)
                //  外卖自配送(order_type=3)：待接单(1)/备餐中(2)
                $query->where(function ($q) {
                    $q->where(function ($q1) {
                        $q1->where('o.order_type', 1)->whereIn('o.order_status', [1, 2]);
                    })->whereOr(function ($q2) {
                        $q2->where('o.order_type', 2)->whereIn('o.order_status', [1, 2, 3]);
                    })->whereOr(function ($q3) {
                        $q3->where('o.order_type', 3)->whereIn('o.order_status', [1, 2]);
                    });
                });
                break;
            case self::STAGE_DOING:
                //快递已发货；外卖配送中（到店核销类无进行中态，核销即完成）
                $query->where(function ($q) {
                    $q->whereIn('o.order_type', [1, 3])->where('o.order_status', 3);
                });
                break;
            case self::STAGE_FINISHED:
                $query->whereIn('o.order_status', [4, 10]);
                break;
            case self::STAGE_ALL:
            default:
                break;
        }
    }

    /**
     * 待处理订单数量统计（用于首页角标与页签计数）
     * @param int $site_id
     * @return array
     */
    public function pendingCount($site_id)
    {
        $site_id = intval($site_id);

        $express = Db::name('order')
            ->where('site_id', $site_id)
            ->where('order_type', 1)
            ->where('order_status', 1)
            ->count();

        $store = Db::name('order')
            ->where('site_id', $site_id)
            ->where('order_type', 2)
            ->whereIn('order_status', [1, 2])
            ->count();

        $local = Db::name('order')
            ->where('site_id', $site_id)
            ->where('order_type', 3)
            ->whereIn('order_status', [1, 2])
            ->count();

        return $this->success([
            'express_pending' => intval($express),
            'store_pending' => intval($store),
            'local_pending' => intval($local),
            'total_pending' => intval($express) + intval($store) + intval($local),
        ]);
    }

    /**
     * 订单管理列表
     * @param int $site_id 店铺id
     * @param string $biz_type 分组 all/express/store/local
     * @param string $stage 处理阶段 pending/doing/finished/all
     * @param string $searchtxt 关键词（订单号/收货人/商品名）
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function manageList($site_id, $biz_type = self::BIZ_ALL, $stage = self::STAGE_ALL, $searchtxt = '', $page = 1, $page_size = PAGE_LIST_ROWS)
    {
        $site_id = intval($site_id);
        $biz_map = self::bizTypeMap();

        $buildQuery = function () use ($site_id, $biz_type, $stage, $searchtxt, $biz_map) {
            $query = Db::name('order')->alias('o')
                ->where('o.site_id', $site_id)
                ->where('o.pay_status', '>', 0);   //仅展示已支付订单，未支付不在商家处理范围内

            if ($biz_type != self::BIZ_ALL && isset($biz_map[$biz_type])) {
                $query->where('o.order_type', $biz_map[$biz_type]);
            }

            $this->applyStage($query, $stage);

            if (!empty($searchtxt)) {
                $searchtxt = trim($searchtxt);
                //商品名称命中时按订单id反查，避免在 whereOr 中嵌套子查询；限定本店铺以减少扫描
                $goods_order_ids = Db::name('order_goods')
                    ->where('site_id', $site_id)
                    ->whereLike('sku_name', '%' . $searchtxt . '%')
                    ->limit(500)
                    ->column('order_id');
                $query->where(function ($q) use ($searchtxt, $goods_order_ids) {
                    $q->whereLike('o.order_no', '%' . $searchtxt . '%')
                        ->whereOr('o.name', 'like', '%' . $searchtxt . '%')
                        ->whereOr('o.mobile', 'like', '%' . $searchtxt . '%');
                    if (!empty($goods_order_ids)) {
                        $q->whereOr('o.order_id', 'in', $goods_order_ids);
                    }
                });
            }
            return $query;
        };

        $total = $buildQuery()->count();
        $page_count = $page_size > 0 ? ceil($total / $page_size) : 1;

        $list = $buildQuery()
            ->field($this->orderField())
            ->order('o.create_time desc')
            ->page($page, $page_size)
            ->select()
            ->toArray();

        //按分组统计每个阶段的数量，供前端页签展示
        $stage_count = $this->stageCount($site_id, $biz_type);

        $list = $this->formatOrderList($list, $site_id);

        return $this->success([
            'list' => $list,
            'count' => $total,
            'page_count' => $page_count,
            'stage_count' => $stage_count,
        ]);
    }

    /**
     * 各阶段订单数量
     * @param int $site_id
     * @param string $biz_type
     * @return array
     */
    public function stageCount($site_id, $biz_type = self::BIZ_ALL)
    {
        $site_id = intval($site_id);
        $biz_map = self::bizTypeMap();
        $count = [];
        foreach ([self::STAGE_PENDING, self::STAGE_DOING, self::STAGE_FINISHED] as $stage) {
            $query = Db::name('order')->alias('o')
                ->where('o.site_id', $site_id)
                ->where('o.pay_status', '>', 0);
            if ($biz_type != self::BIZ_ALL && isset($biz_map[$biz_type])) {
                $query->where('o.order_type', $biz_map[$biz_type]);
            }
            $this->applyStage($query, $stage);
            $count[$stage] = intval($query->count());
        }
        return $count;
    }

    /**
     * 批量补充订单商品、买家信息、状态文案与可执行操作
     * @param array $list
     * @param int $site_id
     * @return array
     */
    private function formatOrderList($list, $site_id)
    {
        if (empty($list)) return $list;

        $order_ids = array_column($list, 'order_id');
        $member_ids = array_unique(array_filter(array_column($list, 'member_id')));

        //订单商品
        $goods_map = [];
        if (!empty($order_ids)) {
            $goods_list = Db::name('order_goods')
                ->whereIn('order_id', $order_ids)
                ->field('order_goods_id,order_id,goods_id,sku_id,sku_name,sku_image,price,num,goods_money,delivery_status,delivery_status_name,delivery_no,refund_status,refund_status_name')
                ->select()->toArray();
            foreach ($goods_list as $g) {
                $g['sku_image'] = $g['sku_image'] ?? '';
                $goods_map[$g['order_id']][] = $g;
            }
        }

        //买家信息
        $member_map = [];
        if (!empty($member_ids)) {
            $members = Db::name('member')
                ->whereIn('member_id', $member_ids)
                ->field('member_id,nickname,headimg,mobile')
                ->select()->toArray();
            foreach ($members as $m) {
                $member_map[$m['member_id']] = $m;
            }
        }

        foreach ($list as $k => $row) {
            $order_type = intval($row['order_type']);
            $order_status = intval($row['order_status']);

            //状态文案以订单类型（实际配送链路）为准，兜底历史数据的 order_status_name 快照
            $row['trade_status_name'] = OrderCommonModel::orderTypeStatusName($order_type, $order_status, $row);
            //虚拟订单(order_type=4)不在商家配送链路内，归入 all 分组避免出现 biz_type=false
            $biz = array_search($order_type, self::bizTypeMap());
            $row['biz_type'] = $biz !== false ? $biz : self::BIZ_ALL;
            //交易类型名称：到店自提/到店消费/预约到店同属「到店核销」链路(order_type=2)，
            //但业务含义不同（自提带走 / 店内消费 / 预约时段），需单独展示避免店员混淆。
            //名称统一由 OrderCreate::tradeTypeName 提供，避免各端各自维护映射。
            $row['trade_type_name'] = OrderCreate::tradeTypeName( $row[ 'trade_type' ] ?? 0 );
            $row['goods_list'] = $goods_map[$row['order_id']] ?? [];
            $row['member_info'] = $member_map[$row['member_id']] ?? [];
            $row['nickname'] = $member_map[$row['member_id']]['nickname'] ?? ($row['name'] ?: '匿名用户');
            $row['headimg'] = $member_map[$row['member_id']]['headimg'] ?? '';
            $row['actions'] = self::buildActions($row);
            $row['express_no'] = $this->firstDeliveryNo($row['goods_list']);

            //时间统一格式化为字符串，避免各端重复处理时间戳
            foreach (['create_time', 'pay_time', 'delivery_time'] as $time_field) {
                $row[$time_field . '_text'] = !empty($row[$time_field]) ? date('Y-m-d H:i:s', $row[$time_field]) : '';
            }

            $list[$k] = $row;
        }
        return $list;
    }

    /**
     * 取首个已填写的快递单号
     * @param array $goods_list
     * @return string
     */
    private function firstDeliveryNo($goods_list)
    {
        foreach ((array) $goods_list as $g) {
            if (!empty($g['delivery_no'])) return $g['delivery_no'];
        }
        return '';
    }

    /**
     * 计算订单当前可执行的操作（前端按钮）
     * @param array $order
     * @return array
     */
    public static function buildActions($order)
    {
        $order_type = intval($order['order_type'] ?? 1);
        $order_status = intval($order['order_status'] ?? 0);
        $actions = [];

        if ($order_type == 1) {
            //快递邮寄
            if ($order_status == 1) {
                $actions[] = ['key' => 'express_delivery', 'title' => '发货'];
            }
        } elseif ($order_type == 2) {
            //到店核销
            if (in_array($order_status, [1, 2], true)) {
                $actions[] = ['key' => 'verify', 'title' => '核销'];
            }
        } elseif ($order_type == 3) {
            //外卖自配送
            if ($order_status == 1) {
                $actions[] = ['key' => 'local_take', 'title' => '接单'];
            }
            if (in_array($order_status, [1, 2], true)) {
                $actions[] = ['key' => 'local_delivery', 'title' => '开始配送'];
            }
            if ($order_status == 3) {
                $actions[] = ['key' => 'local_finish', 'title' => '确认送达'];
            }
        }
        return $actions;
    }

    /**
     * 订单详情
     * @param int $site_id
     * @param int $order_id
     * @return array
     */
    public function orderDetail($site_id, $order_id)
    {
        $site_id = intval($site_id);
        $order_id = intval($order_id);

        $order = Db::name('order')->alias('o')
            ->where('o.site_id', $site_id)
            ->where('o.order_id', $order_id)
            ->field($this->orderField())
            ->find();

        if (empty($order)) {
            return $this->error([], '订单不存在或不属于当前店铺');
        }

        $list = $this->formatOrderList([$order], $site_id);
        $detail = $list[0];

        //外卖订单补充配送信息（配送员姓名/电话由商家在「开始配送」时填写）
        if (intval($order['order_type']) == 3) {
            $package = Db::name('local_delivery_package')
                ->where('order_id', $order_id)
                ->field('id,delivery_no,deliverer,deliverer_mobile,delivery_type,delivery_time')
                ->order('id desc')
                ->find();
            $detail['package_info'] = $package ?: [];
            if (!empty($package['delivery_time'])) {
                $detail['package_info']['delivery_time_text'] = date('Y-m-d H:i:s', $package['delivery_time']);
            }
        }

        return $this->success($detail);
    }

    /**
     * 物流公司列表（发货用）
     * @param int $site_id
     * @return array
     */
    public function expressCompanyList($site_id)
    {
        $site_id = intval($site_id);

        //优先取店铺已启用的物流公司（express_company_shop 为店铺与物流公司的关联表）
        $list = Db::name('express_company_shop')->alias('ecs')
            ->join('express_company ec', 'ec.company_id = ecs.company_id')
            ->where('ecs.site_id', $site_id)
            ->field('ec.company_id,ec.company_name,ec.logo,ec.url,ec.sort')
            ->order('ec.sort asc,ec.company_id asc')
            ->select()->toArray();

        //店铺未单独配置时回退平台物流公司，避免小程序端无可选项导致无法发货
        if (empty($list)) {
            $list = Db::name('express_company')
                ->field('company_id,company_name,logo,url,sort')
                ->order('sort asc,company_id asc')
                ->limit(100)
                ->select()->toArray();
        }

        return $this->success($list);
    }
}
