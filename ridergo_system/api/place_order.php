<?php
// --- 1. CORS & SECURITY HEADERS (THE FIX) ---
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle "Preflight" OPTIONS request
// Browsers send this first to check if it's safe to connect. We must say "Yes" and stop.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// --- 2. GET INPUT DATA ---
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

// --- 3. LOAD EXISTING ORDERS ---
$file = '../data/orders.json';
$orders = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// --- 4. CREATE NEW ORDER ---
// We use the data sent from your shop's frontend
$new_order = [
    'id' => rand(10000, 99999),            // Unique 5-digit Order ID
    'shop_id' => $input['shop_id'],        // e.g. "lovebitespizza"
    'shop_name' => $input['shop_name'],    // e.g. "Love Bites Pizza"
    'customer_name' => $input['customer_name'] ?? 'Guest', // e.g. "Ali (0300...)"
    'items' => $input['items'] ?? $input['cart'], // Handle both naming conventions
    'total' => $input['total'],
    'note' => $input['note'] ?? '',        // Customer Address/Note
    'status' => 'Pending',                 // Default status for Kitchen
    'timestamp' => date('Y-m-d H:i:s'),
    'rider_id' => null
];

// --- 5. SAVE TO DATABASE ---
// Add new order to the TOP of the list (array_unshift)
array_unshift($orders, $new_order);
file_put_contents($file, json_encode($orders, JSON_PRETTY_PRINT));

// --- 6. RESPOND TO FRONTEND ---
echo json_encode([
    'status' => 'success', 
    'order_id' => $new_order['id'],
    'message' => 'Order placed successfully'
]);
?>