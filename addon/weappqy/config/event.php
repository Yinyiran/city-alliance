<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        // 生成获取二维码
        'Qrcode' => [
            'addon\weappqy\event\Qrcode'
        ],
        // 开放数据解密
        'DecryptData' => [
            'addon\weappqy\event\DecryptData'
        ],
        'WeappMenu' => [
            'addon\weappqy\event\WeappMenu',
        ],
    ],

    'subscribe' => [
    ],
];
