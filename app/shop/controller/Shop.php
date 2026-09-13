<?php


namespace app\shop\controller;

use addon\live\model\Goods;
use addon\sitemessage\model\MemberMessage;
use addon\sitemessage\model\Sitemessage;
use app\model\message\Message;
use app\model\message\Message as MessageModel;
use app\model\pay\Huifu;
use app\model\shop\OpenAccount;
use app\model\shop\Shop as ShopModel;
use app\model\shop\ShopAccount;
use app\model\shop\ShopDeposit;
use app\model\system\Address as AddressModel;
use app\model\order\OrderCommon;
use app\model\web\Config as ConfigModel;

/**
 * 店铺
 * Class Shop
 * @package app\shop\controller
 */
class Shop extends BaseShop
{


    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

    }

    /**
     * 店铺设置
     * @return mixed
     */
    public function config()
    {
        $shop_model = new ShopModel();
        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        if (request()->isAjax()) {
            $logo = input("logo", '');//店铺logo
            $avatar = input("avatar", '');//店铺头像（大图）
            $banner = input("banner", '');//店铺条幅
            $seo_keywords = input("seo_keywords", '');//店铺关键字
            $seo_description = input("seo_description", '');//店铺简介
            $data = array(
                "logo" => $logo,
                "avatar" => $avatar,
                "banner" => $banner,
                "seo_keywords" => $seo_keywords,
                "seo_description" => $seo_description,
//				'shop_status' => 1
            );
            $res = $shop_model->editShop($data, $condition);
            return $res;
        } else {
            $shop_info_result = $shop_model->getShopInfo($condition);
            $shop_info = $shop_info_result["data"];
            $this->assign("shop_info", $shop_info);
            return $this->fetch("shop/config");
        }

    }

    /**
     * 联系方式
     * @return mixed
     */
    public function contact()
    {
        $shop_model = new ShopModel();
        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        if (request()->isAjax()) {
            $province = input("province", 0);//省级地址
            $province_name = input("province_name", '');//省级地址
            $city = input("city");//市级地址
            $city_name = input("city_name", '');//市级地址
            $district = input("district", 0);//县级地址
            $district_name = input("district_name", '');//县级地址
            $community = input("community", 0);//乡镇地址
            $community_name = input("community_name", '');//乡镇地址
            $address = input("address", 0);//详细地址
            $full_address = input("full_address", 0);//完整地址
            $longitude = input("longitude", '');//经度
            $latitude = input("latitude", '');//纬度

            $qq = input("qq", '');//qq号
            $ww = input("ww", '');//阿里旺旺
            $email = input("email", '');//邮箱
            $telephone = input("telephone", '');//联系电话
            $name = input("name", '');//联系人姓名
            $mobile = input("mobile", '');//联系人手机号

            $work_week = input("work_week", '');//工作日  例如 : 1,2,3,4,5,6,7
            $start_time = input("start_time", 0);//开始时间
            $end_time = input("end_time", 0);//结束时间
            $data = array(
                "province" => $province,
                "province_name" => $province_name,
                "city" => $city,
                "city_name" => $city_name,
                "district" => $district,
                "district_name" => $district_name,
                "community" => $community,
                "community_name" => $community_name,
                "address" => $address,
                "full_address" => $full_address,
                "longitude" => $longitude,
                "latitude" => $latitude,
                "qq" => $qq,
                "ww" => $ww,
                "email" => $email,
                "telephone" => $telephone,
                "work_week" => $work_week,
                "start_time" => $start_time,
                "end_time" => $end_time,
                "name" => $name,
                "mobile" => $mobile
            );
            $store_data = [
                "province_id" => $province,
                "city_id" => $city,
                "district_id" => $district,
                "community_id" => $community,
                "address" => $address,
                "full_address" => $full_address,
                "longitude" => $longitude,
                "latitude" => $latitude,
            ];
            $res = $shop_model->editShop($data, $condition, $store_data);
            return $res;
        } else {
            $shop_info_result = $shop_model->getShopInfo($condition);
            $shop_info = $shop_info_result["data"];

            // 如果 shop 表中经纬度或地址为空，尝试从门店 store 表获取并补充
            $store_info = model('store')->getInfo(['site_id' => $this->site_id]);
            if (!empty($store_info)) {
                if ((empty($shop_info['longitude']) || empty($shop_info['latitude'])) && !empty($store_info['longitude']) && !empty($store_info['latitude'])) {
                    $shop_info['longitude'] = $store_info['longitude'];
                    $shop_info['latitude'] = $store_info['latitude'];
                }
                if (empty($shop_info['address']) && !empty($store_info['address'])) {
                    $shop_info['address'] = $store_info['address'];
                }
                if (empty($shop_info['full_address']) && !empty($store_info['full_address'])) {
                    $shop_info['full_address'] = $store_info['full_address'];
                }
                if (empty($shop_info['province']) && !empty($store_info['province_id'])) {
                    $shop_info['province'] = $store_info['province_id'];
                }
                if (empty($shop_info['city']) && !empty($store_info['city_id'])) {
                    $shop_info['city'] = $store_info['city_id'];
                }
                if (empty($shop_info['district']) && !empty($store_info['district_id'])) {
                    $shop_info['district'] = $store_info['district_id'];
                }
                if (empty($shop_info['community']) && !empty($store_info['community_id'])) {
                    $shop_info['community'] = $store_info['community_id'];
                }
            }

            $this->assign("info", $shop_info);

            //查询省级数据列表
            $address_model = new AddressModel();
            $list = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);

            //获取地址key配置
            $config_model = new ConfigModel();
            $config = $config_model->getMapConfig();
            $this->assign('map_key', $config['data']['value']);
            return $this->fetch("shop/contact");
        }

    }

    /**
     * 店铺装修
     * @return mixed
     */
    public function decoration()
    {
        return $this->fetch("shop/decoration");
    }

    /**
     * 账户信息
     * @return mixed
     */
    public function account()
    {
        $shop_model = new ShopModel();
        $shop_account_model = new ShopAccount();
        //获取商家转账设置
        $shop_withdraw_config = $shop_account_model->getShopWithdrawConfig();

        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        $shop_info_result = $shop_model->getShopInfo($condition, 'account, account_withdraw, shop_baozhrmb');
        $shop_info = $shop_info_result["data"];

        //获取店家结算账户信息
        $shop_cert_result = $shop_model->getShopCert($condition, 'bank_type, settlement_bank_account_name, settlement_bank_account_number, settlement_bank_name, settlement_bank_address');

        $this->assign("account", $shop_info['account']);//账户余额
        $this->assign("account_withdraw", $shop_info['account_withdraw']); //已提现
        $this->assign('order_calc', 0);//待结算
        $this->assign('shop_deposit', $shop_info['shop_baozhrmb']);//保证金
        $this->assign('shop_withdraw_config', $shop_withdraw_config['data']['value']);//商家转账设置
        $this->assign('shop_cert_info', $shop_cert_result['data']);//店家结算账户信息
        return $this->fetch("shop/account");
    }

    /**
     * 认证信息
     */
    public function cert()
    {
        $shop_model = new ShopModel();
        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        $cert_info_result = $shop_model->getShopCert($condition);
        $cert_info = $cert_info_result["data"];
        $this->assign("cert_info", $cert_info);
        return $this->fetch("shop/cert");
    }


    /**
     * 获取待结算列表
     */
    public function getOrderCalc()
    {
        if (request()->isAjax()) {
            $order = new OrderCommon();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $is_refund = input("is_refund", '');
            $condition = array(
                ['site_id', "=", $this->site_id],
                ['is_settlement', "=", 0],
                ['order_status', "not in", '0,-1'],
            );

            if ($is_refund !== '') {
                $condition[] = ['refund_status', '=', $is_refund];
            }
            $order_no = input('order_no', '');
            if ($order_no) {
                $condition[] = ['order_no', 'like', '%' . $order_no . '%'];
            }

            $list = $order->getOrderPageList($condition, $page, $page_size, 'create_time desc', $field = 'order_id,order_no,order_type_name,order_status_name,order_money,shop_money,platform_money,is_settlement,create_time,order_type');
            return $list;
        }
    }


    /**
     * 获取账户流水
     */
    public function getShopAccount()
    {
        if (request()->isAjax()) {
            $account_model = new ShopAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "create_time desc");
            $condition[] = ['site_id', "=", $this->site_id];

            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "between", [date_to_time($start_time), date_to_time($end_time)]];
            }
            $type_name = input('type_name', '');
            if ($type_name) {
                $condition[] = ['type_name', '=', $type_name];
            }

            $list = $account_model->getAccountPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 获取保证金记录
     */
    public function getShopDeposit()
    {
        if (request()->isAjax()) {
            $shop_deposit_model = new ShopDeposit();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "id desc");
            $search_text = input("search_text", "");

            $condition = array(
                ['site_id', "=", $this->site_id],
            );
            if (!empty($search_text)) {
                $condition[] = ['pay_no|pay_account_name', 'like', '%' . $search_text . '%'];
            }
            $list = $shop_deposit_model->getShopDepositPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 续签信息
     * @return mixed
     */
    public function reopen()
    {
        return $this->fetch("shop/reopen");
    }

    /**
     * 店铺推广
     * return
     */
    public function shopUrl()
    {
        //获取商品sku_id
        $shop_model = new ShopModel();
        $res = $shop_model->qrcode($this->site_id);
        // dump($res);exit;
        return $res;
    }

    public function openaccount()
    {
        $shop_model = new OpenAccount();
        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        if (request()->isAjax()) {

            $data = array(
                "enterprise_name" => input("enterprise_name", ''),
                "province_id" => input("province_id", 0),
                "city_id" => input("city_id", 0),
                "district_id" => input("district_id",0),
                "address" => input("address", ''),
                "business_licence_number" => input("business_licence_number", ''),
                "business_license_type" => input("business_license_type", 0),
                "business_license_start_time" => input("business_license_start_time", ''),
                "business_license_end_time" => input("business_license_end_time", ''),
                "legal_name" => input("legal_name", ''),
                "contact_name" => input("contact_name", ''),
                "id_card_num" => input("id_card_num", ''),
                "contact_mobile" => input("contact_mobile", ''),
                "id_card_time_type" => input("id_card_time_type", 0),
                "id_card_start_time" => input("id_card_start_time", ''),
                "id_card_end_time" => input("id_card_end_time", ''),
                "full_address"  =>  input('full_address',''),
            );


            $info = $shop_model->getOpenAccountInfo($condition);
            if ($info['data']){
                $condition[] = ['account_id'=>$info['data']['account_id']];
                $res = $shop_model->editOpenAccount($data,$condition);
            } else {
                $res = $shop_model->addOpenAccount($data);
            }
            return $res;
        } else {
            $shop_info_result = $shop_model->getOpenAccountInfo($condition);
            $account_info = $shop_info_result["data"];
            $this->assign("account_info", $account_info);

            $huifu_model = new Huifu();
            $bank_list = $huifu_model->getBankCode();
            $this->assign("bank_list", $bank_list);
            return $this->fetch("shop/openaccount");
        }

    }

    public function opencard()
    {
        $shop_model = new OpenAccount();

        if (request()->isAjax()){
            $data = [
                "card_type" => input("card_type", 0),
                "prov_id" => input("prov_id", 0),
                "area_id" => input("area_id", 0),
                "card_no"  =>  input('card_no',''),
                "bank_code"  =>  input('bank_code',''),
                "branch_code"  =>  input('branch_code',''),
                "cert_no"  =>  input('cert_no',''),
                "cert_validity_type"  =>  input('cert_validity_type',''),
                "cert_begin_date"  =>  input('cert_begin_date',''),
                "cert_end_date"  =>  input('cert_end_date',''),
                'card_name'=>input('card_name',''),
                "apply_status"=>1
            ];

            $condition = array(
                ["site_id", "=", $this->site_id]
            );

            $info = $shop_model->getOpenAccountInfo($condition);
            $condition[] = ['account_id'=>$info['data']['account_id']];

            $res = $shop_model->opencard($data,$condition,$info['data']['huifu_id']);
            return $res;
        }

    }

    public function branch()
    {
        $bank_code = input('bank_code','');
        if (request()->isAjax()){
            $open_account_model = new OpenAccount();
            $condition = [  ];

            $serial_name = input('serial_name', '');
            if ($serial_name) {
                $condition[] = [ 'serial_name', 'like', '%' . $serial_name . '%' ];
            }
            $bank_num = input('bank_num','');
            if ($bank_num) {
                $condition[] = [ 'bank_num', '=', $bank_num];
            }
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $open_account_model->getBranchPageList($condition, $page, $page_size);
            return $list;
        } else {
            $huifu_model = new Huifu();
            $bank_list = $huifu_model->getBankCode();
            $this->assign("bank_list", $bank_list);
            $this->assign("bank_code", $bank_code);
            return $this->fetch('shop/branch');
        }

    }
}