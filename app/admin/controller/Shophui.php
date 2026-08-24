<?php


namespace app\admin\controller;

use app\model\BaseModel;

/**
 * 商会管理
 * Class Hui
 * @package app\admin\controller
 */
class Shophui extends BaseAdmin
{
    /**
     * 列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [];
            if(!empty($search_text)) $condition[] = [ 'name', 'like', '%' . $search_text . '%' ];
            $order = 'id asc';
            $field = '*';

            $list = model('shop_can')->pageList($condition, $field, $order, $page, $page_size);
            return (new BaseModel())->success($list);
        } else {
            return $this->fetch('shophui/lists');
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if (request()->isAjax()) {
            $data = [
                'name' => input('name', ''),//行业名称
                'sort' => input('sort', 0),//排序
                'create_time' => time(),
            ];
            model('shop_can')->add($data);
            return (new BaseModel())->success();
        } else {
            return $this->fetch('shophui/add');
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $data = [
                'name' => input('name', ''),
                'sort' => input('sort', 0.00),
                'id' => input('id', 0),//直接在数据中传参
            ];
            model('shop_can')->update($data, ['id'=>$data['id']]);
            return (new BaseModel())->success();
        } else {
            $id = input('id', 0);
            $category_info = model('shop_can')->getInfo(['id'=>$id]);
            $category_info = (new BaseModel())->success($category_info);
            $this->assign('category_info', $category_info);
            return $this->fetch('shophui/edit');
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = input('id');
        model('shop_can')->delete(['id' => $id]);
        return (new BaseModel())->success();
    }
}