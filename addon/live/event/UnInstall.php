<?php
/**

 */


namespace addon\live\event;

use app\model\system\Cron;

/**
 * 应用卸载
 */
class UnInstall
{
	/**
	 * 执行卸载
	 */
	public function handle()
	{
	    try{

            $cron_model = new Cron();
            $result = $cron_model->deleteCron([ [ 'event', '=', 'LiveRoomStatus' ] ] );
            if($result['code'] < 0)
                return $result;

            $result = $cron_model->deleteCron([ [ 'event', '=', 'LiveGoodsStatus' ] ] );
            if($result['code'] < 0)
                return $result;

	        return success();
	    }catch (\Exception $e)
	    {
	        return error('', $e->getMessage());
	    }
	}
}