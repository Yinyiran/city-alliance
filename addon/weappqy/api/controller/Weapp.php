<?php
/**

 */

namespace addon\weappqy\api\controller;

use addon\weappqy\model\Message;
use app\api\controller\BaseApi;
use addon\weappqy\model\Weapp as WeappModel;

class Weapp extends BaseApi
{
	/**
	 * 获取openid
	 */
	public function authCodeToOpenid()
	{
		$weapp_model = new WeappModel();
		$res = $weapp_model->authCodeToOpenid($this->params);
		return $this->response($res);
	}

    /**
     * 获取消息模板id(最多三条)
     */
    public function messageTmplIds(){
        $keywords = $this->params['keywords'] ?? '';
        $message = new Message();
        $res = $message->getMessageTmplIds($keywords);
        return $this->response($res);
    }
}