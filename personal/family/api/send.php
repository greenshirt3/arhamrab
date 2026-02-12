<?php
// public_html/family/api/send.php
require 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$chatFile = $DATA_DIR . 'chat_general.json'; // Default group chat

$message = [
    "id" => uniqid(),
    "sender" => $input['sender'],
    "text" => htmlspecialchars($input['text']),
    "time" => date("h:i A")
];

// Open and append safely
$current_msgs = file_exists($chatFile) ? json_decode(file_get_contents($chatFile), true) : [];
$current_msgs[] = $message;

// Keep only last 100 messages to stay fast
if (count($current_msgs) > 100) array_shift($current_msgs);

write_json($chatFile, $current_msgs);
echo json_encode(["status" => "success"]);
?>