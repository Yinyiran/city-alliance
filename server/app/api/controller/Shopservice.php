<?php
/**

 */

namespace app\api\controller;

use app\model\shop\ShopService as ShopServiceModel;

/**
 * 店铺服务
 * Class Shopcategory
 * @package app\api\controller
 */
class Shopservice extends BaseApi
{

	public function lists()
	{
		$shop_service_model = new ShopServiceModel();
		$list = $shop_service_model->getServiceNameList();
		foreach ($list as $k => $v) {
			unset($list[ $k ][ 'icon' ]);
		}
		return $this->response($this->success($list));
	}

}