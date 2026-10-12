<?php
namespace think;

// 加载基础文件
require __DIR__ . '/../vendor/autoload.php';

// 应用初始化
$http = (new App())->http;
$response = $http->name('admin')->run();

use think\facade\Db;
use think\facade\Cache;

header('Content-Type: text/html; charset=utf-8');

echo "<h2>城市合伙人系统 - 菜单及页面修复初始化工具</h2>";

$menus = [
    // 1. 总后台 - 会员管理模块
    [
        'app_module' => 'admin',
        'addon' => '',
        'title' => '会员开通分润明细',
        'name' => 'MEMBER_LEVEL_SETTLEMENT',
        'parent' => 'MEMBER_ROOT',
        'level' => 2,
        'url' => 'admin/memberlevel/settlementlist',
        'is_show' => 1,
        'sort' => 4,
        'desc' => '展示会员购买开通邀约提成、主理人分成、分公司分成与退款作废冲正',
        'is_control' => 1
    ],
    // 2. 总后台 - 营销分销模块
    [
        'app_module' => 'admin',
        'addon' => 'fenxiao',
        'title' => '分销结算明细',
        'name' => 'PROMOTION_FENXIAO_MONEY',
        'parent' => 'PROMOTION_FENXIAO_ROOT',
        'level' => 3,
        'url' => 'fenxiao://admin/fxorder/lists',
        'is_show' => 1,
        'sort' => 4,
        'desc' => '展示带单分润、主理人提成、员工提成、分公司提成明细',
        'is_control' => 1
    ],
    // 3. 总后台 - 财务管理快捷入口（双保险，财务人员无需进营销即可直接查看）
    [
        'app_module' => 'admin',
        'addon' => 'fenxiao',
        'title' => '分销结算明细',
        'name' => 'ACCOUNT_FENXIAO_MONEY',
        'parent' => 'ACCOUNT_ROOT',
        'level' => 2,
        'url' => 'fenxiao://admin/fxorder/lists',
        'is_show' => 1,
        'sort' => 5,
        'desc' => '财务管理直达分销结算明细',
        'is_control' => 1
    ],
    [
        'app_module' => 'admin',
        'addon' => '',
        'title' => '会员开通分润明细',
        'name' => 'ACCOUNT_MEMBER_LEVEL_SETTLEMENT',
        'parent' => 'ACCOUNT_ROOT',
        'level' => 2,
        'url' => 'admin/memberlevel/settlementlist',
        'is_show' => 1,
        'sort' => 6,
        'desc' => '财务管理直达会员开通分润明细',
        'is_control' => 1
    ],
    // 4. 分站后台 - 商家管理模块
    [
        'app_module' => 'city',
        'addon' => 'city',
        'title' => '门店收益与业绩报表',
        'name' => 'ADDON_CITY_SHOP_REVENUE_REPORT',
        'parent' => 'ADDON_CITY_SHOP_INDEX',
        'level' => 3,
        'url' => 'city://city/shop/revenuereport',
        'is_show' => 1,
        'sort' => 4,
        'desc' => '分站查看下辖门店带单收益与会员收益业绩报表',
        'is_control' => 1
    ],
    // 5. 分站后台 - 资产模块
    [
        'app_module' => 'city',
        'addon' => 'city',
        'title' => '分公司账户流水',
        'name' => 'ADDON_CITY_ACCOUNT_ORDERLIST',
        'parent' => 'ADDON_CITY_ACCOUNT_ROOT',
        'level' => 2,
        'url' => 'city://city/account/orderlist',
        'is_show' => 1,
        'sort' => 2,
        'desc' => '分公司订单交易与结算流水明细',
        'is_control' => 1
    ],
    [
        'app_module' => 'city',
        'addon' => 'city',
        'title' => '会员开通分润明细',
        'name' => 'ADDON_CITY_ACCOUNT_MEMBER_SETTLEMENT',
        'parent' => 'ADDON_CITY_ACCOUNT_ROOT',
        'level' => 2,
        'url' => 'city://city/account/membersettlement',
        'is_show' => 1,
        'sort' => 3,
        'desc' => '分站查看本区域会员开通分公司分成收益明细',
        'is_control' => 1
    ],
    [
        'app_module' => 'city',
        'addon' => 'city',
        'title' => '会员开通分润明细',
        'name' => 'ADDON_CITY_SHOP_MEMBER_SETTLEMENT',
        'parent' => 'ADDON_CITY_SHOP_INDEX',
        'level' => 3,
        'url' => 'city://city/account/membersettlement',
        'is_show' => 1,
        'sort' => 5,
        'desc' => '商家管理直达会员开通分润明细',
        'is_control' => 1
    ],
    // 6. 商户端后台 - 资产模块
    [
        'app_module' => 'shop',
        'addon' => '',
        'title' => '会员开通分润明细',
        'name' => 'SHOP_ACCOUNT_MEMBER_SETTLEMENT',
        'parent' => 'ACCOUNT_ROOT',
        'level' => 2,
        'url' => 'shop/account/membersettlement',
        'is_show' => 1,
        'sort' => 5,
        'desc' => '门店查看本店会员开通主理人分成收益明细',
        'is_control' => 1
    ]
];

// 开启分站【资产】一级导航显示（极其关键）
Db::name('menu')->where('name', 'ADDON_CITY_ACCOUNT_ROOT')->where('app_module', 'city')->update(['is_show' => 1]);
echo "<p style='color:green;'>√ 已确保分站后台顶级【资产】菜单显示状态已开启 (is_show = 1)</p>";

$success_count = 0;
foreach ($menus as $m) {
    try {
        Db::name('menu')->where('name', $m['name'])->where('app_module', $m['app_module'])->delete();
        Db::name('menu')->insert($m);
        echo "<p style='color:green;'>√ 成功注册菜单：[{$m['app_module']}] {$m['title']} ({$m['url']})</p>";
        $success_count++;
    } catch (\Exception $e) {
        echo "<p style='color:red;'>× 注册失败：{$m['title']}，错误：" . $e->getMessage() . "</p>";
    }
}

// 自动向财务组、运营组等管理员分组补齐菜单权限
$sync_groups = [27, 28];
$new_menu_keys = ['MEMBER_LEVEL_SETTLEMENT', 'PROMOTION_FENXIAO_MONEY', 'ACCOUNT_FENXIAO_MONEY', 'ACCOUNT_MEMBER_LEVEL_SETTLEMENT'];
foreach ($sync_groups as $gid) {
    $group = Db::name('group')->where('group_id', $gid)->find();
    if ($group && !empty($group['menu_array'])) {
        $arr = explode(',', $group['menu_array']);
        $diff = array_diff($new_menu_keys, $arr);
        if (!empty($diff)) {
            $updated_menu_array = implode(',', array_merge($arr, $diff));
            Db::name('group')->where('group_id', $gid)->update(['menu_array' => $updated_menu_array]);
            echo "<p style='color:green;'>√ 已自动向角色组 [ID: {$gid} {$group['group_name']}] 补充菜单权限</p>";
        }
    }
}

// 清除菜单缓存
try {
    Cache::tag("menu")->clear();
    Cache::clear();
    echo "<p style='color:blue;'>√ 系统菜单缓存（Cache::tag('menu')）及应用缓存已完全清空！</p>";
} catch (\Exception $e) {
    echo "<p style='color:orange;'>! 缓存清除提示：" . $e->getMessage() . "</p>";
}

echo "<hr><h3 style='color:green;'>修复完成！共注入/更新 {$success_count} 个关键菜单节点。请刷新后台页面即可看到菜单。</h3>";
