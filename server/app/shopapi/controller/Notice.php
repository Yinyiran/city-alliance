<?php
/**

 */

namespace app\shopapi\controller;

use app\model\web\Notice as NoticeModel;

/**
 * 网站公告
 * Class Notice
 * @package app\shopapi\controller
 */
class Notice extends BaseApi
{

    public function lists()
    {
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $limit = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;

        $condition = [];
        $receiving_type = isset($this->params[ 'receiving_type' ]) ? $this->params[ 'receiving_type' ] : 'shop';
        if ($receiving_type) {
            $condition[] = [ 'receiving_type', 'like', '%' . $receiving_type . '%' ];
        }
        $notice = new NoticeModel();
        $list = $notice->getNoticePageList($condition, $page, $limit);
        return $this->response($list);
    }

    public function detail()
    {
        $id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;

        $notice = new NoticeModel();
        $info = $notice->getNoticeInfo([ [ 'id', '=', $id ] ]);
        return $this->response($info);
    }
}