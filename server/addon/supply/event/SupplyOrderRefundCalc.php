<?php
/**

 */


namespace addon\supply\event;

use addon\supply\model\SupplyOrderCalc as SupplyOrderCalcModel;
/**
 * 订单项退款后后店铺订单计算
 */
class SupplyOrderRefundCalc
{
	/**
	 * 传入订单信息
	 * @param unknown $data
	 */
	public function handle($data)
	{
	    $supply_order_calc = new SupplyOrderCalcModel();
	    $res = $supply_order_calc->refundCalculate($data);
	    return $res;
	}
	
}