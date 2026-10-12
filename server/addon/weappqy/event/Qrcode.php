<?php
/**

 */


namespace addon\weappqy\event;

use addon\weappqy\model\Weapp;
/**
 * 二维码
 */
class Qrcode
{
	/**
	 * 二维码生成获取
	 */
	public function handle($param)
	{
	    if ($param["app_type"] == 'weappqy' || $param["app_type"] == 'all') {
	        if ($param["app_type"] == 'all') $param["app_type"] = 'weappqy';
            $weapp = new Weapp();
            if ($param["type"] == 'create') {
                $res = $weapp->createQrcode($param);
            } else {
                $filename = $param['qrcode_path'] . '/' . $param['qrcode_name'] . '_' . $param['app_type'] . '.png';
                if (file_exists($filename)) {
                    $res = success(0, '', ['path' => $filename]);
                } else {
                    $res = $weapp->createQrcode($param);
                }
            }             
    	    return $res;
	    }
	}
}