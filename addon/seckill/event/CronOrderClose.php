<?php
/**

 */

namespace addon\seckill\event;

use addon\seckill\model\SeckillOrderCreate;
/**
 * 订单营销活动类型
 */
class CronOrderClose
{

    /**
     * 订单关闭
     * @return multitype:number unknown
     */
    public function handle($params)
    {
        $seckill = new SeckillOrderCreate();
        $res      = $seckill->CronOrderClose($params['order_id']);
        return $res;
    }
}