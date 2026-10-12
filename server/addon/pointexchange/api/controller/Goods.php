<?php
/**

 */

namespace addon\pointexchange\api\controller;

use app\api\controller\BaseApi;
use addon\pointexchange\model\Exchange as ExchangeModel;

/**
 * 积分兑换
 */
class Goods extends BaseApi
{

    /**
     * 详情信息
     */
    public function detail()
    {
        $id = isset($this->params['id']) ? $this->params['id'] : 1;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $exchange_model = new ExchangeModel();
        $info = $exchange_model->getExchangeGoodsDetail($id);
        return $this->response($info);
    }

    public function page()
    {
        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $type = isset($this->params['type']) ? $this->params['type'] : 1;//兑换类型，1：礼品，2：优惠券，3：红包
        $condition = [
            ['peg.state', '=', 1],
            ['peg.type', '=', $type],
        ];

        $order = 'peg.sort asc,peg.create_time desc';
        $field = 'peg.*';

        $alias = 'peg';
        $join = [];
        $exchange_model = new ExchangeModel();

        if ($type == 2) {
            $field .= ',pct.at_least,pct.money';
            $join = [
                ['promotion_platformcoupon_type pct', 'peg.type_id = pct.platformcoupon_type_id', 'inner']
            ];
        }
        $list = $exchange_model->getExchangePageList($condition, $page, $page_size, $order, $field, $alias, $join);
        return $this->response($list);
    }

}