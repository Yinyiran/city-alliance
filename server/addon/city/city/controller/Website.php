<?php
/**

 */

namespace addon\city\city\controller;

use app\model\web\WebSite as WebsiteModel;

/**
 * 跳转页
 */
class Website extends BaseCity
{

    /**
     * 编辑城市分站
     */
    public function config()
    {
        $website_model = new WebsiteModel();
        if (request()->isAjax()) {

            $data = [
                'title' => input('title', ''),
                'logo' => input('logo', ''),
                'desc' => input('desc', ''),
                'keywords' => input('keywords', ''),
                'web_address' => input('web_address', ''),
                'web_qrcode' => input('web_qrcode', ''),
                'web_email' => input('web_email', ''),
                'web_phone' => input('web_phone', ''),
                'web_qq' => input('web_qq', ''),
                'web_weixin' => input('web_weixin', ''),
                'wap_domain' => input('wap_domain', ''),
                'douyin_image' => input('douyin_image', ''),
                'yao_point' => input('yao_point', 0),
                'shop_deposit_amount' => input('shop_deposit_amount', 0),
            ];

            $condition[] = [ 'site_id', '=', $this->site_id ];
            $res = $website_model->setWebSite($data, $condition);
            return $res;
        } else {
            return $this->fetch('website/config', [], $this->replace);
        }
    }

}