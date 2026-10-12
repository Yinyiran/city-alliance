<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\shopapplet\OrderCommon;
use app\model\shopapplet\Order as OrderModel;
use app\model\order\StoreOrder as storeOrderModel;
use think\facade\Cache;
use think\facade\Db;




class Order extends Common
{
    protected $orderModel;
    protected $storeOrderModel;

/**
 * 企业端  订单
    *自营是我自己的客户买我的品牌，
    *带单是别人的客户给我下单也叫收单，
    *贡献是我的人买别人商品
 */
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->orderModel=new OrderModel();
        $this->storeOrderModel=new storeOrderModel();
        $this->site_id=session("site_id");
    
    }

    /**
     * 订单列表
     */
    public function orderlist(){
        $params=request()->param();
        $selectby="";
        $searchtxt="";
        $ordertype="";
        if(request()->has("ordertype")){
            $ordertype=request()->param("ordertype");
        }else{
            $ordertype=0;
        }

        if(request()->has("selectby")){
            $selectby=request()->param("selectby");
        }else{
            $selectby="day";
        }
        if(request()->has("searchtxt")){
            $searchtxt=request()->param("searchtxt");
        }
        if(request()->has("page")){
            $page=request()->param("page");
        }else{
            $page=1;
        }
        switch($ordertype){
            case 1:
                //自营订单                
                $lists=$this->orderModel->getzyOrderList($this->site_id, $selectby, $searchtxt, $page);
                break;
            case 2:
                //带单订单
                $lists=$this->orderModel->getddOrderList($this->site_id, $selectby, $searchtxt, $page);
                break;
            case 3:
                //贡献订单
                $lists=$this->orderModel->getgxOrderList($this->site_id, $selectby, $searchtxt, $page);
                break;
            case 0:
                $lists=$this->orderModel->getzyOrderList($this->site_id,$selectby,$searchtxt,$page);
                break;
        }
        return $this->response($lists);
    }


    /**
     * 核销订单列表
     */
    public function deliveryOrder(){
        if(request()->has("page")){
            $page=request()->param("page");
        }else{
            $page=1;
        }
        $lists=$this->orderModel->hxorder($this->site_id,$page);
        return $this->response($lists);
    }
    
    

    /**
     * 订单二维码
     */
    public function createqrcode(Request $request){
        require_once('./vendor/Weixin_1/WeapiOauth.php');
        require_once('./vendor/Weixin_1/WXBizDataCrypt.php');
         //$delivery_code="202508201820150001";
         $delivery_code=request()->param("delivery_code");
         if(empty($delivery_code)){
            return $this->error("请输入订单号");
         }

         $oauth = new \Weixin_1\WeapiOauth();
         $scene="delivery_code=".$delivery_code;
         $page="otherpages/signCode/signCode"; //地址待定
         $data = '{"scene":"' . $scene . '","page":"' . $page . '","width":280}';
         $appID="wx78e3faf3a96aadd2";
         $appSecret="7414c95568a5ca4277cbc3b5e24da7bb";
         $result=$oauth->create_qrcode($data,$appID,$appSecret);
         $filename = "qrcode_" . $member_id . ".jpg";//要生成的图片名字
         $IMG_PATH = "/public/qrcode/order";//保存服务器路径
        if (file_exists('./'.$IMG_PATH .'/'. $filename)) { //存在返回图片路径
            $filepath=$IMG_PATH .'/'. $filename;
            return $this->response($result);
        }
        $res = file_put_contents('./'.$IMG_PATH .'/'. $filename,$result);
        $filepath=$IMG_PATH .'/'. $filename;
        $response=[
            'code'=>0,
            "filepath"=>$filepath
        ];
        return $this->response($response);
    }

    /**
     * 根据核销码/订单号/动态Token定位订单
     */
     protected function findOrderByCode($code)
     {
         if (empty($code)) return null;

         // 如果是JSON字符串，解析它
         if (is_string($code) && (strpos($code, '{') === 0)) {
             $json_data = json_decode($code, true);
             if (!empty($json_data['code'])) {
                 $code = $json_data['code'];
             } elseif (!empty($json_data['delivery_code'])) {
                 $code = $json_data['delivery_code'];
             } elseif (!empty($json_data['order_no'])) {
                 $code = $json_data['order_no'];
             }
         }

         // 1. 优先查缓存中的动态token
         $order_id = Cache::get('order_verify_token_' . $code);
         if ($order_id) {
             $order = Db::name('order')->where('order_id', $order_id)->find();
             if (!empty($order)) return $order;
         }

         // 2. 按 delivery_code 查询
         $order = Db::name('order')->where('delivery_code', $code)->find();
         if (!empty($order)) return $order;

         // 3. 按 order_no 查询
         $order = Db::name('order')->where('order_no', $code)->find();
         if (!empty($order)) return $order;

         // 4. 按 verify 表查询
         $verify_rec = Db::name('verify')->where('verify_code', $code)->find();
         if (!empty($verify_rec) && !empty($verify_rec['relate_id'])) {
             $order = Db::name('order')->where('order_id', $verify_rec['relate_id'])->find();
             if (!empty($order)) return $order;
         }

         // 5. 若是纯数字且大于0，尝试 order_id
         if (is_numeric($code) && $code > 0) {
             $order = Db::name('order')->where('order_id', (int)$code)->find();
             if (!empty($order)) return $order;
         }

         return null;
     }

    /**
     * 扫码显示订单核销信息
     */
    public function getOrderInfo(){
        $delivery_code = request()->param("delivery_code") ?: request()->param("code");
        if(empty($delivery_code)){
            return $this->response([
                'code' => -1,
                'message' => '请输入或扫描核销码',
                'data' => []
            ]);
        }

        $order_info = $this->findOrderByCode($delivery_code);
        if (empty($order_info)) {
            return $this->response([
                'code' => -1,
                'message' => '未找到对应的订单信息，请确认核销码是否正确',
                'data' => []
            ]);
        }

        // 校验所属门店权限
        $site_id = request()->param('site_id') ?: ($this->site_id ?: session("site_id"));
        if (!empty($site_id) && $order_info['site_id'] != $site_id) {
            return $this->response([
                'code' => -1,
                'message' => '该订单属于【' . ($order_info['site_name'] ?: '其他门店') . '】，当前商家无权核销！',
                'data' => []
            ]);
        }

        // 查询买家信息及会员等级
        $member = Db::name("member")->alias("m")
            ->leftJoin("member_level ml", "ml.level_id = m.member_level")
            ->field("m.nickname, m.mobile, ml.level_name, ml.consume_discount")
            ->where("m.member_id", $order_info['member_id'])
            ->find();

        // 查询订单商品明细
        $goods_list = Db::name("order_goods")
            ->where("order_id", $order_info['order_id'])
            ->select()
            ->toArray();

        $is_verify = ($order_info['order_status'] == 4 || $order_info['delivery_status'] == 1) ? 1 : 0;

        $result = [
            'order_id' => $order_info['order_id'],
            'order_no' => $order_info['order_no'],
            'delivery_code' => $delivery_code,
            'order_status' => $order_info['order_status'],
            'order_status_name' => $is_verify ? '已核销' : ($order_info['order_status_name'] ?: '待核销'),
            'delivery_status' => $order_info['delivery_status'],
            'delivery_status_name' => $is_verify ? '已核销' : ($order_info['delivery_status_name'] ?: '待核销'),
            'pay_status' => $order_info['pay_status'],
            'pay_type_name' => $order_info['pay_type_name'],
            'order_money' => $order_info['order_money'],
            'pay_money' => $order_info['pay_money'],
            'create_time' => date('Y-m-d H:i:s', $order_info['create_time']),
            'pay_time' => !empty($order_info['pay_time']) ? date('Y-m-d H:i:s', $order_info['pay_time']) : '',
            'site_id' => $order_info['site_id'],
            'site_name' => $order_info['site_name'],
            'name' => $order_info['name'] ?: ($member['nickname'] ?? '顾客'),
            'mobile' => $order_info['mobile'] ?: ($member['mobile'] ?? ''),
            'level_name' => $member['level_name'] ?? '普通会员',
            'consume_discount' => $member['consume_discount'] ?? 0,
            'consume_money' => $order_info['pay_money'],
            //交易类型：收银台据此判断是否支持自定义金额(1)、是否可改约(2)
            'trade_type' => intval( $order_info[ 'trade_type' ] ?? 0 ),
            'trade_type_name' => \app\model\order\OrderCreate::tradeTypeName( $order_info[ 'trade_type' ] ?? 0 ),
            //预约到店(2)日程信息，供收银台展示与改约
            'appointment_date' => $order_info[ 'appointment_date' ] ?? '',
            'appointment_slot' => $order_info[ 'appointment_slot' ] ?? '',
            'appointment_status' => $order_info[ 'appointment_status' ] ?? -1,
            'delivery_store_name' => $order_info[ 'delivery_store_name' ] ?? '',
            'is_verify' => $is_verify,
            'goods_list' => $goods_list
        ];

        return $this->response([
            'code' => 0,
            'message' => 'success',
            'data' => $result
        ]);
    }

    /**
     * 执行核销操作
     */
    public function dodelivery(){
        $delivery_code = request()->param("delivery_code") ?: request()->param("code");
        if(empty($delivery_code)){
            return $this->response([
                'code' => -1,
                'message' => '请输入或扫描核销码',
                'data' => []
            ]);
        }

        $order_info = $this->findOrderByCode($delivery_code);
        if (empty($order_info)) {
            return $this->response([
                'code' => -1,
                'message' => '未找到对应的订单，无法核销',
                'data' => []
            ]);
        }

        // 校验所属门店权限
        $site_id = request()->param('site_id') ?: ($this->site_id ?: session("site_id"));
        if (!empty($site_id) && $order_info['site_id'] != $site_id) {
            return $this->response([
                'code' => -1,
                'message' => '该订单属于【' . ($order_info['site_name'] ?: '其他门店') . '】，当前商家无权核销！',
                'data' => []
            ]);
        }

        // 状态检查
        if ($order_info['order_status'] == 4 || $order_info['delivery_status'] == 1) {
            return $this->response([
                'code' => -1,
                'message' => '该订单商品已核销，请勿重复核销！',
                'data' => []
            ]);
        }
        if ($order_info['order_status'] == -1) {
            return $this->response([
                'code' => -1,
                'message' => '该订单已关闭，无法核销！',
                'data' => []
            ]);
        }
        if ($order_info['order_status'] == 0 && $order_info['pay_status'] == 0) {
            return $this->response([
                'code' => -1,
                'message' => '该订单未支付，无法核销！',
                'data' => []
            ]);
        }

        // 链路校验：仅到店核销类(order_type=2)可核销。
        // 快递(1)/同城配送(3)/虚拟商品(4)没有门店核销环节，若放行会直接把订单
        // 置为 order_status=4「已核销」，污染订单状态机与后续退款/结算。
        if (intval($order_info['order_type']) != 2) {
            $type_name = [
                1 => '快递发货',
                2 => '到店核销',
                3 => '同城配送',
                4 => '虚拟商品',
            ][intval($order_info['order_type'])] ?? '未知类型';
            return $this->response([
                'code' => -1,
                'message' => '该订单为【' . $type_name . '】类型，无需到店核销，请走发货/收货流程！',
                'data' => []
            ]);
        }

        $order_id = $order_info['order_id'];

        //统一走 StoreOrder 状态机（与 PC 端 Cashier::verify / Verify 控制器同一实现）。
        //activeTakeDelivery 内部负责：order_type=2 校验、待提货状态校验、并发锁、
        //order_goods 置已提货、order 置已收货(sign_time/评价)、订单日志、自动完成任务、
        //OrderTakeDelivery 事件与站内消息。原实现为裸 Db update，绕过上述全部校验，
        //且与 PC 端形成两套口径（一边「已提货」一边「已核销」），故统一收敛。
        //核销时段不符（迟到超出宽限/非预约日期/未进入时段）时，后端返回 code=-2，
        //需商家确认后带 force=1 重新提交才真正核销，避免直接阻断商家履约。
        $force = intval( $this->params[ 'force' ] ?? 0 ) > 0;
        $store_order_model = new storeOrderModel();
        $result = $store_order_model->activeTakeDelivery( $order_id, $force );

        if (!is_array($result) || (isset($result['code']) && $result['code'] < 0)) {
            $need_force = isset( $result[ 'code' ] ) && intval( $result[ 'code' ] ) == -2;
            return $this->response( [
                'code' => $need_force ? -2 : -1,
                'message' => (is_array($result) && !empty($result[ 'message' ])) ? $result[ 'message' ] : '核销失败',
                'data' => $need_force ? ( $result[ 'data' ] ?? [] ) : []
            ] );
        }

        $now = time();

        //到店类按交易类型修正终态文案（状态码统一为 order_status=4）
        $trade_type = intval($order_info['trade_type'] ?? 0);
        $finish_name = [
            0 => '已提货',
            1 => '已消费',
            2 => '已核销',
        ][$trade_type] ?? '已核销';
        Db::name('order')->where('order_id', $order_id)->update([
            'order_status_name' => $finish_name,
        ]);

        //补充 verify 表核销记录（状态机只覆盖订单维度，核销码维度需单独回写）
        $employee_name = session('employee_info')['username'] ?? '商家员工';
        $verify_update = [
            'is_verify' => 1,
            'verify_time' => $now,
            'verifier_name' => $employee_name
        ];
        Db::name('verify')->where('relate_id', $order_id)->update($verify_update);
        if (!empty($order_info['delivery_code'])) {
            Db::name('verify')->where('verify_code', $order_info['delivery_code'])->update($verify_update);
        }

        //清理核销动态码缓存，避免同一动态码被重复核销
        Cache::delete('order_verify_token_' . $delivery_code);
        if (!empty($order_info['delivery_code'])) {
            Cache::delete('order_verify_token_' . $order_info['delivery_code']);
        }

        return $this->response([
            'code' => 0,
            'message' => '核销成功',
            'data' => [
                'order_id' => $order_id,
                'order_no' => $order_info['order_no'],
                'order_status' => 4,
                'order_status_name' => $finish_name,
                'goods_status' => '已核销'
            ]
        ]);
    }
}