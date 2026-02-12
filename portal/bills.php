<?php 
// 1. INIT
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'includes/header.php'; 

// 2. CHECK PERMISSIONS
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Allow Admin OR HBL Agent
$can_access = (function_exists('has_perm') && (has_perm('admin') || has_perm('hbl')));
if (!$can_access) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED: You do not have permission to add bills.</div></div>");
}

// 3. HANDLE FORM SUBMIT
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['bill_type'];
    $consumer = trim($_POST['consumer_number']);
    $amount = (float)str_replace(',', '', $_POST['amount']); // Remove commas
    $mobile = trim($_POST['mobile_no']);
    $name = trim($_POST['consumer_name']);

    if (empty($consumer) || $amount <= 0) {
        $error = "Please enter valid consumer number and amount.";
    } else {
        try {
            // Check for duplicate in pending queue
            $stmt = $pdo->prepare("SELECT id FROM bill_queue WHERE consumer_number = ? AND status = 'pending'");
            $stmt->execute([$consumer]);
            if ($stmt->fetch()) {
                $error = "⚠️ Duplicate Warning: This bill is already in the pending queue!";
            } else {
                // Insert into Queue
                $stmt = $pdo->prepare("INSERT INTO bill_queue (bill_type, consumer_number, consumer_name, mobile_no, amount, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->execute([$type, $consumer, $name, $mobile, $amount]);
                $msg = "✅ Bill Added to Queue Successfully!";
            }
        } catch (Exception $e) {
            $error = "System Error: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-panel p-4">
            <h4 class="fw-bold mb-4 border-bottom pb-2"><i class="fas fa-file-invoice-dollar me-2"></i> Add New Bill</h4>
            
            <?php if($msg): ?><div class="alert alert-success fw-bold text-center"><?php echo $msg; ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-danger fw-bold text-center"><?php echo $error; ?></div><?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="fw-bold small text-muted">Bill Type</label>
                    <div class="d-flex gap-2">
                        <input type="radio" class="btn-check" name="bill_type" id="elec" value="Electricity" checked>
                        <label class="btn btn-outline-primary flex-fill fw-bold" for="elec"><i class="fas fa-bolt"></i> Electricity</label>

                        <input type="radio" class="btn-check" name="bill_type" id="gas" value="Gas">
                        <label class="btn btn-outline-warning flex-fill fw-bold" for="gas"><i class="fas fa-fire"></i> Gas</label>

                        <input type="radio" class="btn-check" name="bill_type" id="water" value="Water">
                        <label class="btn btn-outline-info flex-fill fw-bold" for="water"><i class="fas fa-tint"></i> Water</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold small text-muted">Consumer / Reference No</label>
                    <input type="text" name="consumer_number" class="form-control form-control-lg fw-bold font-monospace" placeholder="Enter Ref Number" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="fw-bold small text-muted">Amount (Rs)</label>
                    <input type="number" name="amount" class="form-control form-control-lg fw-bold text-danger" placeholder="0" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted">Customer Name (Optional)</label>
                        <input type="text" name="consumer_name" class="form-control" placeholder="Name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted">Mobile No (Optional)</label>
                        <input type="text" name="mobile_no" class="form-control" placeholder="03...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold rounded-pill shadow-sm">
                    ADD TO QUEUE <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>