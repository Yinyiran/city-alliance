<?php
/**

 */

namespace app\api\controller;

use app\model\system\Pay as PayModel;

/**
 * 支付控制器
 */
class Pay extends BaseApi
{
    /**
     * 支付信息
     */
    public function info()
    {
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $pay = new PayModel();
        $info = $pay->getPayInfo($out_trade_no);
        return $this->response($info);
    }

    /**
     * 支付调用
     */
    public function pay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $pay_type = $this->params[ 'pay_type' ];
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $app_type = $this->params[ 'app_type' ];
        $pay = new PayModel();
        $is_matched = $this->params[ 'is_matched' ] ?? 0;
        $profit_sharing = $this->params[ 'profit_sharing' ] ?? false; //微信支付是否指定分账
        $info = $pay->pay($pay_type, $out_trade_no, $app_type, $this->member_id, '', $is_matched, $profit_sharing);
        return $this->response($info);
    }

    /**
     * 门店收银台：生成顾客扫码付款的收款码（微信 Native）
     * 参数：order_no 或 amount + store_id
     */
    public function cashierCode()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0 ) return $this->response($token );

        $order_no = $this->params[ 'order_no' ] ?? '';
        $amount = $this->params[ 'amount' ] ?? 0;
        $store_id = intval($this->params[ 'store_id' ] ?? 0);
        $pay_type = $this->params[ 'pay_type' ] ?? 'WECHAT_PAY';

        if (empty($order_no)) {
            return $this->response($this->error('', '请先创建订单'));
        }

        $order_info = model('order')->getInfo([
            [ 'order_no', '=', $order_no ],
            [ 'pay_status', '=', 0 ],
            [ 'order_status', '=', 0 ],
        ], 'order_id,order_no,pay_money,member_id,trade_type,site_id');
        if (empty($order_info)) {
            return $this->response($this->error('', '订单不存在或已支付'));
        }

        //到店消费订单允许现场自定义金额（仅下单时已允许的情况下）
        if ($amount > 0 && abs($amount - $order_info[ 'pay_money' ]) > 0.001) {
            $goods_ids = model('order_goods')->getColumn([ [ 'order_id', '=', $order_info[ 'order_id' ] ] ], 'goods_id');
            $allow = 0;
            foreach ($goods_ids as $goods_id) {
                $goods = model('goods')->getInfo([ [ 'goods_id', '=', $goods_id ] ], 'trade_type,allow_custom_amount');
                if (intval($goods[ 'trade_type' ] ?? 0) == 1 && intval($goods[ 'allow_custom_amount' ] ?? 0) == 1) {
                    $allow = 1;
                    break;
                }
            }
            if (!$allow) {
                return $this->response($this->error('', '该订单不支持修改金额'));
            }
            $min = 0;
            $max = 0;
            foreach ($goods_ids as $goods_id) {
                $goods = model('goods')->getInfo([ [ 'goods_id', '=', $goods_id ] ], 'min_order_amount,max_order_amount');
                $min = $goods[ 'min_order_amount' ] ?? 0;
                $max = $goods[ 'max_order_amount' ] ?? 0;
                break;
            }
            $amount = round(floatval($amount), 2);
            if ($min > 0 && $amount < $min) return $this->response($this->error('', "金额不能低于 {$min} 元"));
            if ($max > 0 && $amount > $max) return $this->response($this->error('', "金额不能高于 {$max} 元"));

            model('order')->update([
                'custom_amount' => $amount,
                'pay_money' => $amount,
                'order_money' => $amount,
                'pay_scene' => 1,
                'modify_time' => time(),
            ], [ [ 'order_id', '=', $order_info[ 'order_id' ] ] ]);
            $pay_money = $amount;
        } else {
            $pay_money = $order_info[ 'pay_money' ];
        }

        if ($pay_money <= 0) {
            return $this->response($this->error('', '订单金额为0，无需支付'));
        }

        $pay = new PayModel();
        //app_type 传 pc 走微信 Native，返回收款二维码
        $info = $pay->pay($pay_type, $order_info[ 'out_trade_no' ] ?? $order_info[ 'order_no' ], 'pc', $order_info[ 'member_id' ], '', 0, false);
        return $this->response($this->success([
            'order_no' => $order_info[ 'order_no' ],
            'store_id' => $store_id,
            'pay_money' => $pay_money,
            'pay_info' => $info[ 'data' ] ?? [],
        ]));
    }

    /**
     * 支付方式
     */
    public function type()
    {
        $pay = new PayModel();
        $info = $pay->getPayType($this->params);
        $temp = empty($info) ? [] : $info;
        $type = [];
        foreach ($temp[ 'data' ] as $k => $v) {
            array_push($type, $v[ "pay_type" ]);
        }
        $type = implode(",", $type);
        return $this->response(success(0, '', ['pay_type' => $type]));
    }

    /**
     * 获取订单支付状态
     */
    public function status()
    {
        $pay = new PayModel();
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $res = $pay->getPayStatus($out_trade_no);
        return $this->response($res);
    }
    
    /**
     * 汇付支付
     * 订单方法
     */
    public function huifupay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $out_trade_no = $this->params[ 'out_trade_no' ];
        $payModel = model('pay')->getInfo([ [ 'out_trade_no', '=', $out_trade_no ], ['pay_status', '=', 0] ]);

        if (!$payModel) {
            return $this->response($this->error('订单不存在'));
        }

        $order = model('order')->getInfo(['out_trade_no'=>$out_trade_no]);
        $result = (new Huifujspay())->jspay($order);
        return $this->response($this->success($result));
    }

    /**
     * 开通会员支付
     */
    public function memberlevelpay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $payModel = model('pay')->getInfo([ [ 'out_trade_no', '=', $out_trade_no ], ['pay_status', '=', 0] ]);
        if (!$payModel) {
            return $this->response($this->error('订单不存在'));
        }
        $order = model('member_level_order')->getInfo(['out_trade_no'=>$out_trade_no]);
        $result = (new Huifujspay())->memberlevelpay($order, $payModel);
        return $this->response($this->success($result));
    }

    /**
     * 商家缴纳保证金支付
     */
    public function shopdepositpay()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $out_trade_no = $this->params[ 'out_trade_no' ];
        $payModel = model('pay')->getInfo([ [ 'out_trade_no', '=', $out_trade_no ], ['pay_status', '=', 0] ]);
        if (!$payModel) {
            return $this->response($this->error('订单不存在'));
        }
        $order = model('shop_deposit_order')->getInfo(['out_trade_no'=>$out_trade_no]);
        $result = (new Huifujspay())->shopdepositpay($order, $payModel);
        return $this->response($this->success($result));
    }

    /**
     * 汇付线下终端 (扫码盒子) 反扫付款接口
     */
    public function terminalMicropay()
    {
        $site_id = intval($this->params['site_id'] ?? 0);
        $amount = round(floatval($this->params['amount'] ?? 0), 2);
        $auth_code = trim(strval($this->params['auth_code'] ?? ''));
        $remark = trim(strval($this->params['remark'] ?? ''));
        $store_id = intval($this->params['store_id'] ?? 0);
        $terminal_id = trim(strval($this->params['terminal_id'] ?? ''));

        if (empty($auth_code)) {
            return $this->response($this->error('', '请扫描顾客付款码'));
        }
        if ($amount <= 0) {
            return $this->response($this->error('', '请输入有效的收款金额'));
        }

        // 如果未传 site_id，尝试从 terminal_id 匹配商户，或从当前登录商户获取
        if ($site_id <= 0 && !empty($terminal_id)) {
            $shop_by_terminal = model('shop')->getInfo([['huifu_terminal_id', '=', $terminal_id]], 'site_id');
            if (!empty($shop_by_terminal)) {
                $site_id = intval($shop_by_terminal['site_id']);
            }
        }

        if ($site_id <= 0) {
            return $this->response($this->error('', '未指定收款商户或终端设备未绑定'));
        }

        $shop_info = model('shop')->getInfo([['site_id', '=', $site_id]], 'site_id, site_name, logo, website_id, huifu_id, huifu_terminal_id, pay_redirect_appid, pay_redirect_path, pay_ad_img, pay_ad_url');
        if (empty($shop_info)) {
            return $this->response($this->error('', '商户不存在'));
        }

        // 创建买单订单
        $order_common = new \app\model\order\OrderCommon();
        $order_no = $order_common->createOrderNo($site_id);
        $pay_model = new PayModel();
        $out_trade_no = $pay_model->createOutTradeNo(0);

        model('order')->startTrans();
        try {
            $order_data = [
                'order_no' => $order_no,
                'site_id' => $site_id,
                'site_name' => $shop_info['site_name'],
                'website_id' => $shop_info['website_id'] ?? 0,
                'order_name' => '线下终端收款-' . $shop_info['site_name'],
                'order_from' => 'terminal',
                'order_from_name' => '扫码盒子终端',
                'order_type' => 2,
                'order_type_name' => '到店买单',
                'trade_type' => 1,
                'pay_scene' => 2, // 线下终端收银
                'custom_amount' => $amount,
                'out_trade_no' => $out_trade_no,
                'member_id' => 0,
                'name' => '顾客',
                'goods_money' => $amount,
                'order_money' => $amount,
                'pay_money' => $amount,
                'goods_num' => 1,
                'buyer_message' => $remark,
                'order_status' => 0,
                'order_status_name' => '待支付',
                'pay_status' => 0,
                'create_time' => time(),
                'is_enable_refund' => 1,
            ];
            $order_id = model('order')->add($order_data);

            $order_goods_data = [
                'order_id' => $order_id,
                'site_id' => $site_id,
                'site_name' => $shop_info['site_name'],
                'order_no' => $order_no,
                'member_id' => 0,
                'sku_name' => '向商家付款',
                'goods_name' => '线下终端买单',
                'sku_image' => $shop_info['logo'] ?: '',
                'price' => $amount,
                'cost_price' => $amount,
                'num' => 1,
                'goods_money' => $amount,
                'cost_money' => $amount,
                'real_goods_money' => $amount,
                'trade_type' => 1,
                'delivery_status' => 0,
                'delivery_status_name' => '待支付',
            ];
            model('order_goods')->add($order_goods_data);

            $pay_model->addPay(
                $site_id,
                $out_trade_no,
                '',
                '向' . $shop_info['site_name'] . '付款',
                '线下终端买单' . $amount . '元',
                $amount,
                '',
                'OrderPayNotify',
                ''
            );
            model('order')->commit();
        } catch (\Exception $e) {
            model('order')->rollback();
            return $this->response($this->error('', '生成收银订单失败：' . $e->getMessage()));
        }

        // 调用汇付聚合反扫
        $huifu_model = new \app\model\pay\Huifu();
        $huifu_param = [
            'out_trade_no' => $out_trade_no,
            'pay_money' => $amount,
            'pay_body' => $shop_info['site_name'] . '-线下收银',
            'auth_code' => $auth_code,
            'huifu_id' => !empty($shop_info['huifu_id']) ? $shop_info['huifu_id'] : '',
        ];
        $pay_res = $huifu_model->createMicropay($huifu_param);

        if ($pay_res['code'] >= 0) {
            // 支付成功，立即进行订单状态扭转与分佣触发、微信消息推送
            $order_common->orderOnlinePay([
                'out_trade_no' => $out_trade_no,
                'pay_type' => 'HUIFU_MICROPAY'
            ]);

            return $this->response($this->success([
                'status' => 'SUCCESS',
                'order_no' => $order_no,
                'out_trade_no' => $out_trade_no,
                'pay_money' => $amount,
                'site_name' => $shop_info['site_name'],
                'pay_redirect_appid' => $shop_info['pay_redirect_appid'] ?? '',
                'pay_redirect_path' => $shop_info['pay_redirect_path'] ?? '',
                'pay_ad_img' => $shop_info['pay_ad_img'] ?? '',
                'pay_ad_url' => $shop_info['pay_ad_url'] ?? '',
            ], '支付成功'));
        } else {
            // 检查是否为等待顾客输密码 (USERPAYING)
            $msg = $pay_res['message'] ?? '';
            if (strpos($msg, 'USERPAYING') !== false || strpos($msg, '输入密码') !== false || strpos($msg, '支付中') !== false) {
                return $this->response($this->success([
                    'status' => 'USERPAYING',
                    'order_no' => $order_no,
                    'out_trade_no' => $out_trade_no,
                    'pay_money' => $amount,
                ], '等待顾客输入支付密码'));
            }
            return $this->response($this->error('', $msg ?: '收单扣款失败'));
        }
    }

    /**
     * 汇付线下终端支付结果查询接口
     */
    public function terminalQuery()
    {
        $out_trade_no = trim(strval($this->params['out_trade_no'] ?? ''));
        if (empty($out_trade_no)) {
            return $this->response($this->error('', '请传入商户支付流水号'));
        }

        // 先查本地支付记录
        $pay_info = model('pay')->getInfo([['out_trade_no', '=', $out_trade_no]], 'pay_status, pay_money, site_id');
        if (empty($pay_info)) {
            return $this->response($this->error('', '交易记录不存在'));
        }
        if ($pay_info['pay_status'] == 2) {
            $shop = model('shop')->getInfo([['site_id', '=', $pay_info['site_id']]], 'site_name, pay_redirect_appid, pay_redirect_path, pay_ad_img, pay_ad_url');
            return $this->response($this->success([
                'status' => 'SUCCESS',
                'out_trade_no' => $out_trade_no,
                'pay_money' => $pay_info['pay_money'],
                'site_name' => $shop['site_name'] ?? '',
                'pay_redirect_appid' => $shop['pay_redirect_appid'] ?? '',
                'pay_redirect_path' => $shop['pay_redirect_path'] ?? '',
                'pay_ad_img' => $shop['pay_ad_img'] ?? '',
                'pay_ad_url' => $shop['pay_ad_url'] ?? '',
            ], '支付已完成'));
        }

        // 调用汇付斗拱查单接口
        $huifu_model = new \app\model\pay\Huifu();
        $query_res = $huifu_model->queryScanPay($out_trade_no);
        if ($query_res['code'] >= 0 && !empty($query_res['data'])) {
            $data = $query_res['data'];
            $trans_stat = $data['trans_stat'] ?? '';
            if ($trans_stat == 'S' || ($data['resp_code'] ?? '') == '00000000') {
                $order_common = new \app\model\order\OrderCommon();
                $order_common->orderOnlinePay([
                    'out_trade_no' => $out_trade_no,
                    'pay_type' => 'HUIFU_MICROPAY'
                ]);
                $shop = model('shop')->getInfo([['site_id', '=', $pay_info['site_id']]], 'site_name, pay_redirect_appid, pay_redirect_path, pay_ad_img, pay_ad_url');
                return $this->response($this->success([
                    'status' => 'SUCCESS',
                    'out_trade_no' => $out_trade_no,
                    'pay_money' => $pay_info['pay_money'],
                    'site_name' => $shop['site_name'] ?? '',
                    'pay_redirect_appid' => $shop['pay_redirect_appid'] ?? '',
                    'pay_redirect_path' => $shop['pay_redirect_path'] ?? '',
                    'pay_ad_img' => $shop['pay_ad_img'] ?? '',
                    'pay_ad_url' => $shop['pay_ad_url'] ?? '',
                ], '支付成功'));
            } elseif ($trans_stat == 'P') {
                return $this->response($this->success([
                    'status' => 'USERPAYING',
                    'out_trade_no' => $out_trade_no,
                ], '顾客正在输入密码'));
            } else {
                return $this->response($this->error('', $data['resp_desc'] ?? '交易失败或已关闭'));
            }
        }

        return $this->response($this->error('', '暂未查询到支付结果'));
    }

}