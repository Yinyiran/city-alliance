<?php
/**

 */

namespace addon\notes\event;

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
                    'name'        => 'notes',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '店铺笔记',
                    //展示介绍
                    'description' => '店铺笔记',
                    //展示图标
                    'icon'        => 'addon/notes/icon.png',
                    //跳转链接
                    'url'         => 'notes://admin/notes/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name'        => 'notes',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '店铺笔记',
                    //展示介绍
                    'description' => '店铺笔记',
                    //展示图标
                    'icon'        => 'addon/notes/icon.png',
                    //跳转链接
                    'url'         => 'notes://shop/notes/lists',
                ]
            ]

        ];
        return $data;
    }
}