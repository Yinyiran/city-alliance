<?php
/**

 */

namespace addon\seckill\event;

use addon\seckill\model\Seckill;
/**
 * 开启活动
 */
class OpenSeckill
{

    public function handle($params)
    {
        $seckill = new Seckill();
        $res      = $seckill->cronOpenSeckill($params['relate_id']);
        return $res;
    }
}