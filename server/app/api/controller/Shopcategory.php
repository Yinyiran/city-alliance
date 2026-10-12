<?php
/**

 */

namespace app\api\controller;

use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;
use app\model\shop\ShopCategory as ShopCategoryModel;

/**
 * 店铺分类
 * Class Shopcategory
 * @package app\api\controller
 */
class Shopcategory extends BaseApi
{

    public function page()
    {
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;

        $condition = [];
        $order = 'sort asc';
        $field = 'category_id,category_name,baozheng_money';
        $shop_category_model = new ShopCategoryModel();
        $list = $shop_category_model->getCategoryPageList($condition, $page, $page_size, $order, $field);
        return $this->response($list);
    }

    public function tree()
    {
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
        if (empty($site_id)) {
            return $this->response($this->error('', 'REQUEST_SITE_ID'));
        }
        $goods_category_model = new GoodsShopCategoryModel();
        $list = $goods_category_model->getShopCategoryTree([ [ 'site_id', "=", $site_id ] ]);
        return $this->response($list);
    }

    /**
     * 商家分类
     */
    public function categoryList()
    {
        $website_id = $this->params['website_id'] ?? '';
        $order = 'sort asc';
        $field = 'category_id,category_name';
        $list = model('shop_category')->getList(['website_id'=>$website_id], $field, $order);
        return $this->response($this->success($list));
    }

}