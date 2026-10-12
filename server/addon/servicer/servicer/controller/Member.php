<?php

/**

 */

namespace addon\servicer\servicer\controller;

use app\model\member\Member as MemberModel;
use think\facade\Config;

/**
 * 会员相关
 * Class Member
 * @package addon\servicer\servicer\controller
 */
class Member extends BaseServicer
{
    /**
     * 会员信息
     */
    public function info()
    {
        if (request()->isAjax()) {
            $field = 'nickname,mobile,headimg,member_level_name,balance,balance_money,login_type,login_type_name';
            $member_model = new MemberModel();
            $res = $member_model->getMemberInfo([['member_id', '=', input('member_id')]], $field);
            if (!empty($res['data'])) {
                $res['data']['total_balance'] = $res['data']['balance'] + $res['data']['balance_money'];
            }
            return $res;
        }
    }
}