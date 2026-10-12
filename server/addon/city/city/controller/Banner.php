<?php


namespace addon\city\city\controller;

use app\model\BaseModel;

/**
 * 轮播管理
 * Class Banner
 * @package addon\city\city\controller
 */
class Banner extends BaseCity
{
    /**
     * 列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $type = input('type');
            $name = input('name');
            $condition = [
                ['website_id','=',$this->site_id]
            ];
            if ($type) {
                $condition[] = ['type', '=', $type];
            }
            if ($name) {
                $condition[] = ['name', 'like', "%$name%"];
            }
            $list = model('banner')->pageList($condition,'*','sort desc',$page, $page_size);
            $list = (new BaseModel())->success($list);
            return $list;
        } else {
            $this->forthMenu();
            return $this->fetch('banner/lists', [], $this->replace);
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if (request()->isAjax()) {
            $image = input("image", "");
            $sort = input("sort", 0);
            $status = input("status", 0);
//            $banner_url = input('banner_url','');
            $banner_url = input('url','');
            $name = input('name', '');
            $type = input('type', 0);
            if (empty($image)) return (new BaseModel())->error('', '请上传图片');
            $data = array (
                'type' => $type,
                "image" => $image,
                "sort" => $sort,
                "status"=>$status,
                'url'=>$banner_url,
                'name'=>$name,
                'create_time'=>time(),
                'website_id' => $this->site_id,
            );
            $res = model('banner')->add($data);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            return $this->fetch('banner/add', [], $this->replace);
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $banner_id = input("banner_id", 0);
        if (request()->isAjax()) {
            $image = input("image", "");
            $sort = input("sort", 0);
            $status = input("status", 0);
//            $banner_url = input('banner_url','');
            $banner_url = input('url','');
            $name = input('name', '');
            $type = input('type', 0);
            if (empty($image)) return (new BaseModel())->error('', '请上传图片');
            $data = array (
                'type' => $type,
                "image" => $image,
                "sort" => $sort,
                "status"=>$status,
                'url'=>$banner_url,
                'name'=>$name,
            );
            $res = model('banner')->update($data,['id'=>$banner_id]);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            $info = model('banner')->getInfo(['id'=>$banner_id]);
            $this->assign("info", $info);
            $this->assign("banner_id", $banner_id);
            return $this->fetch('banner/edit', [], $this->replace);
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $banner_id = input("banner_id", 0);
            $condition = [
                [ "id", '=', $banner_id ],
                [ 'website_id', '=', $this->site_id]
            ];
            $res = model('banner')->delete($condition);
            $res = (new BaseModel())->success($res);
            return $res;
        }
    }
}