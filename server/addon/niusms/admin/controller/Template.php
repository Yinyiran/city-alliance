<?php
/**

 */

namespace addon\niusms\admin\controller;

use addon\niusms\model\Config as ConfigModel;
use app\admin\controller\BaseAdmin;
use addon\niusms\model\Sms as SmsModel;

class Template extends BaseAdmin
{

    /**
     * 启用短信模板
     */
    public function enableTemplate()
    {
        if (request()->isAjax()) {
            //获取账户名
            $config_model = new ConfigModel();
            $sms_config = $config_model->getSmsConfig();
            $sms_model = new SmsModel();
            $sms_config = $sms_config[ 'data' ][ 'value' ];

            $template_id = input('template_id', '');
            $status = input('status', '');

            $res = $sms_model->enableTemplate($template_id, $status, $sms_config);
            return $res;
        }

    }

    /**
     * 关闭短信模板
     * @return array|mixed
     */
    public function disableTemplate()
    {
        if (request()->isAjax()) {
            //获取账户名
            $config_model = new ConfigModel();
            $sms_config = $config_model->getSmsConfig($this->app_module);
            $sms_model = new SmsModel();
            $sms_config = $sms_config[ 'data' ][ 'value' ];

            $template_ids = input('template_ids', '');
            $status = input('status', '');

            $res = $sms_model->disableTemplate($template_ids, $status, $sms_config);
            return $res;
        }

    }

}