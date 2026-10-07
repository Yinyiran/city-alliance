<?php
/**
 * 商家小程序「只读体验账号」
 *
 * 背景：商家端是 B 端应用，mobileAuth 只允许已在 shop_employee 表中的手机号登录。
 * 微信审核人员使用自己的微信手机号无法进入有数据的登录态，导致审核判定
 * 「【登录】无法加载完整数据内容、页面白屏、页面报错」。
 *
 * 方案：提供一个只读体验账号入口（登录页「体验账号登录」），审核/演示人员无需
 * 真实手机号即可进入并查看真实数据。为避免资损与数据污染，体验账号下所有
 * 写操作（提现、核销、充值、加员工、改 Banner 等）一律拒绝。
 *
 * 配置项（系统配置表 config，app_module = admin，site_id = 0）：
 *   SHOP_APPLET_DEMO_ENABLE  : 1 = 开启，0 = 关闭（默认关闭，审核前再打开）
 *   SHOP_APPLET_DEMO_MEMBER  : 体验账号对应的 member_id
 */
namespace app\model\shopapplet;

use app\model\BaseModel;
use think\facade\Cache;
use think\facade\Db;

class DemoAccount extends BaseModel
{
    /** 体验账号下被禁止的写操作（方法名 => 提示语） */
    const BLOCK_ACTIONS = [
        'dodelivery' => '体验账号为只读模式，无法执行核销操作',
        'deposit' => '体验账号为只读模式，无法进行保证金充值',
        'adds' => '体验账号为只读模式，无法添加员工',
        'bannerAdd' => '体验账号为只读模式，无法新增门店 Banner',
        'bannerDelete' => '体验账号为只读模式，无法删除门店 Banner',
        'openPartnerByBoss' => '体验账号为只读模式，无法开通合伙人',
        'openAccount' => '体验账号为只读模式，无法提交开户信息',
        'openCard' => '体验账号为只读模式，无法提交开卡信息',
        'shopLogin' => '体验账号为只读模式，无法切换商家',
    ];

    /**
     * 读取体验账号配置
     * @return array { enable: bool, member_id: int, mobile: string, shop_name: string }
     */
    public static function config() : array
    {
        $cache_key = 'SHOP_APPLET_DEMO_CONFIG';
        $cached = Cache::get($cache_key);
        if (!empty($cached)) return $cached;

        $config = [
            'enable' => 0,
            'member_id' => 0,
            'mobile' => '',
            'shop_name' => '',
        ];

        $row = Db::name('config')
            ->where([
                [ 'site_id', '=', 0 ],
                [ 'app_module', '=', 'admin' ],
                [ 'config_key', '=', 'SHOP_APPLET_DEMO_MEMBER' ],
            ])
            ->find();

        if (!empty($row) && isset($row[ 'value' ])) {
            $value = is_array($row[ 'value' ]) ? $row[ 'value' ] : (json_decode($row[ 'value' ], true) ?: []);
            $member_id = intval($value[ 'member_id' ] ?? 0);
            if ($member_id > 0) {
                $member = Db::name('member')->where([ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ])->find();
                if (!empty($member)) {
                    $config[ 'enable'] = !empty($value[ 'enable' ]) ? 1 : 0;
                    $config[ 'member_id' ] = $member_id;
                    $config[ 'mobile' ] = $member[ 'mobile' ] ?? '';
                    $shop = Db::name('shop')
                        ->where([ [ 'member_id', '=', $member_id ] ])
                        ->find();
                    if (empty($shop) && !empty($member[ 'mobile' ])) {
                        $shop = Db::name('shop')->where([ [ 'mobile', '=', $member[ 'mobile' ] ] ])->find();
                    }
                    $config[ 'shop_name' ] = $shop[ 'shop_name' ] ?? ( $shop[ 'name' ] ?? '' );
                }
            }
        }

        Cache::set($cache_key, $config, 360);
        return $config;
    }

    /**
     * 当前登录者是否为体验账号
     */
    public static function isDemo(int $member_id) : bool
    {
        if ($member_id <= 0) return false;
        $config = self::config();
        return !empty($config[ 'enable' ]) && $config[ 'member_id' ] == $member_id;
    }

    /**
     * 写操作拦截：体验账号直接返回错误，避免误操作与资损
     *
     * @return string 被拒绝时的提示语；空字符串表示允许
     */
    public static function blockedMessage(string $action) : string
    {
        return self::BLOCK_ACTIONS[ $action ] ?? '';
    }
}
