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
//            return $this->error("员工已存在");
        }
        $mobile=$params["mobile"];
        $mobileExist=$this->mobileexits($mobile);
        if($mobileExist){
//            return $this->error("手机号已存在");
        }
        $member_id=Db::name("member")->where("mobile",$mobile)->value("member_id");
        if(empty($member_id)){
            $member_id=0;
        }
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
            if ($member_id > 0) {
                Db::name("member")->where("member_id", $member_id)->update(['site_id' => $params["shop_id"]]);
            }
            // 设为员工后，自动将该账号升级为合伙人
            self::syncMemberPartnerLevel($member_id, $mobile);
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

    public function mobileexits($mobile, $shop_id = 0){
       $where="mobile='".$mobile."' and is_deleted=0";
       if(!empty($shop_id)){
           $where.=" and shop_id=".$shop_id;
       }
        $info=model("shop_employee")->getValue($where, 'employee_id');
        return $info; 
    }

    /**
     * 商家员工列表
     */
    public function getlistsbyshop($shop_id,$page,$page_size,$searchtxt=''){
        $where=[
            ["shop_id","=",$shop_id],
            ["is_deleted","=",0],
        ];
        if(!empty($searchtxt)){
            if(!empty($searchtxt["username"])){
                $where[]=["username","like","%".$searchtxt["username"]."%"];
            }
            if(!empty($searchtxt["mobile"])){
                $where[]=["mobile","like","%".$searchtxt["mobile"]."%"];
            }
        }
        $list=model("shop_employee")->pageList($where, "*", "", $page, $page_size);
       return $this->success($list);

    }

    /**
     * 新增员工
     */
    public function addsbyadmin($params){
        $getId=$this->getById($params["empid"]);
        if($getId){
            return $this->error("员工会员ID已经存在");
        }
        $mobile=$params["mobile"];
        $mobileExist=$this->mobileexits($mobile);
        if($mobileExist){
            return $this->error("手机号已存在");
        }
        $member_id=Db::name("member")->where("mobile",$mobile)->value("member_id");
        $website_id=Db::name("shop")->where("site_id",$params["site_id"])->value("website_id");
      
        if($params["is_manager"]==1){
            Db::name("shop_employee")->where("shop_id=".$params["site_id"]." and is_manager=1")->update(["is_manager"=>0]);
        }

        $data=[
            'shop_id'=>$params["site_id"],
            'mobile'=>$params["mobile"],
            'username'=>$params["username"],
            'create_time'=>time(),
            'member_id'=>$member_id,
            'employeeID'=>$params["empid"],
            'headimg'=>$params["headimg"],
            'is_manager'=>$params["is_manager"],
            'website_id'=>$website_id
        ];
        $res=Db::name("shop_employee")->save($data);
        if($res>0){
            $employee_id=Db::name("shop_employee")->where("shop_id=".$params["site_id"]." and is_manager=1")->value("employee_id");
            Db::name("shop")->where("site_id=".$params["site_id"])->update(["zlr_employee_id"=>$employee_id]);
            // 员工/主理人自动转为合伙人
            if(!empty($member_id)){
                Db::name("member")->where("member_id", $member_id)->update([
                    "member_level" => 4,
                    "member_level_name" => "合伙人"
                ]);
            }
            return $this->success("操作成功");
        }else{
            return $this->error("新增失败");
        }
    }

    /**
     * 编辑员工
     */
   public function eidtbyadmin($params){
        $getId=Db::name("shop_employee")->where("employeeID=".$params["empid"]." and employee_id<>".$params["employee_id"])->find();
        if($getId){
            return $this->error("会员ID已存在");
        }
        $mobile=$params["mobile"];
        $mobileExist=Db::name("shop_employee")->where("mobile='".$params["mobile"]."' and employee_id<>".$params["employee_id"])->find();
        if($mobileExist){
            return $this->error("手机号已存在");
        }
        $member_id=Db::name("member")->where("mobile",$mobile)->value("member_id");
        if(!isset($params["is_manager"])){
            $params["is_manager"]=0;
        }
        if($params["is_manager"]==1){
            Db::name("shop_employee")->where("shop_id=".$params["site_id"]." and is_manager=1")->update(["is_manager"=>0]);
        }else{
            $zlr_employee_id=Db::name("shop")->where("site_id=".$params["site_id"])->value("zlr_employee_id");
            if($zlr_employee_id==$params["employee_id"]){
                Db::name("shop")->where("site_id=".$params["site_id"])->update(["zlr_employee_id"=>0]);
            }
        }
        $data=[
            'mobile'=>$params["mobile"],
            'username'=>$params["username"],
            'create_time'=>time(),
            'member_id'=>$member_id,
            'employeeID'=>$params["empid"],
            'headimg'=>$params["headimg"],
            'is_manager'=>$params["is_manager"],
        ];
        $res=Db::name("shop_employee")->where("employee_id=".$params["employee_id"])->update($data);
        if($res>0){
            $employee_id=Db::name("shop_employee")->where("shop_id=".$params["site_id"]." and is_manager=1")->value("employee_id");
            if(empty($employee_id)){
                $employee_id=0;
            }
            Db::name("shop")->where("site_id=".$params["site_id"])->update(["zlr_employee_id"=>$employee_id]);
            if(!empty($member_id)){
                Db::name("member")->where("member_id", $member_id)->update([
                    "member_level" => 4,
                    "member_level_name" => "合伙人"
                ]);
            }
            return $this->success("操作成功");
        }else{
            return $this->error("新增失败");
        }
    }
    
    public function dels($id){
        $data=[
            'is_deleted'=>1
        ];
        $res=Db::name("shop_employee")->where("employee_id=".$id)->update($data);
        if($res>0){
            return $this->success("操作成功");
        }else{
            return $this->error("删除失败");
        }

    }

    /**
     * 确保店长和员工自动成为合伙人（member_level = 4）
     * 无论何时被设为店长/员工，或者员工/店长登录时，均自动执行同步
     */
    public static function syncMemberPartnerLevel($member_id = 0, $mobile = '')
    {
        if (empty($member_id) && empty($mobile)) return false;

        $member_query = Db::name('member');
        if (!empty($member_id)) {
            $member_query->where('member_id', $member_id);
        } else {
            $member_query->where('mobile', $mobile);
        }
        $member = $member_query->find();
        if (empty($member)) return false;

        $target_member_id = $member['member_id'];
        $target_mobile = $member['mobile'] ?: $mobile;

        // 查询该会员是否在有效员工表记录中（主理人 或 员工）
        $is_employee = Db::name('shop_employee')
            ->where('is_deleted', 0)
            ->where(function ($query) use ($target_member_id, $target_mobile) {
                $query->where('member_id', $target_member_id);
                if (!empty($target_mobile)) {
                    $query->whereOr('mobile', $target_mobile);
                }
            })
            ->find();

        if ($is_employee) {
            // 反向回填 shop_employee 的 member_id（若此前为空）
            if (empty($is_employee['member_id']) || $is_employee['member_id'] != $target_member_id) {
                Db::name('shop_employee')->where('employee_id', $is_employee['employee_id'])->update([
                    'member_id' => $target_member_id
                ]);
            }

            // 升级会员等级为 4 (合伙人)
            if ($member['member_level'] != 4) {
                Db::name('member')->where('member_id', $target_member_id)->update([
                    'member_level' => 4,
                    'member_level_name' => '合伙人'
                ]);
            }
            return true;
        }

        return false;
    }

}
