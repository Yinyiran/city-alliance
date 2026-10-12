<?php
/**

 */


namespace addon\platformcoupon\event;

use addon\platformcoupon\model\Platformcoupon;
/**
 * 启动活动
 */
class CronPlatformcouponEnd
{

	public function handle($params=[])
	{
	    $coupon = new Platformcoupon();
	    $res= $coupon->cronPlatformcouponEnd();
        return $res;
	}
}