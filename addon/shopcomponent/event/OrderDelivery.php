<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Order;

/**
 * 订单发货完成
 */
class OrderDelivery
{

    public function handle($data)
    {
        $res = (new Order())->delivery($data['order_id']);
        return $res;
    }
}