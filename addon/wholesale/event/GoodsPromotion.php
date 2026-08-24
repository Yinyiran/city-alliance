<?php
/**

 */

namespace addon\wholesale\event;

use addon\wholesale\model\Wholesale;
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
        if (empty($param['goods_id'])) return [];
        $goods_model = new GoodsModel();
        $goods_info  = $goods_model->getGoodsInfo([['goods_id', '=', $param['goods_id']]], 'promotion_addon,goods_stock');
        $goods_info  = $goods_info['data'];
        if (!empty($goods_info['promotion_addon'])) {
            $promotion_addon = json_decode($goods_info['promotion_addon'], true);
            if (!empty($promotion_addon['wholesale'])) {
                $wholesale_model  = new Wholesale();
                $condition    = [
                    ['wgs.sku_id', '=', $param['sku_id']],
                ];
                $goods_detail = $wholesale_model->getWholesaleSkuDetail($condition);
                $goods_detail = $goods_detail['data'];
                if (!empty($goods_detail)) {
                    $goods_detail['stock' ] = $goods_info['goods_stock'];
                    $goods_detail['promotion_type'] = 'wholesale';
                    $goods_detail['promotion_name'] = '批发活动';
                    return $goods_detail;
                }
            }
        }
        return [];
    }
}













