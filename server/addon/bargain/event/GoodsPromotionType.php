<?php
/**

 */


namespace addon\bargain\event;

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
        return [ "name" => "砍价", "short" => "砍", "type" => "bargain", "color" => "#F58760", 'shop_url' => 'bargain://shop/bargain/lists', 'admin_url' => 'bargain://admin/bargain/lists' ];
    }
}