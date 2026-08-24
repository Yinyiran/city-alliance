<?php
/**

 */

namespace addon\wholesale\event;

/**
 * 订单营销活动类型
 */
class OrderPromotionType
{

    /**
     * 订单营销活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "批发", "type" => "wholesale"];
    }
}