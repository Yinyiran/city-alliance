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
        $site_id=(int)request()->param("site_id");
        if(empty($site_id)){
            $site_id=(int)$this->site_id;
        }
        $shopinfo=Db::name("shop")->alias("s")->join("shop_employee se","se.employee_id=s.zlr_employee_id","left")
        ->field("s.*,se.username as employee_username,se.mobile as employee_mobile,se.headimg as employee_headimg")
        ->where("s.site_id",$site_id)
        ->find();
        if(empty($shopinfo)){
            return $this->response($this->error('', '店铺不存在'));
        }

        // 查询当前登录用户信息
        $member = Db::name("member")->where("member_id", $this->member_id)->field("headimg,nickname,username,mobile")->find();
        $user_headimg = $member['headimg'] ?? '';
        $user_nickname = $member['nickname'] ?? '';
        $mobile = trim($member['mobile'] ?? '');

        // 判定用户在当前店铺是否是老板
        $is_owner = ($shopinfo['member_id'] == $this->member_id) || (!empty($mobile) && ($shopinfo['mobile'] == $mobile || $shopinfo['telephone'] == $mobile));

        // 查询当前店铺下的员工记录
        $myinfo = Db::name("shop_employee")->where([
            ['shop_id', '=', $site_id],
            ['is_deleted', '=', 0]
        ])->where(function($q) use ($mobile) {
            $q->where('member_id', $this->member_id);
            if (!empty($mobile)) {
                $q->whereOr('mobile', $mobile);
            }
        })->order('is_manager desc, employee_id asc')->find();

        if (empty($myinfo)) {
            $myinfo = [];
        }

        if ($is_owner) {
            $myinfo['is_manager'] = 1;
            if (empty($shopinfo['member_id'])) {
                Db::name('shop')->where('site_id', $site_id)->update(['member_id' => $this->member_id]);
            }
            if (!empty($myinfo['employee_id'])) {
                if ($myinfo['is_manager'] != 1) {
                    Db::name('shop_employee')->where('employee_id', $myinfo['employee_id'])->update(['is_manager' => 1]);
                }
                if (empty($myinfo['member_id'])) {
                    Db::name('shop_employee')->where('employee_id', $myinfo['employee_id'])->update(['member_id' => $this->member_id]);
                    $myinfo['member_id'] = $this->member_id;
                }
            }
            if (empty($myinfo['username'])) {
                $myinfo['username'] = !empty($user_nickname) ? $user_nickname : '老板';
            }
            if (empty($myinfo['shop_id'])) {
                $myinfo['shop_id'] = $site_id;
            }
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
    /**
     * 商家端收益看板汇总数据 (支持老板与员工双角色自适应)
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

        $this->checkToken();
        $curr_member_id = $this->member_id;

        $member = Db::name('member')->where('member_id', $curr_member_id)->field('nickname,username,headimg,mobile')->find();
        $mobile = trim($member['mobile'] ?? '');

        // 判断是否是老板：
        // 1. shop.member_id 绑定了当前用户
        // 2. shop.mobile 或 shop.telephone 是当前用户的手机号
        $is_owner = ($shop['member_id'] == $curr_member_id)
            || (!empty($mobile) && ($shop['mobile'] == $mobile || $shop['telephone'] == $mobile));

        // 判断当前登录者在 shop_employee 中记录
        $emp = Db::name('shop_employee')
            ->where('shop_id', $site_id)
            ->where(function($q) use ($curr_member_id, $mobile) {
                $q->where('member_id', $curr_member_id);
                if (!empty($mobile)) {
                    $q->whereOr('mobile', $mobile);
                }
            })
            ->where('is_deleted', 0)
            ->order('is_manager desc, employee_id asc')
            ->find();

        $is_manager = 0;
        if ($is_owner || ($emp && $emp['is_manager'] == 1)) {
            $is_manager = 1;
            // 自动补齐 shop.member_id
            if (empty($shop['member_id']) && !empty($curr_member_id)) {
                Db::name('shop')->where('site_id', $site_id)->update(['member_id' => $curr_member_id]);
                $shop['member_id'] = $curr_member_id;
            }
            if (!empty($emp)) {
                if ($emp['is_manager'] != 1) {
                    Db::name('shop_employee')->where('employee_id', $emp['employee_id'])->update(['is_manager' => 1]);
                }
                if (empty($emp['member_id'])) {
                    Db::name('shop_employee')->where('employee_id', $emp['employee_id'])->update(['member_id' => $curr_member_id]);
                }
            }
        }

        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $month_start = strtotime(date('Y-m-01 00:00:00'));

        if ($is_manager == 1) {
            // ==================== 1. 老板角色（主理人2%带单 + 25%会员总业绩） ====================
            $employee_members = Db::name('shop_employee')
                ->where('shop_id', $site_id)
                ->where('is_deleted', 0)
                ->column('member_id');
            if (!empty($shop['member_id']) && !in_array($shop['member_id'], $employee_members)) {
                $employee_members[] = $shop['member_id'];
            }
            if (empty($employee_members)) $employee_members = [0];

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

            $role = 'manager';
            $role_name = '主理人/店长';
        } else {
            // ==================== 2. 员工角色（员工本人1%带单 + 作为合伙人的会员推广奖励） ====================
            $target_member_id = $curr_member_id ?: 0;

            $bring_total_income = floatval(Db::name('fenxiao_money')
                ->where('staff_member_id', $target_member_id)
                ->sum('staff_commission'));
            $bring_today_income = floatval(Db::name('fenxiao_money')
                ->where('staff_member_id', $target_member_id)
                ->where('create_time', '>=', $today_start)
                ->sum('staff_commission'));
            $bring_month_income = floatval(Db::name('fenxiao_money')
                ->where('staff_member_id', $target_member_id)
                ->where('create_time', '>=', $month_start)
                ->sum('staff_commission'));
            $bring_order_count = Db::name('fenxiao_money')
                ->where('staff_member_id', $target_member_id)
                ->count();

            // 员工作为合伙人享受的邀约会员奖励
            $member_total_income = floatval(Db::name('member_level_settlement')
                ->where('invite_member_id', $target_member_id)
                ->sum('invite_money'));
            $member_today_income = floatval(Db::name('member_level_settlement')
                ->where('invite_member_id', $target_member_id)
                ->where('create_time', '>=', $today_start)
                ->sum('invite_money'));
            $member_month_income = floatval(Db::name('member_level_settlement')
                ->where('invite_member_id', $target_member_id)
                ->where('create_time', '>=', $month_start)
                ->sum('invite_money'));
            $member_order_count = Db::name('member_level_settlement')
                ->where('invite_member_id', $target_member_id)
                ->count();

            $role = 'staff';
            $role_name = '门店员工';
        }

        $data = [
            'site_id' => $site_id,
            'site_name' => $shop['site_name'],
            'role' => $role,
            'is_manager' => $is_manager,
            'role_name' => $role_name,
            'account' => floatval($shop['account']),
            'account_withdraw' => floatval($shop['account_withdraw']),
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
            'total_income' => round($bring_total_income + $member_total_income, 2),
            'withdraw_rate' => 0.35,
        ];

        return $this->response($this->success($data));
    }

    /**
     * 商家带单收益订单明细 (自适应老板与员工)
     */
    public function getBringOrderList()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $page = request()->param("page", 1);
        $page_size = request()->param("page_size", PAGE_LIST_ROWS);

        $this->checkToken();
        $curr_member_id = $this->member_id;

        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        $emp = Db::name('shop_employee')
            ->where('shop_id', $site_id)
            ->where('member_id', $curr_member_id)
            ->where('is_deleted', 0)
            ->find();

        $is_manager = (($emp && $emp['is_manager'] == 1) || ($shop && $shop['member_id'] == $curr_member_id)) ? 1 : 0;

        if ($is_manager == 1) {
            $employee_members = Db::name('shop_employee')
                ->where('shop_id', $site_id)
                ->where('is_deleted', 0)
                ->column('member_id');
            if (!empty($shop['member_id']) && !in_array($shop['member_id'], $employee_members)) {
                $employee_members[] = $shop['member_id'];
            }
            if (empty($employee_members)) $employee_members = [0];

            $list = Db::name('fenxiao_money')
                ->where('staff_member_id', 'in', $employee_members)
                ->order('create_time desc')
                ->paginate(['list_rows' => $page_size, 'page' => $page]);
        } else {
            // 员工仅查看自己带单提成
            $list = Db::name('fenxiao_money')
                ->where('staff_member_id', $curr_member_id ?: 0)
                ->order('create_time desc')
                ->paginate(['list_rows' => $page_size, 'page' => $page]);
        }

        return $this->response($this->success([
            'list' => $list->items(),
            'total' => $list->total(),
            'is_manager' => $is_manager,
            'page_count' => ceil($list->total() / $page_size)
        ]));
    }

    /**
     * 商家会员收益订单明细 (自适应老板与员工)
     */
    public function getMemberIncomeList()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $page = request()->param("page", 1);
        $page_size = request()->param("page_size", PAGE_LIST_ROWS);

        $this->checkToken();
        $curr_member_id = $this->member_id;

        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        $emp = Db::name('shop_employee')
            ->where('shop_id', $site_id)
            ->where('member_id', $curr_member_id)
            ->where('is_deleted', 0)
            ->find();

        $is_manager = (($emp && $emp['is_manager'] == 1) || ($shop && $shop['member_id'] == $curr_member_id)) ? 1 : 0;

        if ($is_manager == 1) {
            $list = Db::name('member_level_settlement')->alias('mls')
                ->leftJoin('member m', 'm.member_id = mls.buy_member_id')
                ->field('mls.*, m.nickname, m.headimg, m.mobile')
                ->where('mls.shop_id', $site_id)
                ->order('mls.create_time desc')
                ->paginate(['list_rows' => $page_size, 'page' => $page]);
        } else {
            // 员工查看自己作为合伙人邀请的会员收益明细
            $list = Db::name('member_level_settlement')->alias('mls')
                ->leftJoin('member m', 'm.member_id = mls.buy_member_id')
                ->field('mls.*, m.nickname, m.headimg, m.mobile')
                ->where('mls.invite_member_id', $curr_member_id ?: 0)
                ->order('mls.create_time desc')
                ->paginate(['list_rows' => $page_size, 'page' => $page]);
        }

        return $this->response($this->success([
            'list' => $list->items(),
            'total' => $list->total(),
            'is_manager' => $is_manager,
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

        $target_member = Db::name('member')->where('mobile', $mobile)->where('is_delete', 0)->find();
        if (!$target_member) {
            return $this->response($this->error('', '未找到该手机号对应的会员，请先注册'));
        }

        if ($target_member['member_level'] >= 4) {
            return $this->response($this->error('', '该会员已是合伙人，无需重复开通'));
        }

        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        if (!$shop) {
            return $this->response($this->error('', '未找到店铺信息'));
        }

        $level_id = !empty($shop['level_id']) ? intval($shop['level_id']) : 1;
        $level_info = Db::name('shop_level')->where('level_id', $level_id)->find();
        if (!$level_info) {
            $level_info = Db::name('shop_level')->where('level_id', 1)->find();
        }
        $max_partner = !empty($level_info['max_partner_count']) ? intval($level_info['max_partner_count']) : 2;

        // 校验合伙人数量上限
        $empModel = new \app\model\shopapplet\ShopEmployee();
        $partner_count = $empModel->getTeamMemberBaseQuery($shop['member_id'], $site_id)
            ->where('m.member_level', 4)
            ->count('DISTINCT m.member_id');

        if ($partner_count >= $max_partner) {
            return $this->response($this->error('', "当前店铺合伙人数量已达【{$level_info['level_name']}】上限（{$max_partner}人），请升级为更高等级店铺"));
        }

        // 检查免扣费名额（优先使用店铺独立分配的免扣费名额，若无则使用店铺等级配置的免费额度）
        $level_free_count = intval($level_info['free_partner_count'] ?? 0);
        $shop_free_count = intval($shop['custom_partner_free_num'] ?? 0);
        $free_num = max($shop_free_count, $level_free_count);
        $used_free_num = intval($shop['custom_partner_used_free_num'] ?? 0);
        $is_free_quota = ($used_free_num < $free_num);

        $partner_open_fee = floatval($level_info['partner_open_fee'] ?? 50.00);

        // 如果免费名额已用完，则必须达到合伙人开通费用标准；若在免费额度内，支持0元或自定义金额
        if (!$is_free_quota) {
            if ($custom_price < $partner_open_fee) {
                return $this->response($this->error('', "合伙人开通费用不能低于 ￥{$partner_open_fee} 元"));
            }
        } else {
            if ($custom_price < 0) {
                return $this->response($this->error('', '开通金额不能小于 0 元'));
            }
        }

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

            // 4. 若享有免扣费名额，消耗名额计数
            if ($is_free_quota) {
                Db::name('shop')->where('site_id', $site_id)->inc('custom_partner_used_free_num', 1)->update();
            }

            // 5. 执行主理人与分公司两方拆分分润 (支持免抽佣配额)
            $settle_model = new \app\model\member\MemberLevelSettlement();
            $settle_res = $settle_model->calculateCustomPartnerSettlement($order_id, $site_id, $custom_price, $target_member['member_id'], $is_free_quota);
            if ($settle_res['code'] < 0) {
                Db::rollback();
                return $this->response($settle_res);
            }

            Db::commit();
            $msg = $is_free_quota ? '合伙人开通成功！本单享受免抽佣特权，收益100%归门店' : '合伙人已成功开通并完成分润';
            return $this->response($this->success([
                'order_id' => $order_id,
                'member_id' => $target_member['member_id'],
                'nickname' => $target_member['nickname'],
                'price' => $custom_price,
                'is_free_quota' => $is_free_quota ? 1 : 0
            ], $msg));
        } catch (\Exception $e) {
            Db::rollback();
            return $this->response($this->error('', $e->getMessage()));
        }
    }

    /**
     * 获取商户合伙人开通配额与限制信息
     */
    public function getPartnerQuotaInfo()
    {
        $site_id = request()->param("site_id", $this->site_id);
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        if (!$shop) {
            return $this->response($this->error('', '店铺不存在'));
        }

        $level_id = !empty($shop['level_id']) ? intval($shop['level_id']) : 1;
        $level_info = Db::name('shop_level')->where('level_id', $level_id)->find();
        if (!$level_info) {
            $level_info = Db::name('shop_level')->where('level_id', 1)->find();
        }
        $max_partner = !empty($level_info['max_partner_count']) ? intval($level_info['max_partner_count']) : 2;

        $empModel = new \app\model\shopapplet\ShopEmployee();
        $partner_count = $empModel->getTeamMemberBaseQuery($shop['member_id'], $site_id)
            ->where('m.member_level', 4)
            ->count('DISTINCT m.member_id');

        $level_free_count = intval($level_info['free_partner_count'] ?? 0);
        $shop_free_count = intval($shop['custom_partner_free_num'] ?? 0);
        $free_num = max($shop_free_count, $level_free_count);
        $used_free_num = intval($shop['custom_partner_used_free_num'] ?? 0);
        $remain_free_quota = max(0, $free_num - $used_free_num);
        $partner_open_fee = floatval($level_info['partner_open_fee'] ?? 50.00);

        return $this->response($this->success([
            'free_quota' => $free_num,
            'used_quota' => $used_free_num,
            'remain_free_quota' => $remain_free_quota,
            'partner_count' => $partner_count,
            'max_partner_count' => $max_partner,
            'is_at_limit' => ($partner_count >= $max_partner) ? 1 : 0,
            'level_id' => $level_id,
            'level_name' => $level_info['level_name'] ?? '普通店铺',
            'min_price' => $partner_open_fee,
            'partner_open_fee' => $partner_open_fee,
            'free_partner_count' => $free_num
        ]));
    }

    /**
     * 获取店铺等级详情及有效期限
     */
    public function getShopLevelDetail()
    {
        $site_id = request()->param("site_id", $this->site_id);
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        if (!$shop) {
            return $this->response($this->error('', '店铺不存在'));
        }

        $level_id = !empty($shop['level_id']) ? intval($shop['level_id']) : 1;
        $level = Db::name('shop_level')->where('level_id', $level_id)->find();
        if (!$level) {
            $level = [
                'level_id' => 1,
                'level_name' => '普通店铺',
                'price' => 598.00,
                'validity_type' => 1,
                'validity_years' => 1,
                'max_partner_count' => 2,
                'max_video_count' => 3,
                'base_points' => 0,
                'badge_icon' => '/upload/level/level1.png'
            ];
        }

        // expire_time: 数据库中0表示无限期/长期有效
        $expire_time = intval($shop['expire_time']);
        $days_left = 0;

        // 普通店铺(level_id == 1)为基础入驻身份，无到期概念；至尊店铺(level_id == 3/validity_type == 2)或标记永久有效，或 expire_time == 0(无限期)
        $is_permanent = ($level_id == 1 || !empty($shop['is_permanent']) || intval($level['validity_type']) === 2 || $expire_time == 0) ? 1 : 0;

        if ($level_id == 1) {
            $days_text = '长期有效';
        } elseif ($is_permanent) {
            $days_text = '永久合作';
        } elseif ($expire_time > time()) {
            $days_left = ceil(($expire_time - time()) / 86400);
            $days_text = "剩余 {$days_left} 天";
        } else {
            $days_text = '已到期';
        }

        $start_time = !empty($shop['level_start_time']) ? $shop['level_start_time'] : (!empty($shop['create_time']) ? $shop['create_time'] : time());

        return $this->response($this->success([
            'site_id' => $site_id,
            'site_name' => $shop['site_name'],
            'logo' => $shop['logo'],
            'level_id' => $level_id,
            'level_name' => $level['level_name'],
            'badge_icon' => $level['badge_icon'],
            'is_permanent' => $is_permanent,
            'start_time' => date('Y-m-d', $start_time),
            'expire_time' => $is_permanent ? '永久有效' : ($expire_time > 0 ? date('Y-m-d', $expire_time) : '永久有效'),
            'days_left' => $days_left,
            'days_text' => $days_text,
            'max_partner_count' => $level['max_partner_count'],
            'max_video_count' => $level['max_video_count'],
            'base_points' => $level['base_points'],
            'free_partner_count' => intval($level['free_partner_count'] ?? 0),
            'partner_open_fee' => floatval($level['partner_open_fee'] ?? 50.00),
            'free_partner_quota' => max(intval($shop['custom_partner_free_num'] ?? 0), intval($level['free_partner_count'] ?? 0)),
            'used_partner_quota' => intval($shop['custom_partner_used_free_num'] ?? 0)
        ]));
    }

    /**
     * 获取所有可供购买/升级的店铺等级套餐
     */
    public function getShopLevelPackages()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $shop = !empty($site_id) ? Db::name('shop')->where('site_id', $site_id)->find() : null;
        $current_level_id = $shop ? intval($shop['level_id']) : 1;

        $levels = Db::name('shop_level')
            ->where('status', 1)
            ->order('sort asc, level_id asc')
            ->select()
            ->toArray();

        foreach ($levels as &$lvl) {
            $lvl['is_current'] = ($lvl['level_id'] == $current_level_id) ? 1 : 0;
            $lvl['can_upgrade'] = ($lvl['level_id'] > $current_level_id) ? 1 : 0;
            $lvl['can_renew'] = ($lvl['level_id'] == $current_level_id && $lvl['validity_type'] != 2 && $lvl['level_id'] != 1) ? 1 : 0;
        }

        return $this->response($this->success([
            'current_level_id' => $current_level_id,
            'list' => $levels
        ]));
    }

    /**
     * 创建店铺等级购买/升级订单
     */
    public function createShopLevelOrder()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $to_level_id = intval(request()->param("to_level_id", 0));

        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        $target_level = Db::name('shop_level')->where('level_id', $to_level_id)->where('status', 1)->find();
        if (!$target_level) {
            return $this->response($this->error('', '无效的目标店铺等级'));
        }

        $shop = Db::name('shop')->where('site_id', $site_id)->find();
        if (!$shop) {
            return $this->response($this->error('', '店铺不存在'));
        }

        $from_level_id = intval($shop['level_id'] ?? 1);
        $order_money = floatval($target_level['price']);

        $order_no = 'LO' . date('YmdHis') . rand(1000, 9999);
        $out_trade_no = 'LT' . date('YmdHis') . rand(10000, 99999);

        $order_data = [
            'order_no' => $order_no,
            'out_trade_no' => $out_trade_no,
            'site_id' => $site_id,
            'from_level_id' => $from_level_id,
            'to_level_id' => $to_level_id,
            'order_money' => $order_money,
            'pay_status' => 0,
            'pay_type' => 'wechat',
            'create_time' => time()
        ];
        $order_id = Db::name('shop_level_order')->insertGetId($order_data);

        return $this->response($this->success([
            'order_id' => $order_id,
            'order_no' => $order_no,
            'out_trade_no' => $out_trade_no,
            'order_money' => $order_money,
            'target_level_name' => $target_level['level_name']
        ], '创建升级订单成功'));
    }

    /**
     * 确认并完成店铺等级升级 (模拟/支付回调完成)
     */
    public function completeShopLevelOrder()
    {
        $out_trade_no = trim(request()->param("out_trade_no", ''));
        if (empty($out_trade_no)) {
            return $this->response($this->error('', '交易流水号不能为空'));
        }

        $order = Db::name('shop_level_order')->where('out_trade_no', $out_trade_no)->find();
        if (!$order) {
            return $this->response($this->error('', '未找到对应升级订单'));
        }
        if ($order['pay_status'] == 1) {
            return $this->response($this->success('', '该订单已完成支付'));
        }

        $target_level = Db::name('shop_level')->where('level_id', $order['to_level_id'])->find();
        if (!$target_level) {
            return $this->response($this->error('', '目标等级不存在'));
        }

        Db::startTrans();
        try {
            // 1. 更新订单为已支付
            Db::name('shop_level_order')->where('order_id', $order['order_id'])->update([
                'pay_status' => 1,
                'pay_time' => time()
            ]);

            // 2. 更新店铺等级与有效期
            $shop = Db::name('shop')->where('site_id', $order['site_id'])->find();
            $update_data = [
                'level_id' => $target_level['level_id'],
                'level_name' => $target_level['level_name']
            ];

            if ($target_level['validity_type'] == 2) {
                // 至尊店铺：永久有效
                $update_data['is_permanent'] = 1;
                $update_data['expire_time'] = 0;
            } else {
                $years = intval($target_level['validity_years'] ?: 1);
                $base_time = ($shop['expire_time'] > time()) ? $shop['expire_time'] : time();
                $update_data['is_permanent'] = 0;
                $update_data['expire_time'] = $base_time + ($years * 365 * 86400);
            }

            if (empty($shop['level_start_time'])) {
                $update_data['level_start_time'] = time();
            }

            if (!empty($target_level['free_partner_count'])) {
                $update_data['custom_partner_free_num'] = max(intval($shop['custom_partner_free_num'] ?? 0), intval($target_level['free_partner_count']));
            }

            Db::name('shop')->where('site_id', $order['site_id'])->update($update_data);

            // 3. 发放赠送的品牌基础积分
            $bonus_points = intval($target_level['base_points'] ?? 0);
            if ($bonus_points > 0) {
                Db::name('shop')->where('site_id', $order['site_id'])->inc('points', $bonus_points)->update();
            }

            Db::commit();
            return $this->response($this->success([
                'level_id' => $target_level['level_id'],
                'level_name' => $target_level['level_name'],
                'is_permanent' => $update_data['is_permanent'],
                'bonus_points' => $bonus_points
            ], '恭喜！店铺等级升级成功'));
        } catch (\Exception $e) {
            Db::rollback();
            return $this->response($this->error('', $e->getMessage()));
        }
    }

    /**
     * 获取商户多标签二维码列表
     */
    public function getShopQrcodeList()
    {
        $site_id = request()->param("site_id", $this->site_id);
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }

        $list = Db::name('shop_qrcode')
            ->where('site_id', $site_id)
            ->order('id desc')
            ->select()
            ->toArray();

        // 若当前还没有多码记录，自动初始化默认主码
        if (empty($list)) {
            $shop = Db::name('shop')->where('site_id', $site_id)->find();
            $scene_code = 'S_' . $site_id . '_main';
            $default_qr = [
                'site_id' => $site_id,
                'tag_name' => '默认主码 (收银台)',
                'scene_code' => $scene_code,
                'qrcode_url' => $shop['logo'] ?? '',
                'scan_count' => 0,
                'create_time' => time()
            ];
            $id = Db::name('shop_qrcode')->insertGetId($default_qr);
            $default_qr['id'] = $id;
            $list = [$default_qr];
        }

        return $this->response($this->success($list));
    }

    /**
     * 添加自定义标签二维码
     */
    public function addShopQrcode()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $tag_name = trim(request()->param("tag_name", ''));

        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        if (empty($tag_name)) {
            return $this->response($this->error('', '请输入标签名称(如: 1号桌/外摆区/海报A)'));
        }

        $scene_code = 'S_' . $site_id . '_' . substr(md5($tag_name . time() . rand(100, 999)), 0, 8);
        $shop = Db::name('shop')->where('site_id', $site_id)->find();

        $data = [
            'site_id' => $site_id,
            'tag_name' => $tag_name,
            'scene_code' => $scene_code,
            'qrcode_url' => $shop['logo'] ?? '',
            'scan_count' => 0,
            'create_time' => time()
        ];
        $id = Db::name('shop_qrcode')->insertGetId($data);
        $data['id'] = $id;

        return $this->response($this->success($data, '创建标签二维码成功'));
    }

    /**
     * 获取官方物料品类与收费配置及商户首单免费资格
     */
    public function getMaterialTypeList()
    {
        $site_id = request()->param("site_id", $this->site_id);
        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }

        // 检查商户是否曾申领过物料: 首单免费
        $paid_count = Db::name('shop_material_order')
            ->where('site_id', $site_id)
            ->where('pay_status', 1)
            ->count();
        $is_first_free = ($paid_count == 0) ? 1 : 0;

        $types = Db::name('shop_material_type')
            ->where('is_enable', 1)
            ->order('sort asc, type_id asc')
            ->select()
            ->toArray();

        return $this->response($this->success([
            'is_first_free' => $is_first_free,
            'list' => $types
        ]));
    }

    /**
     * 申请官方物料制作 (首单免费，后续收费)
     */
    public function applyMaterialOrder()
    {
        $site_id = request()->param("site_id", $this->site_id);
        $type_id = intval(request()->param("type_id", 0));
        $qrcode_id = intval(request()->param("qrcode_id", 0));
        $contacts_name = trim(request()->param("contacts_name", ''));
        $contacts_mobile = trim(request()->param("contacts_mobile", ''));
        $address = trim(request()->param("address", ''));

        if (empty($site_id)) {
            return $this->response($this->error('', '未获取到商家身份'));
        }
        if (empty($type_id)) {
            return $this->response($this->error('', '请选择物料品类'));
        }
        if (empty($contacts_name) || empty($contacts_mobile) || empty($address)) {
            return $this->response($this->error('', '请完整填写收货人、联系电话及详细收件地址'));
        }

        $type = Db::name('shop_material_type')->where('type_id', $type_id)->where('is_enable', 1)->find();
        if (!$type) {
            return $this->response($this->error('', '物料品类不存在或已下架'));
        }

        // 判定首单免费
        $paid_count = Db::name('shop_material_order')
            ->where('site_id', $site_id)
            ->where('pay_status', 1)
            ->count();
        $is_free = ($paid_count == 0) ? 1 : 0;

        $order_money = 0.00;
        $pay_status = 1; // 首次免费直接标记已支付/待制作
        if (!$is_free) {
            $order_money = floatval($type['cost_price']) + floatval($type['shipping_fee']);
            $pay_status = 0; // 需支付工本费与运费
        }

        $order_no = 'MO' . date('YmdHis') . rand(1000, 9999);
        $data = [
            'order_no' => $order_no,
            'site_id' => $site_id,
            'type_id' => $type_id,
            'qrcode_id' => $qrcode_id,
            'is_free' => $is_free,
            'order_money' => $order_money,
            'pay_status' => $pay_status,
            'delivery_status' => 0, // 待制作
            'contacts_name' => $contacts_name,
            'contacts_mobile' => $contacts_mobile,
            'address' => $address,
            'create_time' => time(),
            'pay_time' => $is_free ? time() : 0
        ];

        $order_id = Db::name('shop_material_order')->insertGetId($data);

        return $this->response($this->success([
            'order_id' => $order_id,
            'order_no' => $order_no,
            'is_free' => $is_free,
            'order_money' => $order_money,
            'pay_status' => $pay_status
        ], $is_free ? '恭喜！首次制作物料免费申请成功，我们将尽快制作并发货' : '物料订单创建成功，请支付工本费与运费'));
    }
}
