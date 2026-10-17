<?php
/**

 */

namespace app\api\controller;

use app\model\express\ExpressPackage;
use app\model\goods\Appointment;
use app\model\goods\Cart as CartModel;
use app\model\order\Order as OrderModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\OrderCommon;
use app\model\order\OrderRefund as OrderRefundModel;
use think\facade\Cache;
use think\facade\Db;

class Order extends BaseApi
{

    /**
     * 详情信息
     */
    public function detail()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_common_model = new OrderCommonModel();
        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        $result = $order_common_model->getMemberOrderDetail($order_id, $this->member_id);
        return $this->response($result);
    }

    /**
     * 列表信息
     */
    public function lists()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_common_model = new OrderCommonModel();
        $condition = array (
            [ "member_id", "=", $this->member_id ],
        );
        $order_status = isset($this->params[ 'order_status' ]) ? $this->params[ 'order_status' ] : 'all';
        switch ( $order_status ) {
            case "waitpay"://待付款
                $condition[] = [ "order_status", "=", 0 ];
                break;
            case "waitsend"://待发货
                $condition[] = [ "order_status", "=", 1 ];
                break;
            case "waitconfirm"://待收货（快递已发货；外卖备餐中/配送中）
                $condition[] = [ "order_status", "in", [ 2, 3 ] ];
                $condition[] = [ "order_type", "in", [ 1, 3 ] ];
                break;
            case "waitrate"://待评价
                $condition[] = [ "order_status", "in", [ 4, 10 ] ];
                $condition[] = [ "is_evaluate", "=", 1 ];
                break;
            case "waitstock"://待提货/待核销（到店自提0 / 到店消费1）
                $condition[] = [ "order_status", "in", [ 1, 2 ] ];
                $condition[] = [ "delivery_status", "=", 0 ];
                $condition[] = [ "trade_type", "in", [ 0, 1 ] ];
                break;
            case "waitappointment"://待到店（仅预约到店2，且已预约未核销）
                $condition[] = [ "order_status", "in", [ 1, 2 ] ];
                $condition[] = [ "trade_type", "=", 2 ];
                $condition[] = [ "delivery_status", "=", 0 ];
                $condition[] = [ "appointment_status", "=", 1 ];
                break;
            case "waitexpress"://外卖配送中
                $condition[] = [ "order_type", "=", 3 ];
                $condition[] = [ "order_status", "in", [ 1, 3 ] ];
                break;
            case "after"://售后
                $order_ids = model('order_goods')->getColumn([
                    ['member_id', '=', $this->member_id],
                    ['refund_status', '<>', 0]
                ],'order_id');
                $condition[] = [ "order_id", "in", $order_ids ];
                break;
            case "quickpay"://快捷买单/向商家付款记录
                $condition[] = [ "pay_scene", "=", 1 ];
                break;
        }
//		if (c !== "all") {
//			$condition[] = [ "order_status", "=", $order_status ];
//		}
        $page_index = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        $res = $order_common_model->getMemberOrderPageList($condition, $page_index, $page_size, "create_time desc");
        return $this->response($res);
    }

    /**
     * 快捷买单/向商家付款明细列表
     */
    public function quickPayLists()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $page = isset($this->params[ 'page' ]) ? intval($this->params[ 'page' ]) : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? intval($this->params[ 'page_size' ]) : PAGE_LIST_ROWS;
        $site_id = isset($this->params[ 'site_id' ]) ? intval($this->params[ 'site_id' ]) : 0;

        $condition = [
            [ 'member_id', '=', $this->member_id ],
            [ 'pay_scene', '=', 1 ],
        ];
        if ($site_id > 0) {
            $condition[] = [ 'site_id', '=', $site_id ];
        }

        $order_common_model = new OrderCommonModel();
        $res = $order_common_model->getMemberOrderPageList($condition, $page, $page_size, "create_time desc");
        return $this->response($res);
    }

    /**
     * 订单评价基础信息
     */
    public function evluateinfo()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }

        $order_common_model = new OrderCommonModel();
        $order_info = $order_common_model->getOrderInfo([
            [ 'order_id', '=', $order_id ],
            [ 'member_id', '=', $this->member_id ],
            [ 'order_status', 'in', ( '4,10' ) ],
            [ 'is_evaluate', '=', 1 ],
        ], 'evaluate_status,evaluate_status_name');

        $res = $order_info[ 'data' ];
        if (!empty($res)) {
            if ($res[ 'evaluate_status' ] == 2) {
                return $this->response($this->error('', '该订单已评价'));
            } else {
                $condition = [
                    [ 'order_id', '=', $order_id ],
                    [ 'member_id', '=', $token[ 'data' ][ 'member_id' ] ],
                    [ 'refund_status', '<>', 3 ],
                ];
                $list = $order_common_model->getOrderGoodsList($condition, 'order_goods_id,order_id,order_no,site_id,site_name,member_id,goods_id,sku_id,sku_name,sku_image,price,num');
                $list = $list[ 'data' ];
                $res[ 'list' ] = $list;
                return $this->response($this->success($res));
            }
        } else {
            return $this->response($this->error('', '没有找到该订单'));
        }

    }

    /**
     * 订单收货(收到所有货物)
     */
    public function takeDelivery()
    {

        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_model = new OrderCommonModel();
        $take_condition = array (
            [ 'order_id', '=', $order_id ],
            [ 'member_id', '=', $this->member_id ]
        );
        $result = $order_model->orderCommonTakeDelivery($take_condition,$this->member_id);
        return $this->response($result);
    }

    /**
     * 关闭订单
     */
    public function close()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_model = new OrderModel();
        $close_condition = array (
            [ 'order_id', '=', $order_id ],
            [ 'member_id', '=', $this->member_id ]
        );
        $result = $order_model->orderClose($close_condition);
        return $this->response($result);
    }

    /**
     * 取消预约（仅未核销、未完成的到店预约订单）
     */
    public function cancelAppointment()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $order_id = isset($this->params[ 'order_id' ]) ? intval($this->params[ 'order_id' ] ) : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_info = model('order')->getInfo([
            [ 'order_id', '=', $order_id ],
            [ 'member_id', '=', $this->member_id ],
        ], 'order_id,trade_type,order_status,pay_status,delivery_status,appointment_status');
        if (empty($order_info)) {
            return $this->response($this->error('', '未找到该订单'));
        }
        if (intval($order_info[ 'trade_type' ]) != 2) {
            return $this->response($this->error('', '该订单不是预约订单'));
        }
        if ($order_info[ 'delivery_status' ] == 1) {
            return $this->response($this->error('', '该订单已核销，无法取消预约'));
        }
        if ($order_info[ 'order_status' ] == -1) {
            return $this->response($this->error('', '该订单已关闭'));
        }
        if (isset($order_info[ 'appointment_status' ]) && $order_info[ 'appointment_status' ] == 2) {
            return $this->response($this->error('', '该预约已取消'));
        }

        //未支付直接关闭订单并释放名额
        if ($order_info[ 'pay_status' ] == 0) {
            $appointment_model = new Appointment();
            $appointment_model->release($order_id, Appointment::STATUS_CANCEL);
            model('order')->update([
                'appointment_status' => 2,
                'modify_time' => time(),
            ], [ [ 'order_id', '=', $order_id ] ]);

            $order_model = new OrderModel();
            return $this->response($order_model->orderClose([
                [ 'order_id', '=', $order_id ],
                [ 'member_id', '=', $this->member_id ],
            ]));
        }

        //已支付：取消预约，释放名额，尝试原路退款并彻底关闭订单
        $appointment_model = new Appointment();
        $appointment_model->release($order_id, Appointment::STATUS_CANCEL);
        model('order')->update([
            'appointment_status' => 2,
            'modify_time' => time(),
        ], [ [ 'order_id', '=', $order_id ] ]);

        $order_refund_model = new OrderRefundModel();
        $order_refund_model->activeRefund($order_id, '用户取消预约', '用户取消预约');

        //确保订单状态变为已关闭（-1）
        $curr_order = model('order')->getInfo([ [ 'order_id', '=', $order_id ] ], 'order_status');
        if (!empty($curr_order) && $curr_order['order_status'] != -1) {
            $order_model = new OrderModel();
            $close_res = $order_model->orderClose([
                [ 'order_id', '=', $order_id ],
                [ 'member_id', '=', $this->member_id ],
            ]);
            if ($close_res['code'] < 0) {
                model('order')->update([
                    'order_status' => OrderCommon::ORDER_CLOSE,
                    'order_status_name' => '已关闭',
                    'order_status_action' => json_encode(['action' => []], JSON_UNESCAPED_UNICODE),
                    'close_time' => time(),
                    'is_lock' => 0,
                    'is_enable_refund' => 0
                ], [ [ 'order_id', '=', $order_id ] ]);
            }
        }

        return $this->response($this->success([ 'order_id' => $order_id ], '预约已取消')); 
    }

    /**
     * 获取订单数量
     */
    public function num()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        if (empty($this->params[ 'order_status' ])) {
            return $this->response($this->error('', 'REQUEST_ORDER_STATUS'));
        }

        $order_common_model = new OrderCommonModel();
        $order_refund_model = new OrderRefundModel();
        $data = [];
        foreach (explode(',', $this->params[ 'order_status' ]) as $order_status) {
            $condition = array (
                [ "member_id", "=", $this->member_id ],
            );
            switch ( $order_status ) {
                case "waitpay"://待付款
                    $condition[] = [ "order_status", "=", 0 ];
                    break;
                case "waitsend"://待发货
                    $condition[] = [ "order_status", "=", 1 ];
                    break;
                case "waitconfirm"://待收货（快递已发货；外卖备餐中/配送中），与 lists 保持一致
                    $condition[] = [ "order_status", "in", [ 2, 3 ] ];
                    $condition[] = [ "order_type", "in", [ 1, 3 ] ];
                    break;
                case "waitrate"://待评价
                    $condition[] = [ "order_status", "in", [ 4, 10 ] ];
                    $condition[] = [ "is_evaluate", "=", 1 ];
                    break;
                case "waitstock"://待提货/待核销（到店自提0 / 到店消费1）
                    $condition[] = [ "order_status", "in", [ 1, 2 ] ];
                    $condition[] = [ "delivery_status", "=", 0 ];
                    $condition[] = [ "trade_type", "in", [ 0, 1 ] ];
                    break;
                case "waitappointment"://待到店（仅预约到店2，且已预约未核销）
                    $condition[] = [ "order_status", "in", [ 1, 2 ] ];
                    $condition[] = [ "trade_type", "=", 2 ];
                    $condition[] = [ "delivery_status", "=", 0 ];
                    $condition[] = [ "appointment_status", "=", 1 ];
                    break;
            }
            if ($order_status == 'refunding') {
                $result = $order_refund_model->getRefundOrderGoodsCount([
                    [ "member_id", "=", $this->member_id ],
                    [ "refund_status", "in", [ -1, 1, 2, 4, 5, 6 ] ]
                ]);
                $data[ $order_status ] = $result[ 'data' ];
            } elseif ($order_status == 'cart') { //购物车
                $cart = new CartModel();
                $condition = [
                    [ 'gc.member_id', '=', $token[ 'data' ][ 'member_id' ] ],
                    [ 'gs.goods_state', '=', 1 ],
                    [ 'gs.is_delete', '=', 0 ]
                ];
                $list = $cart->getCartList($condition, 'gc.num');
                $list = $list[ 'data' ];
                $count = 0;
                foreach ($list as $k => $v) {
                    $count += $v[ 'num' ];
                }
                $data[ $order_status ] = $count;
            } else {
                $result = $order_common_model->getOrderCount($condition);
                $data[ $order_status ] = $result[ 'data' ];
            }
        }
        return $this->response(success(0, '', $data));
    }

    /**
     * 订单包裹信息
     */
    public function package()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : '';//订单id
        $express_package_model = new ExpressPackage();
        $condition = array (
            [ "member_id", "=", $this->member_id ],
            [ "order_id", "=", $order_id ],
        );
        $order_model = new OrderCommon();
        $order_info = $order_model->getOrderInfo($condition)[ 'data' ] ?? [];
        $condition[] = [ 'site_id', '=', $order_info[ 'site_id' ] ];
        $result = $express_package_model->package($condition, $order_info[ 'mobile' ]);
        if ($result) return $this->response($this->success($result));
        else return $this->response($this->error());
    }

    /**
     * 订单支付
     * @return string
     */
    public function pay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $order_ids = isset($this->params[ 'order_ids' ]) ? $this->params[ 'order_ids' ] : '';//订单id
        if (empty($order_ids)) return $this->response($this->error('', "订单数据为空"));
        $order_common_model = new OrderCommonModel();
        $result = $order_common_model->splitOrderPay($order_ids);
        return $this->response($result);
    }

    /**
     * 延长收货
     */
    public function extendtakedelivery()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }
        $order_model = new OrderCommonModel();
        $take_condition = array (
            [ 'order_id', '=', $order_id ],
            [ 'member_id', '=', $this->member_id ]
        );
        $result = $order_model->extendTakeDelivery($take_condition);
        return $this->response($result);
    }

    /**
     * 获取/动态生成订单核销码（每次进入/刷新都生成新码）
     */
    public function verifyCode()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $order_id = isset($this->params['order_id']) ? (int)$this->params['order_id'] : 0;
        if (empty($order_id)) {
            return $this->response($this->error('', 'REQUEST_ORDER_ID'));
        }

        $order_info = model('order')->getInfo([
            ['order_id', '=', $order_id],
            ['member_id', '=', $this->member_id]
        ], 'order_id, order_no, order_status, order_status_name, pay_status, delivery_code, delivery_status, site_id, site_name, is_lock, trade_type, appointment_date, appointment_slot, appointment_status, delivery_store_name, delivery_store_id');

        if (empty($order_info)) {
            return $this->response($this->error('', '未找到该订单'));
        }

        if ($order_info['order_status'] == -1) {
            return $this->response($this->error('', '订单已关闭'));
        }
        if ($order_info['order_status'] == 0 && $order_info['pay_status'] == 0) {
            return $this->response($this->error('', '订单待支付，无法生成核销码'));
        }
        if ($order_info['order_status'] == 4 || $order_info['delivery_status'] == 1) {
            return $this->response($this->success([
                'order_id' => $order_id,
                'order_no' => $order_info['order_no'],
                'is_verify' => 1,
                'verify_code' => $order_info['delivery_code'] ?: $order_info['order_no']
            ], '该订单商品已核销'));
        }

        // 每次都生成新的动态核销码：V + 年月日时分秒 + 4位随机数字
        $verify_code = 'V' . date('YmdHis') . rand(1000, 9999);

        // 更新订单提货码
        model('order')->update(['delivery_code' => $verify_code], [['order_id', '=', $order_id]]);

        // 存入缓存，有效期 2 小时
        Cache::set('order_verify_token_' . $verify_code, $order_id, 7200);
        Cache::set('order_latest_verify_code_' . $order_id, $verify_code, 7200);

        // 同步更新 verify 表
        $verify_model = new \app\model\verify\Verify();
        $verify_record = model('verify')->getInfo([['relate_id', '=', $order_id]]);
        if (!empty($verify_record)) {
            model('verify')->update([
                'verify_code' => $verify_code,
                'is_verify' => 0
            ], [['id', '=', $verify_record['id']]]);
        } else {
            $order_goods_list = model('order_goods')->getList([['order_id', '=', $order_id]], 'sku_image,sku_name,price,num,order_goods_id,goods_id,sku_id');
            $item_array = [];
            foreach ($order_goods_list as $k => $v) {
                $item_array[$v['order_goods_id']] = [
                    'img' => $v['sku_image'],
                    'name' => $v['sku_name'],
                    'price' => $v['price'],
                    'num' => $v['num'],
                ];
            }
            $remark_array = [
                ['title' => '订单编号', 'value' => $order_info['order_no']],
                ['title' => '门店', 'value' => $order_info['site_name']]
            ];
            $verify_content_json = $verify_model->getVerifyJson($item_array, $remark_array);
            $verify_model->addVerify('pickup', $order_info['site_id'], $order_info['site_name'], $verify_content_json, $order_id);
            model('verify')->update(['verify_code' => $verify_code], [['relate_id', '=', $order_id]]);
        }

        return $this->response($this->success([
            'order_id' => $order_id,
            'order_no' => $order_info['order_no'],
            'verify_code' => $verify_code,
            'is_verify' => 0,
            'timestamp' => time(),
            'trade_type' => intval($order_info[ 'trade_type' ] ?? 0),
            'trade_type_name' => \app\model\order\OrderCreate::tradeTypeName($order_info[ 'trade_type' ] ?? 0),
            'store_id' => $order_info[ 'delivery_store_id' ] ?? 0,
            'store_name' => $order_info[ 'delivery_store_name' ] ?? '',
            'appointment_date' => $order_info[ 'appointment_date' ] ?? '',
            'appointment_slot' => $order_info[ 'appointment_slot' ] ?? '',
            'appointment_status' => $order_info[ 'appointment_status' ] ?? -1,
        ]));
    }

    /**
     * 快递物流轨迹查询
     * 适用于快递邮寄(3) 以及到店自提(0) 选择快递发货的场景。
     * 数据来源：express_delivery_package（发货时写入），轨迹经 Trace 模型分发
     * 快递100/快递鸟第三方接口；未启用物流方式或无记录时返回空轨迹而非报错。
     */
    public function expressTrace()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response( $token );

        $order_id = isset( $this->params[ 'order_id' ] ) ? (int)$this->params[ 'order_id' ] : 0;
        if ( empty( $order_id ) ) {
            return $this->response( $this->error( '', 'REQUEST_ORDER_ID' ) );
        }

        //归属校验：只能查询自己的订单
        $order_info = model( 'order' )->getInfo( [
            [ 'order_id', '=', $order_id ],
            [ 'member_id', '=', $this->member_id ]
        ], 'order_id, order_no, order_status, order_status_name, delivery_type, delivery_status, delivery_type_name, trade_type, mobile' );

        if ( empty( $order_info ) ) {
            return $this->response( $this->error( '', '未找到该订单' ) );
        }

        //仅快递配送存在物流轨迹：门店核销(0/1/2)与同城配送(4)无轨迹
        if ( $order_info[ 'delivery_type' ] != 'express' ) {
            return $this->response( $this->error( '', '该订单不是快递配送，无物流轨迹' ) );
        }

        $express_package_model = new ExpressPackage();
        //快递100/快递鸟的部分接口需要收件人手机号后四位做校验
        $package_list = $express_package_model->package( [
            [ 'order_id', '=', $order_id ]
        ], $order_info[ 'mobile' ] ?? '' );

        //包裹表为空时（历史数据只写了 order_goods.delivery_no），
        //降级返回订单项单号，保证 C 端至少能展示物流信息而非空白
        if ( empty( $package_list ) ) {
            $goods_list = model( 'order_goods' )->getList( [
                [ 'order_id', '=', $order_id ]
            ], 'order_goods_id, sku_name, delivery_no, delivery_status, delivery_status_name' );

            $fallback = [];
            foreach ( $goods_list as $k => $v ) {
                if ( empty( $v[ 'delivery_no' ] ) ) continue;
                $fallback[] = [
                    'package_name' => '包裹' . ( count( $fallback ) + 1 ),
                    'express_company_id' => 0,
                    'express_company_name' => '',
                    'express_company_image' => '',
                    'delivery_no' => $v[ 'delivery_no' ],
                    'delivery_type' => 1,
                    'sku_name' => $v[ 'sku_name' ],
                    'trace' => [ 'success' => false, 'reason' => '暂无物流轨迹，请在商家发货后稍后再试' ],
                ];
            }

            if ( empty( $fallback ) ) {
                return $this->response( $this->error( '', '商家尚未发货，暂无物流信息' ) );
            }
            return $this->response( $this->success( $fallback ) );
        }

        return $this->response( $this->success( $package_list ) );
    }
}