<?php
/**

 */

namespace addon\city\city\controller;

use app\model\upload\Upload as UploadModel;
use app\model\upload\Config as ConfigModel;

/**
 * 上传 控制器
 */
class Upload extends BaseCity
{


    /**
     * 上传(不存入相册)
     * @return \app\model\upload\Ambigous|\multitype
     */
    public function upload()
    {
        $upload_model = new UploadModel($this->site_id);
        $thumb_type = input("thumb", "");
        $name = input("name", "");
        $param = array (
            "thumb_type" => "",
            "name" => "file"
        );
        $result = $upload_model->setPath("common/images/" . date("Ymd") . '/')->image($param);
        return $result;
    }

    /**
     * 上传 存入相册
     * @return \multitype
     */
    public function uploadToAlbum()
    {
        $upload_model = new UploadModel($this->site_id);
        $album_id = input("album_id", 0);
        $name = input("name", "");
        $param = array (
            "thumb_type" => [ "big", "mid", "small" ],
            "name" => "file",
            'album_id' => $album_id
        );
        $result = $upload_model->setPath("common/images/" . date("Ymd") . '/')->imageToAlbum($param);
        return $result;
    }

    /**
     * 云上传方式
     */
    public function oss()
    {
        if (request()->isAjax()) {
            $config_model = new ConfigModel();
            $list = event('OssType', []);
            return $config_model->success($list);
        } else {
            $this->forthMenu();
            return $this->fetch("upload/oss");
        }
    }
}