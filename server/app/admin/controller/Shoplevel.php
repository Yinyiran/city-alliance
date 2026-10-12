<?php
/**

 */

namespace app\admin\controller;


/**
 * 商家等级管理 控制器
 */
class Shoplevel extends BaseAdmin
{
	/**
	 * 等级列表
	 */
	public function lists()
	{
		return $this->fetch('shoplevel/lists');
	}
	
	/**
	 * 等级添加
	 */
	public function addLevel()
	{
		return $this->fetch('shoplevel/add_level');
	}
	
	/**
	 * 等级编辑
	 */
	public function editLevel()
	{
		return $this->fetch('shoplevel/edit_level');
	}
	
	/**
	 * 等级删除
	 */
	public function deleteLevel()
	{
	
	}
}