<?php
/**

 */

namespace addon\shopcomponent\shop\controller;

use app\shop\controller\BaseShop;
use addon\shopcomponent\model\Video as VideoModel;

class Video extends BaseShop
{
    /**
     * 店铺 视频号 微信号 申请 编辑
     */
    public function operation()
    {
        if (request()->isAjax()) {
            $param = input();

            if (empty($param)) return $this->error('必要参数必填');
            $data = [
                'id' => $param['id'] ?? '',
                'site_id' => $this->site_id ?? 0,
                'name' => $param['name'],
                'wx_username' => $param['wx_username'],

            ];
            $video_model = new VideoModel();

            return $video_model->operationVideo(array_filter($data));
        }
    }

    /**
     * @return mixed
     * 视频号直播设置
     */
    public function access()
    {
        $video_model = new VideoModel();
        $condition = [
            ['site_id', '=', $this->site_id ?? 0]
        ];
        $data = $video_model->getVideoInfo($condition);

        $this->assign('info', $data['data'] ?? []);
        return $this->fetch("video/access");
    }

}