<?php


namespace app\api\controller;

use app\model\member\MemberLevel as MemberLevelModel;

use think\facade\Db;

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

	/**
	 * 我的推广分润总览
	 */
	public function myIncomeSummary()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$member_id = $this->member_id;

		// 1. 会员购买邀请奖励：已解冻入账
		$invite_unlocked = Db::name('member_level_settlement')
			->where('invite_member_id', $member_id)
			->where('status', 1)
			->sum('invite_money');

		// 2. 会员购买邀请奖励：待解冻(满7天或消费)
		$invite_locked = Db::name('member_level_settlement')
			->where('invite_member_id', $member_id)
			->where('status', 0)
			->sum('invite_money');

		// 3. 带单消费佣金总额
		$bring_commission = Db::name('fenxiao_money')
			->where('two_member_id', $member_id)
			->sum('two_commission');

		// 4. 用户当前账户信息
		$member_info = Db::name('member')->where('member_id', $member_id)->field('balance_money, point, member_level, member_level_name')->find();

		$total_income = round($invite_unlocked + $bring_commission, 2);

		return $this->response($this->success([
			'total_income' => number_format($total_income, 2, '.', ''),
			'invite_unlocked' => number_format($invite_unlocked, 2, '.', ''),
			'invite_locked' => number_format($invite_locked, 2, '.', ''),
			'bring_commission' => number_format($bring_commission, 2, '.', ''),
			'balance_money' => number_format($member_info['balance_money'] ?? 0, 2, '.', ''),
			'member_level' => $member_info['member_level'] ?? 1,
			'member_level_name' => $member_info['member_level_name'] ?? '普通会员',
		]));
	}

	/**
	 * 我的收益明细列表
	 */
	public function myIncomeList()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$member_id = $this->member_id;
		$type = intval($this->params['type'] ?? 1); // 1-邀约办会员奖励 2-消费带单佣金
		$page = intval($this->params['page'] ?? 1);
		$page_size = intval($this->params['page_size'] ?? 10);

		if ($type == 1) {
			// 邀约会员奖励
			$query = Db::name('member_level_settlement')->alias('mls')
				->leftJoin('member m', 'm.member_id = mls.buy_member_id')
				->field('mls.*, m.nickname, m.headimg, m.mobile')
				->where('mls.invite_member_id', $member_id)
				->order('mls.create_time desc');

			$total = $query->count();
			$list = $query->page($page, $page_size)->select()->toArray();

			foreach ($list as &$item) {
				$passed_days = floor((time() - $item['create_time']) / 86400);
				$remain_days = 7 - $passed_days;
				if ($remain_days < 0) $remain_days = 0;
				$item['remain_days'] = $remain_days;
				$item['create_time_text'] = date('Y-m-d H:i', $item['create_time']);
			}

			return $this->response($this->success([
				'list' => $list,
				'total' => $total,
				'page_count' => ceil($total / $page_size)
			]));
		} else {
			// 带单消费佣金
			$query = Db::name('fenxiao_money')
				->where('two_member_id', $member_id)
				->order('create_time desc');

			$total = $query->count();
			$list = $query->page($page, $page_size)->select()->toArray();

			foreach ($list as &$item) {
				$item['create_time_text'] = date('Y-m-d H:i', $item['create_time']);
			}

			return $this->response($this->success([
				'list' => $list,
				'total' => $total,
				'page_count' => ceil($total / $page_size)
			]));
		}
	}
}