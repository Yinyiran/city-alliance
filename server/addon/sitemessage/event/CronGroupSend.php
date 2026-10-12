<?php
/**

 */

namespace addon\sitemessage\event;



use addon\sitemessage\model\Sitemessage;

/**
 * 会员定时群发
 */
class CronGroupSend
{

    /**
     * 商品营销活动信息
     * @param $param
     * @return array
     */
    public function handle($param)
    {
        $relate_id = $param['relate_id'];//业务id
        $site_message_model = new Sitemessage();
        $params = [
            'records_id' => $relate_id
        ];
        $result = $site_message_model->groupSend($params);
        return $result;
    }
}