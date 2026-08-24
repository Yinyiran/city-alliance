<?php
/**

 */

namespace addon\servicer\servicer\controller;

use app\Controller;

/**
 * 客服端基础控制器
 * Class Base
 * @package addon\servicer\servicer\controller
 */
class Base extends Controller
{
    /**
     * 模块
     * @var string
     */
    protected $app_module = 'servicer';

    /**
     * 当前解析的路径
     * @var
     */
    protected $url;

    /**
     * 当前访问的插件名
     * @var string
     */
    protected $addon = '';

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();

        $this->url   = request()->parseUrl();
        $this->addon = request()->addon() ?: '';
    }


    /**
     * 加载模板输出
     * @param string $template 模板文件名
     * @param array $vars 模板输出变量
     * @param array $config 模板参数
     * @return mixed
     */
    public function fetch($template = '', $vars = [], $config = [])
    {
        $config = array_merge([
            'SERVICER_CSS' => __ROOT__ . '/'. ADDON_PATH . $this->addon .'/' . $this->app_module . '/view/public/css',
            'SERVICER_JS'  => __ROOT__ . '/'. ADDON_PATH . $this->addon .'/' . $this->app_module . '/view/public/js',
            'SERVICER_IMG' => __ROOT__ . '/'. ADDON_PATH . $this->addon .'/' . $this->app_module . '/view/public/img',
        ], $config);
        return parent::fetch($template, $vars, $config);
    }
}