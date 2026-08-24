<?php
/**

 */

namespace app\shopapi\controller;

use app\model\express\ExpressCompany;
use app\model\express\ExpressTemplate as ExpressTemplateModel;

/**
 * 配送
 * Class Express
 * @package app\shop\controller
 */
class Express extends BaseApi
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
     * 获取运费模板
     * @return false|string
     */
    public function getExpressTemplateList()
    {
        $express_template_model = new ExpressTemplateModel();
        $express_template_list = $express_template_model->getExpressTemplateList([ [ 'site_id', "=", $this->site_id ] ], 'template_id,template_name', 'is_default desc');
        return $this->response($express_template_list);
    }

    /**
     * 物流公司
     * @return mixed
     */
    public function expressCompany()
    {
        $express_company_model = new ExpressCompany();

        //平台公用的物流公司
        $company_list_result = $express_company_model->getExpressCompanyList([]);
        $company_list = $company_list_result[ "data" ];
        $company_list = array_column($company_list, null, "company_id");

        //店铺物流公司
        $company_shop_list_result = $express_company_model->getExpressCompanyShopList([ [ "site_id", "=", $this->site_id ] ]);
        $temp_company_shop_list = $company_shop_list_result[ "data" ];
        $company_shop_list = [];

        //删除已有的公用物流公司
        if (!empty($temp_company_shop_list)) {
            foreach ($temp_company_shop_list as $k => $v) {
                //店铺物流公司存在的话,删除掉公共物流公司中对应的数据
                if (!empty($company_list[ $v[ "company_id" ] ])) {
                    $temp_item = $company_list[ $v[ "company_id" ] ];
                    $temp_item[ "id" ] = $v[ "id" ];
                    $company_shop_list[] = $temp_item;
                    unset($company_list[ $v[ "company_id" ] ]);
                }
            }
        }

        return $this->response($this->success($company_shop_list));
    }

}