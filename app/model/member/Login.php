<?php
/**

 */

namespace app\model\member;

use addon\wechat\model\Message as WechatMessage;
use app\model\BaseModel;
use app\model\message\Email;
use app\model\message\Sms;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\model\upload\Upload;

/**
 * 登录
 *
 * @author Administrator
 *
 */
class Login extends BaseModel
{

    /**
     * 用户登录
     * @param $data
     * @return array
     * @throws DbException
     */
    public function login($data)
    {
        //必然传输usern
        $info = model("member")->getInfo(
            [
                [ 'username|mobile|email', '=', $data[ 'username' ] ],
                [ 'password', '=', data_md5($data[ 'password' ]) ],
                [ 'is_delete', '=', 0 ]
            ], 'member_id,
            username, nickname, mobile, email, status,last_login_time'
        );

        if (empty($info)) {
            return $this->error('', 'USERNAME_OR_PASSWORD_ERROR');
        } elseif ($info[ 'status' ] == 0) {
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            //更新登录时间
            model("member")->update([
                'login_time' => time(),
                'login_type' => empty($data['app_type'])?'':$data['app_type'],
                'login_type_name' => empty($data['app_type_name'])?'':$data['app_type_name'],
                'last_login_time' => time(),
                'login_ip' => request()->ip(),
            ], [ [ 'member_id', '=', $info[ 'member_id' ] ] ]);
            //执行登录奖励
            event("MemberLogin", [ 'member_id' => $info[ 'member_id' ] ], true);
            //用户第三方信息刷新
            $this->refreshAuth($info[ 'member_id' ], $data);
            return $this->success($info);
        }
    }

    /**
     * 第三方登录
     * @param $data
     * @return array
     * @throws DbException
     */
    public function authLogin($data)
    {
        $info = [];
        foreach ($data as $key => $value) {
            if (in_array($key, [ 'wx_unionid', 'wx_openid', 'weapp_openid', 'qq_openid', 'ali_openid', 'baidu_openid', 'toutiao_openid' ])) {
                $info = model("member")->getInfo(
                    [
                        [ $key, '=', $value ],
                        [ 'is_delete', '=', 0 ]
                    ], 'member_id,username, nickname, mobile, email, status, last_login_time'
                );
                if (!empty($info)) break;
            }
        }

        if (empty($info)) {
            // 会员不存在 第三方自动注册开启 未开启绑定手机 则进行自动注册
            $config = new Config();
            $config_info = $config->getRegisterConfig();
            if ($config_info[ 'data' ][ 'value' ][ 'third_party' ] && !$config_info[ 'data' ][ 'value' ][ 'bind_mobile' ]) {
                $register = new Register();
                $register_res = $register->authRegister($data);
                if ($register_res[ 'code' ] == 0) {
                    $info = model("member")->getInfo([ [ 'member_id', '=', $register_res[ 'data' ] ] ], 'member_id,username, nickname, mobile, email, status, last_login_time');
                }
            }
        }

        if (empty($info)) {
            return $this->error('', 'MEMBER_NOT_EXIST');
        } elseif ($info[ 'status' ] == 0) {
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            $member_data = [
                'login_time' => time(),
                'login_type' => empty($data['app_type'])?'':$data['app_type'],
                'login_type_name' => empty($data['app_type_name'])?'':$data['app_type_name'],
                'last_login_time' => time(),
                'login_ip' => request()->ip(),
            ];
            if (!empty($data['nickName'])) {
                $member_data['nickname'] = $data['nickName'];
            }
            if (!empty($data['avatarUrl'])) {
                // 获取图片内容
                // 通过 cURL 获取图片内容
                /*$imageData = $this->downloadImageWithCurl($data['avatarUrl']);
                if ($imageData) {
                    $img_hz = 'jpg';
                    //文件名
                    $date = date('Ymd');
                    $name = (new Upload())->createNewFileName();
                    if (!file_exists('upload/1/headimg/'. $date)) {
                        mkdir('upload/1/headimg/'.$date);
                    }
                    file_put_contents('upload/1/headimg/'. $date . '/' . $name . "." . $img_hz,$imageData);

                    $img = 'upload/1/headimg/'. $date . '/' . $name . "." . $img_hz;
                    $member_data['headimg'] = $img;
                }*/
                $member_data['headimg'] = $data['avatarUrl'];
            }
            //更新登录时间
            model("member")->update($member_data, [ [ 'member_id', '=', $info[ 'member_id' ] ] ]);

            //执行登录奖励
            event("MemberLogin", [ 'member_id' => $info[ 'member_id' ] ], true);

            //用户第三方信息刷新
            $this->refreshAuth($info[ 'member_id' ], $data);
            return $this->success($info);
        }
    }

    // 使用 cURL 下载图片
    private function downloadImageWithCurl($url)
    {
        $ch = curl_init(); // 初始化 cURL 会话
        curl_setopt($ch, CURLOPT_URL, $url); // 设置图片 URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 将结果返回而非直接输出
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时时间

        $imageData = curl_exec($ch); // 执行 cURL 会话并获取内容
        curl_close($ch); // 关闭 cURL 会话

        if ($imageData === false) {
            return null; // 如果下载失败返回 null
        }

        return $imageData;
    }


    /**
     * 刷新第三方信息
     * @param $member_id
     * @param $data
     * @return array
     * @throws DbException
     */
    private function refreshAuth($member_id, $data)
    {
        $data = [
            'qq_openid' => isset($data[ 'qq_openid' ]) ? $data[ 'qq_openid' ] : '',
            'wx_openid' => isset($data[ 'wx_openid' ]) ? $data[ 'wx_openid' ] : '',
            'weapp_openid' => isset($data[ 'weapp_openid' ]) ? $data[ 'weapp_openid' ] : '',
            'wx_unionid' => isset($data[ 'wx_unionid' ]) ? $data[ 'wx_unionid' ] : '',
            'ali_openid' => isset($data[ 'ali_openid' ]) ? $data[ 'ali_openid' ] : '',
            'baidu_openid' => isset($data[ 'baidu_openid' ]) ? $data[ 'baidu_openid' ] : '',
            'toutiao_openid' => isset($data[ 'toutiao_openid' ]) ? $data[ 'toutiao_openid' ] : '',
        ];
        if (!empty($data[ 'qq_openid' ])) {
            model("member")->update([ 'qq_openid' => '' ], [ [ 'qq_openid', '=', $data[ 'qq_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'qq_openid' => $data[ 'qq_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        if (!empty($data[ 'wx_openid' ])) {
            model("member")->update([ 'wx_openid' => '' ], [ [ 'wx_openid', '=', $data[ 'wx_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'wx_openid' => $data[ 'wx_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        if (!empty($data[ 'weapp_openid' ])) {
            model("member")->update([ 'weapp_openid' => '' ], [ [ 'weapp_openid', '=', $data[ 'weapp_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'weapp_openid' => $data[ 'weapp_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        if (!empty($data[ 'wx_unionid' ])) {
            model("member")->update([ 'wx_unionid' => '' ], [ [ 'wx_unionid', '=', $data[ 'wx_unionid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'wx_unionid' => $data[ 'wx_unionid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        if (!empty($data[ 'ali_openid' ])) {
            model("member")->update([ 'ali_openid' => '' ], [ [ 'ali_openid', '=', $data[ 'ali_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'ali_openid' => $data[ 'ali_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        if (!empty($data[ 'baidu_openid' ])) {
            model("member")->update([ 'baidu_openid' => '' ], [ [ 'baidu_openid', '=', $data[ 'baidu_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'baidu_openid' => $data[ 'baidu_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        if (!empty($data[ 'toutiao_openid' ])) {
            model("member")->update([ 'toutiao_openid' => '' ], [ [ 'toutiao_openid', '=', $data[ 'toutiao_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'toutiao_openid' => $data[ 'toutiao_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
        }
        return $this->success();
    }

    /**
     * 检测openid是否存在
     * @param array $data
     * @return array
     */
    public function openidIsExits(array $data)
    {
        if (isset($data[ 'wx_unionid' ]) && !empty($data[ 'wx_unionid' ])) {
            $count = model("member")->getCount([ [ 'wx_unionid', '=', $data[ 'wx_unionid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        if (isset($data[ 'wx_openid' ]) && !empty($data[ 'wx_openid' ])) {
            $count = model("member")->getCount([ [ 'wx_openid', '=', $data[ 'wx_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        if (isset($data[ 'weapp_openid' ]) && !empty($data[ 'weapp_openid' ])) {
            $count = model("member")->getCount([ [ 'weapp_openid', '=', $data[ 'weapp_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        if (isset($data[ 'qq_openid' ]) && !empty($data[ 'qq_openid' ])) {
            $count = model("member")->getCount([ [ 'qq_openid', '=', $data[ 'qq_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        if (isset($data[ 'ali_openid' ]) && !empty($data[ 'ali_openid' ])) {
            $count = model("member")->getCount([ [ 'ali_openid', '=', $data[ 'ali_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        if (isset($data[ 'baidu_openid' ]) && !empty($data[ 'baidu_openid' ])) {
            $count = model("member")->getCount([ [ 'baidu_openid', '=', $data[ 'baidu_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        if (isset($data[ 'toutiao_openid' ]) && !empty($data[ 'toutiao_openid' ])) {
            $count = model("member")->getCount([ [ 'toutiao_openid', '=', $data[ 'toutiao_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            if ($count) return $this->success($count);
        }
        return $this->success(0);
    }

    /**
     * 用户登录
     * @param $data
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function mobileLogin($data)
    {
        //必然传输usern
        $info = model("member")->getInfo(
            [ [ 'mobile', '=', $data[ 'mobile' ] ], [ 'is_delete', '=', 0 ] ], 'member_id,
            username, nickname, mobile, email, status,last_login_time');
        if (empty($info)) {
            return $this->error('', 'MEMBER_NOT_EXIST');
        } elseif ($info[ 'status' ] == 0) {
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            //更新登录时间及所属商家
            $update_data = [
                'login_time' => time(),
                'login_type' => empty($data['app_type'])?'':$data['app_type'],
                'login_type_name' => empty($data['app_type_name'])?'':$data['app_type_name'],
                'last_login_time' => time(),
                'login_ip' => request()->ip(),
            ];

            // 检查所属商家绑定
            $current_site_id = model("member")->getValue([ [ 'member_id', '=', $info[ 'member_id' ] ] ], 'site_id');
            // 1. 如果是商家员工/主理人，绑定为该商家
            $emp = \think\facade\Db::name('shop_employee')
                ->where([ [ 'mobile', '=', $info['mobile'] ], [ 'is_deleted', '=', 0 ] ])
                ->find();
            if ($emp && !empty($emp['shop_id'])) {
                $update_data['site_id'] = (int)$emp['shop_id'];
                if (empty($emp['member_id'])) {
                    \think\facade\Db::name('shop_employee')->where('employee_id', $emp['employee_id'])->update(['member_id' => $info['member_id']]);
                }
            } elseif (empty($current_site_id) && !empty($data['site_id'])) {
                // 2. 如果用户尚未绑定商家，且本次登录传入了商家ID
                $update_data['site_id'] = (int)$data['site_id'];
            }

            model("member")->update($update_data, [ [ 'member_id', '=', $info[ 'member_id' ] ] ]);

            event("MemberLogin", [ 'member_id' => $info[ 'member_id' ] ], true);

            //用户第三方信息刷新
            $this->refreshAuth($info[ 'member_id' ], $data);
            return $this->success($info);
        }
    }

    /**
     * 登录动态码
     * @param $data
     * @return array|mixed|void
     */
    public function loginCode($data)
    {
        //发送短信
        $sms_model = new Sms();
        $var_parse = array (
            "code" => $data[ "code" ],
        );
        $data[ "sms_account" ] = $data[ "mobile" ] ?? '';//手机号
        $data[ "var_parse" ] = $var_parse;
        $sms_result = $sms_model->sendMessage($data);
        if ($sms_result[ "code" ] < 0) {
            return $sms_result;
        }

        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        $data[ "email_account" ] = $data[ "email" ] ?? '';//邮箱号
        $email_result = $email_model->sendMessage($data);
        if ($email_result[ "code" ] < 0) {
            return $email_result;
        }

        return $this->success();
    }

    /**
     * 登录通知
     * @param $data
     * @return array|mixed|void
     */
    public function loginSuccess($data)
    {
        $member_model = new Member();
        $member_info_result = $member_model->getMemberInfo(
            [ [ "member_id", "=", $data[ "member_id" ] ] ],
            "username,mobile,email,reg_time,wx_openid,last_login_type,login_time"
        );
        $member_info = $member_info_result[ "data" ];

        //发送短信
        $sms_model = new Sms();
        $var_parse = array (
            "name" => $member_info[ "username" ],//验证码
        );
        $data[ "sms_account" ] = $member_info[ "mobile" ] ?? '';//手机号
        $data[ "var_parse" ] = $var_parse;
        $sms_result = $sms_model->sendMessage($data);
        //        if($sms_result["code"] < 0)
        //            return $sms_result;

        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        $data[ "email_account" ] = $member_info[ "email" ] ?? '';//邮箱号
        $email_result = $email_model->sendMessage($data);
        //        if($email_result["code"] < 0)
        //            return $email_result;


        //发送模板消息
        $wechat_model = new WechatMessage();
        $data[ "openid" ] = $member_info[ "wx_openid" ];

//         if(!empty($member_info["username"])){
//            $user_account = $member_info["username"];
//         }else{
//            if(!empty($member_info["mobile"])){
//              $user_account = $member_info["mobile"];
//            }else{
//              $user_account = $member_info["email"];
//            }
//         }
        $username = '';
        if (!empty($member_info[ "username" ])) {
            $username = $member_info[ "username" ];
        }
        if (empty($username) && !empty($member_info[ "mobile" ])) {
            $username = $member_info[ "mobile" ];
        }
        if (empty($username)) {
            $username = '访客';
        }

        $data[ "template_data" ] = [
            'keyword1' => $username,
            'keyword2' => '登录成功',
            'keyword3' => time_to_date($member_info[ "login_time" ]),
        ];

        $data[ "page" ] = '';
        $wechat_model->sendMessage($data);

        return $this->success();
    }
}
