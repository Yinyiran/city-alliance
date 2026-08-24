<?php


namespace app\event;

use addon\fenxiao\model\FenxiaoOrder;

/**
 * 返积分
 * Class FanPoint
 * @package app\event
 */
class FanPoint
{
    public function handle($order)
    {
        $fenxiao_order = new FenxiaoOrder();
        $res = $fenxiao_order->fanPoint($order);
        return $res;
    }
}