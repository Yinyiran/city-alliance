<?php


namespace app\api\controller;

use app\model\member\Withdraw as WithdrawModel;
use app\model\member\Member as MemberModel;

/**
 * 会员提现
 */
class Memberwithdraw extends BaseApi
{

    /**
     * 会员提现信息
     */
    public function info()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $member_model = new MemberModel();
        $member_info_result = $member_model->getMemberInfo([ [ 'member_id', '=', $token[ 'data' ][ 'member_id' ] ] ], 'balance_money,balance_withdraw_apply,balance_withdraw');
//		$withdraw_model = new WithdrawModel();
//		$transfer_type_list = $withdraw_model->getTransferType();
        $config_model = new WithdrawModel();
        $config_result = $config_model->getConfig();
        $config = $config_result[ "data" ][ 'value' ];
        $config[ 'is_use' ] = $config_result[ "data" ][ 'is_use' ];

        $data = array (
            "member_info" => $member_info_result[ "data" ],
//			"transfer_type_list" => $transfer_type_list,
            "config" => $config
        );
        return $this->response($this->success($data));

    }

    /**
     * 提现规则（免登录）
     *
     * 微信审核要求：提现页面必须清晰展示单日限额、每日提现次数、提现时间、到账时间。
     * 该接口不校验 token，保证未登录 / 接口异常时提现页依然能展示完整规则说明。
     */
    public function rule()
    {
        return $this->response($this->success($this->buildWithdrawRule()));
    }

    /**
     * 会员提现配置
     */
    public function config()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) {
            // 未登录时仍返回规则，仅余额为 0，避免提现页出现空数据与白屏
            $rule = $this->buildWithdrawRule();
            $rule[ 'balance_money' ] = '0.00';
            $rule[ 'is_login' ] = 0;
            return $this->response($this->success($rule));
        }

        $config_model = new WithdrawModel();
        $config_result = $config_model->getConfig();
        //返回用户可提现余额
        if (isset($config_result['data'])) {
            $member = model('member')->getInfo(['member_id'=>$this->member_id]);
            $config_result['data']['balance_money'] = $member['balance_money'];
        }
        $config_result['data']['withdraw_rule'] = $this->buildWithdrawRule();
        $config_result['data']['is_login'] = 1;
        return $this->response($config_result);
    }

    /**
     * 组装结构化提现规则
     *
     * 1) 文案必须与 Withdraw::apply() / Withdraw::calculateWithdrawTax() 的实际逻辑一致，避免展示与真实规则不符。
     * 2) 「最低提现额度」按后台配置动态下发：min > 0 才输出该条；min = 0（不设门槛）时不输出，
     *    前端据此不展示。依据微信《小程序平台运营规范》3.3，建议后台保持 0。
     * 3) 必须包含微信审核点名的四要素：可提现额度、每日提现次数、提现时间、到账时间。
     */
    private function buildWithdrawRule() : array
    {
        $config_model = new WithdrawModel();
        $config_result = $config_model->getConfig();
        $config = $config_result[ 'data' ][ 'value' ] ?? [];
        $is_use = $config_result[ 'data' ][ 'is_use' ] ?? 0;

        // 最低提现额度：支持 0 与两位小数；0 表示不设门槛
        $min = isset($config[ 'min' ]) ? round(floatval($config[ 'min' ]), 2) : 0;
        // 最高提现额度：0 或空表示不限（上限属风控，审核要求明确披露）
        $max = isset($config[ 'max' ]) ? round(floatval($config[ 'max' ]), 2) : 0;
        // 手续费率：与 calculateWithdrawTax() 保持一致
        $rate = (isset($config[ 'rate' ]) && $config[ 'rate' ] !== '') ? floatval($config[ 'rate' ]) : 0.35;
        $is_auto_audit = !empty($config[ 'is_auto_audit' ]) ? 1 : 0;

        $arrive = $is_auto_audit
            ? '提交后系统自动审核通过，1-3 个工作日到账'
            : '提交后人工审核，审核通过后 1-3 个工作日到账';

        $rules = [];
        // 最低门槛仅在后台配置了大于 0 的值时才展示，避免默认出现门槛类文案
        if ($min > 0) {
            $rules[] = [ 'label' => '最低提现金额', 'value' => '单笔最低可提现 ' . $min . ' 元' ];
        }
        $rules[] = [ 'label' => '可提现额度', 'value' => $max > 0
            ? ('单笔最高可提现 ' . $max . ' 元，且不超过可提现余额')
            : '不限额度，可提现金额以账户实际可用余额为准' ];
        $rules[] = [ 'label' => '每日提现次数', 'value' => '每日不限提现次数，可分多次申请' ];
        $rules[] = [ 'label' => '提现时间', 'value' => '全天 00:00-24:00 均可申请提现' ];
        $rules[] = [ 'label' => '到账时间', 'value' => $arrive ];
        $rules[] = [ 'label' => '提现手续费', 'value' => '按提现金额 ' . $rate . '% 收取，从到账金额中内扣' ];
        $rules[] = [ 'label' => '个税代扣', 'value' => '按国家规定代扣：自然月累计提现未超过 800 元的部分免征，超出部分按 20%/30%/40% 阶梯代扣' ];
        $rules[] = [ 'label' => '提现条件', 'value' => '需已绑定企业对公/对私结算卡，并完成实名信息填写' ];

        return [
            'is_use' => $is_use,
            'min' => $min,
            'max' => $max,
            'rate' => $rate,
            'introduction' => $config[ 'introduction' ] ?? '',
            'rules' => $rules,
        ];
    }

    /**
     * 获取转账方式
     * @return false|string
     */
    public function transferType()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        //转账方式
        $withdraw_model = new WithdrawModel();
        $transfer_type_list = $withdraw_model->getTransferType();
        //会员信息
        $member_model = new MemberModel();
        $member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $token[ 'data' ][ 'member_id' ] ] ], 'wx_openid,weapp_openid');

        if (empty($member_info[ 'data' ][ 'wx_openid' ]) && empty($member_info[ 'data' ][ 'weapp_openid' ])) {
            unset($transfer_type_list[ 'wechatpay' ]);
        }
        return $this->response($this->success($transfer_type_list));
    }

    /**
     * 申请提现
     * @return false|string
     */
    public function apply()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) {
            return $this->response($token);
        }

        $apply_money = isset($this->params[ 'apply_money' ]) ? $this->params[ 'apply_money' ] : 0;
        $transfer_type = isset($this->params[ 'transfer_type' ]) ? $this->params[ 'transfer_type' ] : '';//提现方式
        $transfer_type = 'bank';
        $realname = isset($this->params[ 'realname' ]) ? $this->params[ 'realname' ] : '';//真实姓名
        $bank_name = isset($this->params[ 'bank_name' ]) ? $this->params[ 'bank_name' ] : '';//银行名称
        $account_number = isset($this->params[ 'account_number' ]) ? $this->params[ 'account_number' ] : '';//账号名称
        $mobile = isset($this->params[ 'mobile' ]) ? $this->params[ 'mobile' ] : '';//手机号
        $id_card = isset($this->params[ 'id_card' ]) ? $this->params[ 'id_card' ] : '';//身份证号
        $job_type = isset($this->params[ 'job_type' ]) ? $this->params[ 'job_type' ] : '其它';
        $face_auth_status = isset($this->params[ 'face_auth_status' ]) ? $this->params[ 'face_auth_status' ] : 0;
        $face_auth_sn = isset($this->params[ 'face_auth_sn' ]) ? $this->params[ 'face_auth_sn' ] : '';
        $app_type = $this->params[ 'app_type' ] ?? 'weapp';
        $withdraw_model = new WithdrawModel();
        $transfer_type_list = $withdraw_model->getTransferType();
        if (!array_key_exists($transfer_type, $transfer_type_list)) {
            return $this->response($this->error('', '提现方式未开启'));
        }
        $data = array (
            "member_id" => $token[ 'data' ][ 'member_id' ],
            "transfer_type" => $transfer_type,
            "realname" => $realname,
            "bank_name" => $bank_name,
            "account_number" => $account_number,
            "apply_money" => $apply_money,
            "mobile" => $mobile,
            "id_card" => $id_card,
            "job_type" => $job_type,
            "face_auth_status" => $face_auth_status,
            "face_auth_sn" => $face_auth_sn,
            "app_type" => $app_type
        );
        $result = $withdraw_model->apply($data);
        return $this->response($result);
    }

    /**
     * 实时试算提现手续费与预扣个税
     */
    public function calculateTax()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $apply_money = floatval($this->params['apply_money'] ?? 0);
        $withdraw_model = new WithdrawModel();
        $res = $withdraw_model->calculateWithdrawTax($apply_money, $token['data']['member_id']);
        return $this->response($this->success($res));
    }

    /**
     * 提现详情
     * @return mixed
     */
    public function detail()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }
        $condition = [
            [ "member_id", "=", $token[ 'data' ][ 'member_id' ] ],
            [ "id", "=", $id ]
        ];
        $withdraw_model = new WithdrawModel();
        $info = $withdraw_model->getMemberWithdrawDetail($condition);
        return $this->response($info);
    }

    /**
     * 提现记录
     * @return mixed
     */
    public function page()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;

        $condition = [
            [ "member_id", "=", $token[ 'data' ][ 'member_id' ] ]
        ];
        $withdraw_model = new WithdrawModel();
        $list = $withdraw_model->getMemberWithdrawPageList($condition, $page, $page_size, "apply_time desc", "id,withdraw_no,apply_money,apply_time,status,status_name,transfer_type_name");
        return $this->response($list);
    }

}