<?php


namespace app\api\controller;


class Profitsharing  extends BaseApi
{
    /**
     * 分账
     */
    public function profitsharing()
    {
        $url = 'https://api.mch.weixin.qq.com/v3/profitsharing/orders';
        $body = [
            'appid' => 'wx9099d099d29f16a6', //【公众账号ID】 微信分配的服务商appid
            'transaction_id' => '4200002353202407241544183889', //【微信订单号】 微信支付订单号
            'out_order_no' => 'P20150806125346', //【商户分账单号】 服务商系统内部的分账单号，在服务商系统内部唯一，同一分账单号多次请求等同一次。
            'receivers' => [
                [
                    'type' => 'PERSONAL_OPENID', //【分账接收方帐号】类型是MERCHANT_ID时，是商户号;类型是PERSONAL_OPENID时，是个人openid;类型是PERSONAL_SUB_OPENID时，是个人sub_openid
                    'account' => 'oIg4M5bneWmmgm-DFrZaecQf-NFA', //【分账接收方帐号】类型是MERCHANT_ID时，是商户号;类型是PERSONAL_OPENID时，是个人openid;类型是PERSONAL_SUB_OPENID时，是个人sub_openid 客户1681809348
                    'amount' => 1, //分账金额，单位为分，只能为整数，不能超过原订单支付金额及最大分账比例金额
                    'description' => '分账', //分账的原因描述，分账账单中需要体现
                ]
            ], //【分账接收方列表】
            'unfreeze_unsplit' => true, //【是否解冻剩余未分资金】 true,false
        ];
        $header = $this->getCurlHeader($url, json_encode($body), 'POST');
        $result = $this->curl($url, json_encode($body), $header, 'POST');
        dd($result);
        return json_decode($result, true);
    }

    /**
     * header参数
     */
    public function getCurlHeader($url, $body, $method)
    {
        $private_key = "/www/wwwroot/fenzhang.xing-peng.com/public/wx/apiclient_key.pem"; //商户私钥
        $mch_id = '1631992342'; //商户号
        $serial_no = '764EAD27EC8B7C4BF6F8DEDC69CC30EAD6C5E600'; //商户API证书序列号serial_no，用于声明所使用的证书
        $timestamp = time();
        $nonce = $this->nonce_str();
        $sign = $this->sign($url, $method, $timestamp, $nonce, $body, $this->getPrivateKey($private_key), $mch_id,
            $serial_no);

        return [
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.$sign,
            'Accept: application/json',
            'User-Agent:' . $mch_id,
            'Content-Type: application/json',
            'Wechatpay-Serial: 41AE2822E22ED8865EEF3F758FB19949042A91DD',
        ];
    }


    /**
     * 构建签名串
     */
    public function sign($url, $http_method, $timestamp, $nonce, $body, $mch_private_key, $merchant_id, $serial_no)
    {
        $url_parts = parse_url($url);
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message = $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";

        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);

        $schema = 'WECHATPAY2-SHA256-RSA2048';
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);
        return $token;
    }

    protected function nonce_str()
    {
        static $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function getPrivateKey($key)
    {
        return \openssl_get_privatekey(\file_get_contents($key));
    }

    public function curl($url,$data=[],$header,$method='POST'){
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        if($method=="POST"){
            curl_setopt($curl,CURLOPT_POST,TRUE);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        }
        $result=curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 添加分账接收方
     */
    public function receiversAdd()
    {
        $url = 'https://api.mch.weixin.qq.com/v3/profitsharing/receivers/add';
        $body = [
            'appid' => 'wx9099d099d29f16a6', //【公众账号ID】
            'type' => 'PERSONAL_OPENID', //【接收方类型】MERCHANT_ID：商户ID;PERSONAL_OPENID：个人openid（由父商户APPID转换得到）
            'account' => 'oIg4M5bneWmmgm-DFrZaecQf-NFA', //【接收方账号】类型是MERCHANT_ID时，是商户号,类型是PERSONAL_OPENID时，是个人openid
            'relation_type' => 'STAFF', //【与分账方的关系类型】 子商户与接收方的关系。
        ];
        $header = $this->getCurlHeader($url, json_encode($body), 'POST');
        $result = $this->curl($url, json_encode($body), $header, 'POST');
        dd($result);
    }

    /**
     * 微信支付平台证书下载
     */
    public function certificates()
    {
        $url="https://api.mch.weixin.qq.com/v3/certificates";//获取地址
        $timestamp=time();//时间戳
        $nonce=$this->nonce_str();//获取一个随机字符串
        $body="";
        $mch_private_key=$this->getPublicKey();//读取商户api证书公钥
        $merchant_id='1679816552';//商户号
        $serial_no='6913095BDDBBB25A0679BDBF82F1BA0F04904822';//商户证书序列号
        $sign=$this->sign($url,'GET',$timestamp,$nonce,$body,$mch_private_key,$merchant_id,$serial_no);

        $header=[
            'Authorization:WECHATPAY2-SHA256-RSA2048 '.$sign,
            'Accept:application/json',
            'User-Agent:'.$merchant_id
        ];
        $result=$this->curl($url,'',$header,'GET');
        $result=json_decode($result,true);

        $serial_no=$result['data'][0]['serial_no'];//获取的平台证书序列号
        $encrypt_certificate=$result['data'][0]['encrypt_certificate'];
        $sign_key="ZHC4KEK2MWUGH6WZM8YF5T4X5BRXNFXR";  //APIv3密钥，商户平台API安全中获取

        $result=$this->decryptToString($encrypt_certificate['associated_data'],$encrypt_certificate['nonce'],$encrypt_certificate['ciphertext'],$sign_key);

        file_put_contents( '/www/wwwroot/fenzhang.xing-peng.com/public/wx2/cert.pem',$result);//获取的文件临时保存到服务器
        return $serial_no;//返回平台证书序列号
    }

    //读取商户api证书公钥
    public function getPublicKey(){
        return openssl_get_privatekey(file_get_contents('/www/wwwroot/fenzhang.xing-peng.com/public/wx2/apiclient_key.pem'));//商户平台API安全证书中下载，保存到服务器
    }

    //解密返回的信息
    public function decryptToString($associatedData,$nonceStr,$ciphertext,$aesKey){
        $ciphertext=\base64_decode($ciphertext);
        if(function_exists('\sodium_crypto_aead_aes256gcm_is_available')&& \sodium_crypto_aead_aes256gcm_is_available()){
            return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext,$associatedData,$nonceStr,$aesKey);
        }

        if(PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())){
            $ctext=substr($ciphertext,0,-16);
            $authTag=substr($ciphertext,-16);
            return \openssl_decrypt(
                $ctext,
                'aes-256-gcm',
                $aesKey,
                \OPENSSL_RAW_DATA,
                $nonceStr,
                $authTag,
                $associatedData
            );
        }
        throw new \RuntimeException('php7.1');
    }
}