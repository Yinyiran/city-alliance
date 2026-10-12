<?php
/**

 */

namespace addon\niusms\event;

use addon\niusms\model\Order;

/**
 * 关闭短信充值订单
 */
class CloseSmsPayment
{
    public function handle($param)
    {
        $order = new Order();
        $res = $order->cronCloseSmsPayment($param[ 'relate_id' ]);
        return $res;
    }
}