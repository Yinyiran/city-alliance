<?php
/**

 */


namespace addon\city\event;

use addon\city\model\CitySettlement;
use Carbon\Carbon;

/**
 * 分站结算
 */
class WebsiteSettlement
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $model = new CitySettlement();
        $time = Carbon::today()->timestamp+60*30;
        $res = $model->citySettlement($time);

        return $res;
	}
	
}