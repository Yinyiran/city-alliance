<?php
/**

 */

namespace addon\niusms\event;

use addon\niusms\model\Config;

/**
 * 短信方式  (后台调用)
 */
class SmsType
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle($param)
    {
        $info = array(
            "sms_type"      => "niusms",
            "sms_type_name" => "牛云短信",
            "edit_url"      => "niusms://admin/sms/index",
            "desc"          => "专业化系统化短信，与商城系统紧密相连，不需要专门配置短信模板即可使用。"
        );

        $config_model   = new Config();
        $config         = $config_model->getSmsConfig();
        $info['status'] = $config['data']['is_use'] ?? 0;
        return $info;
    }
}