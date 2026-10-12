<?php
/**

 */

namespace app\shopapi\controller;

use app\model\order\LocalOrder as LocalOrderModel;

/**
 * 外卖订单
 * Class Order
 * @package app\shop\controller
 */
class Localorder extends BaseApi
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) {
            echo $this->response($token);
            exit;
        }
    }

    /**
     * 发货
     * @return false|string
     */
    public function delivery()
    {
        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        $deliverer = isset($this->params[ 'deliverer' ]) ? $this->params[ 'deliverer' ] : '';
        $deliverer_mobile = isset($this->params[ 'deliverer_mobile' ]) ? $this->params[ 'deliverer_mobile' ] : '';
        $local_order_model = new LocalOrderModel();
        $data = [
            'order_id' => $order_id,
            'deliverer' => $deliverer,
            'deliverer_mobile' => $deliverer_mobile,
            'site_id' => $this->site_id
        ];
        $result = $local_order_model->orderGoodsDelivery($data);
        return $this->response($result);
    }

    /**
     * 接单（待接单 → 备餐中）
     * @return false|string
     */
    public function take()
    {
        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        $local_order_model = new LocalOrderModel();
        $result = $local_order_model->orderLocalTake($order_id, $this->site_id);
        return $this->response($result);
    }

    /**
     * 确认送达（配送中 → 已送达）
     * @return false|string
     */
    public function finish()
    {
        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        $local_order_model = new LocalOrderModel();
        $result = $local_order_model->orderLocalFinishDelivery($order_id, $this->site_id);
        return $this->response($result);
    }

}