<?php
/**

 */

namespace app\model\member;

use addon\sitemessage\model\MemberMessage;
use addon\sitemessage\model\Message as SiteMessage;
use app\model\BaseModel;
use Carbon\Carbon;
use think\facade\Db;

/**
 * 会员账户
 */
class MemberAccount extends BaseModel
{
    //账户类型
    private $account_type = [
        'balance' => '余额（不可提现）',
        'balance_money' => '余额（可提现）',
        'point' => '积分',
        'growth' => '成长值'
    ];

    //来源类型
    private $from_type = [];

    public function __construct()
    {
        $event_from_type = event('MemberAccountFromType', '');
        $from_type = [];
        foreach ($event_from_type as $info) {

            if (isset($info[ 'balance' ])) {
                $balance = array_keys($info[ 'balance' ]);
                $from_type[ 'balance' ][ $balance[ 0 ] ] = $info[ 'balance' ][ $balance[ 0 ] ];
            }

            if (isset($info[ 'point' ])) {
                $point = array_keys($info[ 'point' ]);
                $from_type[ 'point' ][ $point[ 0 ] ] = $info[ 'point' ][ $point[ 0 ] ];
            }

            if (isset($info[ 'growth' ])) {
                $growth = array_keys($info[ 'growth' ]);
                $from_type[ 'growth' ][ $growth[ 0 ] ] = $info[ 'growth' ][ $growth[ 0 ] ];
            }

            if (isset($info[ 'balance_money' ])) {
                $balance_money = array_keys($info[ 'balance_money' ]);
                $from_type[ 'balance_money' ][ $balance_money[ 0 ] ] = $info[ 'balance_money' ][ $balance_money[ 0 ] ];
            }
        }

        $from_type[ 'balance' ][ 'adjust' ] = [ 'type_name' => '调整', 'type_url' => '' ];
        $from_type[ 'balance_money' ][ 'adjust' ] = [ 'type_name' => '调整', 'type_url' => '' ];

        $from_type[ 'balance' ][ 'withdraw' ] = [ 'type_name' => '提现', 'type_url' => '' ];
        $from_type[ 'balance_money' ][ 'withdraw' ] = [ 'type_name' => '提现', 'type_url' => '' ];

        $from_type[ 'balance' ][ 'order' ] = [ 'type_name' => '消费', 'type_url' => '' ];
        $from_type[ 'balance_money' ][ 'order' ] = [ 'type_name' => '消费', 'type_url' => '' ];

        $from_type[ 'point' ][ 'adjust' ] = [ 'type_name' => '调整', 'type_url' => '' ];
        $from_type[ 'growth' ][ 'adjust' ] = [ 'type_name' => '调整', 'type_url' => '' ];

        $from_type[ 'balance' ][ 'upgrade' ] = [ 'type_name' => '升级', 'type_url' => '' ];
        $from_type[ 'balance_money' ][ 'upgrade' ] = [ 'type_name' => '升级', 'type_url' => '' ];

        $from_type[ 'point' ][ 'upgrade' ] = [ 'type_name' => '升级', 'type_url' => '' ];
        $from_type[ 'growth' ][ 'upgrade' ] = [ 'type_name' => '升级', 'type_url' => '' ];

        $from_type[ 'balance' ][ 'refund' ] = [ 'type_name' => '退还', 'type_url' => '' ];
        $from_type[ 'point' ][ 'refund' ] = [ 'type_name' => '退还', 'type_url' => '' ];

        $this->from_type = $from_type;
    }

    /**
     * 获取账户类型
     */
    public function getAccountType()
    {
        return $this->account_type;
    }

    /**
     * 获取来源类型
     */
    public function getFromType()
    {
        return $this->from_type;
    }

    /**
     * 添加会员账户数据
     * @param $member_id
     * @param $account_type
     * @param $account_data
     * @param $from_type
     * @param $relate_tag
     * @param $remark
     * @return array
     */
    public function addMemberAccount($member_id, $account_type, $account_data, $from_type, $relate_tag, $remark)
    {
        model('member_account')->startTrans();
        try {
            //账户检测
            $member_account = model('member')->getInfo([
                [ 'member_id', '=', $member_id ]
            ], $account_type . ', username, mobile, email');
            
            $account_new_data = (float) $member_account[ $account_type ] + (float) $account_data;
            if ((float) $account_new_data < 0) {
                model('member_account')->rollback();
                $msg = '';
                if ($account_type == 'balance') {
                    $msg = '账户余额不足';
                } elseif ($account_type = 'point') {
                    $msg = '账户积分不足';
                } elseif ($account_type = 'growth') {
                    $msg = '账户成长值不足';
                }
                return $this->error('', $msg);
            }

            //添加记录
            $type_info = $this->from_type[ $account_type ][ $from_type ] ?? '';
            $data = array (
                'member_id' => $member_id,
                'account_type' => $account_type,
                'account_data' => $account_data,
                'from_type' => $from_type,
                'type_name' => $type_info[ 'type_name' ] ?? '',
                'type_tag' => $relate_tag,
                'create_time' => time(),
                'username' => $member_account[ 'username' ] ?? '',
                'mobile' => $member_account[ 'mobile' ] ?? '',
                'email' => $member_account[ 'email' ] ?? '',
                'remark' => $remark
            );

            //调整积分逻辑
            //如果增加积分 判断积分到期时间
            $return_data = $this->setYuPoint($account_type,$account_data,$member_id);
            $data = array_merge($data, $return_data);

            $account_id = model('member_account')->add($data);
            //账户更新
            model('member')->update([
                $account_type => $account_new_data
            ], [
                'member_id' => $member_id
            ]);
//            $MemberMessageModel = new MemberMessage();
            event("AddMemberAccount", $data);
            model('member_account')->commit();
            return $this->success([ 'member_id' => $member_id, $account_type => sprintf("%.2f", $account_new_data) ]);
        } catch (\Exception $e) {
            model('member_account')->rollback();
            return $this->error('', $e->getMessage().$e->getFile().$e->getLine());
        }
    }

    //处理积分扣除
    public function setYuPoint($account_type,$account_data,$member_id)
    {
        $data = [];
        if ($account_type == 'point') {
            if ($account_data > 0) { //增加积分
                $reward_config = model('reward_config')->getInfo(['id'=>1]);
                $point_year = $reward_config['point_year'] ?? 1;
                if ($point_year <= 0) $point_year = 1;
                $point_end_time = strtotime('+'. $point_year.' year 23:59:59');
                $data['point_end_time'] = $point_end_time;
                $data['point_yu'] = $account_data;
            } else { //扣除积分
                //获取未到期的剩余积分大于等于扣除积分的数据
                $yu_list = Db::name('member_account')
                    ->where('member_id', $member_id)
                    ->where('account_type', 'point')
                    ->where('point_yu', '>', 0)
                    ->where('point_expire', 0)
                    ->order('create_time asc')
                    ->select();
                //扣除积分
                $totalPoints = 0;
                $accountsToUpdate = [];
                foreach ($yu_list as $key => $value) {
                    $totalPoints += $value['point_yu'];
                    $accountsToUpdate[] = $value['id'];  // 记录需要更新的ID

                    if ($totalPoints >= abs($account_data)) { //判断扣除积分
                        break;
                    }
                }

                if (!empty($accountsToUpdate)) {
                    $remainingPoints = abs($account_data);  // 要扣除的积分
                    // 遍历符合条件的记录，逐一扣除积分
                    foreach ($accountsToUpdate as $accountId) {
                        // 获取当前记录的积分
                        $account = Db::name('member_account')->find($accountId);

                        // 判断是否可以扣除积分
                        if ($account['point_yu'] >= $remainingPoints) {
                            // 扣除积分并更新
                            Db::name('member_account')
                                ->where('id', $accountId)
                                ->update(['point_yu' => $account['point_yu'] - $remainingPoints]);
                            break;
                        } else {
                            // 扣除该条记录的所有积分
                            $remainingPoints -= $account['point_yu'];
                            Db::name('member_account')
                                ->where('id', $accountId)
                                ->update(['point_yu' => 0]);
                        }
                    }
                }

            }
        }
        return $data;
    }


    public function addMemberAccount2($member_id, $account_type, $account_data, $from_type, $relate_tag, $remark, $type_name = '',$point_type='', $member_type = '')
    {
        model('member_account')->startTrans();
        try {
            //账户检测
            $member_account = model('member')->getInfo([
                [ 'member_id', '=', $member_id ]
            ], $account_type . ', username, mobile, email');

            $account_new_data = (float) $member_account[ $account_type ] + (float) $account_data;
            if ((float) $account_new_data < 0) {
                model('member_account')->rollback();
                $msg = '';
                if ($account_type == 'balance') {
                    $msg = '账户余额不足';
                } elseif ($account_type = 'point') {
                    $msg = '账户积分不足';
                } elseif ($account_type = 'growth') {
                    $msg = '账户成长值不足';
                }
                return $this->error('', $msg);
            }

            //添加记录
            $type_info = $this->from_type[ $account_type ][ $from_type ] ?? '';
            $data = array (
                'member_id' => $member_id,
                'account_type' => $account_type,
                'account_data' => $account_data,
                'from_type' => $from_type,
                'type_name' => $type_info[ 'type_name' ] ?? $type_name,
                'type_tag' => $relate_tag,
                'create_time' => time(),
                'username' => $member_account[ 'username' ] ?? '',
                'mobile' => $member_account[ 'mobile' ] ?? '',
                'email' => $member_account[ 'email' ] ?? '',
                'remark' => $remark,
                'point_type' => $point_type,
                'member_type' => $member_type
            );

            //调整积分逻辑
            //如果增加积分 判断积分到期时间
            $return_data = $this->setYuPoint($account_type,$account_data,$member_id);
            $data = array_merge($data, $return_data);

            $account_id = model('member_account')->add($data);
            //账户更新
            model('member')->update([
                $account_type => $account_new_data
            ], [
                'member_id' => $member_id
            ]);
            model('member_account')->commit();
            return $this->success([ 'member_id' => $member_id, $account_type => sprintf("%.2f", $account_new_data) ]);
        } catch (\Exception $e) {
            model('member_account')->rollback();
            return $this->error('', $e->getMessage().$e->getFile().$e->getLine());
        }
    }

    /**
     * 获取账户分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array|\multitype
     */
    public function getMemberAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*')
    {
        $list = model('member_account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取账户列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array|\multitype
     */
    public function getMemberAccountList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('member_account')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取账户总额
     * @param array $where
     * @param string $field
     * @param string $alias
     * @param null $join
     * @return array
     */
    public function getMemberAccountSum($where = [], $field = '', $alias = 'a', $join = null)
    {
        $sum = model('member_account')->getSum($where, $field, $alias, $join);
        return $this->success($sum);
    }

    /**
     * 用户积分排行榜
     */
    public function pointRanking($data)
    {
        $page = $data['page'];
        $page_size = $data['page_size'];
        $type = $data['type'];
        if ($type == '1') { //年度
            $start_time = Carbon::now()->startOfYear()->timestamp;
            $end_time = Carbon::now()->endOfYear()->timestamp;
        } else { //月度
            $start_time = Carbon::now()->startOfMonth()->timestamp;
            $end_time = Carbon::now()->endOfMonth()->timestamp;
        }

        $condition = [];
        $condition[] = ['ma.account_type', '=', 'point'];
        $condition[] = ['ma.create_time', '>=', $start_time];
        $condition[] = ['ma.create_time', '<=', $end_time];
        $condition[] = ['ma.point_expire', '=', 0];
        $condition[] = ['ma.point_yu', '>', 0];
//        $condition[] = ['ma.member_id', '=', 2];

        $order = 'real_point desc';

        //新逻辑
        $list = Db::name('member_account')
            ->where($condition)
            ->alias('ma')
            ->field('m.member_id,m.nickname,m.headimg,m.member_level,m.member_level_name,SUM(ma.point_yu) AS real_point')
            ->join('member m','ma.member_id=m.member_id','INNER')
            ->group('ma.member_id')
            ->order($order)
            ->limit($page_size)
            ->page($page)
            ->select()
            ->toArray();

        //返回的数据
        $count = Db::name('member_account')
            ->where($condition)
            ->alias('ma')
            ->field('m.member_id,m.nickname,m.headimg,m.member_level,m.member_level_name,SUM(ma.point_yu) AS real_point')
            ->join('member m','ma.member_id=m.member_id','INNER')
            ->group('ma.member_id')
            ->count();

        $page_count = ceil($count / $page_size);

        $list = [
            'count' => $count,
            'page_count' => $page_count,
            'list' => $list,
        ];

        //我的排名
        $ranking = 0;
        $member_data = null;

        if ($data['member_id']) {
            //用户排行
            $all_list = Db::name('member_account')
                ->where($condition)
                ->alias('ma')
                ->field('m.member_id,m.nickname,m.headimg,m.member_level,m.member_level_name,SUM(ma.point_yu) AS real_point')
                ->join('member m','ma.member_id=m.member_id','INNER')
                ->group('ma.member_id')
                ->order($order)
                ->select()
                ->toArray();
            foreach ($all_list as $key => $value) {
                if ($value['member_id'] == $data['member_id']) {
                    $ranking = $key+1;
                    $member_data = $value;
                    break;
                }
            }
        }

        $list['ranking'] = $ranking;
        $list['member_data'] = $member_data;

        return $this->success($list);

    }
    
    //品牌热度排行
    public function shopRanking($data)
    {
        $page = $data['page'];
        $page_size = $data['page_size'];
        $type = $data['type'];
        if ($type == '1') { //年度
            $start_time = Carbon::now()->startOfYear()->timestamp;
            $end_time = Carbon::now()->endOfYear()->timestamp;
        } else { //月度
            $start_time = Carbon::now()->startOfMonth()->timestamp;
            $end_time = Carbon::now()->endOfMonth()->timestamp;
        }

        $condition = [];
        $condition[] = ['sh.create_time', '>=', $start_time];
        $condition[] = ['sh.create_time', '<=', $end_time];
        $condition[] = ['shop.website_id', '=', $data['website_id']];

        $order = 'real_heat desc';

        //新逻辑
        $list = Db::name('shop_heat')
            ->where($condition)
            ->alias('sh')
            ->field('ss.store_id,ss.store_name,ss.store_image,ss.site_id,SUM(sh.heat) AS real_heat')
            ->join('store ss','sh.store_id=ss.store_id','INNER')
            ->join('shop','ss.site_id=shop.site_id','INNER')
            ->group('sh.store_id')
            ->order($order)
            ->limit($page_size)
            ->page($page)
            ->select()
            ->toArray();

        //返回的数据
        $count = Db::name('shop_heat')
            ->where($condition)
            ->alias('sh')
            ->field('ss.store_id,ss.store_name,ss.store_image,ss.site_id,SUM(sh.heat) AS real_heat')
            ->join('store ss','sh.store_id=ss.store_id','INNER')
            ->join('shop','ss.site_id=shop.site_id','INNER')
            ->group('sh.store_id')
            ->count();

        $page_count = ceil($count / $page_size);

        $list = [
            'count' => $count,
            'page_count' => $page_count,
            'list' => $list,
        ];

        return $this->success($list);
    }
    
    //我的积分
    public function pointAccount($data)
    {
        $condition = [];
        $type = $data['point_type'];
        $condition[] = ['member_id', '=', $data['member_id']];
        $condition[] = ['account_type', '=', 'point'];
        if ($type) {
            $condition[] = ['point_type', '=', $type];
        }
        $field = 'id,member_id,account_type,account_data,from_type,type_name,remark,create_time,point_type,type_tag';
        $order = 'id desc';
        $list = model('member_account')->pageList($condition,$field,$order,$data['page'],$data['page_size']);
        foreach ($list['list'] as $key => $value) {
            $nickname = $headimg = $member_level = $member_level_name = '';
            $sku_name = $sku_image = '';
            $goods_money = $num = '';
            if ($value['point_type'] == 1) { //邀约奖励
                $member = model('member')->getInfo(['member_id'=>$value['type_tag']]);
                $nickname = $member['nickname'] ?? '';
                $headimg = $member['headimg'] ?? '';
                $member_level = $member['member_level'] ?? '';
                $member_level_name = $member['member_level_name'] ?? '';
            } elseif ($value['point_type'] == 2) { //下单奖励
                $order_goods = null;
                if (!empty($value['type_tag'])) {
                    $order_goods = model('order_goods')->getInfo(['order_goods_id' => $value['type_tag']]);
                    if (empty($order_goods)) {
                        $order_goods = model('order_goods')->getInfo(['order_id' => $value['type_tag']]);
                    }
                }
                $sku_name = $order_goods['sku_name'] ?? '';
                $sku_image = $order_goods['sku_image'] ?? '';
                $goods_money = $order_goods['goods_money'] ?? 0;
                $num = $order_goods['num'] ?? 0;
            } elseif ($value['point_type'] == 3) { //积分消耗
                $order_goods = null;
                if (!empty($value['type_tag'])) {
                    $order_goods = model('order_goods')->getInfo(['order_goods_id' => $value['type_tag']]);
                    if (empty($order_goods)) {
                        $order_goods = model('order_goods')->getInfo(['order_id' => $value['type_tag']]);
                    }
                }
                $sku_name = $order_goods['sku_name'] ?? '';
                $sku_image = $order_goods['sku_image'] ?? '';
                $goods_money = $order_goods['goods_money'] ?? 0;
                $num = $order_goods['num'] ?? 0;
            } elseif ($value['point_type'] == '') {
                $member = model('member')->getInfo(['member_id'=>$value['member_id']]);
                $nickname = $member['nickname'] ?? '';
                $headimg = $member['headimg'] ?? '';
                $member_level = $member['member_level'] ?? '';
                $member_level_name = $member['member_level_name'] ?? '';
            }
            $list['list'][$key]['nickname'] = $nickname;
            $list['list'][$key]['headimg'] = $headimg;
            $list['list'][$key]['create_time'] = date('Y/m/d H:i:s', $value['create_time']);
            $list['list'][$key]['sku_name'] = $sku_name;
            $list['list'][$key]['sku_image'] = $sku_image;
            $list['list'][$key]['goods_money'] = $goods_money;
            $list['list'][$key]['num'] = $num;
            $list['list'][$key]['member_level'] = $member_level;
            $list['list'][$key]['member_level_name'] = $member_level_name;
        }
        return $this->success($list);
    }

    //我的钱包
    public function moneyAccount($data)
    {
        $condition = [];
        $type = $data['type'];
        $start_time = Carbon::now()->startOfDay()->timestamp;
        $end_time = Carbon::now()->endOfDay()->timestamp;
        if ($type == 2) { //昨日
            $start_time = Carbon::yesterday()->startOfDay()->timestamp;
            $end_time = Carbon::yesterday()->endOfDay()->timestamp;
        } elseif ($type == 3) { //本月
            $start_time = Carbon::now()->startOfMonth()->timestamp;
            $end_time = Carbon::now()->endOfMonth()->timestamp;
        } elseif ($type == 4) { //上月
            $start_time = (new Carbon('first day of last month'))->startOfMonth()->timestamp;
            $end_time = (new Carbon('last day of last month'))->endOfMonth()->timestamp;
        }
        $condition[] = ['create_time', '>=', $start_time];
        $condition[] = ['create_time', '<=', $end_time];
        $condition[] = ['member_id', '=', $data['member_id']];
        $condition[] = ['account_type', '=', 'balance_money'];
        $field = 'id,member_id,account_type,account_data,from_type,type_name,remark,create_time,member_type';
        $order = 'id desc';
        $list = model('member_account')->pageList($condition,$field,$order,$data['page'],$data['page_size']);
        foreach ($list['list'] as $key => $value) {
            if ($value['member_type']) {
                $member_id = $value['member_type'];
            } else {
                $member_id = $value['member_id'];
            }
            $member = model('member')->getInfo(['member_id'=>$member_id]);
            $nickname = $member['nickname'] ?? '';
            $headimg = $member['headimg'] ?? '';
            $list['list'][$key]['nickname'] = $nickname;
            $list['list'][$key]['headimg'] = $headimg;
            $list['list'][$key]['create_time'] = date('Y/m/d H:i:s', $value['create_time']);
        }
        //今日现金奖励
        $start_time = Carbon::now()->startOfDay()->timestamp;
        $end_time = Carbon::now()->endOfDay()->timestamp;
        $condition = [];
        $condition[] = ['create_time', '>=', $start_time];
        $condition[] = ['create_time', '<=', $end_time];
        $condition[] = ['member_id', '=', $data['member_id']];
        $condition[] = ['account_type', '=', 'balance_money'];
        $condition[] = ['account_data', '>', 0];
        $list['today_money'] = model('member_account')->getSum($condition, 'account_data');
        return $this->success($list);
    }

}