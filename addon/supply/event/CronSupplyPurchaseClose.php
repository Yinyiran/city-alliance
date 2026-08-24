<?php
/**

 */


namespace addon\supply\event;

use addon\supply\model\Purchase;

/**
 * 求购单自动关闭
 */
class CronSupplyPurchaseClose
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $purchase = new Purchase();
        $result = $purchase->closePurchase([["purchase_id", "=", $data["relate_id"]]]);
        return $result;
	}
	
}