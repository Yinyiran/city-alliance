<?php
/**

 */

namespace addon\bundling\event;

use addon\bundling\model\Bundling;
/**
 * 下架商品
 */
class OffGoods
{
    /**
     *  下架商品（需判断套餐是否存在该商品，存在活动关闭）
     */
    public function handle($param)
    {
        $model = new Bundling();
        $res = $model->cronDeleteGoods($param);
        return $res;
    }
}