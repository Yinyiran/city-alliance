<?php
/**

 */


namespace addon\seckill\event;



use addon\pc\model\Pc;

/**
 * PC端导航
 * @author Administrator
 *
 */
class InitPcNav
{
    public function handle($data)
    {
        $link = [
            [
                'title' => '秒杀专区',
                'url' => '/promotion/seckill',
                'sort' => 6,
            ]
        ];
        if(addon_is_exit('pc')){
            $pc_model = new Pc();
            foreach ($link as $k => $v) {
                $pc_model->deleteNav([ ['nav_title','=',$v['title']] ]);
                $sort = $v['sort'];
                unset($v['sort']);
                $add_data = [
                    'nav_title' => $v['title'],
                    'nav_url' => json_encode($v, 320),
                    'sort' => $sort,
                    'is_blank' => 0,
                    'create_time' => time(),
                    'is_show' => 1
                ];
                $pc_model->addNav($add_data);
            }
        }


        return 1;
    }
}
