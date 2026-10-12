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
            //事件链路（自动核销流程）传 force=true：
            //此环节无商家交互确认，若沿用核销宽限校验，超时会被静默拦截导致核销丢失。
            $store_order->verify($data[ 'verify_code' ], true);
        }
        return '';
    }

}