<?php
/**

 */

namespace addon\wechat\api\controller;

use addon\wechat\model\Wechat as WechatModel;
use app\api\controller\BaseApi;
use addon\wechat\model\Config as ConfigModel;
use app\model\web\WebSite;
use think\facade\Cache;

class Wechat extends BaseApi
{
	
	/**
	 * 获取openid
	 */
	public function authCodeToOpenid()
	{
		$weapp_model = new WechatModel();
		$res = $weapp_model->getAuthByCode($this->params);
		return $this->response($res);
	}
	
	/**
 * 获取网页授权code
 */
    public function authcode()
    {
        $redirect_url = $this->params['redirect_url'] ?? '';
        $weapp_model = new WechatModel();
        $res = $weapp_model->getAuthCode($redirect_url);
        return $this->response($res);
    }
	
	/**
	 * 获取jssdk配置
	 */
	public function jssdkConfig()
	{
		$url = $this->params['url'] ?? '';
		$weapp_model = new WechatModel();
		$res = $weapp_model->getJssdkConfig($url);
		return $this->response($res);
	}
	
	/**
	 * 分享设置
	 */
	public function share(){
		$data = [];
		
		$url = $this->params['url'] ?? '';
		$weapp_model = new WechatModel();
		$jssdk_config = $weapp_model->getJssdkConfig($url);
		if ($jssdk_config['code'] < 0) return $this->response($jssdk_config);
		$data['jssdk_config'] = $jssdk_config['data'];
		
		$config_model = new ConfigModel();
		$share_config = $config_model->getShareConfig();
		$share_config = $share_config['data']['value'];
		
		$website_model = new WebSite();
		$website_info = $website_model->getWebSite([ [ 'site_id', '=', 0 ] ], '*');
		
		$share_config['site_name'] = $website_info['data']['title'];
		$share_config['site_logo'] = $website_info['data']['logo'];
		$data['share_config'] = $share_config;
		
		return $this->response($this->success($data));
	}

    /**
     * 获取登录二维码
     * @return false|string
     */
    public function loginCode(){
        $key = str_replace('==','', base64_encode(uniqid('')));
        $expire_time = 600;

        $wechat_model = new WechatModel();
        $res = $wechat_model->getTempQrcode('key_' . $key, $expire_time);
        if ($res['code'] != 0) return $this->response($res);

        Cache::set('wechat_' . $key, [ 'expire_time' => (time() + $expire_time)], 3600);
        $data = $this->success([
            'key' => $key,
            'expire_time' => $expire_time,
            'qrcode' => $res['data']
        ]);
        return $this->response($data);
    }
}