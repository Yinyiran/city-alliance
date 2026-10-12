<?php
/**

 */

namespace addon\seckill\event;

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
        return ["name" => "限时秒杀", "short" => "秒", "type" => "seckill", "color" => "#89689D", 'shop_url' => 'seckill://shop/seckill/goodslist', 'admin_url' => 'seckill://admin/seckill/goodslist'];
    }
}