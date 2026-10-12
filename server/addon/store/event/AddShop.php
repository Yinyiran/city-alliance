<?php
/**

 */


namespace addon\store\event;

use addon\store\model\Config;

/**
 *添加店铺门店结算周期
 */
class Addshop
{

    public function handle($data)
    {
        $config = new Config();
        $res = $config->addSettlementCron($data[ 'site_id' ]);
        return $res;
    }
}