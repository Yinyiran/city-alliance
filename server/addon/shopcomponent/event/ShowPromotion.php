<?php
/**

 */

namespace addon\shopcomponent\event;

/**
 * 活动展示
 */
class ShowPromotion
{

    /**
     * 活动展示
     *
     * @return multitype:number unknown
     */
    public function handle()
    {
        $data = [
            'admin' => [
                [
                    //插件名称
                    'name'        => 'shopcomponent',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '微信视频号',
                    //展示介绍
                    'description' => '实现小程序与视频号的连接',
                    //展示图标
                    'icon'        => 'addon/shopcomponent/icon.png',
                    //跳转链接
                    'url'         => 'shopcomponent://admin/goods/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name'        => 'shopcomponent',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '微信视频号',
                    //展示介绍
                    'description' => '实现小程序与视频号的连接',
                    //展示图标
                    'icon'        => 'addon/shopcomponent/icon.png',
                    //跳转链接
                    'url'         => 'shopcomponent://shop/goods/lists',
                ]
            ]
        ];
        return $data;
    }
}