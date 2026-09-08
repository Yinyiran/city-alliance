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
        $site_id = $this->site_id ?: session("site_id");
        if (!empty($site_id) && $order_info['site_id'] != $site_id) {
            return $this->response([
                'code' => -1,
                'message' => '该订单属于【' . $order_info['site_name'] . '】，当前商家无权核销！',
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
        $site_id = $this->site_id ?: session("site_id");
        if (!empty($site_id) && $order_info['site_id'] != $site_id) {
            return $this->response([
                'code' => -1,
                'message' => '该订单属于其他门店，无法核销！',
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

        // 事务执行核销
        Db::startTrans();
        try {
            $now = time();
            $order_id = $order_info['order_id'];

            // 1. 将 order_goods 商品状态改为已核销
            Db::name('order_goods')->where('order_id', $order_id)
                ->where('refund_status', '<>', 3)
                ->update([
                    'delivery_status' => 1,
                    'delivery_status_name' => '已核销'
                ]);

            // 2. 更新主订单状态为已核销 (order_status = 4: ORDER_TAKE_DELIVERY)
            $order_update = [
                'order_status' => 4,
                'order_status_name' => '已核销',
                'delivery_status' => 1,
                'delivery_status_name' => '已核销',
                'sign_time' => $now,
                'is_evaluate' => 1,
                'evaluate_status' => 0,
                'evaluate_status_name' => '待评价'
            ];
            Db::name('order')->where('order_id', $order_id)->update($order_update);

            // 3. 更新 verify 表
            $employee_name = session('employee_info')['username'] ?? '商家员工';
            Db::name('verify')->where('verify_code', $order_info['delivery_code'])
                ->orWhere('relate_id', $order_id)
                ->update([
                    'is_verify' => 1,
                    'verify_time' => $now,
                    'verifier_name' => $employee_name
                ]);

            // 4. 清理缓存核销动态码
            Cache::delete('order_verify_token_' . $delivery_code);
            if (!empty($order_info['delivery_code'])) {
                Cache::delete('order_verify_token_' . $order_info['delivery_code']);
            }

            // 5. 写入订单日志
            $order_log = [
                'order_id' => $order_id,
                'action' => '商家扫码核销',
                'order_status' => 4,
                'order_status_name' => '已核销',
                'create_time' => $now,
                'uid' => $this->member_id ?? 0,
                'nick_name' => $employee_name
            ];
            Db::name('order_log')->insert($order_log);

            // 6. 触发收货事件
            event('OrderTakeDelivery', ['order_id' => $order_id]);

            Db::commit();

            return $this->response([
                'code' => 0,
                'message' => '核销成功',
                'data' => [
                    'order_id' => $order_id,
                    'order_no' => $order_info['order_no'],
                    'order_status' => 4,
                    'order_status_name' => '已核销',
                    'goods_status' => '已核销'
                ]
            ]);
        } catch (\Exception $e) {
            Db::rollback();
            return $this->response([
                'code' => -1,
                'message' => '核销失败：' . $e->getMessage(),
                'data' => []
            ]);
        }
    }
}