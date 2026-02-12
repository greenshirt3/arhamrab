<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['rider_id']) || !isset($input['lat']) || !isset($input['lng'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit;
}

$file = '../data/riders.json';
$riders = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

$updated = false;
foreach ($riders as &$r) {
    if ($r['id'] === $input['rider_id']) {
        $r['lat'] = $input['lat'];
        $r['lng'] = $input['lng'];
        $r['last_seen'] = time(); // Track when they were last online
        $updated = true;
        break;
    }
}

if ($updated) {
    file_put_contents($file, json_encode($riders, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Rider not found']);
}
?>