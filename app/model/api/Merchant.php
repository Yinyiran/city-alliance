<?php


namespace app\model\api;


use app\api\controller\BaseApi;
use app\model\BaseModel;
use app\model\pay\Huifu;
use app\model\shop\OpenAccount;
use think\facade\Db;

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

    //关联企业列表-根据当前用户（老板+店员+归属）
    public function shopList($data)
    {
        $member_id = $data['member_id'] ?? 0;
        $employee = $data['employee'] ?? [];
        $mobile = $employee['mobile'] ?? '';

        if (empty($mobile) && $member_id > 0) {
            $member = Db::name('member')->where('member_id', $member_id)->field('mobile,site_id')->find();
            $mobile = trim($member['mobile'] ?? '');
            $belong_site_id = (int)($member['site_id'] ?? 0);
        } else {
            $belong_site_id = 0;
            if ($member_id > 0) {
                $belong_site_id = (int)Db::name('member')->where('member_id', $member_id)->value('site_id');
            }
        }

        // 1. 我是老板的店铺
        $owner_site_ids = [];
        if ($member_id > 0) {
            $created_shop_ids = Db::name('shop')->where('member_id', $member_id)->column('site_id');
            if (!empty($created_shop_ids)) {
                $owner_site_ids = array_merge($owner_site_ids, $created_shop_ids);
            }
        }
        if (!empty($mobile)) {
            $mobile_shop_ids = Db::name('shop')->where(function ($q) use ($mobile) {
                $q->where('mobile', $mobile)->whereOr('telephone', $mobile);
            })->column('site_id');
            if (!empty($mobile_shop_ids)) {
                $owner_site_ids = array_merge($owner_site_ids, $mobile_shop_ids);
            }
        }
        // 员工表中被设为主理人 (is_manager = 1)
        $manager_cond = [['is_deleted', '=', 0], ['is_manager', '=', 1]];
        if (!empty($mobile) && $member_id > 0) {
            $emp_manager_ids = Db::name('shop_employee')->where($manager_cond)->where(function ($q) use ($member_id, $mobile) {
                $q->where('member_id', $member_id)->whereOr('mobile', $mobile);
            })->column('shop_id');
        } elseif (!empty($mobile)) {
            $emp_manager_ids = Db::name('shop_employee')->where($manager_cond)->where('mobile', $mobile)->column('shop_id');
        } elseif ($member_id > 0) {
            $emp_manager_ids = Db::name('shop_employee')->where($manager_cond)->where('member_id', $member_id)->column('shop_id');
        } else {
            $emp_manager_ids = [];
        }
        if (!empty($emp_manager_ids)) {
            $owner_site_ids = array_merge($owner_site_ids, $emp_manager_ids);
        }
        $owner_site_ids = array_values(array_unique(array_filter(array_map('intval', $owner_site_ids))));

        // 2. 我是店员的店铺 (is_manager = 0，排除老板店铺)
        $staff_site_ids = [];
        $staff_cond = [['is_deleted', '=', 0], ['is_manager', '=', 0]];
        if (!empty($mobile) && $member_id > 0) {
            $emp_staff_ids = Db::name('shop_employee')->where($staff_cond)->where(function ($q) use ($member_id, $mobile) {
                $q->where('member_id', $member_id)->whereOr('mobile', $mobile);
            })->column('shop_id');
        } elseif (!empty($mobile)) {
            $emp_staff_ids = Db::name('shop_employee')->where($staff_cond)->where('mobile', $mobile)->column('shop_id');
        } elseif ($member_id > 0) {
            $emp_staff_ids = Db::name('shop_employee')->where($staff_cond)->where('member_id', $member_id)->column('shop_id');
        } else {
            $emp_staff_ids = [];
        }
        if (!empty($emp_staff_ids)) {
            foreach ($emp_staff_ids as $sid) {
                $sid = (int)$sid;
                if ($sid > 0 && !in_array($sid, $owner_site_ids)) {
                    $staff_site_ids[] = $sid;
                }
            }
        }
        $staff_site_ids = array_values(array_unique($staff_site_ids));

        // 3. 我所归属的店铺
        $belong_site_ids = [];
        if ($belong_site_id > 0 && !in_array($belong_site_id, $owner_site_ids) && !in_array($belong_site_id, $staff_site_ids)) {
            $belong_site_ids[] = $belong_site_id;
        }

        $all_site_ids = array_values(array_unique(array_merge($owner_site_ids, $staff_site_ids, $belong_site_ids)));
        if (empty($all_site_ids)) {
            return $this->success([]);
        }

        // 查询店铺详情
        $shop_list = Db::name('shop')
            ->whereIn('site_id', $all_site_ids)
            ->field('site_id, site_name, logo, avatar, mobile, telephone, address, full_address, shop_status')
            ->select()
            ->toArray();

        // 查询首家门店
        $store_map = [];
        $stores = Db::name('store')
            ->whereIn('site_id', $all_site_ids)
            ->field('store_id, site_id, store_name, store_image, store_logo, address, full_address')
            ->order('store_id asc')
            ->select()
            ->toArray();
        foreach ($stores as $st) {
            $sid = (int)$st['site_id'];
            if (!isset($store_map[$sid])) {
                $store_map[$sid] = $st;
            }
        }

        $result_list = [];
        foreach ($shop_list as $shop) {
            $sid = (int)$shop['site_id'];
            $store_info = $store_map[$sid] ?? null;

            if (in_array($sid, $owner_site_ids)) {
                $role = 'owner';
                $role_name = '我的店铺(老板)';
                $role_sort = 1;
                $is_manager = 1;
            } elseif (in_array($sid, $staff_site_ids)) {
                $role = 'staff';
                $role_name = '我是店员';
                $role_sort = 2;
                $is_manager = 0;
            } else {
                $role = 'member';
                $role_name = '我的归属';
                $role_sort = 3;
                $is_manager = 0;
            }

            $img = '';
            if ($store_info && !empty($store_info['store_image'])) {
                $img = $store_info['store_image'];
            } elseif ($store_info && !empty($store_info['store_logo'])) {
                $img = $store_info['store_logo'];
            } elseif (!empty($shop['logo'])) {
                $img = $shop['logo'];
            } elseif (!empty($shop['avatar'])) {
                $img = $shop['avatar'];
            }

            $name = ($store_info['store_name'] ?? '') ?: ($shop['site_name'] ?: '商家店铺');

            $result_list[] = [
                'site_id' => $sid,
                'store_id' => $store_info['store_id'] ?? 0,
                'store_name' => $name,
                'site_name' => $shop['site_name'] ?: $name,
                'store_image' => $img,
                'store_logo' => $shop['logo'] ?: $img,
                'role' => $role,
                'role_name' => $role_name,
                'role_sort' => $role_sort,
                'is_manager' => $is_manager,
                'team_num' => $this->teamNum($sid)
            ];
        }

        usort($result_list, function ($a, $b) {
            if ($a['role_sort'] === $b['role_sort']) {
                return $b['site_id'] <=> $a['site_id'];
            }
            return $a['role_sort'] <=> $b['role_sort'];
        });

        return $this->success($result_list);
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
        $new_site_id = (int)($data['new_site_id'] ?? 0);
        $member_id = (int)($data['member_id'] ?? 0);
        if ($new_site_id <= 0) {
            return $this->error('', '店铺ID不合法');
        }

        $shop = Db::name('shop')->where('site_id', $new_site_id)->find();
        if (empty($shop)) {
            return $this->error('', '店铺不存在');
        }

        // 验证用户是否有权限切换到该店铺
        $member = Db::name('member')->where('member_id', $member_id)->field('mobile')->find();
        $mobile = trim($member['mobile'] ?? '');

        // 1. 是否是老板
        $is_owner = ($shop['member_id'] == $member_id) || (!empty($mobile) && ($shop['mobile'] == $mobile || $shop['telephone'] == $mobile));

        // 2. 是否是该店铺员工
        $employee = Db::name('shop_employee')->where([
            ['shop_id', '=', $new_site_id],
            ['is_deleted', '=', 0]
        ])->where(function ($q) use ($member_id, $mobile) {
            $q->where('member_id', $member_id);
            if (!empty($mobile)) {
                $q->whereOr('mobile', $mobile);
            }
        })->find();

        if ($is_owner) {
            $is_manager = 1;
        } elseif (!empty($employee)) {
            $is_manager = $employee['is_manager'] ?? 0;
        } else {
            $belong_id = Db::name('member')->where('member_id', $member_id)->value('site_id');
            if ($belong_id == $new_site_id) {
                $is_manager = 0;
            } else {
                return $this->error('', '您不属于该店铺，无权切换');
            }
        }

        // 生成token
        $token = (new BaseApi())->createToken($member_id);
        session('site_id', $new_site_id);
        session('shop_id', $new_site_id);

        return $this->success([
            'token' => $token,
            'site_id' => $new_site_id,
            'is_manager' => $is_manager,
            'site_name' => $shop['site_name']
        ]);
    }
}