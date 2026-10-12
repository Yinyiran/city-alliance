<?php
/**

 */

namespace addon\supply\event;

use addon\supply\model\goods\Goods;

/**
 * 定时下架商品
 * @author Administrator
 *
 */
class CronSupplyGoodsTimerOff
{
    public function handle($param)
    {

        $goods_model = new Goods();
        $condition = [
            [ 'goods_id', '=', $param['relate_id'] ]
        ];
        $res = $goods_model->goodsOff($condition);
        return $res;
    }
}
