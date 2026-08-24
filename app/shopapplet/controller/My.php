<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;

use app\model\shopapplet\OrderCommon;
use app\model\shopapplet\Order as OrderModel;
use app\model\shopapplet\MemberRank as MemberRankModel;

class My extends Common
{
    protected $orderModel;
    protected $site_id;
    protected $remember_rank_model;


/**
 * 企业端  个人中心
 */
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

        $this->orderModel = new OrderModel();
        $this->remember_rank_model = new MemberRankModel();
        $this->site_id=session("site_id");
       

    }

  public function index(){
    $ordertj=$this->orderModel->ordertj($this->site_id);
    return $this->response($ordertj);
  }

  /**
   * 会员邀约排行
   */
  public function invitemember(){
     if(request()->has("selectby")){
            $selectby=request()->param("selectby");
        }else{
            $selectby="year";
        }
        if(request()->has("member_level")){
            $member_level=request()->param("member_level");
        }else{
            $member_level=0;
        }
        if(request()->has("page")){
            $page=request()->param("page");
        }else{
            $page=1;
        }
    $res = $this->remember_rank_model->invitememberlist($this->site_id, $this->member_id,$selectby,$member_level, $page);

    return $this->response($res);
  }
  /**
   * 员工自签单排行
   */
  public function orderbyselfrank(){
    if(request()->has("selectby")){
            $selectby=request()->param("selectby");
        }else{
            $selectby="year";
        }
     if(request()->has("page")){
            $page=request()->param("page");
        }else{
            $page=1;
        }
    $res = $this->remember_rank_model->orderbyselfrank($this->site_id,$selectby,$page);
    return $this->response($res);
  }

  /**
   * 员工贡献签单排行
   */
  public function conorderrank(){
    if(request()->has("selectby")){
        $selectby=request()->param("selectby");
    }else{
        $selectby="year";
    }
     if(request()->has("page")){
        $page=request()->param("page");
    }else{
        $page=1;
    }
    $res = $this->remember_rank_model->conorderrank($this->site_id,$selectby,$page);
    return $this->response($res);
  }

  /**
   * 用户邀约排行
   */
  public function userinviterank(){
   if(request()->has("selectby")){
        $selectby=request()->param("selectby");
    }else{
        $selectby="year";
    }
    if(request()->has("page")){
        $page=request()->param("page");
    }else{
        $page=1;
    }
    $res = $this->remember_rank_model->userinviterank($this->member_id,$selectby,$page);
    return $this->response($res);
  }



    

}