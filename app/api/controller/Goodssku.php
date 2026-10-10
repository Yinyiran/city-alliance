<?php


namespace app\api\controller;

use addon\coupon\model\CouponType;
use addon\platformcoupon\model\PlatformcouponType;
use app\model\goods\Appointment;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsAttribute;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\GoodsShopCategory;
use app\model\shop\Shop as ShopModel;
use app\model\store\Store as StoreModel;
use think\Console;
use think\facade\Db;

/**
 * 商品sku
 * @author Administrator
 *
 */
class Goodssku extends BaseApi
{

    /**
     * 基础信息
     */
    public function info()
    {
        $sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }
        $goods = new GoodsModel();
        $info = $goods->getGoodsSkuDetail($sku_id);

        $token = $this->checkToken();
        if ($token[ 'code' ] >= 0) {
            // 是否参与会员等级折扣
            $goods_member_price = $goods->getGoodsPrice($sku_id, $this->member_id);
            $goods_member_price = $goods_member_price['data'];
            if (!empty($goods_member_price['member_price'])) {
                $info['data']['member_price'] = $goods_member_price['member_price'];
            }
            if ($info[ 'data' ][ 'max_buy' ] > 0) $res[ 'goods_sku_detail' ][ 'purchased_num' ] = $goods->getGoodsPurchasedNum($info[ 'data' ][ 'goods_id' ], $this->member_id);
        }

        // 查询当前商品参与的营销活动信息
        $goods_promotion = event('GoodsPromotion', [ 'goods_id' => $info[ 'data' ][ 'goods_id' ], 'sku_id' => $info[ 'data' ][ 'sku_id' ] ]);
        $info[ 'data' ][ 'goods_promotion' ] = $goods_promotion;
        //判断是否参与预售
        $is_join_presale = event('IsJoinPresale',['sku_id' => $sku_id],true);
        if (!empty($is_join_presale) && $is_join_presale['code'] == 0) {
            $info['data'] = array_merge($info['data'], $is_join_presale['data']);
        }
        return $this->response($info);
    }

    /**
     * 详情信息
     */
    public function detail()
    {
        $sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }

        $res = [];

        $goods_model = new GoodsModel();
        $goods_sku_detail = $goods_model->getGoodsSkuDetail($sku_id);
        $goods_sku_detail = $goods_sku_detail[ 'data' ];
        $res[ 'goods_sku_detail' ] = $goods_sku_detail;

        if (empty($goods_sku_detail)) return $this->response($this->error($res));

        $res[ 'goods_sku_detail' ][ 'purchased_num' ] = 0; // 该商品已购数量

        $token = $this->checkToken();
        $fan_point = 0;
        if ($token[ 'code' ] >= 0) {

            // 是否参与会员等级折扣
            $goods_member_price = $goods_model->getGoodsPrice($sku_id, $this->member_id);
            $goods_member_price = $goods_member_price['data'];

            if (!empty($goods_member_price['member_price'])) {
                $res['goods_sku_detail']['member_price'] = $goods_member_price['member_price'];
            }
            if ($goods_sku_detail[ 'max_buy' ] > 0) $res[ 'goods_sku_detail' ][ 'purchased_num' ] = $goods_model->getGoodsPurchasedNum($goods_sku_detail[ 'goods_id' ], $this->member_id);

            //返积分数量
            $fan_point = $this->fanPoint($this->member_id, $res['goods_sku_detail']);
        }

        $res[ 'goods_sku_detail' ][ 'fan_point' ] = $fan_point;

        //店铺信息
        $shop_model = new ShopModel();
        $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $goods_sku_detail[ 'site_id' ] ] ], 'site_id,site_name,is_own,logo,avatar,banner,seo_description,qq,ww,telephone,shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_baozh,shop_baozhopen,shop_baozhrmb,shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_sales,sub_num');

        $shop_info = $shop_info[ 'data' ];
        $res[ 'shop_info' ] = $shop_info;

        // 查询当前商品参与的营销活动信息
        $goods_promotion = event('GoodsPromotion', [ 'goods_id' => $goods_sku_detail[ 'goods_id' ], 'sku_id' => $goods_sku_detail[ 'sku_id' ] ]);
        $res[ 'goods_sku_detail' ][ 'goods_promotion' ] = $goods_promotion;
        //判断是否参与预售
        $is_join_presale = event('IsJoinPresale',['sku_id' => $sku_id],true);
        if (!empty($is_join_presale) && $is_join_presale['code'] == 0) {
            $res['goods_sku_detail'] = array_merge($res['goods_sku_detail'], $is_join_presale['data']);
        }
        //门店信息
        //营业时间 店面地址 门店电话
        $store = model('store')->getInfo(['site_id'=>$shop_info['site_id']]);
        $res[ 'shop_info' ]['open_date'] = $store['open_date'] ?? '';
        $res[ 'shop_info' ]['province_id'] = $store['province_id'] ?? '';
        $res[ 'shop_info' ]['city_id'] = $store['city_id'] ?? '';
        $res[ 'shop_info' ]['district_id'] = $store['district_id'] ?? '';
        $res[ 'shop_info' ]['address'] = $store['address'] ?? '';
        $res[ 'shop_info' ]['longitude'] = $store['longitude'] ?? '';
        $res[ 'shop_info' ]['latitude'] = $store['latitude'] ?? '';
        $res[ 'shop_info' ]['telphone'] = $store['telphone'] ?? '';
        $full_address = $store['full_address'];
        $address = $store['address'] ?? '';
        if ($full_address) {
            $address = str_replace(',', '', $full_address) . $address;
        }

        $res[ 'shop_info' ]['address'] = $address;

        //交易类型相关信息（到店自提/到店消费/预约到店/快递邮寄/外卖配送）
        $trade_type = intval($res[ 'goods_sku_detail' ][ 'trade_type' ] ?? 0);
        $trade_info = [
            'trade_type' => $trade_type,
            'trade_type_name' => \app\model\order\OrderCreate::tradeTypeName($trade_type),
            'service_duration' => intval($res[ 'goods_sku_detail' ][ 'service_duration' ] ?? 0),
            'allow_custom_amount' => intval($res[ 'goods_sku_detail' ][ 'allow_custom_amount' ] ?? 0),
            'min_order_amount' => $res[ 'goods_sku_detail' ][ 'min_order_amount' ] ?? 0,
            'max_order_amount' => $res[ 'goods_sku_detail' ][ 'max_order_amount' ] ?? 0,
            'store_list' => [],
            'date_list' => [],
        ];
        //到店自提/到店消费/预约到店均需到店扫码核销，返回可核销门店
        if (\app\model\order\OrderCreate::isStoreTradeType($trade_type)) {
            $store_list = model('store')->getList([
                [ 'site_id', '=', $goods_sku_detail[ 'site_id' ] ],
                [ 'is_pickup', '=', 1 ],
                [ 'status', '=', 1 ],
                [ 'is_frozen', '=', 0 ],
            ], 'store_id,store_name,address,telphone,open_date,longitude,latitude', 'store_id asc', 'a', [], '', 50);
            $trade_info[ 'store_list' ] = $store_list;
        }
        //预约到店返回可预约日期
        if ($trade_type == 2) {
            $appointment_model = new Appointment();
            $date_res = $appointment_model->getDateList($goods_sku_detail[ 'goods_id' ], 0, 7);
            $trade_info[ 'date_list' ] = $date_res[ 'data' ] ?? [];
        }
        $res[ 'goods_sku_detail' ][ 'trade_info' ] = $trade_info;

        return $this->response($this->success($res));


    }

    /**
     * 商品详情返积分数
     */
    public function fanPoint($member_id, $goods_sku_detail)
    {
        $fan_point = 0;
        if ($goods_sku_detail['goods_type'] == 0) { //核销商品
            $member = model('member')->getInfo(['member_id'=>$member_id]);
            $city_member_level = model('city_member_level')->getInfo([
                'level_id' => $member['member_level'],
                'website_id' => $goods_sku_detail['website_id'],
            ]);
            if ($city_member_level && $city_member_level['point_feedback'] > 0) {
                $p_rate = $city_member_level['point_feedback'];
                $jc_money = $goods_sku_detail['member_price'] * $p_rate;
                $fan_point = floatval(number_format($jc_money, 2, '.', ''));
            }
        }
        return $fan_point;
    }

    /**
     * 商品详情，商品分类用
     * @return false|string
     */
    public function getInfoForCategory()
    {

        $sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }

        $goods = new GoodsModel();
        $field = 'gs.goods_id,gs.sku_id,gs.goods_name,gs.sku_name,gs.sku_spec_format,gs.price,gs.discount_price,gs.promotion_type,gs.stock,gs.sku_image,gs.goods_spec_format,gs.unit,gs.is_virtual,gs.max_buy,gs.min_buy,g.goods_image';
        $goods_sku_detail = $goods->getGoodsSkuDetail($sku_id, $field);

        $token = $this->checkToken();
        if ($token['code'] >= 0) {
            // 是否参与会员等级折扣
            $goods_member_price = $goods->getGoodsPrice($sku_id, $this->member_id);
            $goods_member_price = $goods_member_price['data'];
            if (!empty($goods_member_price['member_price'])) {
                $goods_sku_detail['data']['member_price'] = $goods_member_price['member_price'];
            }
            if ($goods_sku_detail['data']['max_buy'] > 0) $goods_sku_detail['data']['purchased_num'] = $goods->getGoodsPurchasedNum($goods_sku_detail['data']['goods_id'], $this->member_id);
        }
        return $this->response($goods_sku_detail);
    }

    /**
     * 列表信息
     */
    public function page()
    {
        $page = $this->params[ 'page' ] ?? 1;
        $page_size = $this->params[ 'page_size' ] ?? PAGE_LIST_ROWS;
        $site_id = $this->params[ 'site_id' ] ?? 0; //站点id
        $website_id = $this->params[ 'website_id' ] ?? 0; //城市分站站点id
        $goods_id_arr = $this->params[ 'goods_id_arr' ] ?? ''; //sku_id数组
        $keyword = $this->params[ 'keyword' ] ?? ''; //关键词
        $category_id = $this->params[ 'category_id' ] ?? 0;//分类
        $brand_id = $this->params[ 'brand_id' ] ?? 0; //品牌
        $min_price = $this->params[ 'min_price' ] ?? 0; //价格区间，小
        $max_price = $this->params[ 'max_price' ] ?? 0; //价格区间，大
        $is_free_shipping = $this->params[ 'is_free_shipping' ] ?? -1; //是否免邮
        $is_own = $this->params[ 'is_own' ] ?? ''; //是否自营
        $order = $this->params[ 'order' ] ?? "create_time"; //排序（综合、销量、价格）
        $sort = $this->params[ 'sort' ] ?? "desc"; //升序、降序
        $attr = $this->params[ 'attr' ] ?? ""; //属性json
        $shop_category_id = $this->params[ 'shop_category_id' ] ?? 0;//店内分类
        $condition = [];

        $field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.price,gs.market_price,gs.discount_price,gs.stock,
        gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,gs.website_id,gs.is_own,gs.is_free_shipping,
        gs.introduction,gs.promotion_type,gs.create_time,gs.goods_state,gs.goods_class,gs.goods_class_name,
        gs.max_buy,gs.min_buy,g.goods_image,g.site_name,gs.goods_spec_format,gs.is_virtual,gs.recommend_way,
        s.site_name,s.logo,gs.trade_type,gs.service_duration,g.allow_custom_amount,g.min_order_amount,g.max_order_amount';

        $alias = 'gs';
        $join = [
            [ 'goods g', 'gs.sku_id = g.sku_id', 'inner' ],
            [ 'shop s', 's.site_id = gs.site_id', 'inner' ]
        ];

        //只查看处于开启状态的店铺
        $condition[] = [ 's.shop_status', '=', 1 ];

        if (!empty($site_id)) {
            $condition[] = [ 'gs.site_id', '=', $site_id ];
        }
        if (!empty($website_id)) {

            $condition[] = [ 'gs.website_id', '=', $website_id ];
        }

        if (!empty($goods_id_arr)) {
            $condition[] = [ 'gs.goods_id', 'in', $goods_id_arr ];
        }

        if (!empty($keyword)) {
            $condition[] = [ 'g.goods_name|gs.sku_name|gs.keywords', 'like', '%' . $keyword . '%' ];
        }

        if (!empty($category_id)) {
            $goods_category_model = new GoodsCategoryModel();

            // 查询当前
            $category_list = $goods_category_model->getCategoryList([ [ 'category_id', '=', $category_id ], [ 'is_show', '=', 1 ] ], 'category_id,pid,level');
            $category_list = $category_list[ 'data' ];

            // 查询子级
            $category_child_list = $goods_category_model->getCategoryList([ [ 'pid', '=', $category_id ], [ 'is_show', '=', 1 ] ], 'category_id,pid,level');
            $category_child_list = $category_child_list[ 'data' ];

            $temp_category_list = [];
            if (!empty($category_list)) {
                $temp_category_list = $category_list;
            } elseif (!empty($category_child_list)) {
                $temp_category_list = $category_child_list;
            }

            if (!empty($temp_category_list)) {
                $category_id_arr = [];
                foreach ($temp_category_list as $k => $v) {
                    // 三级分类，并且都能查询到
                    if ($v[ 'level' ] == 3 && !empty($category_list) && !empty($category_child_list)) {
                        array_push($category_id_arr, $v[ 'pid' ]);
                    } else {
                        array_push($category_id_arr, $v[ 'category_id' ]);
                    }
                }
                $category_id_arr = array_unique($category_id_arr);
                $temp_condition = [];
                foreach ($category_id_arr as $ck => $cv) {
                    $temp_condition[] = '%,' . $cv . ',%';
                }
                $category_condition = $temp_condition;
                $condition[] = [ 'g.category_id', 'like', $category_condition, 'or' ];
            }
        }

        if (!empty($brand_id)) {
            $condition[] = [ 'gs.brand_id', '=', $brand_id ];
        }

        if ($min_price != "" && $max_price != "") {
            $condition[] = [ 'gs.discount_price', 'between', [ $min_price, $max_price ] ];
        } elseif ($min_price != "") {
            $condition[] = [ 'gs.discount_price', '>=', $min_price ];
        } elseif ($max_price != "") {
            $condition[] = [ 'gs.discount_price', '<=', $max_price ];
        }

        if (isset($is_free_shipping) && !empty($is_free_shipping) && $is_free_shipping > -1) {
            $condition[] = [ 'gs.is_free_shipping', '=', $is_free_shipping ];
        }

        if ($is_own !== '') {
            $condition[] = [ 'gs.is_own', '=', $is_own ];
        }

        // 非法参数进行过滤
        if ($sort != "desc" && $sort != "asc") {
            $sort = "";
        }

        // 非法参数进行过滤
        if ($order != '') {
            if ($order != "sale_num" && $order != "discount_price") {
                $order = 'gs.sort';
            } else {
                $order = 'gs.' . $order;
            }
            $order_by = $order . ' ' . $sort;
        } else {
            $order_by = 'gs.sort desc,gs.create_time desc';
        }

        //拿到商品属性，查询sku_id
        if (!empty($attr)) {
            $attr = json_decode($attr, true);
            $goods_attribute = new GoodsAttribute();
            $attr_sku_ids = [];
            $attr_is_first = true;
            foreach($attr as $k => $v){
                $attribute_condition = [
                    [ 'attr_id', '=', $v['attr_id']],
                    [ 'attr_value_id', '=', $v[ 'attr_value_id' ] ],
                    [ 'app_module', '=', 'shop' ]
                ];
                $attribute_list = $goods_attribute->getAttributeIndexList($attribute_condition, 'sku_id')['data'] ?? [];
                $item_attr_sku_ids = array_column($attribute_list, 'sku_id');
                if($attr_is_first){
                    $attr_sku_ids = $item_attr_sku_ids;
                    $attr_is_first = false;
                }else{
                    if(!empty($attr_sku_ids)){
                        $attr_sku_ids = array_merge($attr_sku_ids, $item_attr_sku_ids);
                    }else{
                       continue;
                    }

                }

            }
            if(empty($attr_sku_ids)){
                $attr_sku_ids = [0];
            }
            $attr_sku_ids = array_unique($attr_sku_ids);
            $condition[] = [
                    [ 'gs.sku_id', 'in', $attr_sku_ids ]
            ];

        }

        $condition[] = [ 'gs.goods_state', '=', 1 ];
        $condition[] = [ 'gs.verify_state', '=', 1 ];
        $condition[] = [ 'gs.is_delete', '=', 0 ];

        // 优惠券
        $coupon = $this->params[ 'coupon' ] ?? 0; //优惠券
        if ($coupon > 0) {
            $coupon_type = new CouponType();
            $coupon_type_info = $coupon_type->getInfo([
                [ 'coupon_type_id', '=', $coupon ],
//                ['site_id', '=', $site_id],
                [ 'goods_type', '=', 2 ]
            ], 'goods_ids');
            $coupon_type_info = $coupon_type_info[ 'data' ];
            if (isset($coupon_type_info[ 'goods_ids' ]) && !empty($coupon_type_info[ 'goods_ids' ])) {
                $condition[] = [ 'g.goods_id', 'in', explode(',', trim($coupon_type_info[ 'goods_ids' ], ',')) ];
            }
        }

        //平台优惠券
        $platform_coupon = $this->params[ 'platform_coupon_type' ] ?? 0; //平台优惠券
        if ($platform_coupon > 0) {
            $platform_coupon_type = new PlatformcouponType();
            $platform_coupon_type_info = $platform_coupon_type->getInfo([
                [ 'platformcoupon_type_id', '=', $platform_coupon ],
                [ 'use_scenario', '=', 2 ]
            ], 'group_ids');
            $platform_coupon_type_info = $platform_coupon_type_info[ 'data' ];
            if (!empty($platform_coupon_type_info)) {
                $condition[] = [ 's.group_id', 'in', explode(',', trim($platform_coupon_type_info[ 'group_ids' ], ',')) ];
            }
        }

        //店内分类
        if ($shop_category_id > 0) {
            $shop_category_condition = [
                $shop_category_id,
                '%' . $shop_category_id . ',%',
                '%' . $shop_category_id,
                '%,' . $shop_category_id . ',%',
            ];
            $shop_goods_category_model = new GoodsShopCategory();
            $child_category_list = $shop_goods_category_model->getShopCategoryList([ [ 'pid', '=', $shop_category_id ] ], 'category_id')[ 'data' ] ?? [];
            if (!empty($child_category_list)) {
                foreach ($child_category_list as $child_category_item) {
                    $child_shop_category_id = $child_category_item[ 'category_id' ];
                    $shop_category_condition = array_merge([
                        $child_shop_category_id,
                        '%' . $child_shop_category_id . ',%',
                        '%' . $child_shop_category_id,
                        '%,' . $child_shop_category_id . ',%',
                    ], $shop_category_condition);
                }
            }
            $condition[] = [ 'gs.goods_shop_category_ids', 'like', $shop_category_condition, 'or' ];
        }



        $goods = new GoodsModel();
        $list = $goods->getGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);

        $token = $this->checkToken();
        if ($token['code'] >= 0) {
            if (!empty($list['data']['list'])) {
                foreach ($list['data']['list'] as $k => $v) {

                    // 是否参与会员等级折扣
                    $goods_member_price = $goods->getGoodsPrice($v['sku_id'], $this->member_id);
                    $goods_member_price = $goods_member_price['data'];
                    if (!empty($goods_member_price['member_price'])) {
                        $list['data']['list'][$k]['member_price'] = $goods_member_price['member_price'];
                    }

                    $list['data']['list'][$k]['purchased_num'] = 0; // 该商品已购数量

                    if ($v['max_buy'] > 0) $list['data']['list'][$k]['purchased_num'] = $goods->getGoodsPurchasedNum($v['goods_id'], $this->member_id);
                }
            }
        }

        return $this->response($list);

    }

    /**
     * 商品推荐
     * @return string
     */
    public function recommend()
    {
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        $condition = [
            [ 'gs.goods_state', '=', 1 ],
            [ 'gs.verify_state', '=', 1 ],
            [ 'gs.is_delete', '=', 0 ]
        ];
        $goods = new GoodsModel();
        $field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.price,gs.market_price,gs.discount_price,gs.stock,gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,gs.website_id,gs.is_own,gs.is_free_shipping,gs.introduction,gs.promotion_type,g.goods_image';
        $alias = 'gs';
        $join = [
            [ 'goods g', 'gs.sku_id = g.sku_id', 'inner' ]
        ];
        $order_by = 'gs.sort asc,gs.create_time desc';

        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = gs.site_id', 'inner' ];
        $condition[] = [ 's.shop_status', '=', 1 ];

        $list = $goods->getGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);

        $token = $this->checkToken();
        if (!empty($list[ 'data' ][ 'list' ])) {
            foreach ($list[ 'data' ][ 'list' ] as $k => $v) {
                if ($token[ 'code' ] >= 0) {
                    // 是否参与会员等级折扣
                    $goods_member_price = $goods->getGoodsPrice($v[ 'sku_id' ], $this->member_id);
                    $goods_member_price = $goods_member_price[ 'data' ];
                    if (!empty($goods_member_price[ 'member_price' ])) {
                        $list[ 'data' ][ 'list' ][ $k ][ 'member_price' ] = $goods_member_price[ 'member_price' ];
                    }
                }
            }
        }
        return $this->response($list);
    }

    /**
     * 商品二维码
     * return
     */
    public function goodsQrcode()
    {
        $sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }
        $goods_model = new GoodsModel();
        $goods_sku_info = $goods_model->getGoodsSkuInfo([ [ 'sku_id', '=', $sku_id ] ], 'sku_id,goods_name');
        $goods_sku_info = $goods_sku_info[ 'data' ];
        $res = $goods_model->qrcode($goods_sku_info[ 'sku_id' ], $goods_sku_info[ 'goods_name' ]);
        return $this->response($res);
    }

    /***
     * 新商品列表
     * 按照距离排序
     */
    public function goodsList()
    {
        $page = $this->params[ 'page' ] ?? 1;
        $page_size = $this->params[ 'page_size' ] ?? PAGE_LIST_ROWS;
        $website_id = $this->params[ 'website_id' ] ?? 0; //城市分站站点id
        $keyword = $this->params[ 'keyword' ] ?? ''; //关键词
        $category_id = $this->params[ 'category_id' ] ?? 0;//分类
        $order = $this->params[ 'order' ] ?? "create_time"; //排序（综合、销量、价格）
        $sort = $this->params[ 'sort' ] ?? "desc"; //升序、降序
        $latitude = $this->params["latitude"] ?? null;
        $longitude = $this->params["longitude"] ?? null;
        $goods_type = isset($this->params['goods_type']) ? $this->params['goods_type'] : "0";//商品类型:0=普通商品,1=福利品
        $platform = isset($this->params['platform']) ? $this->params['platform'] : "0";//全国包邮商品:0=否,1=是
        $min_point = $this->params[ 'min_point' ] ?? ''; //积分区间，小
        $max_point = $this->params[ 'max_point' ] ?? ''; //积分区间，大
        $site_id = $this->params[ 'site_id' ] ?? '';
        if (!$goods_type) $goods_type = '0';
        if (!$platform) $platform = '0';
        $store_model = new StoreModel();
        $condition = [
            ['status', '=', 1],
            ['is_frozen', '=', 0],
            ['is_pickup', '=', 1],
        ];

        if (!empty($website_id) && $platform == 0) {

            $condition[] = [ 'gs.website_id', '=', $website_id ];
        }

        if ($site_id && $platform == 0) {
            $condition[] = [ 'gs.site_id', '=', $site_id ];
        }

        if (!empty($keyword)) {
            $condition[] = [ 'g.goods_name|gs.sku_name|gs.keywords', 'like', '%' . $keyword . '%' ];
        }
        if ($goods_type != '') {
            $condition[] = ['g.goods_type', '=', $goods_type];
        }
        if ($platform != '') {
            $condition[] = ['g.is_admin', '=', $platform];
        }

        if (!empty($category_id)) {
            $goods_category_model = new GoodsCategoryModel();

            // 查询当前
            $category_list = $goods_category_model->getCategoryList([ [ 'category_id', '=', $category_id ], [ 'is_show', '=', 1 ] ], 'category_id,pid,level');
            $category_list = $category_list[ 'data' ];

            // 查询子级
            $category_child_list = $goods_category_model->getCategoryList([ [ 'pid', '=', $category_id ], [ 'is_show', '=', 1 ] ], 'category_id,pid,level');
            $category_child_list = $category_child_list[ 'data' ];

            $temp_category_list = [];
            if (!empty($category_list)) {
                $temp_category_list = $category_list;
            } elseif (!empty($category_child_list)) {
                $temp_category_list = $category_child_list;
            }

            if (!empty($temp_category_list)) {
                $category_id_arr = [];
                foreach ($temp_category_list as $k => $v) {
                    // 三级分类，并且都能查询到
                    if ($v[ 'level' ] == 3 && !empty($category_list) && !empty($category_child_list)) {
                        array_push($category_id_arr, $v[ 'pid' ]);
                    } else {
                        array_push($category_id_arr, $v[ 'category_id' ]);
                    }
                }
                $category_id_arr = array_unique($category_id_arr);
                $temp_condition = [];
                foreach ($category_id_arr as $ck => $cv) {
                    $temp_condition[] = '%,' . $cv . ',%';
                }
                $category_condition = $temp_condition;
                $condition[] = [ 'g.category_id', 'like', $category_condition, 'or' ];
            }
        }

        if (!empty($min_point) && !empty($max_point)) {
            $condition[] = [ 'gs.point', 'between', [ $min_point, $max_point ] ];
        } elseif (!empty($min_point)) {
            $condition[] = [ 'gs.point', '>=', $min_point ];
        } elseif (!empty($max_point)) {
            $condition[] = [ 'gs.point', '<=', $max_point ];
        }

        // 非法参数进行过滤
        if ($sort != "desc" && $sort != "asc") {
            $sort = "";
        }

        // 非法参数进行过滤
        $order_distance = 0;
        if ($order != '') {
            if ($order == 'distance') {
                $order_distance = 1;
            }
            if ($order != "sale_num" && $order != "discount_price") {
                $order = 'gs.sort';
            } else {
                $order = 'gs.' . $order;
            }
            $order_by = $order . ' ' . $sort;
        } else {
            $order_by = 'gs.sort desc,gs.create_time desc';
        }



        $condition[] = [ 'gs.goods_state', '=', 1 ];
        $condition[] = [ 'gs.verify_state', '=', 1 ];
        $condition[] = [ 'gs.is_delete', '=', 0 ];

        $latlng = array(
            'lat' => $latitude ?: null,
            'lng' => $longitude ?: null,
        );

        $field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.price,gs.market_price,gs.discount_price,gs.stock,
        gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,gs.website_id,gs.is_own,gs.is_free_shipping,
        gs.introduction,gs.promotion_type,gs.create_time,gs.goods_state,gs.goods_class,gs.goods_class_name,
        gs.max_buy,gs.min_buy,g.goods_image,g.site_name,gs.goods_spec_format,gs.is_virtual,gs.recommend_way,
        shop.site_name,shop.logo,gs.click_num,gs.point,gs.trade_type,gs.service_duration,g.allow_custom_amount,g.min_order_amount,g.max_order_amount';

        $store_res = $store_model->getStoreOrder($condition, $field, $latlng); //距离排序order
        $condition = $store_res['condition'];
        $field = $store_res['field'];

        if ($order_distance) { //按照距离排序
            $order_by = $store_res['order'];
        }
        $alias = 's';
        $join = [
            ['shop', 's.site_id = shop.site_id', 'inner'],
            ['goods_sku gs', 's.site_id = gs.site_id', 'inner'],
            [ 'goods g', 'gs.sku_id = g.sku_id', 'inner' ],
        ];
        $list = model('store')->pageList($condition, $field, $order_by, $page, $page_size, $alias, $join);

        $goods = new GoodsModel();
        $token = $this->checkToken();
        if ($token['code'] >= 0) {
            if (!empty($list['data']['list'])) {
                foreach ($list['data']['list'] as $k => $v) {

                    // 是否参与会员等级折扣
                    $goods_member_price = $goods->getGoodsPrice($v['sku_id'], $this->member_id);
                    $goods_member_price = $goods_member_price['data'];
                    if (!empty($goods_member_price['member_price'])) {
                        $list['data']['list'][$k]['member_price'] = $goods_member_price['member_price'];
                    }

                    $list['data']['list'][$k]['purchased_num'] = 0; // 该商品已购数量

                    if ($v['max_buy'] > 0) $list['data']['list'][$k]['purchased_num'] = $goods->getGoodsPurchasedNum($v['goods_id'], $this->member_id);
                }
            }
        }
        return $this->response($this->success($list));
    }
}
