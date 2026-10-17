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
     * 检查门店账号是否可用（查重）
     * @param string $username
     * @param int $store_id
     * @param int $site_id
     * @return array
     */
    public function checkUsername($username, $store_id = 0, $site_id = 0)
    {
        $username = trim($username);
        if (empty($username)) {
            return $this->error('', '请输入门店账号');
        }
        if (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{2,30}$/u', $username)) {
            return $this->error('', '账号由2-30位字母、数字、下划线或中文组成');
        }

        // 1. 检查 user 表中是否有相同 username 且 app_module 为 store
        $user_condition = [
            [ 'username', '=', $username ],
            [ 'app_module', '=', 'store' ]
        ];
        if ($store_id > 0) {
            $user_condition[] = [ 'site_id', '<>', $store_id ];
        }
        $user_count = model('user')->getCount($user_condition);
        if ($user_count > 0) {
            return $this->error('', '该门店账号已存在，请更换其他账号');
        }

        // 2. 检查 store 表中是否有相同 username
        $store_condition = [
            [ 'username', '=', $username ]
        ];
        if ($store_id > 0) {
            $store_condition[] = [ 'store_id', '<>', $store_id ];
        }
        $store_count = model('store')->getCount($store_condition);
        if ($store_count > 0) {
            return $this->error('', '该门店账号已存在，请更换其他账号');
        }

        // 3. 检查 user 表中系统核心保留账号（admin、city、supply等）
        $system_user_count = model('user')->getCount([
            [ 'username', '=', $username ],
            [ 'app_module', 'in', ['admin', 'city', 'supply'] ]
        ]);
        if ($system_user_count > 0) {
            return $this->error('', '该账号为系统保留账号或已被占用，请更换其他账号');
        }

        // 4. 检查是否与其他商家的店铺账号重名
        $other_shop_condition = [
            [ 'username', '=', $username ],
            [ 'app_module', '=', 'shop' ]
        ];
        if ($site_id > 0) {
            $other_shop_condition[] = [ 'site_id', '<>', $site_id ];
        }
        $other_shop_count = model('user')->getCount($other_shop_condition);
        if ($other_shop_count > 0) {
            return $this->error('', '该账号已被其他商家占用，请更换其他账号');
        }

        return $this->success([], '该账号可以使用');
    }

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

        // 提前校验门店账号查重与密码
        if ($is_store == 1) {
            $username = trim($user_data['username'] ?? '');
            if (empty($username)) {
                return $this->error('', '请输入门店账号');
            }
            if (empty($user_data['password'])) {
                return $this->error('', '请输入门店密码');
            }
            $check_res = $this->checkUsername($username, 0, $site_id);
            if ($check_res['code'] < 0) {
                return $check_res;
            }
            $user_data['username'] = $username;
        }

        $data[ 'create_time' ] = time();

        $shop_model = new Shop();
        $shop_info_result = $shop_model->getShopInfo([ [ "site_id", "=", $site_id ] ], "site_name");
        $shop_info = $shop_info_result[ "data" ];
        $data[ "site_name" ] = $shop_info[ "site_name" ];
        model('store')->startTrans();

        try {
            if ($is_store == 1) {
                $data[ 'username' ] = $user_data[ 'username' ];
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
     * @param $condition
     * @param array $user_data
     * @return multitype:string
     */
    public function editStore($data, $condition, $user_data = [])
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

        $store_info = model('store')->getInfo([ [ 'store_id', '=', $store_id ], [ 'site_id', '=', $site_id ] ]);
        if (empty($store_info)) {
            return $this->error('', 'STORE_NOT_EXIST');
        }

        // 如果原有门店未设置账号，且传入了新账号，支持初始化创建门店账号
        if (empty($store_info['username']) && !empty($user_data['username'])) {
            $username = trim($user_data['username']);
            $password = trim($user_data['password'] ?? '');
            if (empty($password)) {
                return $this->error('', '请输入门店密码');
            }
            $check_res = $this->checkUsername($username, $store_id, $site_id);
            if ($check_res['code'] < 0) {
                return $check_res;
            }

            model('store')->startTrans();
            try {
                // 确保用户组存在
                $group_info = model('group')->getInfo([ [ 'site_id', '=', $store_id ], [ 'app_module', '=', 'store' ] ]);
                if (empty($group_info)) {
                    $group = new Group();
                    $group_data = [
                        'site_id' => $store_id,
                        'app_module' => 'store',
                        'group_name' => '管理员组',
                        'is_system' => 1,
                        'create_time' => time()
                    ];
                    $group_id = $group->addGroup($group_data)['data'];
                } else {
                    $group_id = $group_info['group_id'];
                }

                // 添加门店管理员用户
                $data_user = [
                    'app_module' => 'store',
                    'app_group' => 0,
                    'is_admin' => 1,
                    'group_id' => $group_id,
                    'group_name' => '管理员组',
                    'site_id' => $store_id,
                    'username' => $username,
                    'password' => data_md5($password),
                    'create_time' => time(),
                    'status' => 1
                ];
                model('user')->add($data_user);

                $data['username'] = $username;
                $data['modify_time'] = time();
                $res = model('store')->update($data, $condition);
                Cache::tag("store")->clear();

                model('store')->commit();
                return $this->success($res);
            } catch (\Exception $e) {
                model('store')->rollback();
                return $this->error('', $e->getMessage());
            }
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
            model('site')->delete([ [ 'site_type', '=', 'store' ], [ 'site_id', '=', $store_id ] ]);
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
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        $keyword = $data['keyword'] ?? '';
        $sort_type = $data['sort_type'] ?? $data['order_type'] ?? 'distance';
        $sort = strtolower($data['sort'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $store_condition = [
            ['s.status', '=', 1],
            ['s.is_frozen', '=', 0],
            ['s.is_pickup', '=', 1],
        ];
        if (!empty($keyword)) {
            $store_condition[] = ['s.store_name|s.address|s.full_address', 'like', "%$keyword%"];
        }

        // 该城市的商家
        $website_id = $data['website_id'] ?? 0;
        $site_ids = model('shop')->getColumn(['website_id' => $website_id], 'site_id');
        if (!empty($site_ids)) {
            $store_condition[] = ['s.site_id', 'in', $site_ids];
        } elseif (!empty($website_id)) {
            $store_condition[] = ['s.site_id', 'in', [0]];
        }

        $field = 's.store_id,s.store_name,s.telphone,s.store_image,s.site_id,s.province_id,s.city_id,s.district_id,s.address,s.full_address,s.longitude,s.latitude,s.open_date,s.store_desc,s.order_num,s.order_complete_num,s.is_pickup,s.is_o2o,sp.shop_sales,sp.sub_num';
        $alias = 's';
        $join = [
            ['shop sp', 'sp.site_id = s.site_id', 'left']
        ];

        $order = '';
        if ($sort_type === 'sales') {
            $order = 'sp.shop_sales ' . $sort . ', s.order_num ' . $sort . ', s.store_id desc';
        } elseif ($sort_type === 'follow') {
            $order = 'sp.sub_num ' . $sort . ', s.store_id desc';
        } else {
            // distance 默认按定位距离排序
            if ($longitude !== null && $latitude !== null && $longitude !== '' && $latitude !== '') {
                $field .= ',FORMAT(st_distance ( point ( ' . $longitude . ', ' . $latitude . ' ), point ( s.longitude, s.latitude ) ) * 111195 / 1000, 2) as distance';
                $order = Db::raw(' st_distance ( point ( ' . $longitude . ', ' . $latitude . ' ), point ( s.longitude, s.latitude ) ) * 111195 / 1000 asc');
            } else {
                $order = 's.store_id desc';
            }
        }

        $list = model('store')->pageList($store_condition, $field, $order, $data['page'], $data['page_size'], $alias, $join);
        if (isset($list['list']) && !empty($list['list'])) {
            $site_ids = array_unique(array_filter(array_column($list['list'], 'site_id')));
            $store_goods_map = [];
            if (!empty($site_ids)) {
                $goods_rows = model('goods')->getList([
                    ['site_id', 'in', $site_ids],
                    ['goods_state', '=', 1],
                    ['verify_state', '=', 1],
                    ['is_delete', '=', 0]
                ], 'goods_id,goods_name,goods_image,sku_id,price,sale_num,site_id', 'sort desc, goods_id desc');

                foreach ($goods_rows as $g) {
                    $sid = $g['site_id'];
                    if (!isset($store_goods_map[$sid])) {
                        $store_goods_map[$sid] = [];
                    }
                    if (count($store_goods_map[$sid]) < 8) {
                        $store_goods_map[$sid][] = $g;
                    }
                }
            }

            foreach ($list['list'] as $key => $value) {
                $full_address = str_replace(',', '', $value['full_address']) . $value['address'];
                $list['list'][$key]['address'] = $full_address;
                $list['list'][$key]['sales'] = max(intval($value['shop_sales'] ?? 0), intval($value['order_num'] ?? 0));
                $list['list'][$key]['sub_num'] = intval($value['sub_num'] ?? 0);
                $sid = $value['site_id'];
                $list['list'][$key]['goods_list'] = $store_goods_map[$sid] ?? [];

                // 经纬度与距离
                $list['list'][$key]['distance'] = '0km';
                $list['list'][$key]['distance_value'] = 0;
                if ($longitude && $latitude && !empty($value['longitude']) && !empty($value['latitude'])) {
                    $distance = getDistance2($longitude, $latitude, $value['longitude'], $value['latitude']);
                    $list['list'][$key]['distance'] = $distance . 'km';
                    $list['list'][$key]['distance_value'] = floatval($distance);
                }
            }
        }
        return $this->success($list);
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
        $field = 'store_id,store_name,telphone,store_image,store_logo,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc,is_pickup,is_o2o';
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

            // 累计销量及商家扩展信息（带字段自愈与兼容降级，防止数据库未执行迁移报错）
            $base_shop_fields = 'site_name,shop_sales,logo,avatar,telephone,workingtime,qq,ww';
            try {
                $shop_info = model('shop')->getInfo([['site_id', '=', $result['site_id']]], $base_shop_fields . ',wecom_group_url');
            } catch (\Exception $e) {
                // 如果数据库尚未添加 wecom_group_url 字段，降级查询基础字段，并自动尝试补全数据库表结构
                $shop_info = model('shop')->getInfo([['site_id', '=', $result['site_id']]], $base_shop_fields);
                try {
                    $prefix = config('database.connections.mysql.prefix');
                    \think\facade\Db::execute("ALTER TABLE `{$prefix}shop` ADD COLUMN `wecom_group_url` varchar(500) NOT NULL DEFAULT '' COMMENT '企微群URL'");
                } catch (\Exception $e2) {}
            }
            $result['total_sales'] = max(intval($shop_info['shop_sales'] ?? 0), intval($result['order_num'] ?? 0));
            if (!empty($shop_info['logo'])) {
                $result['shop_logo'] = $shop_info['logo'];
            }
            if (!empty($shop_info['site_name'])) {
                $result['site_name'] = $shop_info['site_name'];
            }
            if (!empty($shop_info['workingtime']) && empty($result['open_date'])) {
                $result['open_date'] = $shop_info['workingtime'];
            }
            if (!empty($shop_info['telephone']) && empty($result['telphone'])) {
                $result['telphone'] = $shop_info['telephone'];
            }

            // 品牌专属粉丝群配置（支持每个品牌独立配置企业微信群）
            $config_model = new \app\model\system\Config();
            $fan_group_config = $config_model->getConfig([
                ['site_id', '=', $result['site_id']],
                ['config_key', '=', 'SHOP_FAN_GROUP_CONFIG']
            ]);
            $group_val = $fan_group_config['data']['value'] ?? [];
            $result['group_qrcode'] = $group_val['group_qrcode'] ?? ($shop_info['ww'] ?? '');
            $result['wecom_group_url'] = $shop_info['wecom_group_url'] ?? ($group_val['wecom_group_url'] ?? ($group_val['wechat_group_url'] ?? ''));
            $result['wechat_group_url'] = $result['wecom_group_url'];
            $result['wechat_corp_id'] = $group_val['wechat_corp_id'] ?? '';
            $result['wechat_name'] = $group_val['wechat_name'] ?? '';

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
        $field = 'store_id,store_name,telphone,store_image,store_logo,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc,is_pickup,is_o2o';
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