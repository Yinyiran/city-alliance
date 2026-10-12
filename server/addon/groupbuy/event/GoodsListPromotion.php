<?php
/**

 */


namespace addon\groupbuy\event;

use addon\groupbuy\model\Groupbuy;

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
        if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'groupbuy') return [];

        $condition[] =[
            [ 'pg.status', '=', 2 ],
            //TODO 商品选择框
            ['g.goods_state','=',1],
            ['g.is_delete','=',0],
            [ 'g.verify_state', '=', 1]
        ] ;
        if (!empty($param[ 'site_id' ])) $condition[] = [ 'pg.site_id', '=', $param[ 'site_id' ] ];
        if (isset($param[ 'goods_name' ]) && !empty($param[ 'goods_name' ])) {
            $condition[] = [ 'g.goods_name', 'like', '%' . $param[ 'goods_name' ] . '%' ];
        }
        $model = new Groupbuy();

        //城市分站id
        if (!empty($param[ 'website_id' ])) {
            $condition[] = [ 'g.website_id', '=', $param[ 'website_id' ] ];
        }
        $list = $model->getGroupbuyGoodsPageList($condition, $param[ 'page' ], $param[ 'page_size' ], 'pg.groupbuy_id desc');
        return $list;
    }
}