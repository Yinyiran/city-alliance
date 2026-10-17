<?php
/**

 */

namespace app\model\order;

use app\model\goods\Appointment;
use app\model\goods\GoodsStock;
use app\model\message\Message;
use app\model\verify\Verify;

/**
 * 门店自提订单
 *
 * @author Administrator
 *
 */
class StoreOrder extends OrderCommon
{

    /*****************************************************************************************订单状态***********************************************/
    // 订单创建
    const ORDER_CREATE = 0;

    // 订单已支付
    const ORDER_PAY = 1;

    // 订单待提货
    const ORDER_PENDING_DELIVERY = 2;

    // 订单已发货（配货）
    const ORDER_DELIVERY = 3;

    // 订单已收货
    const ORDER_TAKE_DELIVERY = 4;

    // 订单已结算完成
    const ORDER_COMPLETE = 10;

    // 订单已关闭
    const ORDER_CLOSE = -1;


    /**
     * 订单类型
     *
     * @var int
     */
    public $order_type = 2;


    /**
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
        self::ORDER_PENDING_DELIVERY => [
            'status' => self::ORDER_PENDING_DELIVERY,
            'name' => '待提货',
            'is_allow_refund' => 0,
            'icon' => 'upload/uniapp/order/order-icon-send.png',
            'action' => [
//                [
//                    'action' => 'orderDelivery',
//                    'title'=> '提货',
//                    'color'=> ''
//                ],
            ],
            'member_action' => [

            ],
            'color' => ''
        ],
        self::ORDER_TAKE_DELIVERY => [
            'status' => self::ORDER_TAKE_DELIVERY,
            'name' => '已提货',
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
        $verify = new Verify();
        $order_goods_list = model("order_goods")->getList([ [ "order_id", "=", $order_info[ "order_id" ] ] ], "sku_image,sku_name,price,num,order_goods_id,goods_id,sku_id");
        $item_array = [];
        foreach ($order_goods_list as $k => $v) {
            $item_array[$v['order_goods_id']] = [
                "img" => $v[ "sku_image" ],
                "name" => $v[ "sku_name" ],
                "price" => $v[ "price" ],
                "num" => $v[ "num" ],
                "remark_array" => [

                ]
            ];
        }
        $pay_time = time();
        $remark_array = array (
            [ "title" => '订单金额', "value" => $order_info[ "order_money" ] ],
            [ "title" => '订单编号', "value" => $order_info[ "order_no" ] ],
            [ "title" => '创建时间', "value" => time_to_date($order_info[ "create_time" ]) ],
            [ "title" => '付款时间', "value" => time_to_date($pay_time) ],
            [ "title" => '收货地址', "value" => $order_info[ "full_address" ] ],
            [ "title" => '选择门店', "value" => $order_info[ "delivery_store_name" ] ],
        );
        $verify_content_json = $verify->getVerifyJson($item_array, $remark_array);

        $code = $verify->addVerify("pickup", $order_info[ 'site_id' ], $order_info[ 'site_name' ], $verify_content_json, $order_info[ "order_id" ]);
        //标记核销对应的交易类型（到店自提/到店消费/预约到店）
        model('verify')->update([
            'trade_type' => intval($order_info[ 'trade_type' ] ?? 0),
        ], [ [ 'verify_code', '=', $code[ 'data' ][ 'verify_code' ] ] ]);
        $pay_type_list = $this->getPayType();
        $data = array (
            "order_status" => self::ORDER_PENDING_DELIVERY,
            "order_status_name" => $this->order_status[ self::ORDER_PENDING_DELIVERY ][ "name" ],
            "pay_status" => 1,
            "order_status_action" => json_encode($this->order_status[ self::ORDER_PENDING_DELIVERY ], JSON_UNESCAPED_UNICODE),
            "delivery_code" => $code[ 'data' ][ 'verify_code' ],
            "pay_time" => $pay_time,
            "is_enable_refund" => 1,
            "pay_type" => $pay_type,
            "pay_type_name" => $pay_type_list[ $pay_type ]
        );

        $res = model('order')->update($data, $condition);

        $result = $verify->qrcode($code[ 'data' ][ 'verify_code' ], "all", "pickup", "create");

        // 线下扫码买单/快捷付款：顾客现场付款即交易完成，无需核销
        if (intval($order_info[ 'pay_scene' ] ?? 0) == 1 && intval($order_info[ 'trade_type' ] ?? 0) == 1) {
            $this->orderComplete($order_info[ 'order_id' ]);
        }

        return $this->success($res);
    }

    /**
     * 主动提货
     * @param $delivery_code
     * @return \multitype
     */
    public function verify($delivery_code, $force = false)
    {
        $order_info = model("order")->getInfo([ [ 'delivery_code', '=', $delivery_code ] ], 'order_id, order_type, sign_time, order_status, delivery_code,site_id');
        if (empty($order_info))
            return $this->error([], "ORDER_EMPTY");
        $result = $this->activeTakeDelivery( $order_info[ "order_id" ], $force );


        if ($result[ "code" ] < 0) {
            return $result;
        }
        //核销发送通知
        $message_model = new Message();
        $message_model->sendMessage([ 'keywords' => "VERIFY", 'order_id' => $order_info[ 'order_id' ] ]);

        //预约到店：核销后预约完成
        $appointment_model = new Appointment();
        $appointment_model->finish($order_info[ 'order_id' ]);
        model('order')->update([
            'appointment_status' => 4,
            'modify_time' => time(),
        ], [ [ 'order_id', '=', $order_info[ 'order_id' ] ], [ 'trade_type', '=', 2 ] ]);
        return $result;


    }

    /**
     * 订单提货
     * @param unknown $order_id
     * @param unknown $delivery_code
     */
    public function orderTakeDelivery($order_id)
    {
        $res = model('order_goods')->update([ 'delivery_status' => 1, 'delivery_status_name' => "已提货" ], [ [ 'order_id', '=', $order_id ], [ 'refund_status', '<>', 3 ] ]);
        return $this->success($res);
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
        return [];
    }


    /**
     * 主动提货
     * @param $order_id
     */
    public function activeTakeDelivery($order_id, $force = false)
    {
        $order_condition = array (
            [ 'order_id', '=', $order_id ],
            [ 'order_type', '=', 2 ]
        );
        $order_info = model('order')->getInfo($order_condition, 'delivery_code, order_status, site_id, trade_type');
        if (empty($order_info))
            return $this->error();
        if ($order_info[ 'order_status' ] != self::ORDER_PENDING_DELIVERY)
            return $this->error([], '只有待提货状态的订单才可以提货');

        //预约到店(2)的核销时段校验：超出商家配置的「核销宽限」时给出改约提示，
        //由商家确认后可强制核销（$force=true），不直接阻断商家履约。
        if (intval($order_info[ 'trade_type' ]) == 2 && !$force) {
            $appointment_model = new \app\model\goods\Appointment();
            $appointment_log = model('goods_appointment_log')->getInfo([
                [ 'order_id', '=', $order_id ],
                [ 'status', '=', $appointment_model::STATUS_BOOKED ],
            ], 'goods_id,appointment_date,start_time,end_time,slot_text')[ 'data' ] ?? [];

            if (!empty($appointment_log)) {
                $cfg = $appointment_model->getGoodsAppointmentConfig( $appointment_log[ 'goods_id' ] );
                $grace = intval( $cfg[ 'grace_minutes' ] ) * 60;
                $slot_text = $appointment_log[ 'slot_text' ] ?: ( $appointment_log[ 'start_time' ] . '-' . $appointment_log[ 'end_time' ] );
                $now = time();
                $date = $appointment_log[ 'appointment_date' ];
                $slot_start = strtotime( $date . ' ' . $appointment_log[ 'start_time' ] );
                $slot_end = strtotime( $date . ' ' . $appointment_log[ 'end_time' ] );

                $tip = '';
                $over_minutes = 0;
                if ($date !== date('Y-m-d')) {
                    //预约的不是今天：可能是隔天/约错日期
                    $diff_days = ( strtotime( $date ) - strtotime( date('Y-m-d') ) ) / 86400;
                    $tip = '该订单的预约时段为 ' . $date . ' ' . $slot_text
                        . ( $diff_days > 0 ? '（还有 ' . intval($diff_days) . ' 天）' : '（已过期）' )
                        . '，与当前日期不符，建议先与顾客确认或引导改约。';
                } elseif ($grace > 0 && $now > $slot_end + $grace) {
                    $over_minutes = intval( round( ($now - $slot_end) / 60 ) );
                    $tip = '顾客已迟到约 ' . $over_minutes . ' 分钟，超出预约时段（' . $slot_text . '）与商家设置的核销宽限，建议引导改约。';
                } elseif ($grace > 0 && $now < $slot_start - $grace) {
                    $over_minutes = intval( round( ($slot_start - $now) / 60 ) );
                    $tip = '距离预约时段（' . $slot_text . '）开始还有 ' . $over_minutes . ' 分钟，尚未进入可核销时段，建议与顾客确认后再核销。';
                }

                if ($tip !== '') {
                    $result = $this->error( [], $tip );
                    $result[ 'code' ] = -2;//-2 表示需商家确认，非业务失败
                    $result[ 'data' ] = [
                        'need_force' => true,
                        'slot_text' => $slot_text,
                        'appointment_date' => $date,
                        'over_minutes' => $over_minutes,
                    ];
                    return $result;
                }
            }
        }

        $result = $this->orderCommonTakeDelivery([['order_id', '=', $order_id]]);

        if ($result[ "code" ] < 0) {
            return $result;
        }
        //核销发送通知
        $message_model = new Message();
        $message_model->sendMessage([ 'keywords' => "VERIFY", 'order_id' => $order_id, 'site_id' => $order_info[ 'site_id' ] ]);
        return $result;
    }
}