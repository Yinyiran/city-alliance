<?php
/**

 */


namespace app\event;

use app\model\order\Config;
use app\model\system\Cron;

/**
 * 订单维权被拒绝事件
 */
class OrderRefundRefuse
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        //todo 维权自动撤销时间
        $order_config_model = new Config();
        $config = $order_config_model->getOrderEventTimeConfig()['data']['value'] ?? [];
        $auto_refund_cancel = $config['auto_refund_cancel'] ?? 0;
        $cron_model = new Cron();
        $now_time = time();//当前时间
        $execute_time = $now_time + $auto_refund_cancel * 86400;//自动收货时间
        $result = $cron_model->addCron(1, 1, "维权自动撤销", "CronCancelRefund", $execute_time, $data['order_goods_id']);
        return $result;

    }
	
}