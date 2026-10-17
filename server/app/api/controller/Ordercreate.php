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

            //交易类型相关参数（与 create() 保持一致，否则积分支付下到店消费自定义金额、
            //预约到店时段会被静默丢弃，导致 appointmentPrepare 报「请选择预约时间!��）
            'custom_amount' => isset($this->params[ 'custom_amount' ]) ? $this->params[ 'custom_amount' ] : 0,
            'appointment' => isset($this->params[ "appointment" ]) && !empty($this->params[ "appointment" ]) ? json_decode($this->params[ "appointment" ], true ) : [],
            'pay_scene' => isset($this->params[ 'pay_scene' ]) ? $this->params[ 'pay_scene' ] : 0,

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
    public     function slotList()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response( $token );

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
            //days 传 0/不传时，由后端按商品配置的「可提前预约天数」决定，
            //不再由前端写死，避免商户无法控制可预约范围。
            //上限 90 天，防止传入超大天数造成批量数据扫描。
            $days = intval($this->params[ 'days' ] ?? 0);
            if ($days < 0) $days = 0;
            if ($days > 90) $days = 90;
            $date_list = $appointment_model->getDateList($goods_id, 0, $days);
            return $this->response($date_list);
        }

        //日期必须合法且不早于今天，避免构造任意历史日期绕过截止时间判定
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            return $this->response($this->error('', '日期格式不正确'));
        }
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            return $this->response($this->error('', '不能选择过去的日期'));
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

    /**
     * 快捷买单/向商家付款：创建订单并生成支付流水
     */
    public function quickPay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $site_id = intval($this->params[ 'site_id' ] ?? 0);
        $amount = round(floatval($this->params[ 'amount' ] ?? 0), 2);
        $remark = trim(strval($this->params[ 'remark' ] ?? ''));
        $store_id = intval($this->params[ 'store_id' ] ?? 0);

        if ($site_id <= 0) {
            return $this->response($this->error('', '请选择付款商家'));
        }
        if ($amount <= 0) {
            return $this->response($this->error('', '请输入有效的付款金额'));
        }
        if ($amount > 50000) {
            return $this->response($this->error('', '单笔付款金额不能超过50000元'));
        }

        // 获取店铺信息
        $shop_info = model('shop')->getInfo([ [ 'site_id', '=', $site_id ] ], 'site_id, site_name, logo, website_id')[ 'data' ] ?? [];
        if (empty($shop_info)) {
            return $this->response($this->error('', '商家不存在或未营业'));
        }

        // 获取门店信息（若未指定则取该店铺的默认门店）
        $store_info = [];
        if ($store_id > 0) {
            $store_info = model('store')->getInfo([ [ 'store_id', '=', $store_id ], [ 'site_id', '=', $site_id ] ], 'store_id, store_name, address, full_address, telphone')[ 'data' ] ?? [];
        }
        if (empty($store_info)) {
            $store_info = model('store')->getInfo([ [ 'site_id', '=', $site_id ], [ 'is_default', '=', 1 ] ], 'store_id, store_name, address, full_address, telphone')[ 'data' ] ?? [];
            if (empty($store_info)) {
                $store_info = model('store')->getInfo([ [ 'site_id', '=', $site_id ] ], 'store_id, store_name, address, full_address, telphone')[ 'data' ] ?? [];
            }
        }

        $delivery_store_id = $store_info[ 'store_id' ] ?? 0;
        $delivery_store_name = $store_info[ 'store_name' ] ?? $shop_info[ 'site_name' ];
        $delivery_store_info = !empty($store_info) ? json_encode($store_info, JSON_UNESCAPED_UNICODE) : '';

        // 会员信息
        $member_info = model('member')->getInfo([ [ 'member_id', '=', $this->member_id ] ], 'nickname, mobile, headimg')[ 'data' ] ?? [];

        // 生成单号
        $order_common = new \app\model\order\OrderCommon();
        $order_no = $order_common->createOrderNo($site_id);
        $pay_model = new \app\model\system\Pay();
        $out_trade_no = $pay_model->createOutTradeNo($this->member_id);

        model('order')->startTrans();
        try {
            $order_data = [
                'order_no' => $order_no,
                'site_id' => $site_id,
                'site_name' => $shop_info[ 'site_name' ],
                'website_id' => $shop_info[ 'website_id' ] ?? 0,
                'order_name' => '向' . $shop_info[ 'site_name' ] . '付款',
                'order_from' => $this->params[ 'app_type' ] ?? 'weapp',
                'order_from_name' => $this->params[ 'app_type_name' ] ?? '微信小程序',
                'order_type' => 2, // 门店订单类型
                'order_type_name' => '到店买单',
                'trade_type' => 1, // 到店消费
                'pay_scene' => 1,  // 线下扫码买单/快捷付款
                'custom_amount' => $amount,
                'out_trade_no' => $out_trade_no,
                'member_id' => $this->member_id,
                'name' => $member_info[ 'nickname' ] ?: '顾客',
                'mobile' => $member_info[ 'mobile' ] ?: '',
                'full_address' => $store_info[ 'full_address' ] ?? ($store_info[ 'address' ] ?? ''),
                'delivery_type' => 'store',
                'delivery_type_name' => '到店买单',
                'delivery_store_id' => $delivery_store_id,
                'delivery_store_name' => $delivery_store_name,
                'delivery_store_info' => $delivery_store_info,
                'goods_money' => $amount,
                'order_money' => $amount,
                'pay_money' => $amount,
                'goods_num' => 1,
                'buyer_message' => $remark,
                'order_status' => 0, // 待支付
                'order_status_name' => '待支付',
                'pay_status' => 0,
                'create_time' => time(),
                'is_enable_refund' => 0,
            ];

            $order_id = model('order')->add($order_data);

            $order_goods_data = [
                'order_id' => $order_id,
                'site_id' => $site_id,
                'site_name' => $shop_info[ 'site_name' ],
                'order_no' => $order_no,
                'member_id' => $this->member_id,
                'sku_name' => '向商家付款',
                'goods_name' => '快捷买单',
                'sku_image' => $shop_info[ 'logo' ] ?: '',
                'price' => $amount,
                'cost_price' => $amount,
                'num' => 1,
                'goods_money' => $amount,
                'cost_money' => $amount,
                'real_goods_money' => $amount,
                'trade_type' => 1,
                'delivery_status' => 0,
                'delivery_status_name' => '待支付',
            ];
            model('order_goods')->add($order_goods_data);

            // 写入系统支付记录
            $pay_model->addPay(
                $site_id,
                $out_trade_no,
                '',
                '向' . $shop_info[ 'site_name' ] . '付款',
                '快捷买单' . $amount . '元',
                $amount,
                '',
                'OrderPayNotify',
                ''
            );

            model('order')->commit();

            return $this->response($this->success([
                'out_trade_no' => $out_trade_no,
                'order_id' => $order_id,
                'order_no' => $order_no,
                'pay_money' => $amount,
                'site_name' => $shop_info[ 'site_name' ],
                'logo' => $shop_info[ 'logo' ] ?? '',
            ]));
        } catch (\Exception $e) {
            model('order')->rollback();
            return $this->response($this->error('', $e->getMessage()));
        }
    }

}