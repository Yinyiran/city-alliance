<?php
/**

 */

namespace app\api\controller;

use app\model\member\Login as LoginModel;
use app\model\message\Message;
use app\model\member\Register as RegisterModel;
use Exception;
use think\facade\Db;
use think\facade\Cache;
use think\facade\Db;
use app\model\member\Config as ConfigModel;
use app\model\web\Config as CaptchaModel;
use app\model\web\Config;
use think\facade\Session;

class Login extends BaseApi
{
    /**
     * 账号密码登录
     *
     * 与商家端 app/shopapplet/controller/Login::accountLogin 同源实现。
     * 账号：手机号 或 员工编号(employeeID)；密码：member.password（兼容历史 md5 与 bcrypt）。
     *
     * 用途：
     *  1) 商家端存在「员工没有企业微信/个人微信可授权」的真实场景；
     *  2) 微信审核人员无法用自己的手机号完成手机号快捷登录，需要账号密码通道。
     */
    public function accountLogin()
    {
        $account = trim($this->params[ 'account' ] ?? '');
        $password = (string)($this->params[ 'password' ] ?? '');

        if ($account == '') return $this->response($this->error([], '请输入账号'));
        if ($password == '') return $this->response($this->error([], '请输入密码'));

        // 账号可匹配员工手机号 / 员工编号；会员手机号也允许直接登录
        $employee = Db::name('shop_employee')
            ->where([ [ 'is_deleted', '=', 0 ] ])
            ->where(function ($q) use ($account) {
                $q->where('mobile', $account)->whereOr('employeeID', $account);
            })
            ->find();

        $memberQuery = Db::name('member')->where([ [ 'is_delete', '=', 0 ] ]);
        if (!empty($employee)) {
            $memberQuery->where(function ($q) use ($employee, $account) {
                $q->where('member_id', intval($employee[ 'member_id' ] ?: 0))
                    ->whereOr('mobile', $account);
                if (!empty($employee[ 'mobile' ])) $q->whereOr('mobile', $employee[ 'mobile' ]);
            });
        } else {
            $memberQuery->where('mobile', $account);
        }
        $member = $memberQuery->find();

        if (empty($member)) {
            return $this->response($this->error([], '账号或密码错误'));
        }
        if ($member[ 'status' ] == 0) {
            return $this->response($this->error([], '账号已被禁用，请联系客服'));
        }
        if (!$this->verifyPassword($password, $member[ 'password' ] ?? '')) {
            // 连续失败锁定：10 分钟内 5 次
            $fail_key = 'API_PWD_FAIL_' . $member[ 'member_id' ];
            $fail_count = intval(Cache::get($fail_key)) + 1;
            Cache::set($fail_key, $fail_count, 600);
            if ($fail_count >= 5) {
                return $this->response($this->error([], '密码错误次数过多，请 10 分钟后再试'));
            }
            return $this->response($this->error([], '账号或密码错误'));
        }
        Cache::delete($fail_key);

        $member_id = $member[ 'member_id' ];
        if (!empty($employee) && (empty($employee[ 'member_id' ]) || $employee[ 'member_id' ] != $member_id)) {
            Db::name('shop_employee')->where([ [ 'employee_id', '=', $employee[ 'employee_id' ] ] ])
                ->update([ 'member_id' => $member_id ]);
        }

        Db::name('member')->where([ [ 'member_id', '=', $member_id ] ])->update([
            'login_time' => time(),
            'last_login_time' => time(),
            'login_type' => $this->params[ 'app_type' ] ?? '',
            'login_type_name' => $this->params[ 'app_type_name' ] ?? '',
            'login_ip' => request()->ip(),
        ]);
        event('MemberLogin', [ 'member_id' => $member_id ], true);

        $token = $this->createToken($member_id);
        return $this->response($this->success([
            'token' => $token,
            'member_id' => $member_id,
        ]));
    }

    /**
     * 密码校验：兼容 bcrypt 与历史 md5
     */
    private function verifyPassword(string $input, string $stored) : bool
    {
        if ($stored == '') return false;
        $info = password_get_info($stored);
        if (!empty($info[ 'algo' ])) {
            return password_verify($input, $stored);
        }
        if ($stored === md5($input)) return true;
        if ($stored === sha1(md5($input))) return true;
        if ($stored === strtoupper(md5($input))) return true;
        return false;
    }

    /**
     * 登录方法
     */
    public function login()
    {
        $config = new ConfigModel();
        $config_info = $config->getRegisterConfig();

        if (strstr($config_info[ 'data' ][ 'value' ][ 'login' ], 'username') === false) return $this->response($this->error([], "用户名登录未开启!"));

        $captcha_model = new CaptchaModel();
        $config_info = $captcha_model->getCaptchaConfig();
        $config_info = $config_info[ 'data' ][ 'value' ];
        // 校验验证码
        if(!empty($config_info['shop_reception_login']) && $config_info['shop_reception_login'] == 1){
            $captcha = new Captcha();
            $check_res = $captcha->checkCaptcha();
            if ($check_res[ 'code' ] < 0) {
                return $this->response($check_res);
            }
        }

        // 登录
        $login = new LoginModel();
        if (empty($this->params[ "password" ])) {
            return $this->response($this->error([], "密码不可为空!"));
        }
        $res = $login->login($this->params);
        //生成access_token
        if ($res[ 'code' ] >= 0) {
            $token = $this->createToken($res[ 'data' ][ 'member_id' ]);
            return $this->response($this->success([ 'token' => $token ]));
        }
        return $this->response($res);
    }

    /**
     * 第三方登录
     */
    public function auth()
    {
        $login = new LoginModel();
        $res = $login->authLogin($this->params);
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
     * 手机动态码登录
     */
    public function mobile()
    {
        $config = new ConfigModel();
        $config_info = $config->getRegisterConfig();
        if (strstr($config_info[ 'data' ][ 'value' ][ 'login' ], 'mobile') === false) return $this->response($this->error([], "动态码登录未开启!"));

        $key = $this->params[ 'key' ];
        $verify_data = Cache::get($key);
        if ($verify_data[ "mobile" ] == $this->params[ "mobile" ] && $verify_data[ "code" ] == $this->params[ "code" ]) {
            $register = new RegisterModel();
            $exist = $register->mobileExist($this->params[ "mobile" ]);

            if ($exist) {
                $login = new LoginModel();
                $res = $login->mobileLogin($this->params);
                if ($res[ 'code' ] >= 0) {
                    $token = $this->createToken($res[ 'data' ][ 'member_id' ]);
                    $res = $this->success([ 'token' => $token ]);
                }
            } else {
                $res = $register->mobileRegister($this->params);
                if ($res[ 'code' ] >= 0) {
                    $token = $this->createToken($res[ 'data' ]);
                    $res = $this->success([ 'token' => $token ]);
                }
            }

        } else {
            $res = $this->error("", "手机动态码不正确");
        }
        return $this->response($res);
    }

    /**
     * 手机号登录验证码
     * @throws Exception
     */
    public function mobileCode()
    {
        // 校验验证码
        $config_model = new Config();
        $info = $config_model->getCaptchaConfig();
        if ($info[ 'data' ][ 'value' ][ 'shop_reception_login' ] == 1) {
            $captcha = new Captcha();
            $check_res = $captcha->checkCaptcha(false);
            if ($check_res[ 'code' ] < 0) return $this->response($check_res);
        }

        $mobile = $this->params[ 'mobile' ];
        if (empty($mobile)) {
            return $this->response($this->error([], "手机号不可为空!"));
        }

        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
        $message_model = new Message();
        $res = $message_model->sendMessage(
            [ "mobile" => $mobile, "support_type" => [ 'sms' ], "code" => $code, "keywords" => "LOGIN_CODE" ]
        );
        if ($res[ "code" ] >= 0) {
            //将验证码存入缓存
            $key = 'login_mobile_code_' . md5(uniqid(null, true));
            Cache::tag("login_mobile_code")->set($key, [ 'mobile' => $mobile, 'code' => $code ], 600);
            return $this->response($this->success([ "key" => $key ]));
        } else {
            return $this->response($res);
        }
    }

    /**
     * 手机号授权登录
     */
    public function mobileAuth()
    {
        $decrypt_data = event('DecryptData', $this->params, true);
        if ($decrypt_data[ 'code' ] < 0) {
            return $this->response($decrypt_data);
        }

        $this->params[ 'mobile' ] = $decrypt_data[ 'data' ][ 'purePhoneNumber' ];

        $register = new RegisterModel();
        $exist = $register->mobileExist($this->params[ "mobile" ]);

        if ($exist) {
            $login = new LoginModel();
            $res = $login->mobileLogin($this->params);
            if ($res[ 'code' ] >= 0) {
                $member_id = $res[ 'data' ][ 'member_id' ];
                $update_data = [];
                $exist_member = \think\facade\Db::name('member')->where('member_id', $member_id)->field('member_id,site_id,source_member')->find();
                if (!empty($exist_member)) {
                    if (empty($exist_member['site_id']) && !empty($this->params['site_id'])) {
                        $update_data['site_id'] = (int)$this->params['site_id'];
                    }
                    if (empty($exist_member['source_member']) && !empty($this->params['suid']) && $this->params['suid'] != $member_id) {
                        $update_data['source_member'] = (int)$this->params['suid'];
                    }
                    if (!empty($update_data)) {
                        \think\facade\Db::name('member')->where('member_id', $member_id)->update($update_data);
                    }
                }
                $token = $this->createToken($member_id);
                $res = $this->success([ 'token' => $token ]);
            }
        } else {
            $res = $register->mobileRegister($this->params);
            if ($res[ 'code' ] >= 0) {
                $token = $this->createToken($res[ 'data' ]);
                $res = $this->success([ 'token' => $token ]);
            }
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
        $key = $this->params[ 'key' ];
        $cache = Cache::get('wechat_' . $key);

        if (!empty($cache)) {
            if (isset($cache[ 'openid' ]) && !empty($cache[ 'openid' ])) {
                $login = new LoginModel();
                $data = [
                    'wx_openid' => $cache[ 'openid' ]
                ];
                $is_exits = $login->openidIsExits($data);
                if ($is_exits[ 'data' ]) {
                    // 存在即登录
                    $res = $login->authLogin($data);
                    //生成access_token
                    if ($res[ 'code' ] >= 0) {
                        $token = $this->createToken($res[ 'data' ][ 'member_id' ]);
                        return $this->response($this->success([ 'token' => $token ]));
                    }
                    return $this->response($res);
                } else {

                    // 将openid存入session
                    Session::set("auth_info",[
                        'wx_openid'=>$cache['openid'],
                        'nickname'=>$cache['nickname'],
                        'headimg'=>$cache['headimgurl']
                    ]);

                    Cache::set('wechat_' . $key, null);
                    return $this->response($this->success());
                }
            } elseif (time() > $cache[ 'expire_time' ]) {
                Cache::set('wechat_' . $key, null);
                return $this->response($this->error('', '已失效'));
            } else {
                return $this->response($this->error('', 'no login'));
            }
        } else {
            return $this->response($this->error('', '已失效'));
        }
    }

    /**
     * 微信公众号登录 新增2021.06.30
     * captcha_id 验证码id
     * captcha_code 验证码
     * mobile 手机号码
     * code 手机验证码
     */
    public function wechatLogin()
    {
        //校验验证码
        $captcha   = new Captcha();
        $check_res = $captcha->checkCaptcha();
        if ($check_res['code'] < 0) return $this->response($check_res);


        $auth_info = Session::get("auth_info");

        if (!empty($auth_info)) {
            $this->params = array_merge($this->params, $auth_info);
        }

        $key         = $this->params['key'];
        $verify_data = Cache::get($key);
        //判断手机验证码
        if ($verify_data["mobile"] == $this->params["mobile"] && $verify_data["code"] == $this->params["code"]) {
            $register = new RegisterModel();
            $exist    = $register->mobileExist($this->params["mobile"]);

            if ($exist) {
                //手机号码存在绑定wx_openid并登录

                //绑定openid 如果该手机号有openid直接替换
                $member_id   = $register->getMemberId($this->params["mobile"]);
                $res = $register->wxopenidBind(['wx_openid'=>$this->params['wx_openid'],'member_id'=>$member_id]);

                if ($res['code'] >= 0){
                    $login = new LoginModel();
                    $res   = $login->mobileLogin($this->params);
                    if ($res['code'] >= 0) {
                        $token = $this->createToken($res['data']['member_id']);
                        $res   = $this->success(['token' => $token]);
                    }
                }

            } else {
                //获取存放的缓存推荐人id
                $source_member = Session::get('source_member') ?? 0;
                if ($source_member > 0) {
                    $this->params[ 'source_member' ] = $source_member;
                }
                //手机号码不存在注册账号
                $res = $register->mobileRegister($this->params);
                if ($res[ 'code' ] >= 0) {
                    $token = $this->createToken($res[ 'data' ]);
                    $res = $this->success([ 'token' => $token ]);
                }
            }
        } else {
            $res = $this->error("", "手机动态码不正确");
        }
        return $this->response($res);

    }


    /**
     * 获取第三方首次扫码登录绑定/注册手机号码验证码      手机号码存不存在都可以发送 新增2021.06.30
     * captcha_id 验证码id
     * captcha_code 验证码
     * mobile 手机号码
     */
    public function getMobileCode()
    {
        // 校验验证码 start
        $captcha   = new Captcha();
        $check_res = $captcha->checkCaptcha(false);
        if ($check_res['code'] < 0) return $this->response($check_res);
        // 校验验证码 end

        $mobile = $this->params['mobile'];
        if (empty($mobile)) return $this->response($this->error([], "手机号不可为空!"));


        $register = new RegisterModel();
        $exist    = $register->mobileExist($this->params["mobile"]);

        //判断该手机号码是否已绑定wx_openid
//        $opneid_exist    = $register->openidExist($this->params["mobile"]);
//        if ($opneid_exist) return $this->response($this->error([], "该手机号已绑定其他微信公众号!"));

        if ($exist){
            $keywords = 'LOGIN_CODE';
        }else{
            $keywords = 'REGISTER_CODE';
        }

        $code          = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
        $message_model = new Message();
        $res           = $message_model->sendMessage(["mobile" => $mobile, "support_type" => ['sms'], "code" => $code, "keywords" => $keywords]);
        if ($res["code"] >= 0) {
//            if ($res["code"]) {
            //将验证码存入缓存
            $key = 'login_mobile_code_' . md5(uniqid(null, true));
            Cache::tag("login_mobile_code")->set($key, ['mobile' => $mobile, 'code' => $code], 600);
            return $this->response($this->success(["key" => $key]));
//            return $this->response($this->success(["key" => $key,"code"=>$code]));
        } else {
            return $this->response($res);
        }
    }

}
