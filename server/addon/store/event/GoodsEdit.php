<?php
/**

 */

namespace addon\store\event;

use addon\store\model\StoreGoods;

/**
 * 商品编辑之后
 */
class GoodsEdit
{

    public function handle($data)
    {
        $goods = new StoreGoods();
        $goods->goodsEditAfter($data['goods_id'], $data['site_id']);
    }
}