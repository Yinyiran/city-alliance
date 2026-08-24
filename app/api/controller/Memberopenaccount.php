<?php


namespace app\api\controller;

use app\model\member\MemberOpenAccount as MemberOpenAccountModel;

class Memberopenaccount extends BaseApi
{

    //提交认证
    public function add()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $realname = isset($this->params['realname']) ? $this->params['realname'] : '';
        $mobile_no = isset($this->params['mobile_no']) ? $this->params['mobile_no'] : '';
        $cert_no = isset($this->params['cert_no']) ? $this->params['cert_no'] : '';
        $cert_validity_type = isset($this->params['cert_validity_type']) ? $this->params['cert_validity_type'] : 0;
        $cert_begin_date = isset($this->params['cert_begin_date']) ? $this->params['cert_begin_date'] : '';
        $cert_end_date = isset($this->params['cert_end_date']) ? $this->params['cert_end_date'] : '';
        //$card_type = isset($this->params['card_type']) ? $this->params['card_type'] : 1;
        $card_no = isset($this->params['card_no']) ? $this->params['card_no'] : '';
        $card_name = isset($this->params['card_name']) ? $this->params['card_name'] : '';
        $prov_id = isset($this->params['prov_id']) ? $this->params['prov_id'] : 0;
        $area_id = isset($this->params['area_id']) ? $this->params['area_id'] : 0;
        if (empty($realname)) {
            return $this->response($this->error('', '请输入个人姓名'));
        }
        if (empty($mobile_no)) {
            return $this->response($this->error('', '请输入手机号'));
        }
        if (empty($cert_no)) {
            return $this->response($this->error('', '请输入个人证件号码'));
        }
        if ($cert_validity_type==0){
            if (empty($cert_begin_date)) {
                return $this->response($this->error('', '请选择证件有效期开始日期'));
            }
            if (empty($cert_end_date)) {
                return $this->response($this->error('', '请选择证件有效期结束日期'));
            }
        }
        if (empty($card_no)) {
            return $this->response($this->error('', '请输入卡号'));
        }
        if (empty($card_name)) {
            return $this->response($this->error('', '请输入持卡人姓名'));
        }
        if (empty($prov_id)) {
            return $this->response($this->error('', '请选择银行所在省'));
        }
        if (empty($area_id)) {
            return $this->response($this->error('', '请选择银行所在市'));
        }
        $open_account_model = new MemberOpenAccountModel();

        $info = $open_account_model->getOpenAccountInfo([['member_id','=',$this->member_id]]);

        if ($info['data']){
            return $this->response($this->error('', '请勿重复提交'));
        }

        $data = [
            'member_id'=>$this->member_id,
            'realname'=>$realname,
            'mobile_no'=>$mobile_no,
            'cert_no'=>$cert_no,
            'cert_validity_type'=>$cert_validity_type,
            'cert_begin_date'=>$cert_begin_date,
            'cert_end_date'=>$cert_end_date,
            'card_type'=>1,
            'card_no'=>$card_no,
            'card_name'=>$card_name,
            'prov_id'=>$prov_id,
            'area_id'=>$area_id,
            'createtime'=>time()
        ];

        $res = $open_account_model->addOpenAccount($data);

        return $this->response($res);
    }

    //认证信息
    public function info()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $open_account_model = new MemberOpenAccountModel();

        $info = $open_account_model->getOpenAccountInfo([['member_id','=',$this->member_id]]);

        return $this->response($info);
    }

    //修改银行卡信息
    public function edit()
    {

    }
}