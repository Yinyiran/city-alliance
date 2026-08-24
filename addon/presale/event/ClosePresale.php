<?php
/**

 */


namespace addon\presale\event;

use addon\presale\model\Presale;
/**
 * 关闭活动
 */
class ClosePresale
{

	public function handle($params)
	{
	    $presale = new Presale();
	    $res = $presale->cronClosePresale($params['relate_id']);
        return $res;
	}
}