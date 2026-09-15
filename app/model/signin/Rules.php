<?php
/**

 */

namespace app\model\signin;

use app\model\BaseModel;
use think\facade\Db;
use think\facade\Cache;
use app\model\signin\Records as RecordsModel;


/**
 * 签到管理
 */
class Rules extends BaseModel

{

    public function getCount(){
        $count=Db::name('sign_rules')->count();
        return $count;
    }
 /**
     * 获取签到规则分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getRulesPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $join=[
            ["goods g","g.goods_id=r.goods_id","left"]

        ];
        $order="title asc";
        $alias="r";
        $field="r.*,g.goods_name";
        $list = model('sign_rules')->pageList($condition, $field, $order, $page, $page_size,$alias, $join, '', '');
        return $this->success($list);
    }

    /**
     * 会员签到规则
     */
    public function addrules($data)
    {
        $res=empty(trim($data["title"]))? $this->error("请输入规则名称"):'';
        $res=empty(trim($data["reward_value"]))? $this->error("请输入奖励值"):'';

        $where=["title"=>$data["title"]];
        $exist=model('sign_rules')->getColumn($where, 'rules_id');
        if(!empty($exist)){
            return $this->error("规则名称已存在");
        }
        $data['create_time']=time();
        if (request()->isAjax()) {
            try{
                $res=Db::name('sign_rules')->save($data);
            }catch(\Exception $e){
                return $this->error($e->getMessage());

            }
            return $this->success("添加成功");

        } 
        return $this->error("请提交数据");
    }

    /**
     * 会员签到规则编辑
     */
    public function editrules($data)
    {
        $res=empty(trim($data["title"]))? $this->error("请输入规则名称"):'';
        $res=empty(trim($data["reward_value"]))? $this->error("请输入奖励值"):'';
        $where="title='".$data["title"]."' and rules_id <>".$data["rules_id"];
        $exist=model('sign_rules')->getColumn($where, 'rules_id');
        if(!empty($exist)){
            return $this->error("规则名称已存在");
        }
        if (request()->isAjax()) {
            $where=['rules_id',"=",$data["rules_id"]];
            $res=Db::name("sign_rules")->where("rules_id=".$data["rules_id"])->update($data);

            return $this->success("编辑成功");

        } 
        return $this->error("请提交数据");
    }

    /**
     * 删除签到规则
     * @param int|array $rules_id
     * @return array
     */
    public function delrules($rules_id)
    {
        if (empty($rules_id)) {
            return $this->error("请选择要删除的规则");
        }

        try {
            $res = Db::name('sign_rules')->whereIn('rules_id', $rules_id)->delete();
            if ($res) {
                return $this->success("删除成功");
            } else {
                return $this->error("规则不存在或已被删除");
            }
        } catch (\Exception $e) {
            return $this->error("删除失败: " . $e->getMessage());
        }
    }

    /**
     * 获取指定规则
     */
    public function getrules($title){
        $res=Db::name('sign_rules')->where("title=".$title)->find();
        return $res;
    }

    //获取大礼包商品列表
    public function getGoodsBy2(){
        $goods=Db::name("goods")->where("goods_type",2)->select();
        return $goods;
    }

    //小程序端签到列表
    public function getRulesList($member_id){
        $memberinfo=Db::name("member")->field("shop_sign_round")->where("member_id=".$member_id)->find();
      
        if(empty($memberinfo)){
            return $this->error("参数错误");

        }
        $recordmodel=new RecordsModel();
        $shop_sign_round=$memberinfo["shop_sign_round"];
        $res=Db::name("sign_rules")->order("title asc")->select(); 
        foreach($res as $key =>$val){
            $signdata=$recordmodel->getSigninByday($member_id,$val["rules_id"],$shop_sign_round);
            $val["signdata"]=$signdata;
            $res[$key]=$val;
          
        }
        $signcount=$recordmodel->getSignCount($member_id,$shop_sign_round);
       
        $result["list"]=$res;
        $result["signcount"]=$signcount;


        return $this->success($result);
    }

   
}