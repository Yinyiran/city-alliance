<?php
/**

 */

namespace addon\fenxiao\model;

use app\model\BaseModel;
use think\facade\Db;

/**
 * 分销商品
 */
class FenxiaoOrder extends BaseModel
{

    /**
     * 分销订单计算
     * @param unknown $order
     */
    public function calculate($order)
    {
        //获取分销基础配置
        $config_model = new Config();
        $fenxiao_basic_config = $config_model->getFenxiaoBasicsConfig();
        $level_config = $fenxiao_basic_config[ 'data' ][ 'value' ][ 'level' ];
        if (empty($level_config)) {
            return $this->success();
        }
        //检测分销商上级关系
        $member_info = model("member")->getInfo([ [ 'member_id', '=', $order[ 'member_id' ] ] ], 'fenxiao_id');
        //如果没有分销商直接返回不计算,没有考虑首次付款上下级绑定
        if ($member_info[ 'fenxiao_id' ] == 0) {
            return $this->success();
        }
        $fenxiao_id = $member_info[ 'fenxiao_id' ];
        $fenxiao_info = model("fenxiao")->getInfo([ [ 'fenxiao_id', '=', $fenxiao_id ], [ 'is_delete', '=', 0 ] ]);
        if (empty($fenxiao_info)) {
            return $this->success();
        }
        //判断几级分销
        $parent_fenxiao_info = $level_config >= 2 ? model('fenxiao')->getInfo([ [ 'fenxiao_id', '=', $fenxiao_info[ 'parent' ] ], [ 'is_delete', '=', 0 ] ], 'fenxiao_id, fenxiao_name, status, parent') : [];
        $grand_parent_fenxiao_info = $level_config >= 3 && !empty($parent_fenxiao_info[ 'parent' ]) ? model('fenxiao')->getInfo([ [ 'fenxiao_id', '=', $parent_fenxiao_info[ 'parent' ] ], [ 'is_delete', '=', 0 ] ], 'fenxiao_id, fenxiao_name, status') : [];
        $order_goods = model("order_goods")->getList([ [ 'order_id', '=', $order[ 'order_id' ] ] ], 'order_goods_id, goods_id, sku_id, sku_name, sku_image, sku_no, is_virtual, price, cost_price, num, goods_money, cost_money, delivery_no, delivery_status, real_goods_money, shop_money, platform_money, is_present, is_fenxiao');
        model('fenxiao_order')->delete([ [ 'order_id', '=', $order[ 'order_id' ] ] ]);
        //获取分销等级
        foreach ($order_goods as $k => $v) {
            //赠品订单项,不参与分销
            if($v['is_present'] == 1 || $v['is_fenxiao'] == 0){
                continue;
            }

            //商品信息管理
            $goods_info = model("goods")->getInfo([ [ 'goods_id', '=', $v[ 'goods_id' ] ] ], 'is_fenxiao, fenxiao_type');
            if ($goods_info[ 'is_fenxiao' ] != 1) {
                continue;
            }

            $sku_info = model('goods_sku')->getInfo([ [ 'sku_id', '=', $v[ 'sku_id' ] ] ], 'fenxiao_price');
            if (!empty($sku_info) && $sku_info[ 'fenxiao_price' ] > 0) $v[ 'real_goods_money' ] = $sku_info[ 'fenxiao_price' ] * $v[ 'num' ];

            $commission = 0;
            $commission_rate = 0;
            $order_fenxiao_data = [];

            $fenxiao_level = model('fenxiao_goods_sku')->getInfo([ [ 'goods_id', '=', $v[ 'goods_id' ] ], [ 'sku_id', '=', $v[ 'sku_id' ] ], [ 'level_id', '=', $fenxiao_info[ 'level_id' ] ] ]);
            if ($fenxiao_info[ 'status' ] == 1) {
                if ($fenxiao_level[ 'one_rate' ] > 0) {
                    $commission_rate += $order_fenxiao_data[ 'one_rate' ] = $fenxiao_level[ 'one_rate' ];
                    $commission += $order_fenxiao_data[ 'one_commission' ] = $fenxiao_level[ 'one_rate' ] * $v[ 'real_goods_money' ] / 100;
                } else {
                    $commission_rate += $order_fenxiao_data[ 'one_rate' ] = round($fenxiao_level[ 'one_money' ] * $v[ 'num' ] / $v[ 'real_goods_money' ], 2);
                    $commission += $order_fenxiao_data[ 'one_commission' ] = $fenxiao_level[ 'one_money' ] * $v[ 'num' ];
                }
            }
            if (!empty($parent_fenxiao_info) && $parent_fenxiao_info[ 'status' ] == 1) {
                if ($fenxiao_level[ 'two_rate' ] > 0) {
                    $commission_rate += $order_fenxiao_data[ 'two_rate' ] = $fenxiao_level[ 'two_rate' ];
                    $commission += $order_fenxiao_data[ 'two_commission' ] = $fenxiao_level[ 'two_rate' ] * $v[ 'real_goods_money' ] / 100;
                } else {
                    $commission_rate += $order_fenxiao_data[ 'two_rate' ] = round($fenxiao_level[ 'two_money' ] * $v[ 'num' ] / $v[ 'real_goods_money' ], 2);
                    $commission += $order_fenxiao_data[ 'two_commission' ] = $fenxiao_level[ 'two_money' ] * $v[ 'num' ];
                }
            }
            if (!empty($grand_parent_fenxiao_info) && $grand_parent_fenxiao_info[ 'status' ] == 1) {
                if ($fenxiao_level[ 'three_rate' ] > 0) {
                    $commission_rate += $order_fenxiao_data[ 'three_rate' ] = $fenxiao_level[ 'three_rate' ];
                    $commission += $order_fenxiao_data[ 'three_commission' ] = $fenxiao_level[ 'three_rate' ] * $v[ 'real_goods_money' ] / 100;
                } else {
                    $commission_rate += $order_fenxiao_data[ 'three_rate' ] = round($fenxiao_level[ 'three_money' ] * $v[ 'num' ] / $v[ 'real_goods_money' ], 2);
                    $commission += $order_fenxiao_data[ 'three_commission' ] = $fenxiao_level[ 'three_money' ] * $v[ 'num' ];
                }
            }

            //启动分销
            $data = [
                'order_id' => $order[ 'order_id' ],
                'order_no' => $order[ 'order_no' ],
                'order_goods_id' => $v[ 'order_goods_id' ],
                'site_id' => $order[ 'site_id' ],
                'site_name' => $order[ 'site_name' ],
                'goods_id' => $v[ 'goods_id' ],
                'sku_id' => $v[ 'sku_id' ],
                'sku_name' => $v[ 'sku_name' ],
                'sku_image' => $v[ 'sku_image' ],
                'price' => $v[ 'price' ],
                'num' => $v[ 'num' ],
                'real_goods_money' => $order_goods[ $k ][ 'real_goods_money' ],
                'member_id' => $order[ 'member_id' ],
                'member_name' => $order[ 'name' ],
                'member_mobile' => $order[ 'mobile' ],
                'full_address' => $order[ 'full_address' ] . $order[ 'address' ],
                'commission' => $commission,
                'commission_rate' => $commission_rate,
                'one_fenxiao_id' => $fenxiao_info[ 'fenxiao_id' ],
                'one_rate' => $order_fenxiao_data[ 'one_rate' ] ?? 0,
                'one_commission' => $order_fenxiao_data[ 'one_commission' ] ?? 0,
                'one_fenxiao_name' => $fenxiao_info[ 'fenxiao_name' ] ?? '',
                'two_fenxiao_id' => !empty($parent_fenxiao_info) ? $parent_fenxiao_info[ 'fenxiao_id' ] : 0,
                'two_rate' => empty($order_fenxiao_data[ 'two_rate' ]) ? 0 : $order_fenxiao_data[ 'two_rate' ],
                'two_commission' => empty($order_fenxiao_data[ 'two_commission' ]) ? 0 : $order_fenxiao_data[ 'two_commission' ],
                'two_fenxiao_name' => empty($parent_fenxiao_info) ? '' : $parent_fenxiao_info[ 'fenxiao_name' ],
                'three_fenxiao_id' => empty($grand_parent_fenxiao_info) ? '' : $grand_parent_fenxiao_info[ 'fenxiao_id' ],
                'three_rate' => empty($order_fenxiao_data[ 'three_rate' ]) ? 0 : $order_fenxiao_data[ 'three_rate' ],
                'three_commission' => empty($order_fenxiao_data[ 'three_commission' ]) ? 0 : $order_fenxiao_data[ 'three_commission' ],
                'three_fenxiao_name' => empty($grand_parent_fenxiao_info) ? '' : $grand_parent_fenxiao_info[ 'fenxiao_name' ],
                'create_time' => time()
            ];
            model("fenxiao_order")->add($data);
        }
        // 分销商检测升级
        event('FenxiaoUpgrade', $member_info[ 'fenxiao_id' ]);

        return $this->success();
    }



    /***
     * 返余额 (自消费/自签单2% vs 被动带单6%)
     */
    public function fanMoney($order)
    {
        //普通商品返积分与余额
        if ($order['goods_type'] != 0) {
            return;
        }

        // 获取全局分润配置
        $reward_config = model('reward_config')->getInfo(['id' => 1]);
        if (empty($reward_config)) {
            $reward_config = [
                'self_order_rate' => 2.00,
                'self_city_rate' => 0.50,
                'self_platform_rate' => 1.50,
                'bring_order_rate' => 6.00,
                'bring_shop_rate' => 2.00,
                'bring_city_rate' => 1.50,
                'bring_level1_rate' => 0.00,
                'bring_level2_rate' => 0.20,
                'bring_level3_rate' => 0.30,
                'bring_level4_rate' => 0.50,
            ];
        }

        $member = model('member')->getInfo(['member_id' => $order['member_id']]); // 下单用户
        $fenxiao = Db::name('fenxiao')->where('fenxiao_id', $member['fenxiao_id'] ?? 0)->find();
        $p_fenxiao = $fenxiao ? Db::name('fenxiao')->where('fenxiao_id', $fenxiao['parent'])->find() : null;

        $two_member_id = 0;
        $two_member_level = 1;
        $two_member_level_name = '普通会员';
        $p_member = null;

        if ($p_fenxiao) {
            $p_member = model('member')->getInfo(['member_id' => $p_fenxiao['member_id']]);
        } elseif (!empty($member['source_member'])) {
            $p_member = model('member')->getInfo(['member_id' => $member['source_member']]);
        }

        if ($p_member) {
            $two_member_id = $p_member['member_id'];
            $two_member_level = $p_member['member_level'];
            $two_member_level_name = $p_member['member_level_name'];
        }

        $user_site_id = intval($member['site_id'] ?? 0);
        $inviter_site_id = intval($p_member['site_id'] ?? 0);

        // 下单商品列表
        $order_goods = model("order_goods")->getList([ [ 'order_id', '=', $order[ 'order_id' ] ] ], '*');
        foreach ($order_goods as $k => $v) {
            $goods_site_id = intval($v['site_id']);
            $city_id = intval($order['website_id']);

            // 判断是否为自签单/自消费：会员在自己归属门店下单，且无跨店上级
            $is_self_order = ($goods_site_id > 0 && $user_site_id == $goods_site_id && ($inviter_site_id == 0 || $inviter_site_id == $goods_site_id));

            if ($is_self_order) {
                // 自签单/自消费服务费 2% (分公司0.5%, 总部1.5%, 商家得98%)
                $site_fee = floatval($reward_config['self_order_rate'] ?: 2.00);
                $site_rate = 100.00 - $site_fee;
                $city_rate = floatval($reward_config['self_city_rate'] ?: 0.50);
                $pt_rate = floatval($reward_config['self_platform_rate'] ?: 1.50);
                $two_rate = 0.00;
                $staff_rate = 0.00;
                $staff_member_id = 0;
                $site_rate2 = 0.00;
                $site2_id = $goods_site_id;
            } else {
                // 被动带单总佣金 6% (会员0%~0.5%, 主理人2%, 分公司1.5%, 总部2%~2.5%, 商家得94%)
                $bring_total = floatval($reward_config['bring_order_rate'] ?: 6.00);
                $site_rate = 100.00 - $bring_total;
                $staff_rate = floatval($reward_config['bring_shop_rate'] ?: 2.00);
                $city_rate = floatval($reward_config['bring_city_rate'] ?: 1.50);

                // 带单会员比例
                $two_rate = 0.00;
                if ($two_member_level == 2) {
                    $two_rate = floatval($reward_config['bring_level2_rate'] ?? ($reward_config['level2'] ?? 0.20));
                } elseif ($two_member_level == 3) {
                    $two_rate = floatval($reward_config['bring_level3_rate'] ?? ($reward_config['level3'] ?? 0.30));
                } elseif ($two_member_level == 4) {
                    $two_rate = floatval($reward_config['bring_level4_rate'] ?? ($reward_config['level4'] ?? 0.50));
                }

                $pt_rate = round($bring_total - $two_rate - $staff_rate - $city_rate, 2);
                if ($pt_rate < 0) $pt_rate = 0.00;

                // 寻找带单门店主理人员工ID
                $bring_store_id = ($inviter_site_id > 0) ? $inviter_site_id : $user_site_id;
                $staff_member_id = 0;
                if ($bring_store_id > 0) {
                    $bring_shop = Db::name('shop')->where('site_id', $bring_store_id)->find();
                    if ($bring_shop && !empty($bring_shop['zlr_employee_id'])) {
                        $staff_member_id = Db::name('shop_employee')->where('employee_id', $bring_shop['zlr_employee_id'])->value('member_id') ?: 0;
                    }
                    if (empty($staff_member_id) && $bring_shop && !empty($bring_shop['member_id'])) {
                        $staff_member_id = $bring_shop['member_id'];
                    }
                }
                $site_rate2 = 0.00;
                $site2_id = $bring_store_id;
            }

            // 计算金额
            $real_goods_money = floatval($v['real_goods_money']);
            $site_money = floatval(number_format($real_goods_money * $site_rate * 0.01, 2, '.', ''));
            $city_money = floatval(number_format($real_goods_money * $city_rate * 0.01, 2, '.', ''));
            $staff_money = floatval(number_format($real_goods_money * $staff_rate * 0.01, 2, '.', ''));
            $two_money = floatval(number_format($real_goods_money * $two_rate * 0.01, 2, '.', ''));
            $pt_money = floatval(number_format($real_goods_money * $pt_rate * 0.01, 2, '.', ''));
            $site_money2 = 0.00;

            $fenxiao_site_id = $goods_site_id;
            if (!$fenxiao_site_id) $site_money = 0;
            if (!$city_id) $city_money = 0;
            if (!$staff_member_id) $staff_money = 0;
            if (!$two_member_id) $two_money = 0;

            $website = model('website')->getInfo(['site_id' => $city_id]);

            $res = model("fenxiao_money")->getInfo([
                'order_id' => $order[ 'order_id' ],
                'order_goods_id' => $v[ 'order_goods_id' ],
            ]);

            $save_data = [
                'order_id' => $order[ 'order_id' ],
                'order_no' => $order[ 'order_no' ],
                'order_goods_id' => $v[ 'order_goods_id' ],
                'site_id' => $order[ 'site_id' ],
                'site_name' => $order[ 'site_name' ],
                'goods_id' => $v[ 'goods_id' ],
                'sku_id' => $v[ 'sku_id' ],
                'sku_name' => $v[ 'sku_name' ],
                'sku_image' => $v[ 'sku_image' ],
                'price' => $v[ 'price' ],
                'num' => $v[ 'num' ],
                'real_goods_money' => $v[ 'real_goods_money' ],
                'member_id' => $order[ 'member_id' ],
                'member_name' => $order[ 'name' ],
                'member_mobile' => $order[ 'mobile' ],
                'full_address' => $order[ 'full_address' ] . $order[ 'address' ],
                'one_fenxiao_id' => $fenxiao['fenxiao_id'] ?? 0,
                'one_fenxiao_name' => $fenxiao['fenxiao_name'] ?? '',
                'create_time' => time(),
                'member_level' => $member['member_level'],
                'member_level_name' => $member['member_level_name'],
                'one_member_level' => $member['member_level'],
                'one_member_level_name' => $member['member_level_name'],
                'one_member_id' => $member['member_id'],
                'two_member_id' => $two_member_id,
                'two_rate' => $two_rate,
                'two_commission' => $two_money,
                'two_fenxiao_id' => $p_fenxiao['fenxiao_id'] ?? 0,
                'two_fenxiao_name' => $p_fenxiao['fenxiao_name'] ?? '',
                'staff_rate' => $staff_rate,
                'staff_member_id' => $staff_member_id,
                'staff_commission' => $staff_money,
                'site2_rate' => $site_rate2,
                'site2_id' => $site2_id,
                'site2_commission' => $site_money2,
                'city_rate' => $city_rate,
                'city_id' => $city_id,
                'city_title' => $website['title'] ?? '',
                'city_commission' => $city_money,
                'site_rate' => $site_rate,
                'fenxiao_site_id' => $fenxiao_site_id,
                'site_commission' => $site_money,
                'pt_rate' => $pt_rate,
                'pt_commission' => $pt_money,
                'two_member_level' => $two_member_level ?? '',
                'two_member_level_name' => $two_member_level_name ?? '',
            ];

            if (!$res) {
                model("fenxiao_money")->add($save_data);
            } else {
                model("fenxiao_money")->update($save_data, ['fenxiao_order_id' => $res['fenxiao_order_id']]);
            }
        }
    }

    /***
     * 返积分 (普通1:1, 钻石1:1.2, 黑钻1:1.3, 合伙人1:1.5)
     */
    public function fanPoint($order)
    {
        //普通商品返积分
        if ($order['goods_type'] != 0) {
            return;
        }

        $member = model('member')->getInfo(['member_id' => $order['member_id']]); //下单用户
        if (empty($member)) {
            return;
        }

        // 默认倍率回退: 普通 1.0, 钻石 1.2, 黑钻 1.3, 合伙人 1.5
        $p_rate = 1.00;
        $city_member_level = model('city_member_level')->getInfo([
            'level_id' => $member['member_level'],
            'website_id' => $member['website_id'],
        ]);
        if ($city_member_level && floatval($city_member_level['point_feedback']) > 0) {
            $p_rate = floatval($city_member_level['point_feedback']);
        } else {
            $ml = model('member_level')->getInfo(['level_id' => $member['member_level']]);
            if ($ml && floatval($ml['point_feedback']) > 0) {
                $p_rate = floatval($ml['point_feedback']);
            }
        }

        $p_fenxiao = Db::name('fenxiao')->where('fenxiao_id', $member['fenxiao_id'])->find();
        $order_goods = model("order_goods")->getList([ [ 'order_id', '=', $order[ 'order_id' ] ] ], '*');

        foreach ($order_goods as $k => $v) {
            $jc_money = $v['real_goods_money'] * $p_rate;
            $jc_money = floatval(number_format($jc_money, 2, '.', ''));
            if ($jc_money > 0) {
                $res = model("fenxiao_money")->getInfo([
                    'order_id' => $order[ 'order_id' ],
                    'order_goods_id' => $v[ 'order_goods_id' ],
                ]);
                if (!$res) {
                    //启动分销
                    $data = [
                        'order_id' => $order[ 'order_id' ],
                        'order_no' => $order[ 'order_no' ],
                        'order_goods_id' => $v[ 'order_goods_id' ],
                        'site_id' => $order[ 'site_id' ],
                        'site_name' => $order[ 'site_name' ],
                        'goods_id' => $v[ 'goods_id' ],
                        'sku_id' => $v[ 'sku_id' ],
                        'sku_name' => $v[ 'sku_name' ],
                        'sku_image' => $v[ 'sku_image' ],
                        'price' => $v[ 'price' ],
                        'num' => $v[ 'num' ],
                        'real_goods_money' => $v[ 'real_goods_money' ],
                        'member_id' => $order[ 'member_id' ],
                        'member_name' => $order[ 'name' ],
                        'member_mobile' => $order[ 'mobile' ],
                        'full_address' => $order[ 'full_address' ] . $order[ 'address' ],
                        'one_fenxiao_id' => $p_fenxiao['fenxiao_id'] ?? 0,
                        'one_fenxiao_name' => $p_fenxiao['fenxiao_name'] ?? '',
                        'create_time' => time(),
                        'member_level' => $member['member_level'],
                        'member_level_name' => $member['member_level_name'],
                        'one_member_level' => $member['member_level'],
                        'one_member_level_name' => $member['member_level_name'],
                        'one_member_id' => $member['member_id'],
                        'one_rate_point' => $p_rate,
                        'one_commission_point' => $jc_money,
                    ];
                    model("fenxiao_money")->add($data);
                } else {
                    $data = [
                        'one_rate_point' => $p_rate,
                        'one_commission_point' => $jc_money,
                    ];
                    model("fenxiao_money")->update($data, [ 'fenxiao_order_id' => $res[ 'fenxiao_order_id' ] ]);
                }
            }
        }
    }



    /**
     * 订单退款
     * @param $order_goods_id
     * @return array
     */
    public function refund($order_goods_id)
    {
        $res = model("fenxiao_order")->update([ 'is_refund' => 1 ], [ [ 'order_goods_id', '=', $order_goods_id ] ]);
        model("fenxiao_money")->update([ 'is_refund' => 1 ], [ [ 'order_goods_id', '=', $order_goods_id ] ]);
        return $this->success($res);
    }

    /**
     * 订单结算
     * @param $order_id
     */
    public function settlement($order_id)
    {
        //获取未退款的和未结算的分销订单
        $fenxiao_orders = model("fenxiao_order")->getList([ [ 'order_id', '=', $order_id ], [ 'is_settlement', '=', 0 ], [ 'is_refund', '=', 0 ] ], '*');
        //同时修改分销订单状态为已结算
        model("fenxiao_order")->startTrans();
        try {
            $fenxiao_account = new FenxiaoAccount();
            model('fenxiao_order')->update([ 'is_settlement' => 1 ], [ [ 'order_id', '=', $order_id ] ]);
            $commission = 0;
            foreach ($fenxiao_orders as $fenxiao_order) {
                $commission += $fenxiao_order[ 'one_commission' ];
                $fenxiao_account->addAccount($fenxiao_order[ 'one_fenxiao_id' ], $fenxiao_order[ 'one_fenxiao_name' ], 'order', $fenxiao_order[ 'one_commission' ], $fenxiao_order[ 'fenxiao_order_id' ]);
                model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order[ 'one_fenxiao_id' ] ] ], 'total_commission', $fenxiao_order[ 'one_commission' ]);

                if ($fenxiao_order[ 'two_commission' ] > 0) {
                    $commission += $fenxiao_order[ 'two_commission' ];
                    $fenxiao_account->addAccount($fenxiao_order[ 'two_fenxiao_id' ], $fenxiao_order[ 'two_fenxiao_name' ], 'order', $fenxiao_order[ 'two_commission' ], $fenxiao_order[ 'fenxiao_order_id' ]);
                    model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order[ 'two_fenxiao_id' ] ] ], 'total_commission', $fenxiao_order[ 'two_commission' ]);
                }
                if ($fenxiao_order[ 'three_commission' ] > 0) {
                    $commission += $fenxiao_order[ 'three_commission' ];
                    $fenxiao_account->addAccount($fenxiao_order[ 'three_fenxiao_id' ], $fenxiao_order[ 'three_fenxiao_name' ], 'order', $fenxiao_order[ 'three_commission' ], $fenxiao_order[ 'fenxiao_order_id' ]);
                    model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order[ 'three_fenxiao_id' ] ] ], 'total_commission', $fenxiao_order[ 'three_commission' ]);
                }
            }
            //增加订单佣金结算
            model('order')->setInc([ [ 'order_id', '=', $order_id ] ], 'commission', $commission);
            model("fenxiao_order")->commit();
            return $this->success();
        } catch (\Exception $e) {
            model("fenxiao_order")->rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * calculateOrder 计算对应分销商的第一次订单数量&&金额
     * @param $order_id
     */
    public function calculateOrder($order_id)
    {
        $fenxiao_order_info = model('fenxiao_order')->getFirstData([ [ 'order_id', '=', $order_id ] ], 'one_fenxiao_id');
        if (!empty($fenxiao_order_info)) {
            $one_commission_sum = model('fenxiao_order')->getSum([ [ 'order_id', '=', $order_id ] ], 'one_commission');
            model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order_info[ 'one_fenxiao_id' ] ] ], 'one_fenxiao_order_num');
            model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order_info[ 'one_fenxiao_id' ] ] ], 'one_fenxiao_order_money', $one_commission_sum);
            // 分销商检测升级
            event('FenxiaoUpgrade', $fenxiao_order_info[ 'one_fenxiao_id' ]);
        }
        return $this->success();
    }

    /**
     * 获取分销订单列表
     * @param unknown $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     */
    public function getFenxiaoOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'fo.order_id DESC')
    {
        $field = '
        fo.fenxiao_order_id,fo.order_no,fo.site_id,fo.site_name,fo.sku_id,fo.sku_name,fo.sku_image,fo.price,fo.num,fo.real_goods_money,fo.member_name,
        fo.member_mobile,fo.one_fenxiao_name,fo.is_settlement,fo.commission,fo.is_refund,
        o.order_status_name,o.create_time,o.trade_type,o.order_type,fo.one_fenxiao_id,fo.two_fenxiao_id,fo.three_fenxiao_id,fo.one_commission,fo.two_commission,fo.three_commission
        ';

        $alias = 'fo';
        $join = [
            [
                'order o',
                'fo.order_id = o.order_id',
                'inner'
            ]
        ];
        $list = model('fenxiao_order')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        //补交易类型名称，便于分销端按链路区分订单（fenxiao_order 自身无该字段，取自 order 表）
        $list = $this->success($list);
        if (!empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['trade_type_name'] = \app\model\order\OrderCreate::tradeTypeName($v['trade_type'] ?? 0);
            }
        }
        return $list;
    }

    //返佣金
    public function getFenxiaoOrderPageList2($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'fo.order_id DESC')
    {
        $field = '
        fo.fenxiao_order_id,fo.order_no,fo.site_id,fo.site_name,fo.sku_id,fo.sku_name,fo.sku_image,fo.price,fo.num,fo.real_goods_money,fo.member_name,
        fo.member_mobile,fo.one_fenxiao_name,fo.is_settlement,fo.commission,fo.is_refund,
        o.order_status_name,o.create_time,fo.one_fenxiao_id,fo.two_fenxiao_id,fo.three_fenxiao_id,fo.one_commission,fo.two_commission,fo.three_commission
        ';

        $alias = 'fo';
        $join = [
            [
                'order o',
                'fo.order_id = o.order_id',
                'inner'
            ]
        ];
        $list = model('fenxiao_money')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }

    /**
     * 查询订单信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getFenxiaoOrderInfo($condition, $field = '*')
    {
        $fenxiao_order_info = model('fenxiao_order')->getInfo($condition, $field);
        return $this->success($fenxiao_order_info);
    }

    /**
     * 订单详情
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getFenxiaoOrderDetail($condition, $field = '*')
    {
        $fenxiao_order_info = model('fenxiao_order')->getInfo($condition, $field);
        return $this->success($fenxiao_order_info);
    }

    public function getFenxiaoOrderDetail2($condition, $field = '*')
    {
        $fenxiao_order_info = model('fenxiao_money')->getInfo($condition, $field);
        return $this->success($fenxiao_order_info);
    }

    /**
     * 获取分销订单数量
     * @param $condition
     */
    public function getFenxiaoOrderCount($condition)
    {
        $count = model('fenxiao_order')->getCount($condition);
        return $this->success($count);
    }
}