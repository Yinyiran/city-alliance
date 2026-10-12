<?php


namespace app\model\shopapplet;


use app\model\BaseModel;
use app\model\goods\Goods;
use app\model\system\Config as ConfigModel;
use app\model\system\Group;
use app\model\express\Config as ShopConfigModel;

/**
 * 店铺信息
 */
class Website extends BaseModel
{

   public function getDetail($cityname){
    $condition="title='".$cityname."'";
    $info=model("website")->getInfo($condition);
    return $info;

   }
    
}