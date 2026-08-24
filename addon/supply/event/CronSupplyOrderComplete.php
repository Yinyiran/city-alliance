<?php
/**

 */


namespace addon\supply\event;

use addon\supply\model\order\OrderCommon;
/**
 * 订单自动完成
 */
class CronSupplyOrderComplete
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $order = new OrderCommon();
        $res = $order->orderComplete($data["relate_id"]);//订单自动完成
        return $res;
	}
	
}