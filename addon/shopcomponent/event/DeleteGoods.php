<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Goods;

/**
 * 商品删除之后
 */
class DeleteGoods
{

    public function handle($data)
    {
        $goods = new Goods();
        foreach (explode(',', $data['goods_id']) as $goods_id) {
            $goods->deleteGoods($goods_id, $data['site_id']);
        }
    }
}