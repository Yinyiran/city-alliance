<?php
/**

 */

namespace app\shopapi\controller;

use app\model\web\Config as ConfigModel;

class Config extends BaseApi
{
    /**
     * 详情信息
     */
    public function defaultimg()
    {
        $upload_config_model = new ConfigModel();
        $res = $upload_config_model->getDefaultImg();
        if (!empty($res[ 'data' ][ 'value' ])) {
            return $this->response($this->success($res[ 'data' ][ 'value' ]));
        } else {
            return $this->response($this->error());
        }
    }

    /**
     * 版权信息
     */
    public function copyright()
    {
        $config_model = new ConfigModel();
        $res = $config_model->getCopyright();
        return $this->response($this->success($res[ 'data' ][ 'value' ]));
    }

    /**
     * 查询验证码设置
     * @return false|string
     */
    public function captchaConfig()
    {
        $config_model = new ConfigModel();
        $config_info = $config_model->getCaptchaConfig();
        return $this->response($this->success($config_info[ 'data' ][ 'value' ]));
    }


}