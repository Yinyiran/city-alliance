<?php

/**

 */

namespace app\model\shopapplet;

use app\model\BaseModel;
use think\facade\Db;
use think\facade\Cache;
use Carbon\Carbon;

/**
 * 大屏统计
 */
class Shopstatistics extends BaseModel
{
    /**
     * 销售额统计
     */
   public function byorderamount($website_id){
    $where="s.website_id=".$website_id;
    $res=Db::name("shop")->alias("s")
    ->join("order o ","o.site_id=s.site_id and o.pay_status=1","left")
    ->field("s.site_name,s.logo as shop_logo,sum(o.order_money) as total_money")
    ->where($where)
    ->group("s.site_id")
    ->order("total_money desc")
    ->limit(0,10)
    ->select();
    return $res;

   }
/**
 * 订单统计排行
 */
   public function byordercount($website_id){
        $where="s.website_id=".$website_id;
        $res=Db::name("shop")->alias("s")
        ->join("order o ","o.site_id=s.site_id and o.pay_status=1","left")
        ->field("s.site_name,s.logo as shop_logo,count(o.order_id) as total_count")
        ->where($where)
        ->group("s.site_id")
        ->order("total_count desc")
        ->limit(0,10)
        ->select();
        return $res;
   }
   /**
    * 积分统计排行
    */
    public function bypoints($website_id){
        $where="s.website_id=".$website_id;
        $res=Db::name("shop")->alias("s")
        ->field("s.site_name,s.logo as shop_logo,points")
        ->where($where)
        ->order("points desc")
        ->limit(0,10)
        ->select();
        return $res;
    }
    /**
     * 签到统计排行
     */
    public function bysignin($website_id){
        $where="s.website_id=".$website_id;
        $res=Db::name("shop")->alias("s")
         ->join("sign_member_record sign","sign.shop_id=s.site_id",'left')
        ->field("s.site_name,s.logo as shop_logo,count(signin_id) as sign_in_count")
        ->where($where)
        ->group("s.site_id")
        ->order("sign_in_count desc")
        ->limit(0,10)
        ->select();
        return $res;
    }

    /**
     * 订单核销排行
     * @params delivery_status 
     */
    public function hxorder($website_id,$delivery_status){
        $where="s.website_id=".$website_id;
        if($delivery_status==1){
            //已核销
            $join=" and delivery_type='store' and order_type=2 and order_status=10";
        }
        if($delivery_status==2){
            //未核销
            $join=" and delivery_type='store' and order_type=2 and order_status=2";
        }
        $res=Db::name("shop")->alias("s")
        ->join("order o ","o.site_id=s.site_id ".$join,"left")
        ->field("s.site_name,s.logo as shop_logo,sum(o.order_money) as total_money")
        ->where($where)
        ->group("s.site_id")
        ->order("total_money desc")
        ->limit(0,10)
        ->select();
        return $res;
    }

    /**
     * 大屏中间交易数据-成交记录
     */
    public function ordertjbycity($website_id,$selectby="day"){
        $where="o.pay_status=1";
        if(!empty($selectby)){
            $where.=" and ".$this->setcondition($selectby);
        }
        $res=Db::name("order")->alias("o")
        ->join("shop s","s.site_id=o.site_id and s.website_id=".$website_id)
        ->where($where)
        ->group("s.website_id")
        ->sum("order_money");
        return $res;
    }

    /**
     * 销售额趋势
     */
    public function daysale($website_id,$selectby="day"){
        //按天
        $result=[];
         $where="o.pay_status=1";
        if($selectby=="day"){
          
            //近15天的订单数以及销售金额
            $date_day = getweeks(15);
            $order_total = 0;
            foreach ($date_day as $k => $day) {
                $dayarr = explode('-', $day);
                $starttime=Carbon::create($dayarr[0],$dayarr[1],$dayarr[2],0,0,0)->timestamp;
                $endtime=Carbon::create($dayarr[0],$dayarr[1],$dayarr[2],23,59,59)->timestamp;
                $order_total=Db::name("order")->alias("o")
                ->join("shop s","s.site_id=o.site_id and s.website_id=".$website_id)
                ->where($where." and o.pay_time>= ". $starttime. " and o.pay_time<=". $endtime)
                ->group("s.website_id")
                ->sum("order_money");
                $date=$dayarr[1]."-".$dayarr[2];
               $result[]=["date"=>$date,"order_total"=>$order_total];
            }
        }else{
            //按月查询 显示最近一年
            for($i=11; $i>=0; $i--) {
                $start_month=Carbon::now()->submonths($i);
                $starttime=$start_month->firstOfMonth()->timestamp;
                $endtime=$start_month->lastOfMonth()->timestamp;
                $order_total=Db::name("order")->alias("o")
                ->join("shop s","s.site_id=o.site_id and s.website_id=".$website_id)
                ->where($where." and o.pay_time>= ". $starttime. " and o.pay_time<=". $endtime)
                ->group("s.website_id")
                ->sum("order_money");
                $date=date("Y-m",$starttime);
               $result[]=["date"=>$date,"order_total"=>$order_total];
            }
        }
        return $result;

    }
    /**
     * 入住商家数
     */
    public function getShopnum($website_id){
       $res=Db::name("shop")->where("website_id=".$website_id)->count();
       return $res;
    }
    /**
     * 根据订单类型统计
     */
    public function groupbyordertype($website_id){
        $res=Db::name("order")->alias("o")
        ->join("shop s","s.site_id=o.site_id and s.website_id=".$website_id,"left")
        ->field("count(order_id) as nums,order_type,order_type_name")
        ->where("s.website_id=".$website_id." and pay_status=1")
        ->group("order_type,order_type_name")
        ->select()->toArray();
        return $res;
    }

    /**
     * 根据店铺分类统计
     */
    public function groupbyshoptype($website_id,$limit=0){
       $res=Db::name("shop_category")->alias("c")
       ->join("shop s","c.category_id=s.category_id and s.website_id=".$website_id,"left")
       ->field("count(site_id) as nums,c.category_id,c.category_name")
       ->where("c.website_id=".$website_id)
       ->group("c.category_id,c.category_name")
       ->limit(0,$limit)
       ->select()->toArray();
        return $res;
    }


    /**
     * 获取会员等级统计列表
     */
    public function getmemberlevel($website_id){
        $sql="(select count(m.member_id) as member_counts,member_level from member as m 
        inner join fenxiao f on f.fenxiao_id=m.fenxiao_id
        inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
	    inner join shop_employee se on se.member_id=f_parent.member_id and se.website_id=".$website_id."
        group by m.member_level
        ) mf";
       
        $res=Db::name("member_level")->alias("ml")
            ->join($sql,"ml.level_id=mf.member_level","left")
            ->field("ml.level_name,IFNULL(member_counts,0) as member_counts")
            ->group("ml.level_id")
            ->select()
            ->toArray();
        $totalnum=$this->membertotal($website_id);

        //得到百分比
        foreach($res as $k=>$v){
            if($totalnum>0){
                $res[$k]["percent"]=round($v["member_counts"]/$totalnum*100,2);
            }else{
                $res[$k]["percent"]=0;

            }
        }
        $res[]=["level_name"=>"用户总数","member_counts"=>$totalnum,"percent"=>$totalnum>0?100:0];
        return $res;
    }

    /**
     * 平台用户总数
     */
    public function membertotal($website_id,$is_huiyuan=0){
        //is_huiyuan=1 统计会员  is_huiyuan=0 统计用户

        $where="se.website_id=".$website_id;
        if($is_huiyuan>0){
            $where.=" and m.member_level>1";
        }
        $totalnum=Db::name("member")->alias("m")
            ->join("fenxiao f","f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_parent","f_parent.fenxiao_id=f.parent")
            ->join("shop_employee se","se.member_id=f_parent.member_id")
            ->where($where)
            ->count();
        return $totalnum;
    }
    /**
     * 平台订单数量统计 会员 综合
     */
    public function memberordertj($website_id,$is_huiyuan=0){
        $where="se.website_id=".$website_id;
        if($is_huiyuan>0){
            $where.=" and m.member_level>1";
        }
        $totalnum=Db::name("order")->alias("o")
            ->join("member m","m.member_id=o.member_id")
            ->join("fenxiao f","f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_parent","f_parent.fenxiao_id=f.parent")
            ->join("shop_employee se","se.member_id=f_parent.member_id")
            ->where($where)
            ->count();
        return $totalnum;
    }


 //自营统计
    public function zyshoplists($website_id){
       $sql="(select o.site_id,count(order_id) as order_count from `order` as o
        inner join member m on m.member_id=o.member_id 
        inner join fenxiao f on f.fenxiao_id=m.fenxiao_id
        inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
	    inner join shop_employee se on se.member_id=f_parent.member_id and shop_id=o.site_id
        group by o.site_id
        ) mf";
        $lists=Db::name("shop")->alias("s")
            ->join($sql,"mf.site_id=s.site_id","left")
            ->field("s.site_name,s.logo,s.logo as shop_logo,order_count,s.site_id")
            ->where("s.website_id=".$website_id)
            ->group("s.site_id")
            ->order("order_count desc")
            ->limit(0,10)
            ->select()->toArray();
        return $lists;
    }

    //带单统计
    public function ddshoplists($website_id){
        $sql="(select o.site_id,se.shop_id as se_site_id,o.order_id from `order` as o
        inner join member m on m.member_id=o.member_id 
        inner join fenxiao f on f.fenxiao_id=m.fenxiao_id
        inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
	    inner join shop_employee se on se.member_id=f_parent.member_id
        ) mf";
        $lists=Db::name("shop")->alias("s")
            ->join($sql,"mf.site_id=s.site_id and s.site_id<>mf.se_site_id","left")
            ->field("count(order_id) as order_count,s.site_name,s.logo,s.logo as shop_logo")
            ->where("s.website_id=".$website_id)
            ->group("s.site_id")
            ->order("order_count desc")
            ->limit(0,10)
            ->select()->toArray();
        return $lists;
    }

    //贡献统计
    public function gxshoplists($website_id){
         $sql="(select o.order_id, o.site_id,se.shop_id as se_site_id from `order` as o
            inner join member m on m.member_id=o.member_id 
            inner join fenxiao f on f.fenxiao_id=m.fenxiao_id
            inner join fenxiao f_parent on f_parent.fenxiao_id=f.parent
            inner join shop_employee se on se.member_id=f_parent.member_id
        ) mf";
        $lists=Db::name("shop")->alias("s")
            ->join($sql,"mf.site_id<>s.site_id and s.site_id=mf.se_site_id","left")
            ->field("count(order_id) as order_count,s.site_name,s.logo,s.logo as shop_logo")
            ->where("s.website_id=".$website_id)
            ->group("s.site_id")
            ->order("order_count desc")
            ->limit(0,10)
            ->select()->toArray();
        return $lists;
    }
    /**
     * 会员订单统计 饼状图
     */
    public function pie_member_leveltj($website_id){
        //会员人数
        $memberlevelnum=$this->membertotal($website_id,1);
        $ordernum=$this->memberordertj($website_id,1);
        $result=[
            'member_num'=>$memberlevelnum,
            'order_num'=>$ordernum
        ];
        return $result;
    }

    /**
     * 会员订单统计 饼状图
     */
    public function pie_member_totaltj($website_id){
        //用户人数
        $memberlevelnum=$this->membertotal($website_id,0);
        $ordernum=$this->memberordertj($website_id,1);
        $result=[
            'member_num'=>$memberlevelnum,
            'order_num'=>$ordernum
        ];
        return $result;
    }

    /**
     * 积分 积分消耗
     */
    public function points($website_id){
        //point_type 1/2 加积分 3消耗积分
        $res=Db::name("member_account")->alias("a")
            ->join("member m","m.member_id=a.member_id")
            ->join("fenxiao f","f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_parent","f_parent.fenxiao_id=f.parent")
            ->join("shop_employee se","se.member_id=f_parent.member_id")
            ->field("sum(account_data) as account_data,point_type")
            ->where("se.website_id=".$website_id." and account_type='points'")
            ->group("a.point_type")
            ->select();
            $total_points=0;
            $used_points=0;
            foreach($res as $key => $val){
                if($val['point_type']==1 || $val["point_type"]==2){
                    $total_points+=$val['account_data'];
                }
                if($val["point_type"]==3){
                    $used_points=$val['account_data'];

                }
            }
            $result=[
                "total_points"=>$total_points,
                "used_points"=>$used_points,

            ];
            return $result;

    }

    //统一筛选日期
    public function setcondition($selectby = "day")
    {
        $today = Carbon::now();
        $where = "1=1 ";
        if ($selectby == "year") {
            $starttime = Carbon::now()->startOfYear()->timestamp;
            $endtime = Carbon::now()->endOfYear()->timestamp;
        } 
           
        if ($selectby == "day") {
            $starttime = strtotime($today->toDateString());
            $endtime = strtotime($today->toDateString() . " 23:59:59");
            $where = "o.create_time between " . $starttime . " and " . $endtime;
        }
        if ($selectby == "month") {
            // 获取当前月份的第一天
            $startOfMonth = (array) $today->copy()->startOfMonth();
            $starttime = strtotime($startOfMonth['date']); //时间戳
            // 获取当前月份的最后一天
            $endOfMonth = (array) $today->copy()->endOfMonth();
            $endtime = strtotime($endOfMonth['date']); //时间戳
            $where = "o.create_time between " . $starttime . " and " . $endtime;
        }
        return $where;
    }

    //小程序端统计
    /**
     * 今日/累计 成交额
     */
    public function mini_sums($site_id,$selectby=''){
        $where="site_id=".$site_id." and pay_status=1";
        if ($selectby == "day") {
            $starttime = strtotime(Carbon::now()->toDateString());
            $endtime = strtotime(Carbon::now()->toDateString() . " 23:59:59");
            $where .= " and pay_time between " . $starttime . " and " . $endtime;
        }
        $result=Db::name("order")->where($where)->sum("order_money");
        return $result;
    }
    
    /**
     * 用户数
     */
    public function mini_member_count($site_id){
        $where="se.shop_id=".$site_id;
        $totalnum=Db::name("member")->alias("m")
            ->join("fenxiao f","f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_parent","f_parent.fenxiao_id=f.parent")
            ->join("shop_employee se","se.member_id=f_parent.member_id")
            ->where($where)
            ->count();
        return $totalnum;
    }
/**
 * 订单数
 */
    public function mini_order_count($site_id){
        $where="o.site_id=".$site_id." and pay_status=1";
        $totalnum=Db::name("order")->alias("o")
            ->where($where)
            ->count();
        return $totalnum;
    }

    /**
     * 产品销量记录表  今日/累计
     */
    public function goodsranklist($site_id,$selectby="day"){
        $where="o.site_id=".$site_id." and pay_status=1";
        if ($selectby == "day") {
            $starttime = strtotime(Carbon::now()->toDateString());
            $endtime = strtotime(Carbon::now()->toDateString() . " 23:59:59");
            //$where .= " and o.pay_time between " . $starttime . " and " . $endtime;
        }
        $lists=Db::name("order_goods")->alias("g")
        ->join("order o","o.order_id=g.order_id")
        ->where($where)
        ->field("g.goods_id,sum(g.goods_money) as goods_money_total")
        ->group("g.goods_id")
        ->order("goods_money_total desc")
        ->limit(0,10)
        ->select();
        foreach($lists as $key =>$val){
            $goodsinfo=Db::name("goods")->where("goods_id=".$val["goods_id"])->find();
            $val["goods_name"]=$goodsinfo["goods_name"];
            $val["goods_image"]=$goodsinfo["goods_image"];
            $val["goods_price"]=$goodsinfo["price"];
            $lists[$key]=$val;
        }
        return $lists;
    }



}
