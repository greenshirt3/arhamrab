<?php
session_start();
include 'db.php'; // CONNECT TO SQL

// --- 1. LOGIN LOGIC (SQL) ---
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // Check SQL Database for Shop
    $stmt = $conn->prepare("SELECT * FROM shops WHERE id = ? AND password = ?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $shop = $res->fetch_assoc();
        $_SESSION['shop_logged_in'] = true;
        $_SESSION['shop_id'] = $shop['id'];
        $_SESSION['shop_name'] = $shop['name'];
        header("Location: shop_dashboard.php");
        exit;
    }
    $error = "Invalid Shop ID or Password";
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: shop_dashboard.php");
    exit;
}

// LOGIN SCREEN
if (!isset($_SESSION['shop_logged_in'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vendor Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; padding: 40px; border-radius: 15px; width: 100%; max-width: 400px; text-align: center; }
        .btn-brand { background: #F37021; color: white; width: 100%; padding: 10px; border:none; border-radius:5px;}
    </style>
</head>
<body>
    <div class="login-card">
        <h3>Partner Login</h3>
        <?php if(isset($error)) echo "<div class='text-danger mb-3'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" class="form-control mb-3" placeholder="Shop ID" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" name="login" class="btn-brand">LOGIN</button>
        </form>
    </div>
</body>
</html>
<?php exit; }

// --- 2. DASHBOARD DATA (SQL) ---
$shop_id = $_SESSION['shop_id'];

// Fetch Orders for THIS Shop only
$sql = "SELECT * FROM orders WHERE shop_id = '$shop_id' AND status != 'Delivered' ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display | <?php echo $_SESSION['shop_name']; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #eef2f6; }
        .top-nav { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .order-card { background: white; border-radius: 10px; overflow: hidden; margin-bottom: 20px; border-top: 5px solid #ccc; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .status-Pending { border-color: #F37021; }
        .status-Cooking { border-color: #3b82f6; }
        .status-Ready { border-color: #10b981; }
        .btn-action { width: 100%; padding: 10px; border: none; font-weight: bold; }
    </style>
</head>
<body>

    <audio id="orderSound" src="https://assets.mixkit.co/sfx/preview/mixkit-service-bell-ring-1461.mp3"></audio>

    <div class="top-nav">
        <h4 class="m-0"><i class="fas fa-store text-warning"></i> <?php echo $_SESSION['shop_name']; ?></h4>
        <a href="?logout=true" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <div class="container py-4">
        <div class="row">
            <?php 
            $count = 0;
            if ($result->num_rows > 0):
                while($order = $result->fetch_assoc()): 
                    $count++;
                    $items = json_decode($order['items'], true);
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="order-card status-<?php echo $order['status']; ?>">
                    <div class="p-3 border-bottom d-flex justify-content-between">
                        <span class="fw-bold">#<?php echo $order['id']; ?></span>
                        <span class="badge bg-secondary"><?php echo $order['status']; ?></span>
                    </div>
                    <div class="p-3">
                        <?php if(is_array($items)): foreach($items as $item): ?>
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?php echo $item['qty']; ?>x <?php echo $item['name']; ?></span>
                        </div>
                        <?php endforeach; endif; ?>
                        
                        <?php if(!empty($order['note'])): ?>
                            <div class="alert alert-warning p-2 small mt-2 mb-0"><?php echo $order['note']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($order['status'] == 'Pending'): ?>
                        <button class="btn-action bg-warning text-dark" onclick="updateStatus(<?php echo $order['id']; ?>, 'Cooking')">START COOKING</button>
                    <?php elseif($order['status'] == 'Cooking'): ?>
                        <button class="btn-action bg-success text-white" onclick="updateStatus(<?php echo $order['id']; ?>, 'Ready')">MARK READY</button>
                    <?php else: ?>
                        <button class="btn-action bg-light text-muted" disabled>WAITING RIDER</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            if(!confirm("Update order #" + id + "?")) return;
            fetch('api/update_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_status', order_id: id, status: status })
            }).then(r => r.json()).then(d => {
                if(d.status === 'success') location.reload();
            });
        }

        // Auto Refresh
        setInterval(() => {
            fetch('api/process.php', { method: 'POST', body: new URLSearchParams({action:'fetch_orders'}) })
            .then(r => r.json())
            .then(data => {
                const myOrders = data.filter(o => o.shop_id === "<?php echo $shop_id; ?>" && o.status !== 'Delivered');
                if(myOrders.length > <?php echo $count; ?>) {
                    document.getElementById('orderSound').play();
                    setTimeout(() => location.reload(), 2000);
                }
            });
        }, 5000);
    </script>
</body>
</html>