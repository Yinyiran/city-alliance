<?php
/**

 */
namespace addon\groupbuy\event;

use addon\groupbuy\model\Groupbuy;

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
        if ($param['promotion_type'] == 'groupbuy') {
            $bargain_order = new Groupbuy();
            $res = $bargain_order->orderPay($param);
            return $res;
        }
    }
}