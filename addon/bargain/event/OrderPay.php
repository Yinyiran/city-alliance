<?php
/**

 */

namespace addon\bargain\event;

use addon\bargain\model\Bargain;

/**
 * 活动展示
 */
class OrderPay
{

    /**
     * 活动展示
     *
     * @return multitype:number unknown
     */
    public function handle($param)
    {
        if ($param['promotion_type'] == 'bargain') {
            $bargain_order = new Bargain();
            $res = $bargain_order->orderPay($param);
            return $res;
        }
    }
}