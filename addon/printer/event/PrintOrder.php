<?php
/**

 */


namespace addon\printer\event;

use addon\printer\model\PrinterOrder;
use think\facade\Log;

/**
 * 关闭活动
 */
class PrintOrder
{

	public function handle($params)
	{
        return (new PrinterOrder())->printOrder($params['relate_id']);
	}
}