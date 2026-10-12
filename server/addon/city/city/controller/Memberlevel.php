<?php


namespace addon\city\city\controller;

use addon\platformcoupon\model\PlatformcouponType;
use app\model\member\MemberLevel as MemberLevelModel;

/**
 * 会员等级
 * Class Memberlevel
 * @package addon\city\city\controller
 */
class Memberlevel extends BaseCity
{
    /**
     * 会员等级列表
     */
    public function levelList()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [];
            $condition[] = [ 'level_name', 'like', "%" . $search_text . "%" ];
            $order = 'growth asc';
            $field = '*';

            $member_level_model = new MemberLevelModel();
            $list = $member_level_model->getMemberLevelPageList($condition, $page, $page_size, $order, $field);
            foreach ($list['data']['list'] as $key => $value) {
                //城市的参数
                $condition = [];
                $condition[] = ['website_id', '=', $this->site_id];
                $condition[] = ['level_id', '=', $value['level_id']];
                $city_member_level = model('city_member_level')->getInfo($condition);
                $list['data']['list'][$key]['consume_discount'] = $city_member_level['consume_discount'];
                $list['data']['list'][$key]['point_feedback'] = $city_member_level['point_feedback'];
            }
            return $list;
        } else {
            return $this->fetch('memberlevel/level_list', [], $this->replace);
        }
    }

    /**
     * 会员等级修改
     */
    public function editLevel()
    {
        if (request()->isAjax()) {
            $data = [
                'level_name' => input('level_name', ''),
                'growth' => input('growth', 0.00),
                'remark' => input('remark', ''),
                'consume_discount' => input('consume_discount', 100),
                'point_feedback'   => input('point_feedback', 0),
                'send_point'       => input('send_point', 0),
                'send_balance'     => input('send_balance', 0),
                'send_coupon'      => input('coupon', ''),
                'level_type'      => 0,
                'charge_rule' => '',
                'charge_type' => 0,
                'price' => input('price', 0),
                'fan_point' => input('fan_point', 0),
                'city_member_level_id' => input('city_member_level_id', 0),


            ];

            $level_id = input('level_id', 0);

            $member_level_model = new MemberLevelModel();
            $this->addLog("会员等级修改:" . $data['level_name']);
            //修改城市参数
            model('city_member_level')->update([
                'consume_discount' => $data['consume_discount'],
                'point_feedback' => $data['point_feedback'],
                'price' => $data['price'],
                'fan_point' => $data['fan_point'],
            ], ['id'=>$data['city_member_level_id']]);
            unset($data['consume_discount']);
            unset($data['point_feedback']);
            return $member_level_model->editMemberLevel($data, [ [ 'level_id', '=', $level_id ] ]);
        } else {

            $level_id = input('get.level_id', 0);
            $member_level_model = new MemberLevelModel();
            $level_info = $member_level_model->getMemberLevelInfo([ [ 'level_id', '=', $level_id ] ]);

            //读取优惠券列表
            if (!isset($level_info['data']['send_coupon'])) $level_info['data']['send_coupon'] = 0;
            $coupon_list = [];
            if($level_info['data']['send_coupon'] != 0 && $level_info['data']['send_coupon'] != '') {
                $coupon = new PlatformcouponType();
                $coupon_list = $coupon->getPlatformcouponTypeList([ ['status','=',1],['platformcoupon_type_id','in',$level_info['data']['send_coupon']] ]);
                $coupon_list = $coupon_list['data'];
            }
            $level_info['data']['coupon_list'] = $coupon_list;
            $this->assign('level_info', $level_info);
            //城市设置的参数
            $condition = [];
            $condition[] = ['website_id', '=', $this->site_id];
            $condition[] = ['level_id', '=', $level_id];
            $city_member_level = model('city_member_level')->getInfo($condition);
            if (!$city_member_level) {
                model('city_member_level')->add([
                    'level_id' => $level_id,
                    'website_id' => $this->site_id
                ]);
                $city_member_level = model('city_member_level')->getInfo($condition);
            }
            $this->assign('city_level_info', $city_member_level);
            return $this->fetch('memberlevel/edit_level', [], $this->replace);
        }
    }
}