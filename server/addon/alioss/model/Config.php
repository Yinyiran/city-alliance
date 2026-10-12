<?php
/**

 */

namespace addon\alioss\model;

use app\model\BaseModel;
use app\model\system\Config as ConfigModel;

/**
 * 阿里云配置
 */
class Config extends BaseModel
{
    /**
     * 设置阿里云OSS上传配置
     * array $data
     */
    public function setAliossConfig($data, $status)
    {
        if ($status == 1) {
            event("CloseOss", []);//同步关闭所有云上传
        }

        $config = new ConfigModel();
        $res = $config->setConfig($data, '阿里云OSS上传配置', $status, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'ALIOSS_CONFIG' ] ]);
        return $res;
    }

    /**
     * 获取阿里云上传配置
     */
    public function getAliossConfig()
    {
        $config = new ConfigModel();
        $res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'ALIOSS_CONFIG' ] ]);
        return $res;
    }

    /**
     * 配置阿里云开关状态
     * @param $status
     */
    public function modifyConfigIsUse($status)
    {
        $config = new ConfigModel();
        $res = $config->modifyConfigIsUse($status, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'ALIOSS_CONFIG' ] ]);
        return $res;
    }
}