<?php
/**

 */
return [
    'template' => [],
    'util' => [
        [
            'name' => 'ADMIN_PLATFORM_COUPON',
            'title' => '平台优惠券',
            'type' => 'ADDON',
            'controller' => 'AdminPlatformCoupon',
            'value' => '{ "sources": "default" ,"style": 1, "couponCount" : "6", "styleName": "风格一", "backgroundColor": "", "marginTop": 0, "status": 1, "couponIds": [] }',
            'sort' => '12009',
            'support_diy_view' => 'DIY_VIEW_INDEX',
            'max_count' => 0,
            'icon' => 'addon/platformcoupon/component/view/admin_coupon/img/icon/coupon.png',
            'icon_selected' => 'addon/platformcoupon/component/view/admin_coupon/img/icon/coupon_selected.png'
        ],
    ],
    'link' => [
        [
            'name' => 'COUPON_LIST',
            'title' => '优惠券',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'COUPON_PREFECTURE',
                    'title' => '优惠券专区',
                    'parent' => '',
                    'wap_url' => '/otherpages/goods/coupon/coupon',
                    'web_url' => '',
                    'sort' => 0
                ]
            ]
        ]
    ],

];
