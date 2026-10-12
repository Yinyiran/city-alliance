<?php


namespace app\api\controller;
use app\model\api\Video as videoModel;

/**
 * 短视频
 * Class Video
 * @package app\api\controller
 */
class Video extends BaseApi
{
    /**
     * 视频列表
     */
    public function lists()
    {
        $token = $this->checkToken();
        $data = [
            'page' => $this->params['page'] ?? 1,
            'page_size' => $this->params['page_size'] ?? PAGE_LIST_ROWS,
            'member_id' => $this->member_id,
            'website_id' => $this->params['website_id'] ?? ''
        ];
        $model = new videoModel();
        $result = $model->lists($data);
        return $this->response($result);
    }

    /**
     * 短视频领取积分
     */
    public function pointAdd()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);
        $data = [
            'id' => $this->params['id'],
            'member_id' => $this->member_id,
        ];
        $model = new videoModel();
        $result = $model->pointAdd($data);
        return $this->response($result);
    }

    /**
     * 评论
     */
    public function commentCreate()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $data = [
            'member_id' => $this->member_id,
            'category_id' => $this->params['category_id'],
            'pid' => $this->params['pid'] ?? 0,
            'content' => $this->params['content'],
        ];
        $model = new videoModel();
        $result = $model->commentCreate($data);
        return $this->response($result);
    }

    /**
     * 评论列表
     */
    public function commentLists()
    {
        $token = $this->checkToken();
        $data = [
            'page' => $this->params['page'] ?? 1,
            'page_size' => $this->params['page_size'] ?? PAGE_LIST_ROWS,
            'id' => $this->params['id'],
            'member_id' => $this->member_id,
        ];
        $model = new videoModel();
        $result = $model->commentLists($data);
        return $this->response($result);

    }

    /**
     * 评论点赞/取消
     */
    public function collect()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $data = [
            'category_id' => $this->params['category_id'],
            'member_id' => $this->member_id,
        ];
        $model = new videoModel();
        $result = $model->collect($data);
        return $this->response($result);
    }

    /**
     * 短视频订单
     */
    public function orderList()
    {
        $data = [
            'page' => $this->params['page'] ?? 1,
            'page_size' => $this->params['page_size'] ?? PAGE_LIST_ROWS,
            'id' => $this->params['id'],
        ];
        $model = new videoModel();
        $result = $model->orderList($data);
        return $this->response($result);
    }

    /**
     * 视频详情
     */
    public function detail()
    {
        $token = $this->checkToken();
        $data = [
            'id' => $this->params['id'],
            'member_id' => $this->member_id,
        ];
        $model = new videoModel();
        $result = $model->detail($data);
        return $this->response($result);
    }

    /**
     * 增加转发数
     */
    public function shareAdd()
    {
        $token = $this->checkToken();
        $data = [
            'id' => $this->params['id'],
            'member_id' => $this->member_id,
        ];
        $model = new videoModel();
        $result = $model->shareAdd($data);
        return $this->response($result);
    }

    /**
     * 增加完播数
     */
    public function endAdd()
    {
        $token = $this->checkToken();
        $data = [
            'id' => $this->params['id'],
            'member_id' => $this->member_id,
        ];
        $model = new videoModel();
        $result = $model->endAdd($data);
        return $this->response($result);
    }
}