<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
include '../db.php'; // Connect to StackCP

$action = $_POST['action'] ?? '';

// --- 1. FETCH ORDERS (SQL Version) ---
if ($action == 'fetch_orders') {
    // Fetch ALL orders ordered by newest first
    $sql = "SELECT * FROM orders ORDER BY id DESC";
    $result = $conn->query($sql);
    
    $orders = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Decode JSON items column back to array
            $row['items'] = json_decode($row['items'], true);
            $orders[] = $row;
        }
    }
    echo json_encode($orders);
    exit;
}

// --- 2. FETCH DATA (Shops/Menu) ---
// Note: If you have a 'products' table, update this too. 
// For now, we can keep reading JSON for products if you haven't moved them to SQL.
if ($action == 'get_data') {
    // Fetch Shops from SQL
    $shops = [];
    $res = $conn->query("SELECT * FROM shops");
    while($r = $res->fetch_assoc()) { $shops[] = $r; }

    // Keep Products as JSON for now (unless you have a products table)
    $menu = file_exists('../data/products.json') ? json_decode(file_get_contents('../data/products.json'), true) : [];
    
    echo json_encode(['shops' => $shops, 'menu' => $menu]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>