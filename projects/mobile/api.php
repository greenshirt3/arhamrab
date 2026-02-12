<?php
// api.php - Backend Logic & Database Manager
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// --- CONFIGURATION ---
$dataDir = __DIR__ . '/data';
$productsFile = $dataDir . '/products.json';
$ordersFile = $dataDir . '/orders.json';

// --- AUTO-SETUP DATABASE ---
if (!is_dir($dataDir)) mkdir($dataDir, 0777, true);

if (!file_exists($productsFile)) {
    // Initial Stock Data
    $initialProducts = [
        [
            "id" => 101, "name" => "iPhone 15 Pro Max", "brand" => "Apple", "price" => 545000, 
            "stock" => 5, "image" => "https://images.unsplash.com/photo-1696446701796-da61225697cc?w=600",
            "category" => "Mobile", "specs" => "256GB | Titanium"
        ],
        [
            "id" => 102, "name" => "Samsung S24 Ultra", "brand" => "Samsung", "price" => 420000, 
            "stock" => 8, "image" => "https://images.unsplash.com/photo-1707227137357-19d20c355886?w=600",
            "category" => "Mobile", "specs" => "12GB | AI Features"
        ],
        [
            "id" => 103, "name" => "AirPods Pro 2", "brand" => "Apple", "price" => 68000, 
            "stock" => 20, "image" => "https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=600",
            "category" => "Accessories", "specs" => "USB-C MagSafe"
        ],
        [
            "id" => 104, "name" => "Pixel 8 Pro", "brand" => "Google", "price" => 280000, 
            "stock" => 3, "image" => "https://images.unsplash.com/photo-1696320092305-b0b230238e87?w=600",
            "category" => "Mobile", "specs" => "Obsidian | 128GB"
        ]
    ];
    file_put_contents($productsFile, json_encode($initialProducts, JSON_PRETTY_PRINT));
}

if (!file_exists($ordersFile)) {
    file_put_contents($ordersFile, json_encode([]));
}

// --- API ACTIONS ---
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

if ($action === 'get_products') {
    echo file_get_contents($productsFile);
    exit;
}

if ($action === 'get_orders') {
    echo file_get_contents($ordersFile);
    exit;
}

if ($action === 'create_order') {
    // 1. Load Data
    $products = json_decode(file_get_contents($productsFile), true);
    $orders = json_decode(file_get_contents($ordersFile), true);
    
    $cart = $input['cart'];
    $customer = $input['customer']; // Type: 'Online' or 'Walk-in'
    $total = 0;
    $errors = [];

    // 2. Validate Stock & Calculate Total
    foreach ($cart as $item) {
        $found = false;
        foreach ($products as &$p) {
            if ($p['id'] == $item['id']) {
                $found = true;
                if ($p['stock'] >= $item['qty']) {
                    $p['stock'] -= $item['qty']; // DEDUCT STOCK
                    $total += $p['price'] * $item['qty'];
                } else {
                    $errors[] = "Insufficient stock for " . $p['name'];
                }
                break;
            }
        }
        if (!$found) $errors[] = "Product ID " . $item['id'] . " not found.";
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "message" => implode(", ", $errors)]);
        exit;
    }

    // 3. Save Order
    $newOrder = [
        "order_id" => "ORD-" . time(),
        "date" => date("Y-m-d H:i:s"),
        "customer" => $customer,
        "items" => $cart,
        "total" => $total
    ];
    array_unshift($orders, $newOrder); // Add to top

    // 4. Save to Files
    file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
    file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));

    echo json_encode(["status" => "success", "message" => "Order Placed Successfully", "order_id" => $newOrder['order_id']]);
    exit;
}

if ($action === 'update_product') {
    // Admin features: Edit Price/Stock
    $products = json_decode(file_get_contents($productsFile), true);
    foreach ($products as &$p) {
        if ($p['id'] == $input['id']) {
            $p['price'] = $input['price'];
            $p['stock'] = $input['stock'];
        }
    }
    file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
    echo json_encode(["status" => "success"]);
    exit;
}
?>