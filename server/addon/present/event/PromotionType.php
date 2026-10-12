<?php
/**

 */


namespace addon\present\event;

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
	    return ["name" => "赠品", "type" => "present"];
	}
}