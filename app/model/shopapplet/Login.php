<?php
/**

 */

namespace app\model\shopapplet;

use think\facade\Db;

use addon\wechat\model\Message as WechatMessage;
use app\model\BaseModel;
use app\model\message\Email;
use app\model\message\Sms;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\model\upload\Upload;
use app\model\shopapplet\Register as RegisterModel;

/**
 * 登录
 *
 * @author Administrator
 *
 */
class Login extends BaseModel
{

public function mobileLogin($data)
    {
        //必然传输usern
        $info = model("member")->getInfo(
            [ [ 'mobile', '=', $data[ 'mobile' ] ], [ 'is_delete', '=', 0 ] ], 'member_id,
            username, nickname, mobile, email, status,last_login_time');
        if (empty($info)) {
            return $this->error('', '员工不存在，不能登录企业端');
        } elseif ($info[ 'status' ] == 0) {
            return $this->error('', '企业用户已被禁用');
        } else {
            //更新登录时间
            model("member")->update([
                'login_time' => time(),
                'login_type' => empty($data['app_type'])?'':$data['app_type'],
                'login_type_name' => empty($data['app_type_name'])?'':$data['app_type_name'],
                'last_login_time' => time(),
                'login_ip' => request()->ip(),
            ], [ [ 'member_id', '=', $info[ 'member_id' ] ] ]);

            event("MemberLogin", [ 'member_id' => $info[ 'member_id' ] ], true);
            $employeeinfo =  Db::name("shop_employee")->where("mobile='".$data[ 'mobile' ]."' and is_deleted=0")->find(); 
            if(!empty($employeeinfo)){
                $member_id=$info[ 'member_id' ] ;
                Db::name("shop_employee")->where(["employee_id"=>$employeeinfo["employee_id"]])->update(["member_id"=>$member_id]);
            }

            //用户第三方信息刷新
            $this->refreshAuth($info[ 'member_id' ], $data);
            return $this->success($info);
        }
    }

 /**
     * 用户手机授权登录
     * @param $data
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function mobileLogin1($data)
    {
        //必然传输username
        $mobile = $data[ 'mobile' ];
        $info =  Db::name("shop_employee")->where("mobile='".$mobile."' and is_deleted=0")->find(); 
        if (empty($info)) {
            //员工不存在
            return $this->error('', 'MEMBER_NOT_EXIST');
        } elseif ($info[ 'employee_status' ] == 0) {
            //员工状态被禁用
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            $member_id=Db::name("member")->where(['mobile'=>$mobile,"is_detele"=>0])->value("member_id");
            if(!empty($member_id)){
                //会员表里面存在
                 Db::name("s`hop_employee")->where(["employee_id"=>$info["employee_id"]])->update(["member_id"=>$member_id]);//和企业员工关联
                 //更新登录时间
                model("member")->update([
                    'login_time' => time(),
                    'login_type' => empty($data['app_type'])?'':$data['app_type'],
                    'login_type_name' => empty($data['app_type_name'])?'':$data['app_type_name'],
                    'last_login_time' => time(),
                    'login_ip' => request()->ip(),
                ], [ [ 'member_id', '=', $member_id ] ]);

                event("MemberLogin", [ 'member_id' => $member_id], true);
                //用户第三方信息刷新
                $this->refreshAuth($member_id, $data);
                 return $this->success($info);
            }else{
                //会员表里面不存在，自动填入会员表
                $RegisterModel = new RegisterModel();
                $res=$RegisterModel->mobileRegister($data);
                if($res['code']!=0){
                    return $this->error($res['msg']);
                }else{
                    //会员表里面不存在，自动填入会员表
                    $member_id=$res['data']["member_id"];
                    Db::name("shop_employee")->where(["employee_id"=>$info["employee_id"]])->update(["member_id"=>$member_id]);
                }
                return $this->success($info);
            }
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
            if (in_array($key, [ 'wx_unionid', 'wx_openid','wxqy_openid', 'weapp_openid', 'qq_openid', 'ali_openid', 'baidu_openid', 'toutiao_openid' ])) {
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
            'wxqy_openid' => isset($data[ 'wxqy_openid' ]) ? $data[ 'wxqy_openid' ] : '',
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
        if (!empty($data[ 'wxqy_openid' ])) {
            model("member")->update([ 'wxqy_openid' => '' ], [ [ 'wxqy_openid', '=', $data[ 'wxqy_openid' ] ], [ 'is_delete', '=', 0 ] ]);
            model("member")->update([ 'wxqy_openid' => $data[ 'wxqy_openid' ] ], [ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ]);
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
        
        if (isset($data[ 'wxqy_openid' ]) && !empty($data[ 'wxqy_openid' ])) {
            $count = model("member")->getCount([ [ 'wxqy_openid', '=', $data[ 'wxqy_openid' ] ], [ 'is_delete', '=', 0 ] ]);
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

   

    
}
