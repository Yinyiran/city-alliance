<?php
/**

 */

namespace addon\membersignin\admin\controller;

use addon\membersignin\model\Signin;
use app\admin\controller\BaseAdmin;

/**
 * 会员签到
 */
class Config extends BaseAdmin
{

    public function index()
    {
        $config_model = new Signin();
        if (request()->isAjax()) {
            $data   = input("json", "{}");
            $cycle   = input("cycle", 30);//签到周期
            $is_use = input("is_use", 0);//是否启用
            $return_coupon = input('coupon', '');//优惠券
            $data   = json_decode($data,true);
            $res    = $config_model->setConfig(['cycle' => $cycle,'return_coupon' => $return_coupon, 'reward' => $data], $is_use);
            $this->addLog("设置会员签到奖励");
            return $res;
        } else {
            $config_result = $config_model->getConfig();
            $this->assign('config', $config_result['data']);
            return $this->fetch('config/index');
        }
    }


	
}