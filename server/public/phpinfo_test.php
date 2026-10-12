<?php
echo "PHP_OK " . PHP_VERSION . "\n";
echo "REMOTE_ADDR=" . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'none') . "\n";
echo "CWD=" . getcwd() . "\n";
