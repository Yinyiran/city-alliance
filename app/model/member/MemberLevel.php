<?php
/**

 */

namespace app\model\member;

use think\facade\Cache;
use app\model\BaseModel;
use app\model\system\Pay;

/**
 * 会员等级
 */
class MemberLevel extends BaseModel
{

    /**
     * 添加会员等级
     *
     * @param array $data
     */
    public function addMemberLevel($data)
    {
        $is_default = $data[ 'is_default' ] ?? 0;

        //判断是否重复
        $count = model('member_level')->getCount([ [ 'growth', '=', $data[ 'growth' ] ] ]);
        if ($count > 0) return $this->error('', '等级成长值重复');
        $count = model('member_level')->getCount([ [ 'level_name', '=', $data[ 'level_name' ] ] ]);
        if ($count > 0) return $this->error('', '等级名称已存在');

        if ($is_default == 1) {
            $this->refreshLevel();
        }
        $res = model('member_level')->add($data);
        $this->refreshSort();
        Cache::tag("member_level")->clear();
        return $this->success($res);
    }

    /**
     * 修改会员等级(不允许批量处理)
     *
     * @param array $data
     * @param array $condition
     */
    public function editMemberLevel($data, $condition)
    {
        $is_default = $data[ 'is_default' ] ?? 0;

        //判断是否重复
//        $count = model('member_level')->getCount([['growth', '=', input('growth', 0)]]);
//        if($count > 0) return $this->error('', '等级成长值重复');
//        $count = model('member_level')->getCount([['level_name', '=', $data['level_name']]]);
//        if($count > 0) return $this->error('', '等级名称重复');

        if ($is_default == 1) {
            $this->refreshLevel();
        }
        $res = model('member_level')->update($data, $condition);

        $check_condition = array_column($condition, 2, 0);
        if (isset($data['level_name']) && isset($check_condition['level_id'])) {
            model('member')->update([ "member_level_name" => $data[ "level_name" ] ], [ [ "member_level", "=", $check_condition[ "level_id" ] ] ]);
        }
        $this->refreshSort();
        Cache::tag('member_level')->clear();
        return $this->success($res);
    }

    /**
     * 刷新会员等级排序
     */
    private function refreshSort()
    {
        $list = model('member_level')->getList([], 'level_id, growth', 'growth asc');
        foreach ($list as $k => $v) {
            model('member_level')->update([ 'sort' => $k ], [ [ 'level_id', '=', $v[ 'level_id' ] ] ]);
        }
    }

    /**
     * 刷新会员等级
     */
    private function refreshLevel()
    {
        model('member_level')->update([ 'is_default' => 0 ], [ [ 'is_default', '=', 1 ] ]);
    }

    /**
     * 删除会员等级
     * @param array $condition
     */
    public function deleteMemberLevel($condition)
    {
        $res = model('member_level')->delete($condition);
        $this->refreshSort();
        Cache::tag("member_level")->clear();
        return $this->success($res);
    }

    /**
     * 获取会员等级信息
     *
     * @param array $condition
     * @param string $field
     */
    public function getMemberLevelInfo($condition = [], $field = '*')
    {

        $data = json_encode([ $condition, $field ]);
        $cache = Cache::get("member_level_getMemberLevelInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $info = model('member_level')->getInfo($condition, $field);
        Cache::tag("member_level")->set("member_level_getMemberLevelInfo_" . $data, $info);
        return $this->success($info);
    }

    /**
     * 获取会员等级列表
     *
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getMemberLevelList($condition = [], $field = '*', $order = 'sort asc, level_id asc', $limit = null)
    {

        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("member_level_getMemberLevelList_" . $data);

        if (!empty($cache)) {
            return $this->success($cache);
        }

        $list = model('member_level')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("member_level")->set("member_level_getMemberLevelList_" . $data, $list);

        return $this->success($list);
    }

    /**
     * 获取会员等级分页列表
     *
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getMemberLevelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'sort asc, level_id asc', $field = '*')
    {

        $data = json_encode([ $condition, $field, $order, $page, $page_size ]);
        $cache = Cache::get("member_level_getMemberLevelPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('member_level')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("member_level")->set("member_level_getMemberLevelPageList_" . $data, $list);
        return $this->success($list);
    }

    /**
     * 设置默认等级
     * @param unknown $level_id
     */
    public function setDefaultLevel($level_id)
    {
        model('member_level')->update([ 'is_default' => 1 ], [ [ 'level_id', '=', $level_id ] ]);
        model('member_level')->update([ 'is_default' => 0 ], [ [ 'level_id', '<>', $level_id ] ]);
        Cache::tag("member_level")->clear();
        return $this->success();
    }

    /**
     * 会员等级列表
     */
    public function levelList($data)
    {
        $condition = [];
        $condition[] = ['is_default', '=', 0];
        $field = 'level_id,level_name,remark,consume_discount,is_free_shipping,point_feedback';
        $order = 'level_id asc';
        $list = model('member_level')->getList($condition, $field, $order );
        foreach ($list as $key => $value) {
            //获取城市分站设置
            $condition = [];
            $condition[] = ['level_id', '=', $value['level_id']];
            $condition[] = ['website_id', '=', $data['website_id']];
            $city_member_level = model('city_member_level')->getInfo($condition);
            $consume_discount = 100;
            $point_feedback = 0;
            $price = 0;
            $fan_point = 0;
            if ($city_member_level) {
                $consume_discount = $city_member_level['consume_discount'];
                $point_feedback = $city_member_level['point_feedback'];
                $price = $city_member_level['price'];
                $fan_point = $city_member_level['fan_point'];
            }
            $list[$key]['consume_discount'] = $consume_discount;
            $list[$key]['point_feedback'] = $point_feedback;
            $list[$key]['price'] = $price;
            $list[$key]['fan_point'] = $fan_point;
        }
        return $this->success($list);
    }

    /**
     * 创建会员订单
     */
    public function ordercreate($data)
    {
        $member = model('member')->getInfo(['member_id'=>$data['member_id']]);
        if ($member['member_level'] >= $data['level_id']) {
            return $this->error('', '已开通该等级');
        }
        $member_level = model('member_level')->getInfo(['level_id'=>$data['level_id']]);
        if (!$member_level) {
            return $this->error('', '等级不存在');
        }
        //判断城市是否设置等级
        $condition = [];
        $condition[] = ['level_id', '=', $data['level_id']];
        $condition[] = ['website_id', '=', $data['website_id']];
        $city_member_level = model('city_member_level')->getInfo($condition);
        if (!$city_member_level) {
            return $this->error('', '该等级不可开通');
        }
        //判断价格
        if ($city_member_level['price'] <= 0) {
            return $this->error('', '该等级不可开通');
        }

        //创建或加入
        $pay = new Pay();
        $out_trade_no = $pay->createOutTradeNo($data['member_id']);
        $order_no = $this->createOrderNo(0, $data['member_id']);

        $order_data = [
            'level_id' => $data['level_id'],
            'level_name' => $member_level['level_name'],
            'order_no' => $order_no,
            'out_trade_no' => $out_trade_no,
            'website_id' => $data['website_id'],
            'price' => $city_member_level['price'],
            'fan_point' => $city_member_level['fan_point'],
            'status' => 1,
            'create_time' => time(),
            'member_id' => $data['member_id'],
            'member_img' => $member['headimg'],
            'nickname' => $member['nickname'],
        ];

        model("order")->startTrans();
        try {
            $order_id = model("member_level_order")->add($order_data);
            //生成整体支付单据
            $pay->addPay(0, $out_trade_no, "", "会员升级：" . $member_level['level_name'], "会员升级：" . $member_level['level_name'], $order_data['price'], '', 'MemberLevelOrderPayNotify', '');
            model("order")->commit();
            return $this->success($out_trade_no);
        } catch (\Exception $e) {
            model("order")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    public function createOrderNo($site_id, $member_id)
    {
        $order_no = createno('create_order_no','', $member_id);
        return $order_no;
    }
    
    /**
     * 支付回调
     */
    public function orderPay($data)
    {
        $order_info = model('member_level_order')->getInfo([ [ 'out_trade_no', '=', $data[ 'out_trade_no' ] ] ]);
        if ($order_info[ 'status' ] == 1) {
            model('order')->startTrans();
            try {
                //修改订单状态
                $order_data = [
                    'pay_time' => time(),
                    'status' => 2
                ];
                $res = model('member_level_order')->update($order_data, [ [ 'out_trade_no', '=', $data[ 'out_trade_no' ] ] ]);
                //升级会员
                $levelModel = model('member_level')->getInfo(['level_id'=>$order_info['level_id']]);
                if ($levelModel) {
                    model('member')->update(['member_level'=>$levelModel['level_id'],'member_level_name'=>$levelModel['level_name']], ['member_id'=>$order_info['member_id']]);
                }

                //修改支付状态
                $payModel = model('pay')->getInfo(['out_trade_no' => $data[ 'out_trade_no' ]]);
                model('pay')->update(['pay_type' => 'HUIFU', 'pay_status' => 2, 'pay_time' => time()], ['id' => $payModel['id']]);

                //开通送积分
                if ($order_info['fan_point'] > 0) {
                    $member_account = new MemberAccount();
                    $member_account->addMemberAccount2($order_info['member_id'], 'point', $order_info['fan_point'], 'member_level', $order_info['order_id'], "成为会员送积分".$order_info['fan_point'], '成为会员');
                }

                //如果升级合伙人 归属每个城市下城盟联创 下面成为员工
                if ($order_info['level_id'] == 4) {
                    //获取该城市下城盟联创
                    $shop = model('shop')->getInfo([
                        ['website_id', '=', $order_info['website_id']],
                        ['is_own', '=', 1]
                    ]);
                    if ($shop) {
                        //添加员工
                        $member = model('member')->getInfo(['member_id'=>$order_info['member_id']]);
                        $result = model('shop_employee')->getInfo([
                            ['shop_id', '=', $shop['site_id']],
                            ['mobile', '=', $member['mobile']],
                            ['is_deleted', '=', 0],
                        ]);
                        if (!$result) {
                            model('shop_employee')->add([
                                'shop_id' => $shop['site_id'],
                                'username' => $member['nickname'],
                                'mobile' => $member['mobile'],
                                'create_time' => time(),
                                'website_id' => $order_info['website_id'],
                                'headimg' => $member['headimg']
                            ]);
                        }

                    }
                }

                model('order')->commit();
                return $this->success($res);
            } catch (\Exception $e) {
                model('order')->rollback();
                return $this->error('', $e->getMessage());
            }
        } else {
            return $this->success(true);
        }
    }

}