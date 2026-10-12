<?php
/**

 */

namespace addon\presale\event;

/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{

    public function handle($data)
    {
        $from_type = [

            'balance' => [
                'presale_order' => [
                    'type_name' => '预售订单',
                    'type_url'  => '',
                ]
            ],
            'balance_money' => [
                'presale_order' => [
                    'type_name' => '预售订单',
                    'type_url'  => '',
                ]
            ]
        ];
        if ($data == '') {
            return $from_type;
        } else {
            if (isset($from_type[$data])) {
                return $from_type[$data];
            } else {
                return [];
            }
        }

    }
}