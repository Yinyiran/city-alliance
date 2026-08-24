<?php
/**

 */


namespace addon\presale\event;

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
                    'name' => 'presale',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '商品预售',
                    //展示介绍
                    'description' => '商品预售',
                    //展示图标
                    'icon' => 'addon/presale/icon.png',
                    //跳转链接
                    'url' => 'presale://admin/presale/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'presale',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '商品预售',
                    //展示介绍
                    'description' => '商品预售',
                    //展示图标
                    'icon' => 'addon/presale/icon.png',
                    //跳转链接
                    'url' => 'presale://shop/presale/lists',
                ]
            ]

        ];
	    return $data;
	}
}