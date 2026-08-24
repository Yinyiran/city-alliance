<?php


namespace addon\city\city\controller;

use app\model\BaseModel;

/**
 * 导航
 * Class Nav
 * @package addon\city\city\controller
 */
class Nav extends BaseCity
{
    /**
     * 列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [
                ['website_id','=',$this->site_id]
            ];
            $list = model('nav')->pageList($condition,'*','sort desc',$page, $page_size);
            $list = (new BaseModel())->success($list);
            return $list;
        } else {
            $this->forthMenu();
            return $this->fetch('nav/lists', [], $this->replace);
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
            $banner_url = input('banner_url','');
            $name = input('name', '');
            $type = input('type', 0);
            $category_id = input('category_id', '');
            if ($category_id) {
                $banner_url = '{"menuOne":"CUSTOM_LINK","menuTwo":"MALL_LINK","type":3,"name":"","title":"","wap_url":"/pages/goods/list/list?category_id='. $category_id . '"}';
            }
            $data = array (
                'type' => $type,
                "image" => $image,
                "sort" => $sort,
                "status"=>$status,
                'url'=>$banner_url,
                'name'=>$name,
                'create_time'=>time(),
                'website_id' => $this->site_id,
                'goods_category_id' => $category_id,
            );
            $res = model('nav')->add($data);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            //城市商品分类
            $category = model('goods_category')->getList([
                ['website_id', '=', $this->site_id]
            ]);
            $this->assign('category', $category);
            return $this->fetch('nav/add', [], $this->replace);
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
            $banner_url = input('banner_url','');
            $name = input('name', '');
            $type = input('type', 0);
            $category_id = input('category_id', '');
            if ($category_id) {
                $banner_url = '{"menuOne":"CUSTOM_LINK","menuTwo":"MALL_LINK","type":3,"name":"","title":"","wap_url":"/pages/goods/list/list?category_id='. $category_id . '"}';
            } else {
                $banner_url = '';
            }
            $data = array (
                'type' => $type,
                "image" => $image,
                "sort" => $sort,
                "status"=>$status,
                'url'=>$banner_url,
                'name'=>$name,
                'goods_category_id' => $category_id,
            );
            $res = model('nav')->update($data,['id'=>$banner_id]);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            $info = model('nav')->getInfo(['id'=>$banner_id]);
            $this->assign("info", $info);
            $this->assign("banner_id", $banner_id);
            //城市商品分类
            $category = model('goods_category')->getList([
                ['website_id', '=', $this->site_id]
            ]);
            $this->assign('category', $category);
            return $this->fetch('nav/edit', [], $this->replace);
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
            $res = model('nav')->delete($condition);
            $res = (new BaseModel())->success($res);
            return $res;
        }
    }
}