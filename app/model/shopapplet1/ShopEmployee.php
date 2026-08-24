<?php

/**

 */

namespace app\model\shopapplet;

use app\model\BaseModel;
use app\model\system\Stat;
use think\facade\Db;
use app\model\system\Address;

/**
 * 店铺员工表
 */
class ShopEmployee extends BaseModel
{

    /**
     * 获取团队列表
     * params:member_id 会员id 
     * return array
     */
    public function getTeamMember($member_id,$shop_id,$member_level,$page,$searchtxt,$page_size = PAGE_LIST_ROWS)
    {
       $where="se.shop_id=".$shop_id." and se.is_deleted=0 and m.is_delete=0";
        if(!empty($member_level)){
            $where.=" and m.member_level=".$member_level;
        }
        if(!empty($searchtxt)){
            $where=" mobile like '%".$searchtxt."%' or m.nickname like '%".$searchtxt."%'";
        }
        $alias = "m";
        $join[] =  ["shop_employee se", "se.member_id=m.fenxiao_id", "inner"];
        $field="m.*";
        $order="m.reg_time desc";
        //$res=model("member")->pageList($condition, $field , $order, $page , $page_size, $alias, $join);
        $res=Db::name('member')->alias("m")
            ->field("m.*")
            ->join("fenxiao f","f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se","se.member_id=f_parent.member_id")
            ->where($where)
            ->paginate($page_size);
            
        $data = [
            "list" => $res->items(),
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size)
        ];
        return $this->success($data);
    }

    public function ismanager($member_id){
        $res=Db::name("shop_employee")->where("member_id",$member_id)->value("is_manager");
        if(empty($res)){
            $res=0;
        }
        return $res;

    }

    /**
     * 获取团队人数
     */
    public function getTeamCount($member_id,$shop_id){
        $condition = [
            ["shop_id", "=", $shop_id],
            ["is_deleted", "=", 0]
        ];
        $alias = "m";
        $join[] =  ["shop_employee se", "se.member_id=m.fenxiao_id", "inner"];
        $field="m.*";
        $order="m.reg_time desc";
       
        $field="count(m.member_id) as total_count";
        $total=model("member")->getList($condition, $field , $order, $alias, $join);
      
        $joinlevel[] =["member m","m.member_level=ml.level_id","left"];
        $joinlevel[] =["(select member_id,shop_id from `shop_employee` where shop_id=".$shop_id." and is_deleted=0) se","se.member_id=m.member_id","left"];
        $group="ml.level_id,level_name";
        $field="count(m.member_id) as counts,level_id,ml.level_name";

        $order="ml.sort asc";
        $alias="ml";
      
        $leveltotal=model("member_level")->getList([], $field , $order, $alias, $joinlevel,$group);
        $result=[
            'total_count'=>$total[0]["total_count"]??0,
            'leveltotal'=>$leveltotal,

        ];
        return $result;
    }
    

    /**
     * 获取登录员工信息
     */
    public function getInfo($condition){
        $info=model("shop_employee")->getInfo($condition);
        return $info;
    }

    /**
     * 新增员工
     */
    public function adds($params,$by_member_id){
        $getId=$this->getById($params["empid"]);
        if($getId){
            return $this->error("员工已存在");
        }
        $mobile=$params["mobile"];
        $mobileExist=$this->mobileexits($mobile);
        if($mobileExist){
            return $this->error("手机号已存在");
        }
        $member_id=Db::name("member")->where("mobile",$mobile)->value("member_id");
        $shopinfo=Db::name("shop")->where("site_id",$params["shop_id"])->value("website_id");
        $data=[
            'shop_id'=>$params["shop_id"],
            'mobile'=>$params["mobile"],
            'username'=>$params["username"],
            'create_time'=>time(),
            'member_id'=>$member_id,
            'employeeID'=>$params["empid"],
            'bymember_id'=>$by_member_id,
            'website_id'=>$shopinfo
        ];
        $res=Db::name("shop_employee")->save($data);
        if($res>0){
        return $this->success("操作成功");
        }else{
            return $this->error("新增失败");

        }
    }

    public function getById($employeeId,$id=0){
        $where="employeeID  ='".$employeeId."' and is_deleted=0";
        if($id>0){
            $where.=" and employee_id<>".$id;
        }
        $info=model("shop_employee")->getValue($where, 'employee_id');
        return $info;
    }

    public function mobileexits($mobile){
       $where="mobile='".$mobile."' and is_deleted=0";
        $info=model("shop_employee")->getValue($where, 'employee_id');
        return $info; 
    }



    

}
