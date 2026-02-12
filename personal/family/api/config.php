<?php
// public_html/personal/family/api/config.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
date_default_timezone_set("Asia/Karachi");

// Path Correction: Go up one level (..) to 'family' folder
$DATA_DIR = __DIR__ . '/../data/';
$UPLOAD_DIR = __DIR__ . '/../uploads/';

// Debug: If you get errors, uncomment this line to see where it's trying to save
// echo "Saving to: " . $DATA_DIR; exit; 

if (!file_exists($DATA_DIR)) mkdir($DATA_DIR, 0777, true);
if (!file_exists($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0777, true);

function write_json($file, $data) {
    $fp = fopen($file, 'c+');
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}
?>