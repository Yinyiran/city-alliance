<?php
/**

 */
return [
    'template' => [],
    'util' => [
        [
            'name' => 'PRESALE_LIST',
            'title' => '预售',
            'type' => 'ADDON',
            'controller' => 'Presale',
            'value' => '{"sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "changeType": 1, "styleName": "风格一", "backgroundColor": "", "bgSelect": "blue", "marginTop": 0, "list": {"imageUrl": "public/static/img/diy_view/presale_style1_title.png", "title": "预售专区"}, "listMore": {"imageUrl": "","title": ""}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '12003',
            'support_diy_view' => '',
            'max_count' => 0,
            'icon' => 'addon/presale/component/view/presale/img/icon/presale.png',
            'icon_selected' => 'addon/presale/component/view/presale/img/icon/presale_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'GOODS_BOOKING',
            'title' => '商品预售',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'PRESALE_PREFECTURE',
                    'title' => '预售专区',
                    'parent' => '',
                    'wap_url' => '/promotionpages/presale/list/list',
                    'web_url' => '',
                    'sort' => 1
                ],
                [
                    'name' => 'MAPRESALE_PREFECTURE',
                    'title' => '我的预售',
                    'parent' => '',
                    'wap_url' => '/promotionpages/presale/order_list/order_list',
                    'web_url' => '',
                    'sort' => 2
                ]
            ]
        ],
        [
            'name' => 'PRESALE_GOODS',
            'title' => '预售商品',
            'parent' => 'COMMODITY',
            'wap_url' => '',
            'web_url' => '',
            'child_list' => []
        ]
    ],
];