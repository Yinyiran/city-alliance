<?php
/**

 */


namespace addon\presale\event;

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
		return [ "name" => "商品预售", "short" => "预", "type" => "presale", "color" => "#4CB130", 'url' => 'presale://shop/presale/lists' ];
	}
}