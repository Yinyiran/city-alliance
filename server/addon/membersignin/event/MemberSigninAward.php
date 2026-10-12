<?php
/**

 */


namespace addon\membersignin\event;

use addon\membersignin\model\Signin as SigninModel;

/**
 * 会员签到奖励规则
 */
class MemberSigninAward
{
	/**
	 * 会员操作
	 */
	public function handle()
	{
		$signin_model = new SigninModel();
		$config_result = $signin_model->getConfig();
		$config_result = $config_result['data'];
		if ($config_result['is_use']) {
			$config_result = $config_result['value'];
			return $config_result;
		}
		return [];
	}
}