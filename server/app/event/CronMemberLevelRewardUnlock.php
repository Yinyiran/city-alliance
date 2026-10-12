<?php

namespace app\event;

use app\model\member\MemberLevelSettlement;
use think\facade\Log;

/**
 * 会员购买分润奖励满7天+下级消费自动解冻计划任务
 */
class CronMemberLevelRewardUnlock
{
    public function handle($param = [])
    {
        try {
            $settle_model = new MemberLevelSettlement();
            $res = $settle_model->cronCheckUnlockReward();
            Log::write('CronMemberLevelRewardUnlock result: ' . json_encode($res), 'cron');
            return $res;
        } catch (\Exception $e) {
            Log::write('CronMemberLevelRewardUnlock error: ' . $e->getMessage(), 'error');
            return ['code' => -1, 'message' => $e->getMessage()];
        }
    }
}
