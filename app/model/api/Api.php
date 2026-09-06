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
        $factor = $this->getUseNumFactor();
        $real_use_num = $this->realUseNum($data['website_id']);
        $use_num = (int)ceil($real_use_num * $factor);

        return $this->success([
            'shop_num' => $shop_num,
            'use_num' => $use_num,
            'real_use_num' => $real_use_num,
            'use_num_factor' => $factor,
        ]);
    }

    //获取后台倍数设置
    public function getUseNumFactor()
    {
        $website_info = model('website')->getInfo([['site_id', '=', 0]], 'use_num_factor');
        $factor = 1;
        if (!empty($website_info) && isset($website_info['use_num_factor']) && (float)$website_info['use_num_factor'] > 0) {
            $factor = (float)$website_info['use_num_factor'];
        }
        return $factor;
    }

    //获取真实使用人数
    public function realUseNum($website_id)
    {
        $condition = [];
        if (!empty($website_id)) {
            $condition[] = ['website_id', '=', $website_id];
        }
        return model('member')->getCount($condition);
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

    //计算使用人数（相乘并向上取整）
    public function UseNum($website_id)
    {
        $real_count = $this->realUseNum($website_id);
        $factor = $this->getUseNumFactor();

        return (int)ceil($real_count * $factor);
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