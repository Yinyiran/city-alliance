<?php
/**

 */
return [

    'template' => [],
    'util' => [
        [
            'name' => 'SHOP_COUPON',
            'title' => '店铺优惠券',
            'type' => 'ADDON',
            'controller' => 'ShopCoupon',
            'value' => '{ "sources": "default" ,"style": 1, "couponCount" : "6", "styleName": "风格一", "backgroundColor": "", "marginTop": 0, "status": 1, "couponIds": [] }',
            'sort' => '12006',
            'support_diy_view' => 'DIY_VIEW_SHOP_INDEX',
            'max_count' => 0,
            'icon' => 'addon/coupon/component/view/shop_coupon/img/icon/coupon.png',
            'icon_selected' => 'addon/coupon/component/view/shop_coupon/img/icon/coupon_selected.png'
        ],
    ],
    'link' => [],
];