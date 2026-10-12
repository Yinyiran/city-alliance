<?php


namespace app\admin\controller;

use addon\supply\model\Supplier as SupplierModel;
use app\model\BaseModel;
use app\model\express\ExpressTemplate as ExpressTemplateModel;
use app\model\goods\Config as GoodsConfigModel;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsAttribute as GoodsAttributeModel;
use app\model\goods\GoodsBrand as GoodsBrandModel;
use app\model\goods\GoodsBrowse;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\GoodsCollect;
use app\model\goods\GoodsEvaluate as GoodsEvaluateModel;
use app\model\goods\GoodsExport;
use app\model\goods\GoodsImport;
use app\model\goods\GoodsShopCategory;
use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;

/**
 * 简介
 * Class Company
 * @package app\admin\controller
 */
class Company extends BaseAdmin
{
    public function index()
    {
        $goods_model = new GoodsModel();
        if (request()->isAjax()) {
            $video_url_info = input('video_url', '');
            $content_info = input('goods_content', '');
            model('website')->update(['video_url_info'=>$video_url_info,'content_info'=>$content_info], ['site_id'=>0]);
            return (new BaseModel())->success('');
        } else {
            /*$goods_id = 1;
            $goods_info = $goods_model->editGetGoodsInfo([ [ 'goods_id', '=', $goods_id ], ]);
            $goods_info = $goods_info[ 'data' ];

            $goods_sku_list = $goods_model->getGoodsSkuList([ [ 'goods_id', '=', $goods_id ], [ 'site_id', '=', $this->site_id ] ], "sku_id,sku_name,sku_no,sku_spec_format,price,market_price,cost_price,stock,weight,volume,sku_image,sku_images,goods_spec_format,spec_name,stock_alarm,is_default", '');
            $goods_sku_list = $goods_sku_list[ 'data' ];
            $goods_info[ 'sku_list' ] = $goods_sku_list;
            $this->assign("goods_info", $goods_info);

            //获取一级商品分类
            $goods_category_model = new GoodsCategoryModel();
            $condition = [
                [ 'pid', '=', 0 ],
            ];

            $goods_category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,level,commission_rate');
            $goods_category_list = $goods_category_list[ 'data' ];
            $this->assign("goods_category_list", $goods_category_list);

            //获取运费模板
            $express_template_model = new ExpressTemplateModel();
            $express_template_list = $express_template_model->getExpressTemplateList([ [ 'site_id', "=", $this->site_id ] ], 'template_id,template_name', 'is_default desc');
            $express_template_list = $express_template_list[ 'data' ];
            $this->assign("express_template_list", $express_template_list);

            //获取商品类型
            $goods_attr_model = new GoodsAttributeModel();
            $attr_class_list = $goods_attr_model->getAttrClassList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], 'class_id,class_name');
            $attr_class_list = $attr_class_list[ 'data' ];
            $this->assign("attr_class_list", $attr_class_list);

            //获取品牌
            $goods_brand_model = new GoodsBrandModel();
            $brand_list = $goods_brand_model->getBrandList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], "brand_id, brand_name");
            $brand_list = $brand_list[ 'data' ];
            $this->assign("brand_list", $brand_list);

            //获取店内分类
            $goods_shop_category_model = new GoodsShopCategoryModel();
            $goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([ [ 'site_id', "=", $this->site_id ] ], 'category_id,category_name,pid,level');
            $goods_shop_category_list = $goods_shop_category_list[ 'data' ];
            $this->assign("goods_shop_category_list", $goods_shop_category_list);

            $is_install_supply = addon_is_exit("supply");
            if ($is_install_supply) {
                $supplier_model = new SupplierModel();
                $supplier_list = $supplier_model->getSupplierPageList([], 1, PAGE_LIST_ROWS, 'supplier_id DESC');
                $supplier_list = $supplier_list[ 'data' ][ 'list' ];
                $this->assign("supplier_list", $supplier_list);
            }
            $this->assign("is_install_supply", $is_install_supply);*/
            $goods_info = model('website')->getInfo(['site_id'=>0]);
            $this->assign("goods_info", $goods_info);
            return $this->fetch('company/index');
        }
    }
}