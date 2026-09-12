<?php


namespace app\admin\controller;

use app\model\BaseModel;
use think\facade\Db;

/**
 * 短视频管理
 * Class Video
 * @package app\admin\controller
 */
class Video extends BaseAdmin
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
            $list = model('video')->pageList($condition, '*', 'id desc', $page_index, $page_size, 'ngb', '', '');
            foreach ($list['list'] as $key => $value) {
                //关联商家和商品
                $goods = model('goods')->getInfo(['goods_id'=>$value['goods_id']]);
                $list['list'][$key]['site_name'] = $goods['site_name'] ?? '';
                $list['list'][$key]['goods_name'] = $goods['goods_name'] ?? '';
            }


           /* Db::name('video')
                ->where(function ($query) use ($search_keys) {
                    if (!empty($search_keys)) {
                        $query->where('name', 'like', '%' . $search_keys . '%' );
                    }
                    $query->where('')
                });*/


            $list = (new BaseModel())->success($list);
            return $list;
        } else {
            // 获取总平台审核模式开关（website.site_id = 0）
            $platform = model('website')->getInfo(['site_id' => 0], 'sh_status');
            $global_sh_status = !empty($platform) ? intval($platform['sh_status']) : 0;
            $this->assign('global_sh_status', $global_sh_status);
            return $this->fetch('video/lists');
        }
    }

    /**
     * 修改总平台小程序审核模式（控制全平台视频显示与否）
     * status: 0 正常（展示视频） 1 审核模式（展示商品）
     */
    public function modifyGlobalReviewStatus()
    {
        if (request()->isAjax()) {
            $status = input('status', 0);
            $status = intval($status) == 1 ? 1 : 0;

            // 平台总开关对应 website 表中 site_id = 0
            $platform = model('website')->getInfo(['site_id' => 0]);
            if (!empty($platform)) {
                $res = model('website')->update(['sh_status' => $status], [['site_id', '=', 0]]);
            } else {
                $res = model('website')->add(['site_id' => 0, 'title' => '总平台', 'sh_status' => $status]);
            }
            \think\facade\Cache::tag("website")->clear();
            $msg = $status == 1 ? '已开启提审模式（全平台展示商品）' : '已恢复正常模式（全平台展示视频）';
            return (new BaseModel())->success($res, $msg);
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
                'website_id' => $goods['website_id'],
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
            return $this->fetch('video/add');
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
            } else { //查询总平台全国包邮商品

            }
        }
        if ($store_name != '') {
            $condition[] = ['goods_name', 'like', "%$store_name%"];
        }
        $condition[] = ['is_admin', '=', 0];
        $condition[] = ['goods_type', '=', 0];
        $field = '*';
        $order = 'create_time desc';
//        $list = model('goods')->pageList($condition, $field, $order, $page_index, $page_size);


         $list = Db::name('goods')
                ->where(function ($query) use ($region_id, $store_name) {
                    if ($region_id) {
                        if ($region_id != '-1') {
                            //查询当前商家的
                            $query->where('site_id', '=', $region_id);
                        } else { //查询总平台全国包邮商品
                            $query->where('goods_type', '=', 1)
                                ->where('is_admin', '=', 1);
                        }
                    } else { //默认搜索
                        $query->where('is_admin', '=', 0)
                            ->where('goods_type', '=', 0)
                            ->whereOr(function ($query) {
                                $query->where('goods_type', '=', 1)
                                    ->where('is_admin', '=', 1);
                            });
                    }
                    if ($store_name != '') {
                        $query->where('goods_name', 'like', "%$store_name%");
                    }
                })
             ->order($order)
             ->limit($page_size)
             ->page($page_index)
             ->select()
             ->toArray();

        //返回的数据
        $count = Db::name('goods')
            ->where(function ($query) use ($region_id, $store_name) {
                if ($region_id) {
                    if ($region_id != '-1') {
                        //查询当前商家的
                        $query->where('site_id', '=', $region_id);
                    } else { //查询总平台全国包邮商品
                        $query->where('goods_type', '=', 1)
                            ->where('is_admin', '=', 1);
                    }
                } else { //默认搜索
                    $query->where('is_admin', '=', 0)
                        ->where('goods_type', '=', 0)
                        ->whereOr(function ($query) {
                            $query->where('goods_type', '=', 1)
                                ->where('is_admin', '=', 1);
                        });
                }
                if ($store_name != '') {
                    $query->where('goods_name', 'like', "%$store_name%");
                }
            })
            ->count();
        $page_count = ceil($count / $page_size);

        $list = [
            'count' => $count,
            'page_count' => $page_count,
            'list' => $list,
        ];



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
        $list = model('shop')->getList($condition,$field,'create_time desc');
        array_push($list, ['site_id'=>'-1','site_name'=>'总平台']);
//        array_push($list, ['site_id'=>'-1','site_name'=>'未关联商家']);
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
                'website_id' => $goods['website_id'],
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
            return $this->fetch('video/edit');
        }
    }


    /**
     * 评论列表
     */
    public function evaluateList()
    {
        $video_id = input('id', 0);
        if (request()->isAjax()) {
            $video_id = input('video_id', 0);
            $page          = input('page', 1);
            $page_size     = input('page_size', PAGE_LIST_ROWS);
            $start_date    = input('start_date', '');
            $end_date      = input('end_date', '');
            $condition     = [];
            $condition[] = ['type', '=', 'video'];
            $condition[] = ['category_id', '=', $video_id];
            if ($start_date != '' && $end_date != '') {
                $condition[] = ['create_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } else if ($start_date != '' && $end_date == '') {
                $condition[] = ['create_time', '>=', strtotime($start_date)];
            } else if ($start_date == '' && $end_date != '') {
                $condition[] = ['create_time', '<=', strtotime($end_date)];
            }
            $order = 'create_time desc';
            $res = model('video_comment')->pageList($condition, '*', $order, $page, $page_size);
            foreach ($res['list'] as $key => $value) {
                $res['list'][$key]['video_title'] =  model('video')->getInfo(['id'=>$value['category_id']])['name'];
                $res['list'][$key]['status'] = $value['again_is_audit'];
                $res['list'][$key]['member_headimg'] = model('member')->getInfo([['member_id', '=', $value['member_id']]], '*')['headimg'] ?? '';
                $res['list'][$key]['member_name'] = model('member')->getInfo([['member_id', '=', $value['member_id']]], '*')['nickname'] ?? '';
            }
            $list = (new BaseModel())->success($res);
            return $list;
        } else {
            //新增加
            $this->assign('video_id', $video_id);
            return $this->fetch('video/evaluate_list');
        }
    }

    /**
     * 通过
     */
    public function agree()
    {
        if (request()->isAjax()) {
            $id             = input('id', 0);
            $result = model('video_comment')->update(['again_is_audit'=>1],['id'=>$id]);
            $result = (new BaseModel())->success($result);
            return $result;
        }
    }

    /**
     * 拒绝
     */
    public function refuse()
    {
        if (request()->isAjax()) {
            $id             = input('id', 0);
            $result = model('video_comment')->update(['again_is_audit'=>2],['id'=>$id]);
            $result = (new BaseModel())->success($result);
            return $result;
        }
    }

    /**
     * 删除
     */
    public function commentdelete()
    {
        if (request()->isAjax()) {
            $id = input("id", 0);
            $res = model('video_comment')->delete(['id'=>$id]);
            $res = (new BaseModel())->success($res);
            return $res;
        }
    }

    /**
     * 查看
     */
    public function detail()
    {
        $id                   = input('id', 0);
        $info = model('video_comment')->getInfo(['id' => $id], '*');
        $info['help_title'] = model('video')->getInfo(['id'=>$info['category_id']])['name'] ?? '';
        $info['member_name'] = model('member')->getInfo([['member_id', '=', $info['member_id']]], '*')['nickname'] ?? '';
        $this->assign("withdraw_info", $info);
        return $this->fetch("video/detail");
    }


}