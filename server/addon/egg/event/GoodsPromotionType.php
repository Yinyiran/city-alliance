<?php
/**

 */


namespace addon\egg\event;

/**
 * 活动类型
 */
class GoodsPromotionType
{
    /**
     * 活动类型
     * @return array
     */
	public function handle()
	{
		return ["name" => "砸金蛋", "short" => "", "type" => "egg", "color" => "#4CB130", 'shop_url' => 'egg://shop/egg/lists', 'admin_url' => 'egg://admin/egg/lists'];
	}
}