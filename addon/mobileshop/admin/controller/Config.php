<?php
/**

 */

namespace addon\mobileshop\admin\controller;

use addon\mobileshop\model\Config as ConfigModel;
use app\admin\controller\BaseAdmin;
use app\model\system\Upgrade;

/**
 * 配置
 */
class Config extends BaseAdmin
{

    private $config_model;

    public function __construct()
    {
        parent::__construct();
        $this->config_model = new ConfigModel();
    }

    /**
     * 网站部署
     * @return mixed
     */
    public function deploy()
    {
        $refresh_time = 0;
        $path = 'mobileshop/refresh.log';
        if (file_exists($path)) {
            $refresh_time = file_get_contents($path);
        }
        $this->assign('refresh_time', $refresh_time);

//        $root_url = str_replace('/index.php', '', ROOT_URL);
//        $this->assign("root_url", $root_url);

        $config_model = new ConfigModel();
        $config = $config_model->getMShopDomainName($this->site_id);
        $config = $config[ 'data' ];
        $this->assign('config', $config[ 'value' ]);

        // 检测授权
        $upgrade_model = new Upgrade();
        $auth_info = $upgrade_model->authInfo();
        $this->assign('is_auth', ( $auth_info[ 'code' ] == 0 ));

        return $this->fetch('config/deploy');
    }

    /**
     * 设置移动版商家端域名配置
     * @return array
     */
    public function setMShopDomainName()
    {
        $config_model = new ConfigModel();
        $domain_name = input("domain_name", "");
        $result = $config_model->setMShopDomainName([
            'domain_name_mobileshop' => $domain_name
        ]);
        return $result;
    }

    /**
     * 默认部署：无需下载，一键刷新，API接口请求地址为当前域名，编译代码存放到mobileshop文件夹中
     */
    public function downloadCsDefault()
    {
        return $this->config_model->downloadCsDefault();
    }

    /**
     * 独立部署：下载编译代码包后，放到网站根目录下运行
     */
    public function downloadCsIndep()
    {
        $domain = input("domain", ROOT_URL);
        $res = $this->config_model->downloadCsIndep($domain);
        echo $res[ 'message' ];
    }

    /**
     * 源码下载：下载uni-app代码包，可进行二次开发
     */
    public function downloadOs()
    {
        $res = $this->config_model->downloadOs();
        echo $res[ 'message' ];
    }

    /**
     * 公众号配置
     */
    public function weapp()
    {
        $weapp_model = new ConfigModel();
        if (request()->isAjax()) {
            $weapp_name = input('weapp_name', '');
            $weapp_original = input('weapp_original', '');
            $appid = input('appid', '');
            $appsecret = input('appsecret', '');
            $token = input('token', 'TOKEN');
            $encodingaeskey = input('encodingaeskey', '');
            $is_use = input('is_use', 0);
            $qrcode = input('qrcode', '');
            $data = array (
                "appid" => $appid,
                "appsecret" => $appsecret,
                "token" => $token,
                "weapp_name" => $weapp_name,
                "weapp_original" => $weapp_original,
                "encodingaeskey" => $encodingaeskey,
                'qrcode' => $qrcode
            );
            $res = $weapp_model->setWeAppConfig($data, $is_use);
            return $res;
        } else {
            $weapp_config_result = $weapp_model->getWeAppConfig();
            $config_info = $weapp_config_result[ 'data' ][ "value" ];
            $this->assign("config_info", $config_info);
            // 获取当前域名
            $url = __ROOT__;
            // 去除链接的http://头部
            $url_top = str_replace("https://", "", $url);
            $url_top = str_replace("http://", "", $url_top);
            // 去除链接的尾部/?s=
            $url_top = str_replace('/?s=', '', $url_top);
            $this->assign("url", $url_top);
            return $this->fetch('config/weapp');
        }

    }
}