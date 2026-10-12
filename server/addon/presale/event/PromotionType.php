<?php
/**

 */


namespace addon\presale\event;

/**
 * 活动类型
 */
class PromotionType
{

	/**
	 * 活动类型
	 * @return multitype:number unknown
	 */
	public function handle()
	{
	    return ["name" => "商品预售", "type" => "presale"];
	}
}