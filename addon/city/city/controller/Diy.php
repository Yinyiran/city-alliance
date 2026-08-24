<?php
/**

 */

namespace addon\city\city\controller;

use app\model\web\DiyView as DiyViewModel;

/**
 * 网站装修控制器
 */
class Diy extends BaseCity
{
    /**
     * 网站主页
     */
    public function index()
    {
        $diy_view = new DiyViewModel();
        $page = $diy_view->getPage();
        if (request()->isAjax()) {
            $res = 0;
            $data = array ();
            $id = input("id", 0);
            $name = input("name", "");
            $title = input("title", "");
            $value = input("value", "");
            if (!empty($name) && !empty($title) && !empty($value)) {
                $data[ 'site_id' ] = $this->site_id;
                $data[ 'name' ] = $name;
                $data[ 'title' ] = $title;
                $data[ 'type' ] = $page[ $this->app_module ][ 'port' ];
                $data[ 'value' ] = $value;
                if ($id == 0) {
                    $data[ 'create_time' ] = time();
                    $res = $diy_view->addSiteDiyView($data);
                } else {
                    $data[ 'update_time' ] = time();
                    $res = $diy_view->editSiteDiyView($data, [ [ 'id', '=', $id ] ]);
                }
            }

            return $res;
        } else {
            // 查询公共组件和支持的页面
            $condition = [
                [ 'support_diy_view', 'like', [ $page[ $this->app_module ][ 'index' ][ 'name' ], '%' . $page[ $this->app_module ][ 'index' ][ 'name' ] . ',%', '%' . $page[ $this->app_module ][ 'index' ][ 'name' ], '%,' . $page[ $this->app_module ][ 'index' ][ 'name' ] . ',%', '' ], 'or' ]
            ];
//            var_dump($condition);
            $data = [
                'app_module' => $this->app_module,
                'site_id' => $this->site_id,
                'name' => $page[ $this->app_module ][ 'index' ][ 'name' ],
                'condition' => $condition,
                'addon_name'=>$this->app_module
            ];
            $edit_view = event('DiyViewEdit', $data, true);
            return $edit_view;
        }
    }

}