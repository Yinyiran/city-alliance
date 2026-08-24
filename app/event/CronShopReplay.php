<?php
/**

 */


namespace app\event;

use app\model\shop\ShopReplay;

/**
 * 店铺续签通过之后任务事件
 */
class CronShopReplay
{

    public function handle($data)
    {
        $shop_replay = new ShopReplay();
        $res = $shop_replay->cronShopReplay($data[ 'relate_id' ]);
        return $res;
    }

}