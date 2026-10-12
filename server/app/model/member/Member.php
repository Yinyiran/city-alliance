<?php
/**

 */

namespace app\model\member;

use app\model\BaseModel;
use app\model\message\Email;
use app\model\message\Sms;
use app\model\system\Address;
use app\model\system\Stat;
use app\model\upload\Upload;
use think\facade\Db;
use think\facade\Cache;
use think\Model;

/**
 * 会员管理
 */
class Member extends BaseModel
{

    /**
     * 添加会员(注意等级名称)
     * @param $data
     * @return array
     */
    public function addMember($data)
    {
        if ($data[ 'username' ]) {
            $count = model('member')->getCount([
                'username' => $data[ 'username' ],
            ]);
            if ($count > 0) {
                return $this->error('', 'USERNAME_EXISTED');
            }
        }

        if ($data[ 'mobile' ]) {
            $count = model('member')->getCount([
                'mobile' => $data[ 'mobile' ]
            ]);
            if ($count > 0) {
                return $this->error('', 'MOBILE_EXISTED');
            }
        }

        if ($data[ 'email' ]) {
            $count = model('member')->getCount([
                'email' => $data[ 'email' ]
            ]);
            if ($count > 0) {
                return $this->error('', 'EMAIL_EXISTED');
            }
        }
        $res = model('member')->add($data);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        //添加统计
        $stat = new Stat();
        $stat->addShopStat([ 'member_count' => 1, 'site_id' => 0 ]);
        return $this->success($res);
    }

    /**
     * 修改会员(注意标签与等级名称)
     * @param $data
     * @param $condition
     * @return array
     */
    public function editMember($data, $condition)
    {
        $res = model('member')->update($data, $condition);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 修改用户名
     * @param $member_id
     * @param $username
     */
    public function editUsername($member_id,  $username){
        $member_info = model('member')->getInfo([ ['member_id', '=', $member_id] ], 'username,is_edit_username');
        if (empty($member_info)) return $this->error('', '未查找到该用户');
        if ($member_info['username'] == $username) return $this->error('', '与原用户名一致，无需修改');
        if (!$member_info['is_edit_username']) return $this->error('', '用户名不可进行修改');
        $count = model('member')->getCount([ ['username', '=', $username]],  'member_id');
        if ($count != 0) return $this->error('', '该用户名已存在');
        model('member')->update([ 'username' => $username, 'is_edit_username' => 0 ], [ ['member_id', '=', $member_id] ]);
        return $this->success();
    }

    /**
     * 修改会员状态
     * @param $status
     * @param $condition
     * @return array
     */
    public function modifyMemberStatus($status, $condition)
    {
        $res = model('member')->update([
            'status' => $status
        ], $condition);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        Cache::set('member_blacklist', null);
        return $this->success($res);
    }

    /**
     * 修改会员标签
     * @param $label_ids
     * @param $condition
     * @return array
     */
    public function modifyMemberLabel($label_ids, $condition)
    {
        //查询会员标签
        $label_list = model("member_label")->getList([ [ 'label_id', 'in', $label_ids ] ], 'label_id,label_name');

        $label_ids = '';
        $label_names = '';
        if (!empty($label_list)) {
            foreach ($label_list as $k => $v) {
                $label_ids = $label_ids . $v[ 'label_id' ] . ',';
                $label_names = $label_names . $v[ 'label_name' ] . ',';
            }
        }
        $res = model('member')->update([
            'member_label' => $label_ids,
            'member_label_name' => $label_names
        ], $condition);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 重置密码
     * @param string $password
     * @param $condition
     * @return array
     */
    public function resetMemberPassword($password = '123456', $condition)
    {
        $res = model('member')->update([
            'password' => data_md5($password)
        ], $condition);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 修改密码
     * @param $member_id
     * @param $old_password
     * @param $new_password
     * @return array
     */
    public function modifyMemberPassword($member_id, $old_password, $new_password)
    {
        $res = model('member')->getCount([
            [ 'password', '=', data_md5($old_password) ],
            [ 'member_id', '=', $member_id ]
        ]);
        if ($res > 0) {
            $res = model('member')->update([
                'password' => data_md5($new_password)
            ], [ [ 'member_id', '=', $member_id ] ]);
            if ($res === false) {
                return $this->error('', 'RESULT_ERROR');
            }
            return $this->success($res);
        } else {
            return $this->error('', 'PASSWORD_ERROR');
        }
    }

    /**
     * 删除会员（应用后台）
     * @param $condition
     * @return array
     */
    public function deleteMember($condition)
    {
        $res = model('member')->delete($condition);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 获取会员信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getMemberInfo($condition = [], $field = '*')
    {
        $condition[] = [ 'is_delete', '=', 0 ];
        $member_info = model('member')->getInfo($condition, $field);
        if (!empty($member_info) && isset($member_info['member_id'])) {
            $member_info['member_code'] = sprintf("%06d", $member_info['member_id']);
            $member_info['member_id_str'] = sprintf("%06d", $member_info['member_id']);
        }
        return $this->success($member_info);
    }

    /**
     * 获取会员信息
     * @param int $member_id
     * @return array
     */
    public function getMemberDetail($member_id)
    {
        $field = 'member_id,source_member,username,nickname,mobile,email,status,headimg,member_level,member_level_name,member_label,member_label_name,qq,realname,sex,location,birthday,reg_time,point,balance,growth,balance_money,account5,pay_password,website_id,is_face_auth,id_card,job_type';
        $member_info = model('member')->getInfo([ [ 'member_id', '=', $member_id ] ], $field);
        if (!empty($member_info)) {
            $member_info[ 'balance_total' ] = $member_info[ 'balance' ] + $member_info[ 'balance_money' ];
            $member_info['member_code'] = sprintf("%06d", $member_info['member_id']);
            $member_info['member_id_str'] = sprintf("%06d", $member_info['member_id']);
            return $this->success($member_info);
        }
        return $this->error();
    }

    /**
     * 获取会员分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getMemberPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $condition[] = [ 'is_delete', '=', 0 ];
        $list = model('member')->pageList($condition, $field, $order, $page, $page_size, '', '', '');
        return $this->success($list);
    }

    /**
     * 获取会员列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param string $alias
     * @param array $join
     * @param string $group
     * @param null $limit
     * @return array
     */
    public function getMemberList($where = [], $field = true, $order = '', $alias = 'a', $join = [], $group = '', $limit = null)
    {
        $condition[] = [ 'is_delete', '=', 0 ];
        $res = model('member')->getList($where, $field, $order, $alias, $join, $group, $limit);
        return $this->success($res);
    }

    /**
     * 绑定发送验证码
     * @param $data
     * @return array|mixed|void
     */
    public function bindCode($data)
    {
        //发送短信
        $sms_model = new Sms();
        $var_parse = array (
            "code" => $data[ "code" ],//验证码
//            "site_name" => $data[ "site_name" ],//站点名称
        );
        $data[ "sms_account" ] = $data[ "mobile" ] ?? '';//手机号
        $data[ "var_parse" ] = $var_parse;
        $sms_result = $sms_model->sendMessage($data);
        if ($sms_result[ "code" ] < 0)
            return $sms_result;

        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        $data[ "email_account" ] = $data[ "email" ] ?? '';//邮箱号
        $email_result = $email_model->sendMessage($data);
        if ($email_result[ "code" ] < 0)
            return $email_result;

        return $this->success();
    }

    /**
     * 找回密码发送验证码
     * @param $data
     * @return array|mixed|void
     */
    public function findCode($data)
    {
        //发送短信
        $sms_model = new Sms();
        $var_parse = array (
            "code" => $data[ "code" ],//验证码
            "site_name" => $data[ "site_name" ],//站点名称
        );
        $data[ "sms_account" ] = $data[ "mobile" ] ?? '';//手机号
        $data[ "var_parse" ] = $var_parse;
        $sms_result = $sms_model->sendMessage($data);
        if ($sms_result[ "code" ] < 0)
            return $sms_result;

        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        $data[ "email_account" ] = $data[ "email" ] ?? '';//邮箱号
        $email_result = $email_model->sendMessage($data);
        if ($email_result[ "code" ] < 0)
            return $email_result;

        return $this->success();
    }

    /**
     * 设置会员交易密码
     * @param $member_id
     * @param $password
     * @return array
     */
    public function modifyMemberPayPassword($member_id, $password)
    {
        $res = model('member')->update([
            'pay_password' => data_md5($password)
        ], [ [ 'member_id', '=', $member_id ] ]);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 会员是否已设置支付密码
     * @param $member_id
     * @return array
     */
    public function memberIsSetPayPassword($member_id)
    {
        $info = model('member')->getInfo([ [ 'member_id', '=', $member_id ] ], 'pay_password');
        if (empty($info[ 'pay_password' ])) return $this->success(0);
        else return $this->success(1);
    }

    /**
     * 检测会员支付密码是否正确
     * @param $member_id
     * @param $pay_password
     * @return array
     */
    public function checkPayPassword($member_id, $pay_password)
    {
        $res = model('member')->getCount([
            [ 'pay_password', '=', data_md5($pay_password) ],
            [ 'member_id', '=', $member_id ]
        ]);
        if ($res > 0) {
            return $this->success($res);
        } else {
            return $this->error('', 'PAY_PASSWORD_ERROR');
        }
    }


    /**
     * 找回密码发送验证码
     * @param $data
     * @return array|mixed|void
     */
    public function paypasswordCode($data)
    {
        //发送短信
        $sms_model = new Sms();
        $var_parse = array (
            "code" => $data[ "code" ],//验证码
            "site_name" => $data[ "site_name" ],//站点名称
        );
        $member_info_result = $this->getMemberInfo([ [ "member_id", "=", $data[ "member_id" ] ] ], "mobile");
        $member_info = $member_info_result[ "data" ];
        $data[ "sms_account" ] = $member_info[ "mobile" ] ?? '';//通过member_id获得手机号
        $data[ "var_parse" ] = $var_parse;
        $sms_result = $sms_model->sendMessage($data);
        if ($sms_result[ "code" ] < 0)
            return $sms_result;

        return $this->success();
    }

    /**
     * 拉取用户头像到本地
     * @param $member_id
     */
    public function pullHeadimg($member_id)
    {
        $member_info = model("member")->getInfo([ [ 'member_id', '=', $member_id ] ], 'headimg');
        if (!empty($member_info[ 'headimg' ]) && is_url($member_info[ 'headimg' ])) {
            $upload = new Upload();
            $res = $upload->setPath('upload/user/haedimg/')->remotePull($member_info[ 'headimg' ]);
            if ($res[ 'code' ] >= 0) {
                model("member")->update([ 'headimg' => $res[ 'data' ][ 'pic_path' ] ], [ [ 'member_id', '=', $member_id ] ]);
            }
        }
    }


    /*********************************************************************  会员统计 start ***********************************************************************/
    /**
     * 获取会员数量
     * @param array $condition
     * @return array
     */
    public function getMemberCount($condition = [], $alias = 'a', $join = [], $group = null)
    {
//        $member_info = model('member')->getCount($condition);
//        return $this->success($member_info);
        $condition[] = [ 'is_delete', '=', 0 ];
        $db = Db::name('member')->where($condition);
        if (!empty($join)) {
            $db = $this->parseJoin($db->alias($alias), $join);
        }
        if (!empty($group)) {
            $db = $db->group($group);
        }
        $count = $db->count();
        return $this->success($count);
    }


    /**
     * 按地域分布查询会员数量
     * @param unknown $site_id
     * @param string $handle
     */
    public function getMemberCountByArea($handle = false)
    {
        $total_count = $this->getMemberCount([ [ 'is_delete', '=', 0 ] ])[ 'data' ] ?? 0;
        $address = new Address();
        $list = $address->getAreaList([ [ 'pid', '=', 0 ] ], 'id,shortname', 'sort asc');
        $data = [];
        if ($total_count > 0) {
            foreach ($list[ 'data' ] as $item) {
                $count = $this->getMemberCount(
                    [
                        [ 'nma.is_default', '=', 1 ],
                        [ 'nma.province_id', '=', $item[ 'id' ] ],
                        [ 'nm.is_delete', '=', 0 ]
                    ],
                    'nm', [ [ 'member_address nma', 'nm.member_id = nma.member_id', 'left' ] ], 'nm.member_id'
                );
                if ($handle) {
                    if ($count[ 'data' ] > 0) {
                        array_push($data, [
                            'name' => $item[ 'shortname' ],
                            'value' => $count[ 'data' ],
                            'ratio' => $count[ 'data' ] > 0 ? sprintf("%.2f", $count[ 'data' ] / $total_count * 100) : 0
                        ]);
                    }
                } else {
                    array_push($data, [
                        'name' => $item[ 'shortname' ],
                        'value' => $count[ 'data' ],
                        'ratio' => $count[ 'data' ] > 0 ? sprintf("%.2f", $count[ 'data' ] / $total_count * 100) : 0
                    ]);
                }
            }
        }
        if ($handle) {
            array_multisort(array_column($data, 'value'), SORT_DESC, $data);
        }

        return $this->success([
            'page_count' => 1,
            'count' => $total_count,
            'list' => $data
        ]);
    }

    /**
     * 处理表连接
     * @param unknown $db_obj
     * @param unknown $join
     */
    protected function parseJoin($db_obj, $join)
    {
        foreach ($join as $item) {
            list($table, $on, $type) = $item;
            $type = strtolower($type);
            switch ( $type ) {
                case "left":
                    $db_obj = $db_obj->leftJoin($table, $on);
                    break;
                case "inner":
                    $db_obj = $db_obj->join($table, $on);
                    break;
                case "right":
                    $db_obj = $db_obj->rightjoin($table, $on);
                    break;
                case "full":
                    $db_obj = $db_obj->fulljoin($table, $on);
                    break;
                default:
                    break;
            }
        }
        return $db_obj;
    }
    /*********************************************************************  会员统计 start ***********************************************************************/

    /**
     * 获取用户黑名单
     * @return array
     */
    public function getMemberBlacklist()
    {
        $cache = Cache::get('member_blacklist');
        if (!empty($cache)) return $this->success($cache);

        $blacklist = model('member')->getColumn([ [ 'status', '=', 0 ] ], 'member_id');
        Cache::set('member_blacklist', $blacklist);

        return $this->success($blacklist);
    }

    /***
     * 发送验证码
     */
    public function mobileCode($mobile)
    {
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
//        $code = 1234;
        $res = (new \app\api\controller\Sms())->txsms($mobile, $code);
//        $res = 1;
        if ($res) {
            //将验证码存入缓存
            $key = 'store_login_mobile_code_' . md5(uniqid(null, true));
            Cache::tag("store_login_mobile_code")->set($key, [ 'mobile' => $mobile, 'code' => $code ], 600);
            return $this->success([ "key" => $key ]);
        } else {
            return $this->error('', '发送失败');
        }
    }

    /**
     * 修改手机号
     */
    public function mobileEdit($data)
    {
        if (!isMobile($data['mobile'])) return $this->error('', '手机号格式错误');
        $verify_data = Cache::get($data['key']);
        if ($verify_data[ "mobile" ] == $data['mobile'] && $verify_data[ "code" ] == $data['code']) {
            model('member')->update(['mobile'=>$data['mobile']], ['member_id'=>$data['member_id']]);
            return $this->success();
        } else {
            return $this->error("", "手机动态码不正确");
        }
    }

    /**
     * 商家入驻
     */
    public function shopJoin($data)
    {
        if (!$data['site_name']) return $this->error('', '请输入品牌名称');
        if (!$data['name']) return $this->error('', '请输入真实姓名');
        if (!isMobile($data['mobile'])) return $this->error('', '手机号格式错误');
        if (!$data['address']) return $this->error('', '请输入入驻城市');
        $category = model('shop_category')->getInfo(['category_id'=>$data['category_id']]);
        if ($join = Db::name('shop_join')->where('member_id', $data['member_id'])->where('status', '<>', '-1')->find()) {
            if ($join['status'] == '0') return $this->error('', '请等待审核');
        }
        $verify_data = Cache::get($data['key']);
        if ($verify_data[ "mobile" ] == $data['mobile'] && $verify_data[ "code" ] == $data['code']) {
            model('shop_join')->add([
                'member_id' => $data['member_id'],
                'site_name' => $data['site_name'],
                'mobile' => $data['mobile'],
                'address' => $data['address'],
                'category_id' => $category['category_id'] ?? 0,
                'category_name' => $category['category_name'] ?? '',
                'is_stall' => $data['is_stall'],
                'name' => $data['name'],
            ]);
            return $this->success();
        } else {
            return $this->error("", "手机动态码不正确");
        }
    }

    /**
     * 定位城市
     */
    public function selectCity($data)
    {
        $condition = [];
        $condition[] = ['site_id', '>', 0];
        $condition[] = ['status', '=', 1];
        $condition[] = ['site_area_id', '=', $data['city_id']];
        $field = 'site_id,title,site_area_id,site_area_name';
        $website = Db::name('website')->where($condition)->field($field)->find();
        if (!$website) return $this->error(['code'=>0,'city'=>false], '抱歉，本城市未开通~');
        return $this->success($website);
    }

    /**
     * 邀请好友数据
     */
    public function invitation($data)
    {
        //每邀请1单奖积分数
        $website = model('website')->getInfo(['site_id'=>0]);
        $yao_point = $website['yao_point'];
        //已成功邀请人数
        $condition = [];
        $condition[] = ['source_member', '=', $data['member_id']];
        $invitation_num = model('member')->getCount($condition);
        //邀请积分奖励
        $condition = [];
        $condition[] = ['from_type', '=', 'yaoqing'];
        $condition[] = ['account_type', '=', 'point'];
        $condition[] = ['point_type', '=', 1];
        $invitation_point = model('member_account')->getSum($condition, 'account_data');
        //已成功入驻会员数
        $member_level_num = 0;
        $fenxiao = model('fenxiao')->getInfo(['member_id'=>$data['member_id']]);
        if ($fenxiao) {
            $c_member_ids = model('fenxiao')->getColumn(['parent'=>$fenxiao['fenxiao_id']], 'member_id');
            $member_level_num = model('member')->getCount([ ['member_id', 'in', $c_member_ids], ['member_level', '>', 1] ]);
        }
        //现金奖励
        $condition = [];
        $condition[] = ['from_type', '=', 'yaoqing'];
        $condition[] = ['account_type', '=', 'balance_money'];
        $condition[] = ['point_type', '=', 1];
        $invitation_money = model('member_account')->getSum($condition, 'account_data');
        //邀请记录
        $condition = [];
        $condition[] = ['source_member', '=', $data['member_id']];
        $field = 'member_id,nickname,headimg';
        $order = 'member_id desc';
        $list = model('member')->pageList($condition,$field,$order,$data['page'],$data['page_size']);
        foreach ($list['list'] as $key => $value) {
            //送积分数
            $condition = [];
            $condition[] = ['from_type', '=', 'yaoqing'];
            $condition[] = ['account_type', '=', 'point'];
            $condition[] = ['point_type', '=', 1];
            $condition[] = ['type_tag', '=', $value['member_id']];
            $yao_point = model('member_account')->getValue($condition, 'account_data');
            $list['list'][$key]['yao_point'] = $yao_point;
        }
        $list['yao_point'] = $yao_point;
        $list['invitation_num'] = $invitation_num;
        $list['invitation_point'] = $invitation_point;
        $list['member_level_num'] = $member_level_num;
        $list['invitation_money'] = $invitation_money;
        return $this->success($list);
    }

}