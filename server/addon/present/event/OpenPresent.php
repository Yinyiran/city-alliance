<?php
/**

 */


namespace addon\present\event;

use addon\present\model\Present;
/**
 * 启动活动
 */
class OpenPresent
{

	public function handle($params)
	{
	    $pintuan = new Present();
	    $res= $pintuan->cronOpenPresent($params['relate_id']);
        return $res;
	}
}