<?php
/**

 */


namespace addon\alipay\event;

use addon\alipay\model\Pay as PayModel;
/**
 * 生成支付
 */
class Pay
{
	/**
	 * 支付方式及配置
	 */
	public function handle($param)
	{

        //pay_type 大小写兼容：调用方可能传 ALIPAY，历史只匹配小写会不命中
        if(strtolower((string)($param["pay_type"] ?? '')) == "alipay"){
            if (in_array($param["app_type"], [ "h5", "app", "pc", "aliapp" ])) {
                $pay_model = new PayModel();
                $res = $pay_model->pay($param);
                return $res;
            }
        }
	}
}