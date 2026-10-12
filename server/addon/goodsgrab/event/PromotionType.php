<?php
/**

 */

namespace addon\goodsgrab\event;

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
        return ["name" => "商品采集", "type" => "goodsgrab"];
    }
}