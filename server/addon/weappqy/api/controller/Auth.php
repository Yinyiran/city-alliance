<?php
/**

 */

namespace addon\weappqy\api\controller;

use addon\weappqy\model\Weapp;
use app\Controller;
use think\facade\Log;

class Auth extends Controller
{

    public $weapp;

    public function __construct()
    {
        parent::__construct();
        $this->weapp = new Weapp();
    }

    /**
     * 小程序消息推送
     */
    public function relateWeixin()
    {
        Log::write('微信小程序消息');
        $this->weapp->relateWeixin();
    }


}