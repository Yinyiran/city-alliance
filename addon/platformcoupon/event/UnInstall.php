<?php
/**

 */


namespace addon\platformcoupon\event;

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
        try{
            return error('-1', "系统插件不允许删除");
            //execute_sql('addon/coupon/data/uninstall.sql');
            //return success();
        }catch (\Exception $e)
        {
            return error('-1', $e->getMessage());
        }
    }
}