<?php
/**

 */

namespace app\admin\controller;

use app\model\BaseModel;
use app\model\order\Config as ConfigModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\OrderCreate as OrderCreateModel;use app\model\order\OrderExport;
use app\model\system\Promotion as PromotionModel;
use phpoffice\phpexcel\Classes\PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\Writer\Excel2007;
use think\facade\Config;

/**
 * 订单管理 控制器
 */
class Order extends BaseAdmin
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
            "site_name" => "店铺名称",
        );
        $goods_type_list = array(
            '-1' => '全部',
            '0' => '普通商品',
            '1' => '福利品',
            '2' => '平台福利区',
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
        //快捷页签：空=全部 wait_delivery=待发货 wait_verify=待核销
        $tab = input('tab', '');//快捷页签
        $is_own = input('is_own','');
        $settlement_state = input("settlement_state", "");//结算状态
        $goods_type = input('goods_type', '-1'); //商品类型
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('limit', PAGE_LIST_ROWS);
            $alias = 'o';
            $join = null;
            $condition = [
//                ["order_type", "=", 1]
            ];
            //订单状态
            if ($order_status != "") {
                if($order_status != 'refunding'){
                    $condition[] = [ "o.order_status", "=", $order_status ];
                }else{
                    $join = [
                        [
                            'order_goods og',
                            'og.order_id = o.order_id',
                            'left'
                        ]
                    ];
                    $condition[] = [ "og.refund_status", "not in", [0,3] ];
                }
            }
            //是否自营
            if($is_own !== ''){
                $join = [
                    [
                        'shop s',
                        's.site_id = o.site_id',
                        'left'
                    ]
                ];
                $condition[] = ['s.is_own','=',$is_own];
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
            if($order_type != 'all'){
                $condition[] = ["o.order_type", "=", $order_type];
            }
            //交易类型 0到店自提 1到店消费 2预约到店 3快递邮寄 4外卖配送
            //注意：0 是合法取值，必须用 !== '' 严格比较，否则会被当成空值跳过
            if ($trade_type !== '' && $trade_type !== null) {
                $condition[] = [ "o.trade_type", "=", intval($trade_type) ];
            }
            //待发货：快递发货类(order_type=1)且尚未发货
            if ($tab === 'wait_delivery') {
                $condition[] = [ 'o.order_type', '=', 1 ];
                $condition[] = [ 'o.order_status', '=', 1 ];
            } elseif ($tab === 'wait_verify') {
                //待核销：到店核销类(到店自提/到店消费/预约到店)且尚未核销
                $condition[] = [ 'o.trade_type', 'in', [ 0, 1, 2 ] ];
                $condition[] = [ 'o.order_status', 'in', [ 1, 2 ] ];
                $condition[] = [ 'o.delivery_status', '=', 0 ];
            }

            //结算状态
            if ($settlement_state ==1) {
                $condition[] = ["o.is_settlement", "=", '1'];
            }elseif($settlement_state==2){
                $condition[] = ["o.is_settlement", "=", '0'];
            }
            //营销类型
            if ($promotion_type != "") {
                if($promotion_type == 'empty'){
                    $condition[] = ["o.promotion_type", "=", ''];
                }else{
                    $condition[] = ["o.promotion_type", "=", $promotion_type];
                }
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["o.create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["o.create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
	            $condition[] = [ 'o.create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
            }
            if ($search_text != "") {
                $condition[] = ['o.'.$order_label, 'like', "%$search_text%"];
            }
            if(!empty($site_id))
            {
                $condition[] = ["o.site_id", '=', $site_id];
            }
            //商品类型
            if($goods_type != '-1'){
                if ($goods_type == '0') {
                    $condition[] = ["o.goods_type", '=', $goods_type];
                    $condition[] = ["o.is_admin", '=', 0];
                } elseif ($goods_type == '1') {
                    $condition[] = ["o.goods_type", '=', $goods_type];
                    $condition[] = ["o.is_admin", '=', 0];
                } elseif ($goods_type == '2') {
                    $condition[] = ["o.goods_type", '=', 1];
                    $condition[] = ["o.is_admin", '=', 1];
                }

            }
            $list = $order_common_model->getOrderPageList($condition, $page_index, $page_size, "o.create_time desc", $field = 'o.*', $alias, $join);
            return $list;
        } else {
            $order_type_list = $order_common_model->getOrderTypeStatusList();
            foreach ($order_type_list as $key => $value) {
                if (in_array($value['type'], [3,4])) {
                    unset($order_type_list[$key]);
                }
            }
            $this->assign("order_type_list", $order_type_list);
            $this->assign("order_label_list", $order_label_list);
            $this->assign("order_status_list", $order_type_list[1]['status']);//订单状态
            $this->assign("trade_type_list", OrderCreateModel::tradeTypeList());//交易类型
            $this->assign('tab', $tab);//快捷页签

            //订单来源 (支持端口)
            $order_from = Config::get("app_type");
            $this->assign('order_from_list', $order_from);

            $pay_type = $order_common_model->getPayType();
            $this->assign("pay_type_list", $pay_type);

            //营销活动类型
            $order_promotion_type = event('OrderPromotionType');
            $this->assign("promotion_type", $order_promotion_type);
            $this->assign("http_type", get_http_type());

            $this->assign("goods_type_list", $goods_type_list);
            return $this->fetch('order/lists');
        }

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
        if(empty($order_detail))
            $this->error("查询不到此订单信息!", addon_url('admin/order/lists'));

        $this->assign("order_detail", $order_detail);

        switch ($order_detail["order_type"]) {
            case 1 :
                $template = "order/detail";
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

    /**
     * 订单设置
     */
    public function config()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            //订单事件时间设置数据
            $order_event_time_config_data = [
                'auto_close' => input('order_auto_close_time', 0),//订单未付款自动关闭时间 数字 单位(天)
                'auto_take_delivery' => input('order_auto_take_delivery_time', 0),//订单发货后自动收货时间 数字 单位(天)
                'auto_complete' => input('order_auto_complete_time', 0),//订单收货后自动完成时间 数字 单位(天)

                'auto_refund_take_delivery' => input('auto_refund_take_delivery', 0),//买家退货后商家自动确认收货时间 数字 单位(天)
                'auto_refund_confirm' => input('auto_refund_confirm', 0),//申请维权后,商家自动通过时间 数字 单位(天)
                'auto_refund_cancel' => input('auto_refund_cancel', 0),//维权被拒绝后,自动撤销维权时间 数字 单位(天)
            ];
            // //订单发票设置数据
            // $order_invoice_config_data = [
            //     'is_use' => input('invoice_is_use', 0),//是否启用发票 1:启用 0:不启用
            //     'tax' => input('invoice_tax', 0),//发票税率 0-100 不取小数
            //     'content' => input('invoice_content', ''),//发票内容 文具,服装,水果,建材 格式为逗号分隔的字符串 textarea
            // ];
            $res = $config_model->setOrderEventTimeConfig($order_event_time_config_data);
            // $config_model->setOrderInvoiceConfig($order_invoice_config_data);

            //订单评价设置数据
            $order_evaluate_config_data = [
                'evaluate_status' => input('evaluate_status', 0),//订单评价状态（0关闭 1开启）
                'evaluate_show' => input('evaluate_show', 0),//显示评价（0关闭 1开启）
                'evaluate_audit' => input('evaluate_audit', 0),//评价审核（0关闭 1开启）
            ];
            $config_model->setOrderEvaluateConfig($order_evaluate_config_data);

            //分销比例设置
            $reward_config_data = [
                'city_rate' => input('city_rate', 0),
                'site_rate' => input('site_rate', 0),
                'staff_rate' => input('staff_rate', 0),
                'site_rate2' => input('site_rate2', 0),
                'level2' => input('level2', 0),
                'level3' => input('level3', 0),
                'level4' => input('level4', 0),
                'point_year' => input('point_year', 1),
                'video_day' => input('video_day', 0),
                'video_order_rate' => input('video_order_rate', 0),
                'video_share_rate' => input('video_share_rate', 0),
                'video_end_rate' => input('video_end_rate', 0),
            ];

            if ($reward_config_data['video_order_rate'] > 1) {
                return (new BaseModel())->error('', '权重最大设置1');
            }
            if ($reward_config_data['video_share_rate'] > 1) {
                return (new BaseModel())->error('', '权重最大设置1');
            }
            if ($reward_config_data['video_end_rate'] > 1) {
                return (new BaseModel())->error('', '权重最大设置1');
            }

            model('reward_config')->update($reward_config_data, ['id'=>1]);

            return $res;
        } else {

            //订单事件时间设置
            $order_event_time_config = $config_model->getOrderEventTimeConfig();
            $this->assign('order_event_time_config', $order_event_time_config['data']['value']);

            //订单发票设置
//            $order_invoice_config = $config_model->getOrderInvoiceConfig();
//            $this->assign('order_invoice_config', $order_invoice_config['data']['value']);

            //订单评价设置
            $order_evaluate_config = $config_model->getOrderEvaluateConfig();
            $this->assign('order_event_time_config', $order_event_time_config[ 'data' ][ 'value' ]);
            $this->assign('order_evaluate_config', $order_evaluate_config[ 'data' ][ 'value' ]);

            //分销奖励比例
            $reward_config = model('reward_config')->getInfo(['id'=>1]);
            $this->assign('reward_config', $reward_config);
            return $this->fetch('order/config');
        }
    }

    /**
     * 订单导出（已订单为主）
     */
    public function exportOrder()
    {
        $order_label_list = array(
            "order_no" => "订单号",
            "out_trade_no" => "外部单号",
            "name" => "收货人姓名",
            "mobile" => "收货人手机号",
            "order_name" => "商品名称",
        );

        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');
        $order_label = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";
        $search_text = input("search", '');
        $promotion_type = input("promotion_type", '');
        $order_type = input("order_type", 'all');
        $condition = [];
        $condition_desc = [];

        $order_common_model = new OrderCommonModel();
        //订单类型
        $order_type_name = '全部';
        if ($order_type != 'all') {
            $condition[] = ["o.order_type", "=", $order_type];

            $order_type_list = $order_common_model->getOrderTypeStatusList();
            $order_type_list = array_column($order_type_list, 'name', 'type');
            $order_type_name = $order_type_list[$order_type];
        }
        $condition_desc[] = ['name' => '订单类型', 'value' => $order_type_name];

        //订单状态
        $order_status_name = '全部';
        if ($order_status != "") {
            if ($order_status != 'refunding') {
                $condition[] = ["o.order_status", "=", $order_status];
                $order_status_list = $order_common_model->order_status;
                $order_status_name = $order_status_list[$order_status]['name'] ?? '';
            } else {
                $join = [
                    [
                        'order_goods og',
                        'og.order_id = o.order_id',
                        'left'
                    ]
                ];
                $condition[] = ["og.refund_status", "not in", [0, 3]];
                $order_status_name = '维权中';
            }
        }
        $condition_desc[] = ['name' => '订单状态', 'value' => $order_status_name];

        //订单内容 模糊查询
        if ($order_name != "") {
            $condition[] = ["o.order_name", 'like', "%$order_name%"];
        }
        //订单来源
        $order_from_name = '全部';
        if ($order_from != "") {
            $condition[] = ["o.order_from", "=", $order_from];
            //订单来源 (支持端口)
            $order_from_list = Config::get("app_type");
            $order_from_name = $order_from_list[$order_from]['name'] ?? '';
        }
        $condition_desc[] = ['name' => '订单来源', 'value' => $order_from_name];


        //订单支付
        $pay_type_name = '全部';
        if ($pay_type != "") {
            $condition[] = ["o.pay_type", "=", $pay_type];
            $pay_type_list = $order_common_model->getPayType();
            $pay_type_name = $pay_type_list[$pay_type] ?? '';
        }
        $condition_desc[] = ['name' => '支付方式', 'value' => $pay_type_name];

        //营销类型
        $promotion_type_name = '全部';
        if ($promotion_type != "") {
            if ($promotion_type == 'empty') {
                $condition[] = ["o.promotion_type", "=", ''];
            } else {
                $condition[] = ["o.promotion_type", "=", $promotion_type];
            }
            //营销活动类型
            $promotion_model = new PromotionModel();
            $promotion_type_list = $promotion_model->getPromotionType();
            $promotion_type_list = array_column($promotion_type_list, 'name', 'type');
            $promotion_type_name = $promotion_type_list[$promotion_type] ?? '';
        }
        $condition_desc[] = ['name' => '营销活动', 'value' => $promotion_type_name];
        $time_name = '';
        if (!empty($start_time) && empty($end_time)) {
            $condition[] = ["o.create_time", ">=", date_to_time($start_time)];
            $time_name = $start_time . '起';
        } elseif (empty($start_time) && !empty($end_time)) {
            $condition[] = ["o.create_time", "<=", date_to_time($end_time)];
            $time_name = '至' . $end_time;
        } elseif (!empty($start_time) && !empty($end_time)) {
            $condition[] = ['o.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            $time_name = $start_time . ' 至 ' . $end_time;
        }
        $condition_desc[] = ['name' => '下单时间', 'value' => $time_name];

        if ($search_text != "") {
            $condition[] = ['o.'.$order_label, 'like', "%$search_text%"];
        }
        foreach ($order_label_list as $k => $v) {
            $order_label_name = $v;
            if ($k == $order_label) {
                $condition_desc[] = ['name' => $order_label_name, 'value' => $search_text];
            } else {
                $condition_desc[] = ['name' => $order_label_name, 'value' => ''];
            }
        }
        $order_export_model = new OrderExport();
        $result = $order_export_model->orderExport($condition, $condition_desc, 0, $this->app_module,$join ?? null);
        return $result;

    }

    /**
     * 订单导出（已订单商品为主）
     */
    public function exportOrderGoods()
    {
        $order_label_list = array(
            "order_no" => "订单号",
            "out_trade_no" => "外部单号",
            "name" => "收货人姓名",
            "mobile" => "收货人手机号",
            "order_name" => "商品名称",
        );

        $condition = [];
        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');

        $order_label = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";

        $search_text = input("search", '');
        $promotion_type = input("promotion_type", '');
        $order_type = input("order_type", 'all');
        $condition_desc = [];

        $order_common_model = new OrderCommonModel();
        //订单类型
        $order_type_name = '全部';
        if ($order_type != 'all') {
            $condition[] = ["o.order_type", "=", $order_type];

            $order_type_list = $order_common_model->getOrderTypeStatusList();
            $order_type_list = array_column($order_type_list, 'name', 'type');
            $order_type_name = $order_type_list[$order_type];
        }
        $condition_desc[] = ['name' => '订单类型', 'value' => $order_type_name];

        //订单状态
        $order_status_name = '全部';
        if ($order_status != "") {
            if ($order_status != 'refunding') {
                $condition[] = ["o.order_status", "=", $order_status];
                $order_status_list = $order_common_model->order_status;
                $order_status_name = $order_status_list[$order_status]['name'] ?? '';
            } else {
                $condition[] = ["og.refund_status", "not in", [0, 3]];
                $order_status_name = '维权中';
            }
        }
        $condition_desc[] = ['name' => '订单状态', 'value' => $order_status_name];

        //订单内容 模糊查询
        if ($order_name != "") {
            $condition[] = ["o.order_name", 'like', "%$order_name%"];
        }
        //订单来源
        $order_from_name = '全部';
        if ($order_from != "") {
            $condition[] = ["o.order_from", "=", $order_from];
            //订单来源 (支持端口)
            $order_from_list = Config::get("app_type");
            $order_from_name = $order_from_list[$order_from]['name'] ?? '';
        }
        $condition_desc[] = ['name' => '订单来源', 'value' => $order_from_name];


        //订单支付
        $pay_type_name = '全部';
        if ($pay_type != "") {
            $condition[] = ["o.pay_type", "=", $pay_type];
            $pay_type_list = $order_common_model->getPayType();
            $pay_type_name = $pay_type_list[$pay_type] ?? '';
        }
        $condition_desc[] = ['name' => '支付方式', 'value' => $pay_type_name];

        //营销类型
        $promotion_type_name = '全部';
        if ($promotion_type != "") {
            if ($promotion_type == 'empty') {
                $condition[] = ["o.promotion_type", "=", ''];
            } else {
                $condition[] = ["o.promotion_type", "=", $promotion_type];
            }
            //营销活动类型
            $promotion_model = new PromotionModel();
            $promotion_type_list = $promotion_model->getPromotionType();
            $promotion_type_list = array_column($promotion_type_list, 'name', 'type');
            $promotion_type_name = $promotion_type_list[$promotion_type] ?? '';
        }
        $condition_desc[] = ['name' => '营销活动', 'value' => $promotion_type_name];

        $time_name = '';
        if (!empty($start_time) && empty($end_time)) {
            $condition[] = ["o.create_time", ">=", date_to_time($start_time)];
            $time_name = $start_time . '起';
        } elseif (empty($start_time) && !empty($end_time)) {
            $condition[] = ["o.create_time", "<=", date_to_time($end_time)];
            $time_name = '至' . $end_time;
        } elseif (!empty($start_time) && !empty($end_time)) {
            $condition[] = ['o.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            $time_name = $start_time . ' 至 ' . $end_time;
        }
        $condition_desc[] = ['name' => '下单时间', 'value' => $time_name];

        if ($search_text != "") {
            $condition[] = ['o.' . $order_label, 'like', "%$search_text%"];
        }
        foreach ($order_label_list as $k => $v) {
            $order_label_name = $v;
            if ($k == $order_label) {
                $condition_desc[] = ['name' => $order_label_name, 'value' => $search_text];
            } else {
                $condition_desc[] = ['name' => $order_label_name, 'value' => ''];
            }
        }
        $order_export_model = new OrderExport();
        $result = $order_export_model->orderGoodsExport($condition, $condition_desc, $this->site_id, $this->app_module);
        return $result;

    }

    /**
     * 导出字段
     * @return array
     */
    public function getPrintingField()
    {
        $order_export_model = new OrderExport();
        $data = [
            'order_field' => $order_export_model->order_field,
            'order_goods_field' => $order_export_model->order_goods_field
        ];

        return success('1', '', $data);
    }

    /**
     * 交易记录
     */
    public function tradelist()
    {
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $member_id = input('member_id', 0);//会员id
            $search_text = input('search_text', 0);//h关键字查询
            $condition = array(['site_id','=',$this->site_id]);
            if($member_id > 0){
                $condition[] = ["member_id", "=", $member_id];
            }
            if(!empty($search_text)){
                $condition[] = [ 'order_no|order_name', 'like', '%' . $search_text . '%' ];
            }

            return $order_common_model->getTradePageList($condition, $page, $page_size, "create_time desc");

        }
    }


    /**
     * 订单导出记录
     * @return mixed
     */
    public function export()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');
            $export_model = new OrderExport();
            $condition = array (
                [ 'site_id', '=', $this->site_id ]
            );
            //对时间判断
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }

            $result = $export_model->getExportPageList($condition, $page_index, $page_size, 'create_time desc', '*');
            return $result;
        } else {
            return $this->fetch("order/export");
        }
    }

    /**
     * 删除订单导出记录
     */
    public function deleteExport(){

        if (request()->isAjax()) {
            $export_ids = input('export_ids', '');

            $export_model = new OrderExport();
            $condition = array (
                [ 'site_id', '=', $this->site_id ],
                ['export_id', 'in', (string)$export_ids]
            );
            $result = $export_model->deleteExport($condition);
            return $result;
        }
    }

}