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
        $store_condition = [
            ['status', '=', 1],
            ['is_frozen', '=', 0],
            ['is_pickup', '=', 1],
            ['store_id', '=', $data['store_id']],
        ];
        $field = 'store_id,store_name,telphone,store_image,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc';
        $result = model('store')->getInfo($store_condition,$field);
        if ($result) {
            //关注量 购买量
            //门店总关注人数
            $result['total_num'] = model('shop_member')->getCount(['site_id'=>$result['site_id'],'is_subscribe'=>1]);
            //订单数
            $condition = [];
            $condition[] = ['order_status', 'in', [1,2,3,4,10]];
            $condition[] = ['site_id', '=', $result['site_id']];
            $result['order_num'] = model('order')->getCount($condition);
        }
        return $this->success($result);

    }

    /**
     * 城市下商家
     */
    public function websiteStore($data)
    {
        $store_condition = [
            ['status', '=', 1],
            ['is_frozen', '=', 0],
            ['is_pickup', '=', 1],
        ];
        //该城市的商家
        $site_ids = model('shop')->getColumn(['website_id'=>$data['website_id']],'site_id');
        $store_condition[] = ['site_id', 'in', $site_ids];
        $field = 'store_id,store_name,telphone,store_image,store_logo,site_id,province_id,city_id,district_id,address,full_address,longitude,latitude,open_date,store_desc';
        $list = model('store')->getList($store_condition, $field, 'store_id desc');
        return $this->success($list);
    }

}