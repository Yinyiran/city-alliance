<?php

/**

 */

namespace addon\servicer\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\servicer\model\Fast as FastModel;

/**
 * 关键词回复
 */
class Fast extends BaseAdmin
{
    /**
     * 列表
     */
    public function index()
    {
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [['site_id', '=', 0]];
            if (!empty($search_text)) {
                $condition[] = ['content', 'like', '%' . $search_text . '%'];
            }

            $fast_model = new FastModel();
            $res        = $fast_model->getPageList($condition, $page, $page_size);
            return $res;
        } else {
            return $this->fetch('fast/list');
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if (request()->isAjax()) {
            $fast_model = new FastModel();
            return $fast_model->add([
                'site_id' => 0,
                'content' => input('content', ''),
            ]);
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $fast_model = new FastModel();
        if (request()->isAjax()) {
            return $fast_model->edit([
                'content' => input('content', ''),
            ], [['site_id', '=', 0], ['id', '=', input('id', 0)]]);
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $fast_model = new FastModel();
            return $fast_model->delete([
                ['site_id', '=', 0],
                ['id', 'in', (string)input('ids', '')]
            ]);
        }
    }
}