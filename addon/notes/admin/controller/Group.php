<?php
/**

 */

namespace addon\notes\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\notes\model\Group as GroupModel;

/**
 * 笔记控制器
 */
class Group extends BaseAdmin
{

    /*
     *  笔记分组列表
     */
    public function lists()
    {
        $model = new GroupModel();

        //获取续签信息
        if (request()->isAjax()) {

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getNotesGroupPageList([], $page, $page_size, 'sort asc');
            return $list;
        } else {

            return $this->fetch("group/lists");
        }
    }

    /**
     * 添加分组
     */
    public function add()
    {
        if (request()->isAjax()) {

            $data = [
                'group_name' => input('group_name', ''),
                'sort' => input('sort'),
            ];

            $notes_model = new GroupModel();
            return $notes_model->addNotesGroup($data);
        }
    }

    /**
     * 编辑分组
     */
    public function edit()
    {
        if (request()->isAjax()) {

            $data = [
                'group_id' => input('group_id'),
                'group_name' => input('group_name', ''),
                'sort' => input('sort'),
            ];

            $notes_model = new GroupModel();
            return $notes_model->editNotesGroup([ [ 'group_id', '=', $data[ 'group_id' ] ] ], $data);
        }
    }

    /**
     * 编辑分组排序
     * @return array
     */
    public function modifySort()
    {
        if (request()->isAjax()) {

            $data = [
                'group_id' => input('group_id'),
                'sort' => input('sort'),
            ];
            $notes_model = new GroupModel();
            return $notes_model->editNotesGroup([ [ 'group_id', '=', $data[ 'group_id' ] ] ], $data);
        }
    }

    /*
     *  删除分组
     */
    public function delete()
    {
        $group_id = input('group_id', '');

        $notes_model = new GroupModel();
        return $notes_model->deleteNotesGroup($group_id);
    }

}