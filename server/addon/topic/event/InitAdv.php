<?php
/**

 */

namespace addon\topic\event;

use app\model\web\AdvPosition;

/**
 * 广告
 * @author Administrator
 *
 */
class InitAdv
{
    public function handle($data)
    {
        $adv_position_model = new AdvPosition();
        $res = $adv_position_model->addAdvPosition([
            'ap_name' => '专题活动',
            'keyword' => 'NS_TOPIC',
            'ap_intro' => '',
            'ap_height' => '400',
            'ap_width' => '1920',
            'default_content' => '',
            'ap_background_color' => '#FFFFFF',
            'type' => 2,
        ]);

    }
}
