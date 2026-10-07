<?php


namespace app\api\controller;

use app\model\goods\Appointment;
use app\model\order\OrderCreate as OrderCreateModel;

/**
 * 订单创建
 * @author Administrator
 *
 */
class Ordercreate extends BaseApi
{
    /**
     * 创建订单
     */
    public function create()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data = [
            'cart_ids' => isset($this->params[ 'cart_ids' ]) ? $this->params[ 'cart_ids' ] : '',
            'sku_id' => isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : '',
            'num' => isset($this->params[ 'num' ]) ? $this->params[ 'num' ] : '',
            'member_id' => $this->member_id,
            'is_balance' => isset($this->params[ 'is_balance' ]) ? $this->params[ 'is_balance' ] : 0,//是否使用余额
            'order_from' => $this->params[ 'app_type' ],
            'order_from_name' => $this->params[ 'app_type_name' ],
            'pay_password' => isset($this->params[ 'pay_password' ]) ? $this->params[ 'pay_password' ] : '',//支付密码
            'buyer_message' => isset($this->params[ "buyer_message" ]) && !empty($this->params[ "buyer_message" ]) ? json_decode($this->params[ "buyer_message" ], true) : [],
            'delivery' => isset($this->params[ "delivery" ]) && !empty($this->params[ "delivery" ]) ? json_decode($this->params[ "delivery" ], true) : [],
            'coupon' => isset($this->params[ "coupon" ]) && !empty($this->params[ "coupon" ]) ? json_decode($this->params[ "coupon" ], true) : [],
            'member_address' => isset($this->params[ "member_address" ]) && !empty($this->params[ "member_address" ]) ? json_decode($this->params[ "member_address" ], true) : [],

            'platform_coupon_id' => $this->params[ "platform_coupon_id" ] ?? 0,

            'latitude' => $this->params[ "latitude" ] ?? null,
            'longitude' => $this->params[ "longitude" ] ?? null,

            'invoice' => isset($this->params[ "invoice" ]) && !empty($this->params[ "invoice" ]) ? json_decode($this->params[ "invoice" ], true) : [],
            'video_id' => isset($this->params[ 'video_id' ]) ? $this->params[ 'video_id' ] : 0,//短视频ID

            //交易类型相关
            'custom_amount' => isset($this->params[ 'custom_amount' ]) ? $this->params[ 'custom_amount' ] : 0,//到店消费自定义金额
            'appointment' => isset($this->params[ 'appointment' ]) && !empty($this->params[ 'appointment' ]) ? json_decode($this->params[ 'appointment' ], true) : [],//预约信息
            'pay_scene' => isset($this->params[ 'pay_scene' ]) ? intval($this->params[ 'pay_scene' ]) : 0,//支付场景 0小程序 1顾客扫商家收款码

        ];
        if (empty($data[ 'cart_ids' ]) && empty($data[ 'sku_id' ])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }

        //这里都是商家核销的，物流的再写一个方法
        //核销商品和福利区分开
        if (!empty($data['cart_ids'])) {
            $cart_ids = $data['cart_ids'];
            $sku_ids = model('goods_cart')->getColumn([['cart_id', 'in', $cart_ids]],'sku_id');
            $goods_ids = model('goods_sku')->getColumn([['sku_id', 'in', $sku_ids]],'goods_id');
            $goods_type = model('goods')->getColumn([['goods_id', 'in', $goods_ids]], 'goods_type');
            if (in_array(0, $goods_type) && in_array(1, $goods_type)) {
                return $this->response($this->error('', '商家商品和福利区商品请分开结算'));
            }
        }

        $res = $order_create->create($data);
        return $this->response($res);
    }

    /**
     * 福利区订单创建
     */
    public function pointcreate()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data = [
            'cart_ids' => isset($this->params[ 'cart_ids' ]) ? $this->params[ 'cart_ids' ] : '',
            'sku_id' => isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : '',
            'num' => isset($this->params[ 'num' ]) ? $this->params[ 'num' ] : '',
            'member_id' => $this->member_id,
            'is_balance' => isset($this->params[ 'is_balance' ]) ? $this->params[ 'is_balance' ] : 0,//是否使用余额
            'order_from' => $this->params[ 'app_type' ],
            'order_from_name' => $this->params[ 'app_type_name' ],
            'pay_password' => isset($this->params[ 'pay_password' ]) ? $this->params[ 'pay_password' ] : '',//支付密码
            'buyer_message' => isset($this->params[ "buyer_message" ]) && !empty($this->params[ "buyer_message" ]) ? json_decode($this->params[ "buyer_message" ], true) : [],
            'delivery' => isset($this->params[ "delivery" ]) && !empty($this->params[ "delivery" ]) ? json_decode($this->params[ "delivery" ], true) : [],
            'coupon' => isset($this->params[ "coupon" ]) && !empty($this->params[ "coupon" ]) ? json_decode($this->params[ "coupon" ], true) : [],
            'member_address' => isset($this->params[ "member_address" ]) && !empty($this->params[ "member_address" ]) ? json_decode($this->params[ "member_address" ], true) : [],

            'platform_coupon_id' => $this->params[ "platform_coupon_id" ] ?? 0,

            'latitude' => $this->params[ "latitude" ] ?? null,
            'longitude' => $this->params[ "longitude" ] ?? null,

            'invoice' => isset($this->params[ "invoice" ]) && !empty($this->params[ "invoice" ]) ? json_decode($this->params[ "invoice" ], true) : [],
            'pay_type' => isset($this->params[ 'pay_type' ]) ? $this->params[ 'pay_type' ] : '1',//支付方式1现金支付，2积分支付
            'video_id' => isset($this->params[ 'video_id' ]) ? $this->params[ 'video_id' ] : 0,//短视频ID

        ];
        if (empty($data[ 'cart_ids' ]) && empty($data[ 'sku_id' ])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }

        if (!in_array($data['pay_type'], ['1','2'])) {
            return $this->response($this->error('', '请选择支付方式'));
        }

        //这里都是商家核销的，物流的再写一个方法
        //核销商品和福利区分开
        if (!empty($data['cart_ids'])) {
            $cart_ids = $data['cart_ids'];
            $sku_ids = model('goods_cart')->getColumn([['cart_id', 'in', $cart_ids]],'sku_id');
            $goods_ids = model('goods_sku')->getColumn([['sku_id', 'in', $sku_ids]],'goods_id');
            $goods_type = model('goods')->getColumn([['goods_id', 'in', $goods_ids]], 'goods_type');
            if (in_array(0, $goods_type) ) {
                return $this->response($this->error('', '商家商品和福利区商品请分开结算'));
            }

            //判断如果是商家不能使用现金支付
            $is_admin = model('goods')->getColumn([['goods_id', 'in', $goods_ids]], 'is_admin');
            if ($data['pay_type'] == 1) {
                if (in_array(0, $is_admin)) {
                    return $this->response($this->error('', '商家商品只能积分支付'));
                }
            }
        } else {
            //判断如果是商家不能使用现金支付
            $goods_sku = model('goods_sku')->getInfo(['sku_id'=>$data['sku_id']]);
            $is_admin = model('goods')->getColumn([['goods_id', '=', $goods_sku['goods_id']]], 'is_admin');
            if ($data['pay_type'] == 1) {
                if (in_array(0, $is_admin)) {
                    return $this->response($this->error('', '商家商品只能积分支付'));
                }
            }
        }



        $res = $order_create->pointcreate($data);
        return $this->response($res);
    }

    /**
     * 计算信息
     */
    public function calculate()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data = [
            'cart_ids' => isset($this->params[ 'cart_ids' ]) ? $this->params[ 'cart_ids' ] : '',
            'sku_id' => isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : '',
            'num' => isset($this->params[ 'num' ]) ? $this->params[ 'num' ] : '',
            'member_id' => $this->member_id,
            'is_balance' => isset($this->params[ 'is_balance' ]) ? $this->params[ 'is_balance' ] : 0,//是否使用余额
            'order_from' => $this->params[ 'app_type' ],
            'order_from_name' => $this->params[ 'app_type_name' ],
            'delivery' => isset($this->params[ "delivery" ]) && !empty($this->params[ "delivery" ]) ? json_decode($this->params[ "delivery" ], true) : [],
            'coupon' => isset($this->params[ "coupon" ]) && !empty($this->params[ "coupon" ]) ? json_decode($this->params[ "coupon" ], true) : [],
            'member_address' => isset($this->params[ "member_address" ]) && !empty($this->params[ "member_address" ]) ? json_decode($this->params[ "member_address" ], true) : [],

            'platform_coupon_id' => $this->params[ "platform_coupon_id" ] ?? 0,
            'latitude' => $this->params[ "latitude" ] ?? null,
            'longitude' => $this->params[ "longitude" ] ?? null,

            'invoice' => isset($this->params[ "invoice" ]) && !empty($this->params[ "invoice" ]) ? json_decode($this->params[ "invoice" ], true) : [],

            //交易类型相关
            'custom_amount' => isset($this->params[ 'custom_amount' ]) ? $this->params[ 'custom_amount' ] : 0,
            'appointment' => isset($this->params[ 'appointment' ]) && !empty($this->params[ 'appointment' ]) ? json_decode($this->params[ 'appointment' ], true) : [],
        ];
        if (empty($data[ 'cart_ids' ]) && empty($data[ 'sku_id' ])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }
        $res = $order_create->calculate($data);
        return $this->response($this->success($res));

    }

    /**
     * 预约时段查询
     * 参数：sku_id/goods_id、store_id(可选)、date(可选)、num(可选)
     */
    public function slotList()
    {
        $goods_id = intval($this->params[ 'goods_id' ] ?? 0);
        $sku_id = intval($this->params[ 'sku_id' ] ?? 0);
        $store_id = intval($this->params[ 'store_id' ] ?? 0);
        $date = $this->params[ 'date' ] ?? '';
        $num = intval($this->params[ 'num' ] ?? 1);

        if (empty($goods_id) && !empty($sku_id)) {
            $sku_info = model('goods_sku')->getInfo([ [ 'sku_id', '=', $sku_id ] ], 'goods_id')[ 'data' ] ?? [];
            $goods_id = intval($sku_info[ 'goods_id' ] ?? 0);
        }
        if (empty($goods_id)) {
            return $this->response($this->error('', '商品参数错误'));
        }

        $appointment_model = new Appointment();
        //未指定门店时返回可预约日期与门店
        if (empty($store_id)) {
            $date_list = $appointment_model->getDateList($goods_id, 0, intval($this->params[ 'days' ] ?? 7));
            return $this->response($date_list);
        }

        $slot_list = $appointment_model->getSlotList($goods_id, $store_id, $date, $num);
        return $this->response($slot_list);
    }

    /**
     * 待支付订单 数据初始化
     * @return string
     */
    public function payment()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data = [
            'cart_ids' => isset($this->params[ 'cart_ids' ]) ? $this->params[ 'cart_ids' ] : '',
            'sku_id' => isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : '',
            'num' => isset($this->params[ 'num' ]) ? $this->params[ 'num' ] : '',
            'member_id' => $this->member_id,
            'is_balance' => isset($this->params[ 'is_balance' ]) ? $this->params[ 'is_balance' ] : 0,//是否使用余额
            'order_from' => $this->params[ 'app_type' ],
            'order_from_name' => $this->params[ 'app_type_name' ],

            'latitude' => $this->params[ "latitude" ] ?? null,
            'longitude' => $this->params[ "longitude" ] ?? null,

        ];
        if (empty($data[ 'cart_ids' ]) && empty($data[ 'sku_id' ])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }
        $res = $order_create->orderPayment($data);
        return $this->response($this->success($res));
    }

}