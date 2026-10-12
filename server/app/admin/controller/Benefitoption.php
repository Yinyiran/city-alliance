<?php


namespace app\admin\controller;

use app\model\BaseModel;

/**
 * 平台福利区筛选项
 * Class Benefitoption
 * @package app\admin\controller
 */
class Benefitoption extends BaseAdmin
{
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [];
            if(!empty($search_text)) $condition[] = [ 'name', 'like', '%' . $search_text . '%' ];
            $list = model('benefit_option')->pageList($condition, '*', 'sort asc', $page, $page_size, '', '', '');
            $list = (new BaseModel())->success($list);
            return $list;

        } else {
            return $this->fetch('benefitoption/lists');
        }
    }

    public function add()
    {
        if (request()->isAjax()) {
            $data = [
                'name' => input('name', ''),//名称
                'min' => input('min', 0),
                'max' => input('max', 0),
                'sort' => input('sort', 0),//排序
            ];
            $res = model('benefit_option')->add($data);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {
            return $this->fetch('benefitoption/add_category');
        }
    }

    public function edit()
    {
        if (request()->isAjax()) {
            $data = [
                'name' => input('name', ''),
                'min' => input('min', 0),
                'max' => input('max', 0),
                'sort' => input('sort', 0),//排序
                'id' => input('id', 0),//直接在数据中传参
            ];
            $condition = array(
                [ 'id', '=', $data['id'] ] ,
            );
            $res = model('benefit_option')->update($data, $condition);
            $res = (new BaseModel())->success($res);
            return $res;
        } else {

            $id = input('id', 0);
            $res = model('benefit_option')->getInfo([['id', '=', $id]]);
            $res = (new BaseModel())->success($res);
            $brand_info = $res;
            $this->assign("category_info", $brand_info);

            return $this->fetch('benefitoption/edit_category');
        }
    }

    public function delete()
    {
        if (request()->isAjax()) {
            $id = input("category_ids", 0);
            $condition = [
                [ "id", '=', $id ],
            ];
            $res = model('benefit_option')->delete($condition);
            $res = (new BaseModel())->success($res);
            return $res;
        }
    }
}