<?php

/**

 */

namespace app\model\goods;


use app\model\BaseModel;

/**
 * 购物车
 */
class Cart extends BaseModel
{

    /**
     * 添加购物车
     * @param $data
     * @return array
     */
    public function addCart($data)
    {
        $cart_info = model("goods_cart")->getInfo([ [ 'sku_id', '=', $data[ 'sku_id' ] ], [ 'member_id', '=', $data[ 'member_id' ] ] ], 'cart_id, num');
        if (!empty($cart_info)) {
            $res = model("goods_cart")->update([ 'num' => $cart_info[ 'num' ] + $data[ 'num' ] ], [ [ 'cart_id', '=', $cart_info[ 'cart_id' ] ] ]);
        } else {
            $sku_info = model('goods_sku')->getInfo([ [ 'sku_id', '=', $data[ 'sku_id' ] ] ]);
            if (empty($sku_info))
                return $this->error();

            $site_id = $sku_info[ 'site_id' ] ?? 0;
            $data[ 'site_id' ] = $site_id;
            $res = model("goods_cart")->add($data);
        }
        return $this->success($res);
    }

    /**
     * 更新购物车商品数量
     * @param $data
     * @return array
     */
    public function editCart($data)
    {
        $res = model("goods_cart")->update([ 'num' => $data[ 'num' ] ], [ [ 'cart_id', '=', $data[ 'cart_id' ] ], [ 'member_id', '=', $data[ 'member_id' ] ] ]);
        return $this->success($res);
    }

    /**
     * 编辑购物车
     * @param $data
     * @param $condition
     */
    public function updateCart($data, $condition){
        $res = model("goods_cart")->update($data, $condition);
        return $this->success($res);
    }
    /**
     * 删除购物车商品项(可以多项)
     * @param $data
     * @return array
     */
    public function deleteCart($data)
    {
        $res = model("goods_cart")->delete([ [ 'cart_id', 'in', explode(',', $data[ 'cart_id' ]) ], [ 'member_id', '=', $data[ 'member_id' ] ] ]);
        return $this->success($res);
    }

    /**
     * 清空购物车
     * @param $data
     * @return array
     */
    public function clearCart($data)
    {
        $res = model("goods_cart")->delete([ [ 'member_id', '=', $data[ 'member_id' ] ] ]);
        return $this->success($res);
    }

    /**
     * 获取购物车
     * @param $data
     * @return array
     */
    public function getCartInfo($condition)
    {
        $res = model("goods_cart")->getInfo($condition);
        return $this->success($res);
    }

    /**
     * 获取会员购物车
     * @param $member_id
     * @return array
     */
    public function getCart($member_id, $type = '')
    {

        $field = 'ngc.cart_id, ngc.site_id, ngc.member_id, ngc.sku_id, ngc.num, ngs.sku_name,
            ngs.sku_no, ngs.sku_spec_format, ngs.price, ngs.market_price, 
            ngs.discount_price, ngs.promotion_type,ngs.stock, 
            ngs.sku_image, ngs.site_name, ngs.website_id, ngs.is_own, ngs.goods_state, 
            ngs.verify_state, ngs.goods_name,ngs.unit,s.shop_status,ngs.max_buy, ngs.min_buy,ngc.goods_type,ngs.point';

        $alias = 'ngc';
        $join = [
            [
                'goods_sku ngs',
                'ngc.sku_id = ngs.sku_id',
                'inner'
            ],
        ];

        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = ngc.site_id', 'inner' ];

        $condition = [[ 'ngc.member_id', '=', $member_id ]];
        if ($type != '' && $type != 'all') {
            if ($type == 1) { //积分商品
                $condition[] = ['ngc.goods_type', 'in', [1,2]];
            } else {
                $condition[] = ['ngc.goods_type', '=', $type];
            }

        }

        $list = model("goods_cart")->getList($condition, $field, '', $alias, $join);
        return $this->success($list);
    }

    /**
     * 获取购物车数量
     * @param $member_id
     * @return array
     */
    public function getCartCount($member_id)
    {
        $list = model("goods_cart")->getCount([ [ 'member_id', '=', $member_id ] ]);
        return $this->success($list);
    }

    /**
     * 商品购物车列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getCartList($condition = [], $field = 'cart_id,site_id,member_id,sku_id,num', $order = 'cart_id desc')
    {
        $alias = 'gc';
        $join = [
            [
                'goods_sku gs',
                'gc.sku_id = gs.sku_id',
                'left'
            ]
        ];
        $list = model("goods_cart")->getList($condition, $field, $order, $alias, $join);
        return $this->success($list);
    }
}
