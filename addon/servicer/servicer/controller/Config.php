<?php

/**

 */

namespace addon\servicer\servicer\controller;

use addon\servicer\model\Fast as FastModel;
use app\model\system\Servicer as ServicerModel;

/**
 * 配置相关
 * Class Config
 * @package addon\servicer\servicer\controller
 */
class Config extends BaseServicer
{
    /**
     * 获取配置
     */
    public function config()
    {
        if (request()->isAjax()) {
            $servicer_model = new ServicerModel();
            return $servicer_model->getServicerConfig();
        }
    }

    /**
     * 快捷回复列表
     */
    public function fastList()
    {
        if (request()->isAjax()) {
            $fast_model = new FastModel();
            return $fast_model->getList([['site_id', '=', $this->site_id]], 'content');
        }
    }
}