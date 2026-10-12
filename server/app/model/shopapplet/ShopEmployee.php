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
     * 构建店铺团队成员的基础查询对象
     */
    public function getTeamMemberBaseQuery($member_id, $shop_id)
    {
        $emp = Db::name('shop_employee')
            ->where('shop_id', $shop_id)
            ->where('member_id', $member_id)
            ->where('is_deleted', 0)
            ->find();
        $shop = Db::name('shop')->where('site_id', $shop_id)->find();
        $is_manager = (($emp && $emp['is_manager'] == 1) || ($shop && $shop['member_id'] == $member_id)) ? 1 : 0;

        // 该店铺所有员工及店长的 member_id
        $all_emp_members = Db::name('shop_employee')
            ->where('shop_id', $shop_id)
            ->where('is_deleted', 0)
            ->where('member_id', '>', 0)
            ->column('member_id');
        if (!empty($shop['member_id']) && !in_array($shop['member_id'], $all_emp_members)) {
            $all_emp_members[] = (int)$shop['member_id'];
        }

        // 店铺其它下属员工（不包含当前登录者）
        $sub_emp_members = array_values(array_filter($all_emp_members, function($id) use ($member_id) {
            return $id != $member_id;
        }));

        $all_emp_fenxiao_ids = [];
        if (!empty($all_emp_members)) {
            $all_emp_fenxiao_ids = Db::name('fenxiao')
                ->where('member_id', 'in', $all_emp_members)
                ->column('fenxiao_id');
        }

        $my_fenxiao_id = Db::name('fenxiao')->where('member_id', $member_id)->value('fenxiao_id') ?: 0;

        $query = Db::name('member')->alias('m')
            ->leftJoin('fenxiao f', 'f.fenxiao_id = m.fenxiao_id')
            ->where('m.is_delete', 0)
            ->where('m.member_id', '<>', $member_id);

        if ($is_manager == 1) {
            // 店长/主理人视角：
            // 1. 明确归属于该店铺 site_id 的会员
            // 2. 所属员工 staff_member_id 在该店铺员工列表中的会员
            // 3. 邀请人 source_member 在该店铺员工列表中的会员
            // 4. 分销上级 parent 在该店铺员工分销ID列表中的会员
            // 5. 该店铺的下属员工本身
            $query->where(function($q) use ($shop_id, $all_emp_members, $all_emp_fenxiao_ids, $sub_emp_members) {
                $has_condition = false;
                if (!empty($shop_id)) {
                    $q->where('m.site_id', $shop_id);
                    $has_condition = true;
                }
                if (!empty($all_emp_members)) {
                    if ($has_condition) {
                        $q->whereOr('m.staff_member_id', 'in', $all_emp_members);
                    } else {
                        $q->where('m.staff_member_id', 'in', $all_emp_members);
                        $has_condition = true;
                    }
                    $q->whereOr('m.source_member', 'in', $all_emp_members);
                }
                if (!empty($all_emp_fenxiao_ids)) {
                    $q->whereOr('f.parent', 'in', $all_emp_fenxiao_ids);
                }
                if (!empty($sub_emp_members)) {
                    $q->whereOr('m.member_id', 'in', $sub_emp_members);
                }
            });
        } else {
            // 普通员工视角：仅查看由自己邀请或直属的会员
            $query->where(function($q) use ($member_id, $my_fenxiao_id) {
                $q->where('m.staff_member_id', $member_id);
                $q->whereOr('m.source_member', $member_id);
                if (!empty($my_fenxiao_id)) {
                    $q->whereOr('f.parent', $my_fenxiao_id);
                }
            });
        }

        return $query;
    }

    /**
     * 获取团队列表
     * params:member_id 会员id 
     * return array
     */
    public function getTeamMember($member_id, $shop_id, $member_level, $page, $searchtxt, $page_size = PAGE_LIST_ROWS)
    {
        $query = $this->getTeamMemberBaseQuery($member_id, $shop_id);

        if (!empty($member_level) && $member_level > 0) {
            $query->where('m.member_level', $member_level);
        }

        if (!empty($searchtxt)) {
            $query->where(function($q) use ($searchtxt) {
                $q->where('m.mobile', 'like', '%' . $searchtxt . '%')
                  ->whereOr('m.nickname', 'like', '%' . $searchtxt . '%');
            });
        }

        $res = $query->field('m.*')
            ->order('m.point desc, m.reg_time desc')
            ->paginate(['list_rows' => $page_size, 'page' => $page]);

        $ismanager = $this->ismanager($member_id, $shop_id);

        $data = [
            "list" => $res->items(),
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size),
            "ismanager" => $ismanager
        ];
        return $this->success($data);
    }

    public function ismanager($member_id, $shop_id = 0)
    {
        $emp = Db::name("shop_employee")->where("member_id", $member_id);
        if (!empty($shop_id)) {
            $emp->where("shop_id", $shop_id);
        }
        $res = $emp->where("is_deleted", 0)->value("is_manager");
        if (empty($res) && !empty($shop_id)) {
            $shop = Db::name("shop")->where("site_id", $shop_id)->find();
            if (!empty($shop) && $shop['member_id'] == $member_id) {
                $res = 1;
            }
        }
        return !empty($res) ? 1 : 0;
    }

    /**
     * 获取团队人数及等级统计
     */
    public function getTeamCount($member_id, $shop_id)
    {
        // 1. 获取团队总人数
        $total_query = $this->getTeamMemberBaseQuery($member_id, $shop_id);
        $total_count = $total_query->count('DISTINCT m.member_id');

        // 2. 按会员等级统计各等级人数
        $all_levels = Db::name('member_level')
            ->field('level_id, level_name')
            ->order('sort asc, level_id asc')
            ->select()
            ->toArray();

        $level_count_query = $this->getTeamMemberBaseQuery($member_id, $shop_id);
        $level_counts = $level_count_query
            ->field('m.member_level, count(DISTINCT m.member_id) as counts')
            ->group('m.member_level')
            ->select()
            ->toArray();

        $count_map = [];
        foreach ($level_counts as $row) {
            $count_map[$row['member_level']] = (int)$row['counts'];
        }

        $leveltotal = [];
        foreach ($all_levels as $lvl) {
            $leveltotal[] = [
                'level_id' => $lvl['level_id'],
                'level_name' => $lvl['level_name'],
                'counts' => $count_map[$lvl['level_id']] ?? 0
            ];
        }

        return [
            'total_count' => $total_count,
            'leveltotal' => $leveltotal
        ];
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
