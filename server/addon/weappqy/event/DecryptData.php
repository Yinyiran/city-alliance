<?php
/**

 */


namespace addon\weappqy\event;

use addon\weappqy\model\Weapp;

/**
 * 开放数据解密
 */
class DecryptData
{
    /**
     * 执行安装
     * @param array $param
     * @return array
     */
    public function handle($param = [])
    {
        if ($param['app_type'] == 'weappqy') {
            $weapp = new Weapp();
            return $weapp->decryptData($param);
        }
    }
}