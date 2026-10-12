<?php
/**

 */


namespace app\event;

use app\model\express\Kd100;

/**
 * 初始化配置信息
 * @author Administrator
 *
 */
class Kd100Trace
{
    public function handle($data)
    {
        $kd100_model = new Kd100();
        $express_no_data = $data[ "express_no_data" ];
        $express_no = $express_no_data[ "express_no_kd100" ];
        $result = $kd100_model->trace($data[ "code" ], $express_no);
        return $result;
    }

}
