<?php
/**

 */

namespace addon\store\store\controller;

use app\model\upload\Upload as UploadModel;

/**
 * 图片上传
 * Class Verify
 * @package app\shop\controller
 */
class Upload extends BaseStore
{
    public $site_id = 0;
    protected $app_module = "store";

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

    }

    /**
     * 上传文件
     */
    public function file()
    {
        $upload_model = new UploadModel($this->site_id);

        $param = array (
            "name" => "file",
            'extend_type' => [ 'xlsx' ]
        );

        $result = $upload_model->setPath("common/store/file/" . date("Ymd") . '/')->file($param);
        return $result;
    }

}