<?php


namespace app\api\controller;

use app\api\controller\v1\Task;
use app\api\model\ProductOrder;
use app\model\order\OrderCommon;
use BsPaySdk\core\BsPay;
use BsPaySdk\core\BsPayTools;
use think\facade\Db;

require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/BsPaySdk/init.php';
require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/BsPaySdk/request/V2TradePaymentJspayRequest.php';
require_once app()->getRootPath() . 'extend/huifupay/vendor/huifurepo/dg-php-sdk/Composer/BsPayConfig.php';

class Notify
{
    /**
     * 订单回调
     */
    public function orderNotify()
    {
        $dir = './log/order_notify/'; //设置目录
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


        //验签
        $str = $data;
        $data = urldecode($str);
        parse_str($data, $result);
        $result = str_replace(' ', '+', $result);
        $merConfig = BsPay::getConfig();
        $resp_data = $result['resp_data'];
        $res = BsPayTools::verifySign(
            $result['sign'],
            $resp_data, $merConfig->rsa_huifu_public_key);

        if ($res) { //验签成功
            $resp_data = json_decode($resp_data, true);
            if (!empty($resp_data['resp_code']) && $resp_data['resp_code'] == '00000000' && $resp_data['trans_stat'] == 'S') { //支付成功
                $order_no = $resp_data['req_seq_id'];
                $order_id = Db::name('order_no')->where('order_no', $order_no)->find();
                if ($order_id) {
                    $order = Db::name('order')->where('order_id', $order_id['order_id'])->find();
                    $payModel = model('pay')->getInfo(['out_trade_no' => $order['out_trade_no']]);
                    if ($payModel && $payModel['pay_status'] == 0) {
                        model('pay')->update([
                            'hf_seq_id'=>$resp_data['hf_seq_id'],
                            'req_seq_id'=>$resp_data['req_seq_id'],
                            'req_date'=>$resp_data['req_date'],
                            'trade_no' => $resp_data['out_trans_id'],
                            'pay_type' => 'HUIFU',
                            'pay_status' => 2,
                            'pay_time' => time()
                        ], ['id' => $payModel['id']]);
                        $data = [
                            'out_trade_no' => $payModel['out_trade_no'],
                            'pay_type' => 'HUIFU',
                        ];
                        $res = (new OrderCommon())->orderOnlinePay($data);
                        return 'RECV_ORD_ID_'.$resp_data['req_seq_id'];
                    }


                }
            }
        }

        //返回处理完成信息
        return 'RECV_ORD_ID_'.$resp_data['req_seq_id'];

    }

    /**
     * 开通会员回调
     */
    public function memberLevelNotify()
    {
        $dir = './log/member_level_notify/'; //设置目录
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


        //验签
        $str = $data;
        $data = urldecode($str);
        parse_str($data, $result);
        $result = str_replace(' ', '+', $result);
        $merConfig = BsPay::getConfig();
        $resp_data = $result['resp_data'];
        $res = BsPayTools::verifySign(
            $result['sign'],
            $resp_data, $merConfig->rsa_huifu_public_key);

        if ($res) { //验签成功
            $resp_data = json_decode($resp_data, true);
            if (!empty($resp_data['resp_code']) && $resp_data['resp_code'] == '00000000' && $resp_data['trans_stat'] == 'S') { //支付成功
                $order_no = $resp_data['req_seq_id'];
                $order_id = Db::name('order_no')->where('order_no', $order_no)->find();
                if ($order_id) {
                    $order = Db::name('member_level_order')->where('order_id', $order_id['order_id'])->find();
                    $payModel = model('pay')->getInfo(['out_trade_no' => $order['out_trade_no']]);
                    if ($payModel && $payModel['pay_status'] == 0) {
                        model('pay')->update([
                            'hf_seq_id'=>$resp_data['hf_seq_id'],
                            'req_seq_id'=>$resp_data['req_seq_id'],
                            'req_date'=>$resp_data['req_date'],
                            'trade_no' => $resp_data['out_trans_id'],
                            'pay_type' => 'HUIFU',
                            'pay_status' => 2,
                            'pay_time' => time()
                        ], ['id' => $payModel['id']]);
                        $data = [
                            'out_trade_no' => $payModel['out_trade_no'],
                            'pay_type' => 'HUIFU',
                        ];
                        (new \app\model\member\MemberLevel())->orderPay($data);
                        return 'RECV_ORD_ID_'.$resp_data['req_seq_id'];
                    }


                }
            }
        }

        //返回处理完成信息
        return 'RECV_ORD_ID_'.$resp_data['req_seq_id'];
    }

    /**
     * 商家交保证金回调
     */
    public function shopdepositNotify()
    {
        $dir = './log/member_level_notify/'; //设置目录
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


        //验签
        $str = $data;
        $data = urldecode($str);
        parse_str($data, $result);
        $result = str_replace(' ', '+', $result);
        $merConfig = BsPay::getConfig();
        $resp_data = $result['resp_data'];
        $res = BsPayTools::verifySign(
            $result['sign'],
            $resp_data, $merConfig->rsa_huifu_public_key);

        if ($res) { //验签成功
            $resp_data = json_decode($resp_data, true);
            if (!empty($resp_data['resp_code']) && $resp_data['resp_code'] == '00000000' && $resp_data['trans_stat'] == 'S') { //支付成功
                $order_no = $resp_data['req_seq_id'];
                $order_id = Db::name('order_no')->where('order_no', $order_no)->find();
                if ($order_id) {
                    $order = Db::name('shop_deposit_order')->where('order_id', $order_id['order_id'])->find();
                    $payModel = model('pay')->getInfo(['out_trade_no' => $order['out_trade_no']]);
                    if ($payModel && $payModel['pay_status'] == 0) {
                        model('pay')->update([
                            'hf_seq_id'=>$resp_data['hf_seq_id'],
                            'req_seq_id'=>$resp_data['req_seq_id'],
                            'req_date'=>$resp_data['req_date'],
                            'trade_no' => $resp_data['out_trans_id'],
                            'pay_type' => 'HUIFU',
                            'pay_status' => 2,
                            'pay_time' => time()
                        ], ['id' => $payModel['id']]);
                        $data = [
                            'out_trade_no' => $payModel['out_trade_no'],
                            'pay_type' => 'HUIFU',
                        ];
                        (new \app\model\shopapplet\ShopDeposit())->orderPay($data);
                        return 'RECV_ORD_ID_'.$resp_data['req_seq_id'];
                    }


                }
            }
        }

        //返回处理完成信息
        return 'RECV_ORD_ID_'.$resp_data['req_seq_id'];
    }
}