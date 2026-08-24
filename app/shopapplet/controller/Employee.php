<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\order\OrderCommon;
use app\model\message\Message;
use think\captcha\facade\Captcha;
use think\facade\Cache;

/**
 * 企业员工
 */
class Employee extends BaseApi
{
    protected $site_id;
    protected $member_id;
    protected $employeeModel;
    protected $shopModel;
    protected $city_id='';
    protected $shopinfo=[];

/**
 * 企业端  个人中心
 */
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        
        $this->employeeModel = new ShopEmployeeModel();
        $this->shopModel=new ShopModel();

       

    }

    /**
     * 添加员工
     */
    public function adds(){
        $data=request()->param();
        $data["shop_id"]=session("site_id");
        //短信验证码校验
       if (!isMobile($data['mobile'])) return $this->error('', '手机号格式错误');
        $verify_data = Cache::get($data['key']);
        if ($verify_data[ "mobile" ] == $data['mobile'] && $verify_data[ "code" ] == $data['code']) {
//        if (1) {
            $res=$this->employeeModel->adds($data,session("member_id"));
            return $this->response($res);
        }else{
            return $this->response($this->error("", "验证码错误"));
        }

    }

    /***
     * 发送验证码
     */
    public function mobliecode()
    {
        $mobile = $this->params[ 'mobile' ];
        if (!isMobile($mobile)) return $this->response($this->error('', '手机号格式错误'));
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
        $res = (new \app\api\controller\Sms())->txsms($mobile, $code);
        if ($res) {
            //将验证码存入缓存
            $key = 'employee_mobile_code_' . md5(uniqid(null, true));
            Cache::tag("employee_mobile_code")->set($key, [ 'mobile' => $mobile, 'code' => $code ], 600);
            return $this->response($this->success([ "key" => $key ]));
        } else {
            return $this->response($res);
        }
    }

}