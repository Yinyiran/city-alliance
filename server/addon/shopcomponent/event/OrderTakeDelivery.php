<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Order;

/**
 * 订单收货
 */
class OrderTakeDelivery
{

    public function handle($data)
    {
        $res = (new Order())->takeDelivery($data['order_id']);
        return $res;
    }
}