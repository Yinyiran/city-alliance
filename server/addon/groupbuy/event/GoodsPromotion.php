<?php
/**

 */

namespace addon\groupbuy\event;

use addon\groupbuy\model\Groupbuy;
use app\model\goods\Goods as GoodsModel;

/**
 * 商品营销活动信息
 */
class GoodsPromotion
{

    /**
     * 商品营销活动信息
     * @param $param
     * @return array
     */
    public function handle($param)
    {
        if (empty($param[ 'goods_id' ])) return [];
        $goods_model = new GoodsModel();
        $goods_info = $goods_model->getGoodsInfo([ [ 'goods_id', '=', $param[ 'goods_id' ] ] ], 'promotion_addon,goods_stock');
        $goods_info = $goods_info[ 'data' ];
        if (!empty($goods_info[ 'promotion_addon' ])) {
            $promotion_addon = json_decode($goods_info[ 'promotion_addon' ], true);
            if (!empty($promotion_addon[ 'groupbuy' ])) {
                $groupbuy_model = new Groupbuy();
                $condition = [
                    [ 'pg.groupbuy_id', '=', $promotion_addon[ 'groupbuy' ] ],
                    [ 'pg.goods_id', '=', $param[ 'goods_id' ] ],
                    [ 'pg.status', '=', 2 ],
                    [ 'g.goods_state', '=', 1 ],
                    [ 'g.is_delete', '=', 0 ]
                ];
                $goods_detail = $groupbuy_model->getGroupbuyInfo($condition, 'pg.groupbuy_id,pg.site_id,pg.goods_id,pg.goods_price,pg.groupbuy_price,g.sku_id');
                $goods_detail = $goods_detail[ 'data' ];
                if (!empty($goods_detail)) {
                    $goods_detail[ 'stock' ] = $goods_info['goods_stock'];
                    $goods_detail[ 'promotion_type' ] = 'groupbuy';
                    $goods_detail[ 'promotion_name' ] = '团购';
                    return $goods_detail;
                }
            }
        }
        return [];
    }
}