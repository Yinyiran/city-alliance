<?php


namespace app\event;

use addon\fenxiao\model\FenxiaoOrder;

/**
 * 返余额
 * Class FanMoney
 * @package app\event
 */
class FanMoney
{
    public function handle($order)
    {
        $fenxiao_order = new FenxiaoOrder();
        $res = $fenxiao_order->fanMoney($order);
        return $res;
    }
}