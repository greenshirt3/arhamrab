<?php
require_once '../auth_check.php';
requireRole(['admin', 'pharmacy']);

$inventory = getJSON(FILE_INVENTORY);

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_item = [
        'id' => $_POST['id'] ?: rand(1000,9999),
        'name' => $_POST['name'],
        'batch_no' => $_POST['batch_no'],
        'stock_qty' => (int)$_POST['stock_qty'],
        'unit_price' => (float)$_POST['unit_price'],
        'expiry_date' => $_POST['expiry_date']
    ];
    
    // Check if updating existing
    $found = false;
    foreach($inventory as &$item) {
        if($item['id'] == $new_item['id']) {
            $item = $new_item;
            $found = true;
            break;
        }
    }
    if(!$found) $inventory[] = $new_item;
    
    saveJSON(FILE_INVENTORY, $inventory);
    header("Location: inventory.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light p-5">
    
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Medicine Inventory</h2>
        <a href="dashboard.php" class="btn btn-outline-dark">Back to Dashboard</a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-panel p-4 bg-white sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-3">Add / Update Medicine</h5>
                <form method="POST">
                    <input type="hidden" name="id" value="">
                    <div class="mb-2">
                        <label>Medicine Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Batch No</label>
                        <input type="text" name="batch_no" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label>Stock Qty</label>
                            <input type="number" name="stock_qty" class="form-control" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label>Price (PKR)</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100 fw-bold">Save to Inventory</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="glass-panel p-4 bg-white">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Batch</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($inventory as $item): 
                            $bg = $item['stock_qty'] < 10 ? 'bg-danger text-white' : '';
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $item['name']; ?></td>
                            <td><?php echo $item['batch_no']; ?></td>
                            <td class="<?php echo $bg; ?> text-center rounded fw-bold"><?php echo $item['stock_qty']; ?></td>
                            <td><?php echo $item['unit_price']; ?></td>
                            <td><?php echo $item['expiry_date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>