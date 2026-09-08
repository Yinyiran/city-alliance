<?php

/**

 */

namespace app\model\signin;

use app\model\BaseModel;
use think\facade\Db;
use think\facade\Cache;
use app\model\member\Member as MemberModel;
use app\model\signin\Rules as RulesModel;
use app\model\api\SignMemberRecord as SignMemberRecordModel;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\order\orderCreate as orderCreateModel;




/**
 * 用户签到记录管理
 */
class Records extends BaseModel

{
    /**
     * 获取会员分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getRecordPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $group = "a.member_id";
        $field = "a.member_id,count(a.member_id) as sign_days,m.nickname,m.username,m.mobile";
        $order = "sign_days desc";
        $list = Db::name("sign_member_record")->alias("a")
            ->join("member m", "m.member_id=a.member_id", "inner")
            ->where($condition)
            ->group($group)
            ->order("sign_days desc")
            ->field($field)
            ->paginate(['list_rows' => $page_size, 'page' => $page]);
            $result=[];
            if(!empty($list)){
                $list = $list->toArray();
                $result["list"] = $list["data"];
                $result["page_count"] = ceil( $list["total"] / $page_size);
                $result["count"] = $list["total"];
            }
        return $this->success($result);
    }

    //获取指定用户签到列表
    public function getsigninfo($condition, $page, $page_size)
    {
        $join = [
            ['member m', 'a.member_id=m.member_id', 'inner'],
            ['sign_rules r', 'a.rules_id=r.rules_id', 'inner'],
            ['shop s', 's.site_id=a.shop_id', 'inner'],
            ['goods g', 'g.goods_id=a.goods_id', 'left'],

        ];
        $field = "a.*,m.nickname,s.site_name,s.logo as shop_logo,m.username,m.mobile,s.site_name,a.rewardtxt,r.title as sign_title,a.create_time as sign_time,g.goods_name";

        $alias = "a";
        $order = "a.create_time desc";
        $list = model('sign_member_record')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }

    //获取指定用户签到列表
    public function getMemberlist($condition)
    {
        $join = [
            ['member m', 'a.member_id=m.member_id', 'inner'],
            ['sign_rules r', 'a.rules_id=r.rules_id', 'inner'],
            ['shop s', 's.site_id=a.shop_id', 'inner'],
            ['goods g', 'g.goods_id=a.goods_id', 'left'],

        ];
        $field = "a.*,m.nickname,s.site_name,s.logo as shop_logo,m.username,m.mobile,s.site_name,a.rewardtxt,r.title as sign_title,a.create_time as sign_time,g.goods_name";

        $alias = "a";
        $order = "a.create_time desc";
        $list = model('sign_member_record')->getList($condition, $field, $order, $alias, $join);
        return $this->success($list);
    }

    // 店铺签到
    // 签到一轮完成后进行下一轮签到 判断依据 rounds
    // 规则限制：每个月每家店只能签到一次
    public function shopsignin($member_id, $shop_id)
    {
        $membermodel = new MemberModel();
        $rulesmodel = new RulesModel();

        // 1. 校验会员是否存在
        $condition = [["member_id", '=', $member_id]];
        $member = $membermodel->getMemberInfo($condition);
        if (empty($member) || empty($member["data"])) {
            return error(-1, "会员信息不存在，请扫描有效二维码");
        }

        // 2. 核心限制：每个月每家店只能签到一次（按当前自然月）
        $current_month_start = strtotime(date('Y-m-01 00:00:00'));
        $current_month_end = strtotime(date('Y-m-t 23:59:59'));

        $month_shop_record = Db::name("sign_member_record")
            ->where([
                ["member_id", "=", $member_id],
                ["shop_id", "=", $shop_id],
                ["create_time", "between", [$current_month_start, $current_month_end]]
            ])
            ->order("create_time desc")
            ->find();

        if (!empty($month_shop_record)) {
            $sign_time_str = !empty($month_shop_record['create_time']) ? date('m月d日 H:i', $month_shop_record['create_time']) : '本月';
            return error(-1, "该会员本月已在当前门店完成签到（签到时间：{$sign_time_str}），每个月每家店只能签到一次，下月可再次签到");
        }

        // 3. 校验今天是否已在平台签到过（每天限签1次）
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $today_end = strtotime(date('Y-m-d 23:59:59'));

        $today_record = Db::name("sign_member_record")
            ->where([
                ["member_id", "=", $member_id],
                ["create_time", "between", [$today_start, $today_end]]
            ])
            ->find();

        if (!empty($today_record)) {
            return error(-1, "该会员今天已经签到过了，请明天再来继续签到");
        }

        // 4. 获取会员当前轮次及已签天数
        $shop_sign_round = !empty($member["data"]["shop_sign_round"]) ? (int)$member["data"]["shop_sign_round"] : 1;
        $member_sign = Db::name("sign_member_record")
            ->field("count(member_id) as sign_days, sign_rounds")
            ->where(["member_id" => $member_id, "sign_rounds" => $shop_sign_round])
            ->group("sign_rounds")
            ->find();

        if (!empty($member_sign)) {
            $sign_days = (int)$member_sign["sign_days"];
            $shop_sign_round = (int)$member_sign["sign_rounds"];
        } else {
            $sign_days = 0;
            $shop_sign_round = max(1, $shop_sign_round);
        }

        // 判断是否签够当前轮次的指定总天数，若满则开启下一轮
        $count = $rulesmodel->getCount();
        if ($count > 0 && $sign_days >= $count) {
            $shop_sign_round = $shop_sign_round + 1;
            $sign_days = 0;
        }

        $need_day = $sign_days + 1;
        $rulesinfo = $rulesmodel->getrules($need_day);
        if (empty($rulesinfo)) {
            return error(-1, "未获取到第{$need_day}天的签到规则配置");
        }

        // 5. 校验当前轮次该天数是否已签过
        $day_exist = Db::name("sign_member_record")->where([
            "member_id" => $member_id,
            "sign_rounds" => $shop_sign_round,
            "sign_day" => $need_day
        ])->find();
        if (!empty($day_exist)) {
            return error(-1, "该轮第{$need_day}天已签到，请勿重复操作");
        }

        // 6. 开启事务写入签到记录及发放奖励
        Db::startTrans();
        try {
            $rewardtxt = $rulesinfo["reward_value"] . $rulesinfo["reward_typename"];
            $datarecord = [
                "member_id" => $member_id,
                "shop_id" => $shop_id,
                "rewardtxt" => $rewardtxt,
                "rewardname" => $rulesinfo["reward_typename"],
                "rewardtype" => $rulesinfo["reward_type"],
                "rewardnum" => $rulesinfo["reward_value"],
                "rules_id" => $rulesinfo['rules_id'],
                'sign_day' => $need_day,
                'sign_rounds' => $shop_sign_round,
                'goods_id' => $rulesinfo["goods_id"],
                'create_time' => time()
            ];

            $record_id = Db::name("sign_member_record")->insertGetId($datarecord);
            if (!$record_id) {
                Db::rollback();
                return error(-1, "签到记录创建失败");
            }

            // 更新用户签到天数和轮次
            Db::name("member")->where(["member_id" => $member_id])->update([
                "shop_sign_days" => $need_day,
                "shop_sign_round" => $shop_sign_round
            ]);

            // 处理签到奖励（积分、现金余额、大礼包）
            $memberaccountmodel = new MemberAccountModel();
            $reward_value = $rulesinfo["reward_value"];
            $rewardtype = $rulesinfo["reward_type"];
            $goods_id = $rulesinfo["goods_id"];

            switch ($rewardtype) {
                case 1:
                    // 积分奖励
                    $memberaccountmodel->addMemberAccount($member_id, "point", $reward_value, "shop_signin", "商铺签到奖励积分", "用户商铺签到奖励积分+{$reward_value}分");
                    break;
                case 2:
                    // 现金余额奖励
                    $memberaccountmodel->addMemberAccount($member_id, "balance", $reward_value, "shop_signin", "商铺签到奖励现金", "用户商铺签到奖励现金+{$reward_value}元");
                    break;
                case 3:
                    // 大礼包奖励订单
                    $result = $this->signincreate($member_id, $goods_id);
                    if ($result["code"] == 0) {
                        $order_out_trade = $result["data"];
                        $orderinfo = Db::name("order")->field("order_id,out_trade_no")->where("out_trade_no", $order_out_trade)->find();
                        if (!empty($orderinfo)) {
                            Db::name("sign_member_record")->where("signin_id", $record_id)->update(["order_id" => $orderinfo["order_id"]]);
                        }
                    }
                    break;
            }

            Db::commit();
            return success(0, "签到成功！恭喜获得：" . $rewardtxt, [
                'signin_id' => $record_id,
                'rewardtxt' => $rewardtxt,
                'sign_day' => $need_day,
                'sign_rounds' => $shop_sign_round
            ]);
        } catch (\Exception $e) {
            Db::rollback();
            return error(-1, "签到处理异常: " . $e->getMessage());
        }
    }

      /**
     * 商铺签到订单创建

     */
    public function signincreate($member_id,$goods_id)
    {
        /*$token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);*/
        $order_create = new OrderCreateModel();
        $sku_id=Db::name("goods_sku")->where("goods_id",$goods_id)->value("sku_id");
        $data = [
            'cart_ids' =>  '',
            'sku_id' => $sku_id,
            'num' => 1,
            'member_id' => $member_id,
            'is_balance' =>1,//是否使用余额
            'order_from' => "webapp",
            'order_from_name' =>"微信小程序",
           // 'buyer_message' => isset($this->params[ "buyer_message" ]) && !empty($this->params[ "buyer_message" ]) ? json_decode($this->params[ "buyer_message" ], true) : [],
            //'delivery' => isset($this->params[ "delivery" ]) && !empty($this->params[ "delivery" ]) ? json_decode($this->params[ "delivery" ], true) : [],
           // 'member_address' => isset($this->params[ "member_address" ]) && !empty($this->params[ "member_address" ]) ? json_decode($this->params[ "member_address" ], true) : [],
            'platform_coupon_id' =>  0,

            'latitude' => null,
            'longitude' => null,
           // 'invoice' => isset($this->params[ "invoice" ]) && !empty($this->params[ "invoice" ]) ? json_decode($this->params[ "invoice" ], true) : [],
            'pay_type' => '3',//支付方式1现金支付，2积分支付,3大礼包无需支付

        ];
        //这里都是商家核销的，物流的再写一个方法

        $res = $order_create->signincreate($data);
        return $res;
    }

    public function getSigninByday($member_id,$rules_id,$shop_sign_round){
        $condition=[
            ['a.member_id',"=",$member_id],
            ['rules_id',"=",$rules_id], 
            ['sign_rounds',"=",$shop_sign_round],
        ];
       
        $alias="a";
        $join[]=['shop s','s.site_id=a.shop_id','inner'];
        $field="s.site_name,s.logo,a.signin_id,a.sign_day,a.sign_rounds,a.rewardtxt,a.rewardname,a.rewardtype,a.rewardnum,a.goods_id";
        
       // $res=Db::name("sign_member_record")->where($condition)->join($join)->field($field)->select();
        $res=model("sign_member_record")->getList($condition,$field,"",$alias,$join);
        if(empty($res)){
            return [];
        }
        return $res[0];

    }

    public function getSignCount($member_id,$shop_sign_round){
        
        $where="member_id=".$member_id." and sign_rounds=".$shop_sign_round;
        $res=Db::name("sign_member_record")->where($where)->count();
        return $res;

    }

}
