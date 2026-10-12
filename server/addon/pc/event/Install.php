<?php
/**

 */


namespace addon\pc\event;

use addon\pc\model\Pc;

/**
 * 应用安装
 */
class Install
{
	/**
	 * 执行安装
	 */
	public function handle()
	{
        try{
            $pc_model = new Pc();
            $pc_model->downloadCsDefault();
            return success();
        }catch (\Exception $e)
        {
            return error('', $e->getMessage());
        }
	}
}