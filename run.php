<?php

if (count($argv) < 2) {
    p('Usage: php run.php <DAYNUMBER>');
    die();
}

$day = $argv[1];
if (!is_numeric($day)) {
    p('Usage: php run.php <DAYNUMBER>');
    die();
}

if (!file_exists(__DIR__ . '/day' . $day . '/run.php')) {
    p('day not found (' . __DIR__ . '/day' . $day . '/run.php)');
    die();
}

$executionStartTime = microtime(true);
echo 'Day ' . $day . "\n";
$dayStartTime = microtime(true);
include __DIR__ . '/day' . $day . '/run.php';
$dayTime = number_format((microtime(true) - $dayStartTime) * 1000, 3, '.', '') . 'ms';
echo 'Day time ' . $dayTime . "\n\n";
