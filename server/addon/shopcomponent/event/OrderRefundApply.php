<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Order;

/**
 * 订单申请维权
 */
class OrderRefundApply
{

    public function handle($data)
    {
        $res = (new Order())->refundApply($data);
        return $res;
    }
}