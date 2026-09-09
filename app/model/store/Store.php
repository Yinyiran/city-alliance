<?php

namespace app\model\store;


use app\model\BaseModel;
use app\model\shop\Shop;
use app\model\system\Group;
use think\facade\Cache;
use think\facade\Db;
use app\model\store\Store as StoreModel;

/**
 * 门店管理
 */
class Store extends BaseModel
{

    /**
     * 添加门店
     * @param unknown $data
     */
    public function addStore($data, $user_data = [], $is_store = 0)
    {
        $site_id = isset($data[ 'site_id' ]) ? $data[ 'site_id' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        $data[ 'create_time' ] = time();

        $shop_model = new Shop();
        $shop_info_result = $shop_model->getShopInfo([ [ "site_id", "=", $site_id ] ], "site_name");
        $shop_info = $shop_info_result[ "data" ];
        $data[ "site_name" ] = $shop_info[ "site_name" ];
        model('store')->startTrans();

        try {
            if ($is_store == 1) {
                $data[ 'username' ] = $is_store == 1 ? $user_data[ 'username' ] : '';
                $data[ 'store_id' ] = model("site")->add([ 'site_type' => 'store' ]);
                $store_id = model('store')->add($data);
                Cache::tag("store")->clear();
                //添加系统用户组
                $group = new Group();
                $group_data = [
                    'site_id' => $store_id,
                    'app_module' => 'store',
                    'group_name' => '管理员组',
                    'is_system' => 1,
                    'create_time' => time()
                ];
                $group_id = $group->addGroup($group_data)[ 'data' ];

                //用户检测
                if (empty($user_data[ 'username' ])) {
                    model("store")->rollback();
                    return $this->error('', 'USER_NOT_EXIST');
                }
                $user_count = model("user")->getCount([ [ 'username', '=', $user_data[ 'username' ] ], [ 'app_module', '=', 'store' ] ]);
                if ($user_count > 0) {
                    model("store")->rollback();
                    return $this->error('', 'USERNAME_EXISTED');
                }

                //添加用户
                $data_user = [
                    'app_module' => 'store',
                    'app_group' => 0,
                    'is_admin' => 1,
                    'group_id' => $group_id,
                    'group_name' => '管理员组',
                    'site_id' => $store_id
                ];
                $user_info = array_merge($data_user, $user_data);
                model("user")->add($user_info);
            } else {
                $store_id = model('store')->add($data);
            }
            Cache::tag("store")->clear();
            event("AddStore", [ 'store_id' => $store_id ]);
            model('store')->commit();
            return $this->success($store_id);
        } catch (\Exception $e) {

            model('store')->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 修改门店
     * @param unknown $data
     * @return multitype:string
     */
    public function editStore($data, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition[ 'site_id' ]) ? $check_condition[ 'site_id' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        $data[ "modify_time" ] = time();
        $res = model('store')->update($data, $condition);
        Cache::tag("store")->clear();
        return $this->success($res);
    }

    /**
     * 删除门店
     * @param $condition
     * @return array
     */
    public function deleteStore($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition[ 'site_id' ]) ? $check_condition[ 'site_id' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }

        $store_id = isset($check_condition[ 'store_id' ]) ? $check_condition[ 'store_id' ] : '';
        if ($store_id === '') {
            return $this->error('', 'REQUEST_STORE_ID');
        }
        model('store')->startTrans();
        try {
            //删除站点
            model('site')->delete([ [ 'app_module', '=', 'store' ], [ 'site_id', '=', $store_id ] ]);
            //删除门店
            model('store')->delete($condition);
            Cache::tag("store")->clear();
            //删除用户组
            model('group')->delete([ [ 'app_module', '=', 'store' ], [ 'site_id', '=', $store_id ] ]);
            //删除用户
            model('user')->delete([ [ 'app_module', '=', 'store' ], [ 'site_id', '=', $store_id ] ]);

            event("DeleteStore", [ 'store_id' => $store_id ]);

            model('store')->commit();
            return $this->success();

        } catch (\Exception $e) {

            model('store')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * @param $condition
     * @param $is_frozen
     */
    public function frozenStore($condition, $is_frozen)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition[ 'site_id' ]) ? $check_condition[ 'site_id' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        $res = model('store')->update([ 'is_frozen' => $is_frozen == 1 ? 0 : 1 ], $condition);
        Cache::tag("store")->clear();
        return $this->success($res);
    }

    /**
     * 获取门店信息
     * @param array $condition
     * @param string $field
     */
    public function getStoreInfo($condition, $field = '*')
    {
        $data = json_encode([ $condition, $field ]);
        $cache = Cache::get("store_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('store')->getInfo($condition, $field);
        Cache::tag("store")->set("store_" . $data, $res);
        return $this->success($res);
    }

    /**
     * 获取门店列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getStoreList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("store_getStoreList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('store')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("store")->set("store_getStoreList_" . $data, $list);

        return $this->success($list);
    }

    /**
     * 获取门店分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getStorePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $data = json_encode([ $condition, $field, $order, $page, $page_size ]);
        $cache = Cache::get("store_getStorePageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('store')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("store")->set("store_getStorePageList_" . $data, $list);
        return $this->success($list);
    }

    /**
     * 查询门店  带有距离
     * @param $condition
     * @param $lnglat
     */
    public function getLocationStoreList($condition, $field, $lnglat)
    {
        $order = '';
        if ($lnglat[ 'lat' ] !== null && $lnglat[ 'lng' ] !== null) {
            $field .= ' , FORMAT(st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) as distance ';
            $condition[] = [ '', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) < 10000') ];
            $order = 'distance asc';
        }

        $list = model('store')->getList($condition, $field, $order);
        return $this->success($list);
    }

    /**
     * 查询门店  带有距离
     * @param $condition
     * @param $lnglat
     */
    public function getLocationStorePageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $field, $lnglat)
    {
        $order = '';
        if ($lnglat[ 'lat' ] !== null && $lnglat[ 'lng' ] !== null) {
            $field .= ',FORMAT(st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) as distance';
            $condition[] = [ '', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) < 10000') ];
            $order = Db::raw(' st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( longitude, latitude ) ) * 111195 / 1000 asc');
        }
        $list = model('store')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /***
     * 查询门店 带距离 只返回order
     */
    public function getStoreOrder($condition, $field, $lnglat)
    {
        $order = '';
        if ($lnglat[ 'lat' ] !== null && $lnglat[ 'lng' ] !== null) {
            $field .= ',FORMAT(st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( s.longitude, s.latitude ) ) * 111195 / 1000, 2) as distance';
            $condition[] = [ '', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( s.longitude, s.latitude ) ) * 111195 / 1000, 2) < 10000') ];
            $order = Db::raw(' st_distance ( point ( ' . $lnglat[ 'lng' ] . ', ' . $lnglat[ 'lat' ] . ' ), point ( s.longitude, s.latitude ) ) * 111195 / 1000 asc');
        }
        return ['condition'=>$condition, 'field'=>$field, 'order'=>$order];
    }

    /**
     * 删除门店（事件）
     * @param $store_id
     * @return array
     */
    public function cronDeleteStore($store_id)
    {
        model('store_goods')->startTrans();
        try {

            //删除门店商品
            model('store_goods')->delete([ [ 'store_id', '=', $store_id ] ]);
            //删除门店商品规格值
            model('store_goods_sku')->delete([ [ 'store_id', '=', $store_id ] ]);
            //删除门店会员
            model('store_member')->delete([ [ 'store_id', '=', $store_id ] ]);
            //删除门店库存导入记录
            model('store_stock_import')->delete([ [ 'store_id', '=', $store_id ] ]);
            //删除门店库存导入日志
            model('store_stock_import_log')->delete([ [ 'store_id', '=', $store_id ] ]);
            //删除用户日志
            model('user_log')->delete([ [ 'site_id', '=', $store_id ] ]);

            model('store_goods')->commit();
            return $this->success();
        } catch (\Exception $e) {

            model('store_goods')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 门店列表
     */
    public function storeList($data)
    {
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $store_model = new StoreModel();
        $store_condition = [
            ['status', '=', 1],
            ['is_frozen', '=', 0],
            ['is_pickup', '=', 1],
        ];
        if (!empty($keyword)) {
            $store_condition[] = ['store_name|address|full_address', 'like', "%$keyword%"];
        }
        //该城市的商家
        $site_ids = model('shop')->getColumn(['website_id'=>$data['website_id']],'site_id');
        $store_condition[] = ['site_id', 'in', $site_ids];

        $latlng = array(
            'lat' => $latitude ?: null,
            'lng' => $longitude ?: null,
        );
        $field = 'store_id,store_name,telphone,store_image,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc';
        $result = $store_model->getLocationStorePageList($store_condition, $data['page'], $data['page_size'], $field, $latlng); //新增加注释这里查询门店
        if (isset($result['data']['list'])) {
            foreach ($result['data']['list'] as $key => $value) {
                $full_address = str_replace(',', '', $value['full_address']) . $value['address'];
                $result['data']['list'][$key]['address'] = $full_address;
                //经纬度
                $result['data']['list'][$key]['distance'] = '0km';
                $result['data']['list'][$key]['distance_value'] = 0;
                if ($longitude && $latitude && !empty($value['longitude']) && !empty($value['latitude'])) {
                    //计算距离
                    $distance = getDistance2($longitude, $latitude, $value['longitude'], $value['latitude']);
                    $result['data']['list'][$key]['distance'] = $distance . 'km';
                    $result['data']['list'][$key]['distance_value'] = $distance;
                }
            }
        }
        return $result;
    }

    /**
     * 门店详情
     */
    public function storeDetail($data)
    {
        $base_condition = [
            ['status', '=', 1],
            ['is_frozen', '=', 0],
            ['is_pickup', '=', 1],
        ];
        $field = 'store_id,store_name,telphone,store_image,store_logo,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc';
        $result = null;

        // 1. 如果传了 store_id，先按 store_id 查找
        if (!empty($data['store_id'])) {
            $condition = array_merge($base_condition, [['store_id', '=', $data['store_id']]]);
            $result = model('store')->getInfo($condition, $field);
            // 容错：前端可能误将 site_id 当作 store_id 传入
            if (empty($result)) {
                $condition = array_merge($base_condition, [['site_id', '=', $data['store_id']]]);
                $result = model('store')->getInfo($condition, $field);
            }
        }

        // 2. 如果未找到且传了 site_id，按 site_id 查找
        if (empty($result) && !empty($data['site_id'])) {
            $condition = array_merge($base_condition, [['site_id', '=', $data['site_id']]]);
            $result = model('store')->getInfo($condition, $field);
        }

        // 3. 如果当前登录用户身上有绑定的 site_id（商家ID），且未指定门店或上面未查到，尝试根据用户自身绑定的商家匹配门店
        if (empty($result) && !empty($data['member_id'])) {
            $member = model('member')->getInfo([['member_id', '=', $data['member_id']]], 'site_id,website_id');
            if (!empty($member['site_id'])) {
                $condition = array_merge($base_condition, [['site_id', '=', $member['site_id']]]);
                $result = model('store')->getInfo($condition, $field);
            }
            if (empty($result) && empty($data['website_id']) && !empty($member['website_id'])) {
                $data['website_id'] = $member['website_id'];
            }
        }

        // 4. 兜底保护：如果用户没有绑定商家或指定门店不存在，优先获取所属城市分站的有效门店
        if (empty($result) && !empty($data['website_id'])) {
            $site_ids = model('shop')->getColumn(['website_id' => $data['website_id']], 'site_id');
            if (!empty($site_ids)) {
                $condition = array_merge($base_condition, [['site_id', 'in', $site_ids]]);
                $result = model('store')->getInfo($condition, $field, 'store_id desc');
            }
        }

        // 5. 全局兜底：获取系统中第一个可用的有效门店，绝不让前端白屏
        if (empty($result)) {
            $result = model('store')->getInfo($base_condition, $field, 'store_id desc');
        }

        if ($result) {
            //关注量 购买量
            //门店总关注人数
            $result['total_num'] = model('shop_member')->getCount(['site_id' => $result['site_id'], 'is_subscribe' => 1]);
            //订单数
            $condition = [];
            $condition[] = ['order_status', 'in', [1, 2, 3, 4, 10]];
            $condition[] = ['site_id', '=', $result['site_id']];
            $result['order_num'] = model('order')->getCount($condition);

            // 累计销量
            $shop_info = model('shop')->getInfo([['site_id', '=', $result['site_id']]], 'shop_sales,logo,avatar');
            $result['total_sales'] = max(intval($shop_info['shop_sales'] ?? 0), intval($result['order_num'] ?? 0));
            if (!empty($shop_info['logo'])) {
                $result['shop_logo'] = $shop_info['logo'];
            }

            // 计算与用户当前位置的距离
            $result['distance'] = '0km';
            $result['distance_value'] = 0;
            if (!empty($data['latitude']) && !empty($data['longitude']) && !empty($result['latitude']) && !empty($result['longitude'])) {
                $distance = getDistance2($data['longitude'], $data['latitude'], $result['longitude'], $result['latitude']);
                $result['distance'] = $distance . 'km';
                $result['distance_value'] = floatval($distance);
            }

            // 商户 Banner 列表（优先展示后台上传的专属 Banner）
            $banner_list = model('banner')->getList([
                ['site_id', '=', $result['site_id']],
                ['status', '=', 1]
            ], 'id,name,image,url,sort', 'sort desc, id desc');

            if (empty($banner_list) && !empty($result['store_image'])) {
                $banner_list = [
                    [
                        'id' => 0,
                        'name' => $result['store_name'],
                        'image' => $result['store_image'],
                        'url' => ''
                    ]
                ];
            }
            $result['banner_list'] = $banner_list ?: [];
        }
        return $this->success($result);
    }

    /**
     * 门店列表（支持指定商家、城市筛选，并根据经纬度按距离升序排列）
     */
    public function websiteStore($data)
    {
        $store_condition = [
            ['status', '=', 1],
            ['is_frozen', '=', 0],
            ['is_pickup', '=', 1],
        ];
        $field = 'store_id,store_name,telphone,store_image,store_logo,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc';
        $list = [];

        // 1. 如果传了指定商家 site_id，优先获取该商家名下的所有门店
        if (!empty($data['site_id'])) {
            $condition = array_merge($store_condition, [['site_id', '=', $data['site_id']]]);
            $list = model('store')->getList($condition, $field, 'store_id desc');
        }

        // 2. 如果未指定 site_id 或该商家下未查到门店，按城市 website_id 筛选
        if (empty($list) && !empty($data['website_id'])) {
            $site_ids = model('shop')->getColumn(['website_id' => $data['website_id']], 'site_id');
            if (!empty($site_ids)) {
                $condition = array_merge($store_condition, [['site_id', 'in', $site_ids]]);
                $list = model('store')->getList($condition, $field, 'store_id desc');
            }
        }

        // 3. 兜底：返回所有可用门店
        if (empty($list)) {
            $list = model('store')->getList($store_condition, $field, 'store_id desc');
        }

        // 4. LBS距离计算与格式化
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        foreach ($list as $key => $value) {
            $full_address = str_replace(',', '', $value['full_address'] ?? '') . ($value['address'] ?? '');
            $list[$key]['address_text'] = $full_address ?: ($value['address'] ?? '');
            $list[$key]['distance'] = '';
            $list[$key]['distance_value'] = 999999;
            if (!empty($latitude) && !empty($longitude) && !empty($value['latitude']) && !empty($value['longitude'])) {
                $dist = getDistance2($longitude, $latitude, $value['longitude'], $value['latitude']);
                $list[$key]['distance'] = $dist . 'km';
                $list[$key]['distance_value'] = floatval($dist);
            }
        }

        // 5. 若有经纬度，按距离由近到远排序
        if (!empty($latitude) && !empty($longitude)) {
            usort($list, function ($a, $b) {
                if ($a['distance_value'] == $b['distance_value']) return 0;
                return ($a['distance_value'] < $b['distance_value']) ? -1 : 1;
            });
        }

        return $this->success($list);
    }

}