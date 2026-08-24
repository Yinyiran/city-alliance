<?php


namespace app\event;

use app\model\shopapplet\ShopDeposit;

/**
 * 会员升级回调
 * Class MemberLevelOrderPayNotify
 * @package app\event
 */
class MemberLevelOrderPayNotify
{
    public function handle($data)
    {
        $model = new ShopDeposit();
        $res = $model->orderPay($data);
        return $res;
    }
}