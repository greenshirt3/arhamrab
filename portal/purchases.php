<?php 
require 'includes/header.php'; 

// 1. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$allowed_ids = [1, 14, 15]; 
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role']) === 'admin');

if (!$is_admin) { 
    die("<div class='alert alert-danger text-center m-5'>⛔ ACCESS DENIED: Only Admin can access Purchases.</div>"); 
}

// 2. HANDLE PURCHASE SUBMISSION
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_purchase'])) {
    $supplier_id = $_POST['supplier_id']; 
    $items = $_POST['item_name']; // Array
    $qtys = $_POST['qty']; // Array
    $costs = $_POST['cost']; // Array
    $paid = $_POST['paid_amount'];
    
    // Validate
    if (empty($supplier_id) || count($items) == 0) {
        $error = "Please select a supplier and add at least one item.";
    } else {
        $grand_total = 0;
        $inv_no = "PUR-" . date('ymd-His');
        $items_json = [];

        try {
            // LOOP THROUGH ITEMS
            for($i=0; $i < count($items); $i++) {
                if(empty($items[$i])) continue;
                
                $name = trim($items[$i]);
                $qty = (int)$qtys[$i];
                $cost = (float)$costs[$i];
                $row_total = $qty * $cost;
                $grand_total += $row_total;
                
                // Store for JSON record
                $items_json[] = ['name'=>$name, 'qty'=>$qty, 'cost'=>$cost];

                // A. Update Inventory
                // Check if item exists in inventory
                $chk = $pdo->prepare("SELECT id FROM inventory WHERE item_name = ?");
                $chk->execute([$name]);
                
                if($row = $chk->fetch()) {
                    // Update existing item: Add Stock, Update Purchase Price
                    $stmt = $pdo->prepare("UPDATE inventory SET stock_qty = stock_qty + ?, purchase_price = ? WHERE id=?");
                    $stmt->execute([$qty, $cost, $row['id']]);
                } else {
                    // Create New Item
                    // Auto-set Sale Price to Cost + 20% margin
                    $sale_price = $cost * 1.2;
                    $stmt = $pdo->prepare("INSERT INTO inventory (item_name, stock_qty, purchase_price, sale_price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $qty, $cost, $sale_price]);
                }
                
                // B. Add to Ledger (Detailed breakdown)
                $desc = "Bought $name x$qty";
                $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, invoice_no) VALUES (CURDATE(), 'purchase', 'Stock', ?, ?, ?)");
                $stmt->execute([$desc, $row_total, $inv_no]);
            }

            // C. Handle Financials
            // Fetch Supplier Name
            $sup_q = $pdo->query("SELECT person_name FROM loans WHERE id=$supplier_id")->fetch();
            $supplier_name = $sup_q['person_name'];
            
            // Deduct Cash (If Paid)
            if($paid > 0) {
                // Deduct from Drawer
                $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name='Shop Cash Drawer'")->execute([$paid]);
                
                // Ledger Entry for Payment
                $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, invoice_no) VALUES (CURDATE(), 'expense', 'Supplier Payment', ?, ?, 'Cash', ?)");
                $stmt->execute(["Paid to $supplier_name", $paid, $inv_no]);
            }

            // Update Supplier Balance (Debt)
            $due_amount = $grand_total - $paid;
            if($due_amount > 0) {
                // Increase the 'total_amount' we owe in the loans table
                $pdo->prepare("UPDATE loans SET total_amount = total_amount + ? WHERE id=?")->execute([$due_amount, $supplier_id]);
            }
            
            // D. Create Master Purchase Record
            $json_str = json_encode($items_json);
            $stmt = $pdo->prepare("INSERT INTO purchases (supplier_name, invoice_no, items_json, total_amount, paid_amount) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$supplier_name, $inv_no, $json_str, $grand_total, $paid]);

            $msg = "Purchase #$inv_no recorded successfully! Stock and Ledger updated.";

        } catch (Exception $e) {
            $error = "Error recording purchase: " . $e->getMessage();
        }
    }
}

// Fetch Suppliers for Dropdown
$suppliers = $pdo->query("SELECT * FROM loans WHERE type='taken' ORDER BY person_name ASC")->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold"><i class="fas fa-truck-loading text-primary"></i> New Purchase Order</h3>
    </div>
</div>

<?php if($msg): ?>
    <div class="alert alert-success fw-bold text-center"><?php echo $msg; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger fw-bold text-center"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="save_purchase" value="1">
    
    <div class="glass-panel p-4">
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="fw-bold text-muted small">Select Supplier</label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">-- Choose Supplier --</option>
                    <?php foreach($suppliers as $s): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['person_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="mt-2">
                    <a href="contacts.php" class="small text-decoration-none"><i class="fas fa-plus-circle"></i> Create New Supplier</a>
                </div>
            </div>
            <div class="col-md-4">
                <label class="fw-bold text-muted small">Date</label>
                <input type="text" class="form-control" value="<?php echo date('d M Y'); ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-bold text-muted small">Invoice #</label>
                <input type="text" class="form-control" value="Auto-Generated" readonly>
            </div>
        </div>

        <table class="table table-bordered mb-3" id="pTable">
            <thead class="bg-light">
                <tr>
                    <th>Item Name</th>
                    <th width="15%">Qty</th>
                    <th width="20%">Cost Price (Per Unit)</th>
                    <th width="20%">Total</th>
                    <th width="5%"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="item_name[]" class="form-control" placeholder="Product Name" required></td>
                    <td><input type="number" name="qty[]" class="form-control qty" oninput="calcRow(this)" required></td>
                    <td><input type="number" name="cost[]" class="form-control cost" oninput="calcRow(this)" required></td>
                    <td class="row-total fw-bold text-end pt-3">0.00</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); calcGrand();">x</button></td>
                </tr>
            </tbody>
        </table>
        
        <button type="button" class="btn btn-sm btn-secondary mb-4 rounded-pill px-3" onclick="addRow()">+ Add Another Item</button>

        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Grand Total:</span>
                    <span class="fw-bold fs-5">Rs. <span id="grand">0</span></span>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-success small">Amount Paid (Cash)</label>
                    <input type="number" name="paid_amount" class="form-control fw-bold border-success" value="0" required>
                    <small class="text-muted">Balance will be added to Supplier ledger.</small>
                </div>
                <button class="btn btn-primary w-100 fw-bold py-3 shadow">CONFIRM PURCHASE</button>
            </div>
        </div>
    </div>
</form>

<script>
function addRow() {
    let row = `<tr>
        <td><input type="text" name="item_name[]" class="form-control" placeholder="Product Name"></td>
        <td><input type="number" name="qty[]" class="form-control qty" oninput="calcRow(this)"></td>
        <td><input type="number" name="cost[]" class="form-control cost" oninput="calcRow(this)"></td>
        <td class="row-total fw-bold text-end pt-3">0.00</td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); calcGrand();">x</button></td>
    </tr>`;
    document.querySelector('#pTable tbody').insertAdjacentHTML('beforeend', row);
}

function calcRow(el) {
    let tr = el.closest('tr');
    let qty = parseFloat(tr.querySelector('.qty').value) || 0;
    let cost = parseFloat(tr.querySelector('.cost').value) || 0;
    let total = qty * cost;
    tr.querySelector('.row-total').innerText = total.toFixed(2);
    calcGrand();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.row-total').forEach(el => {
        grand += parseFloat(el.innerText);
    });
    document.getElementById('grand').innerText = grand.toLocaleString();
}
</script>
<?php include 'includes/footer.php'; ?>