<?php
/**

 */

namespace app\shopapplet\controller;

use app\model\shopapplet\Login as LoginModel;
use app\model\message\Message;
use Exception;
use think\facade\Cache;
use app\model\shopapplet\Config as ConfigModel;
use app\model\web\Config as CaptchaModel;
use app\model\web\Config;
use app\model\shopapplet\ShopEmployee as EmployeeModel;
use think\facade\Session;
use app\model\shopapplet\Register as RegisterModel;
use think\facade\Db;
use addon\weappqy\model\Config as WeappqyConfigModel;


class Login extends BaseApi
{
    
    
    /**
     * 第三方登录
     */
    public function auth()
    {
        $login = new LoginModel(); 
        $params=$this->params;
        $params["wxqy_openid"]=$params["weapp_openid"];
        if(empty($params["wxqy_openid"])){
            return $this->response($this->error("openid不能为空"));
        }
        $res = $login->authLogin($params);
        //生成access_token
        if ($res[ 'code' ] >= 0) {
            $token = $this->createToken($res[ 'data' ][ 'member_id' ]);
            return $this->response($this->success([ 'token' => $token ]));
        }
        return $this->response($res);
    }

    /**
     * 检测openid是否存在
     */
    public function openidIsExits()
    {
        $login = new LoginModel();
        $res = $login->openidIsExits($this->params);
        return $this->response($res);
    }

    
    /**
     * 手机号授权登录
     */
    public function mobileAuth()
    {
        $decrypt_data = event('DecryptData', $this->params, true);
        $this->params["wxqy_openid"]=$this->params["weapp_openid"];
        $this->params["weapp_openid"]='';
        if ($decrypt_data[ 'code' ] < 0) {
            return $this->response($decrypt_data);
        }
        $this->params[ 'mobile' ] = $decrypt_data[ 'data' ][ 'purePhoneNumber' ];
        $employeemodel = new EmployeeModel();
        $exist = $employeemodel->mobileexits($this->params[ "mobile" ]);
        if ($exist) {
            $register = new RegisterModel();
            $exist    = $register->mobileExist($this->params["mobile"]);
            if ($exist) {
                $login = new LoginModel();
                $res   = $login->mobileLogin($this->params);
                if ($res['code'] >= 0) {
                    $token = $this->createToken($res['data']['member_id']);
                    $res   = $this->success(['token' => $token]);
                }
            } else {
                $res = $register->mobileRegister($this->params);
                if ($res['code'] >= 0) {
                    $token = $this->createToken($res['data']);
                    $res   = $this->success(['token' => $token]);
                }
            }
        } else{
            
            $res = $this->error(-1,"员工不存在，不能登录");
        }
         return $this->response($res);
    }

    

    /**
     * 验证token有效性
     */
    public function verifyToken()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        return $this->response($this->success());
    }
    /**
     * 检测登录
     * @return false|string
     */
    public function checkLogin()
    {
        $code = $this->params['code'];
        if (!$code) return $this->response($this->error([], "code不存在!"));
        $result = $this->auth_code2Session($code);
        if (!array_key_exists('openid', $result)) {
            return $this->response($this->error([], '授权码code无效'));
        }
        $memberModel = model('member')->getInfo(['wxqy_openid' => $result['openid']]);
        $result = false;
        $nickname = $avatar = $mobile = null;
        $hasmobile=false;
        if ($memberModel) {
            $result = true;
            $nickname = $memberModel['nickname'];
            $avatar = $memberModel['headimg'];
            $mobile=$memberModel["mobile"];
            if(!empty($mobile)){
                $hasmobile=true;
            }else{
                $hasmobile=false;
            }
        }
        return $this->response($this->success(['result' => $result, 'nickname' => $nickname, 'avatar' => $avatar,"mobile"=>$mobile,"hasmobile"=>$hasmobile]));

      
    }

    /**
     * 获取微信配置
     */
    public function getWeAppConfig($site_id = 0)
    {
        //微信小程序配置
        $weapp_config_model = new WeappqyConfigModel();
        $weapp_config       = $weapp_config_model->getWeappConfig($site_id);
        $weapp_config       = $weapp_config["data"]["value"];
        return [
            'app_id' => $weapp_config["appid"] ?? '',
            'secret' => $weapp_config["appsecret"] ?? '',
        ];
    }

    /**
     * 获取openid
     */
    public function auth_code2Session($js_code)
    {
        $config = $this->getWeAppConfig();
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $config['app_id'] . '&secret=' . $config['secret'] . '&js_code=' . $js_code . '&grant_type=authorization_code';
        return httpRequest($url, 'GET');
    }
    

}
