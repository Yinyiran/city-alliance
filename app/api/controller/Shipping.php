<?php


namespace app\api\controller;

use addon\weapp\model\Config as WeappConfigModel;
use EasyWeChat\Factory;
use think\facade\Db;

/**
 * 小程序发货信息管理服务
 * Class Shipping
 * @package app\api\controller
 */
class Shipping
{
    /**
     * 发货信息录入接口
     */
    public function uploadShippingInfo($order_id)
    {
        $order = model('order')->getInfo(['order_id'=>$order_id]);
        $pay = model('pay')->getInfo(['out_trade_no'=>$order['out_trade_no']]);
        if (!empty($pay['trade_no'])) {
            $shipping_list = [];
            $items = Db::name('order_goods')->where('order_id', $order_id)->select();
            $shipping_list[] = [
                'item_desc' => str_sub($items[0]['goods_name'], 95).'...', //商品信息
            ];

            //判断支付人openid
            if ($order['pay_member_id']) {
                $openid = Db::name('member')->where('member_id', $order['pay_member_id'])->value('weapp_openid');
            } else {
                $openid = Db::name('member')->where('member_id', $order['member_id'])->value('weapp_openid');
            }

            $request_data = [
                'order_key' => [ //订单，需要上传物流信息的订单
                    'order_number_type' => 2, //订单单号类型，用于确认需要上传详情的订单。枚举值1，使用下单商户号和商户侧单号；枚举值2，使用微信支付单号。
                    'transaction_id' => $pay['trade_no'], //商户系统内部订单号，只能是数字、大小写字母`_-*`且在同一个商户号下唯一
                ],
                'logistics_type' => 4, //物流模式，发货方式枚举值：1、实体物流配送采用快递公司进行实体物流配送形式 2、同城配送 3、虚拟商品，虚拟商品，例如话费充值，点卡等，无实体配送形式 4、用户自提
                'delivery_mode' => 1, //发货模式，发货模式枚举值：1、UNIFIED_DELIVERY（统一发货）2、SPLIT_DELIVERY（分拆发货）
                'shipping_list' => $shipping_list,
                'upload_time' => self::getTime(),
                'payer' => [
                    'openid' => $openid,
                ]
            ];
            $result = $this->uploadShippingInfoApi($request_data);
            return $result;
        }

    }

    /**
     * 快递发货
     */
    public function expressShippingInfo($order_id, $order_goods_id_array, $delivery_no,$express_company_id)
    {
        $order = model('order')->getInfo(['order_id'=>$order_id]);
        $pay = model('pay')->getInfo(['out_trade_no'=>$order['out_trade_no']]);
        if (!empty($pay['trade_no']) && $order['order_type'] == 1) {
            $shipping_list = [];

            $express_company_template = model('express_company_template')->getInfo(['company_id'=>$express_company_id]);

//            dd($express_company_template);


            foreach ($order_goods_id_array as $key => $value) {
                $order_goods = Db::name('order_goods')->where('order_goods_id', $value)->find();
                $shipping_list[] = [
                    'tracking_no' => $delivery_no, //物流单号，物流快递发货时必填
                    'express_company' => $express_company_template['delivery_id'], //物流公司编码，快递公司ID
                    'item_desc' => str_sub($order_goods['sku_name'], 100), //商品信息
                    'contact' => [ //联系方式
                        'receiver_contact' => $order['mobile'] ?? '', //收件人联系方式
                    ],
                ];
            }

            $request_data = [
                'order_key' => [ //订单，需要上传物流信息的订单
                    'order_number_type' => 2, //订单单号类型，用于确认需要上传详情的订单。枚举值1，使用下单商户号和商户侧单号；枚举值2，使用微信支付单号。
                    'transaction_id' => $pay['trade_no'], //商户系统内部订单号，只能是数字、大小写字母`_-*`且在同一个商户号下唯一
                ],
                'logistics_type' => 1, //物流模式，发货方式枚举值：1、实体物流配送采用快递公司进行实体物流配送形式 2、同城配送 3、虚拟商品，虚拟商品，例如话费充值，点卡等，无实体配送形式 4、用户自提
                'delivery_mode' => 1, //发货模式，发货模式枚举值：1、UNIFIED_DELIVERY（统一发货）2、SPLIT_DELIVERY（分拆发货）
                'shipping_list' => $shipping_list,
                'upload_time' => self::getTime(),
                'payer' => [
                    'openid' => Db::name('member')->where('member_id', $order['member_id'])->value('weapp_openid'),
                ]
            ];
            $result = $this->uploadShippingInfoApi($request_data);
            return $result;
        }
    }

    /**
     * 虚拟商品
     */
    public function virtualShippingInfo($order, $type)
    {

        $pay = model('pay')->getInfo(['out_trade_no'=>$order['out_trade_no']]);
        if (!empty($pay['trade_no'])) {

            $shipping_list = [];
            $shipping_list[] = [
                'item_desc' => str_sub($pay['pay_body'], 95), //商品信息
            ];

            //判断支付人openid
            if (!empty($order['pay_member_id'])) {
                $openid = Db::name('member')->where('member_id', $order['pay_member_id'])->value('weapp_openid');
            } else {
                $openid = Db::name('member')->where('member_id', $order['member_id'])->value('weapp_openid');
            }

            //按照支付时候传递的type
            if ($type == 1) { //商品订单

            } elseif ($type == 2) { //余额充值回调

            } elseif ($type == 3) { //礼品卡回调

            } elseif ($type == 4) { //专家会员升级回调

            }

            $request_data = [
                'order_key' => [ //订单，需要上传物流信息的订单
                    'order_number_type' => 2, //订单单号类型，用于确认需要上传详情的订单。枚举值1，使用下单商户号和商户侧单号；枚举值2，使用微信支付单号。
                    'transaction_id' => $pay['trade_no'], //商户系统内部订单号，只能是数字、大小写字母`_-*`且在同一个商户号下唯一
                ],
                'logistics_type' => 3, //物流模式，发货方式枚举值：1、实体物流配送采用快递公司进行实体物流配送形式 2、同城配送 3、虚拟商品，虚拟商品，例如话费充值，点卡等，无实体配送形式 4、用户自提
                'delivery_mode' => 1, //发货模式，发货模式枚举值：1、UNIFIED_DELIVERY（统一发货）2、SPLIT_DELIVERY（分拆发货）
                'shipping_list' => $shipping_list,
                'upload_time' => self::getTime(),
                'payer' => [
                    'openid' => $openid,
                ]
            ];
            $result = $this->uploadShippingInfoApi($request_data);
            return $result;
        }
    }

    //上传时间
    public static function getTime()
    {
        $date = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $date->format("Y-m-d\TH:i:s.vP");
    }

    //发货信息录入接口
    public function uploadShippingInfoApi($request_data)
    {
        //微信小程序配置
        $weapp_config_model = new WeappConfigModel();
        $weapp_config       = $weapp_config_model->getWeappConfig(1);
        $weapp_config       = $weapp_config["data"]["value"];
        $config    = [
            'app_id'        => $weapp_config["appid"] ?? '',
            'secret'        => $weapp_config["appsecret"] ?? '',
            'response_type' => 'array',
            'log'           => [
                'level'      => 'debug',
                'permission' => 0777,
                'file'       => 'runtime/log/wechat/easywechat.logs',
            ],
        ];
        $app = Factory::miniProgram($config);
        $access_token = $app->access_token->getToken()['access_token'];

        $url = 'https://api.weixin.qq.com/wxa/sec/order/upload_shipping_info?access_token=' . $access_token;
        $request_data = json_encode($request_data, JSON_UNESCAPED_UNICODE);
        $result = $this->httpRequest($url, 'POST', $request_data);
        if ($result['errcode'] == 0) {
            return ['code' => 0, 'msg' => 'ok'];
        } else {
            return ['code' => 1, 'msg' => $result['errmsg'] ?? '系统有误'];
        }
    }
    
    //
    /*public function getDeliveryList()
    {
        $weapp_config_model = new WeappConfigModel();
        $weapp_config       = $weapp_config_model->getWeappConfig(1);
        $weapp_config       = $weapp_config["data"]["value"];
        $config    = [
            'app_id'        => $weapp_config["appid"] ?? '',
            'secret'        => $weapp_config["appsecret"] ?? '',
            'response_type' => 'array',
            'log'           => [
                'level'      => 'debug',
                'permission' => 0777,
                'file'       => 'runtime/log/wechat/easywechat.logs',
            ],
        ];
        $app = Factory::miniProgram($config);
        $access_token = $app->access_token->getToken()['access_token'];
        $url = 'https://api.weixin.qq.com/cgi-bin/express/delivery/open_msg/get_delivery_list?access_token=' . $access_token;
        $result = $this->httpRequest($url, 'POST', '{}');
        foreach ($result['delivery_list'] as $key => $value) {
            model('delivery')->add([
                'delivery_id' => $value['delivery_id'],
                'delivery_name' => $value['delivery_name'],
            ]);
        }
        dd($result);
    }*/

    public function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
        $method = strtoupper($method);
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
        curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }
        $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
        curl_setopt($ci, CURLOPT_URL, $url);
        if($ssl){
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
        }
        //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
        curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
        $response = curl_exec($ci);
        $requestinfo = curl_getinfo($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);
            echo "=====info===== \r\n";
            print_r($requestinfo);
            echo "=====response=====\r\n";
            print_r($response);
        }
        curl_close($ci);
        return json_decode($response,true);
        //return array($http_code, $response,$requestinfo);
    }
}