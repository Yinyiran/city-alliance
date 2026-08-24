<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Order;

/**
 * 订单维权状态变更
 */
class RefundStatusChange
{

    public function handle($data)
    {
        $res = (new Order())->refundStatusChange($data);
        return $res;
    }
}