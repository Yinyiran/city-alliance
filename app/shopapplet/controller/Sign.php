<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\order\OrderCommon;
use app\model\message\Message;
use app\model\shopapplet\Records as RecordsModel;

/**
 * 签到
 */
class Sign extends Common
{

    protected $recordmodel;
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->recordmodel=new RecordsModel();
    }

    public function index(){
        $result=[
            'code'=>0,
            'data'=>[
                'today_count'=>$this->recordmodel->todaycount(session("shop_id")),
                'total_count'=>$this->recordmodel->totalcount(session("shop_id"))
            ],
            'message'=>'操作成功'
        ];
        return $this->response($result);
    }

    /**
     * 签到列表
     */
    public function getList(){
        $params=request()->param();
        if(empty($params["page"])){
            $page=1;
        }else{
            $page=$params["page"];
        }
        if(request()->has("sortcount")){
            $sortbycount=$params["sortcount"];
        }else{
            $sortbycount=1;
        }
        
        if(request()->has("sorttime")){
            $sortbytime=$params["sorttime"];
        }else{
            $sortbytime=1;
        }
         if(request()->has("searchtxt")){
            $searchtxt=$params["searchtxt"];
        }else{
            $searchtxt="";
        }
        session("shop_id",1);
        $res=$this->recordmodel->signlistbyshop(session("shop_id"),$searchtxt,$sortbycount,$sortbytime,$page);
        return $this->response($res);
    }
}