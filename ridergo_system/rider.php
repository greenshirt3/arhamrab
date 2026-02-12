<?php
session_start();
include 'db.php'; // CONNECT TO STACKCP DATABASE

// ==========================================
// 1. AUTHENTICATION (SQL VERIFICATION)
// ==========================================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: rider.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $input_id = strtoupper(trim($_POST['rider_id']));
    $input_pin = trim($_POST['rider_pin']);

    // Check credentials against 'riders' table
    $stmt = $conn->prepare("SELECT * FROM riders WHERE id = ? AND pin = ?"); // Assuming 'id' is the column name in riders table
    $stmt->bind_param("ss", $input_id, $input_pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $rider = $result->fetch_assoc();
        $_SESSION['rider_logged_in'] = true;
        $_SESSION['rider_id'] = $rider['id'];
        $_SESSION['rider_name'] = $rider['name'];
        header("Location: rider.php");
        exit;
    } else {
        $error = "Invalid Badge ID or PIN";
    }
}

// LOGIN SCREEN
if (!isset($_SESSION['rider_logged_in'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Rider Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #111827; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #1f2937; padding: 30px; border-radius: 15px; width: 90%; max-width: 350px; text-align: center; color: white; }
        .btn-brand { background: #F37021; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: bold; }
        .form-control { background: #374151; border: none; color: white; padding: 12px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-card">
        <i class="fas fa-motorcycle fa-3x mb-3" style="color:#F37021"></i>
        <h3 class="mb-4">Fleet Login</h3>
        <?php if($error): ?><div class="alert alert-danger p-2 small"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="rider_id" class="form-control" placeholder="Badge ID (e.g. R01)" required>
            <input type="password" name="rider_pin" class="form-control" placeholder="PIN" required inputmode="numeric">
            <button type="submit" name="login" class="btn-brand">START SHIFT</button>
        </form>
    </div>
</body>
</html>
<?php exit; }

// ==========================================
// 2. FETCH ORDERS (SMART SQL JOIN)
// ==========================================
$rider_id = $_SESSION['rider_id'];

// We JOIN 'orders' with 'shops' to get the Shop Name nicely
$sql = "SELECT orders.*, shops.name as shop_name, shops.phone as shop_phone 
        FROM orders 
        LEFT JOIN shops ON orders.shop_id = shops.id 
        WHERE orders.rider_id = ? AND orders.status != 'Delivered' 
        ORDER BY orders.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rider_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Dashboard | <?php echo $rider_id; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f3f4f6; padding-bottom: 80px; font-family: sans-serif; }
        .top-nav { background: #111827; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .order-card { background: white; border-radius: 12px; padding: 15px; margin: 15px; border-left: 5px solid #ccc; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .order-card.Assigned { border-left-color: #F37021; }
        .order-card.Picked { border-left-color: #10B981; }
        .btn-action { width: 100%; padding: 12px; border-radius: 8px; font-weight: bold; border: none; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="top-nav">
        <h5 class="m-0 fw-bold"><i class="fas fa-id-badge me-2"></i><?php echo $_SESSION['rider_name']; ?></h5>
        <a href="?logout=true" class="text-white"><i class="fas fa-power-off"></i></a>
    </div>

    <div class="bg-white m-3 p-3 rounded-3 shadow-sm d-flex justify-content-between align-items-center">
        <div id="gps-status" class="fw-bold text-muted">🔴 GPS Offline</div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="gpsToggle" onchange="toggleGPS()" style="transform: scale(1.3);">
        </div>
    </div>

    <h6 class="ms-3 text-uppercase text-muted small fw-bold">Active Deliveries</h6>

    <?php 
    if ($result->num_rows > 0):
        while($order = $result->fetch_assoc()): 
            // Decode items (stored as JSON text in SQL)
            $items = json_decode($order['items'], true);
            $address = $order['address'] ?? 'No address provided'; // New SQL Column
            $map_query = $order['lat'] ? "{$order['lat']},{$order['lng']}" : urlencode($address);
    ?>
        <div class="order-card <?php echo $order['status']; ?>">
            <div class="d-flex justify-content-between mb-2">
                <span class="badge bg-dark">#<?php echo $order['id']; ?></span>
                <span class="fw-bold text-primary"><?php echo $order['status']; ?></span>
            </div>
            
            <h5 class="fw-bold m-0"><?php echo $order['shop_name'] ?? 'Unknown Shop'; ?></h5>
            <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo $address; ?></p>

            <div class="bg-light p-2 rounded mb-2 small">
                <?php if(is_array($items)): foreach($items as $item): ?>
                    <div><?php echo $item['qty']; ?>x <?php echo $item['name']; ?></div>
                <?php endforeach; endif; ?>
                <div class="border-top mt-2 pt-1 fw-bold d-flex justify-content-between">
                    <span>Collect Cash:</span>
                    <span class="text-danger">Rs. <?php echo number_format($order['total']); ?></span>
                </div>
            </div>

            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $map_query; ?>" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                <i class="fas fa-location-arrow"></i> Navigate
            </a>

            <?php if($order['status'] == 'Assigned'): ?>
                <button class="btn-action btn-warning" onclick="updateStatus(<?php echo $order['id']; ?>, 'Picked')">
                    <i class="fas fa-box"></i> PICK UP ORDER
                </button>
            <?php elseif($order['status'] == 'Picked'): ?>
                <button class="btn-action btn-success text-white" onclick="updateStatus(<?php echo $order['id']; ?>, 'Delivered')">
                    <i class="fas fa-check"></i> MARK DELIVERED
                </button>
            <?php endif; ?>
        </div>

    <?php endwhile; else: ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i><br>
            All caught up! Wait for tasks.
        </div>
    <?php endif; ?>

    <script>
        // Use the same JS as before, but ensure update_order.php is also SQL-ready!
        function updateStatus(id, status) {
            if(!confirm("Update status to " + status + "?")) return;
            
            fetch('api/update_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_status', order_id: id, status: status })
            }).then(r => r.json()).then(d => {
                if(d.status === 'success') location.reload();
                else alert("Error: " + d.message);
            });
        }
        
        // GPS Logic (Same as before)
        const riderId = "<?php echo $rider_id; ?>";
        let gpsTimer;
        function toggleGPS() {
            if(document.getElementById('gpsToggle').checked) {
                document.getElementById('gps-status').innerHTML = '<span class="text-success">🟢 GPS Live</span>';
                sendLoc(); gpsTimer = setInterval(sendLoc, 10000);
            } else {
                document.getElementById('gps-status').innerHTML = '🔴 GPS Offline';
                clearInterval(gpsTimer);
            }
        }
        function sendLoc() {
            navigator.geolocation.getCurrentPosition(pos => {
                fetch('api/update_location.php', {
                    method:'POST', headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({ rider_id: riderId, lat: pos.coords.latitude, lng: pos.coords.longitude })
                });
            });
        }
    </script>
</body>
</html>