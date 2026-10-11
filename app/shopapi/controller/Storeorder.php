<?php
/**

 */

namespace app\shopapi\controller;

use app\model\order\StoreOrder as StoreOrderModel;

/**
 * 订单
 * Class Order
 * @package app\shop\controller
 */
class Storeorder extends BaseApi
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
     * 门店提货
     * @return false|string
     */
    public function storeOrderTakeDelivery()
    {
        $order_id = isset($this->params[ 'order_id' ]) ? $this->params[ 'order_id' ] : 0;
        $store_order_model = new StoreOrderModel();
        $force = intval( $this->params[ 'force' ] ?? 0 ) > 0;
        $result = $store_order_model->activeTakeDelivery( $order_id, $force );
        return $this->response($result);

    }

}