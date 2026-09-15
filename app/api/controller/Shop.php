<?php
/**

 */

namespace app\api\controller;

use app\model\shop\Poster;
use app\model\shop\Shop as ShopModel;
use app\model\web\WebSite;
use think\facade\Db;

class Shop extends BaseApi
{

    /**
     * 基础信息
     */
    public function info()
    {
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
        if (empty($site_id)) {
            return $this->response($this->error('', 'REQUEST_SITE_ID'));
        }
        $shop = new ShopModel();
        $field = 'site_id,expire_time,site_name,website_id,is_own,level_name,category_name,shop_status,start_time,logo,avatar,banner,
		seo_description,qq,ww,telephone,shop_desccredit,shop_servicecredit,shop_deliverycredit,workingtime,shop_baozh,shop_baozhopen,shop_baozhrmb,
		shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_free_time,shop_sales,shop_adv,work_week,address,full_address,longitude,latitude,sub_num';
        $info = $shop->getShopInfo([ [ 'site_id', '=', $site_id ] ], $field);
        return $this->response($info);
    }

    public function page()
    {
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        $keyword = isset($this->params[ 'keyword' ]) ? $this->params[ 'keyword' ] : '';//关键词
        $order = isset($this->params[ 'order' ]) ? $this->params[ 'order' ] : "site_id";//排序（综合、销量、信用）
        $sort = isset($this->params[ 'sort' ]) ? $this->params[ 'sort' ] : "desc";//升序、降序
        $web_city = isset($this->params[ 'web_city' ]) ? $this->params[ 'web_city' ] : "";
        $lat = isset($this->params[ 'lat' ]) ? $this->params[ 'lat' ] : ""; // 纬度
        $lng = isset($this->params[ 'lng' ]) ? $this->params[ 'lng' ] : ""; // 经度
        $website_id = $this->params[ 'website_id' ] ?? 0;
        $shop = new ShopModel();
        $condition = [
            [ 'shop_status', '=', 1 ],
            [ 'cert_id', '<>', 0 ]
        ];

        if (!empty($keyword)) {
            $condition[] = [ 'site_name', 'like', '%' . $keyword . '%' ];
        }
        if($website_id > 0){
            $condition[] = [ 'website_id', '=', $website_id];
        }
        // 非法参数进行过滤
        if ($sort != "desc" && $sort != "asc") {
            $sort = "";
        }

        // 非法参数进行过滤
        if ($order != '') {
            if ($order != "shop_sales" && $order != "shop_desccredit") {
                $order = 'site_id';
            }
            $order_by = $order . ' ' . $sort;
        } else {
            $order_by = 'is_recommend desc,sort desc,site_id desc';
        }

        // 查询是否存在城市分站
        if (addon_is_exit('city') && !empty($web_city)) {
            $website_model = new WebSite();
            $website_info = $website_model->getWebSite([ [ 'site_area_id', '=', $web_city ] ], 'site_id');
            if (!empty($website_info[ 'data' ])) {
                $order_by = "INSTR('{$website_info['data']['site_id']}', website_id) desc," . $order_by;
            }
        }
        $list = $shop->getShopPageList($condition, $page, $page_size, $order_by, 'site_id,site_name,category_name,group_name,logo,avatar,banner,seo_description,shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_sales,sub_num,is_own,longitude,latitude,telephone,address,full_address');

        if (!empty($list[ 'data' ][ 'list' ])) {
            foreach ($list[ 'data' ][ 'list' ] as $k => $item) {
                if ($item[ 'longitude' ] && $item[ 'latitude' ] && $lng && $lat) {
                    $list[ 'data' ][ 'list' ][ $k ][ 'distance' ] = round(getDistance((float) $item[ 'longitude' ], (float) $item[ 'latitude' ], (float) $lng, (float) $lat));
                } else {
                    $list[ 'data' ][ 'list' ][ $k ][ 'distance' ] = 0;
                }
            }
        }

        return $this->response($list);
    }

    /**
     * 是否显示店铺相关功能，用于审核小程序
     */
    public function isShow()
    {
        $shop = new ShopModel();
        $res = $shop->getDevelopment();// 0 隐藏，1 显示
        return $this->response($this->success($res));
    }

    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));

        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'suid' ] = $qrcode_param[ 'suid' ] ?? 0;
        $poster = new Poster();
        $res = $poster->shop($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param);
        return $this->response($res);
    }

    /**
     * 获取用户关联的店铺列表（我的店铺[老板] + 我是店员的店铺 + 我所归属的店铺）
     */
    public function myShopList()
    {
        $token_res = $this->checkToken();
        if ($token_res['code'] < 0) {
            return $this->response($token_res);
        }

        $member_id = $this->member_id;
        $member = Db::name('member')->where('member_id', $member_id)->field('member_id,mobile,site_id')->find();
        if (empty($member)) {
            return $this->response($this->error('', 'MEMBER_NOT_EXIST'));
        }

        $mobile = trim($member['mobile'] ?? '');
        $belong_site_id = (int)($member['site_id'] ?? 0);

        // 1. 我的店铺（老板）：shop.member_id = $member_id 或 员工表主理人 is_manager = 1
        $owner_site_ids = [];
        $created_shop_ids = Db::name('shop')->where('member_id', $member_id)->column('site_id');
        if (!empty($created_shop_ids)) {
            $owner_site_ids = array_merge($owner_site_ids, $created_shop_ids);
        }

        $manager_cond = [
            ['is_deleted', '=', 0],
            ['is_manager', '=', 1]
        ];
        if (!empty($mobile)) {
            $emp_manager_ids = Db::name('shop_employee')
                ->where($manager_cond)
                ->where(function ($query) use ($member_id, $mobile) {
                    $query->where('member_id', $member_id)->whereOr('mobile', $mobile);
                })
                ->column('shop_id');
        } else {
            $emp_manager_ids = Db::name('shop_employee')
                ->where($manager_cond)
                ->where('member_id', $member_id)
                ->column('shop_id');
        }
        if (!empty($emp_manager_ids)) {
            $owner_site_ids = array_merge($owner_site_ids, $emp_manager_ids);
        }
        $owner_site_ids = array_values(array_unique(array_filter(array_map('intval', $owner_site_ids))));

        // 2. 我是店员的店铺：shop_employee 表中 is_manager = 0，排除已是老板的店铺
        $staff_site_ids = [];
        $staff_cond = [
            ['is_deleted', '=', 0],
            ['is_manager', '=', 0]
        ];
        if (!empty($mobile)) {
            $emp_staff_ids = Db::name('shop_employee')
                ->where($staff_cond)
                ->where(function ($query) use ($member_id, $mobile) {
                    $query->where('member_id', $member_id)->whereOr('mobile', $mobile);
                })
                ->column('shop_id');
        } else {
            $emp_staff_ids = Db::name('shop_employee')
                ->where($staff_cond)
                ->where('member_id', $member_id)
                ->column('shop_id');
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

        // 3. 我所归属的店铺：member.site_id 以及关注店铺，排除前两类
        $belong_site_ids = [];
        if ($belong_site_id > 0 && !in_array($belong_site_id, $owner_site_ids) && !in_array($belong_site_id, $staff_site_ids)) {
            $belong_site_ids[] = $belong_site_id;
        }
        $follow_site_ids = Db::name('shop_member')
            ->where([['member_id', '=', $member_id], ['is_subscribe', '=', 1]])
            ->column('site_id');
        if (!empty($follow_site_ids)) {
            foreach ($follow_site_ids as $fsid) {
                $fsid = (int)$fsid;
                if ($fsid > 0 && !in_array($fsid, $owner_site_ids) && !in_array($fsid, $staff_site_ids) && !in_array($fsid, $belong_site_ids)) {
                    $belong_site_ids[] = $fsid;
                }
            }
        }
        $belong_site_ids = array_values(array_unique($belong_site_ids));

        $all_site_ids = array_values(array_unique(array_merge($owner_site_ids, $staff_site_ids, $belong_site_ids)));
        if (empty($all_site_ids)) {
            return $this->response($this->success([]));
        }

        // 查询店铺与关联首家默认门店信息
        $shop_list = Db::name('shop')
            ->whereIn('site_id', $all_site_ids)
            ->field('site_id, site_name, logo, avatar, telephone, mobile, address, full_address, longitude, latitude, website_id, is_own, shop_status')
            ->select()
            ->toArray();

        $store_map = [];
        $stores = Db::name('store')
            ->whereIn('site_id', $all_site_ids)
            ->where([['status', '=', 1], ['is_pickup', '=', 1]])
            ->field('store_id, site_id, store_name, store_image, address, full_address, telphone, longitude, latitude')
            ->order('is_default desc, store_id asc')
            ->select()
            ->toArray();
        foreach ($stores as $st) {
            $sid = (int)$st['site_id'];
            if (!isset($store_map[$sid])) {
                $store_map[$sid] = $st;
            }
        }

        $lat = isset($this->params['latitude']) ? (float)$this->params['latitude'] : 0;
        $lng = isset($this->params['longitude']) ? (float)$this->params['longitude'] : 0;

        $result_list = [];
        foreach ($shop_list as $item) {
            $sid = (int)$item['site_id'];

            if (in_array($sid, $owner_site_ids)) {
                $role = 'owner';
                $role_name = '我的店铺';
                $role_sort = 1;
            } elseif (in_array($sid, $staff_site_ids)) {
                $role = 'staff';
                $role_name = '我是店员';
                $role_sort = 2;
            } else {
                $role = 'member';
                $role_name = '我所归属';
                $role_sort = 3;
            }

            $store_info = $store_map[$sid] ?? null;
            $shop_image = '';
            if ($store_info && !empty($store_info['store_image'])) {
                $shop_image = $store_info['store_image'];
            } elseif (!empty($item['logo'])) {
                $shop_image = $item['logo'];
            } elseif (!empty($item['avatar'])) {
                $shop_image = $item['avatar'];
            }

            $shop_name = $item['site_name'] ?: ($store_info['store_name'] ?? '商家店铺');
            $address = $item['full_address'] ?: ($item['address'] ?: ($store_info['full_address'] ?? ($store_info['address'] ?? '')));
            $telephone = $item['telephone'] ?: ($item['mobile'] ?: ($store_info['telphone'] ?? ''));

            $distance_text = '';
            $item_lat = (float)($item['latitude'] ?: ($store_info['latitude'] ?? 0));
            $item_lng = (float)($item['longitude'] ?: ($store_info['longitude'] ?? 0));
            if ($lat > 0 && $lng > 0 && $item_lat > 0 && $item_lng > 0 && function_exists('getDistance')) {
                $dist_num = round(getDistance($item_lng, $item_lat, $lng, $lat));
                if ($dist_num < 1000) {
                    $distance_text = $dist_num . 'm';
                } else {
                    $distance_text = round($dist_num / 1000, 1) . 'km';
                }
            }

            $result_list[] = [
                'site_id' => $sid,
                'store_id' => $store_info['store_id'] ?? 0,
                'site_name' => $shop_name,
                'store_name' => $store_info['store_name'] ?? $shop_name,
                'shop_image' => $shop_image,
                'role' => $role,
                'role_name' => $role_name,
                'role_sort' => $role_sort,
                'telephone' => $telephone,
                'address' => $address,
                'distance' => $distance_text,
                'shop_status' => $item['shop_status'],
            ];
        }

        // 排序：老板(1) > 店员(2) > 归属(3)，次级按 site_id 降序
        usort($result_list, function ($a, $b) {
            if ($a['role_sort'] === $b['role_sort']) {
                return $b['site_id'] <=> $a['site_id'];
            }
            return $a['role_sort'] <=> $b['role_sort'];
        });

        return $this->response($this->success($result_list));
    }
}