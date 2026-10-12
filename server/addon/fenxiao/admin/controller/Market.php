<?php
/**

 */

namespace addon\fenxiao\admin\controller;

use app\admin\controller\BaseAdmin;

/**
 * 分销市场
 */
class Market extends BaseAdmin
{
    /**
     * 分销市场
     */
    public function index()
    {
        // 查询公共组件和支持的页面
        $condition = [
            [ 'support_diy_view', 'like', [ 'DIY_FENXIAO_MARKET', '%' . 'DIY_FENXIAO_MARKET' . ',%', '%' . 'DIY_FENXIAO_MARKET', '%,' . 'DIY_FENXIAO_MARKET' . ',%', '' ], 'or' ],
            [ 'addon_name', 'not in', [ 'seckill', 'pintuan', 'groupbuy', 'live', 'bargain', 'wholesale' ], 'or' ],
        ];
        $data = [
            'app_module' => 'admin',
            'site_id' => $this->site_id,
            'name' => 'DIY_FENXIAO_MARKET',
            'condition' => $condition
        ];
        $edit_view = event('DiyViewEdit', $data, true);
        return $edit_view;
    }

}