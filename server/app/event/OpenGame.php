<?php
/**

 */

namespace app\event;

use app\model\games\Games;

/**
 * 开启小游戏
 * @author Administrator
 *
 */
class OpenGame
{
    public function handle($param)
    {
        $model  = new Games();
        $result = $model->cronOpenGames($param['relate_id']);
        return $result;
    }
}
