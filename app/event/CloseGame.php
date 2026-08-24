<?php
/**

 */

namespace app\event;

use app\model\games\Games;

/**
 * 关闭小游戏
 * @author Administrator
 *
 */
class CloseGame
{
    public function handle($param)
    {
        $model  = new Games();
        $result = $model->cronCloseGames($param['relate_id']);
        return $result;
    }
}
