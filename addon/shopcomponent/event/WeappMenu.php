<?php
/**

 */

namespace addon\shopcomponent\event;

/**
 * 小程序菜单
 */
class WeappMenu
{

    /**
     * 小程序菜单
     *
     * @return multitype:number unknown
     */
    public function handle()
    {
        $data = [
            'title'       => '微信视频号',
            'description' => '实现小程序与视频号的连接',
            'url'         => 'shopcomponent://admin/goods/lists',
            'icon'        => 'addon/shopcomponent/icon.png'
        ];
        return $data;
    }
}