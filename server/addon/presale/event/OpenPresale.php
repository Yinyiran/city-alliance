<?php
/**

 */


namespace addon\presale\event;

use addon\presale\model\Presale;
/**
 * 启动活动
 */
class OpenPresale
{

	public function handle($params)
	{
	    $pintuan = new Presale();
	    $res= $pintuan->cronOpenPresale($params['relate_id']);
        return $res;
	}
}