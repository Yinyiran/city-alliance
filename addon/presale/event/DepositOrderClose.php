<?php
/**

 */


namespace addon\presale\event;

use addon\presale\model\PresaleOrderCommon;
/**
 * 定金订单关闭
 */
class DepositOrderClose
{

	public function handle($params)
	{
	    $presale_order_model = new PresaleOrderCommon();
	    $res = $presale_order_model->cronDepositOrderClose($params['relate_id']);
        return $res;
	}

}