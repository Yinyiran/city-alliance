<?php
/**

 */

namespace addon\niusms\event;

use addon\niusms\model\Config;

/**
 * 获取短信模板数据
 */
class SmsTemplateInfo
{
    /**
     * 获取短信模板数据
     */
    public function handle($param)
    {
        $config_model = new Config();
        $sms_config = $config_model->getSmsConfig();
        $sms_config = $sms_config[ 'data' ];
        if ($sms_config['is_use']) {

            $template_info = model('sms_template')->getInfo([ ['keywords', '=', $param['keywords'] ]]);
            return $template_info;
        }
    }
}