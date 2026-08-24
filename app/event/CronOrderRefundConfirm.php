<?php
/**

 */

namespace app\event;

use app\model\order\OrderCommon;
use app\model\order\OrderRefund;

/**
 * 定时通过维权
 * @author Administrator
 *
 */
class CronOrderRefundConfirm
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
        $result = $order_refund_model->orderRefundConfirm($data);
        return $result;
    }
}
