<?php
/**

 */


namespace addon\alioss\event;

/**
 * 云上传方式
 */
class OssType
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle()
    {
        $info = array (
            "sms_type" => "alioss",
            "sms_type_name" => "阿里云上传",
            "edit_url" => "alioss://admin/config/config",
            "desc" => "阿里云上传"
        );
        return $info;
    }
}