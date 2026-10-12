<?php

/**

 */

namespace addon\servicer\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\servicer\model\Config as ConfigModel;

/**
 * 客服配置
 */
class Config extends BaseAdmin
{
    /**
     * 客服配置
     */
    public function config()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $web_socket = input("web_socket");

            $data = [
                'web_socket' => $web_socket,//websocket链接
            ];
            $this->addLog("设置客服配置");
            $res = $config_model->setConfig($data);
            return $res;
        } else {
            $config_result = $config_model->getConfig()[ 'data' ] ?? [];
            $this->assign('config', $config_result[ 'value' ] ?? '');
            return $this->fetch('config/config');
        }
    }

}
