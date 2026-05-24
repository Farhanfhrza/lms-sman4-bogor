<?php

header('Content-Type: text/plain');

$logFile = __DIR__ . '/../storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Log file not found at: $logFile\n";
    exit;
}

$lines = file($logFile);
$count = count($lines);
$tail = array_slice($lines, max(0, $count - 100));

echo "Last 100 lines of laravel.log:\n";
echo implode('', $tail);
exit;
