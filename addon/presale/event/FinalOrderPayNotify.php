<?php
/**

 */


namespace addon\presale\event;

use addon\presale\model\PresaleOrderCommon;
/**
 * 尾款支付回调
 */
class FinalOrderPayNotify
{

	public function handle($params)
	{
	    $presale_order_model = new PresaleOrderCommon();
	    $res = $presale_order_model->finalOrderOnlinePay($params);
        return $res;
	}

}