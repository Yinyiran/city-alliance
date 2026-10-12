<?php
/**

 */

namespace addon\present\event;

use addon\present\model\Present;
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
            if (!empty($promotion_addon['present'])) {
                $present_model  = new Present();
                $condition    = [
                    ['pg.sku_id', '=', $param['sku_id']],
                ];
                $goods_detail = $present_model->getPresentGoodsDetail($condition);
                $goods_detail = $goods_detail['data'];
                if (!empty($goods_detail)) {
                    $goods_detail['stock'] = $goods_info['goods_stock'];
                    $goods_detail['promotion_type'] = 'present';
                    $goods_detail['promotion_name'] = '赠品活动';
                    return $goods_detail;
                }
            }
        }
        return [];
    }
}













