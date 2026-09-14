<?php


namespace addon\city\city\controller;

use addon\shopwithdraw\model\Config as ShopWithdrawConfig;
use app\model\BaseModel;
use app\model\shop\Shop as ShopModel;
use app\model\shop\ShopAccount;
use app\model\shop\ShopCategory as ShopCategoryModel;
use app\model\shop\ShopExport;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\member\Member as MemberModel;
use app\model\order\OrderCommon;
use app\model\shop\ShopDeposit;
use app\model\system\Address as AddressModel;
use phpoffice\phpexcel\Classes\PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\Writer\Excel2007;
use app\model\shop\ShopApply as ShopApplyModel;
use think\facade\Cache;
use think\facade\Session;
use app\model\web\WebSite;
use app\model\web\Config as ConfigModel;

/**
 * 商家管理 控制器
 */
class Shop extends BaseCity
{

    /******************************* 商家列表及相关操作 ***************************/

    /**
     * 商家列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $seo_keywords = input('seo_keywords', '');
            $category_id = input('category_id', 0);
            $group_id = input('group_id', 0);
            $shop_status = input('shop_status', '');
            $cert_id = input('cert_id', '');
            $is_own = input('is_own', '');
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');

            $condition[] = [ 'website_id', '=', $this->site_id ];
            $condition[] = [ 'site_name', 'like', '%' . $search_text . '%' ];

            $condition[] = [ 'seo_keywords', 'like', '%' . $seo_keywords . '%' ];

            //商家分类
            if ($category_id != 0) {
                $condition[] = [ 'category_id', '=', $category_id ];
            }
            //店铺等级
            if ($group_id != 0) {
                $condition[] = [ 'group_id', '=', $group_id ];
            }
            //商家状态
            if ($shop_status != '') {
                $condition[] = [ 'shop_status', '=', $shop_status ];
            }
            if ($cert_id) {
                switch ( $cert_id ) {
                    case 1: //未认证
                        $condition[] = [ 'cert_id', '=', 0 ];
                        break;
                    case 2: //已认证
                        $condition[] = [ 'cert_id', '>', 0 ];
                        break;
                }
            }
            if ($is_own != '') {
                $condition[] = [ 'is_own', '=', $is_own ];
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = [ 'expire_time', '>=', strtotime($start_time) ];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = [ "expire_time", "<=", strtotime($end_time) ];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = [ "expire_time", ">=", strtotime($start_time) ];
                $condition[] = [ "expire_time", "<=", strtotime($end_time) ];
            }

            $order = 'site_id desc';
            $field = '*';

            $shop_model = new ShopModel();
            // 商家名称site_name 商家分类category_name 店铺等级group_name 是否自营is_own 1：是 0：否 商家状态shop_status 1：正常 0：锁定 有效期
            return $shop_model->getShopPageList($condition, $page, $page_size, $order, $field);

        } else {
            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $condition = [];
            $condition[] = ['website_id', '=', $this->site_id];
            $shop_category_list = $shop_category_model->getCategoryList($condition, 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list[ 'data' ]);

            //商家主营分组
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([ [ 'is_own', '=', 0 ] ], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list[ 'data' ]);

            $is_addon_city = addon_is_exit('city');
            $this->assign('is_addon_city', $is_addon_city);
            return $this->fetch('shop/lists', [], $this->replace);
        }
    }

    /**
     * 商家详情
     */
    public function shopDetail()
    {
        $site_id = input('site_id', 0);
        $shop_model = new ShopModel();

        //商家信息
        $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ] ]);
        $this->assign('shop_info', $shop_info);

        //认证信息
        $cert_info = $shop_model->getShopCert([ [ 'site_id', '=', $site_id ] ]);
        $this->assign('cert_info', $cert_info);

        return $this->fetch('shop/shop_detail', [], $this->replace);
    }

    /**
     * 基本信息
     */
    public function basicInfo_yuan()
    {
        $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $data = [
                //待定
                'shop_status' => input('shop_status', ''),//店铺经营状态（0.关闭，1正常）
                'sort' => input('sort', 0),//排序号
                'logo' => input('logo', ''),//店铺logo
                'avatar' => input('avatar', ''),//店铺头像（大图）
                'banner' => input('banner', ''),//店铺条幅
                'seo_keywords' => input('seo_keywords', ''),//店铺关键字
                'seo_description' => input('seo_description', ''),//店铺简介
                'telephone' => input('telephone', ''),//联系电话
                'is_recommend' => input('is_recommend', 0),//是否推荐 1是 0否
                'shop_qtian' => input('shop_qtian', 0),//七天退货 1是 0否
                'shop_zhping' => input('shop_zhping', 0),//正品保障 1是 0否
                'shop_erxiaoshi' => input('shop_erxiaoshi', 0),//两小时发货 1是 0否
                'shop_tuihuo' => input('shop_tuihuo', 0),//退货承诺 1是 0否
                'shop_shiyong' => input('shop_shiyong', 0),//试用中心 1是 0否
                'shop_shiti' => input('shop_shiti', 0),//实体验证 1是 0否
                'shop_xiaoxie' => input('shop_xiaoxie', 0),//消协保证 1是 0否
            ];
            return $shop_model->editShop($data, [ [ 'site_id', '=', $site_id ], [ 'website_id', '=', $this->site_id ] ]);
        } else {
            //商家信息
            $site_id = input('site_id', 22);
            $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ] ]);
            $this->assign('shop_info', $shop_info[ 'data' ]);

            //关联前台会员信息
            if (!empty($shop_info[ 'data' ][ 'member_id' ])) {
                $member_id = $shop_info[ 'data' ][ 'member_id' ];
                $member_model = new MemberModel();
                $member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $member_id ] ]);
                $this->assign('member_info', $member_info[ 'data' ]);
            }

            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $shop_category_list = $shop_category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list[ 'data' ]);

            //商家开店套餐（非自营）
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([ [ 'is_own', '=', 0 ] ], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list[ 'data' ]);

            //商家开店套餐（自营）
            $shop_group_model = new ShopGroupModel();
            $shop_own_group_list = $shop_group_model->getGroupList([ [ 'is_own', '=', 1 ] ], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_own_group_list', $shop_own_group_list[ 'data' ]);
            //四级菜单
            $this->forthMenu([ 'site_id' => $site_id ]);

            return $this->fetch('shop/basic_info', [], $this->replace);
        }
    }

    /**
     * 认证信息
     */
    public function certInfo()
    {
        $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            //认证信息
            $data = [
                /* 公司信息 只有公司类型有 */
                'company_name' => input('company_name', ''),//公司名称
                'company_province_id' => input('company_province_id', 0),//公司所在省
                'company_city_id' => input('company_city_id', 0),//公司所在市
                'company_district_id' => input('company_district_id', 0),//公司所在区/县
                'company_address' => input('company_address', ''),//公司地址
                /* 联系人手机号身份证 公司、个人类型都有 */
                'contacts_name' => input('contacts_name', ''),//联系人姓名
                'contacts_mobile' => input('contacts_mobile', ''),//联系人手机
                'contacts_card_no' => input('contacts_card_no', ''),//联系人身份证
                'contacts_card_electronic_1' => input('contacts_card_electronic_1', ''),//申请人手持身份证电子版
                'contacts_card_electronic_2' => input('contacts_card_electronic_2', ''),//申请人身份证正面
                'contacts_card_electronic_3' => input('contacts_card_electronic_3', ''),//申请人身份证反面
                /* 营业执照 税务 只有公司类型有 */
                'business_licence_number' => input('business_licence_number', ''),//统一社会信用码 input
                'business_licence_number_electronic' => input('business_licence_number_electronic', ''),//营业执照电子版
                'business_sphere' => input('business_sphere', ''),//法定经营范围 textarea
                'tax_registration_certificate' => input('tax_registration_certificate', ''),//税务登记证号
                'tax_registration_certificate_electronic' => input('tax_registration_certificate_electronic', ''),//税务登记证号电子版
                /* 对公账户信息 只有公司类型有 */
                'bank_account_name' => input('bank_account_name', ''),//银行开户名
                'bank_account_number' => input('bank_account_number', ''),//公司银行账号
                'bank_name' => input('bank_name', ''),//开户银行支行名称
                'bank_address' => input('bank_address', ''),//开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
                'site_id' => $site_id
            ];
            //查询是否为当前分站店铺信息
            $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ], [ 'website_id', '=', $this->site_id ] ]);
            if (empty($shop_info)) {
                return $shop_model->error();
            }
            $res = $shop_model->editShopCert($data, [ [ 'site_id', '=', $site_id ] ]);
            if ($res) {
                $shop_model->editShop([ 'cert_id' => $res[ 'data' ][ 'data' ] ], [ [ 'site_id', '=', $site_id ] ]);
            }
            return $res;
        } else {
            $site_id = input('site_id', 0);
            $cert_info = $shop_model->getShopCert([ [ 'site_id', '=', $site_id ] ]);
            $this->assign('cert_info', $cert_info[ 'data' ]);

            //四级菜单
            $this->forthMenu([ 'site_id' => $site_id ]);
            $this->assign('site_id', $site_id);
            return $this->fetch('shop/cert_info', [], $this->replace);
        }
    }

    /**
     * 结算信息
     */
    public function settlementInfo()
    {
        $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $bank_type = input('bank_type', 0);

            //结算账户信息
            $cert_data = [
                /* 结算信息 公司、个人类型都有 */
                'bank_type' => input('bank_type', 0),//结算账户类型  1银行卡 2 支付宝
                'settlement_bank_name' => input('settlement_bank_name', 0),//结算开户银行支行名称
                'settlement_bank_address' => input('settlement_bank_address', 0),//结算开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
                'site_id' => $site_id
            ];

            if ($bank_type == 1) {
                $cert_data[ 'settlement_bank_account_name' ] = input('settlement_bank_account_name', 0);//结算银行开户名
                $cert_data[ 'settlement_bank_account_number' ] = input('settlement_bank_account_number', 0);//结算公司银行账号
            } elseif ($bank_type == 2) {
                $cert_data[ 'settlement_bank_account_name' ] = input('zfb_settlement_bank_account_name', 0);//结算银行开户名
                $cert_data[ 'settlement_bank_account_number' ] = input('zfb_settlement_bank_account_number', 0);//结算公司银行账号
            } else {
                $cert_data[ 'settlement_bank_account_name' ] = input('settlement_bank_account_name', 0);//结算银行开户名
                $cert_data[ 'settlement_bank_account_number' ] = input('settlement_bank_account_number', 0);//结算公司银行账号
            }
            //查询是否为当前分站店铺信息
            $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ], [ 'website_id', '=', $this->site_id ] ]);
            if (empty($shop_info)) {
                return $shop_model->error();
            }

            return $shop_model->editShopCert($cert_data, [ [ 'site_id', '=', $site_id ] ]);
        } else {
            $site_id = input('site_id', 0);
            //查询是否为当前分站店铺信息
            $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ], [ 'website_id', '=', $this->site_id ] ]);
            if (empty($shop_info)) {
                return $shop_model->error();
            }
            //获取商家结算账户信息
            $cert_info = $shop_model->getShopCert([ [ 'site_id', '=', $site_id ] ]);
            $this->assign('site_id', $site_id);
            $this->assign('cert_info', $cert_info[ 'data' ]);

            //四级菜单
            $this->forthMenu([ 'site_id' => $site_id ]);

            return $this->fetch('shop/settlement_info', [], $this->replace);
        }
    }

    /**
     * 账户信息
     */
    public function accountInfo()
    {
        $site_id = input('site_id', 0);
        //四级菜单
        $this->forthMenu([ 'site_id' => $site_id ]);
        $shop_model = new ShopModel();
        $condition = [
            [ 'site_id', '=', $site_id ]
        ];
        $account_info = $shop_model->getShopInfo($condition);

        $account = $account_info[ 'data' ][ 'account' ] - $account_info[ 'data' ][ 'account_withdraw_apply' ];
        $this->assign('account', number_format($account, 2, '.', ''));

        $this->assign('account_info', $account_info[ 'data' ]);
        $this->assign('order_calc', 0);//待结算

        return $this->fetch('shop/account_info', [], $this->replace);
    }


    /**
     * 获取待结算列表
     */
    public function getOrderCalc()
    {

        if (request()->isAjax()) {
            $order_common = new OrderCommon();
            $site_id = input('site_id', 0);
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "create_time desc");
            $is_refund = input("is_refund", '');
            $order_no = input("order_no", '');

            $condition = array (
                [ 'site_id', "=", $site_id ],
                [ 'is_settlement', "=", 0 ],
                [ 'order_status', "not in", '0,-1' ],
                [ 'website_id', '=', $this->site_id ]
            );
            if ($order_no) {
                $condition[] = [ 'order_no', 'like', '%' . $order_no . '%' ];
            }
            if ($is_refund !== '') {
                $condition[] = [ 'refund_status', '=', $is_refund ];
            }

            $list = $order_common->getOrderPageList($condition, $page, $page_size, $order, $field = 'order_id,order_no,order_type_name,order_status_name,order_money,shop_money,platform_money,is_settlement,create_time,refund_status,order_type');
            return $list;
        }
    }


    /**
     * 获取账户流水
     */
    public function getShopAccount()
    {
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $account_model = new ShopAccount();

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = [ 'site_id', '=', $site_id ];
            $type = input('type', '');//收支类型（1收入  2支出）
            if (!empty($type)) {
                switch ( $type ) {
                    case 1:
                        $condition[] = [ 'account_data', '>', 0 ];
                        break;
                    case 2:
                        $condition[] = [ 'account_data', '<', 0 ];
                        break;
                }
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

            return $account_model->getAccountPageList($condition, $page, $page_size, 'id desc');
        }
    }


    /**
     * 获取提现记录
     */
    public function getShopWithdraw()
    {
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $account_model = new ShopAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "id desc");
            $search_text = input("search_text", "");

            $condition = array (
                [ 'site_id', "=", $site_id ],
            );
            if (!empty($search_text)) {
                $condition[] = [ 'withdraw_no|settlement_bank_account_name|mobile', 'like', '%' . $search_text . '%' ];
            }
            $list = $account_model->getShopWithdrawPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 获取保证金记录
     */
    public function getShopDeposit()
    {
        if (request()->isAjax()) {
            $shop_deposit_model = new ShopDeposit();
            $site_id = input('site_id', 0);
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "id desc");
            $search_text = input("search_text", "");

            $condition = array (
                [ 'site_id', "=", $site_id ],
            );
            if (!empty($search_text)) {
                $condition[] = [ 'pay_no|pay_account_name', 'like', '%' . $search_text . '%' ];
            }
            $list = $shop_deposit_model->getShopDepositPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 商家锁定
     */
    public function lockShop()
    {

    }

    /**
     * 商家解锁
     */
    public function unlockShop()
    {

    }

    /**
     *  商家导出
     */
    public function exportShop()
    {
        $search_text = input('search_text', '');
        $category_id = input('category_id', 0);
        $group_id = input('group_id', 0);
        $shop_status = input('shop_status', '');

        $condition[] = [ 'website_id', '=', $this->site_id ];
        if ($search_text) {
            $condition[] = [ 'site_name', 'like', '%' . $search_text . '%' ];
        }

        //商家分类
        if ($category_id != 0) {
            $condition[] = [ 'category_id', '=', $category_id ];
        }
        //店铺等级
        if ($group_id != 0) {
            $condition[] = [ 'group_id', '=', $group_id ];
        }
        //商家状态
        if ($shop_status != '') {
            $condition[] = [ 'shop_status', '=', $shop_status ];
        }
        $order = 's.site_id desc';
        $shop_model = new ShopModel();

        $shop = $shop_model->getShopCertList($condition, $order);

        $header_arr = array (
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
        );

        $shop_export_model = new ShopExport();
        //导出所有字段
        $field = array_merge($shop_export_model->shop_field, $shop_export_model->shop_cert_field);

        //接收需要展示的字段
        $input_field = input('field', implode(',', array_keys($field)));
        $input_field = explode(',', $input_field);

        //处理数据
        if (!empty($shop[ 'data' ])) {
            $shop_list = $shop_export_model->handleData($shop[ 'data' ], $input_field);
        }

        $count = count($input_field);
        // 实例化excel
        $phpExcel = new \PHPExcel();

        $phpExcel->getProperties()->setTitle("店铺信息");
        $phpExcel->getProperties()->setSubject("店铺信息");
        //单独添加列名称
        $phpExcel->setActiveSheetIndex(0);

        for ($i = 0; $i < $count; $i++) {
            $phpExcel->getActiveSheet()->setCellValue($header_arr[ $i ] . '1', $field[ $input_field[ $i ] ]);
        }

        if (!empty($shop_list)) {
            foreach ($shop_list as $k => $v) {
                $start = $k + 2;
                for ($i = 0; $i < $count; $i++) {

                    $phpExcel->getActiveSheet()->setCellValue($header_arr[ $i ] . $start, $v[ $input_field[ $i ] ]);
                }
            }
        }

        // 重命名工作sheet
        $phpExcel->getActiveSheet()->setTitle('店铺信息');
        // 设置第一个sheet为工作的sheet
        $phpExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = date('Y年m月d日-店铺信息表', time()) . '.xlsx';
        $objWriter->save($file);

        header("Content-type:application/octet-stream");

        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        unlink($file);
        exit;

    }

    /**
     * 导出字段
     * @return array
     */
    public function getPrintingField()
    {
        $model = new ShopExport();
        $field = array_merge($model->shop_field, $model->shop_cert_field);
        return success('1', '', $field);
    }

    /**
     * 店铺绑定openid 二维码
     */
    public function shopBindQrcode()
    {
        $key = 'bing_shop_openid_' . md5(uniqid(null, true));
        $url = addon_url("wechat://api/auth/shopBindOpenid", [ "key" => $key ]);
        Session::set("bing_shop_openid", $key);
        Cache::tag("bing_shop_openid")->set($key, [], 600);
        return \extend\QRcode::png($url, false, '', 4, 1);
    }

    /**
     * 验证店铺绑定情况(成功返回openid)
     */
    public function checkShopBind()
    {
        $key = Session::get("bing_shop_openid");
        $data = Cache::get($key);
        $shop_apply_model = new ShopApplyModel();
        if (!isset($data)) {
            return $shop_apply_model->error([ "is_expire" => 1 ], "二维码已过期");
        }

        if (empty($data)) {
            return $shop_apply_model->error([ "is_expire" => 0 ], "二维码还没有被扫描");
        }

        return $shop_apply_model->success($data);
    }

    /**
     * 商家添加
     */
    public function addShop()
    {
        if (request()->isAjax()) {
            //店铺信息
            $shop_data = [
                'site_name' => input('site_name', ''),//店铺名称
                'category_id' => input('category_id', 0),//分类id
                'category_name' => input('category_name', ''),//分类名称
                'group_id' => input('group_id', 0),//组id
                'group_name' => input('group_name', ''),//组名称
                'is_own' => input('is_own', 0),//是否自营
                'member_id' => input('member_id', 0),//关联会员 用于前台登陆和相关商家操作
                'year' => input('year', 0),//入驻时长 1 2 3 4 5 下拉选择
                'website_id' => $this->site_id,//选择城市分站  下拉选择
                'region_id' => input('region_id', 0), //所属城市
                'can_id' => input('can_id', 0), //所属商会
                'shop_mobile' => input('shop_mobile', ''), //主理人关联手机号，多个商家可切换

            ];
            //认证信息
            $cert_data = [
                /* 申请类型 */
                'cert_type' => input('cert_type', 1),//申请类型1.个人店铺 2.企业店铺
                /* 公司信息 只有公司类型有 */
                'company_name' => input('company_name', ''),//公司名称
                'company_province_id' => input('company_province_id', ''),//公司所在省
                'company_city_id' => input('company_city_id', ''),//公司所在市
                'company_district_id' => input('company_district_id', ''),//公司所在区/县
                'company_address' => input('company_address', ''),//公司地址
                'company_full_address' => input('company_full_address', ''),//公司完整地址
                /* 联系人手机号身份证 公司、个人类型都有 */
                'contacts_name' => input('contacts_name', ''),//联系人姓名
                'contacts_mobile' => input('contacts_mobile', ''),//联系人手机
                'contacts_card_no' => input('contacts_card_no', ''),//联系人身份证
                'contacts_card_electronic_1' => input('contacts_card_electronic_1', ''),//申请人手持身份证电子版
                'contacts_card_electronic_2' => input('contacts_card_electronic_2', ''),//申请人身份证正面
                'contacts_card_electronic_3' => input('contacts_card_electronic_3', ''),//申请人身份证反面
                /* 营业执照 税务 只有公司类型有 */
                'business_licence_number' => input('business_licence_number', ''),//统一社会信用码 input
                'business_licence_number_electronic' => input('business_licence_number_electronic', ''),//营业执照电子版
                'business_sphere' => input('business_sphere', ''),//法定经营范围 textarea
                'tax_registration_certificate' => input('tax_registration_certificate', ''),//税务登记证号
                'tax_registration_certificate_electronic' => input('tax_registration_certificate_electronic', ''),//税务登记证号电子版
                /* 对公账户信息 只有公司类型有 */
                'bank_account_name' => input('bank_account_name', ''),//银行开户名
                'bank_account_number' => input('bank_account_number', ''),//公司银行账号
                'bank_name' => input('bank_name', ''),//开户银行支行名称
                'bank_address' => input('bank_address', ''),//开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
                /* 结算信息 公司、个人类型都有 */
                'bank_type' => input('bank_type', 1),//结算账户类型  1银行卡 2 支付宝
                'settlement_bank_account_name' => input('settlement_bank_account_name', 0),//结算银行开户名
                'settlement_bank_account_number' => input('settlement_bank_account_number', 0),//结算公司银行账号
                'settlement_bank_name' => input('settlement_bank_name', 0),//结算开户银行支行名称
                'settlement_bank_address' => input('settlement_bank_address', 0),//结算开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
            ];
            //个人信息
            $user_info = [
                'username' => input('username', ''),
                'password' => data_md5(input('password', '')),
            ];

            //门店信息
            $store_name = input("site_name", '');
            $telphone = input("telphone", '');
            $store_image = input("store_image", '');
            $status = input("status", 0);
            $province_id = input("province_id", 0);
            $city_id = input("city_id", 0);
            $district_id = input("district_id", 0);
            $community_id = input("community_id", 0);
            $address = input("address", '');
            $full_address = input("full_address", '');
            $longitude = input("longitude", 0);
            $latitude = input("latitude", 0);
            $open_date = input("open_date", '');
            $store_desc = input('store_desc', '');
            $store_logo = input('store_logo', '');

            if (!$store_image) {
                return (new BaseModel())->error('', '请上传商家图片');
            }
            if (!$store_logo) {
                return (new BaseModel())->error('', '请上传商家LOGO');
            }
            if ($shop_data['is_own'] == 1) {
                $website_shop = model('shop')->getInfo(['website_id'=>$shop_data['website_id'], 'is_own'=>1]);
                if ($website_shop)  return (new BaseModel())->error('', '该城市已存在平台品牌');
            }
            if (!empty($shop_data['shop_mobile'])) {
                if (!isMobile($shop_data['shop_mobile'])) {
                    return (new BaseModel())->error('', '商家关联手机号不正确');
                }
            }

            $store_data = [
                'store_name' => $store_name,
                "telphone" => $telphone,
                "store_image" => $store_image,
                "status" => $status,
                "province_id" => $province_id,
                "city_id" => $city_id,
                "district_id" => $district_id,
                "community_id" => $community_id,
                "address" => $address,
                "full_address" => $full_address,
                "longitude" => $longitude,
                "latitude" => $latitude,
                "is_pickup" => 1,
                "open_date" => $open_date,
                'store_desc' => $store_desc,
                'store_logo' => $store_logo,
            ];
            $shop_data['logo'] = $store_image;

            $shop_model = new ShopModel();
            $this->addLog("添加商家:" . $shop_data[ 'site_name' ]);
            return $shop_model->addShop($shop_data, $cert_data, $user_info, $store_data);

        } else {
            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $shop_category_list = $shop_category_model->getCategoryList(['website_id'=>$this->site_id], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list[ 'data' ]);

            //商家主营行业
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([ [ 'is_own', '=', 0 ] ], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list[ 'data' ]);
            //商家自营等级
            $shop_own_group = $shop_group_model->getGroupList([ [ 'is_own', '=', 1 ] ], 'group_id,is_own,group_name,fee,remark', 'fee asc');
            $this->assign('shop_own_group', $shop_own_group[ 'data' ]);
            //结算账户类型
            $this->assign("support_transfer_type", $this->getTransferType());
            //城市分站
            $is_addon_city = addon_is_exit('city');
            $this->assign('is_addon_city', $is_addon_city);

            if ($is_addon_city == 1) {
                $website_model = new WebSite();
                $website_list = $website_model->getWebsiteList([], 'site_id,site_area_name');
                $this->assign('website_list', $website_list[ 'data' ]);
            }

            //查询省级数据列表
            $address_model = new AddressModel();
            $list = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);
            //获取地址key配置
            $config_model = new ConfigModel();
            $config = $config_model->getMapConfig();
            $this->assign('map_key', $config['data']['value']);
            //所属区域
            $map = [];
            $shop_region = model('shop_region')->getList($map,'*','type asc, sort asc');
            $this->assign('shop_region', $shop_region);
            //所属商会
            $shop_can = model('shop_can')->getList([], '*', 'sort asc, id asc');
            $this->assign('shop_can', $shop_can);

            return $this->fetch('shop/add_shop', [], $this->replace);
        }
    }

    /**
     * 判断结算类型
     */
    public function getTransferType()
    {

        $support_type = [];
        if (addon_is_exit("shopwithdraw")) {
            $config_model = new ShopWithdrawConfig();
            $config_result = $config_model->getConfig();
            $config = $config_result[ "data" ];
            if ($config[ "is_use" ]) {
                $support_type = explode(",", $config[ 'value' ][ "transfer_type" ]);
            } else {
                $support_type = [ "alipay", "bank" ];
            }
        } else {
            $support_type = [ "alipay", "bank" ];
        }
        return $support_type;
    }

    /**
     * 基本信息
     */
    public function basicInfo()
    {
        $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $website_id = input('website_id', 0);
            $store_image = input("store_image", '');
            $data = [
                'site_name' => input('site_name', ''),//商家名称
//                'expire_time' => input('expire_time') ? strtotime(input('expire_time')) : 0,//到期时间（0表示无限期）
                'expire_time' => 0,
                //待定
                'is_own' => input('is_own', 0),//是否自营
                'category_id' => input('category_id', 0),//店铺分类id
                'category_name' => input('category_name', ''),//店铺类别名称
                //待定
                'group_id' => input('group_id', 0),//分组id
                'group_name' => input('group_name', ''),//分组名称
                'member_id' => input('member_id', 0),//关联前台会员id
                'shop_status' => input('shop_status', ''),//店铺经营状态（0.关闭，1正常）
                'sort' => input('sort', 0),//排序号
                'logo' => $store_image,//店铺logo
                'avatar' => input('avatar', ''),//店铺头像（大图）
                'banner' => input('banner', ''),//店铺条幅
                'seo_keywords' => input('seo_keywords', ''),//店铺关键字
                'seo_description' => input('seo_description', ''),//店铺简介
                'telephone' => input('telephone', ''),//联系电话
                'is_recommend' => input('is_recommend', 0),//是否推荐 1是 0否
                'shop_qtian' => input('shop_qtian', 0),//七天退货 1是 0否
                'shop_zhping' => input('shop_zhping', 0),//正品保障 1是 0否
                'shop_erxiaoshi' => input('shop_erxiaoshi', 0),//两小时发货 1是 0否
                'shop_tuihuo' => input('shop_tuihuo', 0),//退货承诺 1是 0否
                'shop_shiyong' => input('shop_shiyong', 0),//试用中心 1是 0否
                'shop_shiti' => input('shop_shiti', 0),//实体验证 1是 0否
                'shop_xiaoxie' => input('shop_xiaoxie', 0),//消协保证 1是 0否
//                'website_id' => $website_id,//选择城市分站  下拉选择
                'username' => input('username'),
                'region_id' => input('region_id', 0), //所属城市
                'can_id' => input('can_id', 0), //所属商会
                'shop_mobile' => input('shop_mobile', ''), //商家关联手机号
            ];
            $data['shop_status'] = input("status", 0);
            //门店信息
            $store_name = input("site_name", '');
            $telphone = input("telphone", '');

            $status = input("status", 0);
            $province_id = input("province_id", 0);
            $city_id = input("city_id", 0);
            $district_id = input("district_id", 0);
            $community_id = input("community_id", 0);
            $address = input("address", '');
            $full_address = input("full_address", '');
            $longitude = input("longitude", 0);
            $latitude = input("latitude", 0);
            $open_date = input("open_date", '');
            $store_desc = input('store_desc', '');
            $store_logo = input('store_logo', '');

            if (!$store_image) {
                return (new BaseModel())->error('', '请上传商家图片');
            }
            if (!$store_logo) {
                return (new BaseModel())->error('', '请上传商家LOGO');
            }
            if ($data['is_own'] == 1) {
                $website_shop = model('shop')->getInfo([
                    ['website_id', '=', $this->site_id],
                    ['is_own', '=', 1],
                    ['site_id', '<>', $site_id]
                ]);
                if ($website_shop)  return (new BaseModel())->error('', '该城市已存在平台品牌');
            }

            $store_data = [
                'store_name' => $store_name,
                "telphone" => $telphone,
                "store_image" => $store_image,
                "status" => $status,
                "province_id" => $province_id,
                "city_id" => $city_id,
                "district_id" => $district_id,
                "community_id" => $community_id,
                "address" => $address,
                "full_address" => $full_address,
                "longitude" => $longitude,
                "latitude" => $latitude,
                "is_pickup" => 1,
                "open_date" => $open_date,
                'store_desc' => $store_desc,
                'store_logo' => $store_logo,
            ];
            return $shop_model->editShop($data, [ [ 'site_id', '=', $site_id ] ], $store_data);

            model('shop')->startTrans();
            try {
                $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ] ]);
                $shop_edit_info = $shop_model->editShop($data, [ [ 'site_id', '=', $site_id ] ]);
                if($shop_info['data']['website_id'] != $website_id){
                    $goods_model->editWebsiteId($site_id,$website_id);
                }
                model('shop')->commit();
                return $shop_edit_info;
            }catch (\Exception $e) {
                model('shop')->rollback();
                return $this->error('', $e->getMessage());
            }

        } else {
            //商家信息
            $site_id = input('site_id', 22);
            $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $site_id ] ]);
            $info = model('store')->getInfo(['site_id' => $site_id]);
            $shop_info['data']['store_image'] = $info['store_image'] ?? '';
            $shop_info['data']['store_logo'] = $info['store_logo'] ?? '';
            $this->assign('shop_info', $shop_info[ 'data' ]);

            //关联前台会员信息
            if (!empty($shop_info[ 'data' ][ 'member_id' ])) {
                $member_id = $shop_info[ 'data' ][ 'member_id' ];
                $member_model = new MemberModel();
                $member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $member_id ] ]);
                $this->assign('member_info', $member_info[ 'data' ]);
            }

            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $shop_category_list = $shop_category_model->getCategoryList(['website_id'=>$this->site_id], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list[ 'data' ]);

            //商家开店套餐（非自营）
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([ [ 'is_own', '=', 0 ] ], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list[ 'data' ]);

            //商家开店套餐（自营）
            $shop_own_group_list = $shop_group_model->getGroupList([ [ 'is_own', '=', 1 ] ], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_own_group_list', $shop_own_group_list[ 'data' ]);


            //城市分站
            $is_addon_city = addon_is_exit('city');
            $this->assign('is_addon_city', $is_addon_city);
            $website_model = new WebSite();
            $shop_website_list = $website_model->getWebsiteList([], 'site_id,site_area_name', 'site_id asc');
            $this->assign('shop_website_list', $shop_website_list[ 'data' ]);

            //四级菜单
            $this->forthMenu([ 'site_id' => $site_id ]);

            //查询省级数据列表
            $address_model = new AddressModel();
            $list = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);
            //获取地址key配置
            $config_model = new ConfigModel();
            $config = $config_model->getMapConfig();
            $this->assign('map_key', $config['data']['value']);
            //所属区域
            $map = [];
            $shop_region = model('shop_region')->getList($map,'*','type asc, sort asc');
            $this->assign('shop_region', $shop_region);
            //所属商会
            $shop_can = model('shop_can')->getList([], '*', 'sort asc, id asc');
            $this->assign('shop_can', $shop_can);

            //门店的信息
            $info = model('store')->getInfo(['site_id' => $site_id]);
            if (!$info) {
                $info['store_name'] = '';
                $info['telphone'] = '';
                $info['status'] = 1;
                $info['address'] = '';
                $info['longitude'] = '';
                $info['latitude'] = '';
                $info['is_o2o'] = '';
                $info['open_date'] = '';
                $info['province_id'] = '';
                $info['city_id'] = '';
                $info['district_id'] = '';
                $info['store_image'] = '';
            }
            $this->assign('info', $info);

            return $this->fetch('shop/basic_info', [], $this->replace);
        }
    }

    /**
     * 员工管理
     */
    public function siteStaff()
    {
        $new_site_id = input('site_id', 0);
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_keys = input('search_keys', "");
            $search_username = input('search_username', "");
            $condition = [];
            if (!empty($search_keys)) {
                $condition[] = ['ngb.username', 'like', '%' . $search_keys . '%'];
            }
            if (!empty($search_username)) {
                $condition[] = ['ngb.mobile', 'like', '%' . $search_username . '%'];
            }
            $condition[] = ['ngb.shop_id', '=', $new_site_id];

            $list = model('shop_employee')->pageList($condition, '*', 'employee_id desc', $page_index, $page_size, 'ngb', '', '');
            foreach ($list['list'] as $key => $value) {
                if (empty($value['phone'])) {
                    $list['list'][$key]['phone'] = '-';
                }


            }
            $list = (new BaseModel())->success($list);
            return $list;
        } else {
            //新增加
            $this->assign('new_site_id', $new_site_id);
            return $this->fetch('shop/site_staff', [], $this->replace);
        }
    }

    /**
     * 添加员工
     */
    public function staffAdd()
    {
        $site_id = input('site_id', '0');
        if (request()->isAjax()) {
            $name = input('name', ''); //姓名
            $image_url = input('image_url', ""); //头像
            $username = input('username', ''); //员工手机号
            $is_manager = input('is_manager', 0); //是否主理人1是
            if (!isMobile($username)) return (new BaseModel())->error('', '手机号格式不正确');

            if ($is_manager) {
                $result = model('shop_employee')->getInfo([
                    ['shop_id', '=', $site_id],
                    ['is_deleted', '=', 0],
                    ['is_manager', '=', 1]
                ]);
                if ($result) {
                    return (new BaseModel())->error('', '主理人已存在');
                }
            }

            $result = model('shop_employee')->getInfo([
                ['shop_id', '=', $site_id],
                ['is_deleted', '=', 0],
                ['mobile', '=', $username]
            ]);
            if ($result) {
                return (new BaseModel())->error('', '员工已存在');
            }

            $shop = model('shop')->getInfo(['site_id' => $site_id]);

            $res = model('shop_employee')->add([
                'shop_id' => $site_id,
                'username' => $name,
                'mobile' => $username,
                'create_time' => time(),
                'is_manager' => $is_manager,
                'website_id' => $shop['website_id'],
                'headimg' => $image_url,
            ]);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            $this->assign('site_id', $site_id);
            return $this->fetch('shop/staff_add', [], $this->replace);
        }
    }

    /**
     * 编辑员工
     */
    public function staffEdit()
    {
        $site_id = input('site_id', '0');
        if (request()->isAjax()) {
            $id = input('id', 0);
            $name = input('name', ''); //姓名
            $image_url = input('image_url', ""); //头像
            $username = input('username', ''); //员工手机号
            $is_manager = input('is_manager', 0); //是否主理人1是
            if (!isMobile($username)) return (new BaseModel())->error('', '手机号格式不正确');

            if ($is_manager) {
                $result = model('shop_employee')->getInfo([
                    ['shop_id', '=', $site_id],
                    ['is_deleted', '=', 0],
                    ['is_manager', '=', 1],
                    ['employee_id', '<>', $id],
                ]);
                if ($result) {
                    return (new BaseModel())->error('', '主理人已存在');
                }
            }

            $result = model('shop_employee')->getInfo([
                ['shop_id', '=', $site_id],
                ['is_deleted', '=', 0],
                ['mobile', '=', $username],
                ['employee_id', '<>', $id],
            ]);
            if ($result) {
                return (new BaseModel())->error('', '员工已存在');
            }

            $shop = model('shop')->getInfo(['site_id' => $site_id]);

            $res = model('shop_employee')->update([
                'shop_id' => $site_id,
                'username' => $name,
                'mobile' => $username,
                'is_manager' => $is_manager,
                'website_id' => $shop['website_id'],
                'headimg' => $image_url,
            ], ['employee_id' => $id]);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            $id = input('id', 0);
            $res = model('shop_employee')->getInfo([['employee_id', '=', $id]]);
            $site_id = $res['shop_id'];
            $this->assign("brand_info", $res);
            $this->assign('site_id', $site_id);
            return $this->fetch('shop/staff_edit', [], $this->replace);
        }
    }

    /**
     * 删除员工
     */
    public function staffDelete()
    {
        if (request()->isAjax()) {
            $id = input("id", 0);
            $condition = [
                ["employee_id", '=', $id],
            ];
            $res = model('shop_employee')->delete($condition);
            $res = (new BaseModel())->success($res);
            return $res;
        }
    }

    /**
     * 各门店带单收益与会员收益报表 (分公司视角)
     */
    public function revenueReport()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [
                ['s.website_id', '=', $this->site_id]
            ];
            if (!empty($search_text)) {
                $condition[] = ['s.site_name', 'like', '%' . $search_text . '%'];
            }

            $shop_list = \think\facade\Db::name('shop')->alias('s')
                ->leftJoin('shop_employee se', 'se.employee_id = s.zlr_employee_id')
                ->leftJoin('shop_cert sc', 'sc.site_id = s.site_id')
                ->field('s.site_id, s.site_name, s.logo, s.account, s.account_withdraw, s.zlr_employee_id, se.username as zlr_name, se.mobile as zlr_mobile, sc.business_licence_number_electronic, sc.bank_account_name, sc.bank_account_number, sc.bank_name, sc.bank_code')
                ->where($condition)
                ->paginate(['list_rows' => $page_size, 'page' => $page]);

            $items = $shop_list->items();
            foreach ($items as &$shop) {
                $site_id = $shop['site_id'];

                // 获取店铺全部员工member_id
                $emp_members = \think\facade\Db::name('shop_employee')
                    ->where('shop_id', $site_id)
                    ->where('is_deleted', 0)
                    ->column('member_id');
                if (empty($emp_members)) $emp_members = [0];

                // 1. 门店带单收益 (主理人2%提成)
                $shop['bring_income'] = floatval(\think\facade\Db::name('fenxiao_money')
                    ->where('staff_member_id', 'in', $emp_members)
                    ->sum('staff_commission'));
                $shop['bring_order_count'] = \think\facade\Db::name('fenxiao_money')
                    ->where('staff_member_id', 'in', $emp_members)
                    ->count();

                // 2. 分公司从该店带单获得的提成 (1.5%)
                $shop['city_bring_commission'] = floatval(\think\facade\Db::name('fenxiao_money')
                    ->where('city_id', $this->site_id)
                    ->where('staff_member_id', 'in', $emp_members)
                    ->sum('city_commission'));

                // 3. 门店会员总业绩收益 (主理人25%分成 + 自定义开通)
                $shop['member_income'] = floatval(\think\facade\Db::name('member_level_settlement')
                    ->where('shop_id', $site_id)
                    ->sum('shop_money'));
                $shop['member_order_count'] = \think\facade\Db::name('member_level_settlement')
                    ->where('shop_id', $site_id)
                    ->count();

                // 4. 分公司从该店会员获得的提成 (25%)
                $shop['city_member_commission'] = floatval(\think\facade\Db::name('member_level_settlement')
                    ->where('city_id', $this->site_id)
                    ->where('shop_id', $site_id)
                    ->sum('city_money'));
            }

            return (new BaseModel())->success([
                'list' => $items,
                'count' => $shop_list->total(),
                'page_count' => ceil($shop_list->total() / $page_size)
            ]);
        }

        return $this->fetch('shop/revenue_report', [], $this->replace);
    }
}