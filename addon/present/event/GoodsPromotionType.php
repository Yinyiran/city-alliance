<?php
/**

 */

namespace addon\present\event;

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
        return ["name" => "批发活动", "short" => "赠", "type" => "present", "color" => "#F1A750", 'shop_url' => 'present://shop/present/lists', 'admin_url' => 'present://admin/present/lists'];
    }
}