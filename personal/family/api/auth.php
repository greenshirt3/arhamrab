<?php
// public_html/family/api/auth.php
require 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$usersFile = $DATA_DIR . 'users.json';

// Create default admin user if empty
if (!file_exists($usersFile)) {
    write_json($usersFile, [
        ["username" => "admin", "password" => "1234", "name" => "Saif Ullah"]
    ]);
}

if ($action === 'login') {
    $users = json_decode(file_get_contents($usersFile), true);
    foreach ($users as $u) {
        if ($u['username'] === $input['username'] && $u['password'] === $input['password']) {
            echo json_encode(["status" => "success", "user" => $u]);
            exit;
        }
    }
    echo json_encode(["status" => "error", "message" => "Invalid Login"]);
}
?>