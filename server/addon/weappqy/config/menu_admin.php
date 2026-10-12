<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'WEAPPQY_ROOT',
        'title' => '微信小程序(商户端)',
        'url' => 'weappqy://admin/weapp/setting',
        'parent' => 'CHANNEL_ROOT',
        'picture_select' => '',
        'picture' => 'addon/weappqy/admin/view/public/img/menu_icon/wechat_app.png',
        'is_show' => 1,
        'sort' => 2,
        'child_list' => [
            [
                'name' => 'WEAPP_SETTING',
                'title' => '小程序设置(商户端)',
                'url' => 'weappqy://admin/weapp/setting',
                'is_show' => 1,
                'picture' => '',
                'picture_select' => '',
                'sort' => 0,
                'child_list' => [
                    [
                         
                        'name' => 'WEAPPQY_CONFIG',
                        'title' => '小程序配置(商户端)',
                        'url' => 'weappqy://admin/weapp/config',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WEAPP_RELEASE',
                        'title' => '源码发布',
                        'url' => 'weappqy://admin/weapp/release',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WEAPP_PACKAGE',
                        'title' => '小程序包管理',
                        'url' => 'weappqy://admin/weapp/package',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WEAPP_MESSAGE',
                        'title' => '订阅消息',
                        'url' => 'weappqy://admin/message/config',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WEAPP_EDIT',
                        'title' => '修改图文',
                        'url' => 'weappqy://admin/message/edit',
                        'is_show' => 0
                    ],
                ]
            ],
            /* [
                'name' => 'WEAPP_STAT',
                'title' => '访问统计',
                'url' => 'weappqy://admin/stat/stat',
                'picture_select' => '',
                'picture' => '',
                'is_show' => 1,
                'sort' => 1,
            ], */
        ]
    ]
];
