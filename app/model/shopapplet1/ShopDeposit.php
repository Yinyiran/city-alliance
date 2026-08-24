<?php
/**

 */

namespace app\model\shopapplet;

use app\model\BaseModel;
use think\facade\Cache;
use app\model\system\Pay;
use think\facade\Db;

/**
 * 店铺保证金（预存款）
 */
class ShopDeposit extends BaseModel
{

     /**
     * 创建保证金订单
     */
    public function ordercreate($data)
    {
        //判断是否交过保证金
        $shop_deposit = model('shop_deposit_order')->getInfo([ [ 'site_id', '=', $data['site_id'] ], [ 'site_id', '=', $data['site_id'] ] ]);
        if(!empty($shop_deposit)){
            if ($shop_deposit['pay_time'] > 0) {
                return $this->error('', '已缴纳过保证金');
            }
        }
       
        $shopinfo=Db::name("shop")->where("site_id=".$data["site_id"])->find();
        $shopinfo['deposit_amount']=10;//保证金金额先设置固定为10元
        $deposit_amount=$shopinfo['deposit_amount'];
        //创建或加入
        $pay = new Pay();
        $out_trade_no = $pay->createOutTradeNo($data['site_id']);
        $order_no = $this->createOrderNo(0, $data['site_id']);

        $order_data = [
            'order_no' => $order_no,
            'member_id'=>$data['member_id'],
            'out_trade_no' => $out_trade_no,
            'website_id' => $shopinfo["website_id"],
            'price' => $deposit_amount,
            'status' => 1,
            'create_time' => time(),
            'site_id' => $shopinfo['site_id'],
            'site_name' => $shopinfo['site_name'],
            'logo' => $shopinfo['logo']
        ];

        try {
            //$order_id=Db::name("shop_deposit_order")->save($order_data);
            $order_id = model("shop_deposit_order")->add($order_data);
            //生成整体支付单据
            $pay->addPay($data['site_id'], $out_trade_no, "", "店铺缴纳保证金" , '',$order_data['price'], '', 'ShopDepositOrderPayNotify', '');
           
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
        $order_info = model('shop_deposit')->getInfo([ [ 'out_trade_no', '=', $data[ 'out_trade_no' ] ] ]);
        if ($order_info[ 'status' ] == 1) {
            model('order')->startTrans();
            try {
                //修改订单状态
                $order_data = [
                    'pay_time' => time(),
                    'status' => 2
                ];
                $res = model('shop_deposit')->update($order_data, [ [ 'out_trade_no', '=', $data[ 'out_trade_no' ] ] ]);
                //修改店铺保证金缴纳状态
                model('shop')->update(['deposit_status'=>1],['site_id'=>$order_info['site_id']]);
                //修改支付状态
                $payModel = model('pay')->getInfo(['out_trade_no' => $data[ 'out_trade_no' ]]);
                model('pay')->update(['pay_type' => 'HUIFU', 'pay_status' => 2, 'pay_time' => time()], ['id' => $payModel['id']]);

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