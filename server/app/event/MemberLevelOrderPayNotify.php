<?php


namespace app\event;

use app\model\member\MemberLevel;

/**
 * 会员升级回调
 * Class MemberLevelOrderPayNotify
 * @package app\event
 */
class MemberLevelOrderPayNotify
{
    public function handle($data)
    {
        $model = new MemberLevel();
        $res = $model->orderPay($data);
        return $res;
    }
}