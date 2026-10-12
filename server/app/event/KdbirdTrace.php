<?php
/**

 */


namespace app\event;

use app\model\express\Kdbird;

/**
 * 初始化配置信息
 * @author Administrator
 *
 */
class KdbirdTrace
{
    public function handle($data)
    {
        $kdbird_model = new Kdbird();
        $express_no_data = $data["express_no_data"];
        $express_no = $express_no_data["express_no_kdniao"];
        $mobile = $data["mobile"] ?? '';
        $result = $kdbird_model->trace($data["code"], $express_no, $mobile);
        return $result;
    }
}
