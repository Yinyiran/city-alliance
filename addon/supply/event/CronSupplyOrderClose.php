<?php
/**

 */


namespace addon\supply\event;

use addon\supply\model\order\OrderCommon;
/**
 * 订单自动关闭
 */
class CronSupplyOrderClose
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $order = new OrderCommon();
        $order_info_result = $order->getOrderInfo([["order_id", "=", $data["relate_id"]]], "order_status");
        if(!empty($order_info_result) && $order_info_result["data"]["order_status"] == 0){
            $result = $order->orderClose($data["relate_id"]);//订单自动关闭
            return $result;
        }
	}
	
}