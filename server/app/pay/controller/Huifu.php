<?php


namespace app\pay\controller;

use app\Controller;
use BsPaySdk\core\BsPay;
use BsPaySdk\core\BsPayTools;
use think\Db;

require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/core/BsPay.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/core/BsPayTools.php';
require_once  root_path() . '/extend/dg-php-sdk/BsPaySdk/core/BsPayRequestV2.php';

class Huifu extends Controller
{
    /**
     * 商城回调
     */
    public function shopnotify()
    {
        \think\facade\Log::error(456);
        \think\facade\Log::error('店铺用户业务入驻回调');
        \think\facade\Log::error(json_encode(input(),320));
        $dir = './log/shop/'; //设置目录
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
        $resp_data = $result['data'];
        $res = BsPayTools::verifySign(
            $result['sign'],
            $resp_data, $merConfig->rsa_huifu_public_key);

        if ($res) { //验签成功
            $resp_data = json_decode($resp_data, true);
            if (!empty($resp_data['sub_resp_code']) && $resp_data['sub_resp_code'] == '00000000') {
                $audit_info = json_decode($resp_data['audit_info'], true);
                $req_seq_id = $resp_data['req_seq_id'];//原请求流水号
                $audit_status = $audit_info['audit_status']; //Y:审核通过 P:审核中 N:审核拒绝
                $audit_desc = $audit_info['audit_desc '];//审核意见
                $apply_no = $audit_info['apply_no'];//申请单号
                $token_no = $audit_info['token_no'];//取现卡序列号，交易时使用；

                $open_account = model('open_account')->getInfo(['req_seq_id'=>$req_seq_id,'apply_no'=>$apply_no],'site_id,apply_status');

                if ($open_account['apply_status']!=1){
                    return false;
                }

                $data = [
                    'apply_reason'=>$audit_desc,
                    'token_no'=>$token_no
                ];
                if ($audit_status=='Y'){
                    $data['apply_status'] = 2;
                } elseif ($audit_status=='N'){
                    $data['apply_status'] = 3;
                }
                $res = model('open_account')->update($data,[['req_seq_id','=',$req_seq_id],['apply_no','=',$apply_no]]);
            }
        }

        //返回处理完成信息
        return true;
    }

    //用户结算卡回调
    public function membernotify()
    {
        \think\facade\Log::error(123);
        \think\facade\Log::error('用户业务入驻回调');
        \think\facade\Log::error(json_encode(input(),320));
        $dir = './log/member/'; //设置目录
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
        $resp_data = $result['data'];
        $res = BsPayTools::verifySign(
            $result['sign'],
            $resp_data, $merConfig->rsa_huifu_public_key);

        if ($res) { //验签成功
            $resp_data = json_decode($resp_data, true);
            if (!empty($resp_data['sub_resp_code']) && $resp_data['sub_resp_code'] == '00000000') {
                $audit_info = json_decode($resp_data['audit_info'], true);
                $req_seq_id = $resp_data['req_seq_id'];//原请求流水号
                $audit_status = $audit_info['audit_status']; //Y:审核通过 P:审核中 N:审核拒绝
                $audit_desc = $audit_info['audit_desc '];//审核意见
                $apply_no = $audit_info['apply_no'];//申请单号
                $token_no = $audit_info['token_no'];//取现卡序列号，交易时使用；

                $open_account = model('member_open_account')->getInfo(['req_seq_id'=>$req_seq_id,'apply_no'=>$apply_no],'member_id,apply_status');

                if ($open_account['apply_status']!=1){
                    return false;
                }

                $data = [
                    'apply_reason'=>$audit_desc,
                    'token_no'=>$token_no
                ];
                if ($audit_status=='Y'){
                    $data['apply_status'] = 2;
                } elseif ($audit_status=='N'){
                    $data['apply_status'] = 3;
                }
                $res = model('member_open_account')->update($data,[['req_seq_id','=',$req_seq_id],['apply_no','=',$apply_no]]);
            }
        }

        //返回处理完成信息
        return true;
    }
}