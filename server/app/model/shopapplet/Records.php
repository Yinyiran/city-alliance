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




/**
 * 用户签到记录管理
 */
class Records extends BaseModel{
    /**
     * 用户商铺签到列表
     */
    public function getSigninByday($member_id,$rules_id,$shop_sign_round){
        $condition=[
            ['a.member_id',"=",$member_id],
            ['a.rules_id',"=",$rules_id], 
            ['a.sign_rounds',"=",$shop_sign_round],
        ];
       
        $alias="a";
        $join = [
            ['shop s','s.site_id=a.shop_id','left']
        ];
        $field="s.site_name,s.logo,s.avatar,a.signin_id,a.sign_day,a.sign_rounds,a.rewardtxt,a.rewardname,a.rewardtype,a.rewardnum,a.goods_id";
        
        $res=model("sign_member_record")->getList($condition,$field,"",$alias,$join);
        if(empty($res)){
            return [];
        }
        return $res[0];

    }

    /**
     * 用户当前轮签到的天数
     */
    public function getSignCount($member_id,$shop_sign_round){
        $where="member_id=".$member_id." and sign_rounds=".$shop_sign_round;
        $res=Db::name("sign_member_record")->where($where)->count();
        return $res;
    }

    /**
     * 商铺签到记录
     */
    public function signlistbyshop($shop_id,$searchtxt,$sortbycount=1,$sortbytime=1,$page=1,$page_size = 10){
        $where="shop_id=".$shop_id." and m.is_delete=0";
        if(!empty($searchtxt)){
            $where.=" and (m.nickname like '%".$searchtxt."%' or mobile like '%".$searchtxt."%')";
        }
        $alias="a";
        $field="count(signin_id) as signcount,max(a.create_time) as sign_time,a.member_id,m.nickname,m.headimg,m.mobile";
        $order="";
        $order_count=(empty($sortbycount)?1:$sortbycount);
        $order_time=(empty($sortbytime)?1:$sortbytime);
        if($order_count==1){
            //从大到小
            $order="signcount desc";
        }else{
            $order="signcount asc";
        }
        if($order_time==1){
            $order.=",sign_time desc";
        }else{
            $order.=",sign_time asc";
        }
        $list=Db::name("sign_member_record")->alias("a")
                ->join("member m","m.member_id=a.member_id")->field($field)
                ->where($where)->group("a.member_id")->order($order)
                ->paginate($page_size);
        $data=$list->items();
        foreach($data as $key => $val){
            $val["sign_time"] = !empty($val["sign_time"]) ? (is_numeric($val["sign_time"]) ? date("Y-m-d H:i:s", $val["sign_time"]) : $val["sign_time"]) : '';
            $data[$key]=$val;
        }
        $result=[
            'count'=>$list->total(),
            'list'=>$data,
            'page_count'=>ceil($list->total() / $page_size),
        ];
        return $this->success($result);
    }

    /**
     * 今日门店签到
     * */
    public function todaycount($shop_id){
        $where="shop_id=".$shop_id;
        $where.=' and (DATE_FORMAT((FROM_UNIXTIME(create_time)),"%Y-%m-%d")=DATE_FORMAT(CURDATE(),"%Y-%m-%d"))';
        $res=Db::name("sign_member_record")->where($where)->count();
        return $res;
    }
    /**
     * 店铺累计签到
     * */
    public function totalcount($shop_id){
        $where="shop_id=".$shop_id;
        $res=Db::name("sign_member_record")->where($where)->count();
        return $res;
    }

}
