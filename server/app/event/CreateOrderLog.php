<?php
/**

 */

namespace app\event;

use app\model\order\OrderCommon;

/**
 * 创建订单后添加订单日志
 */
class CreateOrderLog
{
    // 行为扩展的执行入口必须是run
    public function handle($params)
    {
        if (isset($params['order_id']) && $params['order_id'] > 0) {

            $order_model = new OrderCommon();
            //获取订单信息
            $order_info = $order_model->getOrderInfo([['order_id', '=', $params['order_id']]], 'member_id,name,order_status,order_status_name');
            $order_info = $order_info['data'];
            if (!empty($order_info)) {

                $order_log = [
                    'uid' => $order_info['member_id'] ?? 0,
                    'nick_name' => $order_info['name'] ?? '',
                    'action' => '订单创建',
                    'order_status' => $order_info['order_status'],
                    'order_status_name' => $order_info['order_status_name'],
                    'module' => 'member',
                    'action_way' => 'create',
                    'order_id' => $params['order_id']
                ];
                $res = $order_model->addOrderLog($order_log);
                return $res;
            }
        }

    }
}