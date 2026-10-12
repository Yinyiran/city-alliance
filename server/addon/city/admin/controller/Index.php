<?php
/**

 */

namespace addon\city\admin\controller;

use app\Controller;

/**
 * 跳转页
 */
class Index extends Controller
{
    /**
     * 首页跳转
     */
    public function index()
    {
        $this->redirect(addon_url("city://city/index/index"));
    }
}