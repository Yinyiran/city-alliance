<?php
/**

 */


namespace app\event;

use app\model\goods\Goods;

/**
 * 店铺关闭
 * @author Administrator
 *
 */
class ShopClose
{
    public function handle($data)
    {
        $site_id = $data["site_id"];
        //将店铺下的商品全部下架
//        $goods_model = new Goods();
//        $goods_result = $goods_model->lockup([["site_id", "=", $site_id]], "店铺关闭");
//        return $goods_result;
    }
}
