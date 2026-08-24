<?php
/**

 */

namespace addon\store\event;

use app\model\store\Store;

/**
 * 删除门店
 */
class DeleteStore
{

    public function handle($data)
    {
        $store_model = new Store();
        $res = $store_model->cronDeleteStore($data['store_id']);
        return $res;
    }
}