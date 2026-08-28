<?php


namespace app\api\controller;

use app\model\member\MemberLevel as MemberLevelModel;

class Memberlevel extends BaseApi
{
	/**
	 * 列表信息
	 */
	public function lists()
	{
		$member_level_model = new MemberLevelModel();

        $field = 'level_id,level_name,growth,remark,consume_discount,is_free_shipping,point_feedback,send_point,send_balance,send_coupon,charge_rule,charge_type,bg_color';
        $member_level_list  = $member_level_model->getMemberLevelList([], $field, 'growth asc,level_id desc');
        return $this->response($member_level_list);

        
//		$member_level_list = $member_level_model->getMemberLevelList([], 'level_id,level_name,growth,remark', 'growth asc');
//		return $this->response($member_level_list);
	}

	/**
     * 会员等级列表
     */
    public function levelList()
    {
        $data = [
            'website_id' => $this->params['website_id'] ?? 0,
        ];
        $member_level_model = new MemberLevelModel();
        $result = $member_level_model->levelList($data);
        return $this->response($result);
	}

	/**
     * 开通会员
     * 订单创建
     */
    public function ordercreate()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $data = [
            'website_id' => $this->params['website_id'] ?? 0,
            'level_id' => $this->params['level_id'],
            'member_id' => $this->member_id,
        ];
        $member_level_model = new MemberLevelModel();
        $result = $member_level_model->ordercreate($data);
        return $this->response($result);
	}

	
}