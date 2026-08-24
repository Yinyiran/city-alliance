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

  /**
   * 我的积分列表
   */
 //我的积分
    public function pointAccount($data)
    {
        $condition = [];
        $type = $data['point_type'];
        $condition[] = ['member_id', '=', $data['member_id']];
        $condition[] = ['account_type', '=', 'point'];
        $field = 'id,member_id,account_type,account_data,from_type,type_name,remark,create_time,point_type,type_tag';
        $order = 'id desc';
        $list = model('member_account')->pageList($condition,$field,$order,$data['page'],$data['page_size']);
        foreach ($list['list'] as $key => $value) {
            $nickname = $headimg = $member_level = $member_level_name = '';
            $sku_name = $sku_image = '';
            $goods_money = $num = '';
            if ($value['point_type'] == 1) { //邀约奖励
                $member = model('member')->getInfo(['member_id'=>$value['type_tag']]);
                $nickname = $member['nickname'] ?? '';
                $headimg = $member['headimg'] ?? '';
                $member_level = $member['member_level'] ?? '';
                $member_level_name = $member['member_level_name'] ?? '';
            } elseif ($value['point_type'] == 2) { //下单奖励
                $order_goods = model('order_goods')->getInfo(['order_goods_id'=>$value['type_tag']]);
                $sku_name = $order_goods['sku_name'] ?? '';
                $sku_image = $order_goods['sku_image'] ?? '';
                $goods_money = $order_goods['goods_money'] ?? 0;
                $num = $order_goods['num'] ?? 0;
            } elseif ($value['point_type'] == 3) { //积分消耗
                $order_goods = model('order_goods')->getInfo(['order_goods_id'=>$value['type_tag']]);
                $sku_name = $order_goods['sku_name'] ?? '';
                $sku_image = $order_goods['sku_image'] ?? '';
                $goods_money = $order_goods['goods_money'] ?? 0;
                $num = $order_goods['num'] ?? 0;
            } elseif ($value['point_type'] == '') {
                $member = model('member')->getInfo(['member_id'=>$value['member_id']]);
                $nickname = $member['nickname'] ?? '';
                $headimg = $member['headimg'] ?? '';
                $member_level = $member['member_level'] ?? '';
                $member_level_name = $member['member_level_name'] ?? '';
            }
            $list['list'][$key]['nickname'] = $nickname;
            $list['list'][$key]['headimg'] = $headimg;
            $list['list'][$key]['create_time'] = date('Y/m/d H:i:s', $value['create_time']);
            $list['list'][$key]['sku_name'] = $sku_name;
            $list['list'][$key]['sku_image'] = $sku_image;
            $list['list'][$key]['goods_money'] = $goods_money;
            $list['list'][$key]['num'] = $num;
            $list['list'][$key]['member_level'] = $member_level;
            $list['list'][$key]['member_level_name'] = $member_level_name;
        }
        return $this->success($list);
    }



}