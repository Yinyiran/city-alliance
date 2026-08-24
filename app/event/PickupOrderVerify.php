<?php
/**

 */


namespace app\event;

use app\model\order\StoreOrder;

/**
 * 门店订单提货
 */
class PickupOrderVerify
{

    public function handle($data)
    {
        if ($data[ 'verify_type' ] == 'pickup') {

            $store_order = new StoreOrder();
            $store_order->verify($data[ 'verify_code' ]);
        }
        return '';
    }

}