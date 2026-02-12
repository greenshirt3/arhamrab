<?php
$order_id = $_GET['id'] ?? '';
$order = null;

if($order_id) {
    $orders = json_decode(file_get_contents('../data/orders.json'), true);
    foreach($orders as $o) {
        if($o['id'] == $order_id) {
            $order = $o;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order | RiderGo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: sans-serif; }
        .track-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        .status-step { display: flex; align-items: center; margin-bottom: 20px; color: #ccc; }
        .status-step.active { color: #F37021; font-weight: bold; }
        .status-icon { width: 40px; height: 40px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
        .active .status-icon { background: #F37021; color: white; }
    </style>
</head>
<body>

    <div class="track-card">
        <h3 class="fw-bold text-center mb-4">Track Your Order</h3>

        <form class="d-flex gap-2 mb-4">
            <input type="text" name="id" class="form-control" placeholder="Enter Order ID (e.g. 10502)" value="<?php echo $order_id; ?>" required>
            <button class="btn btn-dark">Track</button>
        </form>

        <?php if($order): ?>
            <div class="alert alert-light border">
                <strong>Shop:</strong> <?php echo $order['shop_name']; ?><br>
                <strong>Total:</strong> Rs. <?php echo $order['total']; ?><br>
                <strong>Items:</strong> <?php echo count($order['items']); ?>
            </div>

            <div class="mt-4">
                <?php 
                    $s = $order['status']; 
                    $step = 1;
                    if($s == 'Cooking' || $s == 'Ready') $step = 2;
                    if($s == 'Assigned' || $s == 'Picked') $step = 3;
                    if($s == 'Delivered') $step = 4;
                ?>
                
                <div class="status-step <?php echo $step >= 1 ? 'active' : ''; ?>">
                    <div class="status-icon"><i class="fas fa-receipt"></i></div> 
                    Order Received
                </div>
                <div class="status-step <?php echo $step >= 2 ? 'active' : ''; ?>">
                    <div class="status-icon"><i class="fas fa-fire"></i></div> 
                    Preparing
                </div>
                <div class="status-step <?php echo $step >= 3 ? 'active' : ''; ?>">
                    <div class="status-icon"><i class="fas fa-motorcycle"></i></div> 
                    Rider on the way
                </div>
                <div class="status-step <?php echo $step >= 4 ? 'active' : ''; ?>">
                    <div class="status-icon"><i class="fas fa-check"></i></div> 
                    Delivered
                </div>
            </div>
            
            <?php if($step == 3): ?>
                <div class="alert alert-info mt-3"><i class="fas fa-map-marker-alt"></i> Rider is on the way to you!</div>
            <?php endif; ?>

        <?php elseif($order_id): ?>
            <div class="alert alert-danger">Order not found. Please check ID.</div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="text-decoration-none text-muted">← Back to Home</a>
        </div>
    </div>

</body>
</html>