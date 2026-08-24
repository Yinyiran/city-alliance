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