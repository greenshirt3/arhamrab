<?php
// public_html/family/api/monitor_log.php
require 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$child = $input['child_name'] ?? 'Unknown';
$type = $input['type'] ?? 'log';

// Save to a daily log file for that child
$logFile = $DATA_DIR . 'log_' . $child . '_' . date('Y-m-d') . '.json';
$current_logs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

$current_logs[] = [
    "time" => date("H:i:s"),
    "type" => $type,
    "data" => $input['data']
];

write_json($logFile, $current_logs);
echo json_encode(["status" => "logged"]);
?>