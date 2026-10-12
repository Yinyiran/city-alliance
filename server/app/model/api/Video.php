<?php


namespace app\model\api;


use app\model\BaseModel;
use app\model\member\MemberAccount;
use Carbon\Carbon;
use think\facade\Db;

class Video extends BaseModel
{
    //视频列表
    public function lists($data)
    {
        //过期的视频取消置顶
        $this->setNew();

        $condition = [];

        //显示当前城市和总平台视频
        $condition[] = ['v.website_id', 'in', [$data['website_id'], 0, -1]];

        $field = 'v.id,v.name,v.video_url,v.site_id,v.goods_id,v.point,v.point_num,v.create_time,v.second,
            g.goods_name,g.goods_image,g.price,g.market_price,g.sku_id,g.goods_type,g.is_admin,
            s.logo,s.site_name,
            gs.sku_image
        ';
        $order = 'v.is_new desc,v.weight desc,v.create_time desc';
        $alias = 'v';
        $join = [
            [
                'goods g',
                'g.goods_id = v.goods_id',
                'inner'
            ],
            [
                'shop s',
                's.site_id = v.site_id',
                'inner'
            ],
            [
                'goods_sku gs',
                'g.sku_id = gs.sku_id',
                'inner'
            ]
        ];
        $list = model('video')->pageList($condition, $field, $order, $data['page'], $data['page_size'],$alias, $join);
        //是否还可领取积分
        foreach ($list['list'] as $key => $value) {
            $status_info = $this->checkPointStatus($value, $data['member_id'] ?? 0);
            $list['list'][$key]['point_status'] = $status_info['point_status'];
            $list['list'][$key]['point_status_code'] = $status_info['point_status_code'];
            $list['list'][$key]['point_status_msg'] = $status_info['point_status_msg'];
            $list['list'][$key]['today_point_count'] = $status_info['today_point_count'];
            $list['list'][$key]['point_limit_num'] = $status_info['point_limit_num'];
            //视频评论数
            $list['list'][$key]['comment_num'] = model('video_comment')->getCount(['category_id'=>$value['id'],'type'=>'video']);
            //订单数量
            $list['list'][$key]['order_num'] = $this->orderNum($value['goods_id']);
        }
        return $this->success($list);
    }

    //过期的视频取消置顶
    public function setNew()
    {
        $ids = model('video')->getColumn([
            ['is_new', '=', 1],
            ['end_time', '<', time()],
        ], 'id');
        model('video')->update(['is_new' => 0], [[ 'id', 'in', $ids]]);
    }

    //短视频增加积分
    public function pointAdd($data)
    {
        $video = model('video')->getInfo(['id'=>$data['id']]);
        if (!$video) {
            return $this->error('', '视频不存在');
        }
        //判断用户是否可领取
        $status_info = $this->checkPointStatus($video, $data['member_id'] ?? 0);
        if ($status_info['point_status'] != 1) {
            return $this->error('', $status_info['point_status_msg']);
        }
        //领取积分
        model('member')->startTrans();
        try {
            //增加积分
            $point = $video['point'];
            if ($point > 0) {
                model('video_log')->add([
                    'video_id' => $data['id'],
                    'member_id' => $data['member_id'],
                    'point' => $video['point'],
                    'create_time' => time(),
                ]);
                $member_account = new MemberAccount();
                $member_account->addMemberAccount2($data['member_id'], 'point', $point, 'videoadd', 0, "观看视频获得积分".$point, '观看视频');
            }
            model('member')->commit();
            return $this->success('');
        } catch (\Exception $e) {
            model('member')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    
    //评论
    public function commentCreate($data)
    {
        $result = Db::name('video')->where('id', $data['category_id'])->find();
        if (!$result) return $this->error('', '视频不存在');
        $content = $data['content'];
        if (empty($content))  return $this->error('', '请输入内容');
        model('video_comment')->add([
            'member_id' => $data['member_id'],
            'content' => $content,
            'category_id' => $data['category_id'],
            'pid' => $pid ?? 0,
            'create_time' => time(),
            'type' => 'video',
            'again_is_audit' => 1,
        ]);
        return $this->success();
    }
    
    //评论列表
    public function commentLists($data)
    {
        $page = $data['page'];
        $page_size = $data['page_size'];
        $query_obj = VideoComment::where('category_id', $data['id'])
            ->where('type', 'video')
            ->where('again_is_audit', 1)
            ->with(['replies','member'=>function($query) {
                $query->field('member_id,nickname,headimg,member_level,member_level_name');
            }])
            ->where('pid', 0);
        $count_obj = VideoComment::where('category_id',  $data['id'])
            ->where('type', 'video')
            ->where('again_is_audit', 1)
            ->with(['replies'])
            ->where('pid', 0);
        $count = $count_obj->count();
        $field = '*';
        if ($page_size == 0) {
            //查询全部
            $result_data = $query_obj->field($field)->limit($count)->page($page)->select()->toArray();
            $result[ 'page_count' ] = 1;
        } else {
            $result_data = $query_obj->field($field)->limit($page_size)->page($page)->select()->toArray();
            $result[ 'page_count' ] = ceil($count / $page_size);
        }

        //用户是否点赞
        $member_id = $data['member_id'];
        foreach ($result_data as $key => $value) {
            $collection_status = 0;
            if ($member_id) {
                if (Db::name('member_collection')->where('member_id', $member_id)->where('category_id', $value['id'])->where('type', 'video_comment')->find()) {
                    $collection_status = 1;
                }
            }
            $result_data[$key]['collection_status'] = $collection_status;
        }

        $result[ 'count' ] = $count;
        $result[ 'list' ] = $result_data;
        //总评论数
        $result['total_count'] = VideoComment::where('category_id', $data['id'])->where('type', 'video')->count();
        return $this->success($result);
    }

    //点赞/取消
    public function collect($data)
    {
        $type = 'video_comment';
        $result = model('video_comment')->getInfo(['id' => $data['category_id']]);
        if (!$result) return $this->error('', '评论不存在');
        $collection = model('member_collection')->getInfo(['member_id' => $data['member_id'], 'category_id' => $data['category_id'], 'type' => $type]);
        if ($collection) {
            model('member_collection')->delete(['id' => $collection['id']]);
        } else {
            model('member_collection')->add([
                'member_id' => $data['member_id'],
                'category_id' => $data['category_id'],
                'type' => $type,
                'create_time' => time(),
            ]);
        }
        //更新关注量
        $collect_count = model('member_collection')->getCount(['category_id' => $data['category_id'], 'type' => $type]);
        model('video_comment')->update(['collect_count' => $collect_count], ['id' => $data['category_id']]);
        return $this->success('');
    }
    
    //短视频订单
    public function orderList($data)
    {
        $result = Db::name('video')->where('id', $data['id'])->find();
        if (!$result) return $this->error('', '视频不存在');
        $goods_id = $result['goods_id'];
        $condition = [];
        $field = 'og.sku_name,og.sku_image,og.goods_id,og.sku_id,og.goods_money,o.create_time,
            m.member_id,m.nickname,m.headimg,m.member_level,m.member_level_name
        ';
        $order = 'o.create_time desc';
        $alias = 'og';
        $join = [
            [
                'order o',
                'o.order_id = og.order_id',
                'inner'
            ],
            [
                'member m',
                'm.member_id = o.member_id',
                'inner'
            ],
        ];
        $condition[] = ['o.order_status', 'in', [1,2,3,4,10]];
        $condition[] = ['og.goods_id', '=', $goods_id];
        $list = model('order_goods')->pageList($condition,$field,$order,$data['page'],$data['page_size'],$alias,$join);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['create_time'] = date('Y/m/d', $value['create_time']);
        }
        return $this->success($list);
    }
    
    //获取视频订单数量
    public function orderNum($goods_id)
    {
        $condition = [];
        $field = 'og.sku_name,og.sku_image,og.goods_id,og.sku_id,og.goods_money,o.create_time,
            m.member_id,m.nickname,m.headimg,m.member_level,m.member_level_name
        ';
        $order = 'o.create_time desc';
        $alias = 'og';
        $condition[] = ['o.order_status', 'in', [1,2,3,4,10]];
        $condition[] = ['og.goods_id', '=', $goods_id];
        return Db::name('order_goods')->where($condition)
            ->alias($alias)
            ->join('order o', 'o.order_id = og.order_id', 'inner')
            ->join('member m', 'm.member_id = o.member_id', 'inner')
            ->field($field)
            ->count();
    }

    //视频详情
    public function detail($data)
    {
        $condition = [];
        $field = 'v.id,v.name,v.video_url,v.site_id,v.goods_id,v.point,v.point_num,v.create_time,v.second,
            g.goods_name,g.goods_image,g.price,g.market_price,g.sku_id,
            s.logo,s.site_name
        ';
        $alias = 'v';
        $join = [
            [
                'goods g',
                'g.goods_id = v.goods_id',
                'inner'
            ],
            [
                'shop s',
                's.site_id = v.site_id',
                'inner'
            ]
        ];
        $condition[] = ['v.id', '=', $data['id']];
        $result = model('video')->getInfo(['id'=>$data['id']],$field,$alias,$join);

        $status_info = $this->checkPointStatus($result, $data['member_id'] ?? 0);
        $result['point_status'] = $status_info['point_status'];
        $result['point_status_code'] = $status_info['point_status_code'];
        $result['point_status_msg'] = $status_info['point_status_msg'];
        $result['today_point_count'] = $status_info['today_point_count'];
        $result['point_limit_num'] = $status_info['point_limit_num'];
        //视频评论数
        $result['comment_num'] = model('video_comment')->getCount(['category_id'=>$result['id'],'type'=>'video']);
        //订单数量
        $result['order_num'] = $this->orderNum($result['goods_id']);
        return $this->success($result);
    }

    //增加转发数
    public function shareAdd($data)
    {
        model('video')->setInc([['id', '=', $data['id']]], 'share_count');
        $this->weight($data['id']);
        return $this->success('');
    }

    //增加完播数
    public function endAdd($data)
    {
        model('video')->setInc([['id', '=', $data['id']]], 'end_count');
        $this->weight($data['id']);
        return $this->success('');
    }

    //增加订单数
    public function orderAdd($data)
    {
        model('video')->setInc([['id', '=', $data['id']]], 'order_num');
        $this->weight($data['id']);
    }

    //计算权重
    public function weight($video_id)
    {
        $config = model('reward_config')->getInfo(['id' => 1]);
        $video_order_rate = $config['video_order_rate']; //下单次数权重
        $video_share_rate = $config['video_share_rate']; //转发数权重
        $video_end_rate = $config['video_end_rate']; //完播率权重
        $video = model('video')->getInfo(['id' => $video_id]);
        if ($video) {
            $weight = $video['order_num']*$video_order_rate+$video['share_count']*$video_share_rate+$video['end_count']*$video_end_rate;
            $weight = number_format($weight, 2, '.', '');
            model('video')->update(['weight'=>$weight], ['id' => $video_id]);
        }
    }

    /**
     * 统一校验当前用户是否可领取该视频积分，并返回状态码与说明
     */
    private function checkPointStatus($videoInfo, $memberId)
    {
        $point = floatval($videoInfo['point'] ?? 0);
        $point_num = intval($videoInfo['point_num'] ?? 0);
        $second = intval($videoInfo['second'] ?? 0);

        // 1. 未登录
        if (!$memberId) {
            return [
                'point_status' => 0,
                'point_status_code' => 'not_login',
                'point_status_msg' => '请先登录后再观看领取积分',
                'today_point_count' => 0,
                'point_limit_num' => $point_num
            ];
        }

        // 2. 视频未配置奖励（积分<=0 或 次数<=0 或 转动秒数<=0）
        if ($point <= 0 || $point_num <= 0 || $second <= 0) {
            return [
                'point_status' => 0,
                'point_status_code' => 'no_reward',
                'point_status_msg' => '该视频暂未开放积分奖励',
                'today_point_count' => 0,
                'point_limit_num' => $point_num
            ];
        }

        // 3. 统计今日已领次数（当天 00:00:00 ~ 23:59:59）
        $start_time = Carbon::now()->startOfDay()->timestamp;
        $end_time = Carbon::now()->endOfDay()->timestamp;
        $condition = [
            ['video_id', '=', $videoInfo['id']],
            ['member_id', '=', $memberId],
            ['create_time', '>=', $start_time],
            ['create_time', '<=', $end_time]
        ];
        $today_count = model('video_log')->getCount($condition);

        if ($today_count >= $point_num) {
            return [
                'point_status' => 0,
                'point_status_code' => 'limit_reached',
                'point_status_msg' => "今日该视频积分已领完（每日上限{$point_num}次，已领{$today_count}次）",
                'today_point_count' => $today_count,
                'point_limit_num' => $point_num
            ];
        }

        return [
            'point_status' => 1,
            'point_status_code' => 'ok',
            'point_status_msg' => '观看视频可获得积分',
            'today_point_count' => $today_count,
            'point_limit_num' => $point_num
        ];
    }
}