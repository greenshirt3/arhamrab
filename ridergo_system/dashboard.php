<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiderGo Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .order-card { padding:15px; border:1px solid #ddd; margin:10px; background:white; border-radius:8px; }
        .status-btn { padding:5px 10px; cursor:pointer; margin-right:5px; }
    </style>
</head>
<body style="background:#f0f2f5;">

    <?php $role = $_GET['role'] ?? 'admin'; ?>
    
    <div class="app-header" style="background:#333;">
        <div class="brand">RiderGo Panel: <?php echo strtoupper($role); ?></div>
        <a href="index.php" style="color:white;">Logout</a>
    </div>

    <div id="orders-list" style="padding:10px;">Loading Orders...</div>

    <script>
        const role = "<?php echo $role; ?>";
        
        // Auto-Refresh Every 5 Seconds (Real-Time Simulation)
        setInterval(loadOrders, 5000);
        loadOrders();

        function loadOrders() {
            const formData = new FormData();
            formData.append('action', 'fetch_orders');

            fetch('api/process.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(orders => {
                const container = document.getElementById('orders-list');
                container.innerHTML = '';

                orders.forEach(order => {
                    let actions = '';
                    
                    // LOGIC: Who sees what buttons?
                    if (role === 'shop' && order.status === 'pending') {
                        actions = `<button class="status-btn" style="background:orange" onclick="setStatus(${order.id}, 'cooking')">Accept & Cook</button>`;
                    } else if (role === 'shop' && order.status === 'cooking') {
                        actions = `<button class="status-btn" style="background:green; color:white;" onclick="setStatus(${order.id}, 'ready')">Mark Ready</button>`;
                    } else if (role === 'rider' && order.status === 'ready') {
                        actions = `<button class="status-btn" style="background:purple; color:white;" onclick="setStatus(${order.id}, 'picked')">Pick Up</button>`;
                    } else if (role === 'rider' && order.status === 'picked') {
                        actions = `<button class="status-btn" style="background:blue; color:white;" onclick="setStatus(${order.id}, 'delivered')">Complete Delivery</button>`;
                    }

                    // Only show relevant orders
                    if (order.status !== 'delivered' || role === 'admin') {
                        const items = order.items.map(i => i.name).join(', ');
                        const html = `
                        <div class="order-card">
                            <div style="display:flex; justify-content:space-between;">
                                <b>#${order.id}</b>
                                <span style="font-weight:bold; color:var(--primary)">${order.status.toUpperCase()}</span>
                            </div>
                            <p>${items}</p>
                            <p><b>Total: Rs. ${order.total}</b></p>
                            <div style="margin-top:10px;">${actions}</div>
                        </div>`;
                        container.innerHTML += html;
                    }
                });
            });
        }

        function setStatus(id, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('order_id', id);
            formData.append('status', status);
            if(role === 'rider') formData.append('rider', 'Rider1'); // Simulating logged in rider

            fetch('api/process.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(() => loadOrders()); // Refresh instantly
        }
    </script>
</body>
</html>