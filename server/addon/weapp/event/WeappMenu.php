<?php
/**

 */


namespace addon\weapp\event;

/**
 * 小程序菜单
 */
class WeappMenu
{

    /**
     * 小程序菜单
     * 
     * @return multitype:number unknown
     */
	public function handle()
	{
        $data = [
            'title' => '订阅消息',
            'description' => '给用户提供更好的服务闭环体验',
            'url' => 'weapp://admin/message/config',
            'icon' => 'addon/weapp/admin/view/public/img/message.png'
        ];
	    return $data;
	}
}