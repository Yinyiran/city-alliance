<?php
/**

 */


namespace addon\presale\event;

use addon\presale\model\PresaleOrderCommon;
/**
 * 定金支付回调
 */
class DepositOrderPayNotify
{

	public function handle($params)
	{
	    $presale_order_model = new PresaleOrderCommon();
	    $res = $presale_order_model->depositOrderOnlinePay($params);
        return $res;
	}

}