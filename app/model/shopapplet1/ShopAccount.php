<?php
/**

 */

namespace app\model\shopapplet;

use think\facade\Db;

use app\model\system\Config;
use app\model\BaseModel;
use app\model\system\Cron;
use Carbon\Carbon;

/**
 * 店铺账户(无缓存)
 */
class ShopAccount extends BaseModel
{
    public $from_type = [
        'order' => [
            'type_name' => '店铺结算',
            'type_url' => '',
        ],
        'withdraw' => [
            'type_name' => '提现',
            'type_url' => '',
        ],
        'point'=>[
            'type_name'=>'积分',
            'type_url'=>''
        ]
    ];
    /**************************************************************店铺账户****************************************************************/
    /**
     * 店铺积分排名
     * 排名方式：年度、月度
     */
  public function getRankList($site_id,$city=0,$sortby="year",$page=1,$page_size = 10){
    $where="s.shop_status=1 and city=".$city." ";
    $join=["shop s","s.site_id=sa.site_id","inner"];

    $alias="sa";
    $field="s.site_name,s.logo as site_logo,s.site_id,sum(sa.account_data) as account_points";
    $order="account_points desc";
    $group="s.site_id";
    $today = Carbon::now();
    $joinand='';
    if($sortby=="year"){
        $starttime = strtotime($today->startOfYear());
            $endtime = strtotime($today->endOfYear());
            $joinand = "sa.create_time between " . $starttime . " and " . $endtime;
    }
    if($sortby=="month"){
            $startOfMonth = (array) $today->copy()->startOfMonth();
            $starttime = strtotime($startOfMonth['date']); //时间戳
            // 获取当前月份的最后一天
            $endOfMonth = (array) $today->copy()->endOfMonth();
            $endtime = strtotime($endOfMonth['date']); //时间戳
            $joinand = "sa.create_time between " . $starttime . " and " . $endtime;
           }
    $list=Db::name("shop")->alias("s")
    ->join("shop_account sa","sa.site_id=s.site_id and account_type='point' and from_type='order' and ".$joinand,'left')

    ->field($field)
    ->where($where)->order($order)->group($group)->paginate($page_size);

    $siteinfo=[];
    $siterank=0;
    foreach($list as $key => $val){
        if($val["site_id"]==$site_id){
            $siterank=$key+1;
            $siteinfo=$val;
            break;
        }
    }
    $result=[
        'count'=>$list->total(),
        'list'=>$list->items(),
        'page_count'=>ceil($list->total() / $page_size),
        'myinfo'=>[
            'info'=>$siteinfo,
            'rank'=>$siterank
        ]
    ];
    return $this->success($result);

  }

  /**
   * 员工积分排行
   */
  public function getmemberRankList($member_id,$site_id,$sortby="year",$page=1,$page_size = 10){
    $where="shop_id=".$site_id;
   
    $alias="sa";
    $field="sum(ma.account_data) as account_points,sa.member_id";
    $order="account_points desc";
    $group="sa.member_id";
    $today = Carbon::now();
    $joinand='';
    if($sortby=="year"){
        $starttime = strtotime($today->startOfYear());
            $endtime = strtotime($today->endOfYear());
            $joinand = "sa.create_time between " . $starttime . " and " . $endtime;
    }
    if($sortby=="month"){
            $startOfMonth = (array) $today->copy()->startOfMonth();
            $starttime = strtotime($startOfMonth['date']); //时间戳
            // 获取当前月份的最后一天
            $endOfMonth = (array) $today->copy()->endOfMonth();
            $endtime = strtotime($endOfMonth['date']); //时间戳
            $joinand = "sa.create_time between " . $starttime . " and " . $endtime;
           }
    $list=Db::name("shop_employee")->alias($alias)
    ->join("member_account ma","ma.member_id=sa.member_id and account_type='point' and ".$joinand,"left")
    ->join("member m","m.member_id=sa.member_id")
    ->field($field)
    ->where($where)->order($order)->group($group)->paginate($page_size);
    $myrank=0;
    $myinfo=[];
    $mymemberinfo=[];
    foreach($list as $key => $val){
        $memberinfo=Db::name('member')->where("member_id",$val["member_id"])->find();
        if($val["member_id"]==$member_id){
            $myrank=$key+1;
            $myinfo=$val;
            $mymemberinfo=$memberinfo;
        }
        $val["memberinfo"]=$memberinfo;
        $list[$key]=$val;
    }
    $result=[
        'count'=>$list->total(),
        'list'=>$list->items(),
        'page_count'=>ceil($list->total() / $page_size),
        'myinfo'=>[
            'info'=>$myinfo,
            'rank'=>$myrank,
            'memberinfo'=>$mymemberinfo
        ]

    ];
    return $this->success($result);
  }



}