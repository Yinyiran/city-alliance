<?php
/**
 * 小程序审核设置
 *
 * 提供「只读体验账号」的开关与会员选择，供微信审核 / 演示人员
 * 在没有真实手机号授权的情况下进入小程序查看真实数据。
 *
 * 体验账号下所有写操作会被 app\shopapplet\controller\Common 统一拦截，
 * 详见 app\model\shopapplet\DemoAccount::BLOCK_ACTIONS。
 */

namespace app\admin\controller;

use app\model\shopapplet\DemoAccount;

class Miniapp extends BaseAdmin
{
    /**
     * 体验账号配置
     */
    public function demoaccount()
    {
        if (request()->isAjax()) {
            $enable = intval(input('enable', 0)) === 1 ? 1 : 0;
            $member_id = intval(input('member_id', 0));
            return DemoAccount::saveConfig($enable, $member_id);
        }

        $config = DemoAccount::config();
        $this->assign('config', [
            'enable' => !empty($config[ 'enable' ]) ? 1 : 0,
            'member_id' => !empty($config[ 'member_id' ]) ? $config[ 'member_id' ] : 0,
            'mobile' => $config[ 'mobile' ] ?? '',
            'shop_name' => $config[ 'shop_name' ] ?? '',
        ]);

        return $this->fetch('miniapp/demo_account');
    }

    /**
     * 体验账号会员搜索（选择器数据源）
     */
    public function searchmember()
    {
        $keyword = input('keyword', '');
        $list = DemoAccount::candidateMembers($keyword);
        $options = [];
        foreach ($list as $item) {
            $name = trim(($item[ 'nickname' ] ?? '') . ' ' . ($item[ 'username' ] ?? ''));
            $options[] = [
                'id' => intval($item[ 'member_id' ]),
                'name' => $name !== '' ? $name : ('ID' . $item[ 'member_id' ]),
                'mobile' => $item[ 'mobile' ] ?? '',
                'text' => ($name !== '' ? $name : ('ID' . $item[ 'member_id' ])) . ' (' . ($item[ 'mobile' ] ?? '') . ')',
            ];
        }
        return [
            'code' => 0,
            'message' => '',
            'data' => $options,
        ];
    }
}