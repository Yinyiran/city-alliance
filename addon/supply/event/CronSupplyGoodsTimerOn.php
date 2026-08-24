<?php
/**

 */
namespace addon\supply\event;

use addon\supply\model\goods\Goods;

/**
 * 定时上架商品
 * @author Administrator
 *
 */
class CronSupplyGoodsTimerOn
{
    public function handle($param)
    {

        $goods_model = new Goods();
        $condition = [
            [ 'goods_id', '=', $param['relate_id'] ]
        ];
        $res = $goods_model->goodsOn($condition);
        return $res;
    }
}
