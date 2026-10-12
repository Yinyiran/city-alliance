<?php

namespace app\shopapi\controller;

/**
 * 商家小程序端 Banner 管理接口
 */
class Banner extends BaseApi
{
    public function __construct()
    {
        parent::__construct();
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            echo $this->response($token);
            exit;
        }
    }

    /**
     * 列表
     */
    public function lists()
    {
        $page = $this->params['page'] ?? 1;
        $page_size = $this->params['page_size'] ?? 20;
        $condition = [
            ['site_id', '=', $this->site_id]
        ];
        $list = model('banner')->pageList($condition, '*', 'sort desc, id desc', $page, $page_size);
        return $this->response($this->success($list));
    }

    /**
     * 添加
     */
    public function add()
    {
        $name = $this->params['name'] ?? '';
        $image = $this->params['image'] ?? '';
        $url = $this->params['url'] ?? '';
        $sort = $this->params['sort'] ?? 0;
        $status = $this->params['status'] ?? 1;

        if (empty($image)) {
            return $this->response($this->error('', '请上传Banner图片'));
        }

        $shop = model('shop')->getInfo([['site_id', '=', $this->site_id]], 'website_id');
        $website_id = $shop['website_id'] ?? 0;

        $data = [
            'site_id'     => $this->site_id,
            'website_id'  => $website_id,
            'name'        => $name,
            'image'       => $image,
            'url'         => $url,
            'status'      => $status,
            'sort'        => $sort,
            'create_time' => time(),
            'type'        => 3
        ];

        $res = model('banner')->add($data);
        return $this->response($this->success($res));
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = $this->params['id'] ?? 0;
        $condition = [
            ['id', '=', $id],
            ['site_id', '=', $this->site_id]
        ];

        $name = $this->params['name'] ?? '';
        $image = $this->params['image'] ?? '';
        $url = $this->params['url'] ?? '';
        $sort = $this->params['sort'] ?? 0;
        $status = $this->params['status'] ?? 1;

        if (empty($image)) {
            return $this->response($this->error('', '请上传Banner图片'));
        }

        $data = [
            'name'   => $name,
            'image'  => $image,
            'url'    => $url,
            'sort'   => $sort,
            'status' => $status
        ];

        $res = model('banner')->update($data, $condition);
        return $this->response($this->success($res));
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->params['id'] ?? 0;
        $res = model('banner')->delete([
            ['id', '=', $id],
            ['site_id', '=', $this->site_id]
        ]);
        return $this->response($this->success($res));
    }

    /**
     * 切换状态
     */
    public function modifyStatus()
    {
        $id = $this->params['id'] ?? 0;
        $status = $this->params['status'] ?? 0;
        $res = model('banner')->update(['status' => $status], [
            ['id', '=', $id],
            ['site_id', '=', $this->site_id]
        ]);
        return $this->response($this->success($res));
    }
}
