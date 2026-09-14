<?php

namespace app\event;

use app\model\signin\SignSettlement;
use think\facade\Log;

/**
 * 签到收费与返现周结算计划任务
 */
class CronSignSettlement
{
    public function handle($param = [])
    {
        try {
            $settlement_model = new SignSettlement();
            $res = $settlement_model->cronWeeklySettlement();
            Log::write('CronSignSettlement result: ' . json_encode($res), 'cron');
            return $res;
        } catch (\Exception $e) {
            Log::write('CronSignSettlement error: ' . $e->getMessage(), 'error');
            return ['code' => -1, 'message' => $e->getMessage()];
        }
    }
}
