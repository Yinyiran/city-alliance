<?php
/**

 */

namespace addon\wechat\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 微信公众号配置
 */
class Menu extends BaseModel
{

    /******************************************************************** 微信公众号菜单配置 start ****************************************************************************/
    /**
     * 设置微信公众号配置
     * @return multitype:string mixed
     */
    public function setWechatMenuConfig($data)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '微信公众号设置', 1, [['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'WECHAT_MENU_CONFIG']]);
        return $res;
    }

    /**
     * 微信公众号菜单配置
     */
    public function getWechatMenuConfig(){
        $config = new ConfigModel();
        $res = $config->getConfig([['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'WECHAT_MENU_CONFIG']]);
        return $res;
    }
    /******************************************************************** 微信公众号菜单配置 end ****************************************************************************/
}