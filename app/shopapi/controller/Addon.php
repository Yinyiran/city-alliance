<?php
/**

 */

namespace app\shopapi\controller;

use app\model\system\Addon as AddonModel;

/**
 * 插件管理
 * @author Administrator
 *
 */
class Addon extends BaseApi
{

    /**
     * 列表信息
     */
    public function lists()
    {
        $addon = new AddonModel();
        $list = $addon->getAddonList();
        return $this->response($list);
    }

    public function addonisexit()
    {
        $res = [];
        $res[ 'city' ] = addon_is_exit('city');// 城市分站

        return $this->response($this->success($res));
    }

    /**
     * 插件是否存在
     */
    public function isexit()
    {
        $name = $this->params[ 'name' ] ?? '';
        $res = 0;
        if (!empty($name)) $res = addon_is_exit($name);
        return $this->response($this->success($res));
    }

}