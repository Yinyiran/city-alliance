<?php
/**

 */

namespace app\event;

use app\model\order\OrderCommon;
use app\model\order\OrderRefund;

/**
 * 定时通过卖家收货
 * @author Administrator
 *
 */
class CronOrderRefundTakeDelivery
{
    public function handle($param)
    {
        $order_model = new OrderCommon();
        $condition = array (
            [ 'order_goods_id', '=', $param[ 'relate_id' ] ]
        );
        $order_goods_info = $order_model->getOrderGoodsInfo($condition)[ 'data' ] ?? [];
        $order_refund_model = new OrderRefund();
        $data = array (
            'order_goods_id' => $order_goods_info[ 'order_goods_id' ],
            'site_id' => $order_goods_info[ 'site_id' ]
        );
        $result = $order_refund_model->orderRefundTakeDelivery($data);
        return $result;
    }
}
