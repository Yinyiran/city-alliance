<?php
/**

 */

namespace app\shopapplet\controller;


use addon\city\admin\controller\Shop;
use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\shopapplet\Website as WebsiteModel;
use app\model\goods\Goods as GoodsModel;
use app\model\system\User as ShopUser;
use app\model\order\OrderCommon;
use app\model\shopapplet\ShopAccount as ShopAccountModel;

class Index extends Common
{
    public $site_id;
    public $member_id=1;
    public $employeeModel;
    public $shopModel;
    public $city_id='';
    public $websiteModel;
    public $shopinfo=[];
    public $shopaccountModel;


    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        
        $this->employeeModel = new ShopEmployeeModel();
        $this->websiteModel = new WebsiteModel();
        $this->shopModel = new ShopModel();
        $this->shopaccountModel = new ShopAccountModel();
        if (empty($this->site_id)) {
            $this->site_id = session("site_id") ?: ($this->params['site_id'] ?? 0);
        }
        if (empty($this->city_id)) {
            $this->city_id = session("city_id") ?: 0;
        }
        

    }

    /**
     * 企业首页统计
     * @return mixed
     */
    public function getnum()
    {
     $this->shopinfo=session("shopinfo");
       $points=$this->shopinfo["points"];//商家积分
        $member_total=$this->employeeModel->getTeamCount($this->member_id,$this->site_id);
        $result=[
            'code'=>0,
            "message"=>"请求成功",
            'data'=>[
                'team_total'=>$member_total,
                'points'=>$points
            ]
        ];
        return $this->response($result);
    }

    /**
     * 团队人员明细
     */

    public function getteam(){
        $page=request()->param("page");
        if(empty($page)){
            $page=1;
        }
        $member_level=request()->param("member_level");
        if(empty($member_level)){
            $member_level=0;
        }
        $searchtxt="";
        $searchtxt=request()->param("searchtxt");
        $list=$this->employeeModel->getTeamMember($this->member_id,$this->site_id,$member_level,$page,$searchtxt);
     
        $ismanager=$this->employeeModel->ismanager($this->member_id, $this->site_id);
        $list["data"]["ismanager"]=$ismanager;
        return $this->response($list);
    }

 
    /**
     * 品牌(商家)排名
     * @param string $ranksort 排序方式 year/month
     * @param int $page 页码
     */
    public function getshopranklist(){
        $ranksort=request()->param("sortby");//sortby 取值：year/month
        if(empty($ranksort)){
            $ranksort="year";

        }
        $page=request()->param("page");
        if(empty($page)){
            $page=1;
        }
        $ranksort=strtolower($ranksort);
        $res=$this->shopaccountModel->getRankList($this->site_id,$this->city_id,$ranksort,$page);
        return $this->response($res);
    }


    /**
     * 员工(商家)排名
     * @param string $ranksort 排序方式 year/month
     * @param int $page 页码
     */
    public function getmemberranklist(){
        $ranksort=request()->param("sortby");//sortby 取值：year/month
        if(empty($ranksort)){
            $ranksort="year";

        }
        $page=request()->param("page");
        if(empty($page)){
            $page=1;
        }
        $ranksort=strtolower($ranksort);
        $res=$this->shopaccountModel->getmemberRankList($this->member_id,$this->site_id,$ranksort,$page);
        return $this->response($res);
    }

    

}