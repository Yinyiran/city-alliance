<?php

/**

 */

namespace app\model\shopapplet;

use app\model\goods\GoodsStock;
use app\model\message\Message;
use app\model\verify\Verify;
use think\facade\Db;
use Carbon\Carbon;

/**
 * 门店自提订单
 *
 * @author Administrator
 *
 */
class Order extends OrderCommon
{

    public function getAllOrder(){
      $res=Db::name('order')->where("site_id",$this->site_id)->select();

    }


    /**
     * 待核销订单
     */
    public function hxorder($site_id, $page, $page_size = PAGE_LIST_ROWS){
        $where="site_id=".$site_id." and delivery_type='store' and order_type=2 and order_status=2";
        $res=Db::name("order")->where($where)->order("create_time desc")->paginate($page_size);
        $items=$res->items();
        foreach($items as $key => $val){
            $items[$key]["goods_list"]=Db::name("order_goods")->where("order_id",$val["order_id"])->select();

        }
        $data = [
            "list" => $items,
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size),
        ];
        return $this->success($data);
    }
    /**
     * 自营是我自己的客户买我的品牌，带单是别人的客户给我下单也叫收单，贡献是我的人买别人商品
     * 上级分销查询方法：fenxiao里面的parent对应的member_id 我的就是作为代理商parent的member_id
     */
    public function getzyOrderList($site_id, $selectby = "day", $searchtxt = "", $page, $page_size =  PAGE_LIST_ROWS)
    {
        $where = $this->setcondition($selectby);
        $where2 = "";
        if (!empty($searchtxt)) {
            $where2 = "og.sku_name like '%" . $searchtxt . "%' ";
        }
        $res = Db::name("order")->alias("o")
            ->field("o.order_id,o.order_no,m.nickname,m.headimg,o.site_id,o.site_name,o.create_time,member_level_name,o.order_point,f_parent.member_id as fenxiao_member_id")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            /*->where("o.site_id=" . $site_id)
            ->where($where)
            ->where($where2)*/
            ->order("o.create_time desc")
            ->paginate($page_size);

         $totalcount=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->where($where2)
            ->count();

        $totalsum=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->where($where2)
            ->sum("goods_money");
           
        $lists = $this->getOtherinfo($res->items(),"zy");
        $data = [
            "list" => $lists,
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size),
            'total_count'=>$totalcount,
            'total_money'=>$totalsum,


        ];
        return $this->success($data);
    }

    
    /**
     * 带单
     * @param $site_id
     * @param $page
     * @param int $page_size
     */
    public function getddOrderList($site_id, $selectby = "day", $searchtxt = "", $page, $page_size =  PAGE_LIST_ROWS)
    {
        $where = $this->setcondition($selectby);
        $where2 = "";
        if (!empty($searchtxt)) {
            $where2 = "og.sku_name like '%" . $searchtxt . "%' ";
        }
        $field="o.order_id,o.order_name,o.site_name,o.order_point,o.order_no,m.nickname"
        .",m.headimg,o.site_id,o.site_name,o.create_time,member_level_name,o.goods_num"
        .",o.order_point,m.fenxiao_id,f_parent.member_id as fenxiao_member_id";
        $res = Db::name("order")->alias("o")
            ->field($field)
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and se.shop_id<>" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->where($where2)
            ->order("o.create_time desc")
            ->paginate($page_size);
        $totalcount= Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
             ->join("shop_employee se", "f_parent.member_id=se.member_id and se.shop_id<>" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->where($where2)
            ->count();

        $totalsum=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and se.shop_id<>" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->where($where2)
            ->sum("goods_money");
           
        $lists = $this->getOtherinfo($res->items(),"dd");
        $data = [
            "list" => $lists,
            "count" => $res->total(),
            "page_count" => ceil($res->total() / $page_size),
            'total_count'=>$totalcount,
            'total_money'=>$totalsum,


        ];

        return $this->success($data);
    }

    /**
     * 贡献
     * @param $site_id
     * @param $page
     * @param int $page_size
     */
    public function getgxOrderList($site_id, $selectby = "day", $searchtxt = "", $page, $page_size =  PAGE_LIST_ROWS)
    {
        $where = $this->setcondition($selectby);
        $where2 = "";
        if (!empty($searchtxt)) {
            $where2 = "og.sku_name like '%" . $searchtxt . "%' ";
        }
        $res = Db::name("order")->alias("o")
            ->field("o.order_id,o.order_no,m.nickname,m.headimg,o.site_id,o.site_name,o.create_time,member_level_name,o.order_point")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")//分销员
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id<>" . $site_id)
            ->where($where)
            ->where($where2)
            ->order("o.create_time desc")
            ->paginate($page_size);
        $totalcount= Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id<>" . $site_id)
            ->where($where)
            ->where($where2)->count();

        $totalsum= Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id<>" . $site_id)
            ->where($where)
            ->where($where2)->sum("goods_money");

        
        $lists = $this->getOtherinfo($res->items(),"gx");
        $data = [
            "list" => $lists,
            "count" => $res->total(),
           "page_count" => ceil($res->total() / $page_size),
            'total_count'=>$totalcount,
            'total_money'=>$totalsum,
        ];

        return $this->success($data);
    }
    
    //商品列表
    public function getOtherinfo($listsitem,$type="dd")
    {
        foreach ($listsitem as $key => $item) {
            $listsitem[$key]["create_time"]=date("Y-m-d H:i:s",$item["create_time"]);

           
                //带单人信息
                $ddinfo=Db::name("shop_employee")->alias("a")
                ->join("shop s","s.site_id=a.shop_id")
                ->field("s.site_name,a.username,s.site_id")
                ->where("a.member_id",$item["fenxiao_member_id"])->find();
               $ddren_count=Db::name("order")->alias("o")
                    ->join("member m","m.member_id=o.member_id")
                    ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
                    ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
                    ->join("shop_employee se","se.member_id=f_parent.fenxiao_id")
                    ->where("se.member_id",$item["fenxiao_member_id"])->count();
                $ddinfo["ddcount"]=$ddren_count;
                $item["fenxiao_memberinfo"]=$ddinfo;
                $listsitem[$key]=$item;
               
            if($type=="gx"){

            }
            $goodslist=Db::name("order_goods")->where("order_id",$item["order_id"])->select();
            $listsitem[$key]["goodslist"]=$goodslist;

        }
        return $listsitem;
    }

    //统一筛选日期
    public function setcondition($selectby = "day")
    {
        $today = Carbon::now();
        $where = "1=1 ";
        if ($selectby == "day") {
            //$where="DATE_FORMAT(FROM_UNIXTIME(o.create_time), '%Y-%m-%d')=DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
            $starttime = strtotime($today->toDateString());
            $endtime = strtotime($today->toDateString() . " 23:59:59");
            $where = "o.create_time between " . $starttime . " and " . $endtime;
        }
        if ($selectby == "month") {
            //$where="DATE_FORMAT(FROM_UNIXTIME(o.create_time), '%Y-%m')=DATE_FORMAT(CURDATE(), '%Y-%m')";
            // 获取当前月份的第一天
            $startOfMonth = (array) $today->copy()->startOfMonth();
            $starttime = strtotime($startOfMonth['date']); //时间戳
            // 获取当前月份的最后一天
            $endOfMonth = (array) $today->copy()->endOfMonth();
            $endtime = strtotime($endOfMonth['date']); //时间戳
            $where = "o.create_time between " . $starttime . " and " . $endtime;
        }
        //$where.=" and o.pay_status=1";
        return $where;
    }


    /**
     * 订单详情
     */
    public function getOrderInfobycode($delivery_code){
        $order_info=Db::name("order")->alias("a")
        ->join("member m","m.member_id=a.member_id")
        ->join("member_level ml","ml.level_id=m.member_level","left")
        ->field("ml.level_name,consume_discount,a.order_money,adjust_money,a.balance_money,pay_money")
        ->where("a.delivery_code",$delivery_code)
        ->find();
        if(empty($order_info)){
            return $this->error([], "ORDER_EMPTY");
        }
        $consume_discount=empty($order_info["consume_discount"])?0:$order_info["consume_discount"];
        $paymoney=$order_info["order_money"];
        if($consume_discount>0){
            $consume_money=$paymoney*$consume_discount/100;
            $order_info["consume_money"]=sprintf("%.2f", $consume_money);
        }

        return $this->success($order_info);
    }

    /**
     * 订单统计
     */
    public function ordertj($site_id){
        $zy=$this->zy_tj($site_id);
        $dd=$this->dd_tj($site_id);
        $gx=$this->gx_tj($site_id);
        $total_count=$zy["totalcount"]+$dd["totalcount"]+$gx["totalcount"];
        $total_sum=$zy["totalsum"]+$dd["totalsum"]+$gx["totalsum"];


        $result=[
            'ziying'=>$zy,
            'daidan'=>$dd,
            'gongxian'=>$gx,
            'total'=>[
                'total_count'=>$total_count,
                'total_sum'=>$total_sum,
            ]

        ];
        return $this->success($result);
    }

    //自营统计
    public function zy_tj($site_id){
        $selectby="";
        $where = $this->setcondition($selectby);
        //自营统计
        $totalcount=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->count();

        $totalsum=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->sum("goods_money");
        return ["totalcount"=>$totalcount,"totalsum"=>$totalsum];
    }

    //带单统计
    public function dd_tj($site_id){
        $selectby="";
        $where = $this->setcondition($selectby);
        //自营统计
        $totalcount=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id<>" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->count();

        $totalsum=Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id<>" . $site_id)
            ->where("o.site_id=" . $site_id)
            ->where($where)
            ->sum("goods_money");
        return ["totalcount"=>$totalcount,"totalsum"=>$totalsum];
    }

    //贡献统计
    public function gx_tj($site_id){
        $selectby="";
        $where = $this->setcondition($selectby);
       $totalcount= Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id<>" . $site_id)
            ->where($where)
            ->count();

        $totalsum= Db::name("order")->alias("o")
            ->join("member m", "m.member_id=o.member_id")
            ->join("fenxiao f"," f.fenxiao_id=m.fenxiao_id")//分销员
            ->join("fenxiao f_parent","f.parent=f_parent.fenxiao_id")
            ->join("shop_employee se", "f_parent.member_id=se.member_id and shop_id=" . $site_id)
            ->where("o.site_id<>" . $site_id)
            ->where($where)
            ->sum("goods_money");
        return ["totalcount"=>$totalcount,"totalsum"=>$totalsum];
    }
}
