<?php
/**

 */

namespace addon\goodsgrab\event;

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
            'shop' => [
                [
                    //插件名称
                    'name'        => 'goodsgrab',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '商品采集',
                    //展示介绍
                    'description' => '商品采集',
                    //展示图标
                    'icon'        => 'addon/goodsgrab/icon.png',
                    //跳转链接
                    'url'         => 'goodsgrab://shop/goodsgrab/lists',
                ]
            ]

        ];
        return $data;
    }
}