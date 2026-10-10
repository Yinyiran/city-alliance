<?php
namespace app\shopapplet\controller;

use app\model\goods\Appointment;
use app\model\system\Pay as PayModel;

/**
 * 商家小程序收银台：顾客扫商家收款码 + 预约日程
 */
class Cashier extends BaseApi
{
    /**
     * 生成顾客扫码付款的收款码
     */
    public function cashierCode()
    {
        $order_no = $this->params[ 'order_no' ] ?? '';
        $amount = $this->params[ 'amount' ] ?? 0;
        $store_id = intval($this->params[ 'store_id' ] ?? 0);
        $pay_type = $this->params[ 'pay_type' ] ?? 'WECHAT_PAY';
        $site_id = $this->params[ 'site_id' ] ?? ($this->site_id ?? 0);

        if (empty($order_no)) return $this->response($this->error([], '请先选择订单'));
        $condition = [ [ 'order_no', '=', $order_no ] ];
        if (!empty($site_id)) $condition[] = [ 'site_id', '=', $site_id ];

        $order_info = model('order')->getInfo($condition, 'order_id,order_no,order_status,pay_status,pay_money,member_id,trade_type,site_id')[ 'data' ] ?? [];
        if (empty($order_info)) return $this->response($this->error([], '订单不存在'));
        if ($order_info[ 'pay_status' ] > 0) return $this->response($this->error([], '该订单已支付'));

        $pay_money = $order_info[ 'pay_money' ];
        if ($amount > 0 && abs($amount - $pay_money) > 0.001) {
            if (!$this->checkCustomAmount($order_info[ 'order_id' ], $amount)) {
                return $this->response($this->error([], '该订单不支持自定义金额或金额超出范围'));
            }
            model('order')->update([
                'custom_amount' => round($amount, 2),
                'pay_money' => round($amount, 2),
                'order_money' => round($amount, 2),
                'pay_scene' => 1,
            ], [ [ 'order_id', '=', $order_info[ 'order_id' ] ] ]);
            $pay_money = round($amount, 2);
        }
        if ($pay_money <= 0) return $this->response($this->error([], '订单金额为0，无需支付'));

        $pay = new PayModel();
        $info = $pay->pay($pay_type, $order_info[ 'order_no' ], 'pc', $order_info[ 'member_id' ], '', 0, false);
        if ((int)($info[ 'code' ] ?? -1) !== 0) {
            return $this->response($this->error([], $info[ 'message' ] ?? '收款码生成失败'));
        }
        return $this->response($this->success([
            'order_no' => $order_info[ 'order_no' ],
            'store_id' => $store_id,
            'pay_money' => $pay_money,
            'pay_info' => $info[ 'data' ] ?? [],
        ]));
    }

    /**
     * 校验自定义金额
     */
    private function checkCustomAmount($order_id, $amount)
    {
        $goods_ids = model('order_goods')->getColumn([ [ 'order_id', '=', $order_id ] ], 'goods_id');
        $min = 0;
        $max = 0;
        $allow = false;
        foreach ($goods_ids as $goods_id) {
            $goods = model('goods')->getInfo([ [ 'goods_id', '=', $goods_id ] ], 'trade_type,allow_custom_amount,min_order_amount,max_order_amount')[ 'data' ] ?? [];
            if (intval($goods[ 'trade_type' ] ?? 0) == 1 && intval($goods[ 'allow_custom_amount' ] ?? 0) == 1) {
                $allow = true;
                $min = $goods[ 'min_order_amount' ] ?? 0;
                $max = $goods[ 'max_order_amount' ] ?? 0;
                break;
            }
        }
        if (!$allow) return false;
        $amount = round($amount, 2);
        if ($min > 0 && $amount < $min) return false;
        if ($max > 0 && $amount > $max) return false;
        return true;
    }
    /**
     * 预约日程
     */
    public function schedule()
    {
        $date = $this->params[ 'date' ] ?? date('Y-m-d');
        $site_id = $this->params[ 'site_id' ] ?? ($this->site_id ?? 0);
        $appointment_model = new Appointment();
        $condition = [ [ 'appointment_date', '=', $date ] ];
        if (!empty($site_id)) $condition[] = [ 'site_id', '=', $site_id ];
        $res = $appointment_model->getScheduleList($condition, $date, 1, 100);
        return $this->response($this->success($res[ 'data' ] ?? []));
    }

    /**
     * 查询某日「可预约时段」（商家改约用）
     * schedule 返回的是已预约记录，无法判断哪些时段还能约，
     * 因此改约需要基于 Appointment::getSlotList 的时段配置与占用情况判断。
     */
    public function slots()
    {
        $goods_id = intval($this->params[ 'goods_id' ] ?? 0);
        $store_id = intval($this->params[ 'store_id' ] ?? 0);
        $date = $this->params[ 'date' ] ?? date( 'Y-m-d' );
        $num = intval( $this->params[ 'num' ] ?? 1 );
        $site_id = $this->params[ 'site_id' ] ?? ( $this->site_id ?? 0 );

        if ( empty( $goods_id ) || empty( $store_id ) ) {
            return $this->response( $this->error( [], '商品或门店参数缺失' ) );
        }

        $appointment_model = new Appointment();
        $res = $appointment_model->getSlotList( $goods_id, $store_id, $date, max( 1, $num ) );
        if ( $res[ 'code' ] != 0 ) {
            return $this->response( $this->error( [], $res[ 'message' ] ?? '查询时段失败' ) );
        }
        return $this->response( $this->success( $res[ 'data' ] ?? [] ) );
    }

    /**
     * 商家改约
     */
    public function reschedule()
    {
        $order_id = intval($this->params[ 'order_id' ] ?? 0);
        $date = $this->params[ 'appointment_date' ] ?? '';
        $start_time = $this->params[ 'start_time' ] ?? '';
        $slot_text = $this->params[ 'appointment_slot' ] ?? '';
        $num = intval($this->params[ 'num' ] ?? 0);
        $site_id = $this->params[ 'site_id' ] ?? ($this->site_id ?? 0);

        if (empty($order_id) || empty($date) || empty($start_time)) {
            return $this->response($this->error([], '改约信息不完整'));
        }
        $condition = [ [ 'order_id', '=', $order_id ] ];
        if (!empty($site_id)) $condition[] = [ 'site_id', '=', $site_id ];
        $order_info = model('order')->getInfo($condition, 'order_id,trade_type')[ 'data' ] ?? [];
        if (empty($order_info) || intval($order_info[ 'trade_type' ]) != 2) {
            return $this->response($this->error([], '该订单不是预约订单'));
        }
        $appointment_model = new Appointment();
        $res = $appointment_model->reschedule($order_id, $date, $start_time, $slot_text, $num ?: null);
        if ($res[ 'code' ] != 0) {
            return $this->response($this->error([], $res[ 'message' ] ?? '改约失败'));
        }
        return $this->response(success(0, '改约成功'));
    }
}
