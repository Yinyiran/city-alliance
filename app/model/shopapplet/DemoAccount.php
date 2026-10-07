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
    /** 配置缓存键（后台保存后需主动失效） */
    const CACHE_KEY = 'SHOP_APPLET_DEMO_CONFIG';

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
     * 用户端（app/api）体验账号下被禁止的操作
     *
     * 用户端与商家端不同：面向消费者，资金与下单动作分散在多个 controller
     * （提现、下单、支付、退款…）。这里只拦截「会真实产生资金/订单变动」的关键动作，
     * 其余浏览类接口（商品、店铺、评价、地址列表等）保持可用，保证审核能看到完整页面。
     *
     * 键为「控制器/方法」小写，匹配 request()->controller() . '/' . request()->action()
     */
    const CLIENT_BLOCK_ACTIONS = [
        'memberwithdraw/apply' => '体验账号为只读模式，无法发起提现',
        'memberwithdraw/transfertype' => '体验账号为只读模式，无法变更提现方式',
        'ordercreate/create' => '体验账号为只读模式，无法下单',
        'ordercreate/buy' => '体验账号为只读模式，无法下单',
        'order/cancel' => '体验账号为只读模式，无法取消订单',
        'order/refund' => '体验账号为只读模式，无法申请退款',
        'pay/*' => '体验账号为只读模式，无法发起支付',
        'ordercomplain/add' => '体验账号为只读模式，无法提交投诉',
        'goodscollect/*' => '体验账号为只读模式，无法修改收藏',
        'cart/*' => '体验账号为只读模式，无法修改购物车',
    ];

    /**
     * 判断用户端某接口是否被体验账号拦截
     *
     * @return string 被拒绝时的提示语；空字符串表示允许
     */
    public static function blockedMessageForClient(string $key) : string
    {
        $key = strtolower(trim($key, '/'));
        if ($key === '') return '';
        if (isset(self::CLIENT_BLOCK_ACTIONS[ $key ])) {
            return self::CLIENT_BLOCK_ACTIONS[ $key ];
        }
        // 支持通配：pay/* 之类的前缀匹配
        foreach (self::CLIENT_BLOCK_ACTIONS as $pattern => $msg) {
            if (substr($pattern, -2) !== '/*') continue;
            $prefix = substr($pattern, 0, -1); // 保留末尾 '/'
            if (strpos($key, $prefix) === 0) return $msg;
        }
        return '';
    }

    /**
     * 读取体验账号配置
     * @return array { enable: bool, member_id: int, mobile: string, shop_name: string }
     */
    public static function config() : array
    {
        $cached = Cache::get(self::CACHE_KEY);
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

        Cache::set(self::CACHE_KEY, $config, 360);
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

    /**
     * 保存体验账号配置（后台「小程序审核设置 → 体验账号」使用）
     *
     * @param int $enable    1 开启 / 0 关闭
     * @param int $member_id 体验账号对应的会员 ID
     * @return array
     */
    public static function saveConfig(int $enable, int $member_id) : array
    {
        if ($enable === 1 && $member_id <= 0) {
            return [
                'code' => -1,
                'message' => '请先选择体验账号会员',
                'data' => [],
            ];
        }

        // 开启时必须校验：该会员需存在且未删除
        if ($enable === 1) {
            $member = Db::name('member')
                ->where([ [ 'member_id', '=', $member_id ], [ 'is_delete', '=', 0 ] ])
                ->find();
            if (empty($member)) {
                return [
                    'code' => -1,
                    'message' => '所选会员不存在或已删除',
                    'data' => [],
                ];
            }
        }

        $value = json_encode([
            'enable' => $enable === 1 ? 1 : 0,
            'member_id' => $member_id,
        ], JSON_UNESCAPED_UNICODE);

        $existing = Db::name('config')
            ->where([
                [ 'site_id', '=', 0 ],
                [ 'app_module', '=', 'admin' ],
                [ 'config_key', '=', 'SHOP_APPLET_DEMO_MEMBER' ],
            ])
            ->find();

        if (!empty($existing)) {
            Db::name('config')->where([ [ 'id', '=', $existing[ 'id' ] ] ])->update([
                'value' => $value,
                'config_desc' => '商家/用户小程序只读体验账号（微信审核/演示），关闭后体验登录入口不可用',
                'modify_time' => time(),
            ]);
        } else {
            Db::name('config')->insert([
                'site_id' => 0,
                'app_module' => 'admin',
                'config_key' => 'SHOP_APPLET_DEMO_MEMBER',
                'value' => $value,
                'config_desc' => '商家/用户小程序只读体验账号（微信审核/演示），关闭后体验登录入口不可用',
                'is_use' => 1,
                'create_time' => time(),
                'modify_time' => time(),
            ]);
        }

        // 配置变更后立即失效缓存，避免后台改了要等 6 分钟才生效
        Cache::delete(self::CACHE_KEY);

        return [
            'code' => 0,
            'message' => '保存成功',
            'data' => [],
        ];
    }

    /**
     * 后台选择器用：可作为体验账号的会员列表
     *
     * 只返回「已绑定手机号」且未删除的会员，避免选中后无法登录。
     */
    public static function candidateMembers(string $keyword = '', int $limit = 50) : array
    {
        $query = Db::name('member')
            ->where([ [ 'is_delete', '=', 0 ] ])
            ->where('mobile', '<>', '')
            ->field('member_id,nickname,username,mobile,headimg')
            ->order('member_id desc');

        $keyword = trim($keyword);
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('mobile', 'like', '%' . $keyword . '%')
                    ->whereOr('nickname', 'like', '%' . $keyword . '%')
                    ->whereOr('username', 'like', '%' . $keyword . '%');
            });
        }

        return $query->limit($limit)->select()->toArray();
    }
}
