<?php
/**

 */

namespace app\event;

use app\model\member\MemberCluster as MemberClusterModel;

/**
 * 初始化添加会员群体定时刷新事件
 */
class AddMemberClusterCronRefresh
{
    // 行为扩展的执行入口必须是run
    public function handle()
    {
        $member_cluster_model = new MemberClusterModel();
        $result = $member_cluster_model->addMemberClusterCronRefresh();
        return $result;
    }
}