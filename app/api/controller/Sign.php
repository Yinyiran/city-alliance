<?php


namespace app\api\controller;

use app\model\member\Config as ConfigModel;
use think\facade\Config;
use think\facade\Db;

use think\Request;
use app\model\signin\Rules as RulesModel;
use app\model\signin\Records as MemberRecordModel;
use app\model\shop\Shop as ShopModel;
use app\model\member\Member as MemberModel;
use addon\weapp\model\Weapp;

/**
 * 商家签到
 */
class Sign extends BaseApi
{
    protected $rules_type = [
        ['type_id' => 1, 'type_name' => "积分"],
        ['type_id' => 2, 'type_name' => "现金"],
        ['type_id' => 3, 'type_name' => "礼品"]
    ];
    protected $modelrules;
    protected $memberrecordmodel;
    protected $shopmodel;
    protected $membermodel;
    /**
     * 构造方法
     * Sign constructor.
     */
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->modelrules = new RulesModel();
        $this->memberrecordmodel = new MemberRecordModel();
        $this->shopmodel = new ShopModel();
        $this->membermodel = new MemberModel();
        
    }
    

    /**
     * 用户签到详情
     */
    public function getsignininfo(Request  $request){
           $token = $this->checkToken();
            if ($token[ 'code' ] < 0) return $this->response($token);
            $member_id=$token[ 'data' ][ 'member_id' ];
            $condition = [];
            if(empty($member_id)) {
               return $this->response(["code"=>-101,"msg"=>"参数错误"]);
            }
            $result = $this->modelrules->getRulesList($member_id);
            
            if($result["code"]==0){
                 return $this->response(["code"=>$result["code"],"message"=>$result["message"],"data"=>$result["data"]["list"],"signcount"=>$result["data"]["signcount"]]);
            }else{
                return $this->response(["code"=>$result["code"],"message"=>$result["message"]]);
            }
           
           
    }

    /**
     * 用户签到
     */
    /**
     * 用户签到码
     */
    public function getqrcode(Request $request)
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $member_id = $this->member_id;

        if (empty($member_id)) {
            return $this->response(['code' => -1, 'message' => '未获取到用户信息']);
        }

        // 优先检查已存在的签到码缓存（必须物理文件真实存在且大于 1KB，避免微信错误 JSON）
        $member = Db::name("member")->field("shop_sign_qrcode")->where("member_id", $member_id)->find();
        if (!empty($member["shop_sign_qrcode"])) {
            $existing_path = ltrim($member["shop_sign_qrcode"], '/');
            if (file_exists($existing_path) && filesize($existing_path) > 1024) {
                return $this->response([
                    'code' => 0,
                    'message' => '获取成功',
                    'filepath' => $existing_path
                ]);
            }
        }

        $qrcode_dir = 'upload/qrcode/sign';
        if (!is_dir($qrcode_dir)) {
            mkdir($qrcode_dir, 0755, true);
        }

        $filename = 'sign_' . $member_id . '.png';
        $full_relative_path = $qrcode_dir . '/' . $filename;
        $is_success = false;

        // 1. 优先尝试使用微信小程序官方接口生成小程序码
        try {
            $weapp = new Weapp();
            $param = [
                'data' => [
                    'member_id' => $member_id
                ],
                'page' => 'otherpages/signCode/signCode',
                'scene' => 'member_id=' . $member_id,
                'width' => 280,
                'qrcode_path' => $qrcode_dir,
                'qrcode_name' => 'sign_' . $member_id,
                'app_type' => 'weapp'
            ];
            $result = $weapp->createQrcode($param);
            if ($result['code'] == 0 && !empty($result['data']['path'])) {
                $saved_path = ltrim($result['data']['path'], '/');
                if (file_exists($saved_path) && filesize($saved_path) > 1024) {
                    $full_relative_path = $saved_path;
                    $is_success = true;
                }
            }
        } catch (\Exception $e) {
            // 微信接口异常或配置不全
        }

        // 2. 兜底方案：如果微信接口失败（例如 token 失效、小程序页面未发布、微信未配置等）
        // 使用内置的本地 QRcode 生成标准二维码，保证商户端扫码依然可用
        if (!$is_success) {
            try {
                $qr_content = "member_id=" . $member_id;
                \extend\QRcode::png($qr_content, $full_relative_path, 'L', 6, 1);
                if (file_exists($full_relative_path) && filesize($full_relative_path) > 100) {
                    $is_success = true;
                }
            } catch (\Exception $e) {
                return $this->response([
                    'code' => -1,
                    'message' => '二维码生成失败: ' . $e->getMessage()
                ]);
            }
        }

        if ($is_success) {
            Db::name("member")->where(["member_id" => $member_id])->update(["shop_sign_qrcode" => $full_relative_path]);
            return $this->response([
                'code' => 0,
                'message' => '获取成功',
                'filepath' => $full_relative_path
            ]);
        }

        return $this->response([
            'code' => -1,
            'message' => '二维码生成失败'
        ]);
    }

    //商户扫一扫 用户签到  商户通过扫描二维码得到二维码信息
   //修改表记录：member  新增shop_sign_days
   //修改表记录：sign_member_record  新增rewardtxt
    public function dosignin(Request $request){
        if($request->has("member_id")&$request->has("shop_id")){//如果有签到天数和签到规则id  则说明用户已经签到过了  则提示用户签到天数已超过
            $member_id=$request->param("member_id");//商户id
            $shop_id=$request->param("shop_id");//商户id
        }else{
            return $this->response(["code"=>-101,"参数错误"]);
        }
        
        $result=$this->memberrecordmodel->shopsignin($member_id,$shop_id);//调用签到方法  并返回结果
        
        return $this->response(["code"=>$result["code"],"message"=>$result["message"]]);

       
    }
}
