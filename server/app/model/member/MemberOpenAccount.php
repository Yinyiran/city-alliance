<?php


namespace app\model\member;

use app\model\pay\Huifu;
use think\facade\Cache;
use app\model\BaseModel;

/**
 * 开户
 */
class MemberOpenAccount extends BaseModel
{

    public function addOpenAccount($data)
    {
        $huifu_model = new Huifu();

        $huifu_data = [
            'name'=>$data['realname'],
            'mobile_no'=>$data['mobile_no'],
            'cert_no'=>$data['cert_no'],
            'cert_validity_type'=>$data['cert_validity_type'],
            'cert_begin_date'=>$data['cert_begin_date'],
            'cert_end_date'=>$data['cert_end_date'],
        ];

        $result = $huifu_model->personalAccountOpening($huifu_data);
        //dump($result);
        if ($result[ "code" ] < 0)
            return $result;
        //$data['huifu_id'] = '6666000176915123';
        $data['huifu_id'] = $result['data']['huifu_id'] ?? '';
        //$data['login_name'] = $result['data']['login_name'] ?? '';
        //$data['login_password'] = $result['data']['login_password'] ?? '';

        $req_seq_id = date("YmdHis");
        $req_date = date("Ymd");
        $return_url = addon_url("pay/huifu/membernotify");

        $open_data = [
            'huifu_id'=>$data['huifu_id'],
            'req_date'=>$req_date,
            'req_seq_id'=>$req_seq_id,
            'card_type'=>$data['card_type'],
            'card_no'=>$data['card_no'],
            'card_name'=>$data['card_name'],
            'prov_id'=>$data['prov_id'],
            'area_id'=>$data['area_id'],
            'cert_no'=>$data['cert_no'],
            'cert_validity_type'=>$data['cert_validity_type'],
            'cert_begin_date'=>$data['cert_begin_date'],
            'cert_end_date'=>$data['cert_end_date'],
            'return_url'=>$return_url,
        ];

        $use_open_res = $huifu_model->userBusiOpen($open_data);
        if ($use_open_res[ "code" ] < 0)
            return $use_open_res;

        $data['apply_no'] = $use_open_res['data']['apply_no'] ?? '';
        if (!$data['apply_no']){
            $data['apply_status'] = 2;
        }
        $data['token_no'] = $use_open_res['data']['token_no'] ?? '';
        //$resp_business = json_decode($use_open_res['data']['resp_business']);
        $data['req_seq_id'] = $req_seq_id;
        $data['req_date'] = $req_date;
        $res = model('member_open_account')->add($data);

        return $this->success($res);
    }

    public function editOpenAccount($data, $condition)
    {

        $res = model('member_open_account')->update($data, $condition);

        return $this->success($res);
    }

    public function deleteOpenAccount($condition)
    {
        $res = model('member_open_account')->delete($condition);

        return $this->success($res);
    }

    public function getOpenAccountInfo($condition = [], $field = '*')
    {

        $info = model('member_open_account')->getInfo($condition, $field);

        return $this->success($info);
    }

    public function getOpenAccountList($condition = [], $field = '*', $order = 'create_time desc', $limit = null)
    {

        $list = model('member_open_account')->getList($condition, $field, $order, '', '', '', $limit);

        return $this->success($list);
    }

    public function getOpenAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'a.create_time desc', $field = 'a.*',$alias='a',$join=[])
    {
        $list = model('member_open_account')->pageList($condition, $field, $order, $page, $page_size,$alias,$join);
        return $this->success($list);
    }

}