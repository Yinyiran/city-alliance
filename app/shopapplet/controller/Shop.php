<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use Carbon\Carbon;
use app\model\shopapplet\ShopDeposit as ShopDepositModel;
use think\facade\Db;

/**
 * 
 */
class Shop extends Common
{  
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        if (empty($this->site_id)) {
            $this->site_id = session("site_id");
        }
    }
    
    /**
     * 缴纳保证金按钮
     */
    public function deposit(){
        $shop_model=new ShopModel();
        $shopinfo=$shop_model->getDetail($this->site_id);
        if(empty($shopinfo)){
            return $this->error('', '店铺不存在');

        }
        $data=["site_id"=>$this->site_id,"member_id"=>$this->member_id];

        $res=$this->ordercreate($data);

        if(!empty($res)){
            $res=json_decode($res,true);
        }else{
            $res=['data'=>''];
        }
        $shopinfo["out_trade_no"]=$res["data"];
        $website = model('website')->getInfo(['site_id'=>$shopinfo['data']['website_id']]);
        $shopinfo['data']['deposit_amount']=$website['shop_deposit_amount'] ?? 0;
        return $this->response($shopinfo);
    }

    /**
     * 缴纳保证金
     */
    public function ordercreate($data){
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $shop_deposit_model = new ShopDepositModel();
        $result = $shop_deposit_model->ordercreate($data);

        return $this->response($result);
    }

    /**
     * 商户基本信息
     */
    public function getInfo(){
        $shop_model=new ShopModel();
        $site_id=request()->param("site_id");
        if(empty($site_id)){
            $site_id=$this->site_id;
        }
        $shopinfo=Db::name("shop")->alias("s")->join("shop_employee se","se.employee_id=s.zlr_employee_id","left")
        ->field("s.*,se.username as employee_username,se.mobile as employee_mobile,se.headimg as employee_headimg")
        ->where("s.site_id",$site_id)
        ->find();
        if(empty($shopinfo)){
            return $this->response($this->error('', '店铺不存在'));
        }
        $myinfo=Db::name("shop_employee")->where("member_id",$this->member_id)->find();
        if(empty($myinfo)){
            $myinfo=[];
        }
        // 查询当前登录用户的 member 信息以补充头像与昵称
        $member = Db::name("member")->where("member_id", $this->member_id)->field("headimg,nickname,username")->find();
        $user_headimg = '';
        $user_nickname = '';
        if (!empty($member)) {
            $user_headimg = $member['headimg'] ?? '';
            $user_nickname = $member['nickname'] ?? '';
        }
        if (!empty($myinfo)) {
            if (empty($myinfo['headimg']) && !empty($user_headimg)) {
                $myinfo['headimg'] = $user_headimg;
            }
            if (empty($myinfo['nickname']) && !empty($user_nickname)) {
                $myinfo['nickname'] = $user_nickname;
            }
        }
        $shopinfo["myinfo"] = $myinfo;
        $shopinfo["headimg"] = !empty($myinfo['headimg']) ? $myinfo['headimg'] : (!empty($user_headimg) ? $user_headimg : (!empty($shopinfo['logo']) ? $shopinfo['logo'] : ''));
        $shopinfo["user_headimg"] = !empty($myinfo['headimg']) ? $myinfo['headimg'] : (!empty($user_headimg) ? $user_headimg : '');
        $shopinfo["user_nickname"] = !empty($myinfo['username']) ? $myinfo['username'] : (!empty($user_nickname) ? $user_nickname : '');
        return $this->response($this->success($shopinfo));

    }

    /**
     * 获取商家小程序二维码（携带商家id）
     */
    public function getShopQrcode()
    {
        $site_id = request()->param("site_id");
        if (empty($site_id)) {
            $site_id = $this->site_id;
        }
        if (empty($site_id)) {
            return $this->response($this->error('', '店铺ID不能为空'));
        }

        $shop = Db::name("shop")->where("site_id", $site_id)->field("site_id,site_name,logo,avatar")->find();
        if (empty($shop)) {
            return $this->response($this->error('', '店铺不存在'));
        }

        $suid = !empty($this->member_id) ? (int)$this->member_id : 0;
        $qrcode_url = '';
        try {
            if (class_exists('\addon\weapp\model\Weapp')) {
                $weapp = new \addon\weapp\model\Weapp();
                $param = [
                    'data' => [
                        'site_id' => $site_id,
                        'suid' => $suid
                    ],
                    'page' => 'pages/index/index',
                    'width' => 430,
                    'qrcode_path' => 'upload/qrcode/shop',
                    'qrcode_name' => 'shop_' . $site_id . ($suid ? '_' . $suid : ''),
                    'app_type' => 'shop'
                ];
                $result = $weapp->createQrcode($param);
                if (!empty($result) && $result['code'] == 0 && !empty($result['data']['path'])) {
                    $qrcode_url = $result['data']['path'];
                }
            }
        } catch (\Exception $e) {
            // 异常容错
        }

        $qr_path = 'pages/index/index?site_id=' . $site_id . ($suid ? '&suid=' . $suid : '');
        $data = [
            'site_id' => (int)$site_id,
            'suid' => $suid,
            'site_name' => $shop['site_name'] ?? '',
            'logo' => !empty($shop['logo']) ? $shop['logo'] : ($shop['avatar'] ?? ''),
            'qrcode_url' => $qrcode_url,
            'qr_path' => $qr_path
        ];

        return $this->response($this->success($data));
    }

    /**
     * 门店Banner列表
     */
    public function bannerList()
    {
        $site_id = $this->site_id;
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        $list = model('banner')->getList([
            ['site_id', '=', $site_id]
        ], '*', 'sort desc, id desc');
        return $this->response($this->success($list ?: []));
    }

    /**
     * 添加门店Banner
     */
    public function bannerAdd()
    {
        $site_id = $this->site_id;
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }

        $image = $this->params['image'] ?? '';
        $name = $this->params['name'] ?? '';
        $url = $this->params['url'] ?? '';
        $sort = $this->params['sort'] ?? 0;

        if (empty($image)) {
            return $this->response($this->error('', '请上传Banner图片'));
        }

        $shop = model('shop')->getInfo([['site_id', '=', $site_id]], 'website_id');
        $website_id = $shop['website_id'] ?? 0;

        $data = [
            'site_id'     => $site_id,
            'website_id'  => $website_id,
            'name'        => $name,
            'image'       => $image,
            'url'         => $url,
            'status'      => 1,
            'sort'        => $sort,
            'create_time' => time(),
            'type'        => 3
        ];

        $res = model('banner')->add($data);
        return $this->response($this->success($res));
    }

    /**
     * 删除门店Banner
     */
    public function bannerDelete()
    {
        $site_id = $this->site_id;
        $id = $this->params['id'] ?? 0;
        if (empty($id)) {
            return $this->response($this->error('', '缺少参数'));
        }

        $res = model('banner')->delete([
            ['id', '=', $id],
            ['site_id', '=', $site_id]
        ]);
        return $this->response($this->success($res));
    }
}