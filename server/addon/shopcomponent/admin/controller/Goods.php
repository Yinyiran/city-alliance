<?php
/**

 */

namespace addon\shopcomponent\admin\controller;

use addon\shopcomponent\model\Category;
use addon\shopcomponent\model\Weapp;
use app\model\system\Cron;
use app\admin\controller\BaseAdmin;
use addon\shopcomponent\model\Goods as GoodsModel;
use addon\shopcomponent\model\Category as CategoryModel;
/**
 * 商品
 */
class Goods extends BaseAdmin
{
    public function lists()
    {
        if (request()->isAjax()) {
            $goods = new GoodsModel();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [];
            $data = $goods->getGoodsPageList($condition, '*', 'id desc', $page, $page_size);
            return $data;
        } else {
            $category = (new Category())->getCategoryByParent(1);

            $this->assign('first_cat', $category['data']);

            $check_res = (new Weapp())->checkRegister();
            $this->assign('checkres', $check_res);
            return $this->fetch("goods/index");
        }
    }

    public function sync(){
        $start = input('start', 0);
        $site_id= input('site_id', 0);
        $res = (new GoodsModel())->syncGoods($start,100,$site_id);
        return $res;
    }

    /**
     * 小程序添加商品
     */
    public function add()
    {
        if (request()->isAjax()) {
            $param = input();
            if(empty($param))return $this->error('必要参数必填');
            $data = [
                'site_id' => isset($param['site_id']) ? $param['site_id'] : 0,
                'goods_ids' =>$param['goods_ids'],
                'third_cat_id' =>$param['third_cat_id'],

            ];
            $goods_model = new GoodsModel();
            return $goods_model->addGoods($data);
        }
    }

    /**
     * 小程序修改商品分类
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $param = input();
            if(empty($param))return $this->error('必要参数必填');
            $data = [
                'site_id' => isset($param['site_id']) ? $param['site_id'] : 0,
                'goods_id' =>$param['goods_id'],
                'third_cat_id' =>$param['third_cat_id'],

            ];
            $goods_model = new GoodsModel();
            return $goods_model->updateGoods($data);
        }
    }

    /**
     * 检验分类是否已上传资质
     */
    public function check()
    {
        if (request()->isAjax()) {
            $param = input();
            if(empty($param))return $this->error('必要参数必填');
            $data = [
                'third_cat_id' =>$param['third_cat_id'],
                'site_id' =>isset($param['site_id']) ? $param['site_id'] : 0,
            ];
            $cat_model = new CategoryModel();
            return $cat_model->isQualifications($data);
        }
    }

    /**
     * 小程序删除商品
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $site_id= input('site_id', 0);

            $goods_model = new GoodsModel();
            return $goods_model->deleteGoods(input('out_product_ids',''),$site_id);
        }
    }

    /**
     * 商品上架
     */
    public function listing()
    {
        if (request()->isAjax()) {
            $site_id= input('site_id', 0);

            $goods_model = new GoodsModel();
            $out_product_ids = input('out_product_ids', '');
            return $goods_model->goodsListing($out_product_ids, $site_id);
        }
    }

    /**
     * 商品下架
     */
    public function dellisting()
    {
        if (request()->isAjax()) {
            $site_id= input('site_id', 0);

            $goods_model = new GoodsModel();
            $out_product_ids = input('out_product_ids', '');
            return $goods_model->goodsDelisting($out_product_ids, $site_id);
        }
    }

    /**
     * 视频号接入
     * @return mixed
     */
    public function access()
    {
        $weapp = new Weapp();
        if (request()->isAjax()) {
            $site_id= input('site_id', 0);

            $res = $weapp->apply();
            if ($res['code'] == 0) {
                (new Cron())->addCron(1, 0, "同步微信类目", "SyncWxCategory", time(), $site_id);
            }
            return $res;
        }
        $checkres = $weapp->checkRegister();
        $this->assign('checkres', $checkres);
        return $this->fetch("goods/access");
    }

}