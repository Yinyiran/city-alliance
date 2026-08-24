<?php
/**

 */


namespace addon\present\event;

use addon\present\model\Present;
/**
 * 关闭活动
 */
class ClosePresent
{

	public function handle($params)
	{
	    $present = new present();
	    $res = $present->cronClosePresent($params['relate_id']);
        return $res;
	}
}