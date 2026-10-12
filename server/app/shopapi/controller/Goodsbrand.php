<?php
/**

 */

namespace app\shopapi\controller;

use app\model\goods\GoodsBrand as GoodsBrandModel;

/**
 * 商品品牌
 * Class Goodsbrand
 * @package app\shopapi\controller
 */
class Goodsbrand extends BaseApi
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }

    /**
     * 获取品牌
     * @return false|string
     */
    public function getBrandList()
    {
        $goods_brand_model = new GoodsBrandModel();
        $brand_list = $goods_brand_model->getBrandList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], "brand_id, brand_name");
        return $this->response($brand_list);
    }


}
