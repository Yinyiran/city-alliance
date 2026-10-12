<?php
namespace app\shopapplet\controller;


use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\order\OrderCommon;
use app\model\message\Message;

use app\model\member\MemberAccount as MemberAccountModel;
use app\model\member\Member as MemberModel;
use think\facade\Db;

/**
 * 积分列表
 */
class Points extends Common
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }

    /**
     * 积分列表
     * @param int $point_type 积分来源:1=邀约奖励,2=下单奖励,3=积分消耗,0=全部积分
     * @param int $page 分页
     * @param int $page_size 每页数量
     */
    public function getList(){
        $data = [
            'member_id' => $this->member_id,
            'page' => $this->params['page'] ?? 1,
            'page_size' => $this->params['page_size'] ?? PAGE_LIST_ROWS,
            'point_type' => $this->params['point_type']??0, //积分来源:1=邀约奖励,2=下单奖励,3=积分消耗,0=全部积分
        ];
        $member_account_model = new MemberAccountModel();
        $res = $member_account_model->pointAccount($data);
        $memberinfo=Db::name("member")->where("member_id=".$this->member_id)->value("point");
        $res["data"]["point"]=$memberinfo;
        $res["data"]["use_point"]=$memberinfo;//可用积分
        return $this->response($res);
    }
}