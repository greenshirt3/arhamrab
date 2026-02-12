<?php 
require_once 'includes/config.php';

// 1. AUTH & PERMISSIONS
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (!has_perm('shop') && !has_perm('admin')) { die("Access Denied"); }

// 2. HANDLE AJAX INVOICE SAVING
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_invoice') {
    ob_clean();
    header('Content-Type: application/json');
    
    $items = json_decode($_POST['items'], true);
    $total = (float)$_POST['total'];
    $paid = (float)$_POST['paid'];
    $customer = $_POST['customer_name'] ?: 'Walk-in';
    
    try {
        $pdo->beginTransaction();
        $inv_no = "AP-" . date('ymd') . rand(100, 999);

        // A. Insert Master Invoice
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, customer_name, total_amount, grand_total, paid_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$inv_no, $customer, $total, $total, $paid]);
        $inv_id = $pdo->lastInsertId();

        // B. Insert Specific Job Items
        $itemStmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, item_name, width, height, sq_ft, qty, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $itemStmt->execute([$inv_id, $item['name'], $item['w'], $item['h'], $item['sqft'], $item['qty'], $item['price'], $item['total']]);
        }

        // C. Financial Ledger Record
        $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, account_head) VALUES (CURDATE(), 'income', 'Printing Sale', ?, ?, 'Shop Cash Drawer')")->execute(["Invoice #$inv_no - $customer", $paid]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'inv_no' => $inv_no]);
    } catch (Exception $e) { $pdo->rollBack(); echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ARHAM PRINTERS - Professional POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; overflow: hidden; display: flex; flex-direction: column; }
        .pos-header { background: #1a1a1a; color: #00E5FF; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #00E5FF; }
        .main-container { display: flex; flex: 1; overflow: hidden; }
        .job-entry-side { flex: 4; padding: 20px; border-right: 1px solid #ddd; background: #fff; overflow-y: auto; }
        .invoice-side { flex: 3; display: flex; flex-direction: column; background: #fff; }
        .cart-table { flex: 1; overflow-y: auto; padding: 10px; }
        .calc-box { padding: 20px; background: #212529; color: white; }
        .btn-cyan { background: #00E5FF; color: #000; font-weight: bold; }
        .btn-cyan:hover { background: #00b8cc; }
    </style>
</head>
<body>

<div class="pos-header">
    <h4 class="m-0 font-monospace fw-bold">ARHAM PRINTERS POS</h4>
    <div>
        <a href="dashboard.php" class="btn btn-sm btn-outline-info me-2">Dashboard</a>
        <span class="badge bg-info text-dark">User: <?php echo $_SESSION['username']; ?></span>
    </div>
</div>

<div class="main-container">
    <div class="job-entry-side">
        <h5 class="fw-bold mb-3">Add Printing Job</h5>
        <div class="row g-3">
            <div class="col-12">
                <label class="small fw-bold">Job Title (e.g. Panaflex, Visiting Card)</label>
                <input type="text" id="job_title" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">Width (ft)</label>
                <input type="number" id="w" class="form-control" value="0" oninput="calcSqft()">
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">Height (ft)</label>
                <input type="number" id="h" class="form-control" value="0" oninput="calcSqft()">
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">Sq. Ft</label>
                <input type="number" id="sqft" class="form-control bg-light" readonly>
            </div>
            <div class="col-6">
                <label class="small fw-bold">Quantity</label>
                <input type="number" id="qty" class="form-control" value="1">
            </div>
            <div class="col-6">
                <label class="small fw-bold">Rate (Per Unit/Sqft)</label>
                <input type="number" id="rate" class="form-control">
            </div>
            <div class="col-12">
                <button class="btn btn-cyan w-100 py-2" onclick="addToInvoice()">ADD TO INVOICE <i class="fas fa-plus ms-2"></i></button>
            </div>
        </div>

        <hr class="my-4">
        <h5 class="fw-bold">Quick Design Charges</h5>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-dark" onclick="addFixed('Logo Design', 500)">Logo (500)</button>
            <button class="btn btn-outline-dark" onclick="addFixed('Urgent Design', 200)">Urgent (200)</button>
            <button class="btn btn-outline-dark" onclick="addFixed('Composition', 100)">Comp (100)</button>
        </div>
    </div>

    <div class="invoice-side">
        <div class="p-3 bg-light border-bottom">
            <input type="text" id="cust_name" class="form-control" placeholder="Customer Name (Mobile No)">
        </div>
        <div class="cart-table">
            <table class="table table-sm">
                <thead><tr class="small text-muted"><th>ITEM</th><th>DETAILS</th><th class="text-end">TOTAL</th></tr></thead>
                <tbody id="cartItems"></tbody>
            </table>
        </div>

        <div class="calc-box">
            <div class="d-flex justify-content-between fs-3 mb-2">
                <span>TOTAL</span>
                <span id="grandTotal">Rs. 0</span>
            </div>
            <div class="mb-3">
                <label class="small opacity-75">Paid Amount</label>
                <input type="number" id="paidAmt" class="form-control form-control-lg bg-dark text-white border-info text-end" oninput="calcChange()">
            </div>
            <div class="d-flex justify-content-between mb-3 text-info">
                <span>Balance:</span>
                <span id="balanceTxt">Rs. 0</span>
            </div>
            <button class="btn btn-cyan w-100 py-3 fs-5" onclick="saveInvoice()">SAVE & PRINT <i class="fas fa-print ms-2"></i></button>
        </div>
    </div>
</div>

<script>
let items = [];

function calcSqft() {
    let w = parseFloat(document.getElementById('w').value) || 0;
    let h = parseFloat(document.getElementById('h').value) || 0;
    document.getElementById('sqft').value = (w * h).toFixed(2);
}

function addToInvoice() {
    let name = document.getElementById('job_title').value;
    let w = parseFloat(document.getElementById('w').value);
    let h = parseFloat(document.getElementById('h').value);
    let sqft = parseFloat(document.getElementById('sqft').value);
    let qty = parseInt(document.getElementById('qty').value);
    let price = parseFloat(document.getElementById('rate').value);

    if(!name || !price) return alert("Enter Title and Rate");

    let total = (sqft > 0) ? (sqft * qty * price) : (qty * price);
    items.push({ name, w, h, sqft, qty, price, total });
    render();
}

function addFixed(name, price) {
    items.push({ name, w:0, h:0, sqft:0, qty:1, price, total: price });
    render();
}

function render() {
    let h = ''; let total = 0;
    items.forEach((it, idx) => {
        total += it.total;
        let details = (it.sqft > 0) ? `${it.w}x${it.h} (${it.sqft}ft) x ${it.qty}` : `Qty: ${it.qty}`;
        h += `<tr>
            <td><b>${it.name}</b></td>
            <td class="small">${details} @ ${it.price}</td>
            <td class="text-end"><b>${it.total.toLocaleString()}</b></td>
            <td><button class="btn btn-link text-danger p-0" onclick="items.splice(${idx},1);render()"><i class="fas fa-times"></i></button></td>
        </tr>`;
    });
    document.getElementById('cartItems').innerHTML = h;
    document.getElementById('grandTotal').innerText = "Rs. " + total.toLocaleString();
    calcChange();
}

function calcChange() {
    let total = items.reduce((a, b) => a + b.total, 0);
    let paid = parseFloat(document.getElementById('paidAmt').value) || 0;
    document.getElementById('balanceTxt').innerText = "Rs. " + (total - paid).toLocaleString();
}

function saveInvoice() {
    if(items.length === 0) return alert("Invoice is empty");
    let total = items.reduce((a, b) => a + b.total, 0);
    let paid = parseFloat(document.getElementById('paidAmt').value) || 0;
    let customer = document.getElementById('cust_name').value;

    let fd = new FormData();
    fd.append('action', 'save_invoice');
    fd.append('items', JSON.stringify(items));
    fd.append('total', total);
    fd.append('paid', paid);
    fd.append('customer_name', customer);

    fetch('shop_pos.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        if(d.status === 'success') {
            alert("Invoice Saved: " + d.inv_no);
            window.location.href = 'invoice.php?id=' + d.inv_no; // Direct to print page
        } else {
            alert(d.message);
        }
    });
}
</script>
</body>
</html>