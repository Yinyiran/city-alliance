<?php
/**

 */


namespace addon\sitemessage\event;


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
        try {

            return success();
        } catch (\Exception $e) {
            return error('', $e->getMessage());
        }
    }
}