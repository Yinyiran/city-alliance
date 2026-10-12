<?php
/**

 */


namespace app\event;

use app\model\shop\ShopOrderCalc as ShopOrderCalcModel;
/**
 * 订单项退款后后店铺订单计算
 */
class ShopOrderRefundCalc
{
	/**
	 * 传入订单信息
	 * @param unknown $data
	 */
	public function handle($data)
	{
	    $shop_order_calc = new ShopOrderCalcModel();
	    $res = $shop_order_calc->refundCalculate($data);
	    return $res;
	}
	
}