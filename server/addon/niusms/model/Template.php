<?php
/**

 */

namespace addon\niusms\model;

use app\model\BaseModel;


class Template extends BaseModel
{

    /**
     * 获取模板信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getTemplateInfo($keywords, $field = "*")
    {
        $info = model('sms_template')->getInfo([ [ 'keywords', '=', $keywords ] ], $field);
        if(empty($info)) $info = [];
        //获取message信息
        $message = model('message')->getInfo([ [ 'keywords', '=', $keywords ] ]);
        if (empty($message)) {
            $data = [
                'keywords' => $keywords,
                'sms_is_open' => 0,
            ];
            model('message')->add($data);
            $message = model('message')->getInfo([ [ 'keywords', '=', $keywords ] ]);
        }
        $info = array_merge($info, $message);

        return $this->success($info);
    }
}