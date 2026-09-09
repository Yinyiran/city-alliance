<?php

namespace app\shop\controller;

/**
 * 商家端 Banner 管理
 */
class Banner extends BaseShop
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Banner 列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('limit', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [
                ['site_id', '=', $this->site_id]
            ];

            if (!empty($search_text)) {
                $condition[] = ['name', 'like', '%' . $search_text . '%'];
            }

            $list = model('banner')->pageList($condition, '*', 'sort desc, id desc', $page_index, $page_size);
            return $this->success($list);
        } else {
            return $this->fetch('banner/lists');
        }
    }

    /**
     * 添加 Banner
     */
    public function add()
    {
        if (request()->isAjax()) {
            $name = input('name', '');
            $image = input('image', '');
            $url = input('url', '');
            $status = input('status', 1);
            $sort = input('sort', 0);

            if (empty($image)) {
                return $this->error('', '请上传Banner图片');
            }

            // 获取店铺所属城市分站ID
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
                'type'        => 3 // 商家页面/门店Banner
            ];

            $res = model('banner')->add($data);
            return $this->success($res);
        } else {
            return $this->fetch('banner/add');
        }
    }

    /**
     * 编辑 Banner
     */
    public function edit()
    {
        $id = input('id', 0);
        $condition = [
            ['id', '=', $id],
            ['site_id', '=', $this->site_id]
        ];

        if (request()->isAjax()) {
            $name = input('name', '');
            $image = input('image', '');
            $url = input('url', '');
            $status = input('status', 1);
            $sort = input('sort', 0);

            if (empty($image)) {
                return $this->error('', '请上传Banner图片');
            }

            $data = [
                'name'   => $name,
                'image'  => $image,
                'url'    => $url,
                'status' => $status,
                'sort'   => $sort
            ];

            $res = model('banner')->update($data, $condition);
            return $this->success($res);
        } else {
            $info = model('banner')->getInfo($condition);
            if (empty($info)) {
                $this->error('未获取到该Banner信息');
            }
            $this->assign('info', $info);
            return $this->fetch('banner/edit');
        }
    }

    /**
     * 删除 Banner
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $id = input('id', 0);
            $res = model('banner')->delete([
                ['id', '=', $id],
                ['site_id', '=', $this->site_id]
            ]);
            return $this->success($res);
        }
    }

    /**
     * 修改状态
     */
    public function modifyStatus()
    {
        if (request()->isAjax()) {
            $id = input('id', 0);
            $status = input('status', 0);
            $res = model('banner')->update(['status' => $status], [
                ['id', '=', $id],
                ['site_id', '=', $this->site_id]
            ]);
            return $this->success($res);
        }
    }
}
