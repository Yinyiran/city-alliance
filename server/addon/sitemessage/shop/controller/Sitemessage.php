<?php
/**

 */

namespace addon\sitemessage\shop\controller;

use app\shop\controller\BaseShop;

/**
 * 站内信
 * @author Administrator
 *
 */
class Sitemessage extends BaseShop
{
    /**
     * 添加活动
     */
    public function lists()
    {

        if (request()->isAjax()) {

        } else {

            return $this->fetch("sitemessage/lists");
        }
    }


}