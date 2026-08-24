<?php
/**

 */

namespace addon\seckill\event;

use addon\seckill\model\Seckill;
/**
 * 关闭活动
 */
class CloseSeckill
{

    public function handle($params)
    {
        $seckill = new Seckill();
        $res      = $seckill->cronCloseSeckill($params['relate_id']);
        return $res;
    }
}