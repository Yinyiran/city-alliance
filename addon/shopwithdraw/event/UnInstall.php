<?php
/**

 */


namespace addon\shopwithdraw\event;

/**
 * 应用卸载
 */
class UnInstall
{
	/**
	 * 执行卸载
	 */
	public function handle()
	{
	    return error('-1', "系统插件不允许删除");
	}
}