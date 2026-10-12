<?php


namespace app\api;


use think\facade\Db;

use app\model\member\MemberAccount;

class Task
{
    //积分到期扣除积分
    public function pointExpire($member_id)
    {
        $condition = [];
        $condition[] = ['member_id', '=', $member_id];
        $condition[] = ['point_expire', '=', 0];
        $condition[] = ['point_end_time', '<', time()];
        $condition[] = ['point_yu', '>', 0];
        $list = model('member_account')->getList($condition);

        $totalPoints = 0;
        $accountsToUpdate = [];
        foreach ($list as $key => $value) {
            $totalPoints += $value['point_yu'];
            $accountsToUpdate[] = $value['id'];  // 记录需要更新的ID
        }

        model('member')->startTrans();
        try {
            if (!empty($accountsToUpdate)) {
                model('member_account')->update([
                    'point_expire' => 1,
                ], [[ 'id', 'in', $accountsToUpdate ]]);
            }

            if ($totalPoints > 0) {
                $member_account = new MemberAccount();
                $result = $member_account->addMemberAccount2($member_id, 'point', -$totalPoints, 'expire', 0, "积分过期".$totalPoints, '积分过期',3);
                if ($result['code'] < 0) {
                    model('member')->rollback();
                }
            }

            model('member')->commit();
        } catch (\Exception $e) {
            model('member')->rollback();
        }
    }
}