<?php
/**

 */

namespace addon\membersignin\api\controller;

use app\api\controller\BaseApi;
use addon\membersignin\model\Signin as SigninModel;

/**
 * 会员签到
 */
class Signin extends BaseApi
{
	
	/**
	 * 配置信息
	 */
	public function config()
	{
		$signin_model = new SigninModel();
        $result = $signin_model->getConfig();

		return $this->response($result);
	}

    /**
     * 获取签到是否开启
     */
    public function getSignStatus()
    {
        $config_model = new SigninModel();
        $config_result = $config_model->getConfig();
        return $this->response($config_result);
    }
	
}