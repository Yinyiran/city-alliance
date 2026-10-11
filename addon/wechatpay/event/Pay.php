<?php
/**

 */


namespace addon\wechatpay\event;

use addon\wechatpay\model\Pay as PayModel;
/**
 * 生成支付
 */
class Pay
{
    /**
     * 支付
     */
    public function handle($params)
    {
        //pay_type 大小写兼容：收银台等调用方可能传 WECHAT_PAY，
        //而历史上本监听器只匹配小写 wechatpay，会导致事件不命中、收款码生成失败
        if(strtolower((string)($params["pay_type"] ?? '')) == "wechatpay"){

            $app_type = $params['app_type'];
            $is_weapp = 0;
            switch ($app_type){
                case 'h5' :
                    $trade_type = "MWEB";
                    break;
                case 'wechat' :
                    $trade_type = "JSAPI";
                    break;
                case 'weapp' :
                    $is_weapp = 1;
                    $trade_type = "JSAPI";
                    break;
                case 'app' :
                    $is_weapp = 2;
                    $trade_type = "APP";
                    break;
                case 'pc' :
                    $trade_type = "NATIVE";
                    break;
            }
            $params["trade_type"] = $trade_type;
            $pay_model = new PayModel($is_weapp);
            $result = $pay_model->pay($params);
            return $result;
        }
    }
}