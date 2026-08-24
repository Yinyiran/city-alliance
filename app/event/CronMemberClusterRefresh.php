<?php
/**

 */

namespace app\event;

use app\model\member\MemberCluster as MemberClusterModel;

/**
 * 刷新会员群体会员信息
 */
class CronMemberClusterRefresh
{
    // 行为扩展的执行入口必须是run
    public function handle()
    {
        $member_cluster_model = new MemberClusterModel();
        $result = $member_cluster_model->refreshMemberCluster();//会员群体定时刷新
        return $result;
    }
}