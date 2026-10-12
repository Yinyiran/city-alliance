<?php
/**

 */

namespace addon\memberregister\model;

use addon\platformcoupon\model\PlatformcouponType;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 会员注册
 */
class Register extends BaseModel
{
	/**
	 * 会员注册奖励设置
	 * array $data
	 */
	public function setConfig($data, $is_use)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '会员注册奖励设置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'MEMBER_REGISTER_REWARD_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 会员注册奖励设置
	 */
    public function getConfig()
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'MEMBER_REGISTER_REWARD_CONFIG']]);

        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'point' => 0,
                'balance' => 0,
                'growth' => 0,
                'coupon' => 0,
            ];
        }
        //读取优惠券列表
        if (!isset($res['data']['value']['coupon'])) $res['data']['value']['coupon'] = 0;
        $coupon_list = [];
        if($res['data']['value']['coupon'] != 0 && $res['data']['value']['coupon'] != '') {
            $coupon = new PlatformcouponType();
            $coupon_list = $coupon->getPlatformcouponTypeList([ ['status','=',1],['platformcoupon_type_id','in',$res['data']['value']['coupon']] ]);

            $coupon_list = $coupon_list['data'];

        }
        $res['data']['value']['coupon_list'] = $coupon_list;

        return $res;
    }
	
	public function memberRegister()
	{
	
	}
	
}