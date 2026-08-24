<?php

namespace app\admin\controller;
use app\model\web\WebSite;

use app\model\shop\ShopCategory as ShopCategoryModel;

/**
 * 商家主营行业管理 控制器
 */
class Shopcategory extends BaseAdmin
{
	/**
	 * 主营行业列表
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			
			$condition = [];
			if(!empty($search_text)) $condition[] = [ 'category_name', 'like', '%' . $search_text . '%' ];
			$order = 'category_id asc';
			$field = '*';
			
			$shop_category_model = new ShopCategoryModel();
			$list = $shop_category_model->getCategoryPageList($condition, $page, $page_size, $order, $field);
			foreach ($list['data']['list'] as $key => $value) {
			    //所属城市
                $list['data']['list'][$key]['website_title'] = model('website')->getInfo(['site_id'=>$value['website_id']])['title'];
            }
			return $list;
		} else {
			return $this->fetch('shopcategory/lists');
		}
	}
	
	/**
	 * 主营行业添加
	 */
	public function addCategory()
	{
		if (request()->isAjax()) {
			$data = [
				'category_name' => input('category_name', ''),//行业名称
				'baozheng_money' => input('baozheng_money', 0.00),//保证金金额
				'sort' => input('sort', 0),//排序
                'website_id' => input('website_id', 0),
			];
			$shop_category_model = new ShopCategoryModel();
			$this->addLog("添加店铺主营行业:" . $data['category_name'] . ",保证金:" . $data["baozheng_money"]);
			return $shop_category_model->addCategory($data);
		} else {
            $website_model = new WebSite();
            $website_list = $website_model->getWebsiteList([ ['site_id', '>', 0] ], 'site_id,site_area_name');
            $this->assign('website_list', $website_list[ 'data' ]);
			return $this->fetch('shopcategory/add_category');
		}
	}
	
	/**
	 * 主营行业编辑
	 */
	public function editCategory()
	{
		$shop_category_model = new ShopCategoryModel();
		if (request()->isAjax()) {
			$data = [
				'category_name' => input('category_name', ''),
				'baozheng_money' => input('baozheng_money', 0.00),
				'sort' => input('sort', 0.00),
				'category_id' => input('category_id', 0),//直接在数据中传参
                'website_id' => input('website_id', 0),
			];
			$this->addLog("编辑店铺主营行业:" . $data['category_name'] . ",保证金:" . $data["baozheng_money"]);
			return $shop_category_model->editCategory($data);
		} else {
			//商家主营行业信息
			$category_id = input('category_id', 0);
			$category_info = $shop_category_model->getCategoryInfo([ [ 'category_id', '=', $category_id ] ]);
			$this->assign('category_info', $category_info);

            $website_model = new WebSite();
            $shop_website_list = $website_model->getWebsiteList([ ['site_id', '>', 0] ], 'site_id,site_area_name', 'site_id asc');
            $this->assign('shop_website_list', $shop_website_list[ 'data' ]);
			
			return $this->fetch('shopcategory/edit_category');
		}
	}
	
	/**
	 * 主营行业删除
	 */
	public function deleteCategory()
	{
		$category_ids = input('category_ids', '');
		$shop_category_model = new ShopCategoryModel();
		$this->addLog("删除店铺主营行业id:" . $category_ids);
		return $shop_category_model->deleteCategory([ [ 'category_id', 'in', $category_ids ] ]);
	}
}