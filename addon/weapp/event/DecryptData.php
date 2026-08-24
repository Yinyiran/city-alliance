<?php
/**

 */


namespace addon\weapp\event;

use addon\weapp\model\Weapp;

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
        if ($param['app_type'] == 'weapp') {
            $weapp = new Weapp();
            return $weapp->decryptData($param);
        }
    }
}