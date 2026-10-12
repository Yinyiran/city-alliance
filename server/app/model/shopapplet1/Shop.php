<?php


namespace app\model\shopapplet;


use app\model\BaseModel;
use app\model\goods\Goods;
use app\model\system\Config as ConfigModel;
use app\model\system\Group;
use app\model\web\WebSite as WebsiteModel;
use app\model\express\Config as ShopConfigModel;
use app\model\shopapplet\ShopDeposit;
use think\facade\Db;



/**
 * 店铺信息
 */
class Shop extends BaseModel
{
    //店铺状态 shop_status 0.关闭，1正常 2. 审核中

    /**
     * 获取店铺排名
     */
    public function getrank($shop_id){
        $condition="site_id=".$shop_id;
        $shopinfo=model("shop")->getInfo($condition);
        $points=$shopinfo["points"];
        $where="points >".$points;
        $count=model("shop")->where($condition)->where($where)->count();
        if(empty($count)){
            return 1;
        }else{
            return $count+1;
        }

    }

    /**
     * 商铺详情
     */
    public function getDetail($shop_id){
        $condition[]=["site_id","=",$shop_id];
        $shopinfo=Db::name("shop")->where($condition)->find();
        return $this->success($shopinfo);

    }



    
}