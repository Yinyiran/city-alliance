<?php
/**

 */


namespace addon\platformcoupon\event;

use addon\platformcoupon\model\PlatformcouponType;

/**
 * 优惠券定时结束
 */
class CronPlatformcouponTypeEnd
{

	public function handle($params=[])
	{
	    $coupon = new PlatformcouponType();
	    $res= $coupon->platformcouponCronEnd($params['relate_id']);
        return $res;
	}
}