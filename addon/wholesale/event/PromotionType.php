<?php
/**

 */


namespace addon\wholesale\event;

/**
 * 活动类型
 */
class PromotionType
{

	public function handle()
	{
	    return ["name" => "批发", "type" => "wholesale"];
	}
}