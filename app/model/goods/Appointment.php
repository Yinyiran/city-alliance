<?php
/**
 * 预约到店服务
 */

namespace app\model\goods;

use app\model\BaseModel;

/**
 * Appointment
 * 预约时段规则维护 / 时段余量计算 / 下单占用 / 超时释放
 */
class Appointment extends BaseModel
{
    const STATUS_WAIT_PAY = 0; //待付款（占用中）
    const STATUS_BOOKED = 1;   //已预约（已付款）
    const STATUS_CANCEL = 2;   //已取消
    const STATUS_EXPIRE = 3;   //已过期（超时未付）
    const STATUS_FINISH = 4;   //已完成（已核销）

    const DEFAULT_EXPIRE_MINUTES = 30; //下单后名额保留时长

    /**
     * 保存商品预约时段规则（先清空后写入）
     * @param int   $goods_id
     * @param array $rules
     * @param array $base  ['site_id','site_name','website_id']
     */
    public function saveRules($goods_id, $rules, $base = [])
    {
        if (empty($goods_id)) return $this->error([], '商品ID不能为空');

        model('goods_appointment_rule')->delete([ [ 'goods_id', '=', $goods_id ] ]);

        if (empty($rules) || !is_array($rules)) return $this->success();

        $time = time();
        $data = [];
        foreach ($rules as $rule) {
            if (empty($rule[ 'store_id' ])) continue;
            $start_time = $this->toTimeStr($rule[ 'start_time' ] ?? '09:00');
            $end_time = $this->toTimeStr($rule[ 'end_time' ] ?? '18:00');
            if ($start_time >= $end_time) return $this->error([], '预约时段的开始时间必须小于结束时间');

            $interval = intval($rule[ 'interval_minutes' ] ?? 30);
            if ($interval < 5) $interval = 30;
            $max_num = intval($rule[ 'max_num' ] ?? 1);
            if ($max_num < 1) $max_num = 1;
            $duration = intval($rule[ 'service_duration' ] ?? 0);
            if ($duration <= 0) $duration = $interval;

            $data[] = [
                'site_id' => intval($base[ 'site_id' ] ?? 0),
                'site_name' => $base[ 'site_name' ] ?? '',
                'website_id' => intval($base[ 'website_id' ] ?? 0),
                'store_id' => intval($rule[ 'store_id' ]),
                'store_name' => $rule[ 'store_name' ] ?? '',
                'goods_id' => $goods_id,
                'sku_id' => intval($rule[ 'sku_id' ] ?? 0),
                'week_days' => $rule[ 'week_days' ] ?? '1,2,3,4,5,6,7',
                'start_time' => $start_time,
                'end_time' => $end_time,
                'interval_minutes' => $interval,
                'max_num' => $max_num,
                'stop_before_minutes' => intval($rule[ 'stop_before_minutes' ] ?? 60),
                'service_duration' => $duration,
                'sort' => intval($rule[ 'sort' ] ?? 0),
                'status' => intval($rule[ 'status' ] ?? 1),
                'create_time' => $time,
                'update_time' => $time,
            ];
        }
        if (!empty($data)) model('goods_appointment_rule')->addList($data);

        return $this->success();
    }

    /**
     * 获取商品预约规则
     */
    public function getRuleList($condition = [], $field = '*', $order = 'sort asc,id asc')
    {
        return $this->success(model('goods_appointment_rule')->getList($condition, $field, $order));
    }

    /**
     * 可预约日期列表
     */
    public function getDateList($goods_id, $store_id = 0, $days = 7)
    {
        $condition = [ [ 'goods_id', '=', $goods_id ], [ 'status', '=', 1 ] ];
        if (!empty($store_id)) $condition[] = [ 'store_id', '=', $store_id ];

        $rules = model('goods_appointment_rule')->getList($condition, '*', 'id asc');
        if (empty($rules)) return $this->success([]);

        $store_ids = array_unique(array_column($rules, 'store_id'));
        $date_list = [];
        $today = strtotime(date('Y-m-d'));
        for ($i = 0; $i < $days; $i++) {
            $timestamp = $today + $i * 86400;
            $date = date('Y-m-d', $timestamp);
            $week = date('N', $timestamp);
            $item = [ 'date' => $date, 'week' => $week, 'week_name' => $this->weekName($week), 'store_list' => [] ];

            foreach ($store_ids as $sid) {
                foreach ($rules as $rule) {
                    if ($rule[ 'store_id' ] != $sid) continue;
                    if (!in_array($week, explode(',', $rule[ 'week_days' ]))) continue;
                    $item[ 'store_list' ][] = [
                        'store_id' => $sid,
                        'store_name' => $rule[ 'store_name' ],
                        'rule_id' => $rule[ 'id' ],
                    ];
                    break;
                }
            }
            if (empty($item[ 'store_list' ])) continue;//当天全部门店不营业
            $date_list[] = $item;
        }
        return $this->success($date_list);
    }

    /**
     * 某天某门店时段余量
     * @param int    $goods_id
     * @param int    $store_id
     * @param string $date Y-m-d
     * @param int    $num  需要名额数
     */
    public function getSlotList($goods_id, $store_id, $date, $num = 1)
    {
        $date = $date ?: date('Y-m-d');
        $timestamp = strtotime($date);
        if (!$timestamp) return $this->error([], '日期格式不正确');
        $week = date('N', $timestamp);
        $num = intval($num) < 1 ? 1 : intval($num);

        $rules = model('goods_appointment_rule')->getList([
            [ 'goods_id', '=', $goods_id ],
            [ 'store_id', '=', $store_id ],
            [ 'status', '=', 1 ],
        ], '*', 'sort asc,id asc');
        if (empty($rules)) return $this->error([], '该商品未配置预约时段');

        $used = $this->slotUsedMap($goods_id, $store_id, $date);
        $goods_info = model('goods')->getInfo([ [ 'goods_id', '=', $goods_id ] ], 'service_duration')[ 'data' ] ?? [];
        $default_duration = intval($goods_info[ 'service_duration' ] ?? 0);

        $now = time();
        $is_today = ($date == date('Y-m-d'));
        $slot_list = [];
        $seen = [];
        foreach ($rules as $rule) {
            if (!in_array($week, explode(',', $rule[ 'week_days' ]))) continue;
            $interval = intval($rule[ 'interval_minutes' ]) ?: 30;
            $duration = intval($rule[ 'service_duration' ]) ?: ($default_duration ?: $interval);
            $max_num = intval($rule[ 'max_num' ]) ?: 1;
            $stop_before = intval($rule[ 'stop_before_minutes' ]);

            $start_ts = strtotime($date . ' ' . $rule[ 'start_time' ]);
            $end_ts = strtotime($date . ' ' . $rule[ 'end_time' ]);
            for ($ts = $start_ts; $ts + $duration * 60 <= $end_ts; $ts += $interval * 60) {
                $slot_key = date('H:i', $ts);
                if (isset($seen[ $slot_key ])) continue;//重叠时段去重
                $seen[ $slot_key ] = 1;

                $used_num = $used[ $slot_key ] ?? 0;
                $remain = $max_num - $used_num;
                $deadline = $ts - $stop_before * 60;
                //超过截止时间或名额不足不可选；迟到仍可履约，故不限制早到/晚到
                $unavailable = ($is_today && $now > $deadline) || $remain < $num;

                $slot_list[] = [
                    'start_time' => $slot_key,
                    'end_time' => date('H:i', $ts + $duration * 60),
                    'slot_text' => $slot_key . '-' . date('H:i', $ts + $duration * 60),
                    'max_num' => $max_num,
                    'used_num' => $used_num,
                    'remain_num' => $remain > 0 ? $remain : 0,
                    'can_select' => !$unavailable,
                    'expire_time' => $deadline,
                ];
            }
        }
        return $this->success($slot_list);
    }

    /**
     * 占用预约名额（下单先占位，支付成功转正式）
     */
    public function occupy($data)
    {
        $goods_id = intval($data[ 'goods_id' ] ?? 0);
        $store_id = intval($data[ 'store_id' ] ?? 0);
        $date = $data[ 'appointment_date' ] ?? '';
        $start_time = $data[ 'start_time' ] ?? '';
        $num = intval($data[ 'num' ] ?? 1);
        if (!$goods_id || !$store_id || !$date || !$start_time) return $this->error([], '预约信息不完整');
        if ($num < 1) $num = 1;

        $this->expire();//先释放历史超时占用

        model('goods_appointment_log')->startTrans();
        try {
            $rules = model('goods_appointment_rule')->getList([
                [ 'goods_id', '=', $goods_id ],
                [ 'store_id', '=', $store_id ],
                [ 'status', '=', 1 ],
            ], '*', 'id asc');
            $week = date('N', strtotime($date));
            $max_num = 0;
            foreach ($rules as $rule) {
                if (!in_array($week, explode(',', $rule[ 'week_days' ]))) continue;
                $max_num = max($max_num, intval($rule[ 'max_num' ]));
            }
            if ($max_num <= 0) {
                model('goods_appointment_log')->rollback();
                return $this->error([], '该商品未配置预约时段');
            }

            $used_list = model('goods_appointment_log')->getList([
                [ 'goods_id', '=', $goods_id ],
                [ 'store_id', '=', $store_id ],
                [ 'appointment_date', '=', $date ],
                [ 'start_time', '=', $start_time ],
                [ 'status', 'in', [ self::STATUS_WAIT_PAY, self::STATUS_BOOKED ] ],
            ], 'id,num,status,expire_time', 'id asc');
            $used_num = 0;
            foreach ($used_list as $item) $used_num += intval($item[ 'num' ]);
            if ($used_num + $num > $max_num) {
                model('goods_appointment_log')->rollback();
                return $this->error([], '该时段名额已约满，请选择其他时段');
            }

            $time = time();
            $log_data = [
                'order_id' => intval($data[ 'order_id' ] ?? 0),
                'order_no' => $data[ 'order_no' ] ?? '',
                'site_id' => intval($data[ 'site_id' ] ?? 0),
                'site_name' => $data[ 'site_name' ] ?? '',
                'website_id' => intval($data[ 'website_id' ] ?? 0),
                'store_id' => $store_id,
                'store_name' => $data[ 'store_name' ] ?? '',
                'goods_id' => $goods_id,
                'goods_name' => $data[ 'goods_name' ] ?? '',
                'sku_id' => intval($data[ 'sku_id' ] ?? 0),
                'sku_name' => $data[ 'sku_name' ] ?? '',
                'appointment_date' => $date,
                'start_time' => $start_time,
                'end_time' => $data[ 'end_time' ] ?? '',
                'slot_text' => $data[ 'slot_text' ] ?? $start_time,
                'num' => $num,
                'user_name' => $data[ 'user_name' ] ?? '',
                'mobile' => $data[ 'mobile' ] ?? '',
                'remark' => $data[ 'remark' ] ?? '',
                'expire_time' => $time + intval($data[ 'expire_minutes' ] ?? self::DEFAULT_EXPIRE_MINUTES) * 60,
                'status' => self::STATUS_WAIT_PAY,
                'create_time' => $time,
                'update_time' => $time,
            ];
            $log_id = model('goods_appointment_log')->add($log_data);
            model('goods_appointment_log')->commit();
            return $this->success([ 'id' => $log_id, 'expire_time' => $log_data[ 'expire_time' ] ]);
        } catch (\Exception $e) {
            model('goods_appointment_log')->rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 订单创建成功后回填订单号
     */
    public function bindOrder($log_id, $order_id, $order_no)
    {
        if (empty($log_id)) return;
        model('goods_appointment_log')->update([
            'order_id' => $order_id,
            'order_no' => $order_no,
            'update_time' => time(),
        ], [ [ 'id', '=', intval($log_id) ] ]);
    }

    /**
     * 支付成功：预约生效；支付失败/取消：释放
     */
    public function confirm($order_id, $is_pay = true)
    {
        model('goods_appointment_log')->update([
            'status' => $is_pay ? self::STATUS_BOOKED : self::STATUS_CANCEL,
            'expire_time' => 0,
            'update_time' => time(),
        ], [ [ 'order_id', '=', $order_id ] ]);
    }

    /**
     * 取消预约 / 关闭订单释放名额
     */
    public function release($order_id, $status = self::STATUS_CANCEL)
    {
        $status = ($status == self::STATUS_EXPIRE) ? self::STATUS_EXPIRE : self::STATUS_CANCEL;
        model('goods_appointment_log')->update([
            'status' => $status,
            'update_time' => time(),
        ], [ [ 'order_id', '=', $order_id ], [ 'status', 'in', [ self::STATUS_WAIT_PAY, self::STATUS_BOOKED ] ] ]);
    }

    /**
     * 核销完成
     */
    public function finish($order_id)
    {
        model('goods_appointment_log')->update([
            'status' => self::STATUS_FINISH,
            'update_time' => time(),
        ], [ [ 'order_id', '=', $order_id ], [ 'status', '=', self::STATUS_BOOKED ] ]);
    }

    /**
     * 超时未支付释放名额
     * @return int 释放条数
     */
    public function expire()
    {
        $list = model('goods_appointment_log')->getList([
            [ 'status', '=', self::STATUS_WAIT_PAY ],
            [ 'expire_time', '>', 0 ],
            [ 'expire_time', '<', time() ],
        ], 'id,order_id,order_no', 'id asc', 'a', [], '', 200);

        $ids = array_column($list, 'id');
        if (empty($ids)) return 0;

        model('goods_appointment_log')->update([
            'status' => self::STATUS_EXPIRE,
            'update_time' => time(),
        ], [ [ 'id', 'in', $ids ] ]);

        $order_ids = array_filter(array_column($list, 'order_id'));
        if (!empty($order_ids)) {
            model('order')->update([
                'appointment_status' => self::STATUS_EXPIRE,
                'modify_time' => time(),
            ], [ [ 'order_id', 'in', $order_ids ], [ 'order_status', '=', 0 ], [ 'appointment_status', '=', self::STATUS_WAIT_PAY ] ]);
        }
        return count($ids);
    }

    /**
     * 商家改约
     */
    public function reschedule($order_id, $date, $start_time, $slot_text = '', $num = null)
    {
        $log = model('goods_appointment_log')->getInfo([ [ 'order_id', '=', $order_id ], [ 'status', '=', self::STATUS_BOOKED ] ], '*')[ 'data' ] ?? [];
        if (empty($log)) return $this->error([], '未找到预约记录');

        $this->release($order_id, self::STATUS_CANCEL);
        $res = $this->occupy([
            'goods_id' => $log[ 'goods_id' ],
            'sku_id' => $log[ 'sku_id' ],
            'site_id' => $log[ 'site_id' ],
            'site_name' => $log[ 'site_name' ],
            'website_id' => $log[ 'website_id' ],
            'store_id' => $log[ 'store_id' ],
            'store_name' => $log[ 'store_name' ],
            'goods_name' => $log[ 'goods_name' ],
            'sku_name' => $log[ 'sku_name' ],
            'appointment_date' => $date,
            'start_time' => $start_time,
            'slot_text' => $slot_text ?: $start_time,
            'num' => $num ?: $log[ 'num' ],
            'user_name' => $log[ 'user_name' ],
            'mobile' => $log[ 'mobile' ],
            'remark' => $log[ 'remark' ],
            'order_id' => $order_id,
            'order_no' => $log[ 'order_no' ],
        ]);
        if ($res[ 'code' ] != 0) return $res;

        $new_id = $res[ 'data' ][ 'id' ];
        model('goods_appointment_log')->update([
            'status' => self::STATUS_BOOKED,
            'expire_time' => 0,
        ], [ [ 'id', '=', $new_id ] ]);

        model('order')->update([
            'appointment_id' => $new_id,
            'appointment_date' => $date,
            'appointment_slot' => $slot_text ?: $start_time,
            'appointment_num' => $num ?: $log[ 'num' ],
            'appointment_status' => self::STATUS_BOOKED,
            'modify_time' => time(),
        ], [ [ 'order_id', '=', $order_id ] ]);

        return $this->success([ 'id' => $new_id ]);
    }

    /**
     * 商家日程列表
     */
    public function getScheduleList($condition = [], $date = '', $page = 1, $page_size = 20)
    {
        if (!empty($date)) $condition[] = [ 'appointment_date', '=', $date ];
        $list = model('goods_appointment_log')->pageList($condition, '*', 'appointment_date desc,start_time asc', $page, $page_size);
        return $this->success($list);
    }

    /**
     * 某时段已占用数量
     * @return array [ 'H:i' => num ]
     */
    public function slotUsedMap($goods_id, $store_id, $date)
    {
        $list = model('goods_appointment_log')->getList([
            [ 'goods_id', '=', $goods_id ],
            [ 'store_id', '=', $store_id ],
            [ 'appointment_date', '=', $date ],
            [ 'status', 'in', [ self::STATUS_WAIT_PAY, self::STATUS_BOOKED, self::STATUS_FINISH ] ],
        ], 'start_time,num', 'id asc');

        $map = [];
        foreach ($list as $item) {
            $key = $item[ 'start_time' ];
            $map[ $key ] = ($map[ $key ] ?? 0) + intval($item[ 'num' ] );
        }
        return $map;
    }

    /**
     * 时间格式化
     */
    public function toTimeStr($time)
    {
        $time = trim($time);
        if (empty($time)) return '09:00:00';
        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) return $time . ':00';
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time)) return sprintf('%02d:%s', intval(substr($time, 0, 2)), substr($time, 3));
        return '09:00:00';
    }

    /**
     * 星期名称
     */
    public function weekName($week)
    {
        $name = [ 1 => '周一', 2 => '周二', 3 => '周三', 4 => '周四', 5 => '周五', 6 => '周六', 7 => '周日' ];
        return $name[ intval($week) ] ?? '';
    }
}
