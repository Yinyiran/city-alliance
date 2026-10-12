<?php
/**

 */
namespace addon\printer\event;

use app\model\system\Cron;
use think\facade\Log;

/**
 * 订单打印
 */
class OrderPay
{

	public function handle($param)
	{
	    if(!addon_is_exit('printer')) return [];
        (new Cron())->addCron(1, 0, "订单小票打印", "PrintOrder", time(), $param['order_id']);
	}
}