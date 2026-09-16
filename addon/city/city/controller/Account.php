<?php
/**

 */

namespace addon\city\city\controller;

use addon\city\model\CityAccount;
use app\model\shop\ShopOpenAccount;
use app\model\web\WebSite as WebsiteModel;


class Account extends BaseCity
{

    /**
     * 城市分站账户面板
     */
    public function dashboard()
    {
        $city_account_model = new CityAccount();

        //站点信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([ [ 'site_id', '=', $this->site_id ] ], 'account,account_withdraw,account_shop,account_order', false);
        $total_account = $website_info[ 'data' ][ 'account' ] + $website_info[ 'data' ][ 'account_withdraw' ];
        $this->assign('total_account', number_format($total_account, 2, '.', ''));
        $this->assign('website_info', $website_info[ 'data' ]);

        //账户收支
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = [ 'site_id', '=', $this->site_id ];
            $type = input('type', '');//收支类型（1收入  2支出）
            switch ( $type ) {
                case 1:
                    $condition[] = [ 'account_data', '>', 0 ];
                    break;
                case 2:
                    $condition[] = [ 'account_data', '<', 0 ];
                    break;
            }
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = [ 'create_time', '>=', $start_time ];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'create_time', '<=', $end_time ];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'create_time', 'between', [ $start_time, $end_time ] ];
            }

            $field = 'account_no,site_id,account_type,account_data,from_type,type_name,relate_tag,create_time,remark';
            return $city_account_model->getCityAccountPageList($condition, $page, $page_size, 'id desc', $field);
        }

        return $this->fetch("account/dashboard", [], $this->replace);
    }

    /**
     * 城市分站费用
     */
    public function fee()
    {

        if (request()->isAjax()) {

            $shop_open = new ShopOpenAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = [ 'create_time', '>=', $start_time ];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'create_time', '<=', $end_time ];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'create_time', 'between', [ $start_time, $end_time ] ];
            }

            $list = $shop_open->getShopOpenAccountPageList([ [ 'website_id', '=', $this->site_id ] ], $page, $page_size, 'id desc');
            return $list;
        }

        return $this->fetch('account/fee', [], $this->replace);
    }

    /**
     * 城市分站订单交易与结算流水
     */
    public function orderlist()
    {
        $order_common_model = new \app\model\order\OrderCommon();

        // 统计金额
        $wait_condition = [
            ['website_id', '=', $this->site_id],
            ['order_status', '=', 10],
            ['is_settlement', '=', 0]
        ];
        $wait_money = \think\facade\Db::name('order')->where($wait_condition)->sum(\think\facade\Db::raw('order_money - refund_shop_money'));
        $this->assign('wait_settlement', number_format($wait_money ?: 0, 2, '.', ''));

        $ing_condition = [
            ['website_id', '=', $this->site_id],
            ['order_status', 'not in', [0, -1, 10]]
        ];
        $ing_money = \think\facade\Db::name('order')->where($ing_condition)->sum(\think\facade\Db::raw('order_money - refund_shop_money'));
        $this->assign('settlement', number_format($ing_money ?: 0, 2, '.', ''));

        $finish_condition = [
            ['website_id', '=', $this->site_id],
            ['order_status', '=', 10],
            ['is_settlement', '=', 1]
        ];
        $finish_money = \think\facade\Db::name('order')->where($finish_condition)->sum(\think\facade\Db::raw('order_money - refund_shop_money'));
        $this->assign('finish_settlement', number_format($finish_money ?: 0, 2, '.', ''));

        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition = [
                ['website_id', '=', $this->site_id]
            ];

            $order_status = input('order_status', '');
            if ($order_status != '') {
                switch ($order_status) {
                    case 1: // 进行中
                        $condition[] = ['order_status', 'not in', [0, -1, 10]];
                        break;
                    case 2: // 待结算
                        $condition[] = ['order_status', '=', 10];
                        $condition[] = ['is_settlement', '=', 0];
                        break;
                    case 3: // 已结算
                        $condition[] = ['order_status', '=', 10];
                        $condition[] = ['is_settlement', '=', 1];
                        break;
                }
            }

            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['finish_time', '>=', $start_time];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ['finish_time', '<=', $end_time];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['finish_time', 'between', [$start_time, $end_time]];
            }

            $field = 'order_id,order_no,order_money,refund_shop_money,platform_money,shop_money,order_status_name,finish_time';
            return $order_common_model->getOrderPageList($condition, $page, $page_size, 'order_id desc', $field);
        }

        return $this->fetch('account/order_list', [], $this->replace);
    }
}