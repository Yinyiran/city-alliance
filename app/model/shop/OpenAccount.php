<?php


namespace app\model\shop;

use app\model\pay\Huifu;
use think\facade\Cache;
use app\model\BaseModel;

/**
 * 开户
 */
class OpenAccount extends BaseModel
{

    /**
     * 添加会员开户
     *
     * @param array $data
     */
    public function addOpenAccount($data)
    {
        $huifu_model = new Huifu();

        $huifu_data = [
            'reg_name'=>$data['enterprise_name'],
            'license_code'=>$data['business_licence_number'],
            'license_validity_type'=>$data['business_license_type'],
            'license_begin_date'=>$data['business_license_start_time'],
            'license_end_date'=>$data['business_license_end_time'],
            'reg_prov_id'=>$data['province_id'],
            'reg_area_id'=>$data['city_id'],
            'reg_district_id'=>$data['district_id'],
            'reg_detail'=>$data['full_address'].$data['address'],
            'legal_name'=>$data['legal_name'],
            'legal_cert_type'=>'00',
            'legal_cert_no'=>$data['id_card_num'],
            'legal_cert_validity_type'=>$data['id_card_time_type'],
            'legal_cert_begin_date'=>$data['id_card_start_time'],
            'legal_cert_end_date'=>$data['id_card_end_time'],
            'contact_name'=>$data['contact_name'],
            'contact_mobile'=>$data['contact_mobile'],
        ];

        $result = $huifu_model->businessAccountOpening($huifu_data);

        if ($result[ "code" ] < 0)
            return $result;
        $data['huifu_id'] = $result['data']['huifu_id'];
        $data['login_name'] = $result['data']['login_name'] ?? '';
        $data['login_password'] = $result['data']['login_password'] ?? '';



        $res = model('open_account')->add($data);


        return $this->success($res);
    }

    /**
     * 修改会员标签
     *
     * @param array $data
     * @param array $condition
     */
    public function editOpenAccount($data, $condition)
    {

        $res = model('open_account')->update($data, $condition);

        return $this->success($res);
    }

    public function opencard($data, $condition,$huifu_id)
    {
        $huifu_model = new Huifu();

        $req_seq_id = date("YmdHis");
        $req_date = date("Ymd");
        $return_url = addon_url("pay/huifu/shopnotify");

        $open_data = [
            'huifu_id'=>$huifu_id,
            'req_date'=>$req_date,
            'req_seq_id'=>$req_seq_id,
            'card_type'=>$data['card_type'],
            'card_no'=>$data['card_no'],
            'prov_id'=>$data['prov_id'],
            'area_id'=>$data['area_id'],
            'bank_code'=>$data['bank_code'],
            'branch_code'=>$data['branch_code'],
            'cert_no'=>$data['cert_no'],
            'cert_validity_type'=>$data['cert_validity_type'],
            'cert_begin_date'=>$data['cert_begin_date'],
            'cert_end_date'=>$data['cert_end_date'],
            'return_url'=>$return_url
        ];

        $use_open_res = $huifu_model->userBusiOpen($open_data);

        if ($use_open_res[ "code" ] < 0)
            return $use_open_res;

        $data['apply_no'] = $use_open_res['data']['apply_no'] ?? '';

        $res = model('open_account')->update($data, $condition);

        return $this->success($res);
    }

    /**
     * 删除会员标签
     * @param array $condition
     */
    public function deleteOpenAccount($condition)
    {
        $res = model('open_account')->delete($condition);

        return $this->success($res);
    }


    /**
     * 获取会员标签信息
     *
     * @param array $condition
     * @param string $field
     */
    public function getOpenAccountInfo($condition = [], $field = '*')
    {

        $info = model('open_account')->getInfo($condition, $field);

        return $this->success($info);
    }

    /**
     * 获取会员标签列表
     *
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getOpenAccountList($condition = [], $field = '*', $order = 'create_time desc', $limit = null)
    {

        $list = model('open_account')->getList($condition, $field, $order, '', '', '', $limit);

        return $this->success($list);
    }

    /**
     * 获取会员标签分页列表
     *
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getOpenAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'a.create_time desc', $field = 'a.*',$alias='a',$join=[])
    {
        $list = model('open_account')->pageList($condition, $field, $order, $page, $page_size,$alias,$join);
        return $this->success($list);
    }


    public function getBranchPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'createtime desc',$field='*')
    {
        $list = model('branch_code')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

}