<?php
/**

 */
return [
	'template' => [],
    'util' => [
        [
            'name' => 'WHOLESALE_LIST',
            'title' => '批发',
            'type' => 'ADDON',
            'controller' => 'Wholesale',
            'value' => '{"sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "styleName": "风格一", "changeType": 1, "backgroundColor": "", "bgSelect": "yellow", "marginTop": 0, "list": {"imageUrl": "public/static/img/diy_view/wholesale_style1_title.png","title": "批发"}, "listMore": {"imageUrl": "","title": "查看更多"}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '10000',
            'support_diy_view' => '',
            'max_count' => 1,
			'icon' => 'addon/wholesale/component/view/wholesale/img/icon/wholesale.png',
			'icon_selected' => 'addon/wholesale/component/view/wholesale/img/icon/wholesale_selected.png'
        ]
    ],
	'link' => [
        [
            'name' => 'WHOLESALE',
            'title' => '批发',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'WHOLESALE_LIST',
                    'title' => '批发列表',
                    'parent' => '',
                    'wap_url' => '/promotionpages/wholesale/list/list',
                    'web_url' => '',
                    'sort' => 0
                ],
            ]
        ]
	],
];