<?php
/**

 * =========================================================
 */

namespace app\shopapplet\controller;

use app\model\member\Member as MemberModel;
use app\model\system\Api;
use extend\RSA;
use think\facade\Cache;
use app\model\shop\Config;

class BaseInfo extends BaseApi
{
    public function getMiniInfo(){
      //企业端的用户协议和隐私政策
      $logo="";
      $title="";
      $config_model = new Config();
      $yinsi=$config_model->getPrivacyqy();
      $xieyi=$config_model->getVipqy();
      $result=[
        "logo"=>$logo,
        "title"=>$title,
        "xieyi"=>$xieyi["data"]['content'],
        "yinsi"=>$yinsi["data"]['content']
      ];
      return $this->response($result);
    }
}