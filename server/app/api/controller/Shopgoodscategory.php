<?php
/**

 */

namespace app\api\controller;

use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;

/**
 * 店铺商品分类
 * Class Shopcategory
 * @package app\api\controller
 */
class Shopgoodscategory extends BaseApi
{
    /**
     * 树状结构信息
     */
    public function tree()
    {
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
        $level = isset($this->params[ 'level' ]) ? $this->params[ 'level' ] : 2;// 分类等级 1 2
//		$template = isset($this->params['template']) ? $this->params['template'] : 2;// 模板 1：无图，2：有图，3：有商品
        if (empty($site_id)) {
            return $this->response($this->error('', 'REQUEST_SITE_ID'));
        }
        $condition = [
            [ 'site_id', "=", $site_id ],
            [ 'is_show', '=', 1 ],
            [ 'level', '<=', $level ]
        ];
        $goods_category_model = new GoodsShopCategoryModel();
        $list = $goods_category_model->getShopCategoryTree($condition);
        return $this->response($list);
    }

}