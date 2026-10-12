<?php


namespace app\model\pay;

use app\model\BaseModel;
use app\model\member\Member;
use BsPaySdk\core\BsPayClient;
use BsPaySdk\request\V2TradePaymentJspayRequest;
use BsPaySdk\request\V2UserBasicdataIndvRequest;
use BsPaySdk\request\V2UserBasicdataEntRequest;
use BsPaySdk\request\V2MerchantBasicdataIndvRequest;
use BsPaySdk\request\V2MerchantBasicdataEntRequest;
use BsPaySdk\request\V2TradeSettlementEnchashmentRequest;
use BsPaySdk\request\V2SupplementaryPictureRequest;
use BsPaySdk\request\V2TradePaymentScanpayRefundRequest;
use BsPaySdk\request\V2UserBusiOpenRequest;
use CURLFile;
use function BsPayDemo\getCardInfo;
use function BsPayDemo\getDetail;
use function BsPayDemo\getExtendInfos;
use function BsPayDemo\getSceneInfo;

require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/init.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2UserBusiOpenRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2TradePaymentJspayRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2UserBasicdataIndvRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2UserBasicdataEntRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2MerchantBasicdataIndvRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2MerchantBasicdataEntRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2TradeSettlementEnchashmentRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2SupplementaryPictureRequest.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/request/V2TradePaymentScanpayRefundRequest.php';

require_once root_path() . '/extend/dg-php-sdk/Composer/BsPayConfig.php';

class Huifu extends BaseModel
{
    protected $openid = '';
    protected $notify_url = '';
    protected $trade_type = '';
    protected $huifu_id = '6666000126879397';

    //个人用户基本信息开户
    public function personalAccountOpening($data)
    {

        // 2.组装请求参数
        $request = new V2UserBasicdataIndvRequest();
        $request->setReqSeqId(date("YmdHis"));
        $request->setreqDate(date("Ymd"));
        $request->setName($data['name']);
        $request->setCertType('00');
        $request->setCertNo($data['cert_no']);
        $request->setCertValidityType($data['cert_validity_type']);
        $request->setCertBeginDate(date('Ymd',strtotime($data['cert_begin_date'])));
        $request->setCertEndDate(date('Ymd',strtotime($data['cert_end_date'])));
        $request->setMobileNo($data['mobile_no']);
        //$request->setAddress($data['contacts_card_no']);
        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            //$res = json_decode($data['data'],true);
            $res = $data['data'];
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //企业用户基本信息开户
    public function businessAccountOpening($data)
    {

        $request = new V2UserBasicdataEntRequest();
        $request->setReqSeqId(date("YmdHis"));
        $request->setreqDate(date("Ymd"));
        $request->setRegName($data['reg_name']);
        $request->setLicenseCode($data['license_code']);
        $request->setLicenseValidityType($data['license_validity_type']);
        $request->setLicenseBeginDate(date('Ymd',strtotime($data['license_begin_date'])));
        $request->setLicenseEndDate(date('Ymd',strtotime($data['license_end_date'])));
        $request->setRegProvId($data['reg_prov_id']);
        $request->setRegAreaId($data['reg_area_id']);
        $request->setRegDistrictId($data['reg_district_id']);
        $request->setRegDetail($data['reg_detail']);
        $request->setLegalName($data['legal_name']);
        $request->setLegalCertType($data['legal_cert_type']);
        $request->setLegalCertNo($data['legal_cert_no']);
        $request->setLegalCertValidityType($data['legal_cert_validity_type']);
        $request->setLegalCertBeginDate(date('Ymd',strtotime($data['legal_cert_begin_date'])));
        $request->setLegalCertEndDate(date('Ymd',strtotime($data['legal_cert_end_date'])));
        $request->setContactName($data['contact_name']);
        $request->setContactMobile($data['contact_mobile']);
        //$request->setLoginName($data['reg_name']);
        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            $res = $data['data'];
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //用户业务入驻
    public function userBusiOpen($data)
    {
        $request = new V2UserBusiOpenRequest();
        $request->setHuifuId($data['huifu_id']);
        $request->setReqSeqId($data['req_seq_id']);
        $request->setreqDate($data['req_date']);
        $request->setUpperHuifuId($this->huifu_id);
        $card_info = [
            'card_type'=>$data['card_type'],
            'card_no'=>$data['card_no'],
            'prov_id'=>$data['prov_id'],
            'area_id'=>$data['area_id'],
        ];
        if (isset($data['card_name'])){
            $card_info['card_name'] = $data['card_name'];
        }
        if ($data['card_type']==0){
            $card_info['bank_code'] = $data['bank_code'];
            $card_info['branch_code'] = $data['branch_code'];
        } else {
            $card_info['cert_type'] = '00';
            $card_info['cert_no'] = $data['cert_no'];
            $card_info['cert_validity_type'] = $data['cert_validity_type'];
            $card_info['cert_begin_date'] = date('Ymd',strtotime($data['cert_begin_date']));
            $card_info['cert_end_date'] =  date('Ymd',strtotime($data['cert_end_date']));
        }

        //$return_url = addon_url("pay/huifu/shopnotify");

        $request->setAsyncReturnUrl($data['return_url']);
        $request->setCardInfo(json_encode($card_info,320));
        $client = new BsPayClient();

        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            $res = $data['data'];
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //个人商户进件
    public function personalMerchantPurchase($data)
    {
        $request = new V2MerchantBasicdataIndvRequest();
        $request->setReqSeqId($data['req_seq_id']);
        $request->setreqDate(date("Ymd"));
        $request->setUpperHuifuId($this->huifu_id);
        $request->setRegName($data['reg_name']);
        $request->setProvId($data['prov_id']);
        $request->setAreaId($data['area_id']);
        $request->setDistrictId($data['district_id']);
        $request->setDetailAddr($data['detail_addr']);
        $request->setContactName($data['contact_name']);
        $request->setContactMobileNo($data['contact_mobile_no']);
        $request->setContactEmail($data['contact_email']);
        $request->setCardInfo($data['card_info']);
        $request->setAsyncReturnUrl($data['return_url']);
        $request->setMcc($data['mcc']);
        $request->setSceneType($data['scene_type']);
        $request->setLegalCertNo($data['legal_cert_no']);
        $request->setLegalCertBeginDate(date('Ymd',strtotime($data['legal_cert_begin_date'])));
        $request->setLegalCertEndDate(date('Ymd',strtotime($data['legal_cert_end_date'])));
        $request->setLegalAddr($data['legal_addr']);
        $request->setLegalCertBackPic($data['legal_cert_back_pic']);
        $request->setLegalCertFrontPic($data['legal_cert_front_pic']);
        $request->setSettleCardFrontPic($data['settle_card_front_pic']);

        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            $res = $data['data'];
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //企业商户进件
    public function businessMerchantPurchase($data)
    {
        $request = new V2MerchantBasicdataEntRequest();
        $request->setReqSeqId($data['req_seq_id']);
        $request->setreqDate(date("Ymd"));
        $request->setUpperHuifuId($this->huifu_id);
        $request->setRegName($data['reg_name']);
        $request->setShortName($data['short_name']);
        $request->setEntType($data['ent_type']);
        $request->setLicenseCode($data['license_code']);
        $request->setLicensePic($data['license_pic']);
        $request->setLicenseValidityType($data['license_validity_type']);
        $request->setLicenseBeginDate(date('Ymd',strtotime($data['license_begin_date'])));
        $request->setLicenseEndDate(date('Ymd',strtotime($data['license_end_date'])));
        $request->setRegProvId($data['reg_prov_id']);
        $request->setRegAreaId($data['reg_area_id']);
        $request->setRegDistrictId($data['reg_district_id']);
        $request->setRegDetail($data['reg_detail']);
        $request->setLegalName($data['legal_name']);
        $request->setLegalCertType($data['legal_cert_type']);
        $request->setLegalCertNo($data['legal_cert_no']);
        $request->setLegalCertValidityType($data['legal_cert_validity_type']);
        $request->setLegalCertBeginDate(date('Ymd',strtotime($data['legal_cert_begin_date'])));
        $request->setLegalCertEndDate(date('Ymd',strtotime($data['legal_cert_end_date'])));
        $request->setProvId($data['prov_id']);
        $request->setAreaId($data['area_id']);
        $request->setDistrictId($data['district_id']);
        $request->setDetailAddr($data['detail_addr']);
        $request->setLegalAddr($data['legal_addr']);
        $request->setContactMobileNo($data['contact_mobile_no']);
        $request->setContactEmail($data['contact_email']);

        $request->setBusiType($data['busi_type']);
        $request->setReceiptName($data['receipt_name']);
        $request->setMcc($data['mcc']);
        $request->setCardInfo($data['card_info']);

        $request->setAsyncReturnUrl($data['return_url']);
        $request->setSceneType($data['scene_type']);
        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            $res = $data['data'];
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //取现
    public function withdraw($data)
    {
        $request = new V2TradeSettlementEnchashmentRequest();
        $request->setReqSeqId($data['req_seq_id']);
        $request->setreqDate($data['req_date']);
        $request->setCashAmt($data['cash']);
        $request->setHuifuId($data['huifu_id']);
        $request->setIntoAcctDateType('D1');
        $request->setTokenNo($data['token_no']);
        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            $res = json_decode($data['data'],true);
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }

    }

    //下单
    public function create($param)
    {
        $this->trade_type = $param["trade_type"];
        $this->notify_url = $param["notify_url"];

        // 2.组装请求参数
        $request = new V2TradePaymentJspayRequest();

        //$pay_info = model('pay')->getInfo(['out_trade_no'=>$param["out_trade_no"]]);

        $request->setReqDate(date("Ymd"));
        $request->setReqSeqId(date("YmdHis"));
        $request->setRemark($param["out_trade_no"]);

        $request->setHuifuId($this->huifu_id);
        $request->setGoodsDesc(str_sub($param["pay_body"], 15));

        $request->setTradeType($param["trade_type"]);
        $request->setTransAmt($param["pay_money"]);

        if ($param["trade_type"]=='T_MINIAPP'){
            $member_model = new Member();
            $member_info_result = $member_model->getMemberInfo([["member_id", "=", $param["member_id"]]], "weapp_openid");
            $member_info = $member_info_result["data"];
            $this->openid = $member_info["weapp_openid"];
        }

        //dump($param);die;
        $extendInfoMap = $this->getExtendInfos();

        $request->setExtendInfo($extendInfoMap);

        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();
        //dump($data);die;
        if (isset($data['data']['bank_code'])&&$data['data']['bank_code']==00000000){
            $pay_info = json_decode($data['data']['pay_info'],true);
            return $this->success($pay_info);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //退款
    public function refund($out_trade_no,$refund_no,$refund_fee,$pay_time)
    {
        $request = new V2TradePaymentScanpayRefundRequest();

        $request->setReqDate(date("Ymd"));
        //$request->setReqSeqId($refund_no);
        $request->setReqSeqId(date("YmdHis"));
        $request->setHuifuId($this->huifu_id);

        $request->setOrdAmt($refund_fee);//申请退款金额

        $request->setOrgReqDate(date("Ymd",$pay_time));//原交易请求日期
        $request->setOrgHfSeqId($out_trade_no);

        //$extendInfoMap = $this->getExtendInfos();

        //$request->setExtendInfo($extendInfoMap);

        $client = new BsPayClient();
        $result = $client->postRequest($request);

        if (!$result || $result->isError()) {  //失败处理
            return $this->error('',$result->getErrorInfo());
        }

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']=='00000000'||$data['data']['resp_code']=='00000100'){
            //$pay_info = json_decode($data['data'],true);
            $info = $data['data'];
            return $this->success($info);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    //上传图片
    public function upload($file_type,$file_url,$file_name)
    {
        $request = new V2SupplementaryPictureRequest();
        $request->setReqSeqId(date("YmdHis"));
        $request->setreqDate(date("Ymd"));
        $request->setFileType($file_type);
        $request->setPicture($file_name);
        //dump($file_url);die;
        // 设置非必填字段
        //$extendInfoMap = $this->getUploadExtendInfos($file_url);
        //$request->setExtendInfo($extendInfoMap);

        // 3. 发起API调用

        $client = new BsPayClient();
        $result = $client->postRequest($request,new CURLFile($file_url,"image/png",$file_name));
        //dump($result);die;

        $data = $result->getRspDatas();

        if (isset($data['data']['resp_code'])&&$data['data']['resp_code']==00000000){
            $res = $data['data'];
            return $this->success($res);

        } else {
            return $this->error('',$data['data']['resp_desc']);
        }
    }

    public function getExtendInfos() {
        // 设置非必填字段
        $extendInfoMap = array();
        // 账户号
        // $extendInfoMap["acct_id"]= "";
        // 交易有效期
        //$extendInfoMap["time_expire"]= "20250518235959";
        if ($this->trade_type=='T_MINIAPP'||$this->trade_type=='T_APP'){
            // 微信参数集合
            $extendInfoMap["wx_data"] = $this->getWxData();
        }

        // 支付宝参数集合
        //$extendInfoMap["alipay_data"]= getAlipayData();
        // 银联参数集合
        //$extendInfoMap["unionpay_data"]= getUnionpayData();
        // 异步通知地址
        $extendInfoMap["notify_url"]= $this->notify_url;

        return $extendInfoMap;
    }

    /**
     * 非必填字段
     *
     */
    public function getUploadExtendInfos($file_url) {
        // 设置非必填字段
        $extendInfoMap = array();
        // 商户号
        $extendInfoMap["huifu_id"]= $this->huifu_id;
        // 文件url链接
        $extendInfoMap["file_url"]= $file_url;
        return $extendInfoMap;
    }

    public function getWxData() {
        $dto = array();
        if ($this->trade_type=='T_MINIAPP'){
            // 子商户应用ID
            $dto["sub_appid"] = "wx239a53bae8eb57fe";
            // 子商户用户标识
            $dto["sub_openid"] = $this->openid;
        }
        $dto["body"] = '测试';
        return json_encode($dto,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }


    public function getBankCode()
    {
        $data = [
            [
                'name'=>'中国银行',
                'code'=>'01040000'
            ],
            [
                'name'=>'中国农业银行',
                'code'=>'01030000'
            ],
            [
                'name'=>'中国工商银行',
                'code'=>'01020000'
            ],
            [
                'name'=>'中国建设银行',
                'code'=>'01050000'
            ],
            [
                'name'=>'交通银行',
                'code'=>'03010000'
            ],
            [
                'name'=>'中国邮政储蓄银行',
                'code'=>'04030000'
            ],
            [
                'name'=>'华夏银行',
                'code'=>'03040000'
            ],
            [
                'name'=>'招商银行',
                'code'=>'03080000',
            ],
            [
                'name'=>'上海浦东发展银行',
                'code'=>'03100000'
            ],
            [
                'name'=>'浙商银行',
                'code'=>'03160000'
            ],
            [
                'name'=>'广发银行',
                'code'=>'03060000'
            ],
            [
                'name'=>'天津银行',
                'code'=>'03130012'
            ],
            [
                'name'=>'济宁银行',
                'code'=>'31346100'
            ],
            [
                'name'=>'中国民生银行',
                'code'=>'03050000'
            ],
        ];

        return $data;
    }
}