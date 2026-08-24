<?php
/**

 */

namespace app\model\web;

use app\model\BaseModel;


/**
 * 网站系统性设置
 */
class Link extends BaseModel
{

    public function getLinkDict()
    {
        $dict_array =
            [
                'shop' => [
                    'ALL_GOODS' => [
                        'wap_url' => '/pages/goods/detail/detail?sku_id=',
                        'promotion' => 'all',
                        'select_id' => 'sku_id'
                    ],
                    'PINTUAN_GOODS' => [
                        'wap_url' => '/promotionpages/pintuan/detail/detail?pintuan_id=',
                        'goodsColsParameter' => [
                            'price' => 'pintuan_price'
                        ],
                        'promotion' => 'pintuan',
                        'select_id' => 'pintuan_id'
                    ],
                    'GROUPBUY_GOODS' => [
                        'wap_url' => '/promotionpages/groupbuy/detail/detail?groupbuy_id=',
                        'goodsColsParameter' => [
                            'price' => 'groupbuy_price'
                        ],
                        'promotion' => 'groupbuy',
                        'select_id' => 'groupbuy_id'
                    ],
                    'DISTRIBUTION_GOODS' => [
                        'wap_url' => '/pages/goods/detail/detail?sku_id=',
                        'promotion' => 'distribution',
                        'select_id' => 'sku_id'
                    ],
                    'BARGAIN_GOODS' => [
                        'wap_url' => '/promotionpages/bargain/detail/detail?bargain_id=',
                        'promotion' => 'bargain',
                        'select_id' => 'bargain_id'
                    ],
                    'PRESALE_GOODS' => [
                        'wap_url' => '/promotionpages/presale/detail/detail?id=',
                        'promotion' => 'presale',
                        'select_id' => 'presale_id'
                    ],
                    'CARDS_GAME' => [
                        'wap_url' => '/promotionpages/game/cards/cards?id=',
                        'url' => addon_url("cards://shop/cards/lists"),
                        'goodsCols' => [
                            [
                                [
                                    'unresize' => 'false',
                                    'width' => '8%',
                                    'templet' => '#checkbox'
                                ],
                                [
                                    'field' => 'game_name',
                                    'title' => '标题',
                                    'unresize' => 'false',
                                    'width' => '62%',
                                ],
                                [
                                    'field' => 'price',
                                    'title' => '状态',
                                    'unresize' => 'false',
                                    'width' => '30%',
                                    'templet' => '#game_status'
                                ]
                            ]
                        ],
                        'select_id' => 'game_id'
                    ],
                    'TURNTABLE_GAME' => [
                        'wap_url' => '/promotionpages/game/turntable/turntable?id=',
                        'url' => addon_url("turntable://shop/turntable/lists"),
                        'goodsCols' => [
                            [
                                [
                                    'unresize' => 'false',
                                    'width' => '8%',
                                    'templet' => '#checkbox'
                                ],
                                [
                                    'field' => 'game_name',
                                    'title' => '标题',
                                    'unresize' => 'false',
                                    'width' => '62%',
                                ],
                                [
                                    'field' => 'price',
                                    'title' => '状态',
                                    'unresize' => 'false',
                                    'width' => '30%',
                                    'templet' => '#game_status'
                                ]
                            ]
                        ],
                        'select_id' => 'game_id'
                    ],
                    'EGG_GAME' => [
                        'wap_url' => '/promotionpages/game/smash_eggs/smash_eggs?id=',
                        'url' => addon_url("egg://shop/egg/lists"),
                        'goodsCols' => [
                            [
                                [
                                    'unresize' => 'false',
                                    'width' => '8%',
                                    'templet' => '#checkbox'
                                ],
                                [
                                    'field' => 'game_name',
                                    'title' => '标题',
                                    'unresize' => 'false',
                                    'width' => '62%',
                                ],
                                [
                                    'field' => 'price',
                                    'title' => '状态',
                                    'unresize' => 'false',
                                    'width' => '30%',
                                    'templet' => '#game_status'
                                ]
                            ]
                        ],
                        'select_id' => 'game_id'
                    ]
                ],
                'admin' => [
                    'ALL_GOODS' => [
                        'wap_url' => '/pages/goods/detail/detail?sku_id=',
                        'promotion' => 'all',
                        'select_id' => 'sku_id'
                    ],
                    'PINTUAN_GOODS' => [
                        'wap_url' => '/promotionpages/pintuan/detail/detail?pintuan_id=',
                        'goodsColsParameter' => [
                            'price' => 'pintuan_price'
                        ],
                        'promotion' => 'pintuan',
                        'select_id' => 'pintuan_id'
                    ],
                    'GROUPBUY_GOODS' => [
                        'wap_url' => '/promotionpages/groupbuy/detail/detail?groupbuy_id=',
                        'goodsColsParameter' => [
                            'price' => 'groupbuy_price'
                        ],
                        'promotion' => 'groupbuy',
                        'select_id' => 'groupbuy_id'
                    ],
                    'DISTRIBUTION_GOODS' => [
                        'wap_url' => '/pages/goods/detail/detail?sku_id=',
                        'promotion' => 'distribution',
                        'select_id' => 'sku_id'
                    ],
                    'BARGAIN_GOODS' => [
                        'wap_url' => '/ppromotionpages/bargain/detail/detail?bargain_id=',
                        'promotion' => 'bargain',
                        'select_id' => 'bargain_id'
                    ],
                    'PRESALE_GOODS' => [
                        'wap_url' => '/promotionpages/presale/detail/detail?id=',
                        'promotion' => 'presale',
                        'select_id' => 'presale_id'
                    ],
                    'CARDS_GAME' => [
                        'wap_url' => '/promotionpages/game/cards/cards?id=',
                        'url' => addon_url("cards://admin/cards/lists"),
                        'goodsCols' => [
                            [
                                [
                                    'unresize' => 'false',
                                    'width' => '8%',
                                    'templet' => '#checkbox'
                                ],
                                [
                                    'field' => 'game_name',
                                    'title' => '标题',
                                    'unresize' => 'false',
                                    'width' => '62%',
                                ],
                                [
                                    'field' => 'price',
                                    'title' => '状态',
                                    'unresize' => 'false',
                                    'width' => '30%',
                                    'templet' => '#game_status'
                                ]
                            ]
                        ],
                        'select_id' => 'game_id'
                    ],
                    'TURNTABLE_GAME' => [
                        'wap_url' => '/promotionpages/game/turntable/turntable?id=',
                        'url' => addon_url("turntable://admin/turntable/lists"),
                        'goodsCols' => [
                            [
                                [
                                    'unresize' => 'false',
                                    'width' => '8%',
                                    'templet' => '#checkbox'
                                ],
                                [
                                    'field' => 'game_name',
                                    'title' => '标题',
                                    'unresize' => 'false',
                                    'width' => '62%',
                                ],
                                [
                                    'field' => 'price',
                                    'title' => '状态',
                                    'unresize' => 'false',
                                    'width' => '30%',
                                    'templet' => '#game_status'
                                ]
                            ]
                        ],
                        'select_id' => 'game_id'
                    ],
                    'EGG_GAME' => [
                        'wap_url' => '/promotionpages/game/smash_eggs/smash_eggs?id=',
                        'url' => addon_url("egg://admin/egg/lists"),
                        'goodsCols' => [
                            [
                                [
                                    'unresize' => 'false',
                                    'width' => '8%',
                                    'templet' => '#checkbox'
                                ],
                                [
                                    'field' => 'game_name',
                                    'title' => '标题',
                                    'unresize' => 'false',
                                    'width' => '62%',
                                ],
                                [
                                    'field' => 'price',
                                    'title' => '状态',
                                    'unresize' => 'false',
                                    'width' => '30%',
                                    'templet' => '#game_status'
                                ]
                            ]
                        ],
                        'select_id' => 'game_id'
                    ]
                ]
            ];
        $temp_link = event('LinkDict', []);
        if (!empty($temp_link)) {
            foreach ($temp_link as $k => $v) {
                $dict_array = array_merge($dict_array, $v);
            }
        }
        return $dict_array;
    }
}