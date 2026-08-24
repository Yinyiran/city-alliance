<?php


namespace app\model\api;


use app\model\BaseModel;
use think\facade\Db;

class Api extends BaseModel
{
    //首页统计
    public function websiteInfo($data)
    {
        //入驻商家数
        $shop_num = $this->joinShopNum($data['website_id']);
        $use_num = $this->UseNum($data['website_id']);
        return $this->success([
            'shop_num' => $shop_num,
            'use_num' => $use_num,
        ]);
    }

    //计算入驻商家数
    public function joinShopNum($website_id)
    {
        if (empty($website_id)) $website_id = -1;
        $condition = [];
        $condition[] = ['website_id', '=', $website_id];
        $condition[] = ['shop_status', '=', 1];
        return model('shop')->getCount($condition);
    }

    //计算使用人数
    public function UseNum($website_id)
    {
        return model('member')->getCount(['website_id'=>$website_id]);
    }

    //导航
    public function navList($data)
    {
        $condition = [];
        $condition[] = ['website_id', '=', $data['website_id']];
        $condition[] = ['status', '=', 1];
        $field = 'id,website_id,name,image,url';
        $list = model('nav')->getList($condition, $field, 'sort desc');
        return $this->success($list);
    }
}