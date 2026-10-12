<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Goods;

/**
 * 商品编辑之后
 */
class GoodsEdit
{

    public function handle($data)
    {
        $goods = new Goods();
        $goods->updateGoods(['goods_id' => $data['goods_id'], 'site_id' => $data['site_id'] ]);
    }
}