<?php
/**

 */

namespace addon\shopcomponent\event;

use addon\shopcomponent\model\Category;

/**
 * 同步微信类目
 */
class SyncWxCategory
{
    public function handle($data)
    {
        (new Category())->syncCategory($data['relate_id']);
    }
}