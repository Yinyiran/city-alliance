<?php
/**

 */

namespace app\shopapplet\controller;


use addon\city\admin\controller\Shop;
use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use think\facade\Db;


class Common extends BaseApi
{
    public $employeeModel;
    public $shopModel;
    public $member_id;
    public $site_id;
    public $city_id;
    public $shopinfo;

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->employeeModel = new ShopEmployeeModel();
        $this->shopModel=new ShopModel();

        if(empty(request()->param("token"))){
           echo  $this->response([
              'code' => -10010,
              'msg' => 'token不能为空',
              'data' => [],
          ]);
            exit ;
        }
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0){
          echo $this->response($token);
          exit;
        }
        $this->member_id = $token['data']['member_id'];

        $memberinfo = Db::name("member")->where("member_id", $this->member_id)->find();
        $mobile = trim($memberinfo["mobile"] ?? '');

        $site_id = (int)($this->params['site_id'] ?? 0);
        if ($site_id <= 0) {
            $site_id = (int)session('site_id');
        }

        // 查找用户在该店铺是否合法（老板或员工）
        $is_owner = false;
        $employeeinfo = null;

        if ($site_id > 0) {
            $shop = Db::name('shop')->where('site_id', $site_id)->find();
            if (!empty($shop)) {
                if ($shop['member_id'] == $this->member_id || (!empty($mobile) && ($shop['mobile'] == $mobile || $shop['telephone'] == $mobile))) {
                    $is_owner = true;
                }
            }
            $emp_check = Db::name('shop_employee')->where([
                ['shop_id', '=', $site_id],
                ['is_deleted', '=', 0]
            ])->where(function($q) use ($mobile, $memberinfo) {
                $q->where('member_id', $memberinfo['member_id']);
                if (!empty($mobile)) {
                    $q->whereOr('mobile', $mobile);
                }
            })->find();
            if (!empty($emp_check)) {
                $employeeinfo = $emp_check;
            }
        }

        // 若未指定有效 site_id，自动查找名下首个店铺（优先老板店铺）
        if (!$is_owner && empty($employeeinfo)) {
            $owner_shop = null;
            if (!empty($mobile)) {
                $owner_shop = Db::name('shop')->where('member_id', $this->member_id)->whereOr('mobile', $mobile)->whereOr('telephone', $mobile)->find();
            } else {
                $owner_shop = Db::name('shop')->where('member_id', $this->member_id)->find();
            }
            if (!empty($owner_shop)) {
                $site_id = (int)$owner_shop['site_id'];
                $is_owner = true;
                $emp_check = Db::name('shop_employee')->where([
                    ['shop_id', '=', $site_id],
                    ['is_deleted', '=', 0]
                ])->where(function($q) use ($mobile, $memberinfo) {
                    $q->where('member_id', $memberinfo['member_id']);
                    if (!empty($mobile)) {
                        $q->whereOr('mobile', $mobile);
                    }
                })->find();
                if (!empty($emp_check)) {
                    $employeeinfo = $emp_check;
                }
            } else {
                $emp = Db::name('shop_employee')->where([['is_deleted', '=', 0]])
                    ->where(function($q) use ($mobile, $memberinfo) {
                        $q->where('member_id', $memberinfo['member_id']);
                        if (!empty($mobile)) {
                            $q->whereOr('mobile', $mobile);
                        }
                    })->order('is_manager desc, employee_id asc')->find();
                if (!empty($emp)) {
                    $site_id = (int)$emp['shop_id'];
                    $employeeinfo = $emp;
                }
            }
        }

        if (!$is_owner && empty($employeeinfo)) {
            echo $this->response([
                'code' => -10020,
                'msg' => '当前账号未开通企业端权限，请联系企业管理员在后台添加员工后重试',
                'data' => [],
            ]);
            exit;
        }

        if (!empty($employeeinfo) && empty($employeeinfo["member_id"])) {
            Db::name("shop_employee")->where("employee_id", $employeeinfo['employee_id'])->update(["member_id" => $this->member_id]);
            $employeeinfo["member_id"] = $this->member_id;
        }

        if ($is_owner) {
            Db::name("shop")->where("site_id", $site_id)->where("member_id", 0)->update(["member_id" => $this->member_id]);
            if (empty($employeeinfo) || empty($employeeinfo['employee_id'])) {
                $exist_emp = Db::name('shop_employee')->where([
                    ['shop_id', '=', $site_id],
                    ['is_deleted', '=', 0]
                ])->where(function($q) use ($mobile) {
                    $q->where('member_id', $this->member_id);
                    if (!empty($mobile)) {
                        $q->whereOr('mobile', $mobile);
                    }
                })->order('is_manager desc, employee_id asc')->find();

                if (!empty($exist_emp)) {
                    $employeeinfo = $exist_emp;
                    if (empty($exist_emp['member_id']) || $exist_emp['member_id'] != $this->member_id) {
                        Db::name('shop_employee')->where('employee_id', $exist_emp['employee_id'])->update(['member_id' => $this->member_id]);
                        $employeeinfo['member_id'] = $this->member_id;
                    }
                } else {
                    // 加锁防止并发请求同时进入创建
                    $lock_key = 'shop_emp_create_' . $site_id . '_' . $this->member_id;
                    $lock = \think\facade\Cache::get($lock_key);
                    if (!$lock) {
                        \think\facade\Cache::set($lock_key, 1, 5);
                        // 双重检查
                        $recheck = Db::name('shop_employee')->where([
                            ['shop_id', '=', $site_id],
                            ['is_deleted', '=', 0]
                        ])->where(function($q) use ($mobile) {
                            $q->where('member_id', $this->member_id);
                            if (!empty($mobile)) {
                                $q->whereOr('mobile', $mobile);
                            }
                        })->order('is_manager desc, employee_id asc')->find();

                        if (!empty($recheck)) {
                            $employeeinfo = $recheck;
                        } else {
                            $new_id = Db::name("shop_employee")->insertGetId([
                                'shop_id' => $site_id,
                                'member_id' => $this->member_id,
                                'username' => !empty($memberinfo['nickname']) ? $memberinfo['nickname'] : (!empty($memberinfo['username']) ? $memberinfo['username'] : '老板'),
                                'mobile' => $mobile,
                                'headimg' => $memberinfo['headimg'] ?? '',
                                'is_manager' => 1,
                                'employee_status' => 0,
                                'is_deleted' => 0,
                                'create_time' => time()
                            ]);
                            $employeeinfo = [
                                'employee_id' => $new_id,
                                'shop_id' => $site_id,
                                'username' => !empty($memberinfo['nickname']) ? $memberinfo['nickname'] : '老板',
                                'mobile' => $mobile,
                                'is_manager' => 1,
                                'member_id' => $this->member_id
                            ];
                        }
                        \think\facade\Cache::delete($lock_key);
                    } else {
                        usleep(100000);
                        $employeeinfo = Db::name('shop_employee')->where([
                            ['shop_id', '=', $site_id],
                            ['is_deleted', '=', 0]
                        ])->where(function($q) use ($mobile) {
                            $q->where('member_id', $this->member_id);
                            if (!empty($mobile)) {
                                $q->whereOr('mobile', $mobile);
                            }
                        })->order('is_manager desc, employee_id asc')->find();
                    }
                }
            }

            if (!empty($employeeinfo) && $employeeinfo['is_manager'] != 1) {
                Db::name("shop_employee")->where("employee_id", $employeeinfo['employee_id'])->update(["is_manager" => 1]);
                $employeeinfo['is_manager'] = 1;
            }

            // 同步店铺主理人关联
            if (!empty($employeeinfo['employee_id'])) {
                Db::name('shop')->where('site_id', $site_id)->where('zlr_employee_id', 0)->update(['zlr_employee_id' => $employeeinfo['employee_id']]);
            }

            // 防御性自愈：如果该店铺存在相同手机号的历史重复员工记录，清理多余项（保留主理人/最早的那条）
            if (!empty($mobile)) {
                $dups = Db::name('shop_employee')
                    ->where('shop_id', $site_id)
                    ->where('mobile', $mobile)
                    ->where('is_deleted', 0)
                    ->order('is_manager desc, employee_id asc')
                    ->column('employee_id');
                if (count($dups) > 1) {
                    $keep_id = array_shift($dups);
                    Db::name('shop_employee')->whereIn('employee_id', $dups)->delete();
                }
            }
        }

        $shopdata = Db::name("shop")->where("site_id", $site_id)->find();
        if (empty($shopdata)) {
            echo $this->response([
                'code' => -10021,
                'msg' => '店铺不存在或已停用',
                'data' => [],
            ]);
            exit;
        }

        $this->site_id = $site_id;
        $this->city_id = $shopdata["website_id"] ?? ($shopdata["city"] ?? 0);
        $this->shopinfo = $shopdata;

        session("shopinfo", $this->shopinfo);
        session("city_id", $this->city_id);
        session("site_id", $this->site_id);
        session("shop_id", $this->site_id);
        session("employee_info", $employeeinfo);
    }

    public function getshopInfobyemployee(){
        return [
            "shopinfo" => $this->shopinfo,
            "city_id" => $this->city_id,
            "site_id" => $this->site_id,
            'employee_info' => session('employee_info'),
        ];
    }
}
