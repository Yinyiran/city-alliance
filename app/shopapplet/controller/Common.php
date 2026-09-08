<?php
/**

 */

namespace app\shopapplet\controller;


use addon\city\admin\controller\Shop;
use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use think\facade\Db;


class Common extends BaseApi
{
    public $employeeModel;
    public $shopModel;
    public $member_id;
    public $site_id;
    public $city_id;
    public $shopinfo;

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->employeeModel = new ShopEmployeeModel();
        $this->shopModel=new ShopModel();

        if(empty(request()->param("token"))){
           echo  $this->response([
              'code' => -1,
              'msg' => 'token不能为空',
              'data' => [],
          ]);
            exit ;
        }
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0){
          echo $this->response($token);
          exit;
        }
        $this->member_id=$token[ 'data' ][ 'member_id' ];
        $memberinfo=Db::name("member")->where("member_id=".$this->member_id)->find();
        if(!empty($memberinfo)){
          $mobile=$memberinfo["mobile"];
        }

        //增加传递site_id
        $site_id = $this->params['site_id'] ?? 0;
        if ($site_id) {
            $employeeinfo=$this->employeeModel->getInfo([ ['mobile', '=', $mobile ], ['is_deleted', '=', 0], ['shop_id', '=', $site_id] ],'member_id,shop_id');
        } else {
            $employeeinfo=$this->employeeModel->getInfo([ ['mobile', '=', $mobile ], ['is_deleted', '=', 0] ],'member_id,shop_id');
        }

        if(empty($employeeinfo)){
          echo $this->response([
              'code' => -1,
              'msg' => '员工不存在',
              'data' => [],
          ]);
          exit;
        }
        if(empty($employeeinfo["member_id"])){
            Db::name("shop_employee")->where("mobile",$mobile)->update(["member_id"=>$this->member_id]);
        }
      

        $result=$this->getshopInfobyemployee();
       
        $this->site_id = $result["site_id"] ?? 0;
        $this->city_id = $result["city_id"] ?? 0;
        $this->shopinfo = $result["shopinfo"] ?? [];

        session("shopinfo",$result["shopinfo"]);
        session("city_id",$result["city_id"]);
        session("site_id",$result["site_id"]);
        session("shop_id",$result["site_id"]);
        session("employee_info",$result["employee_info"]);


    }

    public function getshopInfobyemployee(){
      $emptoyee_info=$this->employeeModel->getInfo([ ['member_id', '=', $this->member_id ],['is_deleted', '=', 0 ] ],'member_id,shop_id');
      if(empty($emptoyee_info)){
        return $this->response([
            'code' => -1,
            'msg' => '员工不存在',
            'data' => [],
        ]);
     }
      $site_id=$emptoyee_info["shop_id"];
     
       $shopdata=Db::name("shop")->where("site_id",$site_id)->find();

     if(empty($shopdata)){
        return $this->response([
            'code' => -1,
            'msg' => '店铺不存在',
            'data' => [],
        ]);
     }

       $this->shopinfo=$shopdata;
       $this->city_id=$this->shopinfo["city"];
       $result=[
        "shopinfo"=>$this->shopinfo,
        "city_id"=>$this->city_id,
        "site_id"=>$site_id,
        'employee_info'=>$emptoyee_info,
       ];
       return $result;
      

    }
}
