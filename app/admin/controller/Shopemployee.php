<?php
/**

 */

namespace app\admin\controller;

use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use think\facade\Db;

/**
 * 商家员工列表
 */
class Shopemployee extends BaseAdmin
{
	protected $ShopemployeeModel;

	public function _initialize()
    {
		parent::_initialize();
        $this->ShopemployeeModel = new ShopemployeeModel();
    }
	/**
	 * 员工信息列表
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			
			$page = input('page', 1);
			$site_id = input('site_id', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_username = input('username', '');
			
			$search_mobile = input('mobile', '');
			$search_text=[
				"username"=>$search_username,
				"mobile"=>$search_mobile,
			];
			$employeemodel=new ShopEmployeeModel();
			$result=$employeemodel->getlistsbyshop($site_id,$page,$page_size,$search_text);
			return $result;
			
		} else {
			$search_username = input('username', '');
			$site_id=input("site_id",0);
			$this->assign("site_id",$site_id);
			return $this->fetch('shopemployee/employeelist');
		}
	}

	/**
	 * 员工信息编辑
	 */
	public function edits()
	{
		if (request()->isAjax()) {
			$employeemodel=new ShopEmployeeModel();
			$params=request()->param();
			$res=$employeemodel->eidtbyadmin($params);
			return $res;
			
		} else {
			$employee_id=input("id",0);
			$info=Db::name("shop_employee")->where("employee_id",$employee_id)->find();
			$this->assign("info",$info);
			return $this->fetch('shopemployee/edits');
		}
	}

	/**
	 * 员工信息编辑
	 */
	public function adds()
	{
		if (request()->isAjax()) {
			$employeemodel=new ShopEmployeeModel();
			$params=request()->param();
			if(empty($params["is_manager"])){
				$params["is_manager"]=0;
			}
			$res=$employeemodel->addsbyadmin($params);
			return $res;
			
		} else {
			$site_id=input("site_id",0);
			$this->assign("site_id",$site_id);
			return $this->fetch('shopemployee/adds');
		}
	}
	
	/**
	 * 员工信息编辑
	 */
	public function dels()
	{
		if (request()->isAjax()) {
			$employeemodel=new ShopEmployeeModel();
			$params=request()->param();
			$res=$employeemodel->dels($params["id"]);
			return $res;
			
		}	
			
		
	}
	
}