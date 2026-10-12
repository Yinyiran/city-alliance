<?php


namespace app\admin\controller;

use app\model\member\Config as ConfigModel;
use think\facade\Config;
use think\facade\Db;

use think\Request;
use app\model\signin\Rules as RulesModel;
use app\model\signin\Records as MemberRecordModel;
use app\model\shop\Shop as ShopModel;
use app\model\member\Member as MemberModel;
use app\model\signin\SignSettlement as SignSettlementModel;
use addon\weapp\model\Weapp;

/**
 * 签到
 */
class Sign extends BaseAdmin
{
    protected $rules_type = [
        ['type_id' => 1, 'type_name' => "积分"],
        ['type_id' => 2, 'type_name' => "现金"],
        ['type_id' => 3, 'type_name' => "礼品"]
    ];
    protected $modelrules;
    protected $memberrecordmodel;
    protected $shopmodel;
    protected $membermodel;
    protected $settlementmodel;
    /**
     * 构造方法
     * Sign constructor.
     */
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->modelrules = new RulesModel();
        $this->memberrecordmodel = new MemberRecordModel();
        $this->shopmodel = new ShopModel();
        $this->membermodel = new MemberModel();
        $this->settlementmodel = new SignSettlementModel();
        $shoplist=$this->shopmodel->getShopList([],'*');
        $this->assign("shoplist",$shoplist['data']);

    }

    /**
     * 签到规则
     * @return mixed
     */
    public function rules()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition=[];
            $lists = $this->modelrules->getRulesPageList($condition, $page, $page_size, 'rules_id asc', '*');
            return $lists;
        }
        $goodslist=$this->modelrules->getGoodsBy2();
        $this->assign("goodslist",$goodslist);
        $this->assign("rules_type", $this->rules_type);
        return $this->fetch("sign/rules");
    }
    public function gettypename($typeid)
    {
        $typelist = $this->rules_type;
        foreach ($typelist as $key => $value) {
            if ($value['type_id'] == $typeid) {
                return $value['type_name'];
            }
        }
        return "";
    }
    public function addrules()
    {
        $data = [
            'title' => input('title', ''),
            'reward_value' => input('reward_value', ''),
            'reward_type' => input('reward_type', ''),
            'reward_typename' => $this->gettypename(input('reward_type', '')),
            'goods_ids' =>input('goods_id','')
        ];

        $res = $this->modelrules->addrules($data);
        return $res;
    }
    /**
     * 编辑规则
     */
    public function edits()
    {
        $data = [
            'rules_id' => input('rules_id', ''),
            'title' => input('title', ''),
            'reward_value' => input('reward_value', ''),
            'reward_type' => input('reward_type', ''),
            'reward_typename' => $this->gettypename(input('reward_type', '')),
        ];
        $res = $this->modelrules->editrules($data);
        return $res;
    }

    /**
     * 删除规则
     */
    public function delrules()
    {
        if (request()->isAjax()) {
            $rules_id = input('rules_id', '');
            if (empty($rules_id)) {
                return error(-1, "请选择要删除的规则");
            }
            $res = $this->modelrules->delrules($rules_id);
            return $res;
        }
    }

    public function deleterules()
    {
        return $this->delrules();
    }

    /**
     * 商家签到记录
     */
    public function shoplists(){
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_username = input('search_username', '');
			$search_mobile = input('search_mobile', '');
            $search_shop = input('shop_id', '');
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');
            $condition = [];
            if(trim($search_username)) {
                $condition[] = [ 'nickname', 'like', '%' . $search_username . '%' ];
            }
            if(trim($search_mobile)) {
                $condition[] = [ 'mobile', 'like', '%'. $search_mobile. '%' ];
            }
            if(trim($search_shop)) {
                $condition[] = [ 'shop_id', '=',  $search_shop  ];
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['a.create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["a.create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['a.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            $lists = $this->memberrecordmodel->getsigninfo($condition, $page, $page_size);
            return $lists;
        }
        return $this->fetch("sign/shoplists");
    }

    /**
     * 签到记录 按照用户显示  显示签到详情
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_username = input('search_username', '');
			$search_mobile = input('search_mobile', '');
            $search_shop = input('shop_id', '');
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');
            $condition = [];
            if(trim($search_username)) {
                $condition[] = [ 'nickname', 'like', '%' . $search_username . '%' ];
            }
            if(trim($search_mobile)) {
                $condition[] = [ 'mobile', 'like', '%'. $search_mobile. '%' ];
            }
            if(trim($search_shop)) {
                $condition[] = [ 'shop_id', '=',  $search_shop  ];
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['a.create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["a.create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['a.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            $lists = $this->memberrecordmodel->getRecordPageList($condition, $page, $page_size);
            return $lists;
        }
        return $this->fetch("sign/lists");
    }

    /**
     * 用户签到详情
     */
    public function getrecordinfo(Request  $request){
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
			$search_shop = input('shop_id', '');
			$search_member_id = input('member_id', '');
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');
            $condition = [];
            if(trim($search_shop)) {
                $condition[] = [ 'a.shop_id', '=',  $search_shop  ];
            }
            if(trim($search_member_id)) {
                $condition[] = [ 'a.member_id', '=',  $search_member_id  ];
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['a.create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["a.create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['a.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            $lists = $this->memberrecordmodel->getsigninfo($condition, $page, $page_size);
            return $lists;
        }
        $member_id=$request->param("member_id");
        $this->assign("member_id",$member_id);
        return $this->fetch("sign/info");
    }

    /**
     * 用户签到
     * 需要在用户表新增：二维码、签到天数字段
     */
    public function createqrcode(Request $request){
        require_once('./vendor/Weixin_1/WeapiOauth.php');
        require_once('./vendor/Weixin_1/WXBizDataCrypt.php');
        $member_id=$request->param("member_id");//用户id
       
         $oauth = new \Weixin_1\WeapiOauth();
         $scene="member_id=".$member_id;
         $page="pages/index/index";
         $data = '{"scene":"' . $scene . '","page":"' . $page . '","width":280}';
         $result=$oauth->create_qrcode($data,APPID,AppSecret);

        $filename = "qrcode_" . $member_id . ".jpg";//要生成的图片名字
 
        $IMG_PATH = "/public/qrcode";//保存服务器路径
        if (file_exists('./'.$IMG_PATH .'/'. $filename)) { //存在返回图片路径
             return $IMG_PATH .'/'. $filename;
         }
         $res = file_put_contents('./'.$IMG_PATH .'/'. $filename,$result);
         $filepath=$IMG_PATH .'/'. $filename;
         return  $IMG_PATH .'/'. $filename;
        
    }
    public function createqrcode1(Request $request){
        $weapp=new Weapp();
        $member_id=$request->param("member_id");//用户id
        $param=[
            'data'=>[
                'member_id'=>$member_id,//用户id  用于判断用户是否已经签到过了  签到过了  则提示用户签到天数已超过
            ],
            "page"=>"pages/signin/signin",//需要跳转的页面
            "scene"=>"member_id=".$member_id,//需要传递的参数  格式为：key1=value1&key2=value2
            "width"=>430,//二维码的宽度
            "auto_color"=>false,//自动配置线条颜色，如果颜色依然是黑色，则说明不支持配置,
            "qrcode_path"=>"./qrcode.png",//二维码的保存路径
            'qrcode_name'=>"qrcode-".$member_id.".png",
            'app_type'=>'signin'
        ];
        $result = $weapp->createQrcode($param);
        var_dump($result);
        if($result['code']==0){//生成二维码成功
            $filename=$result['data']['path'];//二维码的名称
        }else{
                echo "生成失败";
            //$this->error("二维码生成失败");
        }
    }

    //商户扫一扫 用户签到  商户通过扫描二维码得到二维码信息
   //修改表记录：member  新增shop_sign_days
   //修改表记录：sign_member_record  新增rewardtxt
    public function dosignin(Request $request){
        if($request->has("member_id") && $request->has("shop_id")){//如果有签到天数和签到规则id  则说明用户已经签到过了  则提示用户签到天数已超过
            $member_id=$request->param("member_id");
            $shop_id=$request->param("shop_id");//商户id
        }else{
            return $this->error("参数错误");
        }
        
        $result=$this->memberrecordmodel->shopsignin($member_id,$shop_id);//调用签到方法  并返回结果
        var_dump($result);die;
        return $result;
       
    }

    /**
     * 签到收费与返现结算设置
     */
    public function config()
    {
        if (request()->isAjax()) {
            $data = [
                'is_open' => input('is_open', 0),
                'shop_fee' => input('shop_fee', '3.00'),
                'user_reward' => input('user_reward', '2.00'),
                'cycle_type' => input('cycle_type', 'week'),
                'settle_day' => input('settle_day', 1),
                'remark' => input('remark', '')
            ];
            $res = $this->settlementmodel->setConfig($data);
            return $res;
        }

        $config = $this->settlementmodel->getConfig();
        $this->assign('config', $config['data']);
        return $this->fetch('sign/config');
    }

    /**
     * 签到收费与返现流水明细
     */
    public function settlementrecords()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_shop = input('shop_id', '');
            $search_member = input('search_member', '');
            $settle_status = input('settle_status', '');
            $period_key = input('period_key', '');
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');

            $condition = [];
            if (!empty($search_shop)) {
                $condition[] = ['r.site_id', '=', $search_shop];
            }
            if (!empty($search_member)) {
                $condition[] = ['m.nickname|m.username|m.mobile', 'like', '%' . trim($search_member) . '%'];
            }
            if ($settle_status !== '' && $settle_status !== null) {
                $condition[] = ['r.settle_status', '=', (int)$settle_status];
            }
            if (!empty($period_key)) {
                $condition[] = ['r.period_key', '=', trim($period_key)];
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['r.create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ['r.create_time', '<=', date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['r.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }

            $res = $this->settlementmodel->getRecordPageList($condition, $page, $page_size);
            return $res;
        }

        $stats = $this->settlementmodel->getSettlementStats();
        $this->assign('stats', $stats['data']);
        return $this->fetch('sign/settlement_records');
    }

    /**
     * 周结算账单汇总管理
     */
    public function bills()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $bill_type = input('bill_type', '');
            $pay_status = input('pay_status', '');
            $period_key = input('period_key', '');
            $search_text = input('search_text', '');

            $condition = [];
            if (!empty($bill_type)) {
                $condition[] = ['b.bill_type', '=', $bill_type];
            }
            if ($pay_status !== '' && $pay_status !== null) {
                $condition[] = ['b.pay_status', '=', (int)$pay_status];
            }
            if (!empty($period_key)) {
                $condition[] = ['b.period_key', '=', trim($period_key)];
            }
            if (!empty($search_text)) {
                $condition[] = ['b.bill_no|s.site_name|m.nickname|m.mobile', 'like', '%' . trim($search_text) . '%'];
            }

            $res = $this->settlementmodel->getBillPageList($condition, $page, $page_size);
            return $res;
        }

        $stats = $this->settlementmodel->getSettlementStats();
        $this->assign('stats', $stats['data']);
        return $this->fetch('sign/bills');
    }

    /**
     * 管理员一键手动触发周结算
     */
    public function settlemanual()
    {
        if (request()->isAjax()) {
            $force_all = input('force_all', 0); // 是否结算包含当前周的所有待结明细
            $res = $this->settlementmodel->cronWeeklySettlement(0, (bool)$force_all);
            return $res;
        }
        return error(-1, '非法请求');
    }

    /**
     * 管理员风控作废异常签到流水
     */
    public function invalidrecord()
    {
        if (request()->isAjax()) {
            $id = input('id', 0);
            $remark = input('remark', '管理员作废');
            $res = $this->settlementmodel->invalidRecord($id, $remark);
            return $res;
        }
        return error(-1, '非法请求');
    }
}
