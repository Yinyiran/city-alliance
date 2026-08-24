<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Weapp;

/**
 * 订单支付成功
 */
class OrderPay
{

    public function handle($data)
    {
        if ($data['is_video_number'] && $data['pay_type'] == 'wechatpay') {
            $member = model('member')->getInfo([ ['member_id', '=', $data['member_id'] ] ], 'weapp_openid');
            $pay = model('pay')->getInfo([ ['out_trade_no', '=', $data['out_trade_no'] ] ], 'trade_no,pay_time');
            $param = [
                'out_order_id' => $data['order_id'],
                'openid' => $member['weapp_openid'],
                'action_type' => 1,
                'transaction_id' => $pay['trade_no'],
                'pay_time' => date('Y-m-d H:i:s', $pay['pay_time'])
            ];
            (new Weapp())->pay($param);
        }
    }
}