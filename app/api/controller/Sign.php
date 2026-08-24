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
    public function getqrcode(Request $request){
        require_once('./vendor/Weixin_1/WeapiOauth.php');
        require_once('./vendor/Weixin_1/WXBizDataCrypt.php');
        /*$token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $member_id=$token[ 'data' ][ 'member_id' ];*/
        $member_id=2;
         $currentUrl = "http://".$_SERVER['HTTP_HOST'];
        $member=Db::name("member")->field("shop_sign_qrcode")->where("member_id",$member_id)->find();
      
        if(!empty($member["shop_sign_qrcode"])){
           $result=[
            'code'=>0,
            "filepath"=>$member["shop_sign_qrcode"]
           ];
            return $this->response($result);
        }
         // 获取当前完整的URL
        
         $oauth = new \Weixin_1\WeapiOauth();
         $scene="member_id=".$member_id;
         $page="otherpages/signCode/signCode"; //地址待定
         $data = '{"scene":"' . $scene . '","page":"' . $page . '","width":280}';
         $appID="wx78e3faf3a96aadd2";
         $appSecret="7414c95568a5ca4277cbc3b5e24da7bb";
         $result=$oauth->create_qrcode($data,$appID,$appSecret);
         $filename = "qrcode_" . $member_id . ".jpg";//要生成的图片名字
         $IMG_PATH = "/public/qrcode";//保存服务器路径
        if (file_exists('./'.$IMG_PATH .'/'. $filename)) { //存在返回图片路径
            $filepath=$IMG_PATH .'/'. $filename;
            $res=Db::name("member")->where(["member_id"=>$member_id])->update(["shop_sign_qrcode"=>$filepath]);
              $result=[
                'code'=>0,
                "filepath"=>$member["shop_sign_qrcode"]
            ];
            return $this->response($result);
        }
        $res = file_put_contents('./'.$IMG_PATH .'/'. $filename,$result);
        $filepath=$IMG_PATH .'/'. $filename;
        $res=Db::name("member")->where(["member_id"=>$member_id])->update(["shop_sign_qrcode"=>$filepath]);
        $response=[
            'code'=>0,
            "filepath"=>$member["shop_sign_qrcode"]
        ];
        return $this->response($response);
        
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
