<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use Carbon\Carbon;
use app\model\shopapplet\ShopDeposit as ShopDepositModel;
use think\facade\Db;

/**
 * 
 */
class Shop extends Common
{  
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->site_id=session("site_id");
    
    }
    
    /**
     * 缴纳保证金按钮
     */
    public function deposit(){
        $shop_model=new ShopModel();
        $shopinfo=$shop_model->getDetail($this->site_id);
        if(empty($shopinfo)){
            return $this->error('', '店铺不存在');

        }
        $data=["site_id"=>$this->site_id,"member_id"=>$this->member_id];

        $res=$this->ordercreate($data);

        if(!empty($res)){
            $res=json_decode($res,true);
        }else{
            $res=['data'=>''];
        }
        $shopinfo["out_trade_no"]=$res["data"];
        $website = model('website')->getInfo(['site_id'=>$shopinfo['data']['website_id']]);
        $shopinfo['data']['deposit_amount']=$website['shop_deposit_amount'] ?? 0;
        return $this->response($shopinfo);
    }

    /**
     * 缴纳保证金
     */
    public function ordercreate($data){
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $shop_deposit_model = new ShopDepositModel();
        $result = $shop_deposit_model->ordercreate($data);

        return $this->response($result);
    }

    /**
     * 商户基本信息
     */
    public function getInfo(){
        $shop_model=new ShopModel();
        $site_id=request()->param("site_id");
        if(empty($site_id)){
            $site_id=$this->site_id;
        }
        $shopinfo=Db::name("shop")->alias("s")->join("shop_employee se","se.employee_id=s.zlr_employee_id","left")
        ->field("s.*,se.username as employee_username,se.mobile as employee_mobile,se.headimg as employee_headimg")
        ->where("s.site_id",$site_id)
        ->find();
        if(empty($shopinfo)){
            return $this->response($this->error('', '店铺不存在'));
        }
        $myinfo=Db::name("shop_employee")->where("member_id",$this->member_id)->find();
        if(empty($myinfo)){
            $myinfo=[];
        }
        $shopinfo["myinfo"]=$myinfo;
        return $this->response($this->success($shopinfo));

    }
}