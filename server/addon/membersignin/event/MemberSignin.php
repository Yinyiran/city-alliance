<?php
/**

 */

namespace addon\membersignin\event;

use addon\platformcoupon\model\MemberPlatformcoupon;
use app\model\member\Member as MemberModel;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\member\MemberSignin as MemberSigninModel;

/**
 * 会员签到奖励
 */
class MemberSignin
{
    /**
     * @param $param
     * @return string|\multitype
     */
    public function handle($param)
    {
        $member_model = new MemberModel();
        $member_signin_model = new MemberSigninModel();
        $member_account_model = new MemberAccountModel();

        // 查询当前用户连签天数
        $member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $param[ 'member_id' ] ] ], 'sign_days_series');
        $member_info = $member_info[ 'data' ];

        $award = $member_signin_model->getAward();
        $award = $award[ 'data' ] ?? [];
        $return_coupon = $award[ 'return_coupon' ] ?? [];
        $award = $award[ 'reward' ] ?? [];

        $res = [];
        //签到送优惠券
        if(!empty($return_coupon))
        {
            $member_id = $param['member_id'];
            $platformcoupon_type_ids = $return_coupon;
            $get_type = 5;
            $platformcoupon_type_ids = explode(',',$platformcoupon_type_ids);
            $memberplatformcoupon_model = new MemberPlatformcoupon();
            $res = $memberplatformcoupon_model->sendPlatformcoupon($platformcoupon_type_ids, $member_id, $get_type, 0,0);
        }
        //签到送积分成长值
        if (!empty($award)) {

            foreach ($award as $k => $v) {
                if ($member_info[ 'sign_days_series' ] == $v[ 'day' ]) {
                    $res = $v;
                    break;
                }
            }
            if (empty($res)) {
                $res = array_pop($award);
            }

            foreach ($res as $curr_k => $curr_v) {
                if ($curr_k != 'day') {
                    $adjust_num = $curr_v;
                    $account_type = $curr_k;
                    $remark = '签到送' . $adjust_num . $this->accountType($curr_k);
                    $member_account_model->addMemberAccount($param[ 'member_id' ], $account_type, $adjust_num, 'signin', 0, $remark);
                }
            }

        }
        return $res;
    }

    private function accountType($key)
    {
        $type = [
            'point' => '积分',
            'growth' => '成长值',
            'coupon' => '优惠券'
        ];
        return $type[ $key ];
    }

}