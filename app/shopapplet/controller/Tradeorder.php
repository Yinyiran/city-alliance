<?php
/**

 */

namespace app\shopapplet\controller;

use app\model\order\LocalOrder as LocalOrderModel;
use app\model\order\Order as OrderModel;
use app\model\shopapplet\TradeOrder;

/**
 * 商家端订单管理（按交易类型/配送链路处理订单）
 *
 * 提供：待处理统计、按分组与阶段筛选的订单列表、订单详情、
 *       快递发货、外卖接单/开始配送/确认送达、物流公司列表
 *
 * 说明：到店核销沿用商家端已有的扫码核销链路（前端跳转 writeOff 页面 +
 *       /shopapplet/order/dodelivery 接口），核销含动态码校验与完整权限判断，
 *       此处不重复实现，避免出现两套核销入口。
 */
class Tradeorder extends Common
{
    protected $tradeOrderModel;

    public function __construct()
    {
        parent::__construct();
        $this->tradeOrderModel = new TradeOrder();
    }

    /**
     * 待处理订单数量（首页角标 / 页签计数）
     */
    public function pendingCount()
    {
        $result = $this->tradeOrderModel->pendingCount($this->site_id);
        return $this->response($result);
    }

    /**
     * 订单管理列表
     * 参数：biz_type(all/express/store/local)、stage(pending/doing/finished/all)、searchtxt、page
     */
    public function lists()
    {
        $biz_type = $this->params['biz_type'] ?? TradeOrder::BIZ_ALL;
        $stage = $this->params['stage'] ?? TradeOrder::STAGE_ALL;
        $searchtxt = $this->params['searchtxt'] ?? '';
        $page = intval($this->params['page'] ?? 1);
        if ($page < 1) $page = 1;

        $result = $this->tradeOrderModel->manageList($this->site_id, $biz_type, $stage, $searchtxt, $page);
        return $this->response($result);
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $order_id = intval($this->params['order_id'] ?? 0);
        if (empty($order_id)) {
            return $this->response($this->error([], '缺少订单id'));
        }
        $result = $this->tradeOrderModel->orderDetail($this->site_id, $order_id);
        return $this->response($result);
    }

    /**
     * 可发货的物流公司列表
     */
    public function expressCompany()
    {
        $result = $this->tradeOrderModel->expressCompanyList($this->site_id);
        return $this->response($result);
    }

    /**
     * 快递发货（快递邮寄订单：待发货 → 已发货）
     * 参数：order_id、order_goods_ids（逗号分隔）、express_company_id、delivery_no
     */
    public function expressDelivery()
    {
        $order_id = intval($this->params['order_id'] ?? 0);
        $order_goods_ids = $this->params['order_goods_ids'] ?? '';
        $express_company_id = intval($this->params['express_company_id'] ?? 0);
        $delivery_no = trim($this->params['delivery_no'] ?? '');

        if (empty($order_id)) {
            return $this->response($this->error([], '缺少订单id'));
        }
        if (empty($order_goods_ids)) {
            return $this->response($this->error([], '请选择要发货的商品'));
        }
        if (empty($express_company_id)) {
            return $this->response($this->error([], '请选择物流公司'));
        }
        if ($delivery_no === '') {
            return $this->response($this->error([], '请填写快递单号'));
        }

        //归属与状态校验：仅本店铺、待发货状态的普通订单可发货
        $order = \think\facade\Db::name('order')
            ->where('order_id', $order_id)
            ->where('site_id', $this->site_id)
            ->field('order_id,order_type,order_status')
            ->find();
        if (empty($order)) {
            return $this->response($this->error([], '订单不存在或不属于当前店铺'));
        }
        if (intval($order['order_type']) != 1) {
            return $this->response($this->error([], '该订单不是快递邮寄订单，不能执行物流发货'));
        }
        if (intval($order['order_status']) != 1) {
            return $this->response($this->error([], '当前订单状态不可发货'));
        }

        //校验待发货的订单项确实属于该订单，防止跨订单传参导致发货串单
        $goods_ids = array_filter(array_map('intval', explode(',', $order_goods_ids)));
        if (empty($goods_ids)) {
            return $this->response($this->error([], '请选择要发货的商品'));
        }
        $valid_count = \think\facade\Db::name('order_goods')
            ->where('order_id', $order_id)
            ->where('site_id', $this->site_id)
            ->whereIn('order_goods_id', $goods_ids)
            ->count();
        if ($valid_count != count($goods_ids)) {
            return $this->response($this->error([], '发货商品与订单不匹配，请刷新后重试'));
        }

        $order_model = new OrderModel();
        $data = [
            'type' => 'manual',
            'order_goods_ids' => $order_goods_ids,
            'express_company_id' => $express_company_id,
            'delivery_no' => $delivery_no,
            'order_id' => $order_id,
            'delivery_type' => 1,
            'site_id' => $this->site_id,
            'template_id' => 0,
        ];
        $result = $order_model->orderGoodsDelivery($data);
        return $this->response($result);
    }

    /**
     * 外卖接单（待接单 → 备餐中）
     */
    public function localTake()
    {
        $order_id = intval($this->params['order_id'] ?? 0);
        if (empty($order_id)) {
            return $this->response($this->error([], '缺少订单id'));
        }
        $local_order_model = new LocalOrderModel();
        $result = $local_order_model->orderLocalTake($order_id, $this->site_id);
        return $this->response($result);
    }

    /**
     * 外卖开始配送（待接单/备餐中 → 配送中）
     * 参数：order_id、deliverer（配送员）、deliverer_mobile（配送员电话）
     */
    public function localDelivery()
    {
        $order_id = intval($this->params['order_id'] ?? 0);
        $deliverer = trim($this->params['deliverer'] ?? '');
        $deliverer_mobile = trim($this->params['deliverer_mobile'] ?? '');

        if (empty($order_id)) {
            return $this->response($this->error([], '缺少订单id'));
        }
        if ($deliverer === '') {
            return $this->response($this->error([], '请填写配送员姓名'));
        }
        if ($deliverer_mobile === '') {
            return $this->response($this->error([], '请填写配送员联系电话'));
        }

        $local_order_model = new LocalOrderModel();
        $data = [
            'order_id' => $order_id,
            'deliverer' => $deliverer,
            'deliverer_mobile' => $deliverer_mobile,
            'delivery_type' => 'default',
            'site_id' => $this->site_id,
        ];
        $result = $local_order_model->orderGoodsDelivery($data);
        return $this->response($result);
    }

    /**
     * 外卖确认送达（配送中 → 已送达）
     */
    public function localFinish()
    {
        $order_id = intval($this->params['order_id'] ?? 0);
        if (empty($order_id)) {
            return $this->response($this->error([], '缺少订单id'));
        }
        $local_order_model = new LocalOrderModel();
        $result = $local_order_model->orderLocalFinishDelivery($order_id, $this->site_id);
        return $this->response($result);
    }
}
