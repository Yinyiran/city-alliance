<?php
/**

 */


namespace addon\bargain\event;

use addon\bargain\model\Bargain;

/**
 * 商品营销活动信息
 */
class GoodsListPromotion
{

    /**
     * 商品营销活动信息
     * @param $param
     * @return array
     */
    public function handle($param)
    {
        if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'bargain') return [];

        $condition = [
            [ 'pb.status', '=', 1 ],
            [ 'g.goods_state', '=', 1 ],
            [ 'g.is_delete', '=', 0 ],
            [ 'g.verify_state', '=', 1]
        ];
        if (!empty($param[ 'site_id' ])) {
            $condition[] = [ 'pb.site_id', '=', $param[ 'site_id' ] ];
        }
        if (isset($param[ 'bargain_name' ]) && !empty($param[ 'bargain_name' ])) {
            $condition[] = [ 'pb.bargain_name', 'like', '%' . $param[ 'bargain_name' ] . '%' ];
        }

        //商品名称模糊查询
        if (isset($param['goods_name']) && !empty($param['goods_name'])) {
            $condition[] = ['g.goods_name', 'like', '%' . $param['goods_name'] . '%'];
        }
        //城市分站id
        if (!empty($param[ 'website_id' ])) {
            $condition[] = [ 'sku.website_id', '=', $param[ 'website_id' ] ];
        }

        $field = 'pb.bargain_id,pb.floor_price,pb.bargain_stock,pb.site_id,pb.bargain_name,
        sku.sku_id,sku.price,sku.sku_name,sku.sku_image,sku.stock as goods_stock';

        $model = new Bargain();
        $list = $model->getBargainPageList($condition, $param[ 'page' ], $param[ 'page_size' ], $field);
        return $list;
    }
}