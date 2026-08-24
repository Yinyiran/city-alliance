<?php
/**

 */


namespace addon\memberregister\event;

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
                    'name' => 'memberregister',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'member',
                    //展示主题
                    'title' => '新人礼',
                    //展示介绍
                    'description' => '会员注册后奖励设置',
                    //展示图标
                    'icon' => 'addon/memberregister/icon.png',
                    //跳转链接
                    'url' => 'memberregister://admin/config/index',
                ]
            ],
            'shop' => [
            ]

        ];
	    return $data;
	}
}