<?php


namespace app\admin\controller;


use app\model\order\Order as OrderModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\OrderCreate as OrderCreateModel;
use app\model\order\StoreOrder;
use think\facade\Config;

class AdminOrder extends BaseAdmin
{
    /**
     * 订单列表
     */
    public function lists()
    {
        $order_label_list = array(
            "order_no" => "订单号",
            "out_trade_no" => "外部单号",
            "name" => "收货人姓名",
            "mobile" => "收货人手机号",
            "order_name" => "商品名称",
//            "site_name" => "店铺名称",
        );
        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');
        $site_id = input("site_id", "");
        $order_label = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";
        $search_text = input("search", '');
        $promotion_type = input("promotion_type", '');
        $order_type = input("order_type", 'all');//营销类型
        $trade_type = input('trade_type', '');//交易类型
        $is_own = input('is_own', '');
        $settlement_state = input("settlement_state", "");//结算状态
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('limit', PAGE_LIST_ROWS);
            $alias = 'o';
            $join = null;
            $condition = [
                ["type", "=", 1]
            ];
            //订单状态
            if ($order_status != "") {
                if ($order_status != 'refunding') {
                    $condition[] = ["o.order_status", "=", $order_status];
                } else {
                    $join = [
                        [
                            'order_goods og',
                            'og.order_id = o.order_id',
                            'left'
                        ]
                    ];
                    $condition[] = ["og.refund_status", "not in", [0, 3]];
                }
            }
            //是否自营
            if ($is_own !== '') {
                $join = [
                    [
                        'shop s',
                        's.site_id = o.site_id',
                        'left'
                    ]
                ];
                $condition[] = ['s.is_own', '=', $is_own];
            }
            //订单内容 模糊查询
            if ($order_name != "") {
                $condition[] = ["o.order_name", 'like', "%$order_name%"];
            }
            //订单来源
            if ($order_from != "") {
                $condition[] = ["o.order_from", "=", $order_from];
            }
            //订单支付
            if ($pay_type != "") {
                $condition[] = ["o.pay_type", "=", $pay_type];
            }
            //订单类型
            if ($order_type != 'all') {
                $condition[] = ["o.order_type", "=", $order_type];
            }
            //交易类型 0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送
            //注意：0 是合法取值，必须用 !== '' 严格比较，否则会被当成空值跳过
            if ($trade_type !== '' && $trade_type !== null) {
                $condition[] = [ "o.trade_type", "=", intval($trade_type) ];
            }
            //结算状态
            if ($settlement_state == 1) {
                $condition[] = ["o.is_settlement", "=", '1'];
            } elseif ($settlement_state == 2) {
                $condition[] = ["o.is_settlement", "=", '0'];
            }
            //营销类型
            if ($promotion_type != "") {
                if ($promotion_type == 'empty') {
                    $condition[] = ["o.promotion_type", "=", ''];
                } else {
                    $condition[] = ["o.promotion_type", "=", $promotion_type];
                }
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["o.create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["o.create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['o.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            if ($search_text != "") {
                $condition[] = ['o.' . $order_label, 'like', "%$search_text%"];
            }
            if (!empty($site_id)) {
                $condition[] = ["o.site_id", '=', $site_id];
            }
            $list = $order_common_model->getOrderPageList($condition, $page_index, $page_size, "o.create_time desc", $field = 'o.*', $alias, $join);
            return $list;
        } else {
            $order_type_list = $order_common_model->getOrderTypeStatusList();
            //新增加
            foreach ($order_type_list as $k => $v) {
                if ($v['name'] == '外卖订单') {
                    unset($order_type_list[$k]);
                }
                if ($v['name'] == '虚拟订单') {
                    unset($order_type_list[$k]);
                }
                if (isset($v['status']['refunding'])) {
                    unset($order_type_list[$k]['status']['refunding']);
                }
            }

            //新增加结束
            $this->assign("order_type_list", $order_type_list);
            $this->assign("order_label_list", $order_label_list);
            $this->assign("order_status_list", $order_type_list[1]['status']);//订单状态
            $this->assign("trade_type_list", OrderCreateModel::tradeTypeList());//交易类型

            //订单来源 (支持端口)
            $order_from = Config::get("app_type");
            $this->assign('order_from_list', $order_from);

            $pay_type = $order_common_model->getPayType();
            $this->assign("pay_type_list", $pay_type);

            //营销活动类型
            $order_promotion_type = event('OrderPromotionType');
            $this->assign("promotion_type", $order_promotion_type);
            $this->assign("http_type", get_http_type());
            return $this->fetch('admin_order/lists');
        }

    }

    /**
     * 新增加获取订单信息
     */
    public function getOrderInfo()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
//                [ "site_id", "=", $this->site_id ],
            );
            $result = $order_common_model->getOrderInfo($condition);
            return $result;
        }
    }

    /**
     * 新增加获取订单项列表
     */
    public function getOrderGoodsList()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $delivery_status = input("delivery_status", '');
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
//                [ "site_id", "=", $this->site_id ],
                ["refund_status", "<>", 3],
            );
            if ($delivery_status != '') {
                $condition[] = ["delivery_status", "=", $delivery_status];
            }
            $field = "order_goods_id, order_id, site_id, site_name, sku_name, sku_image, sku_no, is_virtual, price, cost_price, num, goods_money, cost_money, delivery_status, delivery_no, goods_id, delivery_status_name,refund_status,refund_status_name";
            $result = $order_common_model->getOrderGoodsList($condition, $field, '', null, "");
            return $result;
        }
    }

    /**
     * 新增加订单发货
     */
    public function delivery()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $order_goods_ids = input("order_goods_ids", '');
            $express_company_id = input("express_company_id", 0);
            $delivery_no = input("delivery_no", '');
            $delivery_type = input("delivery_type", 0);
            $order_model = new OrderModel();
            //平台代发：site_id 必须取订单真实所属店铺。
            //原代码硬编码为 1，会把 express_delivery_package 等物流记录写到错误站点，
            //导致店铺端查不到自己的包裹，且站点维度的物流统计失真。
            $order_site_info = model( 'order' )->getInfo( [ [ 'order_id', '=', $order_id ] ], 'site_id' );
            $data = array(
                "type" => input('type', 'manual'),//发货方式（手动发货、电子面单）
                "order_goods_ids" => $order_goods_ids,
                "express_company_id" => $express_company_id,
                "delivery_no" => $delivery_no,
                "order_id" => $order_id,
                "delivery_type" => $delivery_type,
                "site_id" => intval( $order_site_info[ 'site_id' ] ?? 0 ),
                "template_id" => input('template_id', 0)//电子面单模板id
            );
            //判断site_id
            $result = $order_model->orderGoodsDelivery2($data);
            return $result;
        } else {
            $order_id = input("order_id", 0);
            $delivery_status = input("delivery_status", '');
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
//                [ "site_id", "=", $this->site_id ],
            );
            //仅在明确指定发货状态时才过滤。原判断写作 `if ($delivery_status === '')`
            //导致未指定状态时反而追加 delivery_status='' 的恒假条件、查不到任何商品项。
            if ($delivery_status !== '' && $delivery_status !== null) {
                $condition[] = ["delivery_status", "=", $delivery_status];
            }
            $field = "order_goods_id, order_id, site_id, site_name, sku_name, sku_image, sku_no, is_virtual, price, cost_price, num, goods_money, cost_money, delivery_status, delivery_no, goods_id, delivery_status_name";
            $order_goods_list_result = $order_common_model->getOrderGoodsList($condition, $field, '', null, "delivery_no");
            $order_goods_list = $order_goods_list_result["data"];
            $this->assign("order_goods_list", $order_goods_list);
            return $this->fetch("order/delivery");
        }
    }

    /**
     * 到店核销单：直接提货（核销）
     * 视图 app/admin/view/admin_order/store_order_take_delivery.html 会 POST 本方法，
     * 原控制器缺失该方法导致到店单「提货」按钮必然报错，此处补齐并复用
     * StoreOrder::activeTakeDelivery 的 order_type=2 与订单状态校验。
     */
    public function storeOrderTakedelivery()
    {
        $order_id = input('order_id', 0);
        $store_order_model = new StoreOrder();
        return $store_order_model->activeTakeDelivery($order_id);
    }

    /**
     * 快递订单详情
     */
    public function detail()
    {
        $order_id = input("order_id", 0);
        $order_common_model = new OrderCommonModel();
        $condition = array(
            ['order_id', '=', $order_id],
        );
        $order_detail_result = $order_common_model->getOrderDetail($condition);
        $order_detail = $order_detail_result["data"];
        if (empty($order_detail))
            $this->error("查询不到此订单信息!", addon_url('admin/order/lists'));

        $this->assign("order_detail", $order_detail);

        switch ($order_detail["order_type"]) {
            case 1 :
                $template = "admin_order/detail";
                break;
            case 2 :
                $template = "storeorder/detail";
                break;
            case 3 :
                $template = "localorder/detail";
                break;
            case 4 :
                $template = "virtualorder/detail";
                break;
        }

        return $this->fetch($template);
    }
}