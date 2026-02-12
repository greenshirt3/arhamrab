<?php
// api.php - Fixed Login & Session Handling
// 1. Force Session settings before starting
ini_set('session.gc_maxlifetime', 86400); // Keep login for 24 hours
session_set_cookie_params(86400);
session_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to Database
require_once 'db_conn.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// --- DEBUG LOGIN HANDLER ---
if ($action == 'login') {
    // Try to read JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Fallback: If JSON failed, try standard POST (form data)
    if (!$input) {
        $input = $_POST;
    }

    $receivedPin = $input['pin'] ?? '';
    
    // PIN CHECK (Using == to allow "2733" string or 2733 number)
    if ($receivedPin == 2733) {
        $_SESSION['logged_in'] = true;
        echo json_encode(["status" => "success"]);
    } else {
        // ERROR: Send back what we received so you can see the problem
        echo json_encode([
            "status" => "error", 
            "message" => "Wrong PIN",
            "debug_received" => $receivedPin 
        ]);
    }
    exit;
}

// --- LOGOUT HANDLER ---
if ($action == 'logout') {
    session_destroy();
    echo json_encode(["status" => "success"]);
    exit;
}

// --- SECURITY CHECK FOR ALL OTHER ACTIONS ---
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403); 
    echo json_encode(["error" => "Unauthorized - Session Not Found"]);
    exit;
}

// ... (Rest of your code: GET items, dashboard, POST invoices) ...
// Copy the rest of the GET/POST logic from the previous file here
// OR simply paste the logic below:

if ($method === 'GET') {
    if ($action == 'items') {
        $fixed = $pdo->query("SELECT id, item_name as name, final_price as price, 'Fixed' as type FROM products_fixed")->fetchAll(PDO::FETCH_ASSOC);
        $flex  = $pdo->query("SELECT id, quality_name as name, sale_price_sqft as price, 'Flex' as type FROM rates_panaflex")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(array_merge($fixed, $flex));
    }
    elseif ($action == 'contacts') {
        $sql = "SELECT id, account_name as name, balance FROM fin_accounts 
                WHERE account_name NOT IN ('Shop Cash Drawer', 'Personal Wallet', 'Meezan Bank')";
        echo json_encode($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
    }
    elseif ($action == 'dashboard') {
        $today_sales = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE order_date = CURDATE()")->fetchColumn();
        $cash_hand = $pdo->query("SELECT balance FROM fin_accounts WHERE account_name = 'Shop Cash Drawer'")->fetchColumn();
        $home_exp = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM fin_transactions t 
                                JOIN fin_categories c ON t.category_id = c.id 
                                WHERE c.is_business = 0 AND MONTH(t.trans_date) = MONTH(CURDATE())")->fetchColumn();
        echo json_encode(["sales_today" => $today_sales, "home_expense" => abs($home_exp), "cash_hand" => $cash_hand]);
    }
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($action == 'save_invoice') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, total_amount, paid_amount, status) VALUES (?, ?, ?, ?)");
            $status = ($data['paid'] >= $data['total']) ? 'Completed' : 'Pending';
            $stmt->execute([$data['customer'], $data['total'], $data['paid'], $status]);
            $order_id = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, description, qty, rate, total) VALUES (?, ?, ?, ?, ?)");
            foreach ($data['items'] as $item) {
                $rate = $item['price'] / ($item['is_flex'] ? $item['sqft'] : $item['qty']);
                $qty_val = $item['is_flex'] ? 1 : $item['qty']; 
                $stmtItem->execute([$order_id, $item['desc'], $qty_val, $rate, $item['price']]);
            }

            if ($data['paid'] > 0) {
                $pdo->prepare("UPDATE fin_accounts SET balance = balance + ? WHERE account_name = 'Shop Cash Drawer'")->execute([$data['paid']]);
                $pdo->prepare("INSERT INTO fin_transactions (account_id, category_id, amount, description) VALUES ((SELECT id FROM fin_accounts WHERE account_name='Shop Cash Drawer'), 1, ?, ?)")->execute([$data['paid'], "Inv #$order_id"]);
            }
            
            $pdo->commit();
            echo json_encode(["status" => "success", "id" => $order_id]);
        } catch (Exception $e) { $pdo->rollBack(); echo json_encode(["error" => $e->getMessage()]); }
    }
    elseif ($action == 'save_expense') {
        // Use existing logic from previous code for save_expense
    }
}
?>