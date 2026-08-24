<?php
/**

 */


namespace addon\wechatpay\event;

use addon\wechatpay\model\Pay as PayModel;
use app\api\controller\Huifujspay;
use app\api\controller\Lkl;

/**
 * 原路退款
 */
class PayRefund
{
    /**
     * 原路退款
     */
    public function handle($params)
    {
        if($params["pay_info"]["pay_type"] == "wechatpay"){
            $pay_model = new PayModel();
            $result = $pay_model->refund($params);
            return $result;
        } elseif ($params["pay_info"]["pay_type"] == "HUIFU") { //退款
            $pay_model = new Huifujspay();
            $result = $pay_model->refund($params);
            return $result;
        }
    }
}