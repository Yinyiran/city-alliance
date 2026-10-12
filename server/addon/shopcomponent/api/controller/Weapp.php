<?php
/**

 */

namespace addon\shopcomponent\api\controller;

use app\api\controller\BaseApi;
use addon\weapp\model\Weapp as WeappModel;

class Weapp extends BaseApi
{
    /**
     * 检查场景值是否在支付校验范围内
     * @return mixed
     */
    public function sceneCheck(){
        $scene = $this->params['scene'] ?? '';
        $weapp_model = new WeappModel();
        $result = $weapp_model->sceneCheck($scene);
        return $this->response($result);
    }
}