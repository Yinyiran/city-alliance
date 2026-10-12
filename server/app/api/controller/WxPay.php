<?php


namespace app\api\controller;


use app\model\member\Withdraw as MemberWithdraw;
use think\facade\Db;

/**
 * 微信商家转账
 * Class WxPay
 * @package app\api\controller
 */
class WxPay  extends BaseApi
{
    //传入真实姓名
    public static function encryptUserName($str){
        //$str是待加密字符串
        $public_key_path = app()->getRootPath().'public/wx/shop_key.pem';
        $public_key = file_get_contents($public_key_path);
        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            throw new Exception('encrypt failed');
        }
        return $sign;
    }

    //使用的证书，是通过“下载平台证书接口下载的”
    public static function getEncrypt() {
        //微信支付平台证书序列号
        $str = '37ED88D4B7CAAD5FACE8666FF38E68DF3B147892';
        return $str;
        //$str是待加密字符串
        $public_key_path = '/www/wwwroot/chaichaiban.com/public/cert/shop_key.pem';
        $public_key = file_get_contents($public_key_path);
        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            throw new Exception('encrypt failed');
        }
        return $sign;
    }

    public static function nonce(int $size = 32)
    {
        if ($size < 1) {
            throw new Exception('Size must be a positive integer.');
        }
        return implode('', array_map(static function(string $c): string {
            return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'[ord($c) % 62];
        }, str_split(random_bytes($size))));
    }

    //请求参数里带Body参数(包体参数），计算签名
    public static function getToken($url,$timestamp,$body='',$value,$http_method='POST')
    {
        $url_parts   = parse_url($url); //获取请求的绝对URL
        $nonce       = self::nonce();  //请求随机串
        $stream_opts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];
        $apiclient_cert_path = $value['cert_client'];
        $apiclient_key_path  = $value['cert_key'];
        $apiclient_cert_arr = openssl_x509_parse(file_get_contents($apiclient_cert_path,false, stream_context_create($stream_opts)));
        $serial_no          = $apiclient_cert_arr['serialNumberHex'];   // 证书序列号(忽略)
        $mch_private_key    = file_get_contents($apiclient_key_path,false, stream_context_create($stream_opts));    // 密钥
        $merchant_id = $value['mch_id'];;   // 商户id
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message = $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";

        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');

        $sign = base64_encode($raw_sign);   // 签名
        return sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);   // 微信返回token
    }

    public static function param_filter($para)
    {
        $paraFilter = [];
        foreach ($para as $key => $val) {
            if ($val === '' || $val === null) {
                continue;
            }
            if (!is_array($para[$key])) {
                if (!is_numeric($para[$key])){
                    $para[$key] = is_bool($para[$key]) ? $para[$key] : trim($para[$key]);
                }
            }
            $paraFilter[$key] = $para[$key];
        }
        return $paraFilter;
    }


    public static function param_sort(array $param)
    {
        ksort($param);
        reset($param);
        return $param;
    }

    /**
     *请求接口
     */
    public static function https_request($url,$data = null,$token=''){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, (string)$url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //添加请求头
        $getEncrypt=self::getEncrypt();
        $headers = [
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.$token,
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
            'Content-Type: application/json',
//            'Wechatpay-Serial: '.$getEncrypt,
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    public static function addLOg($msg){
        $file = './log/wechatt/'.date('Y-m-d').'.text'; // 指定文件路径
        $message = '提现请求响应参数----->{}'.$msg; // 你要记录的日志内容
        file_put_contents($file, date('Y-m-d H:i:s') . ' ====== ' . $message . PHP_EOL, FILE_APPEND);
    }




    public static function pay(){

        $id = 0;
        $openId = 'o8G6d67tLhIsm8gtA65CICASSvOQ';
        $userName = '';
        //请求提现前，请求获取最新的微信商户证书、并保存到本地
//        self::certificates();

//        $appIdConfig = Db::name('config')->where('id',23)->find();
//        $withdrawOrder = Db::name('order_withdraw')->where('id',$id)->find();
        $body = [
            'appid'=>'wxb09899db31ded4ae',                     //小程序appid
            'out_bill_no'=>date('YmdHis').str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),          //提现订单本地单号
            'transfer_scene_id'=>'1005',                        //注意此处的场景id，
            'openid'=>$openId,                                  //收款用户姓名
            'notify_url'=>'https://fenzhang.xing-peng.com/api/lkl_notify/wxPayNotify',//通知地址
            'transfer_amount'=>0.3 * 100,   //提现金额
            'transfer_remark'=>'用户提现',                          //提现备注
            'transfer_scene_report_infos'=>[
                [
                    'info_type'=>'岗位类型',                    //需要取对应场景下的信息类型（有几个信息类型，则数组内对应有几个info_type）
                    'info_content'=>'分销人员'
                ],
                [
                    'info_type'=>'报酬说明',
                    'info_content'=>'佣金报酬'
                ]
            ]
        ];
        //微信规则限制、低于0.3不让传姓名，超过2000必须传
//        if ($userName != '' && $withdrawOrder['money'] > 0.3){
//            $body ['user_name'] =  self::encryptUserName($userName);
//        }
        $param = self::param_filter($body);
        $param = self::param_sort($param);
        $param = json_encode($param,JSON_UNESCAPED_UNICODE);
        /**
        $mchIdConfig = Db::name('config')->where('id',25)->find();
        $keyPathConfig = Db::name('config')->where('id',28)->find();
        $certPathConfig = Db::name('config')->where('id',29)->find();

        $config['mch_id'] = $mchIdConfig['value'];          //微信支付商户号
        $config['cert_key'] = $keyPathConfig['value'];      //微信支付证书key路径
        $config['cert_client'] = $certPathConfig['value'];  //微信支付证书cert路径
         */

        $config = [
            'cert_client' => app()->getRootPath().'public/wx/apiclient_cert.pem',
            'cert_key' => app()->getRootPath().'public/wx/apiclient_key.pem',
            'mch_id' => '1679226667',
        ];
        $url='https://api.mch.weixin.qq.com/v3/fund-app/mch-transfer/transfer-bills';

        $token = self::getToken($url,time(),$param,$config);
        $res_xml = self::https_request($url,$param,$token);
//        self::addLOg($res_xml);
        $res = json_decode($res_xml,true);
        dd($res);
        if(isset($res['state']) && $res['state'] == 'WAIT_USER_CONFIRM'){
            //待确认收款
            return $res['package_info'];
        }else{
            throw new Exception($res['message']);
        }
    }


    //用于请求“下载平台证书接口”、并将证书文件保存到本地、后续提现皆以微信支付平台证书来加密解密
    public static function certificates(){
//        dd(app()->getRootPath());
        $config = [
            'cert_client' => app()->getRootPath().'public/wx/apiclient_cert.pem',
            'cert_key' => app()->getRootPath().'public/wx/apiclient_key.pem',
            'mch_id' => '1679226667',
        ];
        $url='https://api.mch.weixin.qq.com/v3/certificates';
        $token = self::getToken($url,time(),'',$config,'GET');
        // 初始化 cURL
        $ch = curl_init($url);
        // 设置 cURL 请求头
        $headers = [
            'Authorization: WECHATPAY2-SHA256-RSA2048 ' . $token,
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        ];
        // 设置 cURL 选项
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,  // 返回响应结果而不是直接输出
            CURLOPT_HTTPHEADER => $headers,  // 设置请求头
            CURLOPT_SSL_VERIFYPEER => false, // 禁用 SSL 验证
            CURLOPT_SSL_VERIFYHOST => false, // 禁用 SSL 验证
        ]);
        // 执行 cURL 请求并获取响应
        $response = curl_exec($ch);

        // 检查 cURL 错误
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        // 关闭 cURL 句柄
        curl_close($ch);
        $response_data = json_decode($response,true);

//        dd($response_data);
        $ciphertext = $response_data['data'][0]['encrypt_certificate']['ciphertext'];
        $nonce = $response_data['data'][0]['encrypt_certificate']['nonce'];
        $associated_data = $response_data['data'][0]['encrypt_certificate']['associated_data'];
        $ciphertext = base64_decode($ciphertext);
        //参数KEY 是V3的密钥
        $decrypted = sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associated_data, $nonce, '0D4DF107D327685D36E99A623324C125');
        if ($decrypted){
            // 证书保存到本地文件
            $path = app()->getRootPath().'public/wx/shop_key.pem';
            file_put_contents($path, $decrypted);
        }else{
            throw new Exception('解密失败');
        }
    }

    //测试支付
    public function wxpay()
    {
        self::pay();
    }


    /**
     * 发起转账
     */
    public function transfer_bills()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $id = $this->params[ 'id' ];
        $withdraw = Db::name('member_withdraw')->where('id', $id)
            ->where('member_id', $this->member_id)
            ->find();
        if (!$withdraw) {
            return $this->response($this->error('', '提现记录不存在'));
        }
        if ($withdraw['status'] != 1) {
            return $this->response($this->error('', '不可发起收款'));
        }
        //发起收款
        $member = Db::name('member')->where('member_id', $this->member_id)->find();
        $openId = $member['weapp_openid'];
        $userName = '';
        //请求提现前，请求获取最新的微信商户证书、并保存到本地
        self::certificates();

        $body = [
            'appid'=>'wxb09899db31ded4ae',                     //小程序appid
            'out_bill_no'=>$withdraw['withdraw_no'],          //提现订单本地单号
            'transfer_scene_id'=>'1005',                        //注意此处的场景id，
            'openid'=>$openId,                                  //收款用户姓名
            'notify_url'=>'https://fenzhang.xing-peng.com/api/wx_pay/wxPayNotify',//通知地址
            'transfer_amount'=>(int)($withdraw['money'] * 100),   //提现金额
            'transfer_remark'=>'用户提现',                          //提现备注
            'transfer_scene_report_infos'=>[
                [
                    'info_type'=>'岗位类型',                    //需要取对应场景下的信息类型（有几个信息类型，则数组内对应有几个info_type）
                    'info_content'=>'分销人员'
                ],
                [
                    'info_type'=>'报酬说明',
                    'info_content'=>'佣金报酬'
                ]
            ]
        ];
        //微信规则限制、低于0.3不让传姓名，超过2000必须传
        if ($userName != '' && $withdraw['money'] > 0.3){
            $body ['user_name'] =  self::encryptUserName($userName);
        }
        $param = self::param_filter($body);
        $param = self::param_sort($param);
        $param = json_encode($param,JSON_UNESCAPED_UNICODE);

        $config = [
            'cert_client' => app()->getRootPath().'public/wx/apiclient_cert.pem',
            'cert_key' => app()->getRootPath().'public/wx/apiclient_key.pem',
            'mch_id' => '1679226667',
        ];
        $url='https://api.mch.weixin.qq.com/v3/fund-app/mch-transfer/transfer-bills';

        $token = self::getToken($url,time(),$param,$config);
        $res_xml = self::https_request($url,$param,$token);
//        self::addLOg($res_xml);
        $res = json_decode($res_xml,true);
        if(isset($res['state']) && $res['state'] == 'WAIT_USER_CONFIRM'){
            //待确认收款
            return $this->response($this->success([
                'package_info' => $res['package_info'],
            ]));
        }else{
            return $this->response($this->error('', '提现失败'));
        }
    }

    /**
     * 用户收款回调
     */
    public function wxPayNotify()
    {
        $dir = './log/wx_pay/'; //设置目录
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $data = file_get_contents('php://input'); //获取到加密的字符串
        $jd_time = strtotime('12:00');
        $time = time();
        if ($time <= $jd_time) { //上午数据
            file_put_contents($dir . date('Ymd', time()) . "_1.txt", var_export($data, true) . PHP_EOL, FILE_APPEND);
        } else { //下午数据
            file_put_contents($dir . date('Ymd', time()) . "_2.txt", var_export($data, true) . PHP_EOL, FILE_APPEND);
        }

        $body = $this->params;
        file_put_contents($dir . date('Ymd', time()) . "_3.txt", var_export($body, true) . PHP_EOL, FILE_APPEND);

        $resource = $body['resource'];//【通知数据】json格式
        $ciphertext = $resource['ciphertext'];//【数据密文】Base64编码后的商家转账结果数据密文
        $associated_data = $resource['associated_data'];//【附加数据】附加数据
        $nonce = $resource['nonce'];//【随机串】加密使用的随机串
        $ciphertext = base64_decode($ciphertext);
        $decrypted = sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associated_data, $nonce, '0D4DF107D327685D36E99A623324C125');
        //解密业务信息
        $business_data = json_decode($decrypted, true);

        if ($business_data['state'] == 'SUCCESS') {
            //打款成功
            $withdraw_model = new MemberWithdraw();
            $withdraw = Db::name('member_withdraw')->where('withdraw_no', $business_data['out_bill_no'])
                ->find();
            if ($withdraw) {
                $result = $withdraw_model->transferFinish([ [ "id", "=", $withdraw['id'] ] ]);
            }

        }

        if (in_array($business_data['state'], ['FAIL','CANCELING','CANCELLED'])) {
            //超时未确认收款，自动取消
            $withdraw = Db::name('member_withdraw')->where('withdraw_no', $business_data['out_bill_no'])
                ->find();
            if ($withdraw) {
                $data = array (
                    "status" => -1,
                    "status_name" => "已拒绝",
                    "refuse_reason" => '',
                    "audit_time" => time(),
                );
                $condition = array(
                    ["id", "=", $withdraw['id']]
                );
                model("member_withdraw")->update($data, $condition);
//                $member = Db::name('member')->where('member_id', $withdraw['member_id'])->find();

                //减少可提现余额
                model("member")->setInc([ [ "member_id", "=", $withdraw[ "member_id" ] ] ], "balance_money", $withdraw[ "apply_money" ]);
                //增加提现中余额
                model("member")->setDec([ [ "member_id", "=", $withdraw[ "member_id" ] ] ], "balance_withdraw_apply", $withdraw[ "apply_money" ]);
            }
        }

    }
}