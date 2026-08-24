<?php


namespace app\api\controller;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;

require_once app()->getRootPath() . 'extend/txsms/vendor/autoload.php';

/**
 * 腾讯云短信
 * Class Sms
 * @package app\api\controller
 */
class Sms
{
    //腾讯云短信
    public function txsms($mobile, $code)
    {
        $cred = new Credential('AKIDKXaR5VkoMl8WnwvsiQ2iFaeeHgvyYzDG', 'whIfpm2HOYYO1ZyABzvlRxIQkvY1GI7j');

        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("sms.tencentcloudapi.com");

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);

        $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);

        $req = new SendSmsRequest();


        $mobile = [
            $mobile
        ];
        $params = array(
            "PhoneNumberSet" => $mobile,
            "SmsSdkAppId" => "1401049441",
            "TemplateId" => "2540820", //正文模板ID
            "SignName" => "广州城盟科技",
            "TemplateParamSet" => array( $code )
        );

        $req->fromJsonString(json_encode($params));
        $resp = $client->SendSms($req);
        $resp = $resp->toJsonString();
        $res = 0;
        if ($resp) {
            $resp = json_decode($resp, true);
            if (!empty($resp['SendStatusSet'][0]['PhoneNumber'])) {
                $res = 1;
            }
        }
        return $res;
    }
}