<?php
/**

 */


namespace addon\memberrecharge\event;
use addon\memberrecharge\model\MemberrechargeOrder;

/**
 * 充值订单回调
 */
class MemberRechargeOrderPayNotify
{

    public function handle($data)
    {
        $model = new MemberrechargeOrder();
        $res = $model->orderPay($data);
        return $res;
    }

}