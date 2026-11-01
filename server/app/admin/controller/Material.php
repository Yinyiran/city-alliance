<?php

namespace app\admin\controller;

use app\model\BaseModel;
use think\facade\Db;

/**
 * 官方物料制作与收费配置控制器
 */
class Material extends BaseAdmin
{
    /**
     * 物料品类与收费标准列表
     */
    public function typeList()
    {
        if (request()->isAjax()) {
            $list = Db::name('shop_material_type')
                ->order('sort asc, type_id asc')
                ->select()
                ->toArray();
            return (new BaseModel())->success([
                'count' => count($list),
                'list' => $list
            ]);
        }
        return $this->fetch('material/type_list');
    }

    /**
     * 添加/编辑物料品类与收费标准
     */
    public function editType()
    {
        if (request()->isAjax()) {
            $type_id = intval(input('type_id', 0));
            $type_name = trim(input('type_name', ''));
            $material_spec = trim(input('material_spec', ''));
            $sample_image = trim(input('sample_image', ''));
            $cost_price = floatval(input('cost_price', 0));
            $shipping_fee = floatval(input('shipping_fee', 0));
            $is_enable = intval(input('is_enable', 1));
            $sort = intval(input('sort', 0));

            if (empty($type_name)) {
                return (new BaseModel())->error('', '物料名称不能为空');
            }

            $data = [
                'type_name' => $type_name,
                'material_spec' => $material_spec,
                'sample_image' => $sample_image,
                'cost_price' => $cost_price,
                'shipping_fee' => $shipping_fee,
                'is_enable' => $is_enable,
                'sort' => $sort,
            ];

            if ($type_id > 0) {
                Db::name('shop_material_type')->where('type_id', $type_id)->update($data);
                return (new BaseModel())->success('', '修改物料配置成功');
            } else {
                $data['create_time'] = time();
                Db::name('shop_material_type')->insert($data);
                return (new BaseModel())->success('', '添加物料配置成功');
            }
        }
        $type_id = intval(input('type_id', 0));
        $info = $type_id > 0 ? Db::name('shop_material_type')->where('type_id', $type_id)->find() : [];
        $this->assign('info', $info);
        return $this->fetch('material/edit_type');
    }

    /**
     * 删除物料配置
     */
    public function deleteType()
    {
        $type_id = intval(input('type_id', 0));
        if ($type_id > 0) {
            Db::name('shop_material_type')->where('type_id', $type_id)->delete();
            return (new BaseModel())->success('', '删除成功');
        }
        return (new BaseModel())->error('', '参数错误');
    }

    /**
     * 官方物料制作申领订单列表
     */
    public function orderList()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $pay_status = input('pay_status', '');
            $delivery_status = input('delivery_status', '');
            $search_text = input('search_text', '');

            $query = Db::name('shop_material_order')->alias('smo')
                ->join('shop s', 's.site_id = smo.site_id', 'left')
                ->join('shop_material_type smt', 'smt.type_id = smo.type_id', 'left')
                ->field('smo.*, s.site_name, smt.type_name, smt.material_spec');

            if ($pay_status !== '') {
                $query->where('smo.pay_status', $pay_status);
            }
            if ($delivery_status !== '') {
                $query->where('smo.delivery_status', $delivery_status);
            }
            if (!empty($search_text)) {
                $query->where(function($q) use ($search_text) {
                    $q->where('smo.order_no', 'like', "%{$search_text}%")
                      ->whereOr('s.site_name', 'like', "%{$search_text}%")
                      ->whereOr('smo.contacts_name', 'like', "%{$search_text}%")
                      ->whereOr('smo.contacts_mobile', 'like', "%{$search_text}%");
                });
            }

            $count = $query->count();
            $list = $query->order('smo.order_id desc')->page($page, $page_size)->select()->toArray();

            return (new BaseModel())->success([
                'count' => $count,
                'list' => $list
            ]);
        }
        return $this->fetch('material/order_list');
    }

    /**
     * 物料发货
     */
    public function delivery()
    {
        $order_id = intval(input('order_id', 0));
        $express_company = trim(input('express_company', ''));
        $express_no = trim(input('express_no', ''));

        if (empty($order_id)) {
            return (new BaseModel())->error('', '订单ID不能为空');
        }
        if (empty($express_no)) {
            return (new BaseModel())->error('', '快递单号不能为空');
        }

        Db::name('shop_material_order')->where('order_id', $order_id)->update([
            'express_company' => $express_company,
            'express_no' => $express_no,
            'delivery_status' => 1
        ]);

        return (new BaseModel())->success('', '发货成功');
    }
}
