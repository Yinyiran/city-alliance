<?php
/**

 */


namespace addon\platformcoupon\event;

use app\model\system\Cron;

/**
 * 应用安装
 */
class Install
{
	/**
	 * 执行安装
	 */
	public function handle()
	{
	    try{
	        execute_sql('addon/platformcoupon/data/install.sql');

            $cron = new Cron();
            $cron->deleteCron([ ['event', '=', 'CronPlatformcouponEnd'] ]);
            $cron->addCron(2, 1, '平台优惠券过期自动关闭', 'CronPlatformcouponEnd', time(), 0);

	        return success();
	    }catch (\Exception $e)
	    {
	        return error('', $e->getMessage());
	    }
	}
}