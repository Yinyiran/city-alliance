<?php
/**

 */

namespace addon\presale\event;

use addon\presale\model\PresaleOrderRefund;

/**
 * 订单退款
 */
class AddonOrderRefund
{

	public function handle($params)
	{
	    if($params['promotion_type'] == 'presale'){
            $presale = new PresaleOrderRefund();
            $res = $presale->refundPresaleOrder($params['order_no']);
            return $res;
        }
	}
}