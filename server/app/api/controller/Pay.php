<?php
/**

 */

namespace app\api\controller;

use app\model\system\Pay as PayModel;

/**
 * 支付控制器
 */
class Pay extends BaseApi
{
    /**
     * 支付信息
     */
    public function info()
    {
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $pay = new PayModel();
        $info = $pay->getPayInfo($out_trade_no);
        return $this->response($info);
    }

    /**
     * 支付调用
     */
    public function pay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $pay_type = $this->params[ 'pay_type' ];
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $app_type = $this->params[ 'app_type' ];
        $pay = new PayModel();
        $is_matched = $this->params[ 'is_matched' ] ?? 0;
        $profit_sharing = $this->params[ 'profit_sharing' ] ?? false; //微信支付是否指定分账
        $info = $pay->pay($pay_type, $out_trade_no, $app_type, $this->member_id, '', $is_matched, $profit_sharing);
        return $this->response($info);
    }

    /**
     * 门店收银台：生成顾客扫码付款的收款码（微信 Native）
     * 参数：order_no 或 amount + store_id
     */
    public function cashierCode()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0 ) return $this->response($token );

        $order_no = $this->params[ 'order_no' ] ?? '';
        $amount = $this->params[ 'amount' ] ?? 0;
        $store_id = intval($this->params[ 'store_id' ] ?? 0);
        $pay_type = $this->params[ 'pay_type' ] ?? 'WECHAT_PAY';

        if (empty($order_no)) {
            return $this->response($this->error('', '请先创建订单'));
        }

        $order_info = model('order')->getInfo([
            [ 'order_no', '=', $order_no ],
            [ 'pay_status', '=', 0 ],
            [ 'order_status', '=', 0 ],
        ], 'order_id,order_no,pay_money,member_id,trade_type,site_id');
        if (empty($order_info)) {
            return $this->response($this->error('', '订单不存在或已支付'));
        }

        //到店消费订单允许现场自定义金额（仅下单时已允许的情况下）
        if ($amount > 0 && abs($amount - $order_info[ 'pay_money' ]) > 0.001) {
            $goods_ids = model('order_goods')->getColumn([ [ 'order_id', '=', $order_info[ 'order_id' ] ] ], 'goods_id');
            $allow = 0;
            foreach ($goods_ids as $goods_id) {
                $goods = model('goods')->getInfo([ [ 'goods_id', '=', $goods_id ] ], 'trade_type,allow_custom_amount');
                if (intval($goods[ 'trade_type' ] ?? 0) == 1 && intval($goods[ 'allow_custom_amount' ] ?? 0) == 1) {
                    $allow = 1;
                    break;
                }
            }
            if (!$allow) {
                return $this->response($this->error('', '该订单不支持修改金额'));
            }
            $min = 0;
            $max = 0;
            foreach ($goods_ids as $goods_id) {
                $goods = model('goods')->getInfo([ [ 'goods_id', '=', $goods_id ] ], 'min_order_amount,max_order_amount');
                $min = $goods[ 'min_order_amount' ] ?? 0;
                $max = $goods[ 'max_order_amount' ] ?? 0;
                break;
            }
            $amount = round(floatval($amount), 2);
            if ($min > 0 && $amount < $min) return $this->response($this->error('', "金额不能低于 {$min} 元"));
            if ($max > 0 && $amount > $max) return $this->response($this->error('', "金额不能高于 {$max} 元"));

            model('order')->update([
                'custom_amount' => $amount,
                'pay_money' => $amount,
                'order_money' => $amount,
                'pay_scene' => 1,
                'modify_time' => time(),
            ], [ [ 'order_id', '=', $order_info[ 'order_id' ] ] ]);
            $pay_money = $amount;
        } else {
            $pay_money = $order_info[ 'pay_money' ];
        }

        if ($pay_money <= 0) {
            return $this->response($this->error('', '订单金额为0，无需支付'));
        }

        $pay = new PayModel();
        //app_type 传 pc 走微信 Native，返回收款二维码
        $info = $pay->pay($pay_type, $order_info[ 'out_trade_no' ] ?? $order_info[ 'order_no' ], 'pc', $order_info[ 'member_id' ], '', 0, false);
        return $this->response($this->success([
            'order_no' => $order_info[ 'order_no' ],
            'store_id' => $store_id,
            'pay_money' => $pay_money,
            'pay_info' => $info[ 'data' ] ?? [],
        ]));
    }

    /**
     * 支付方式
     */
    public function type()
    {
        $pay = new PayModel();
        $info = $pay->getPayType($this->params);
        $temp = empty($info) ? [] : $info;
        $type = [];
        foreach ($temp[ 'data' ] as $k => $v) {
            array_push($type, $v[ "pay_type" ]);
        }
        $type = implode(",", $type);
        return $this->response(success(0, '', ['pay_type' => $type]));
    }

    /**
     * 获取订单支付状态
     */
    public function status()
    {
        $pay = new PayModel();
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $res = $pay->getPayStatus($out_trade_no);
        return $this->response($res);
    }
    
    /**
     * 汇付支付
     * 订单方法
     */
    public function huifupay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $out_trade_no = $this->params[ 'out_trade_no' ];
        $payModel = model('pay')->getInfo([ [ 'out_trade_no', '=', $out_trade_no ], ['pay_status', '=', 0] ]);

        if (!$payModel) {
            return $this->response($this->error('订单不存在'));
        }

        $order = model('order')->getInfo(['out_trade_no'=>$out_trade_no]);
        $result = (new Huifujspay())->jspay($order);
        return $this->response($this->success($result));
    }

    /**
     * 开通会员支付
     */
    public function memberlevelpay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $payModel = model('pay')->getInfo([ [ 'out_trade_no', '=', $out_trade_no ], ['pay_status', '=', 0] ]);
        if (!$payModel) {
            return $this->response($this->error('订单不存在'));
        }
        $order = model('member_level_order')->getInfo(['out_trade_no'=>$out_trade_no]);
        $result = (new Huifujspay())->memberlevelpay($order, $payModel);
        return $this->response($this->success($result));
    }

    /**
     * 商家缴纳保证金支付
     */
    public function shopdepositpay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $payModel = model('pay')->getInfo([ [ 'out_trade_no', '=', $out_trade_no ], ['pay_status', '=', 0] ]);
        if (!$payModel) {
            return $this->response($this->error('订单不存在'));
        }
        $order = model('shop_deposit_order')->getInfo(['out_trade_no'=>$out_trade_no]);
        $result = (new Huifujspay())->shopdepositpay($order, $payModel);
        return $this->response($this->success($result));
    }

}