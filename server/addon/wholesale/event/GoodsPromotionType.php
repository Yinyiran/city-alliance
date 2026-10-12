<?php
/**

 */

namespace addon\wholesale\event;

/**
 * 活动类型
 */
class GoodsPromotionType
{

    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "批发活动", "short" => "批", "type" => "wholesale", "color" => "#F1A750", 'shop_url' => 'wholesale://shop/wholesale/lists', 'admin_url' => 'wholesale://admin/wholesale/lists'];
    }
}