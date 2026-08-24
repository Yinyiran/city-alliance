<?php


namespace app\admin\controller;

use app\model\api\Api;
use app\model\BaseModel;
use app\model\system\Address as AddressModel;

/**
 * 城市管理
 * Class Region
 * @package app\admin\controller
 */
class Region extends BaseAdmin
{
    /**
     * 城市列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $name = input('name', '');
            $username = input('username', '');
            $type = input('type', '');
            $condition = [];
            if ($name != '') {
                $condition[] = ['name', 'like', "%$name%"];
            }
            if ($username != '') {
                $condition[] = ['username', 'like', "%$username%"];
            }
            if ($type != '') {
                $condition[] = ['type', '=', "$type"];
            }
            $order = 'id desc,create_time desc';
            $field = '*';
            $list = model('shop_region')->pageList($condition, $field, $order, $page, $page_size);
            foreach ($list['list'] as $key => $value) {
                //区域地址
                $address = '';
                if ($value['type'] == 1) {
                    $provinceModel = model('area')->getInfo(['id'=>$value['province_id']]);
                    $address = $provinceModel['name'] ?? '';
                } elseif ($value['type'] == 2) {
                    $provinceModel = model('area')->getInfo(['id'=>$value['province_id']]);
                    $cityModel = model('area')->getInfo(['id'=>$value['city_id']]);
                    $provinceName = $provinceModel['name'] ?? '';
                    $cityName = $cityModel['name'] ?? '';
                    $address = $provinceName.'-'.$cityName;
                } elseif ($value['type'] == 3) {
                    $provinceModel = model('area')->getInfo(['id'=>$value['province_id']]);
                    $cityModel = model('area')->getInfo(['id'=>$value['city_id']]);
                    $districtModel = model('area')->getInfo(['id'=>$value['district_id']]);
                    $provinceName = $provinceModel['name'] ?? '';
                    $cityName = $cityModel['name'] ?? '';
                    $districtName = $districtModel['name'] ?? '';
                    $address = $provinceName.'-'.$cityName.'-'.$districtName;
                }
                $list['list'][$key]['address'] = $address;
            }
            $list = (new BaseModel())->success($list);
            return $list;
        } else {
            $this->assign('cluster_id', '');
            return $this->fetch('region/lists');
        }
    }


    /**
     * 城市添加
     */
    public function addRegion()
    {
        if (request()->isAjax()) {
            $name = input('name', ''); //区域名称
            $username = input('username', ''); //账号
            $password = input('password', ''); //密码
            $type = input('type'); //区域类型
            $province_id = input('province_id', '');  //省
            $city_id = input('city_id', '');  //市
            $district_id = input('district_id', '');  //区
            $sort = input('sort');

            $regionModel = model('shop_region')->getInfo(['name'=>$name]);
            if ($regionModel) {
                return (new BaseModel())->error('', '城市名称已存在');
            }

            if ($type == 1) { //省
                $regionModel = model('shop_region')->getInfo(['type'=>1,'province_id'=>$province_id]);
                if ($regionModel) {
                    return (new BaseModel())->error('', '该区域已存在');
                }
            } elseif ($type == 2) { //市
                $regionModel = model('shop_region')->getInfo(['type'=>2,'province_id'=>$province_id,'city_id'=>$city_id]);
                if ($regionModel) {
                    return (new BaseModel())->error('', '该城市已存在');
                }
            } elseif ($type == 3) {
                $regionModel = model('shop_region')->getInfo(['type'=>3,'province_id'=>$province_id,'city_id'=>$city_id,'district_id'=>$district_id]);
                if ($regionModel) {
                    return (new BaseModel())->error('', '该区域已存在');
                }
            }

//            if (empty($username)) return (new BaseModel())->error('', '区域账号不存在');
//            if (!isMobile($username)) return (new BaseModel())->error('', '手机号不正确');
//            if (empty($password)) return (new BaseModel())->error('', '密码不存在');

            //创建
            model('member')->startTrans();
            try {
                //设置密码
//                $password = data_md5($password); //默认密码
                $data_array = [];
                $data_array['name'] = $name;
                $data_array['create_time'] = time();
//                $data_array['username'] = $username;
//                $data_array['password'] = $password;
                $data_array['type'] = $type;
                $data_array['sort'] = $sort;
                if ($type == 1) { //省
                    $data_array['province_id'] = $province_id;
                } elseif ($type == 2) { //市
                    if (!$province_id) return (new BaseModel())->error('', '请选择省');
                    if (!$city_id) return (new BaseModel())->error('', '请选择市');
                    $data_array['province_id'] = $province_id;
                    $data_array['city_id'] = $city_id;
                } elseif ($type == 3) { //区
                    $data_array['province_id'] = $province_id;
                    $data_array['city_id'] = $city_id;
                    $data_array['district_id'] = $district_id;
                }

                $region_id = model('shop_region')->add($data_array);

                /*//生成登录账号
                $user_data = [
                    'username' => $username,
                    'password' => $password,
                ];
                $res = (new Api())->setRegionUser($user_data);
                if (isset($res['code']) && $res['code'] < 0) {
                    return (new BaseModel())->error('', $res['message']);
                }*/
                model('member')->commit();
                $res = (new BaseModel())->success();
                return $res;

            } catch (\Exception $e) {
                model('member')->rollback();
                return (new BaseModel())->error('', '添加失败');
            }



//            dd(input());
        } else {
            //查询省级数据列表
            $address_model = new AddressModel();
            $list = $address_model->getAreaList([ [ "pid", "=", 0 ], [ "level", "=", 1 ] ]);
            $this->assign("province_list", $list[ "data" ]);
            return $this->fetch('region/add');
        }
    }

    /**
     * 编辑
     */
    public function editRegion()
    {
        if (request()->isAjax()) {
            $id = input('id', 0);
            $name = input('name', ''); //区域名称
            $username = input('username', ''); //账号
            $password = input('password', ''); //密码
            $type = input('type'); //区域类型
            $province_id = input('province_id', '');  //省
            $city_id = input('city_id', '');  //市
            $district_id = input('district_id', '');  //区
            $sort = input('sort');

            $regionModel = model('shop_region')->getInfo([['name', '=', $name], ['id', '<>', $id]]);
            if ($regionModel) {
                return (new BaseModel())->error('', '城市名称已存在');
            }

            if ($type == 1) { //省
                $regionModel = model('shop_region')->getInfo([['type', '=', 1], ['province_id', '=', $province_id], ['id', '<>', $id]]);
                if ($regionModel) {
                    return (new BaseModel())->error('', '该区域已存在');
                }
            } elseif ($type == 2) { //市
                $regionModel = model('shop_region')->getInfo([['type', '=', 2], ['province_id', '=', $province_id], ['city_id', '=', $city_id], ['id', '<>', $id]]);
                if ($regionModel) {
                    return (new BaseModel())->error('', '该区域已存在');
                }
            } elseif ($type == 3) {
                $regionModel = model('shop_region')->getInfo([['type', '=', 3], ['province_id', '=', $province_id], ['city_id', '=', $city_id], ['district_id', '=', $district_id], ['id', '<>', $id]]);
                if ($regionModel) {
                    return (new BaseModel())->error('', '该区域已存在');
                }
            }

            //编辑
            model('member')->startTrans();
            try {
                $data_array = [];
                /*if ($password) {
                    //设置密码
                    $password = data_md5($password); //默认密码
                    $data_array['password'] = $password;
                    $regionModel = model('shop_region')->getInfo(['id'=>$id]);
                    $user_count = model("user")->getInfo([['username', '=', $regionModel['username']], ['app_module', '=', 'city']]);
                    if ($user_count) {
                        model("user")->update(['password'=>$password],['uid'=>$user_count['uid']]);
                    }
                }*/


                $data_array['name'] = $name;
                $data_array['type'] = $type;
                $data_array['sort'] = $sort;
                if ($type == 1) { //省
                    $data_array['province_id'] = $province_id;
                } elseif ($type == 2) { //市
                    if (!$province_id) return (new BaseModel())->error('', '请选择省');
                    if (!$city_id) return (new BaseModel())->error('', '请选择市');
                    $data_array['province_id'] = $province_id;
                    $data_array['city_id'] = $city_id;
                } elseif ($type == 3) { //区
                    $data_array['province_id'] = $province_id;
                    $data_array['city_id'] = $city_id;
                    $data_array['district_id'] = $district_id;
                }
                model('shop_region')->update($data_array, ['id' => $id]);

                model('member')->commit();
                $res = (new BaseModel())->success();
                return $res;
            } catch (\Exception $e) {
                model('member')->rollback();
                return (new BaseModel())->error('', '修改失败');
            }

        } else {
            $id = input('id', 0);
            $res = model('shop_region')->getInfo([['id', '=', $id]]);
            $this->assign('info', $res);
            //查询省级数据列表
            $address_model = new AddressModel();
            $list = $address_model->getAreaList([ [ "pid", "=", 0 ], [ "level", "=", 1 ] ]);
            $this->assign("province_list", $list[ "data" ]);
            return $this->fetch('region/edit');
        }
    }

    /**
     * 删除区域
     */
    public function deleteRegion()
    {
        if (request()->isAjax()) {
            $id = input("id", 0);
            $condition = [
                ["id", '=', $id],
            ];
            model('member')->startTrans();
            try {
                $info = model('shop_region')->getInfo($condition);
                model('shop_region')->delete($condition);
                /*//获取用户信息
                $user_info = model('user')->getInfo(
                    [['username', '=', $info['username']], ['app_module', '=', 'city']],
                    'group_id'
                );
                model('user')->delete(['username'=>$info['username'],'app_module'=>'city']);
                //删除用户组
                model('group')->delete([['group_id', '=', $user_info['group_id']]]);*/
                model('member')->commit();
                $res = (new BaseModel())->success();
                return $res;
            } catch (\Exception $e) {
                model('member')->rollback();
                return (new BaseModel())->error('', '删除失败');
            }
        }
    }
}