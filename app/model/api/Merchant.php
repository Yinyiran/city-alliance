<?php


namespace app\model\api;


use app\api\controller\BaseApi;
use app\model\BaseModel;
use app\model\pay\Huifu;
use app\model\shop\OpenAccount;

class Merchant extends BaseModel
{
    //企业开户
    public function openAccount($data)
    {
        $shop_model = new OpenAccount();
        $condition = [
            ['site_id', '=', $data['site_id']]
        ];
        $info =  model('open_account')->getInfo($condition);
        if ($info) { //修改
            $condition[] = ['account_id'=>$info['data']['account_id']];
            $res = $shop_model->editOpenAccount($data,$condition);
        } else { //新增
            $res = $shop_model->addOpenAccount($data);
        }
        return $res;
    }
    
    //用户业务入驻
    public function openCard($data)
    {
        $shop_model = new OpenAccount();
        $condition = array(
            ["site_id", "=", $data['site_id']]
        );

        $info = $shop_model->getOpenAccountInfo($condition);
        $condition[] = ['account_id'=>$info['data']['account_id']];

        $res = $shop_model->opencard($data,$condition,$info['data']['huifu_id']);
        return $res;
    }

    //总行银行号
    public function bankCode($data)
    {
        $huifu_model = new Huifu();
        $bank_list = $huifu_model->getBankCode();
        return $this->success($bank_list);
    }

    //联行号
    public function branchCode($data)
    {
        $open_account_model = new OpenAccount();
        $condition = [
            ['bank_num', '=', $data['bank_num']]
        ];
        $serial_name = $data['serial_name'];
        if ($serial_name) {
            $condition[] = [ 'serial_name', 'like', '%' . $serial_name . '%' ];
        }
        $list = $open_account_model->getBranchPageList($condition, $data['page'], $data['page_size']);
        return $list;
    }
    
    //公司开户详情
    public function openAccountDetail($data)
    {
        $condition = [
            ['site_id', '=', $data['site_id']]
        ];
        $result = model('open_account')->getInfo($condition);
        return $this->success($result);
    }

    //关联企业列表-新方法-根据员工表主理人手机号
    public function shopList($data)
    {
        $employee = $data['employee'];
        //所有这个手机号的主理人
        $shop_ids = model('shop_employee')->getColumn([
            ['mobile', '=', $employee['mobile']],
            ['is_deleted', '=', 0],
            ['is_manager', '=', 1]
        ], 'shop_id');
        $condition = [];

        $alias = 'shop';
        $join = [
            ['store s', 'shop.site_id = s.site_id', 'inner'],
        ];
        $order = 'shop.site_id desc';
        $condition[] = ['shop.site_id', 'in', $shop_ids];
        $field = 's.store_id,s.site_id,s.store_name,s.store_image,s.store_logo';
        $list = model('shop')->getList($condition, $field, $order, $alias, $join);
        foreach ($list as $key => $value) {
            //团队人数
            $team_num = $this->teamNum($value['site_id']);
            $list[$key]['team_num'] = $team_num;
        }
        return $this->success($list);
    }

    //老方法-根据shop表关联
    public function shopListOld($data)
    {
        $info = model('shop')->getInfo(['site_id' => $data['site_id']]);
        $shop_mobile = $info['shop_mobile'];
        $condition = [];
        if (empty($shop_mobile)) {
            $condition[] = ['shop.site_id', '=', -1];
        }

        $alias = 'shop';
        $join = [
            ['store s', 'shop.site_id = s.site_id', 'inner'],
        ];
        $order = 'shop.site_id desc';
        $condition[] = ['shop.shop_mobile', '=', $shop_mobile];
        $field = 's.store_id,s.site_id,s.store_name,s.store_image,s.store_logo';
        $list = model('shop')->getList($condition, $field, $order, $alias, $join);
        foreach ($list as $key => $value) {
            //团队人数
            $team_num = $this->teamNum($value['site_id']);
            $list[$key]['team_num'] = $team_num;
        }
        return $this->success($list);
    }

    //团队人数
    public function teamNum($site_id)
    {
        return model('shop_employee')->getCount([
            ['shop_id', '=', $site_id],
            ['is_deleted', '=', 0],
        ]);
    }

    //切换商家
    public function shopLogin($data)
    {
        $employee = model('shop_employee')->getInfo([
            ['shop_id', '=', $data['new_site_id']],
            ['is_deleted', '=', 0],
            ['member_id', '=', $data['member_id']],
            ['is_manager', '=', 1],
        ]);
        if (!$employee) {
            return $this->error('', '不是主理人身份');
        }

        //生成token
        $token =(new BaseApi())->createToken($data['member_id']);
        return $this->success(['token' => $token, 'site_id' => $data['new_site_id']]);

    }

}