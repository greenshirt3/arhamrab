<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$query = strtolower(trim($_GET['q'] ?? ''));
$results = [];

if (strlen($query) < 1) {
    echo json_encode([]);
    exit;
}

// SEARCH LOGIC
if ($type === 'patient') {
    $data = getJSON(FILE_PATIENTS);
    foreach ($data as $d) {
        // Search by Name or ID or Phone
        if (str_contains(strtolower($d['name']), $query) || str_contains(strtolower($d['id']), $query) || str_contains($d['phone'], $query)) {
            $results[] = [
                'label' => $d['name'] . " (" . $d['id'] . ")",
                'value' => $d['id'], // What gets filled in the box
                'extra' => $d['phone']
            ];
        }
    }
} 
elseif ($type === 'medicine') {
    $data = getJSON(FILE_INVENTORY);
    foreach ($data as $d) {
        if (str_contains(strtolower($d['name']), $query)) {
            $results[] = [
                'label' => $d['name'] . " (Stock: " . $d['stock_qty'] . ")",
                'value' => $d['name'],
                'extra' => 'PKR ' . $d['unit_price']
            ];
        }
    }
} 
elseif ($type === 'doctor') {
    $data = getJSON(FILE_USERS);
    foreach ($data as $d) {
        if ($d['role'] === 'doctor' && str_contains(strtolower($d['name']), $query)) {
            $results[] = [
                'label' => "Dr. " . $d['name'] . " (" . $d['dept'] . ")",
                'value' => $d['id'],
                'extra' => $d['dept']
            ];
        }
    }
}

// Return top 10 matches only
echo json_encode(array_slice($results, 0, 10));
?>