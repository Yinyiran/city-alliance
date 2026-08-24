<?php
/**

 */

namespace addon\supply\model\goods;

use think\facade\Cache;
use app\model\BaseModel;

/**
 * 商品分类
 */
class GoodsCategory extends BaseModel
{

    /**
     * 修改商品分类 的供应商分佣比率
     * @param $data
     * @return \multitype
     */
    public function editCategory($condition, $commission_rate)
    {
        $check_condition = array_column($condition, 2, 0);
        $category_id = isset($check_condition[ 'category_id' ]) ? $check_condition[ 'category_id' ] : '';
        $data = array (
             'supply_commission_rate'=> $commission_rate
        );
        $res = model('goods_category')->update($data, $condition);

        //修改受影响的商品分佣比率
        $goods_condition = array (
            [ 'category_json', 'like', '%' . $category_id . '"]' ]
        );
        model('supply_goods')->update([ 'commission_rate' => $commission_rate ],$goods_condition);

        Cache::tag("goods_category")->clear();

        return $this->success($res);
    }


}