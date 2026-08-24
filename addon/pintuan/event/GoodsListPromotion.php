<?php
/**

 */


namespace addon\pintuan\event;

use addon\pintuan\model\Pintuan;

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
        if (empty($param['promotion']) || $param['promotion'] != 'pintuan') return [];

        $condition = [
            ['pp.status', '=', 1],
            ['g.is_delete', '=', 0],
            ['g.goods_state', '=', 1],
            [ 'g.verify_state', '=', 1]
        ];
        if (!empty($param['site_id'])) $condition[] = ['pp.site_id', '=', $param['site_id']];
        if (isset($param['pintuan_name']) && !empty($param['pintuan_name'])) {
            $condition[] = ['pp.pintuan_name', 'like', '%' . $param['pintuan_name'] . '%'];
        }
        //商品名称模糊查询
        if (isset($param['goods_name']) && !empty($param['goods_name'])) {
            $condition[] = ['g.goods_name', 'like', '%' . $param['goods_name'] . '%'];
        }
        //城市分站id
        if (!empty($param['website_id'])) {
            $condition[] = ['g.website_id', '=', $param['website_id']];
        }

        $model = new Pintuan();
        $list = $model->getPintuanGoodsPageList($condition, $param['page'], $param['page_size'], 'pp.pintuan_id desc');
        return $list;
    }
}