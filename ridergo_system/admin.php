<?php
session_start();
include 'db.php'; // CONNECT TO SQL

// --- AUTH ---
$ADMIN_USER = 'admin'; $ADMIN_PASS = 'ridergo';
if (isset($_GET['logout'])) { session_destroy(); header("Location: admin.php"); exit; }
if (isset($_POST['login'])) {
    if ($_POST['username'] === $ADMIN_USER && $_POST['password'] === $ADMIN_PASS) $_SESSION['admin_logged_in'] = true;
}
if (!isset($_SESSION['admin_logged_in'])) { include 'views/login_screen.php'; exit; }

// --- FETCH DATA FROM SQL ---
// 1. Shops
$shops = [];
$r = $conn->query("SELECT * FROM shops");
while($row = $r->fetch_assoc()) $shops[] = $row;

// 2. Riders
$riders = [];
$r = $conn->query("SELECT * FROM riders");
while($row = $r->fetch_assoc()) $riders[] = $row;

// 3. Orders
$orders = [];
$r = $conn->query("SELECT orders.*, shops.name as shop_name FROM orders LEFT JOIN shops ON orders.shop_id = shops.id ORDER BY id DESC");
while($row = $r->fetch_assoc()) $orders[] = $row;

// Stats
$revenue = 0;
$pending = []; $assigned = [];
foreach($orders as $o) {
    $revenue += $o['total'];
    if(in_array($o['status'], ['Pending','Cooking','Ready'])) $pending[] = $o;
    if(in_array($o['status'], ['Assigned','Picked'])) $assigned[] = $o;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel | RiderGo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background: #f1f5f9; }
        .sidebar { width: 250px; position: fixed; height: 100vh; background: #1e293b; color: white; padding: 20px; }
        .main { margin-left: 250px; padding: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .map-box { height: 400px; border-radius: 10px; overflow: hidden; }
        .order-item { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #F37021; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>RiderGo</h3>
        <hr>
        <p><i class="fas fa-chart-pie"></i> Dashboard</p>
        <p><i class="fas fa-motorcycle"></i> Riders (<?php echo count($riders); ?>)</p>
        <p><i class="fas fa-store"></i> Shops (<?php echo count($shops); ?>)</p>
        <a href="?logout=true" class="text-white mt-5 d-block">Logout</a>
    </div>

    <div class="main">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <small>TOTAL REVENUE</small>
                    <h3>Rs. <?php echo number_format($revenue); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <small>PENDING ORDERS</small>
                    <h3 class="text-danger"><?php echo count($pending); ?></h3>
                </div>
            </div>
             <div class="col-md-3">
                <div class="stat-card">
                    <small>ACTIVE RIDERS</small>
                    <h3 class="text-success"><?php echo count($riders); ?></h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card p-3 shadow-sm mb-4">
                    <h5>Live Fleet Map</h5>
                    <div id="map" class="map-box"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 shadow-sm" style="height: 450px; overflow-y: auto;">
                    <h5>Kitchen Queue</h5>
                    <?php foreach($pending as $o): ?>
                        <div class="order-item">
                            <div class="d-flex justify-content-between">
                                <strong>#<?php echo $o['id']; ?></strong>
                                <span class="badge bg-warning text-dark"><?php echo $o['status']; ?></span>
                            </div>
                            <small><?php echo $o['shop_name']; ?></small><br>
                            <?php if($o['status'] == 'Ready'): ?>
                                <button class="btn btn-sm btn-success w-100 mt-2" onclick="assignRider(<?php echo $o['id']; ?>)">Assign Rider</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-light w-100 mt-2" disabled>Wait for Ready</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // MAP
        const map = L.map('map').setView([32.6384, 74.2040], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Add Riders from SQL
        const riders = <?php echo json_encode($riders); ?>;
        riders.forEach(r => {
            if(r.lat && r.lng) {
                L.marker([r.lat, r.lng]).addTo(map).bindPopup("Rider: " + r.name);
            }
        });

        // Add Shops from SQL
        const shops = <?php echo json_encode($shops); ?>;
        shops.forEach(s => {
            if(s.lat && s.lng) {
                L.circleMarker([s.lat, s.lng], {color: 'red', radius: 8}).addTo(map).bindPopup(s.name);
            }
        });

        // ASSIGN RIDER FUNCTION
        function assignRider(id) {
            let rider = prompt("Enter Rider ID (e.g. R01):");
            if(rider) {
                fetch('api/update_order.php', {
                    method:'POST', headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({action:'assign_rider', order_id:id, rider_id:rider})
                }).then(r=>r.json()).then(d=>{
                    if(d.status==='success') location.reload();
                    else alert(d.message);
                });
            }
        }
    </script>
</body>
</html>