<?php
// FIX: Use __DIR__ to always find config correctly
require_once __DIR__ . '/config.php';

// 1. DATA HANDLING (JSON WRAPPERS)
function getJSON($filename) {
    if (!file_exists($filename)) return [];
    $data = file_get_contents($filename);
    return json_decode($data, true) ?? [];
}

function saveJSON($filename, $data) {
    // Add error check to prevent saving empty files
    $json = json_encode($data, JSON_PRETTY_PRINT);
    if ($json === false) return false;
    return file_put_contents($filename, $json);
}

// 2. ID GENERATOR (Format: PRE-YEAR-RAND)
function generateID($prefix) {
    return strtoupper($prefix . '-' . date('y') . '-' . rand(1000, 9999));
}

// 3. QR CODE GENERATOR
function getQRCode($data) {
    return "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($data);
}

// 4. ALERTS & NOTIFICATIONS
function setFlash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function displayFlash() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type']; 
        $msg = $_SESSION['flash']['msg'];
        echo "<div class='alert alert-{$type} shadow-sm border-0 mb-4 animate__animated animate__fadeIn'>{$msg}</div>";
        unset($_SESSION['flash']);
    }
}

// 5. SEARCH HELPER
function findEntry($array, $key, $value) {
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $value) return $item;
    }
    return null;
}

// 6. UI HELPERS (Restored)
function getRoleBadge($role) {
    $colors = [
        'admin' => 'bg-dark',
        'doctor' => 'bg-primary',
        'reception' => 'bg-info text-dark',
        'lab' => 'bg-warning text-dark',
        'pharmacy' => 'bg-success'
    ];
    $c = $colors[$role] ?? 'bg-secondary';
    return "<span class='badge {$c} rounded-pill text-uppercase'>" . ucfirst($role) . "</span>";
}
?>