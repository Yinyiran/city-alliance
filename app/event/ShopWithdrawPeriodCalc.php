<?php
/**

 */


namespace app\event;

use app\model\shop\ShopSettlement;
use Carbon\Carbon;
/**
 * 店铺账期转账
 */
class ShopWithdrawPeriodCalc
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $model = new ShopSettlement();
        $time = Carbon::today()->timestamp;

        $res = $model->shopSettlement($time);

        return $res;
	}
	
}