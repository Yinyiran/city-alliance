<?php

namespace app\model\member;

use app\model\BaseModel;
use think\facade\Db;
use app\model\shop\ShopAccount;
use app\model\shopapplet\Website as WebSiteModel;

/**
 * 会员购买与开通多级分润模型
 */
class MemberLevelSettlement extends BaseModel
{
    /**
     * 计算并创建会员开通分润记录
     * @param int $order_id
     * @return array
     */
    public function calculateSettlement($order_id)
    {
        $order_info = Db::name('member_level_order')->where('order_id', $order_id)->find();
        if (empty($order_info)) {
            return $this->error('', '会员订单不存在');
        }

        // 幂等防重复
        $exist = Db::name('member_level_settlement')->where('order_id', $order_id)->find();
        if ($exist) {
            return $this->success($exist);
        }

        $buy_member = Db::name('member')->where('member_id', $order_info['member_id'])->find();
        if (empty($buy_member)) {
            return $this->error('', '会员信息不存在');
        }

        $price = floatval($order_info['price']);
        $level_id = intval($order_info['level_id']);
        $level_name = $order_info['level_name'];
        $website_id = intval($order_info['website_id'] ?: $buy_member['website_id']);
        $shop_id = intval($buy_member['site_id']);

        // 1. 邀约人奖励计算
        $invite_member_id = intval($buy_member['source_member']);
        $invite_rate = 0.00;
        $invite_money = 0.00;

        if ($invite_member_id > 0) {
            $invite_member = Db::name('member')->where('member_id', $invite_member_id)->find();
            if ($invite_member) {
                $inviter_level = intval($invite_member['member_level']);
                // 矩阵：普通(10/15/20), 钻石(15/20/25), 黑钻(20/25/30), 合伙人(25/30/35)
                if ($inviter_level == 1) {
                    if ($level_id == 2) $invite_rate = 10.00;
                    elseif ($level_id == 3) $invite_rate = 15.00;
                    elseif ($level_id == 4) $invite_rate = 20.00;
                } elseif ($inviter_level == 2) {
                    if ($level_id == 2) $invite_rate = 15.00;
                    elseif ($level_id == 3) $invite_rate = 20.00;
                    elseif ($level_id == 4) $invite_rate = 25.00;
                } elseif ($inviter_level == 3) {
                    if ($level_id == 2) $invite_rate = 20.00;
                    elseif ($level_id == 3) $invite_rate = 25.00;
                    elseif ($level_id == 4) $invite_rate = 30.00;
                } elseif ($inviter_level == 4) {
                    if ($level_id == 2) $invite_rate = 25.00;
                    elseif ($level_id == 3) $invite_rate = 30.00;
                    elseif ($level_id == 4) $invite_rate = 35.00;
                }
                $invite_money = round($price * $invite_rate / 100, 2);
            }
        }

        // 2. 主理人 25% (会员总业绩)
        $shop_rate = 25.00;
        $shop_money = round($price * $shop_rate / 100, 2);

        // 3. 分公司 25% (会员总业绩，绑定城市对公后台收款)
        $city_rate = 25.00;
        $city_money = round($price * $city_rate / 100, 2);

        // 4. 总部 剩余
        $platform_money = round($price - $invite_money - $shop_money - $city_money, 2);
        if ($platform_money < 0) $platform_money = 0.00;

        Db::startTrans();
        try {
            $settlement_data = [
                'order_id' => $order_id,
                'order_no' => $order_info['order_no'],
                'buy_member_id' => $buy_member['member_id'],
                'level_id' => $level_id,
                'level_name' => $level_name,
                'order_money' => $price,
                'open_type' => 1,
                'invite_member_id' => $invite_member_id,
                'invite_rate' => $invite_rate,
                'invite_money' => $invite_money,
                'shop_id' => $shop_id,
                'shop_rate' => $shop_rate,
                'shop_money' => $shop_money,
                'city_id' => $website_id,
                'city_rate' => $city_rate,
                'city_money' => $city_money,
                'platform_money' => $platform_money,
                'status' => 0, // 初始冻结中，待满7天+消费解冻
                'is_seven_days' => 0,
                'is_consume' => 0,
                'create_time' => time(),
            ];
            $settle_id = Db::name('member_level_settlement')->insertGetId($settlement_data);

            // 商家主理人到账 25%
            if ($shop_id > 0 && $shop_money > 0) {
                $shop_account = new ShopAccount();
                $shop_account->addShopAccount($shop_id, 'account', $shop_money, 'member_level', $order_id, '主理人会员总业绩分润(25%)');
            }

            // 分公司到账 25%
            if ($website_id > 0 && $city_money > 0) {
                $website_model = new WebSiteModel();
                Db::name('website')->where('site_id', $website_id)->inc('account_order', $city_money)->update();
                $website_model->addWebsiteAccount($website_id, 'account', $city_money, 'member_level', $order_id, '分公司会员总业绩分润(25%)');
            }

            Db::commit();
            return $this->success($settle_id);
        } catch (\Exception $e) {
            Db::rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 老板主动为他人开通合伙人（此金额只走主理人和分公司分佣）
     * @param int $order_id
     * @param int $shop_id
     * @param float $custom_price
     * @param int $buy_member_id
     * @return array
     */
    public function calculateCustomPartnerSettlement($order_id, $shop_id, $custom_price, $buy_member_id)
    {
        $order_info = Db::name('member_level_order')->where('order_id', $order_id)->find();
        $buy_member = Db::name('member')->where('member_id', $buy_member_id)->find();
        $shop = Db::name('shop')->where('site_id', $shop_id)->find();
        $website_id = $shop['website_id'] ?? ($buy_member['website_id'] ?? 0);

        $reward_config = Db::name('reward_config')->where('id', 1)->find();
        $shop_rate = !empty($reward_config['custom_partner_shop_rate']) ? floatval($reward_config['custom_partner_shop_rate']) : 50.00;
        $city_rate = !empty($reward_config['custom_partner_city_rate']) ? floatval($reward_config['custom_partner_city_rate']) : 50.00;

        $shop_money = round($custom_price * $shop_rate / 100, 2);
        $city_money = round($custom_price * $city_rate / 100, 2);

        Db::startTrans();
        try {
            $settlement_data = [
                'order_id' => $order_id,
                'order_no' => $order_info['order_no'] ?? ('CP'.date('YmdHis').rand(1000,9999)),
                'buy_member_id' => $buy_member_id,
                'level_id' => 4,
                'level_name' => '合伙人',
                'order_money' => $custom_price,
                'open_type' => 2, // 老板开通
                'invite_member_id' => 0,
                'invite_rate' => 0.00,
                'invite_money' => 0.00,
                'shop_id' => $shop_id,
                'shop_rate' => $shop_rate,
                'shop_money' => $shop_money,
                'city_id' => $website_id,
                'city_rate' => $city_rate,
                'city_money' => $city_money,
                'platform_money' => 0.00,
                'status' => 1, // 老板开通即生效
                'is_seven_days' => 1,
                'is_consume' => 1,
                'unlock_time' => time(),
                'create_time' => time(),
            ];
            $settle_id = Db::name('member_level_settlement')->insertGetId($settlement_data);

            if ($shop_id > 0 && $shop_money > 0) {
                $shop_account = new ShopAccount();
                $shop_account->addShopAccount($shop_id, 'account', $shop_money, 'member_level', $order_id, '老板开通合伙人主理人分成');
            }
            if ($website_id > 0 && $city_money > 0) {
                $website_model = new WebSiteModel();
                Db::name('website')->where('site_id', $website_id)->inc('account_order', $city_money)->update();
                $website_model->addWebsiteAccount($website_id, 'account', $city_money, 'member_level', $order_id, '老板开通合伙人分公司分成');
            }

            Db::commit();
            return $this->success($settle_id);
        } catch (\Exception $e) {
            Db::rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 定时任务/触发检测解锁邀约奖励
     * 规则: 下级办理会员后满 7 天 + 在平台产生消费后，奖励解冻入账到邀请人 balance_money
     * @return array
     */
    public function cronCheckUnlockReward()
    {
        $seven_days_ago = time() - (7 * 86400);
        $pending_list = Db::name('member_level_settlement')
            ->where('status', 0)
            ->where('invite_money', '>', 0)
            ->select();

        $unlocked_count = 0;
        $member_account = new MemberAccount();

        foreach ($pending_list as $record) {
            $is_seven_days = ($record['create_time'] <= $seven_days_ago) ? 1 : 0;
            
            // 校验购买会员的下级在平台是否有已支付/已完成的消费订单
            $consume_count = Db::name('order')
                ->where('member_id', $record['buy_member_id'])
                ->where('pay_status', 1)
                ->where('order_status', 'in', [2, 3, 4, 10]) // 待发货、已发货、已收货、已完成
                ->count();
            $is_consume = ($consume_count > 0) ? 1 : 0;

            // 更新状态标记
            Db::name('member_level_settlement')
                ->where('id', $record['id'])
                ->update([
                    'is_seven_days' => $is_seven_days,
                    'is_consume' => $is_consume
                ]);

            // 两项均满足时解冻
            if ($is_seven_days == 1 && $is_consume == 1) {
                Db::startTrans();
                try {
                    Db::name('member_level_settlement')
                        ->where('id', $record['id'])
                        ->update([
                            'status' => 1,
                            'unlock_time' => time()
                        ]);

                    // 发放给邀请人
                    $res = $member_account->addMemberAccount2(
                        $record['invite_member_id'],
                        'balance_money',
                        $record['invite_money'],
                        'member_level_reward',
                        $record['order_id'],
                        "下级办理会员奖励解冻入账:{$record['invite_money']}元",
                        "现金奖励",
                        '',
                        $record['buy_member_id']
                    );

                    if ($res['code'] >= 0) {
                        Db::commit();
                        $unlocked_count++;
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) {
                    Db::rollback();
                }
            }
        }

        return $this->success(['unlocked_count' => $unlocked_count]);
    }

    /**
     * 会员等级订单退款与分润作废冲正
     * @param int $order_id
     * @return array
     */
    public function refundSettlement($order_id)
    {
        $settle_info = Db::name('member_level_settlement')->where('order_id', $order_id)->find();
        if (empty($settle_info)) {
            return $this->error('', '分润记录不存在');
        }

        if ($settle_info['status'] == 2) {
            return $this->success('', '该订单分润已作废');
        }

        Db::startTrans();
        try {
            $old_status = $settle_info['status'];
            // 1. 标记分润记录为已退款作废
            Db::name('member_level_settlement')->where('id', $settle_info['id'])->update([
                'status' => 2,
            ]);

            // 2. 冲减主理人预加分润
            if ($settle_info['shop_id'] > 0 && $settle_info['shop_money'] > 0) {
                $shop_account = new ShopAccount();
                $shop_account->addShopAccount(
                    $settle_info['shop_id'],
                    'account',
                    -$settle_info['shop_money'],
                    'member_level_refund',
                    $order_id,
                    "会员订单退款扣减主理人分润: -{$settle_info['shop_money']}元"
                );
            }

            // 3. 冲减分公司预加分润
            if ($settle_info['city_id'] > 0 && $settle_info['city_money'] > 0) {
                $website_model = new WebSiteModel();
                Db::name('website')->where('site_id', $settle_info['city_id'])->dec('account_order', $settle_info['city_money'])->update();
                $website_model->addWebsiteAccount(
                    $settle_info['city_id'],
                    'account',
                    -$settle_info['city_money'],
                    'member_level_refund',
                    $order_id,
                    "会员订单退款扣减分公司分润: -{$settle_info['city_money']}元"
                );
            }

            // 4. 若邀约人奖励此前已解冻入账(status=1)，则从邀约人余额中收回
            if ($old_status == 1 && $settle_info['invite_money'] > 0 && $settle_info['invite_member_id'] > 0) {
                $member_account = new MemberAccount();
                $member_account->addMemberAccount2(
                    $settle_info['invite_member_id'],
                    'balance_money',
                    -$settle_info['invite_money'],
                    'member_level_refund',
                    $order_id,
                    "下级会员退款收回邀约奖励: -{$settle_info['invite_money']}元",
                    "退款扣减"
                );
            }

            Db::commit();
            return $this->success('', '分润记录已成功退款作废');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取分润列表分页
     */
    public function getSettlementPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'id desc', $field = '*')
    {
        $list = model('member_level_settlement')->pageList($condition, $field, $order, $page, $page_size);
        if (!empty($list['list'])) {
            foreach ($list['list'] as &$item) {
                // 补充买家昵称/手机号
                $buyer = Db::name('member')->where('member_id', $item['buy_member_id'])->field('nickname,mobile')->find();
                $item['buy_nickname'] = $buyer['nickname'] ?? ('ID:' . $item['buy_member_id']);
                $item['buy_mobile'] = $buyer['mobile'] ?? '';

                // 补充邀约人
                if ($item['invite_member_id'] > 0) {
                    $inviter = Db::name('member')->where('member_id', $item['invite_member_id'])->field('nickname,mobile')->find();
                    $item['invite_nickname'] = $inviter['nickname'] ?? ('ID:' . $item['invite_member_id']);
                    $item['invite_mobile'] = $inviter['mobile'] ?? '';
                } else {
                    $item['invite_nickname'] = '无';
                    $item['invite_mobile'] = '';
                }

                // 补充门店名称
                if ($item['shop_id'] > 0) {
                    $item['shop_name'] = Db::name('shop')->where('site_id', $item['shop_id'])->value('site_name') ?: ('门店ID:' . $item['shop_id']);
                } else {
                    $item['shop_name'] = '平台直属';
                }

                // 补充城市分站名称
                if ($item['city_id'] > 0) {
                    $item['city_name'] = Db::name('website')->where('site_id', $item['city_id'])->value('site_area_name') ?: ('分站ID:' . $item['city_id']);
                } else {
                    $item['city_name'] = '全国/总部';
                }
            }
        }
        return $this->success($list);
    }
}
