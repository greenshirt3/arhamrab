<?php
header('Content-Type: application/json');

// 1. Get Data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_id']) || !isset($input['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$file = '../data/orders.json';
$orders = json_decode(file_get_contents($file), true);
$updated = false;

// 2. Find and Update Order
foreach ($orders as &$order) {
    if ($order['id'] == $input['order_id']) {
        
        // ACTION: ASSIGN RIDER
        if ($input['action'] === 'assign_rider') {
            $order['rider_id'] = $input['rider_id'];
            $order['status'] = 'Assigned';
            $updated = true;
        }
        
        // ACTION: CHANGE STATUS
        if ($input['action'] === 'update_status') {
            $order['status'] = $input['status'];
            $updated = true;
        }
        
        break; // Stop loop once found
    }
}

// 3. Save Changes
if ($updated) {
    file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
}
?>