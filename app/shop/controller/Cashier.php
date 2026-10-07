<?php
/**
 * 门店收银台
 */

namespace app\shop\controller;

use app\model\goods\Appointment;
use app\model\order\StoreOrder;
use app\model\system\Pay as PayModel;
use app\model\verify\Verify as VerifyModel;

/**
 * Cashier
 * 到店消费收款（顾客扫商家收款码）+ 扫码核销 + 预约日程
 */
class Cashier extends BaseShop
{
    /**
     * 收银台首页
     */
    public function index()
    {
        $this->assign('verify_type', $this->getVerifyTypeList());
        return $this->fetch("cashier/index");
    }

    /**
     * 核销类型
     */
    public function getVerifyTypeList()
    {
        $verify_model = new VerifyModel();
        $type = $verify_model->getVerifyType();
        $list = [];
        foreach ($type as $k => $v) {
            $list[] = [ 'name' => $k, 'title' => $v[ 'name' ] ];
        }
        return $list;
    }

    /**
     * 扫码查单（顾客出示的核销码）
     */
    public function findOrder()
    {
        $code = input('delivery_code', '');
        if (empty($code)) return $this->error([], '请输入或扫描核销码');

        $order_info = model('order')->getInfo([
            [ 'delivery_code', '=', $code ],
            [ 'site_id', '=', $this->site_id ],
        ], 'order_id,order_no,order_status,order_status_name,pay_status,delivery_status,trade_type,custom_amount,pay_money,order_money,appointment_date,appointment_slot,appointment_status,delivery_store_name,delivery_store_id,create_time,pay_time')[ 'data' ] ?? [];

        if (empty($order_info)) {
            //兼容通过核销码表查询
            $verify_info = model('verify')->getInfo([
                [ 'verify_code', '=', $code ],
                [ 'site_id', '=', $this->site_id ],
            ], 'relate_id,is_verify,verify_type')[ 'data' ] ?? [];
            if (!empty($verify_info[ 'relate_id' ])) {
                $order_info = model('order')->getInfo([
                    [ 'order_id', '=', $verify_info[ 'relate_id' ] ],
                    [ 'site_id', '=', $this->site_id ],
                ], 'order_id,order_no,order_status,order_status_name,pay_status,delivery_status,trade_type,custom_amount,pay_money,order_money,appointment_date,appointment_slot,appointment_status,delivery_store_name,delivery_store_id,create_time,pay_time')[ 'data' ] ?? [];
            }
        }

        if (empty($order_info)) {
            return $this->error([], '未找到对应订单，请确认核销码是否正确');
        }

        //预约单校验时段（迟到可履约，仅提示）
        $tip = '';
        if (intval($order_info[ 'trade_type' ]) == 2 && !empty($order_info[ 'appointment_date' ])) {
            $date = $order_info[ 'appointment_date' ];
            $slot = $order_info[ 'appointment_slot' ] ?? '';
            $start = substr($slot, 0, 5 );
            $now = time();
            $slot_ts = $start ? strtotime($date . ' ' . $start ) : 0;
            if ($slot_ts && $now > $slot_ts + 3600) {
                $tip = '顾客已迟到超过1小时，请确认后再核销';
            } elseif ($slot_ts && $now < $slot_ts - 3600) {
                $tip = '距预约时间还有较长时间，可提前核销';
            }
        }

        $order_info[ 'trade_type_name' ] = \app\model\order\OrderCreate::tradeTypeName($order_info[ 'trade_type' ] ?? 0);
        $order_info[ 'tip' ] = $tip;
        return $this->success($order_info);
    }

    /**
     * 生成顾客扫码付款的收款码（微信 Native）
     */
    public function cashierCode()
    {
        $order_no = input('order_no', '');
        $amount = input('amount', 0);
        $pay_type = input('pay_type', 'WECHAT_PAY');

        if (empty($order_no)) return $this->error([], '请先选择订单');

        $order_info = model('order')->getInfo([
            [ 'order_no', '=', $order_no ],
            [ 'site_id', '=', $this->site_id ],
        ], 'order_id,order_no,order_status,pay_status,pay_money,member_id,trade_type')[ 'data' ] ?? [];
        if (empty($order_info)) return $this->error([], '订单不存在');
        if ($order_info[ 'pay_status' ] > 0) return $this->error([], '该订单已支付');

        $pay_money = $order_info[ 'pay_money' ];
        //到店消费允许商家现场输入金额（商品需开启自定义金额）
        if ($amount > 0 && abs($amount - $pay_money) > 0.001) {
            if (!$this->checkCustomAmount($order_info[ 'order_id' ], $amount)) {
                return $this->error([], '该订单不支持自定义金额或金额超出范围');
            }
            model('order')->update([
                'custom_amount' => round($amount, 2),
                'pay_money' => round($amount, 2),
                'order_money' => round($amount, 2),
                'pay_scene' => 1,
            ], [ [ 'order_id', '=', $order_info[ 'order_id' ] ] ]);
            $pay_money = round($amount, 2);
        }

        if ($pay_money <= 0) return $this->error([], '订单金额为0，无需支付');

        $pay = new PayModel();
        $info = $pay->pay($pay_type, $order_info[ 'order_no' ], 'pc', $order_info[ 'member_id' ], '', 0, false);
        if ((int)($info[ 'code' ] ?? -1) !== 0) {
            return $this->error([], $info[ 'message' ] ?? '收款码生成失败');
        }
        return $this->success([
            'order_no' => $order_info[ 'order_no' ],
            'pay_money' => $pay_money,
            'pay_info' => $info[ 'data' ] ?? [],
        ]);
    }

    /**
     * 校验自定义金额是否允许
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
     * 核销订单
     */
    public function verify()
    {
        $code = input('delivery_code', '');
        if (empty($code)) return $this->error([], '请输入或扫描核销码');

        $order_info = model('order')->getInfo([
            [ 'delivery_code', '=', $code ],
            [ 'site_id', '=', $this->site_id ],
        ], 'order_id,trade_type')[ 'data' ] ?? [];
        if (empty($order_info)) return $this->error([], '未找到对应订单');

        $order_model = new StoreOrder();
        $res = $order_model->verify($code);
        $res = is_array($res) ? $res : [];
        if (($res[ 'code' ] ?? -1) != 0) {
            return $this->error([], $res[ 'message' ] ?? '核销失败');
        }
        return success(0, '核销成功');
    }

    /**
     * 预约日程列表
     */
    public function schedule()
    {
        if (request()->isAjax()) {
            $date = input('date', date('Y-m-d'));
            $appointment_model = new Appointment();
            $condition = [ [ 'site_id', '=', $this->site_id ], [ 'appointment_date', '=', $date ] ];
            $res = $appointment_model->getScheduleList($condition, $date, 1, 100);
            return $this->success(is_array($res) ? ($res[ 'data' ] ?? []) : []);
        }
        return $this->fetch("cashier/schedule");
    }

    /**
     * 商家改约
     */
    public function reschedule()
    {
        $order_id = input('order_id', 0);
        $date = input('appointment_date', '');
        $start_time = input('start_time', '');
        $slot_text = input('appointment_slot', '');
        $num = input('num', 0);

        if (empty($order_id) || empty($date) || empty($start_time)) {
            return $this->error([], '改约信息不完整');
        }
        //校验订单归属
        $order_info = model('order')->getInfo([
            [ 'order_id', '=', $order_id ],
            [ 'site_id', '=', $this->site_id ],
        ], 'order_id,trade_type')[ 'data' ] ?? [];
        if (empty($order_info) || intval($order_info[ 'trade_type' ]) != 2) {
            return $this->error([], '该订单不是预约订单');
        }

        $appointment_model = new Appointment();
        $res = $appointment_model->reschedule($order_id, $date, $start_time, $slot_text, $num ? intval($num) : null);
        if ($res[ 'code' ] != 0) {
            return $this->error([], $res[ 'message' ] ?? '改约失败');
        }
        return success(0, '改约成功');
    }
}
