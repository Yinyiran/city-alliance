<?php
namespace app\shopapplet\controller;

use app\model\shopapplet\Shopstatistics as StatisticsModel;
use app\model\shopapplet\ShopEmployee as EmployeeModel;
use app\model\shopapplet\Shop as ShopModel;
use think\facade\Db;

/**
 * 积分列表
 */
class Screen extends BaseApi
{
    protected $city_id=0;
    protected $StatisticsModel;
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->city_id=9;
        $this->StatisticsModel=new StatisticsModel();

    }

    /**
     * pc端大屏
     */
    public function index(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        // 处理预检请求（OPTIONS）
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204); // No Content
            exit();
        }
        $city=request()->param("website_id");
        if(empty($city)){
            $this->error("参数错误");
        }
        $this->city_id=$city;
        $websiteinfo=Db::name("website")->where("site_id",$this->city_id)->find();

        //品牌排行
        $salemoneytj=$this->StatisticsModel->byorderamount($this->city_id);//销售总额
        $ordertj=$this->StatisticsModel->byordercount($this->city_id);//订单量
        $pointstj=$this->StatisticsModel->bypoints($this->city_id);//积分排行
        $signintj=$this->StatisticsModel->bysignin($this->city_id);//签到排行

        //核销订单
        //$hxstatus=empty(request()->param("hxstatus"))?1:2;
        $orderhx1=$this->StatisticsModel->hxorder($this->city_id,1);//已核销
        $orderhx2=$this->StatisticsModel->hxorder($this->city_id,2);//未核销

        //积分消耗
       // $points=["total_points"=>"10000000","used_points"=>"10000"];
        $points=$this->StatisticsModel->points($this->city_id);


        //中间部分
        //成交记录
        $city_order=$this->StatisticsModel->ordertjbycity($this->city_id,"day");
        $city_order_all=$this->StatisticsModel->ordertjbycity($this->city_id);

        //日销售额趋势
        $day_sale=$this->StatisticsModel->daysale($this->city_id,"day");
        $month_sale=$this->StatisticsModel->daysale($this->city_id,"month");

        //饼状图 
        $ordersouretj=$this->StatisticsModel->groupbyordertype($this->city_id);//订单来源统计
        $ordertypetj=$this->StatisticsModel->groupbyshoptype($this->city_id,9);//商铺类型统计
        //入驻商家数量
        $shopnum=$this->StatisticsModel->getShopnum($this->city_id);
        //用户等级统计
        $memberlevel=$this->StatisticsModel->getmemberlevel($this->city_id);
        
        
        //自签单、带单、贡献排行
        $zylists=$this->StatisticsModel->zyshoplists($this->city_id);//带单列表
        $ddlists=$this->StatisticsModel->ddshoplists($this->city_id);//带单列表
        $gxlists=$this->StatisticsModel->gxshoplists($this->city_id);//带单列表

        //会员订单统计 饼状图
        $memberleveltj=$this->StatisticsModel->pie_member_leveltj($this->city_id);
        $membertotaltj=$this->StatisticsModel->pie_member_totaltj($this->city_id);


        $result=[
            'city_title'=>$websiteinfo["title"],

            'shop_brand'=>[//品牌排行
                'sale'=>$salemoneytj,
                'order'=>$ordertj,
                'points'=>$pointstj,
                'signin'=>$signintj,
            ],
            'order_hx'=>[//核销订单
                'yihexiao'=>$orderhx1,//已核销
                'weihexiao'=>$orderhx2,//未核销
            ],
            'points'=>$points,//积分
            'city_order_data'=>[//成交金额
                "amount_today"=>$city_order,
                "amount_all"=>$city_order_all,
            ],
            'city_sale'=>[//销售额趋势
                'day_sale'=>$day_sale,
                'month_sale'=>$month_sale,
            ],
            'pie_ordersource'=>$ordersouretj,
            'pie_ordertype'=>$ordertypetj,//右侧商家文字统计同样适用
            'member_level_tj'=>$memberlevel,//会员等级统计
            'shopcount'=>$shopnum,//入驻商家数量,
            'pie_order_tj'=>[//饼状图会员数量 订单数量
                'member_level_tj'=>$memberleveltj,
                'member_total_tj'=>$membertotaltj,

            ],
            'shop_brand_byorder'=>[
                'zylists'=>$zylists,
                'ddlists'=>$ddlists,
                'gxlists'=>$gxlists
            ],
        ];
        return $this->response($result);
    }

    /**
     * 小程序端大屏统计
     */
    public function miniindex1(){
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0){
          return $this->response($token);
        }else{
            $employeeModel=new EmployeeModel();
            $shopModel=new ShopModel();
            $member_id=$token[ 'data' ][ 'member_id' ];
           
            $employeeinfo=Db::name("shop_employee")->where("member_id=".$member_id)->find();
            $site_id=$employeeinfo[ 'shop_id' ];
            if(empty($site_id)){
                return $this->response(["code"=>1,"msg"=>"参数错误"]);

            }
            $shopinfo=Db::name("shop")->where("site_id=".$site_id)->find();
            //今日/累计成交总额
            $totalmoney_today=$this->StatisticsModel->mini_sums($site_id,"day");
            $totalmoney=$this->StatisticsModel->mini_sums($site_id);

            //用户数量
            $member_count=$this->StatisticsModel->mini_member_count($site_id);
            //订单数量
            $order_count=$this->StatisticsModel->mini_order_count($site_id);
            //商品列表
            $goodslist_today=$this->StatisticsModel->goodsranklist($site_id,"day");
            $goodslist=$this->StatisticsModel->goodsranklist($site_id);

            $result=[
                'shopinfo'=>[
                    'site_log'=>$shopinfo["logo"],
                    'site_name'=>$shopinfo["site_name"],
                    'username'=>$employeeinfo["username"],
                ],
                'totalmoney'=>[
                    'today'=>$totalmoney_today,
                    'all'=>$totalmoney,
                ],
                'member_count'=>$member_count,
                'order_count'=>$order_count,
                'goodslists'=>[
                   "today"=>$goodslist_today,
                   "all"=>$goodslist,

                ],
            ];
            return $this->response($result);

        }
        
    }

     /**
     * 小程序端大屏统计
     */
    public function miniindex(){
            $site_id=request()->param("site_id");
            if(empty($site_id)){
                return $this->response(["code"=>1,"msg"=>"参数错误"]);

            }
            $shopinfo=Db::name("shop")->alias("s")->join("website w","w.site_id=s.website_id")->where("s.site_id=".$site_id)
            ->field("s.*,w.title as website_title")->find();

            if(empty($shopinfo)){
                return $this->response(["code"=>1,"msg"=>"商家不存在"]);

            }
            $zlr_employee_id=$shopinfo["zlr_employee_id"];
            $employeeinfo=Db::name("shop_employee")->where("employee_id=".$zlr_employee_id)->find();
            if(empty($employeeinfo)){
                return $this->response(["code"=>1,"msg"=>"主理人不存在"]);
            }
            $headimg="";
            if(!empty($employeeinfo["headimg"])){
                    $headimg=$employeeinfo["headimg"];
            }else{
            if(!empty($employeeinfo["member_id"])){
                $memberinfo=Db::name("member")->where("member_id=".$employeeinfo["member_id"])->find();
                $headimg=$memberinfo["headimg"];
            }
            }
            $shopinfo["headimg"]=$headimg;

            //今日/累计成交总额
            $totalmoney_today=$this->StatisticsModel->mini_sums($site_id,"day");
            $totalmoney=$this->StatisticsModel->mini_sums($site_id);

            //用户数量
            $member_count=$this->StatisticsModel->mini_member_count($site_id);
            //订单数量
            $order_count=$this->StatisticsModel->mini_order_count($site_id);
            //商品列表
            $goodslist_today=$this->StatisticsModel->goodsranklist($site_id,"day");
            $goodslist=$this->StatisticsModel->goodsranklist($site_id);

            $result=[
                'shopinfo'=>[
                    'site_log'=>$shopinfo["logo"],
                    'site_name'=>$shopinfo["site_name"],
                    'username'=>$employeeinfo["username"],
                    'headimg'=>$shopinfo["headimg"],
                    'citytitle'=>$shopinfo["website_title"],


                ],
                'totalmoney'=>[
                    'today'=>$totalmoney_today,
                    'all'=>$totalmoney,
                ],
                'member_count'=>$member_count,
                'order_count'=>$order_count,
                'goodslists'=>[
                   "today"=>$goodslist_today,
                   "all"=>$goodslist,

                ],
            ];
            return $this->response($this->success($result));

        
        
    }


}