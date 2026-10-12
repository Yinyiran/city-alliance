<?php
/**

 */

namespace addon\city\city\controller;

use app\model\shop\ShopAccount as ShopaccountModel;
use app\model\web\Account as AccountModel;
use app\model\shop\Shop as ShopModel;

/**
 * 商家账户控制器
 */
class Shopaccount extends BaseCity
{
    /**
     * 转账列表
     */
    public function withdrawList()
    {
        $model    = new AccountModel();
        $shop_sum = $model->getShopDataSum();
        $this->assign('shop_sum', $shop_sum);

        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $status      = input('status', '');
            $start_date  = input('start_date', '');
            $end_date    = input('end_date', '');
            $period_id   = input('period_id', '');

            $site_id = input('site_id', '');
            //查询是否为当前分站店铺信息
            $shop_model = new ShopModel();
            $shop_info = $shop_model->getShopInfo([['site_id', '=', $site_id],['website_id','=',$this->site_id]]);
            if(empty($shop_info)){
                return $shop_model->error();
            }
            if (!empty($site_id)) {
                $condition[] = ['site_id', '=', $site_id];
            }
            if ($search_text) {
                // $condition[] = ['site_name|settlement_bank_account_name|mobile|withdraw_no', 'like', '%'.$search_text.'%'];
                $condition[] = ['site_name', 'like', '%' . $search_text . '%'];
            }

            if ($start_date != '' && $end_date != '') {
                $condition[] = ['apply_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['apply_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['apply_time', '<=', strtotime($end_date)];
            }

            if ($status !== '') {
                $condition[] = ['status', '=', $status];
            }
            if ($period_id !== '') {
                $condition[] = ['period_id', '=', $period_id];
            }
            $account_model = new ShopaccountModel();
            return $account_model->getShopWithdrawPageList($condition, $page, $page_size, 'apply_time desc');
        } else {
            $period_id = input('period_id', '');
            $this->assign('period_id', $period_id);
            $this->forthMenu();
//            $this->getTransferAction();

            return $this->fetch("shop_account/withdraw_list");
        }
    }
}