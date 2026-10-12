<?php
/**

 */

namespace app\admin\controller;


use app\model\system\Promotion as PromotionModel;

/**
 * 营销管理 控制器
 */
class Promotion extends BaseAdmin
{

	/**
	 * 营销中心
	 */
	public function config()
	{
		$promotion_model = new PromotionModel();
		$promotions = $promotion_model->getPromotions();
		foreach ($promotions['admin'] as $key => $value) {
		    if (!in_array($value['name'], ['fenxiao'])) {
		        unset($promotions['admin'][$key]);
            }
        }
		$this->assign("promotion", $promotions['admin']);
		return $this->fetch('promotion/config');
	}
	
	/**
	 * 店铺营销
	 */
	public function shop()
	{
		$promotion_model = new PromotionModel();
		$promotions = $promotion_model->getPromotions();
		$this->assign("promotion", $promotions['admin']);
		return $this->fetch('promotion/shop');
	}
	
	/**
	 * 会员营销
	 */
	public function member()
	{
		$promotion_model = new PromotionModel();
		$promotions = $promotion_model->getPromotions();
		$this->assign("promotion", $promotions['admin']);
		return $this->fetch('promotion/member');
	}
	
	/**
	 * 平台营销
	 */
	public function platform()
	{
		$promotion_model = new PromotionModel();
		$promotions = $promotion_model->getPromotions();
		$this->assign("promotion", $promotions['admin']);
		return $this->fetch('promotion/platform');
	}
	
	/**
	 * 应用工具
	 */
	public function tool()
	{
		$promotion_model = new PromotionModel();
		$promotions = $promotion_model->getPromotions();
		$this->assign("promotion", $promotions['admin']);
		return $this->fetch('promotion/tool');
	}
	
}