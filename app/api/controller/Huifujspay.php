<?php


namespace app\api\controller;
use app\model\system\Pay;
use BsPaySdk\core\BsPayClient;
use BsPaySdk\request\V2TradePaymentJspayRequest;
use BsPaySdk\request\V2TradePaymentScanpayRefundRequest;
use think\Request;

require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/BsPaySdk/init.php';
require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/BsPaySdk/request/V2TradePaymentJspayRequest.php';
require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/Composer/BsPayConfig.php';

require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/BsPaySdk/request/V2TradeHostingPaymentPreorderWxRequest.php';
require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/BsPaySdk/request/V2TradePaymentScanpayRefundRequest.php';

/**
 * 汇付
 * Class Huifujspay
 * @package app\api\controller
 */
class Huifujspay extends BaseApi
{
    protected $huifuid = '6666000172947614'; //商户号
    /**
     * 聚合正扫
     */
    public function jspay($order)
    {
        $pay = new Pay();
        $order_no = $pay->createOutTradeNo($order['member_id']);
        model('order_no')->add([
            'member_id' => $order['member_id'],
            'order_id' => $order['order_id'],
            'order_no' => $order_no,
            'create_time' => time(),
            'out_trade_no' => $order['out_trade_no'],
        ]);
        // 2.组装请求参数
        $request = new V2TradePaymentJspayRequest();
        // 请求日期
        $request->setReqDate(date("Ymd"));
        // 请求流水号
        $request->setReqSeqId($order_no);
        // 商户号
        $request->setHuifuId($this->huifuid);
        // 商品描述
        $goodsDesc = trim($order['order_name']);
        //去除空格
        $goodsDesc = str_replace(' ', '_', $goodsDesc);
        $pay_money = $order['pay_money'];
        $goodsDesc = str_sub($goodsDesc, 15) ?? '订单支付';
        $request->setGoodsDesc($goodsDesc);
        // 交易类型
        $request->setTradeType("T_MINIAPP");
        // 交易金额
        $request->setTransAmt($pay_money);
//        dd($this->huifuid);
        // 设置非必填字段
        $extendInfoMap = $this->getExtendInfos($order , 1);
        $request->setExtendInfo($extendInfoMap);
        // 3. 发起API调用
        $client = new BsPayClient();
        $result = $client->postRequest($request);
//        dd($result);

        if (!$result || $result->isError()) {  //失败处理
            $this->error($result->getErrorInfo());
        }

        $data = $result->getRspDatas();
//        dd($data);
//        dd($request);
        $pay_info = $data['data']['pay_info'];
        $pay_info = json_decode($pay_info, true);
        return $pay_info;
    }

    public function getExtendInfos($order , $type)
    {
        $member = model('member')->getInfo(['member_id'=>$order['member_id']]);
        // 设置非必填字段
        $extendInfoMap = array();
        // 交易有效期
        $extendInfoMap["time_expire"]= date('YmdHis', time()+1800);
        // 微信参数集合
        $dto = array();
        // 子商户应用ID
        $dto["sub_appid"] = 'wx78e3faf3a96aadd2';
        $dto["sub_openid"] = $member['weapp_openid'];
        if ($type == 3) {
            $dto["sub_appid"] = 'wxc282c72663220eb1';
            $dto["sub_openid"] = $member['wxqy_openid'];
        }
        // 子商户用户标识


        $extendInfoMap["wx_data"]= json_encode($dto,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        // 异步通知地址
        if ($type == 1) {
            $extendInfoMap["notify_url"]= request()->domain() . '/api/notify/orderNotify';
        } elseif($type==2) {
            $extendInfoMap["notify_url"]= request()->domain() . '/api/notify/memberLevelNotify';
        }elseif($type==3) {
            $extendInfoMap["notify_url"]= request()->domain() . '/api/notify/shopdepositNotify';
        }

        return $extendInfoMap;
    }

    //开通会员支付
    public function memberlevelpay($order, $payModel)
    {
        $pay = new Pay();
        $order_no = $pay->createOutTradeNo($order['member_id']);
        model('order_no')->add([
            'member_id' => $order['member_id'],
            'order_id' => $order['order_id'],
            'order_no' => $order_no,
            'create_time' => time(),
            'out_trade_no' => $order['out_trade_no'],
        ]);
        // 2.组装请求参数
        $request = new V2TradePaymentJspayRequest();
        // 请求日期
        $request->setReqDate(date("Ymd"));
        // 请求流水号
        $request->setReqSeqId($order_no);
        // 商户号
        $request->setHuifuId($this->huifuid);
        // 商品描述
        $goodsDesc = trim($payModel['pay_body']);
        //去除空格
        $goodsDesc = str_replace(' ', '_', $goodsDesc);
        $pay_money = $payModel['pay_money'];
        $goodsDesc = str_sub($goodsDesc, 15) ?? '订单支付';
        $request->setGoodsDesc($goodsDesc);
        // 交易类型
        $request->setTradeType("T_MINIAPP");
        // 交易金额
        $request->setTransAmt($pay_money);
//        dd($this->huifuid);
        // 设置非必填字段
        $extendInfoMap = $this->getExtendInfos($order, 2);
        $request->setExtendInfo($extendInfoMap);
        // 3. 发起API调用
        $client = new BsPayClient();
        $result = $client->postRequest($request);
//        dd($result);

        if (!$result || $result->isError()) {  //失败处理
            $this->error($result->getErrorInfo());
        }

        $data = $result->getRspDatas();
//        dd($data);
//        dd($request);
        $pay_info = $data['data']['pay_info'];
        $pay_info = json_decode($pay_info, true);
        return $pay_info;
    }


    //商家保证金支付
    public function shopdepositpay($order, $payModel)
    {
        $pay = new Pay();
        $order_no = $pay->createOutTradeNo($order['member_id']);
        model('order_no')->add([
            'member_id' => $order['member_id'],
            'order_id' => $order['order_id'],
            'order_no' => $order_no,
            'create_time' => time(),
            'out_trade_no' => $order['out_trade_no'],
        ]);
        // 2.组装请求参数
        $request = new V2TradePaymentJspayRequest();
        // 请求日期
        $request->setReqDate(date("Ymd"));
        // 请求流水号
        $request->setReqSeqId($order_no);
        // 商户号
        $request->setHuifuId($this->huifuid);
        // 商品描述
        $goodsDesc = trim($payModel['pay_body']);
        //去除空格
        $goodsDesc = str_replace(' ', '_', $goodsDesc);
        $pay_money = $payModel['pay_money'];
        $goodsDesc = str_sub($goodsDesc, 15) ?? '订单支付';
        $request->setGoodsDesc($goodsDesc);
        // 交易类型
        $request->setTradeType("T_MINIAPP");
        // 交易金额
        $request->setTransAmt($pay_money);
//        dd($this->huifuid);
        // 设置非必填字段
        $extendInfoMap = $this->getExtendInfos($order, 3);
        $request->setExtendInfo($extendInfoMap);
        // 3. 发起API调用
        $client = new BsPayClient();
        $result = $client->postRequest($request);
//        dd($result);

        if (!$result || $result->isError()) {  //失败处理
            $this->error($result->getErrorInfo());
        }

        $data = $result->getRspDatas();
//        dd($data);
//        dd($request);
        $pay_info = $data['data']['pay_info'];
        $pay_info = json_decode($pay_info, true);
        return $pay_info;
    }

    /**
     * 退款
     */
    public function refund($params)
    {
//        dd($params);
        $refund_no = $params['refund_no'];
        $request = new V2TradePaymentScanpayRefundRequest();
        // 请求日期
        $request->setReqDate(date("Ymd"));
        // 请求流水号
        $request->setReqSeqId($refund_no);
        // 商户号
        $request->setHuifuId($this->huifuid);
        // 申请退款金额
        $request->setOrdAmt($params['refund_fee']);
        // 原交易请求日期
        $request->setOrgReqDate($params['pay_info']['req_date']);

        // 设置非必填字段
        $extendInfoMap = [
            // 原交易全局流水号
//            'org_hf_seq_id' => '',
            // 原交易微信支付宝的商户单号
//            'org_party_order_id' => '',
            // 原交易请求流水号
            'org_req_seq_id' => $params['pay_info']['req_seq_id'],
            // 分账对象
//            'acct_split_bunch' => [],
            // 异步通知地址
            'notify_url' => request()->domain() . '/api/notify/orderRefundNotify',
        ];
        $request->setExtendInfo($extendInfoMap);
        // 3. 发起API调用
        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {
            $this->error($result->getErrorInfo());
        }

        $refund_res = $result->getRspDatas();
        if (empty($refund_res['data']) || empty($refund_res['data']['trans_stat'])) {
            $this->error('退款失败');
        }
        if (!in_array($refund_res['data']['trans_stat'], ['P', 'S']) ) {
            $this->error('退款失败');
        }

        return $this->success();
    }
}