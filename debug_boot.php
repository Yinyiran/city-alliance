<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
$log = 'd:/Project/city-alliance-server/debug_result.txt';

// 模拟 Apache 对 /index.php/api/website/info 的完整请求
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/index.php/api/website/info';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = 'd:/Project/city-alliance-server/public/index.php';
$_SERVER['DOCUMENT_ROOT'] = 'd:/Project/city-alliance-server/public';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_HOST'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'curl';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['QUERY_STRING'] = '';
$_SERVER['PATH_INFO'] = '/api/website/info';
$_SERVER['ORIG_PATH_INFO'] = '/index.php/api/website/info';

chdir('d:/Project/city-alliance-server/public');

register_shutdown_function(function () use ($log) {
    $e = error_get_last();
    if ($e) { file_put_contents($log, "SHUTDOWN: {$e['message']} @ {$e['file']}:{$e['line']}\n", FILE_APPEND); }
});
set_error_handler(function ($sev, $msg, $file, $line) use ($log) {
    file_put_contents($log, "ERR[$sev]: $msg @ $file:$line\n", FILE_APPEND);
    return false;
});

try {
    ob_start();
    require 'd:/Project/city-alliance-server/public/index.php';
    $out = ob_get_clean();
    file_put_contents($log, "LEN: " . strlen($out) . "\n", FILE_APPEND);
    if (preg_match('/<h1>(.*?)<\/h1>/s', $out, $m)) { file_put_contents($log, "H1: " . strip_tags($m[1]) . "\n", FILE_APPEND); }
    if (preg_match('/<div class="message"[^>]*>(.*?)<\/div>/s', $out, $m)) { file_put_contents($log, "MSG: " . trim(strip_tags($m[1])) . "\n", FILE_APPEND); }
} catch (\Throwable $e) {
    file_put_contents($log, "EX: " . get_class($e) . ": " . $e->getMessage() . "\nAT: " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);
}
echo "END\n";
