<?php


namespace app\api\controller;

use app\api\Task;
use app\model\api\Api as ApiModel;
use app\model\member\MemberAccount;
use app\model\shop\Config;
use think\facade\Db;
use app\model\order\OrderCommon;

/**
 * 小程序端
 * Class Api
 * @package app\api\controller
 */
class Api extends BaseApi
{
    /**
     * BANNER列表
     */
    public function bannerList()
    {
        $type = $this->params['type'] ?? 1;
        $website_id = $this->params['website_id'];
        $banner = model('banner')->getList(['status' => 1, 'type' => $type, 'website_id' => $website_id], '*', 'sort desc');
        return $this->response($this->success($banner));
    }

    /**
     * 入驻商家/使用人数
     */
    public function websiteInfo()
    {
        $data = [
            'website_id' => $this->params['website_id'],
        ];
        $model = new ApiModel();
        $result = $model->websiteInfo($data);
        return $this->response($result);
    }

    /**
     * 导航
     */
    public function navList()
    {
        $data = [
            'website_id' => $this->params['website_id'],
        ];
        $model = new ApiModel();
        $result = $model->navList($data);
        return $this->response($result);
    }
    
    /**
     * 福利区筛选项
     */
    public function benefitOption()
    {
        $list = model('benefit_option')->getList([], '*', 'sort asc');
        return $this->response($this->success($list));
    }

    /**
     * 设置接口
     */
    public function getConfig()
    {
        $website_id = $this->params['website_id'];
        $website = model('website')->getInfo(['site_id'=>$website_id]);
        $data = [
            'douyin_image' => $website['douyin_image'],
        ];
        return $this->response($this->success($data));
    }

    /**
     * 简介
     */
    public function info()
    {
        $website = model('website')->getInfo(['site_id'=>0]);
        $data = [
            'video_url_info' => $website['video_url_info'],
            'content_info' => $website['content_info'],
            'mini_name' => $website['mini_name'],
            'mini_image' => $website['mini_image'],
        ];
        return $this->response($this->success($data));
    }

    /**
     * 协议
     */
    public function getAgreement()
    {
        $type = input('type', '1');//1=隐私政策,2=用户须知
        $config_model = new Config();
        switch ($type) {
            case 1:
                $result = $config_model->getPrivacy();
                break;
            case 2:
                $result = $config_model->getVip();
                break;
        }
        return $this->response($result ?? '');
    }
    
    //测试
    public function ces()
    {
//        dd((new Task())->pointExpire(7));
        dd((new Login())->createToken(7));
       /* $parent = 1;
        $member_info = model('member')->getInfo(['member_id'=>2]);
        if ($parent) {
            $p_fenxiao = model('fenxiao')->getInfo(['fenxiao_id'=>$parent]);
            if ($p_fenxiao) {
                $website = model('website')->getInfo(['site_id'=>0]);
                $yao_point = $website['yao_point'];
                if ($yao_point > 0) {
                    $member_account = new MemberAccount();
                    $member_account->addMemberAccount2($p_fenxiao['member_id'], 'point', $yao_point, 'yaoqing', $member_info['member_id'], "邀请获得积分".$yao_point, '邀请',1);
                }
            }
        }*/

//        $order = model('order')->getInfo(['order_id'=>80]);
//        $res = (new Huifujspay())->jspay($order);
//        dd($res);
        $order_no = '1756281980822471001';
        $resp_data['hf_seq_id'] = '';
        $order_id = Db::name('order_no')->where('order_no', $order_no)->find();
        if ($order_id) {
            $order = Db::name('order')->where('order_id', $order_id['order_id'])->find();
            $payModel = model('pay')->getInfo(['out_trade_no' => $order['out_trade_no']]);
            if ($payModel && $payModel['pay_status'] == 0) {
                model('pay')->update([
                    'hf_seq_id'=>$resp_data['hf_seq_id'] ?? '',
                    'req_seq_id'=>$resp_data['req_seq_id'] ?? '',
                    'req_date'=>$resp_data['req_date'] ?? '',
                ], ['id' => $payModel['id']]);
                $data = [
                    'out_trade_no' => $payModel['out_trade_no'],
                    'pay_type' => 'ONLINE_PAY',
                ];
                $res = (new OrderCommon())->orderOnlinePay($data);
            }


        }
        dd(44);
//        (new Huifujspay())->refund();
    }

    /**
     * 审核接口
     * 小程序审核模式开关：0 正常（展示视频） 1 审核模式（展示商品）
     * 平台总开关（website.site_id = 0）为 1 时全站生效，否则按当前分站（website.sh_status）生效
     */
    public function reviewStatus()
    {
        $site_id = intval($this->params['site_id'] ?? 0); // 商家/店铺站点id
        $website_id = intval($this->params['website_id'] ?? 0); // 所属分站id（对应 website 表的主键 site_id）

        // 未直接传分站 id 时，按商家找出它所属的分站（开关本身仍属于分站，不属于商家）
        if (empty($website_id) && !empty($site_id)) {
            $shop = model('shop')->getInfo([ 'site_id' => $site_id ], 'website_id');
            if (!empty($shop)) {
                $website_id = intval($shop[ 'website_id' ]);
            }
        }

        // 兼容旧版小程序：传 status 时写入开关（有分站 id 写对应分站，否则写平台总开关 site_id = 0）
        $status_param = $this->params['status'] ?? '';
        if ($status_param !== '') {
            model('website')->update([ 'sh_status' => intval($status_param) ], [ 'site_id' => $website_id ]);
        }

        // 平台总开关（site_id = 0）为 1 时全站生效
        $platform = model('website')->getInfo([ 'site_id' => 0 ], 'sh_status');
        $status = !empty($platform) ? intval($platform['sh_status']) : 0;

        // 平台总开关未开启时，按当前分站的设置生效
        if ($status != 1 && !empty($website_id)) {
            $website = model('website')->getInfo([ 'site_id' => $website_id ], 'sh_status');
            if (!empty($website)) {
                $status = intval($website[ 'sh_status' ]) == 1 ? 1 : 0;
            }
        }

        return $this->response($this->success([ 'status' => $status ]));
    }
}