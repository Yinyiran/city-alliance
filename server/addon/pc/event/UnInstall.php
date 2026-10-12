<?php
/**

 */


namespace addon\pc\event;

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
	    return error("系统插件不能删除");
	}
}