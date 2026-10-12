<?php


namespace app\admin\controller;

/**
 * 协议内容
 * Class Agreement
 * @package app\admin\controller
 */
class Agreement extends BaseAdmin
{


    /**
     * 隐私政策
     */
    public function privacy()
    {
        $config_model = new \app\model\shop\Config();
        if (request()->isAjax()) {
            $title = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setPrivacy($title, $content);
        }
        $shop_apply_agreement = $config_model->getPrivacy();
        $this->assign('shop_apply_agreement', $shop_apply_agreement);

        return $this->fetch('agreement/privacy');
    }
    /**
     * 隐私政策
     */
    public function privacyqy()
    {
        $config_model = new \app\model\shop\Config();
        if (request()->isAjax()) {
            $title = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setPrivacyqy($title, $content);
        }
        $shop_apply_agreement = $config_model->getPrivacyqy();
        $this->assign('shop_apply_agreement', $shop_apply_agreement);

        return $this->fetch('agreement/privacyqy');
    }

    /**
     * 用户须知
     */
    public function vipqy()
    {
        $config_model = new \app\model\shop\Config();
        if (request()->isAjax()) {
            $title = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setVipqy($title, $content);

        }
        $shop_apply_agreement = $config_model->getVipqy();
        $this->assign('shop_apply_agreement', $shop_apply_agreement);

        return $this->fetch('agreement/vipqy');
    }
    /**
     * 用户须知
     */
    public function vip()
    {
        $config_model = new \app\model\shop\Config();
        if (request()->isAjax()) {
            $title = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setVip($title, $content);
        }
        $shop_apply_agreement = $config_model->getVip();
        $this->assign('shop_apply_agreement', $shop_apply_agreement);

        return $this->fetch('agreement/vip');
    }

    /**
     * 用户推广规则
     */
    public function memberfenxiao()
    {
        $config_model = new \app\model\shop\Config();
        if (request()->isAjax()) {
            $title = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setMemberfenxiao($title, $content);
        }
        $shop_apply_agreement = $config_model->getMemberfenxiao();
        $this->assign('shop_apply_agreement', $shop_apply_agreement);

        return $this->fetch('agreement/memberfenxiao');
    }

    /**
     * 门店入驻及运营协议
     */
    public function shop()
    {
        $config_model = new \app\model\shop\Config();
        if (request()->isAjax()) {
            $title = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setShop($title, $content);
        }
        $shop_apply_agreement = $config_model->getShop();
        $this->assign('shop_apply_agreement', $shop_apply_agreement);

        return $this->fetch('agreement/shop');
    }
}