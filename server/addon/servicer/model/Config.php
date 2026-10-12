<?php

/**

 */

namespace addon\servicer\model;

use app\model\BaseModel;
use app\model\system\Config as ConfigModel;

class Config extends BaseModel
{
    /**
     * 客服配置
     * array $data
     */
    public function setConfig($data)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '客服设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SERVICER_CONFIG' ] ]);
        return $res;
    }

    /**
     * 客服配置
     */
    public function getConfig()
    {
        $config = new ConfigModel();
        $res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SERVICER_CONFIG' ] ]);
        if (empty($res['data']['value'])) {
            $socket_url = (get_http_type() === 'http' ? str_replace('http', 'ws', __ROOT__) : str_replace('https', 'wss', __ROOT__)) . '/wss';
            $res['data']['value'] = [
                // 客服类型
                'web_socket'   => $socket_url
            ];
        }
        return $res;
    }
}
