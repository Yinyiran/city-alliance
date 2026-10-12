<?php


namespace addon\city\city\controller;

use app\model\BaseModel;

/**
 * 短视频管理
 * Class Video
 * @package addon\city\city\controller
 */
class Video extends BaseCity
{
    /**
     * 列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_keys = input('search_keys', "");
            $condition = [];
            if (!empty($search_keys)) {
                $condition[] = [ 'ngb.name', 'like', '%' . $search_keys . '%' ];
            }
            $condition[] = ['website_id', '=', $this->site_id];
            $list = model('video')->pageList($condition, '*', 'id desc', $page_index, $page_size, 'ngb', '', '');
            foreach ($list['list'] as $key => $value) {
                //关联商家和商品
                $goods = model('goods')->getInfo(['goods_id'=>$value['goods_id']]);
                $list['list'][$key]['site_name'] = $goods['site_name'] ?? '';
                $list['list'][$key]['goods_name'] = $goods['goods_name'] ?? '';
            }
            $list = (new BaseModel())->success($list);
            return $list;
        } else {
            return $this->fetch('video/lists', [], $this->replace);
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if (request()->isAjax()) {
            $name = input('name', '');
            $video_url = input('video_url', '');
            $store_id = input('store_id', ''); //选择的门店ids
            $point = input('point', 0);
            $point_num = input('point_num', 0);
            $second = input('second', 0);
            $store_ids = explode(',', trim($store_id,','));
            if (empty($video_url)) {
                return (new BaseModel())->error('', '请上传视频');
            }
            if (empty($store_ids)) {
                return (new BaseModel())->error('', '请选择关联商品');
            }
            if (count($store_ids) > 1) {
                return (new BaseModel())->error('', '只能关联一个商品');
            }
            $goods = model('goods')->getInfo(['goods_id'=>$store_ids[0]]);
            $data = [
                'name' => $name,
                'video_url' => $video_url,
                'goods_id' => $store_ids[0],
                'site_id' => $goods['site_id'],
                'create_time' => time(),
                'point' => $point,
                'point_num' => $point_num,
                'second' => $second,
                'website_id' => $this->site_id,
            ];
            //新视频置顶3天
            $config = model('reward_config')->getInfo(['id' => 1]);
            $video_day = $config['video_day'];
            if ($video_day > 0) {
                $data['is_new'] = 1;
                $end_time = strtotime('+'. $video_day.' day 23:59:59');
                $data['end_time'] = $end_time;
            }
            $res = model('video')->add($data);
            $res = (new BaseModel())->success($res);
            return $res;

        } else {
            return $this->fetch('video/add', [], $this->replace);
        }
    }

    /**
     * 商家商品
     */
    public function goodsList()
    {
        $page_index = input('page', 1);
        $page_size = input('page_size', 5);
        $region_id = input('region_id', 0); //商家ID
        $store_name = input('store_name', ''); //商品名称
        $condition = [];
        if (!empty($region_id)) {
            if ($region_id != '-1') {
                //查询当前商家的
                $condition[] = ['site_id', '=', $region_id];
            } else { //查询当前城市商品
                $site_ids  = model('shop')->getColumn(['website_id'=>$this->site_id], 'site_id');
                $condition[] = ['site_id', 'in', $site_ids];
            }
        } else { //查询当前城市商品
            $site_ids  = model('shop')->getColumn(['website_id'=>$this->site_id], 'site_id');
            $condition[] = ['site_id', 'in', $site_ids];
        }
        if ($store_name != '') {
            $condition[] = ['goods_name', 'like', "%$store_name%"];
        }
        $condition[] = ['is_admin', '=', 0];
        $condition[] = ['goods_type', '=', 0];
        $field = '*';
        $order = 'create_time desc';
        $list = model('goods')->pageList($condition, $field, $order, $page_index, $page_size);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['store_id'] = $value['goods_id'];
        }
        $list = (new BaseModel())->success($list);
        return $list;
    }

    /**
     * 商家列表
     */
    public function shopList()
    {
        $field = 'site_id,site_name';
        $condition = [];
        $condition[] = ['is_admin', '=', 0];
        $condition[] = ['website_id', '=', $this->site_id];
        $list = model('shop')->getList($condition,$field,'create_time desc');
        array_push($list, ['site_id'=>'-1','site_name'=>'未关联商家']);
        $list = (new BaseModel())->success($list);
        return $list;
    }

    /**
     * 删除
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $id = input("id", 0);
            $path = app()->getRootPath();
            $info = model('video')->getInfo(['id'=>$id]);
//            dd($path.$info['video_url']);
//            dd(file_exists($path.$info['video_url']));

            $condition = [
                [ "id", '=', $id ],
            ];
            $res = model('video')->delete($condition);
            $res = (new BaseModel())->success($res);
            //删除文件
            if (file_exists($path.$info['video_url'])) {
                unlink($path.$info['video_url']);
            }

            return $res;
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = input("id", 0);
        if (request()->isAjax()) {
            $id = input("goods_id", 0);
            $name = input('name', '');
            $video_url = input('video_url', '');
            $store_id = input('store_id', ''); //选择的门店ids
            $point = input('point', 0);
            $point_num = input('point_num', 0);
            $second = input('second', 0);
            $store_ids = explode(',', trim($store_id,','));
            if (empty($video_url)) {
                return (new BaseModel())->error('', '请上传视频');
            }
            if (empty($store_ids)) {
                return (new BaseModel())->error('', '请选择关联商品');
            }
            if (count($store_ids) > 1) {
                return (new BaseModel())->error('', '只能关联一个商品');
            }
            $goods = model('goods')->getInfo(['goods_id'=>$store_ids[0]]);
            $data = [
                'name' => $name,
                'video_url' => $video_url,
                'goods_id' => $store_ids[0],
                'site_id' => $goods['site_id'],
                'point' => $point,
                'point_num' => $point_num,
                'second' => $second,
            ];
            $res = model('video')->update($data,['id'=>$id]);
            $res = (new BaseModel())->success($res);
            return $res;

        } else {
            $info = model('video')->getInfo(['id'=>$id]);
            $this->assign("goods_info", $info);
            $store_ids = [$info['goods_id']];
            $store_ids = implode(',', $store_ids);
            $this->assign("store_ids", $store_ids);
            $this->assign("id", $id);
            return $this->fetch('video/edit', [], $this->replace);
        }
    }
}