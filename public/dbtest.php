<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$log = 'd:/Project/city-alliance-server/debug_result.txt';
file_put_contents($log, "=== DB test ===\n", FILE_APPEND);
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=www_cmyx_top;charset=utf8', 'www_cmyx_top', 'JRnPbxHyp8EYa1E3');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $st = $pdo->query('SELECT COUNT(*) FROM website');
    $cnt = $st->fetchColumn();
    file_put_contents($log, "DB OK, website rows=" . $cnt . "\n", FILE_APPEND);
} catch (\Throwable $e) {
    file_put_contents($log, "DB FAIL: " . $e->getMessage() . "\n", FILE_APPEND);
}
echo "DONE\n";
