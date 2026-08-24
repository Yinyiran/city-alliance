<?php
/**

 */

namespace addon\city\model;

use app\model\BaseModel;

/**
 * 菜单管理
 * @author Administrator
 *
 */
class Menu extends BaseModel
{
    public function uninstall()
    {
        return $this->success();
    }
}