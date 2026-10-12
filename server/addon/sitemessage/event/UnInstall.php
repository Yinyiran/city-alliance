<?php
/**

 */


namespace addon\sitemessage\event;

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
        try {
            //return success();
        } catch (\Exception $e) {
            return error('-1', $e->getMessage());
        }
    }
}