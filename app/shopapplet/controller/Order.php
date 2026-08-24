<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\shopapplet\OrderCommon;
use app\model\shopapplet\Order as OrderModel;
use app\model\order\StoreOrder as storeOrderModel;




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
     * 扫码显示订单核销金额
     */
    public function getOrderInfo(){
        $delivery_code=request()->param("delivery_code");
        $orderModel=new OrderModel();
        $orderInfo=$orderModel->getOrderInfobycode($delivery_code);
        return $this->response($orderInfo);
    }

    /**
     * 执行核销操作
     */
    public function dodelivery(){
        $delivery_code=request()->param("delivery_code");
        if(empty($delivery_code)){
            return $this->error("请输入订单号");

        }
        $res=$this->storeOrderModel->verify($delivery_code);
        return $this->response($res);
    }

}