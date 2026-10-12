<?php
/**

 */

namespace app\shop\controller;

use app\model\system\Address as AddressModel;
use app\Controller;

/**
 * 市场
 * Class Order
 * @package app\shop\controller
 */
class Market extends Controller
{
    /**
     * 市场选货
     */
    public function index()
    {
        $address_model = new AddressModel();
        $level = input('level', 1);
        $pid = input("pid", 0);
        $condition = array (
            "level" => $level,
            "pid" => $pid
        );
        $list = $address_model->getAreaList($condition, "id, pid, name, level", "id asc");
        return $list;
    }


}