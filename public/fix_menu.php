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
    [
        'app_module' => 'admin',
        'addon' => 'fenxiao',
        'title' => '分销结算明细',
        'name' => 'PROMOTION_FENXIAO_MONEY',
        'parent' => 'PROMOTION_FENXIAO_ROOT',
        'level' => 2,
        'url' => 'fenxiao://admin/fxorder/lists',
        'is_show' => 1,
        'sort' => 4,
        'desc' => '展示带单分润、主理人提成、员工提成、分公司提成明细',
        'is_control' => 1
    ],
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
    ]
];

$success_count = 0;
foreach ($menus as $m) {
    try {
        Db::name('menu')->where('name', $m['name'])->delete();
        Db::name('menu')->insert($m);
        echo "<p style='color:green;'>√ 成功注册菜单：[{$m['app_module']}] {$m['title']} ({$m['url']})</p>";
        $success_count++;
    } catch (\Exception $e) {
        echo "<p style='color:red;'>× 注册失败：{$m['title']}，错误：" . $e->getMessage() . "</p>";
    }
}

// 清除菜单缓存
try {
    Cache::tag("menu")->clear();
    echo "<p style='color:blue;'>√ 系统菜单缓存（menu tag）已成功清除！</p>";
} catch (\Exception $e) {
    echo "<p style='color:orange;'>! 缓存清除提示：" . $e->getMessage() . "</p>";
}

echo "<hr><h3 style='color:green;'>修复完成！共注入/更新 {$success_count} 个关键菜单节点。请登录总后台及分站后台刷新查看。</h3>";
