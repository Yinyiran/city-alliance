<?php

/**

 */

namespace app\model\shopapplet;

use app\model\BaseModel;
use think\facade\Db;
use think\facade\Cache;
use app\model\member\Member as MemberModel;
use app\model\signin\Rules as RulesModel;
use app\model\api\SignMemberRecord as SignMemberRecordModel;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\order\orderCreate as orderCreateModel;
use Carbon\Carbon;

class MemberRank extends BaseModel
{
    public function invitememberlist($site_id,$member_id,$selectby,$member_level,$page,$page_size= PAGE_LIST_ROWS){
        $where=$this->setcondition($selectby);
        /*$res=Db::name("member")->alias("m")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se","se.member_id=f_parent.member_id",'right')
            ->field("se.*,f_parent.create_time as fenxiao_time,count(m.member_id) as invite_count")
            ->where("se.shop_id=".$site_id)
            ->where($where)
            ->group("se.employee_id,f_parent.create_time")
            ->order("invite_count desc")
            ->paginate($page_size);*/
            $sql=$this->commonsql($site_id,$where);
            $res=Db::name("shop_employee")->alias("se")
            ->join("member m","m.member_id=se.member_id","left")
            ->join($sql,"mf.parent_member_id=se.member_id","left")
            ->field("se.*,count(m.member_id) as invite_count")
            ->group("se.employee_id")
            ->order("invite_count asc")
            ->paginate($page_size);
            $rank=0;
            $myrank=0;
            $data=$res->items();
            $myinfo=[];
            foreach($data as $key => $val){
                $rank++;
                $val["headimg"]=Db::name('member')->where("member_id",$val["member_id"])->value("headimg");
                if($val["member_id"]==$member_id){
                    $val["rank"]=$key+1;
                    $myrank=$rank;
                    $myinfo=$val;
                }
                $data[$key]=$val;
            }
            $data = [
                "levelData"=>$this->leveltj($site_id,$where),
                "list" => $data,    
                "myinfo"=>[
                    'myrank'=>$myrank,
                    'myinfo'=>$myinfo,
                ],
                "count" => $res->total(),
                "page_count" => ceil($res->total() / $page_size)
            ];
        return $this->success($data);
    }

    


    //统一筛选日期
    public function setcondition($selectby = "year")
    {
        $today = Carbon::now();
        $where = "1=1 ";
        if ($selectby == "year") {
            $starttime = strtotime($today->startOfYear());
            $endtime = strtotime($today->endOfYear());
            $where = "f.create_time between " . $starttime . " and " . $endtime;
        }
        if ($selectby == "month") {
            // 获取当前月份的第一天
            $startOfMonth = (array) $today->copy()->startOfMonth();
            $starttime = strtotime($startOfMonth['date']); //时间戳
            $endOfMonth = (array) $today->copy()->endOfMonth();
            $endtime = strtotime($endOfMonth['date']); //时间戳
            $where = "f.create_time between " . $starttime . " and " . $endtime;
        }
        return $where;
    }

    /**
     * 获取门店员工分销会员
     */
    public function commonsql($site_id,$where){
        $sql="(select f_parent.member_id as parent_member_id,m.member_level,m.member_id,f.create_time as fenxiao_time from member as m inner join fenxiao f on f.	fenxiao_id=m.fenxiao_id inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
	    inner join shop_employee se on se.member_id=f_parent.member_id and shop_id=".$site_id." and ".$where.") mf";
        return $sql;
    }
    /**
     * 按照等级统计
     * @param $site_id
     * @param $where  按照年/月筛选
     */
    public function leveltj($site_id,$where){
        $sql=$this->commonsql($site_id,$where);
        $data=Db::name("member_level")->alias("ml")
        ->join("member m","ml.level_id=m.member_level","left")
        ->join($sql,"mf.member_id=m.member_id","left")
        ->field("ml.level_id,ml.level_name,count(mf.member_id) as counts")
        ->group("ml.level_id")
        ->select();
        return $data;
    }

    /**
     * 员工自签单排行
     */
    public function orderbyselfrank($site_id,$selectby,$page,$page_size= PAGE_LIST_ROWS){
        $where=$this->setcondition($selectby);
        $sql=$this->commonsql($site_id,$where);
        $res=Db::name("shop_employee")->alias("se")
        ->join("member m","m.member_id=se.member_id","left")
        ->join($sql,"mf.parent_member_id=se.member_id","left")
        ->join("order o","o.member_id=mf.member_id and site_id=".$site_id,"left")
        ->field("se.employee_id,se.member_id,m.headimg,m.nickname,se.username,count(o.order_id) as order_count")
        ->group("se.member_id,se.employee_id,se.username")
        ->order("order_count desc")
        ->paginate($page_size);
        $data = [
            "list" => $res->items(), 
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size)
        ];
        return $this->success($data);
    }

    /**
     * 员工贡献单排行
     */
    public function conorderrank($site_id,$selectby,$page,$page_size= PAGE_LIST_ROWS){
        $where=$this->setcondition($selectby);
        $sql=$this->commonsql($site_id,$where);
        $res=Db::name("shop_employee")->alias("se")
        ->join("member m","m.member_id=se.member_id","left")
        ->join($sql,"mf.parent_member_id=se.member_id","left")
        ->join("order o","o.member_id=mf.member_id and site_id<>".$site_id,"left")
        ->field("se.employee_id,se.member_id,m.headimg,m.nickname,se.username,count(o.order_id) as order_count")
        ->group("se.member_id,se.employee_id,se.username")
        ->order("order_count desc")
        ->paginate($page_size);
        $data = [
            "list" => $res->items(), 
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size)
        ];
        return $this->success($data);
    }


}
