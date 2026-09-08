<?php


namespace app\shopapplet\controller;

use app\model\api\Merchant as MerchantModel;
use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use app\model\shopapplet\Website as WebsiteModel;
use app\model\shopapplet\ShopAccount as ShopAccountModel;

/**
 * 企业端
 * Class Merchant
 * @package app\shopapplet\controller
 */
class Merchant extends Common
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
        $this->websiteModel=new WebsiteModel();
        $this->shopModel=new ShopModel();
        $this->shopaccountModel=new ShopAccountModel();
        $this->site_id=session("site_id");
        //$this->member_id=session("member_id");


    }

    /**
     * 企业用户基本信息开户
     */
    public function openAccount()
    {
        $data = [
            'site_id' => $this->site_id,
            'enterprise_name' => $this->params['enterprise_name'] ?? '', //企业名称
            'province_id' => $this->params['province_id'] ?? '', //企业注册区域-省
            'city_id' => $this->params['city_id'] ?? '', //企业注册区域-市
            'district_id' => $this->params['district_id'] ?? '', //企业注册区域-区
            'address' => $this->params['address'] ?? '', //企业详细地址
            'business_licence_number' => $this->params['business_licence_number'] ?? '', //统一社会信用码
            'business_license_type' => $this->params['business_license_type'] ?? 0, //有效期类型:1=长期有效,0=有期限
            'business_license_start_time' => $this->params['business_license_start_time'] ?? '', //营业执照开始时间
            'business_license_end_time' => $this->params['business_license_end_time'] ?? '', //营业执照结束时间
            'legal_name' => $this->params['legal_name'] ?? '', //法人姓名
            'contact_name' => $this->params['contact_name'] ?? '', //联系人姓名
            'id_card_num' => $this->params['id_card_num'] ?? '', //法人身份证号码
            "contact_mobile" => $this->params['contact_mobile'] ?? '', //联系人手机号
            "id_card_time_type" => $this->params['id_card_time_type'] ?? 0, //法人证件有效期类型:1=长期有效,0=非长期有效
            "id_card_start_time" => $this->params['id_card_start_time'] ?? '', //证件有效期开始时间
            "id_card_end_time" => $this->params['id_card_end_time'] ?? '', //证件有效期结束时间
            "full_address"  =>  $this->params['full_address'] ?? '',
        ];
        $model = new MerchantModel();
        $result = $model->openAccount($data);
        return $this->response($result);
    }

    /**
     * 用户业务入驻
     */
    public function openCard()
    {
        $data = [
            'site_id' => $this->site_id,
            "card_type" => $this->params['card_type'] ?? 0, //结算卡类型:0=对公,1=对私法人,2=对私非法人
            "prov_id" => $this->params['prov_id'] ?? 0, //银行所在省
            "area_id" => $this->params['area_id'] ?? 0, //银行所在市
            "card_no"  =>  $this->params['card_no'] ?? 0, //结算卡卡号
            "bank_code"  =>  $this->params['bank_code'] ?? '', //总行银行号
            "branch_code"  =>  $this->params['branch_code'] ?? '', //联行号
            "cert_no"  =>  $this->params['cert_no'] ?? '', //持卡人身份证号码
            "cert_validity_type"  =>  $this->params['cert_validity_type'] ?? '', //持卡人证件有效期类型:1=长期有效,0=非长期有效
            "cert_begin_date"  =>  $this->params['cert_begin_date'] ?? '', //证件有效期开始时间
            "cert_end_date"  =>  $this->params['cert_end_date'] ?? '', //证件有效期结束时间
            'card_name'=>$this->params['card_name'] ?? '', //持卡人姓名
            "apply_status"=>1
        ];
        $model = new MerchantModel();
        $result = $model->openCard($data);
        return $this->response($result);
    }

    /**
     * 总行银行号列表
     */
    public function bankCode()
    {
        $data = [
            'site_id' => $this->site_id,
        ];
        $model = new MerchantModel();
        $result = $model->bankCode($data);
        return $this->response($result);
    }

    /**
     * 联行号
     */
    public function branchCode()
    {
        $data = [
            'site_id' => $this->site_id,
            'bank_num' => $this->params['bank_num'] ?? '',
            'page' => $this->params['page'] ?? 1,
            'page_size' => $this->params['page_size'] ?? PAGE_LIST_ROWS,
            'serial_name' => $this->params['serial_name'] ?? '', //支行名称
        ];
        $model = new MerchantModel();
        $result = $model->branchCode($data);
        return $this->response($result);
    }

    /**
     * 企业用户基本信息开户详情
     */
    public function openAccountDetail()
    {
        $data = [
            'site_id' => $this->site_id,
        ];
        $model = new MerchantModel();
        $result = $model->openAccountDetail($data);
        return $this->response($result);
    }

    /**
     * 关联企业列表
     */
    public function shopList()
    {
        $data = [
            'site_id' => $this->site_id,
            'employee' => session('employee_info'),
        ];
        $model = new MerchantModel();
        $result = $model->shopList($data);
        return $this->response($result);
    }

    /**
     * 切换商家
     */
    public function shopLogin()
    {
        $data = [
            'site_id' => $this->site_id,
            'new_site_id' => $this->params['site_id'] ?? 0, //切换的site_id
            'member_id' => $this->member_id,
        ];
        $model = new MerchantModel();
        $result = $model->shopLogin($data);
        return $this->response($result);
    }
}