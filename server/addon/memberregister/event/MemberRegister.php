<?php
/**

 */


namespace addon\memberregister\event;

use addon\memberregister\model\Register as RegisterModel;
use addon\platformcoupon\model\MemberPlatformcoupon;
use addon\platformcoupon\model\Platformcoupon as PlatformcouponModel;
use app\model\member\MemberAccount as MemberAccountModel;

/**
 * 会员注册奖励
 */
class MemberRegister
{
	/**
	 * @param $param
	 * @return array|\multitype
	 */
	public function handle($param)
	{
		$register_model = new RegisterModel();
		$member_account_model = new MemberAccountModel();
		
		$register_config = $register_model->getConfig();
		$register_config = $register_config['data'];
		
		$res = [];
		if ($register_config['is_use']) {
			$register_config = $register_config['value'];
			foreach ($register_config as $k => $v) {
				if (!empty($v)) {
				    if($k != 'coupon' && $k != 'coupon_list'){
                        $adjust_num = $v;
                        $account_type = $k;
                        $remark = '注册送' . $adjust_num . $this->accountType($k);
                        $res = $member_account_model->addMemberAccount($param['member_id'], $account_type, $adjust_num, 'register', 0, $remark);
                    }else if($k == 'coupon'){
                        $member_id = $param['member_id'];
                        $platformcoupon_type_ids = $v;
                        $get_type = 5;
                        $platformcoupon_type_ids = explode(',',$platformcoupon_type_ids);
                        $memberplatformcoupon_model = new MemberPlatformcoupon();
                        $res = $memberplatformcoupon_model->sendPlatformcoupon($platformcoupon_type_ids, $member_id, $get_type,0,0);
                    }
				}
			}
		}
		
		return $res;
		
	}
	
	private function accountType($key)
	{
		$type = [
			'point' => '积分',
			'balance' => '余额',
			'growth' => '成长值',
			'coupon' => '优惠券'
		];
		return $type[ $key ];
	}
	
}