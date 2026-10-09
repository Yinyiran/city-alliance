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
                    $member_id = $res['data']['member_id'];
                    EmployeeModel::syncMemberPartnerLevel($member_id, $this->params['mobile']);
                    $token = $this->createToken($member_id);
                    $res   = $this->success(['token' => $token]);
                }
            } else {
                $res = $register->mobileRegister($this->params);
                if ($res['code'] >= 0) {
                    $member_id = $res['data'];
                    EmployeeModel::syncMemberPartnerLevel($member_id, $this->params['mobile']);
                    $token = $this->createToken($member_id);
                    $res   = $this->success(['token' => $token]);
                }
            }
        } else{
            
            $res = $this->error(-1,"员工不存在，不能登录");
        }
         return $this->response($res);
    }

    

    /**
     * 账号密码登录
     *
     * 商家端存在「员工没有企业微信/个人微信可授权」的真实场景，且微信审核人员
     * 无法用自己的手机号完成手机号快捷登录（mobileAuth 仅允许 shop_employee
     * 中已登记的手机号），因此必须提供账号密码登录通道。
     *
     * 账号：手机号 或 员工编号(employeeID)
     * 密码：member.password（兼容历史 md5 与 bcrypt）
     */
    public function accountLogin()
    {
        $account = trim($this->params[ 'account' ] ?? '');
        $password = (string)($this->params[ 'password' ] ?? '');

        if ($account == '') return $this->response($this->error([], '请输入账号'));
        if ($password == '') return $this->response($this->error([], '请输入密码'));

        // 解析账号 -> 员工记录
        // 注意：历史数据 employee_status 均为 0，此处不做该字段过滤，仅按 is_deleted 判断
        $employee = Db::name('shop_employee')
            ->where([ [ 'is_deleted', '=', 0 ] ])
            ->where(function ($q) use ($account) {
                $q->where('mobile', $account)->whereOr('employeeID', $account);
            })
            ->find();

        if (empty($employee)) {
            return $this->response($this->error([], '账号或密码错误'));
        }

        $member = Db::name('member')
            ->where([ [ 'is_delete', '=', 0 ] ])
            ->where(function ($q) use ($employee) {
                $q->where('member_id', intval($employee[ 'member_id' ] ?: 0));
                if (!empty($employee[ 'mobile' ])) $q->whereOr('mobile', $employee[ 'mobile' ]);
            })
            ->find();

        if (empty($member)) {
            return $this->response($this->error([], '该员工尚未绑定会员账号，请先用手机号快捷登录一次'));
        }
        if ($member[ 'status' ] == 0) {
            return $this->response($this->error([], '账号已被禁用，请联系管理员'));
        }

        // 连续失败锁定键：必须在 if 之前定义，成功路径也要用它清零计数
        $fail_key = 'SHOP_PWD_FAIL_' . $member[ 'member_id' ];
        if (!$this->verifyPassword($password, $member[ 'password' ] ?? '')) {
            // 连续失败计数（同一账号 10 分钟内 5 次锁定）
            $fail_count = intval(Cache::get($fail_key)) + 1;
            Cache::set($fail_key, $fail_count, 600);
            if ($fail_count >= 5) {
                return $this->response($this->error([], '密码错误次数过多，请 10 分钟后再试'));
            }
            return $this->response($this->error([], '账号或密码错误'));
        }
        Cache::delete($fail_key);

        $member_id = $member[ 'member_id' ];
        // 补齐员工与会员的关联
        if (empty($employee[ 'member_id' ]) || $employee[ 'member_id' ] != $member_id) {
            Db::name('shop_employee')->where([ [ 'employee_id', '=', $employee[ 'employee_id' ] ] ])
                ->update([ 'member_id' => $member_id ]);
        }
        EmployeeModel::syncMemberPartnerLevel($member_id, $employee[ 'mobile' ]);

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
     * 修改登录密码
     */
    public function changePassword()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $old_password = (string)($this->params[ 'old_password' ] ?? '');
        $new_password = (string)($this->params[ 'new_password' ] ?? '');
        if (strlen($new_password) < 6 || strlen($new_password) > 32) {
            return $this->response($this->error([], '新密码长度需为 6-32 位'));
        }
        if (!preg_match('/^[A-Za-z0-9_@#$%^&*\-.]+$/', $new_password)) {
            return $this->response($this->error([], '新密码只能包含字母、数字及 _ @ # $ % ^ & * - .'));
        }

        $member = Db::name('member')->where([ [ 'member_id', '=', $this->member_id ] ])->find();
        if (empty($member)) return $this->response($this->error([], '账号不存在'));

        if (!empty($member[ 'password' ]) && !$this->verifyPassword($old_password, $member[ 'password' ])) {
            return $this->response($this->error([], '原密码错误'));
        }

        Db::name('member')->where([ [ 'member_id', '=', $this->member_id ] ])->update([
            'password' => password_hash($new_password, PASSWORD_DEFAULT),
        ]);
        return $this->response($this->success([], '操作成功'));
    }

    /**
     * 密码校验：兼容 bcrypt 与历史 md5
     */
    private function verifyPassword(string $input, string $stored) : bool
    {
        if ($stored == '') return false;

        // bcrypt：先把已知的跨语言前缀归一化为 PHP 认识的 $2y$
        // （password_get_info 只识别 $2y$ / $2a$，node bcryptjs 等生成的 $2b$ 会被判为 algo=0）
        if (preg_match('/^\$2[abxy]\$/', $stored)) {
            $normalized = '$2y$' . substr($stored, 4);
            if (password_verify($input, $normalized)) return true;
            if (password_verify($input, $stored)) return true;
        }

        // 历史数据：md5 / sha1(md5) / 大写 md5
        if ($stored === md5($input)) return true;
        if ($stored === sha1(md5($input))) return true;
        if ($stored === strtoupper(md5($input))) return true;
        if ($stored === sha1($input)) return true;
        return false;
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
