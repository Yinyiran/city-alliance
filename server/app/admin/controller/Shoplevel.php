<?php
/**
 * 商家/店铺等级管理 控制器
 */

namespace app\admin\controller;

use app\model\shop\ShopLevel as ShopLevelModel;

class Shoplevel extends BaseAdmin
{
    /**
     * 等级列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');

            $condition = [];
            if (!empty($search_text)) {
                $condition[] = ['level_name', 'like', '%' . $search_text . '%'];
            }

            $order = 'sort asc, level_id asc';
            $field = '*';

            $shop_level_model = new ShopLevelModel();
            return $shop_level_model->getLevelPageList($condition, $page, $page_size, $order, $field);
        } else {
            return $this->fetch('shoplevel/lists');
        }
    }

    /**
     * 等级添加
     */
    public function addLevel()
    {
        if (request()->isAjax()) {
            $data = [
                'level_name'        => input('level_name', ''),
                'price'             => floatval(input('price', 0.00)),
                'validity_type'     => intval(input('validity_type', 1)),
                'validity_years'    => intval(input('validity_years', 1)),
                'max_partner_count' => intval(input('max_partner_count', 0)),
                'free_partner_count'=> intval(input('free_partner_count', 0)),
                'partner_open_fee'  => floatval(input('partner_open_fee', 50.00)),
                'max_video_count'   => intval(input('max_video_count', 0)),
                'base_points'       => intval(input('base_points', 0)),
                'sort'              => intval(input('sort', 0)),
                'status'            => intval(input('status', 1)),
                'create_time'       => time()
            ];

            if (empty($data['level_name'])) {
                return error(-1, '等级名称不能为空');
            }

            $shop_level_model = new ShopLevelModel();
            $this->addLog("添加店铺等级:" . $data['level_name'] . ", 价格:" . $data['price']);
            return $shop_level_model->addLevel($data);
        } else {
            return $this->fetch('shoplevel/add_level');
        }
    }

    /**
     * 等级编辑
     */
    public function editLevel()
    {
        $shop_level_model = new ShopLevelModel();
        if (request()->isAjax()) {
            $level_id = input('level_id', 0);
            $data = [
                'level_id'          => $level_id,
                'level_name'        => input('level_name', ''),
                'price'             => floatval(input('price', 0.00)),
                'validity_type'     => intval(input('validity_type', 1)),
                'validity_years'    => intval(input('validity_years', 1)),
                'max_partner_count' => intval(input('max_partner_count', 0)),
                'free_partner_count'=> intval(input('free_partner_count', 0)),
                'partner_open_fee'  => floatval(input('partner_open_fee', 50.00)),
                'max_video_count'   => intval(input('max_video_count', 0)),
                'base_points'       => intval(input('base_points', 0)),
                'sort'              => intval(input('sort', 0)),
                'status'            => intval(input('status', 1))
            ];

            if (empty($level_id)) {
                return error(-1, '参数错误');
            }
            if (empty($data['level_name'])) {
                return error(-1, '等级名称不能为空');
            }

            $this->addLog("编辑店铺等级:" . $data['level_name'] . ", 价格:" . $data['price']);
            return $shop_level_model->editLevel($data);
        } else {
            $level_id = input('level_id', 0);
            $level_info = $shop_level_model->getLevelInfo([['level_id', '=', $level_id]]);
            $this->assign('level_info', $level_info);
            return $this->fetch('shoplevel/edit_level');
        }
    }

    /**
     * 快捷修改价格
     */
    public function editPrice()
    {
        if (request()->isAjax()) {
            $level_id = input('level_id', 0);
            $price = floatval(input('price', 0.00));

            if (empty($level_id)) {
                return error(-1, '缺少等级ID');
            }
            if ($price < 0) {
                return error(-1, '价格不能小于0');
            }

            $shop_level_model = new ShopLevelModel();
            $data = [
                'level_id' => $level_id,
                'price'    => $price
            ];

            $this->addLog("快捷修改店铺等级价格: ID={$level_id}, 价格={$price}");
            return $shop_level_model->editLevel($data);
        }
    }

    /**
     * 快捷修改排序
     */
    public function editSort()
    {
        if (request()->isAjax()) {
            $level_id = input('level_id', 0);
            $sort = intval(input('sort', 0));

            if (empty($level_id)) {
                return error(-1, '缺少等级ID');
            }

            $shop_level_model = new ShopLevelModel();
            $data = [
                'level_id' => $level_id,
                'sort'     => $sort
            ];
            return $shop_level_model->editLevel($data);
        }
    }

    /**
     * 快捷设置状态
     */
    public function setStatus()
    {
        if (request()->isAjax()) {
            $level_id = input('level_id', 0);
            $status = intval(input('status', 1));

            if (empty($level_id)) {
                return error(-1, '缺少等级ID');
            }

            $shop_level_model = new ShopLevelModel();
            $data = [
                'level_id' => $level_id,
                'status'   => $status
            ];
            $this->addLog("修改店铺等级状态: ID={$level_id}, 状态={$status}");
            return $shop_level_model->editLevel($data);
        }
    }

    /**
     * 等级删除
     */
    public function deleteLevel()
    {
        if (request()->isAjax()) {
            $level_id = input('level_id', 0);
            if (empty($level_id)) {
                return error(-1, '参数错误');
            }

            // 检查是否有店铺在使用该等级
            $count = model('shop')->getCount([['level_id', '=', $level_id]]);
            if ($count > 0) {
                return error(-1, '已有店铺属于该等级，不能删除');
            }

            $shop_level_model = new ShopLevelModel();
            $this->addLog("删除店铺等级ID:" . $level_id);
            return $shop_level_model->deleteLevel([['level_id', '=', $level_id]]);
        }
    }
}
