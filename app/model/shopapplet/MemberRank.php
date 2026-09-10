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
            $where=$this->setcondition($selectby,"f_son.create_time");
            $join='';
           if(!empty($member_level)){
                $join.=" and m2.member_level=".$member_level;
           }
            $sql="(select m.member_id,f.fenxiao_id,f_son.fenxiao_id as son_fenxiao_id,f_son.member_id as son_member_id from member as m "
                ." inner join fenxiao f on f.fenxiao_id=m.fenxiao_id inner join fenxiao f_son on f_son.parent=f.fenxiao_id"
                .' inner join member m2 on m2.fenxiao_id=f_son.fenxiao_id'
                ." where " .$where.$join.") mf";
            $res=Db::name("shop_employee")->alias("se")
            ->join($sql,"se.member_id=mf.member_id","left")
            ->field("count(mf.member_id) as invite_count ,se.*")
            ->where("se.shop_id=".$site_id)
            ->group("se.employee_id")
            ->order("invite_count desc")
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
    public function setcondition($selectby = "year",$wherecolumn="f.create_time"){
        $today = Carbon::now();
        $where = "1=1 ";
        if ($selectby == "year") {
            $starttime = strtotime($today->startOfYear());
            $endtime = strtotime($today->endOfYear());
            $where .=" and ".$wherecolumn." between " . $starttime . " and " . $endtime;
        }
        if ($selectby == "month") {
            // 获取当前月份的第一天
            $startOfMonth = (array) $today->copy()->startOfMonth();
            $starttime = strtotime($startOfMonth['date']); //时间戳
            $endOfMonth = (array) $today->copy()->endOfMonth();
            $endtime = strtotime($endOfMonth['date']); //时间戳
            $where .=" and ".$wherecolumn." between " . $starttime . " and " . $endtime;
        }
        return $where;
    }

    /**
     * 获取门店员工分销会员
     */
    public function countsqlmember($site_id,$where){
        $sql="(select count(m.member_id) as counts,f_parent.member_id as parent_member_id,m.member_id from member as m 
        inner join fenxiao f on f.fenxiao_id=m.fenxiao_id and ".$where."
        inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
	    inner join shop_employee se on se.member_id=f_parent.member_id and shop_id=".$site_id."
        group by parent_member_id,m.member_id
        ) mf";
        return $sql;
    }

     /**
     * 获取门店员工分销会员
     * 获取员工
     */
    public function commonsql($site_id,$where){
        $sql="(select f_parent.member_id as parent_member_id,m.member_level,m.member_id,f.create_time as fenxiao_time from member as m inner join fenxiao f on f.fenxiao_id=m.fenxiao_id inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
	    inner join shop_employee se on se.member_id=f_parent.member_id and shop_id=".$site_id." and ".$where.") mf";
        return $sql;
    }
    /**
     * 按照等级统计
     * @param $site_id
     * @param $where  按照年/月筛选
     */
    public function leveltj($site_id,$where){
        $levellist=Db::name("member_level")->column("level_id,level_name");
        foreach($levellist as $key => $val){
             $sql="(select m.member_id,f.fenxiao_id,f_son.fenxiao_id as son_fenxiao_id,f_son.member_id as son_member_id from member as m "
                ." inner join fenxiao f on f.fenxiao_id=m.fenxiao_id inner join fenxiao f_son on f_son.parent=f.fenxiao_id"
                .' inner join member m2 on m2.fenxiao_id=f_son.fenxiao_id'
                ." where " .$where." and m2.member_level=".$val['level_id'].") mf";

            $res=Db::name("shop_employee")->alias("se")
            ->join($sql,"se.member_id=mf.member_id","left")
            ->field("count(mf.member_id) as counts")
            ->where("se.shop_id=".$site_id)
            ->find();
            if(!empty($res)){
                $levellist[$key]["counts"]=$res['counts'];
            }else{
                $levellist[$key]["counts"]=0;
            }


        }
        return $levellist;
    }

    /**
     * 员工自签单排行
     */
    public function orderbyselfrank($site_id, $member_id = 0, $selectby = "year", $page = 1, $page_size = PAGE_LIST_ROWS){
        $where=$this->setcondition($selectby,"o.pay_time");
        $sql=$this->commonsql($site_id,"1=1");
        $res=Db::name("shop_employee")->alias("se")
        ->join("member m","m.member_id=se.member_id","left")
        ->join($sql,"mf.parent_member_id=se.member_id","left")
        ->join("order o","o.member_id=mf.member_id and o.site_id=".$site_id." and ".$where,"left")
        ->field("se.employee_id,se.member_id,m.headimg,m.nickname,se.username,count(o.order_id) as order_count,count(o.order_id) as invite_count")
        ->group("se.member_id,se.employee_id,se.username")
        ->order("order_count desc")
        ->paginate($page_size);

        $rank=0;
        $myrank=0;
        $data=$res->items();
        $myinfo=[];
        foreach($data as $key => $val){
            $rank++;
            $member_headimg=Db::name('member')->where("member_id",$val["member_id"])->value("headimg");
            if(!empty($member_headimg)){
                $val["headimg"]=$member_headimg;
            }
            if($val["member_id"]==$member_id){
                $val["rank"]=$key+1;
                $myrank=$rank;
                $myinfo=$val;
            }
            $data[$key]=$val;
        }

        $data = [
            "list" => $data, 
            "myinfo" => [
                'myrank' => $myrank,
                'myinfo' => $myinfo,
            ],
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size)
        ];
        return $this->success($data);
    }

    /**
     * 员工贡献单排行
     */
    public function conorderrank($site_id, $member_id = 0, $selectby = "year", $page = 1, $page_size = PAGE_LIST_ROWS){
        $where=$this->setcondition($selectby,"o.pay_time");
        $sql=$this->commonsql($site_id,"1=1");
        $res=Db::name("shop_employee")->alias("se")
        ->join("member m","m.member_id=se.member_id","left")
        ->join($sql,"mf.parent_member_id=se.member_id","left")
        ->join("order o","o.member_id=mf.member_id and o.site_id<>".$site_id." and ".$where,"left")
        ->field("se.employee_id,se.member_id,m.headimg,m.nickname,se.username,count(o.order_id) as order_count,count(o.order_id) as invite_count")
        ->group("se.member_id,se.employee_id,se.username")
        ->order("order_count desc")
        ->paginate($page_size);

        $rank=0;
        $myrank=0;
        $data=$res->items();
        $myinfo=[];
        foreach($data as $key => $val){
            $rank++;
            $member_headimg=Db::name('member')->where("member_id",$val["member_id"])->value("headimg");
            if(!empty($member_headimg)){
                $val["headimg"]=$member_headimg;
            }
            if($val["member_id"]==$member_id){
                $val["rank"]=$key+1;
                $myrank=$rank;
                $myinfo=$val;
            }
            $data[$key]=$val;
        }

        $data = [
            "list" => $data, 
            "myinfo" => [
                'myrank' => $myrank,
                'myinfo' => $myinfo,
            ],
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size)
        ];
        return $this->success($data);
    }

    /**
     * 用户邀约排行
     */
    public function userinviterank($member_id,$selectby,$page,$page_size= PAGE_LIST_ROWS){
         $where=$this->setcondition($selectby,"f_son.create_time");
        //我的邀约码
        $fenxiao_parent_id=Db::name("fenxiao")->where("member_id=".$member_id)->value("fenxiao_id");
        //我的邀约客户的member_id
        $member_id=Db::name("fenxiao")->where("parent=".$fenxiao_parent_id)->column("member_id");
        $member_ids=implode(",",$member_id);

        $res=Db::name("member")->alias("m")
            ->join("fenxiao f","f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_son","f_son.parent=f.fenxiao_id and ".$where,"left")
            ->field("m.member_id,count(f_son.parent) as counts,m.nickname,m.mobile,m.headimg")
            ->where("m.member_id in (".$member_ids.")")
            ->group("m.member_id")
            ->order("counts desc")
            ->limit(0,1000)
            ->select();
        return $this->success($res);
    }


}
