<?php
/**

 */

namespace app\model\order;

use app\model\express\LocalPackage;
use app\model\goods\GoodsStock;

/**
 * 外卖订单（商家自配送，不接入第三方外卖平台）
 *
 * 交易流程（商家在 PC 端/商家小程序逐级点击操作）：
 *   待支付(0) → 待接单(1) →【商家接单】→ 备餐中(2) →【商家开始配送(填配送员)】→ 配送中(3) →【商家确认送达】→ 已送达(4) → 结算 → 已完成(10)
 * 其中接单可跳过：待接单状态下也可直接「开始配送」。
 */
class LocalOrder extends OrderCommon
{
    /*****************************************************************************************订单基础状态（其他使用）********************************/
    // 订单待付款
    const ORDER_CREATE = 0;

    // 订单已支付（待商家接单）
    const ORDER_PAY = 1;

    // 订单备货中（商家已接单，备餐中）
    const ORDER_PENDING_DELIVERY = 2;

    // 订单配送中（商家已安排配送员取货）
    const ORDER_DELIVERY = 3;

    // 订单已送达
    const ORDER_TAKE_DELIVERY = 4;

    // 订单已结算完成
    const ORDER_COMPLETE = 10;

    // 订单已关闭
    const ORDER_CLOSE = -1;

    // 本模型对应的订单类型（与 OrderCommon::$order_type 键对应：3=外卖订单）
    const ORDER_TYPE_LOCAL = 3;

    /***********************************************************************************订单项  配送状态**************************************************/
    // 待发货
    const DELIVERY_WAIT = 0;

    // 已发货
    const DELIVERY_DOING = 1;

    // 已收货
    const DELIVERY_FINISH = 2;


    /**
     * 订单类型
     *
     * @var int
     */
    public $order_type = 3;


    /**
     * 基础订单状态(不同类型的订单可以不使用这些状态，但是不能冲突)
     * @var unknown
     */
    public $order_status = [
        self::ORDER_CREATE => [
            'status' => self::ORDER_CREATE,
            'name' => '待支付',
            'is_allow_refund' => 0,
            'icon' => 'upload/uniapp/order/order-icon.png',
            'action' => [
                [
                    'action' => 'orderClose',
                    'title' => '关闭订单',
                    'color' => ''
                ],
                [
                    'action' => 'orderAddressUpdate',
                    'title' => '修改地址',
                    'color' => ''
                ],
                [
                    'action' => 'orderAdjustMoney',
                    'title' => '调整价格',
                    'color' => ''
                ],
            ],
            'member_action' => [
                [
                    'action' => 'orderClose',
                    'title' => '关闭订单',
                    'color' => ''
                ],
                [
                    'action' => 'orderPay',
                    'title' => '支付',
                    'color' => ''
                ],
            ],
            'color' => ''
        ],
        self::ORDER_PAY => [
            'status' => self::ORDER_PAY,
            'name' => '待接单',
            'is_allow_refund' => 0,
            'icon' => 'upload/uniapp/order/order-icon-send.png',
            'action' => [
                [
                    'action' => 'orderLocalTake',
                    'title' => '接单',
                    'color' => ''
                ],
                [
                    'action' => 'orderLocalDelivery',
                    'title' => '开始配送',
                    'color' => ''
                ],
                [
                    'action' => 'orderAddressUpdate',
                    'title' => '修改地址',
                    'color' => ''
                ],
            ],
            'member_action' => [

            ],
            'color' => ''
        ],
        self::ORDER_PENDING_DELIVERY => [
            'status' => self::ORDER_PENDING_DELIVERY,
            'name' => '备餐中',
            'is_allow_refund' => 0,
            'icon' => 'upload/uniapp/order/order-icon-send.png',
            'action' => [
                [
                    'action' => 'orderLocalDelivery',
                    'title' => '开始配送',
                    'color' => ''
                ],
                [
                    'action' => 'orderAddressUpdate',
                    'title' => '修改地址',
                    'color' => ''
                ],
            ],
            'member_action' => [

            ],
            'color' => ''
        ],
        self::ORDER_DELIVERY => [
            'status' => self::ORDER_DELIVERY,
            'name' => '配送中',
            'is_allow_refund' => 1,
            'icon' => 'upload/uniapp/order/order-icon-receive.png',
            'action' => [
                [
                    'action' => 'orderLocalFinish',
                    'title' => '确认送达',
                    'color' => ''
                ],
            ],
            'member_action' => [
                [
                    'action' => 'memberTakeDelivery',
                    'title' => '确认收货',
                    'color' => ''
                ],
            ],
            'color' => ''
        ],
        self::ORDER_TAKE_DELIVERY => [
            'status' => self::ORDER_TAKE_DELIVERY,
            'name' => '已送达',
            'is_allow_refund' => 1,
            'icon' => 'upload/uniapp/order/order-icon-received.png',
            'action' => [
            ],
            'member_action' => [
            ],
            'color' => ''
        ],
        self::ORDER_COMPLETE => [
            'status' => self::ORDER_COMPLETE,
            'name' => '已完成',
            'is_allow_refund' => 1,
            'icon' => 'upload/uniapp/order/order-icon-received.png',
            'action' => [
            ],
            'member_action' => [

            ],
            'color' => ''
        ],
        self::ORDER_CLOSE => [
            'status' => self::ORDER_CLOSE,
            'name' => '已关闭',
            'is_allow_refund' => 0,
            'icon' => 'upload/uniapp/order/order-icon-close.png',
            'action' => [

            ],
            'member_action' => [

            ],
            'color' => ''
        ],
    ];

    /**
     * 配送状态
     */
    public $delivery_status = [
        self::DELIVERY_WAIT => [
            'status' => self::DELIVERY_WAIT,
            'name' => '待配送',
            'color' => ''
        ],
        self::DELIVERY_DOING => [
            'status' => self::DELIVERY_DOING,
            'name' => '配送中',
            'color' => ''
        ],
        self::DELIVERY_FINISH => [
            'status' => self::DELIVERY_FINISH,
            'name' => '已送达',
            'color' => ''
        ]
    ];

    /**
     * 订单支付
     * @param unknown $order_info
     */
    public function orderPay($order_info, $pay_type)
    {
        if ($order_info[ 'order_status' ] != 0) {
            return $this->error();
        }
        $condition = array (
            [ "order_id", "=", $order_info[ "order_id" ] ],
            [ "order_status", "=", self::ORDER_CREATE ],
        );
        $pay_type_list = $this->getPayType();
        $data = array (
            "order_status" => self::ORDER_PAY,
            "order_status_name" => $this->order_status[ self::ORDER_PAY ][ "name" ],
            "pay_status" => 1,
            "order_status_action" => json_encode($this->order_status[ self::ORDER_PAY ], JSON_UNESCAPED_UNICODE),
            "pay_time" => time(),
            "is_enable_refund" => 1,
            "pay_type" => $pay_type,
            "pay_type_name" => $pay_type_list[ $pay_type ]
        );
        $result = model("order")->update($data, $condition);
        return $this->success($result);
    }

    /**
     * 商家接单（待接单 → 备餐中）
     * @param int $order_id
     * @param int $site_id 店铺id，用于归属校验（防跨店铺越权）
     * @return array
     */
    public function orderLocalTake($order_id, $site_id = 0)
    {
        $condition = [ [ 'order_id', '=', $order_id ] ];
        //商家端必须校验订单归属，防止任意商家对接单/确认送达其他店铺的外卖订单
        if (!empty($site_id)) {
            $condition[] = [ 'site_id', '=', $site_id ];
        }
        $order_info = model('order')->getInfo($condition, 'order_id,order_status,order_type,pay_status');
        if (empty($order_info) || $order_info[ 'order_type' ] != self::ORDER_TYPE_LOCAL) {
            return $this->error([], '订单不存在或不是外卖订单');
        }
        if ($order_info[ 'order_status' ] != self::ORDER_PAY) {
            return $this->error([], '仅待接单状态的订单可执行接单操作');
        }

        $lock_result = $this->verifyOrderLock($order_id);
        if ($lock_result[ "code" ] < 0) return $lock_result;

        $data = array (
            "order_status" => self::ORDER_PENDING_DELIVERY,
            "order_status_name" => $this->order_status[ self::ORDER_PENDING_DELIVERY ][ "name" ],
            "order_status_action" => json_encode($this->order_status[ self::ORDER_PENDING_DELIVERY ], JSON_UNESCAPED_UNICODE),
            "modify_time" => time()
        );
        $res = model('order')->update($data, [
            [ 'order_id', "=", $order_id ],
            [ 'order_status', "=", self::ORDER_PAY ]
        ]);

        //商家接单日志
        $this->addOrderLog([
            'uid' => 0,
            'nick_name' => '商家操作',
            'action' => '外卖商家接单',
            'order_status' => self::ORDER_PENDING_DELIVERY,
            'order_status_name' => '备餐中',
            'module' => 'shop',
            'action_way' => 'orderLocalTake',
            'order_id' => $order_id
        ]);

        return $this->success($res);
    }

    /**
     * 商家确认送达（配送中 → 已送达）
     * @param int $order_id
     * @param int $site_id 店铺id，用于归属校验（防跨店铺越权）
     * @return array
     */
    public function orderLocalFinishDelivery($order_id, $site_id = 0)
    {
        $condition = [ [ 'order_id', '=', $order_id ] ];
        //商家端必须校验订单归属，防止任意商家对接单/确认送达其他店铺的外卖订单
        if (!empty($site_id)) {
            $condition[] = [ 'site_id', '=', $site_id ];
        }
        $order_info = model('order')->getInfo($condition, 'order_id,order_status,order_type,delivery_status');
        if (empty($order_info) || $order_info[ 'order_type' ] != self::ORDER_TYPE_LOCAL) {
            return $this->error([], '订单不存在或不是外卖订单');
        }
        if ($order_info[ 'order_status' ] != self::ORDER_DELIVERY) {
            return $this->error([], '仅配送中的订单可执行确认送达操作');
        }

        $lock_result = $this->verifyOrderLock($order_id);
        if ($lock_result[ "code" ] < 0) return $lock_result;

        model('order')->startTrans();
        try {
            //订单项同步为已送达
            model('order_goods')->update([
                'delivery_status' => self::DELIVERY_FINISH,
                'delivery_status_name' => $this->delivery_status[ self::DELIVERY_FINISH ][ 'name' ]
            ], [ [ 'order_id', '=', $order_id ], [ 'delivery_status', '<>', self::DELIVERY_FINISH ] ]);

            $data = array (
                "order_status" => self::ORDER_TAKE_DELIVERY,
                "order_status_name" => $this->order_status[ self::ORDER_TAKE_DELIVERY ][ "name" ],
                "order_status_action" => json_encode($this->order_status[ self::ORDER_TAKE_DELIVERY ], JSON_UNESCAPED_UNICODE),
                "delivery_status" => self::DELIVERY_FINISH,
                "delivery_status_name" => $this->delivery_status[ self::DELIVERY_FINISH ][ 'name' ],
                "delivery_time" => time(),
                "sign_time" => time(),
                "modify_time" => time()
            );
            $res = model('order')->update($data, [
                [ 'order_id', "=", $order_id ],
                [ 'order_status', "=", self::ORDER_DELIVERY ]
            ]);

            //订单日志
            $this->addOrderLog([
                'uid' => 0,
                'nick_name' => '商家操作',
                'action' => '外卖确认送达',
                'order_status' => self::ORDER_TAKE_DELIVERY,
                'order_status_name' => '已送达',
                'module' => 'shop',
                'action_way' => 'orderLocalFinish',
                'order_id' => $order_id
            ]);

            model('order')->commit();
            return $this->success($res);
        } catch (\Exception $e) {
            model('order')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 顾客确认收货（配送中 → 已送达）
     * 原为空实现，导致顾客对外卖订单确认收货无任何校验与订单项同步
     * @param int $order_id
     * @return array
     */
    public function orderTakeDelivery($order_id)
    {
        $order_info = model('order')->getInfo([ [ 'order_id', '=', $order_id ] ], 'order_id,order_status,order_type');
        if (empty($order_info)) return $this->error([], "ORDER_EMPTY");
        if ($order_info[ 'order_status' ] != self::ORDER_DELIVERY) {
            return $this->error([], '仅配送中的外卖订单可确认收货');
        }

        model('order_goods')->update([
            'delivery_status' => self::DELIVERY_FINISH,
            'delivery_status_name' => $this->delivery_status[ self::DELIVERY_FINISH ][ 'name' ]
        ], [ [ 'order_id', '=', $order_id ], [ 'delivery_status', '<>', self::DELIVERY_FINISH ] ]);

        return $this->success(1);
    }

    /**
     * 订单项发货（物流）
     * @param $param
     * @return array
     */
    public function orderGoodsDelivery($param)
    {
        model('order_goods')->startTrans();
        try {
            $delivery_no = $param[ "delivery_no" ] ?? '';//物流单号
            $delivery_type = $param[ "delivery_type" ] ?? 'default';
            $order_id = $param[ 'order_id' ] ?? 0;
            $site_id = $param[ "site_id" ];

            //状态前置校验：仅待接单(1)/备餐中(2)可开始配送；已配送中/已送达不可重复操作
            $order_status_info = model('order')->getInfo([ [ 'order_id', '=', $order_id ] ], 'order_status,order_type');
            if (empty($order_status_info) || $order_status_info[ 'order_type' ] != self::ORDER_TYPE_LOCAL) {
                model('order_goods')->rollback();
                return $this->error([], '订单不存在或不是外卖订单');
            }
            if (!in_array(intval($order_status_info[ 'order_status' ]), [ self::ORDER_PAY, self::ORDER_PENDING_DELIVERY ], true)) {
                model('order_goods')->rollback();
                return $this->error([], '当前订单状态不可开始配送');
            }

            $order_goods_list = model('order_goods')->getList([ [ 'site_id', '=', $site_id ], [ 'order_id', '=', $order_id ] ], 'sku_id,num,order_id,sku_name,sku_image,member_id,refund_status,order_goods_id');
            if (empty($order_goods_list)) {
                model('order_goods')->rollback();
                return $this->error('', "发送货物不可为空!");
            }

            $order_goods_id_array = [];
            $goods_id_array = [];
            foreach ($order_goods_list as $k => $order_goods_info) {

                //已全额退款的订单项跳过，其余订单项正常配送
                //（原逻辑遇退款项直接 commit 并报错，会提交半成品事务且导致含退款项的订单永久无法配送）
                if ($order_goods_info[ "refund_status" ] == 3) {
                    continue;
                }
                $order_goods_id_array[] = $order_goods_info[ 'order_goods_id' ];
                $order_id = $order_goods_info[ "order_id" ];
                $member_id = $order_goods_info[ "member_id" ];

                $goods_id_array[] = array (
                    'sku_id' => $order_goods_info[ "sku_id" ],
                    'num' => $order_goods_info[ "num" ],
                    'sku_name' => $order_goods_info[ "sku_name" ],
                    'sku_image' => $order_goods_info[ "sku_image" ],
                );

                $data = [ "delivery_status" => self::DELIVERY_DOING, "delivery_status_name" => $this->delivery_status[ self::DELIVERY_DOING ][ "name" ] ];
                if (!empty($delivery_no)) {
                    $data[ 'delivery_no' ] = $delivery_no;
                }
                $res = model('order_goods')->update($data, [
                    [ 'order_goods_id', "=", $order_goods_info[ 'order_goods_id' ] ],
                    [ 'delivery_status', "=", self::DELIVERY_WAIT ]
                ]);
            }

            //创建包裹
            $order_common_model = new OrderCommon();
            $lock_result = $order_common_model->verifyOrderLock($order_id);
            if ($lock_result[ "code" ] < 0) {
                model('order_goods')->rollback();
                return $lock_result;
            }

            $local_delivery_model = new LocalPackage();
            $delivery_data = array (
                "order_id" => $order_id,
                "order_goods_id_array" => $order_goods_id_array,
                "goods_id_array" => $goods_id_array,
                "goods_array" => $goods_id_array,
                "site_id" => $site_id,
                "delivery_no" => $delivery_no,
                "member_id" => $member_id,
                "delivery_type" => $delivery_type,
                "deliverer" => $param[ 'deliverer' ],
                "deliverer_mobile" => $param[ 'deliverer_mobile' ],
            );
            $result = $local_delivery_model->delivery($delivery_data);
            //检测整体, 订单中订单项是否全部发放完毕
            //需校验返回码：失败时回滚，避免订单项已置为配送中而订单主表状态未推进
            $res = $this->orderCommonDelivery($order_id);
            if (isset($res[ 'code' ]) && $res[ 'code' ] < 0) {
                model('order_goods')->rollback();
                return $res;
            }
            model('order_goods')->commit();
            return $this->success($res);
        } catch (\Exception $e) {
            model('order_goods')->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 订单发货
     *
     * @param array $condition
     */
    public function orderDelivery($order_id)
    {
        //统计订单项目
        $count = model('order_goods')->getCount([ [ 'order_id', "=", $order_id ], [ 'delivery_status', "=", self::DELIVERY_WAIT ], [ "refund_status", "<>", 3 ] ], "order_goods_id");
        $delivery_count = model('order_goods')->getCount([ [ 'order_id', "=", $order_id ], [ 'delivery_status', "=", self::DELIVERY_DOING ], [ "refund_status", "<>", 3 ] ], "order_goods_id");
        if ($count == 0 && $delivery_count > 0) {

            $order_info = model('order')->getInfo([ [ 'order_id', "=", $order_id ] ], 'site_id');

            //修改订单项的配送状态
            //注：此处为「开始配送」，配送状态应为配送中(1)，原误写为已送达(2)会导致
            //    送达前订单项就显示已送达，且商家确认送达时无订单项可再更新
            $order_data = array (
                'order_status' => self::ORDER_DELIVERY,
                'order_status_name' => $this->order_status[ self::ORDER_DELIVERY ][ "name" ],
                'delivery_status' => self::DELIVERY_DOING,
                'delivery_status_name' => $this->delivery_status[ self::DELIVERY_DOING ][ "name" ],
                'order_status_action' => json_encode($this->order_status[ self::ORDER_DELIVERY ], JSON_UNESCAPED_UNICODE),
                'delivery_time' => time()
            );
            //带状态前置条件更新，防止 TOCTOU 场景下把已送达/已完成的订单拉回配送中
            $res = model('order')->update($order_data, [
                [ 'order_id', "=", $order_id ],
                [ 'order_status', "in", [ self::ORDER_PAY, self::ORDER_PENDING_DELIVERY ] ]
            ]);

//            //获取订单自动收货时间
//            $config_model = new Config();
//            $event_time_config_result = $config_model->getOrderEventTimeConfig($order_info['site_id']);
//            $event_time_config = $event_time_config_result["data"];
//            $now_time = time();//当前时间
//
//            if (!empty($event_time_config)) {
//                $execute_time = $now_time + $event_time_config["value"]["auto_take_delivery"] * 86400;//自动收货时间
//            } else {
//                $execute_time = $now_time + 86400;//尚未配置  默认一天
//            }
//            //默认自动时间
//            $cron_model = new Cron();
//            $cron_model->addCron(1, 1, "订单自动收货", "CronOrderTakeDelivery", $execute_time, $order_id);
//
//            event('OrderDelivery', ['order_id' => $order_id]);
//
//            //订单发货消息
//            $message_model = new Message();
//            $message_model->sendMessage(['keywords' => "ORDER_DELIVERY", 'order_id' => $order_id, 'site_id' => $order_info['site_id']]);

            return $res;
        } else {
            return $this->error();
        }

    }

    /**
     * 退款完成操作
     * @param $order_info
     */
    public function refund($order_goods_info)
    {
        //是否入库
        if ($order_goods_info[ "is_refund_stock" ] == 1) {
            $goods_stock_model = new GoodsStock();
            $item_param = array (
                "sku_id" => $order_goods_info[ "sku_id" ],
                "num" => $order_goods_info[ "num" ],
            );
            //返还库存
            $goods_stock_model->incStock($item_param);
        }
    }

    /**
     * 订单详情
     * @param $order_info
     */
    public function orderDetail($order_info)
    {
        $local_package_model = new LocalPackage();
        $package_info = $local_package_model->package([ 'order_id' => $order_info[ 'order_id' ] ], $order_info[ 'mobile' ]);
        $data[ 'package_list' ] = $package_info[ 'data' ];
        return $data;
    }
}