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

    /**
     * 主理人收益看板汇总数据 (带单收益及会员收益)
     */
    public function getIncomeSummary()
    {
        $site_id = request()->param("site_id", $this->site_id);
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }

        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        if (!$shop) {
            return $this->response($this->error('', '店铺不存在'));
        }

        // 获取店铺员工/主理人关联的 member_id 集合
        $employee_members = Db::name('shop_employee')
            ->where('shop_id', $site_id)
            ->where('is_deleted', 0)
            ->column('member_id');
        if (!empty($shop['member_id']) && !in_array($shop['member_id'], $employee_members)) {
            $employee_members[] = $shop['member_id'];
        }
        if (empty($employee_members)) {
            $employee_members = [0];
        }

        // 1. 带单收益统计 (主理人 2% 带单佣金)
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $month_start = strtotime(date('Y-m-01 00:00:00'));

        $bring_total_income = floatval(Db::name('fenxiao_money')
            ->where('staff_member_id', 'in', $employee_members)
            ->sum('staff_commission'));
        $bring_today_income = floatval(Db::name('fenxiao_money')
            ->where('staff_member_id', 'in', $employee_members)
            ->where('create_time', '>=', $today_start)
            ->sum('staff_commission'));
        $bring_month_income = floatval(Db::name('fenxiao_money')
            ->where('staff_member_id', 'in', $employee_members)
            ->where('create_time', '>=', $month_start)
            ->sum('staff_commission'));
        $bring_order_count = Db::name('fenxiao_money')
            ->where('staff_member_id', 'in', $employee_members)
            ->count();

        // 2. 会员收益统计 (主理人 25% 会员总业绩 + 老板主动开通合伙人分成)
        $member_total_income = floatval(Db::name('member_level_settlement')
            ->where('shop_id', $site_id)
            ->sum('shop_money'));
        $member_today_income = floatval(Db::name('member_level_settlement')
            ->where('shop_id', $site_id)
            ->where('create_time', '>=', $today_start)
            ->sum('shop_money'));
        $member_month_income = floatval(Db::name('member_level_settlement')
            ->where('shop_id', $site_id)
            ->where('create_time', '>=', $month_start)
            ->sum('shop_money'));
        $member_order_count = Db::name('member_level_settlement')
            ->where('shop_id', $site_id)
            ->count();

        // 3. 汇总数据
        $data = [
            'site_id' => $site_id,
            'site_name' => $shop['site_name'],
            'account' => floatval($shop['account']), // 可提现余额
            'account_withdraw' => floatval($shop['account_withdraw']), // 已提现金额
            'bring_income' => [
                'total' => $bring_total_income,
                'today' => $bring_today_income,
                'month' => $bring_month_income,
                'count' => $bring_order_count,
            ],
            'member_income' => [
                'total' => $member_total_income,
                'today' => $member_today_income,
                'month' => $member_month_income,
                'count' => $member_order_count,
            ],
            'withdraw_rate' => 0.35, // 提现手续费 0.35%
        ];

        return $this->response($this->success($data));
    }

    /**
     * 商家带单收益订单明细
     */
    public function getBringOrderList()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $page = request()->param("page", 1);
        $page_size = request()->param("page_size", PAGE_LIST_ROWS);

        $employee_members = Db::name('shop_employee')
            ->where('shop_id', $site_id)
            ->where('is_deleted', 0)
            ->column('member_id');
        if (empty($employee_members)) $employee_members = [0];

        $list = Db::name('fenxiao_money')
            ->where('staff_member_id', 'in', $employee_members)
            ->order('create_time desc')
            ->paginate(['list_rows' => $page_size, 'page' => $page]);

        return $this->response($this->success([
            'list' => $list->items(),
            'total' => $list->total(),
            'page_count' => ceil($list->total() / $page_size)
        ]));
    }

    /**
     * 商家会员收益订单明细
     */
    public function getMemberIncomeList()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $page = request()->param("page", 1);
        $page_size = request()->param("page_size", PAGE_LIST_ROWS);

        $list = Db::name('member_level_settlement')->alias('mls')
            ->leftJoin('member m', 'm.member_id = mls.buy_member_id')
            ->field('mls.*, m.nickname, m.headimg, m.mobile')
            ->where('mls.shop_id', $site_id)
            ->order('mls.create_time desc')
            ->paginate(['list_rows' => $page_size, 'page' => $page]);

        return $this->response($this->success([
            'list' => $list->items(),
            'total' => $list->total(),
            'page_count' => ceil($list->total() / $page_size)
        ]));
    }

    /**
     * 店铺老板主动添加合伙人
     * 店铺老板可以设定金额给某个人开通合伙人，此金额只走主理人和分公司分佣
     */
    public function openPartnerByBoss()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $mobile = trim(request()->param("mobile", ''));
        $custom_price = floatval(request()->param("custom_price", 0));

        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        if (empty($mobile)) {
            return $this->response($this->error('', '请输入目标会员手机号'));
        }
        if ($custom_price <= 0) {
            return $this->response($this->error('', '开通金额必须大于0元'));
        }

        $target_member = Db::name('member')->where('mobile', $mobile)->where('is_delete', 0)->find();
        if (!$target_member) {
            return $this->response($this->error('', '未找到该手机号对应的会员，请先注册'));
        }

        if ($target_member['member_level'] >= 4) {
            return $this->response($this->error('', '该会员已是合伙人，无需重复开通'));
        }

        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        $website_id = $shop['website_id'] ?? 0;

        Db::startTrans();
        try {
            // 1. 创建会员开通订单
            $order_no = createno('create_order_no', '', $target_member['member_id']);
            $out_trade_no = 'CP' . date('YmdHis') . rand(1000, 9999);
            $order_data = [
                'level_id' => 4,
                'level_name' => '合伙人',
                'website_id' => $website_id,
                'price' => $custom_price,
                'fan_point' => 10000,
                'order_no' => $order_no,
                'out_trade_no' => $out_trade_no,
                'status' => 2, // 直接支付完成/已开通
                'create_time' => time(),
                'pay_time' => time(),
                'member_id' => $target_member['member_id'],
                'member_img' => $target_member['headimg'],
                'nickname' => $target_member['nickname']
            ];
            $order_id = Db::name('member_level_order')->insertGetId($order_data);

            // 2. 升级会员等级至合伙人
            Db::name('member')->where('member_id', $target_member['member_id'])->update([
                'member_level' => 4,
                'member_level_name' => '合伙人'
            ]);

            // 3. 赠送合伙人 10000 积分
            $member_account = new \app\model\member\MemberAccount();
            $member_account->addMemberAccount2($target_member['member_id'], 'point', 10000, 'member_level', $order_id, '开通合伙人赠送10000积分', '成为会员');

            // 4. 执行主理人与分公司两方拆分分润
            $settle_model = new \app\model\member\MemberLevelSettlement();
            $settle_res = $settle_model->calculateCustomPartnerSettlement($order_id, $site_id, $custom_price, $target_member['member_id']);
            if ($settle_res['code'] < 0) {
                Db::rollback();
                return $this->response($settle_res);
            }

            Db::commit();
            return $this->response($this->success([
                'order_id' => $order_id,
                'member_id' => $target_member['member_id'],
                'nickname' => $target_member['nickname'],
                'price' => $custom_price
            ], '恭喜！合伙人已成功开通并完成分润'));
        } catch (\Exception $e) {
            Db::rollback();
            return $this->response($this->error('', $e->getMessage()));
        }
    }
}