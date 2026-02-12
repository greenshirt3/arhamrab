<?php 
// 1. ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/header.php'; 

// 2. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Master Admin List (IDs from your SQL Dump)
$allowed_ids = [1, 14, 15]; 
$is_admin = false;

if (in_array($_SESSION['user_id'], $allowed_ids)) {
    $is_admin = true;
} elseif (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin') {
    $is_admin = true;
}

if (!$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED: Only Admins can manage contacts.</div></div>");
}

// 3. HANDLE FORM ACTIONS
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. ADD OR EDIT CONTACT
    if (isset($_POST['save_contact'])) {
        $id = $_POST['contact_id'];
        $name = trim($_POST['name']);
        $phone = $_POST['phone'];
        $addr = $_POST['address'];
        $type = $_POST['type']; // 'given' (Customer) or 'taken' (Supplier)
        $initial_balance = $_POST['initial_balance']; 

        // Validate
        if (empty($name)) {
            $error = "Name is required.";
        } else {
            try {
                if ($id) {
                    // Update Existing Contact
                    // Note: We do NOT update initial_balance on edit to prevent messing up ledgers
                    $stmt = $pdo->prepare("UPDATE loans SET person_name=?, phone=?, address=?, type=? WHERE id=?");
                    $stmt->execute([$name, $phone, $addr, $type, $id]);
                    $msg = "Contact details updated successfully!";
                } else {
                    // Create New Contact
                    $stmt = $pdo->prepare("INSERT INTO loans (person_name, phone, address, type, total_amount, paid_amount, status) VALUES (?, ?, ?, ?, ?, 0, 'active')");
                    // Initial balance goes into 'total_amount' (Debt)
                    $stmt->execute([$name, $phone, $addr, $type, $initial_balance]);
                    $msg = "New Contact added successfully!";
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }

    // B. DELETE CONTACT
    if (isset($_POST['delete_contact'])) {
        $id = $_POST['delete_id'];
        try {
            $pdo->prepare("DELETE FROM loans WHERE id=?")->execute([$id]);
            $msg = "Contact deleted permanently.";
        } catch (PDOException $e) {
            $error = "Could not delete contact: " . $e->getMessage();
        }
    }

    // C. RECORD PAYMENT (Receive Money OR Pay Money)
    if (isset($_POST['add_payment'])) {
        $id = $_POST['pay_id'];
        $amount = $_POST['amount'];
        $note = $_POST['note'];
        
        if ($amount > 0) {
            try {
                // 1. Fetch Contact Info to know if it's Customer or Supplier
                $stmt = $pdo->prepare("SELECT * FROM loans WHERE id = ?");
                $stmt->execute([$id]);
                $contact = $stmt->fetch();

                if ($contact) {
                    // 2. Update Loan Record (Increase 'paid_amount')
                    $pdo->prepare("UPDATE loans SET paid_amount = paid_amount + ? WHERE id=?")->execute([$amount, $id]);
                    
                    // 3. Ledger Logic
                    // If type is 'given' (Customer), we received money -> Income
                    // If type is 'taken' (Supplier), we paid money -> Expense
                    $ledger_type = ($contact['type'] == 'given') ? 'income' : 'expense'; 
                    $description = "Payment: " . $contact['person_name'] . " - " . $note;
                    
                    $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head) VALUES (CURDATE(), ?, 'Payment', ?, ?, 'Cash', 'Shop Cash Drawer')");
                    $stmt->execute([$ledger_type, $description, $amount]);
                    
                    // 4. Update Cash Drawer Balance
                    if ($ledger_type == 'income') {
                        $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name='Shop Cash Drawer'")->execute([$amount]);
                    } else {
                        $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name='Shop Cash Drawer'")->execute([$amount]);
                    }
                    $msg = "Payment of Rs. $amount recorded successfully!";
                }
            } catch (PDOException $e) {
                $error = "Payment Error: " . $e->getMessage();
            }
        } else {
            $error = "Please enter a valid amount.";
        }
    }
}

// 4. FETCH DATA
$customers = $pdo->query("SELECT * FROM loans WHERE type='given' ORDER BY person_name ASC")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM loans WHERE type='taken' ORDER BY person_name ASC")->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold"><i class="fas fa-address-book text-primary"></i> Contact Manager</h3>
        <p class="text-muted">Manage Customers (Receivables) and Suppliers (Payables)</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-success rounded-pill fw-bold px-4 shadow me-2" onclick="openModal('given')">
            <i class="fas fa-user-plus me-2"></i> Add Customer
        </button>
        <button class="btn btn-warning rounded-pill fw-bold px-4 shadow" onclick="openModal('taken')">
            <i class="fas fa-truck me-2"></i> Add Supplier
        </button>
    </div>
</div>

<?php if($msg): ?>
    <div class="alert alert-success fw-bold shadow-sm border-0"><i class="fas fa-check-circle me-2"></i> <?php echo $msg; ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger fw-bold shadow-sm border-0"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3 border-bottom-0" id="myTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold border" id="cust-tab" data-bs-toggle="tab" data-bs-target="#cust" type="button">
            <i class="fas fa-users me-2"></i> Customers (Receivables)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold border ms-2" id="sup-tab" data-bs-toggle="tab" data-bs-target="#sup" type="button">
            <i class="fas fa-building me-2"></i> Suppliers (Payables)
        </button>
    </li>
</ul>

<div class="tab-content glass-panel p-0 overflow-hidden">
    
    <div class="tab-pane fade show active" id="cust">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Balance Due</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($customers as $c): 
                        $bal = $c['total_amount'] - $c['paid_amount']; ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?php echo htmlspecialchars($c['person_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['phone']); ?></td>
                        <td class="small text-muted"><?php echo htmlspecialchars($c['address']); ?></td>
                        <td>
                            <?php if($bal > 0): ?>
                                <span class="badge bg-danger fs-6">Rs. <?php echo number_format($bal); ?></span>
                            <?php else: ?>
                                <span class="badge bg-success">Cleared</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-success fw-bold me-1" onclick='payModal(<?php echo json_encode($c); ?>)'>Receive Pay</button>
                            <button class="btn btn-sm btn-outline-primary" onclick='editContact(<?php echo json_encode($c); ?>)'>Edit</button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this contact permanently?');">
                                <input type="hidden" name="delete_contact" value="1">
                                <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger ms-1"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="sup">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>We Owe</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($suppliers as $s): 
                        $bal = $s['total_amount'] - $s['paid_amount']; ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?php echo htmlspecialchars($s['person_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['phone']); ?></td>
                        <td class="small text-muted"><?php echo htmlspecialchars($s['address']); ?></td>
                        <td>
                            <?php if($bal > 0): ?>
                                <span class="badge bg-danger fs-6">Rs. <?php echo number_format($bal); ?></span>
                            <?php else: ?>
                                <span class="badge bg-success">Cleared</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-warning fw-bold me-1" onclick='payModal(<?php echo json_encode($s); ?>)'>Pay Supplier</button>
                            <button class="btn btn-sm btn-outline-primary" onclick='editContact(<?php echo json_encode($s); ?>)'>Edit</button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this contact permanently?');">
                                <input type="hidden" name="delete_contact" value="1">
                                <input type="hidden" name="delete_id" value="<?php echo $s['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger ms-1"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="mTitle">Add Contact</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="save_contact" value="1">
                    <input type="hidden" name="contact_id" id="c_id">
                    <input type="hidden" name="type" id="c_type">
                    
                    <div class="mb-3">
                        <label class="fw-bold small">Name / Business Name</label>
                        <input type="text" name="name" id="c_name" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="fw-bold small">Phone</label>
                            <input type="text" name="phone" id="c_phone" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="fw-bold small">Opening Balance (Due)</label>
                            <input type="number" name="initial_balance" id="c_bal" class="form-control" value="0">
                            <small class="text-muted">Enter current debt if any</small>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small">Address</label>
                        <input type="text" name="address" id="c_addr" class="form-control">
                    </div>
                    
                    <button class="btn btn-primary w-100 fw-bold rounded-pill">Save Contact</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Record Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="add_payment" value="1">
                    <input type="hidden" name="pay_id" id="p_id">
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold" id="p_name"></h4>
                        <small class="text-muted">Enter the amount transacted</small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-success">Amount</label>
                        <input type="number" name="amount" class="form-control form-control-lg fw-bold border-success" required>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small">Note</label>
                        <input type="text" name="note" class="form-control" placeholder="e.g. Cash Handover, Bank Transfer">
                    </div>
                    <button class="btn btn-success w-100 fw-bold rounded-pill">Confirm Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var cModal = new bootstrap.Modal(document.getElementById('contactModal'));
var pModal = new bootstrap.Modal(document.getElementById('payModal'));

function openModal(type) {
    // Clear fields
    document.getElementById('c_id').value = '';
    document.getElementById('c_name').value = '';
    document.getElementById('c_phone').value = '';
    document.getElementById('c_addr').value = '';
    document.getElementById('c_bal').value = '0';
    document.getElementById('c_bal').disabled = false; // Enable for new
    document.getElementById('c_type').value = type;
    
    // Set Title
    document.getElementById('mTitle').innerText = (type=='given') ? "Add New Customer" : "Add New Supplier";
    cModal.show();
}

function editContact(c) {
    // Fill fields
    document.getElementById('c_id').value = c.id;
    document.getElementById('c_name').value = c.person_name;
    document.getElementById('c_phone').value = c.phone;
    document.getElementById('c_addr').value = c.address;
    document.getElementById('c_type').value = c.type;
    document.getElementById('c_bal').value = c.total_amount; 
    document.getElementById('c_bal').disabled = true; // Protect balance from edit, force payment instead
    document.getElementById('mTitle').innerText = "Edit Contact";
    cModal.show();
}

function payModal(c) {
    document.getElementById('p_id').value = c.id;
    document.getElementById('p_name').innerText = c.person_name;
    pModal.show();
}
</script>
<?php include 'includes/footer.php'; ?>