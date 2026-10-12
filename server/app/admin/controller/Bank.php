<?php
/**

 */

namespace app\admin\controller;

use app\Controller;
use app\model\pay\Huifu;
use app\model\system\Address as AddressModel;

/**
 * 地址查询 控制器 不控权限 不入菜单
 */
class Bank extends Controller
{
    public function getBank()
    {
        $huifu_model = new Huifu();
        $bank_list = $huifu_model->getBankCode();
        return $bank_list;
    }


}