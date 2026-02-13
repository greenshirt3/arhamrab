<?php include 'includes/header.php'; 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("ACCESS DENIED"); }

$today = date('Y-m-d');
$total_paid = $pdo->query("SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = '$today' AND status='success'")->fetchColumn() ?: 0;
$count = $pdo->query("SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = '$today' AND status='success'")->fetchColumn() ?: 0;

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opening = $_POST['opening_cash'];
    $refill = $_POST['bank_withdrawals'];
    $closing = $_POST['physical_cash_now'];
    
    // Formula: (Opening + Refills) - PaidOut should equal Closing
    $expected = ($opening + $refill) - $total_paid;
    $diff = $closing - $expected;
    
    $color = ($diff == 0) ? 'success' : (($diff < 0) ? 'danger' : 'warning');
    $status = ($diff == 0) ? 'Perfect Match' : (($diff < 0) ? 'Cash Shortage' : 'Surplus Cash');
    
    $msg = "<div class='alert alert-$color glass-panel text-center'>
        <h3 class='fw-bold'>$status: Rs. " . number_format($diff) . "</h3>
        <p class='mb-0'>Expected: " . number_format($expected) . " | Found: " . number_format($closing) . "</p>
    </div>";
}
?>

<link rel="stylesheet" href="css/modern.css">

<div class="row justify-content-center">
    <div class="col-md-5">
        
        <?php echo $msg; ?>

        <div class="glass-panel p-4">
            <div class="text-center mb-4">
                <h4 class="fw-bold"><i class="fas fa-balance-scale"></i> Daily Closing</h4>
                <p class="text-muted"><?php echo date('l, d F Y'); ?></p>
            </div>

            <div class="alert alert-info text-center border-0 bg-opacity-10">
                <small class="text-uppercase">Total Disbursed Today</small>
                <h2 class="fw-bold my-1">Rs. <?php echo number_format($total_paid); ?></h2>
                <small>(<?php echo $count; ?> Transactions)</small>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="fw-bold small">1. Opening Cash (Morning)</label>
                    <input type="number" name="opening_cash" class="form-control" required placeholder="0">
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">2. Cash Added (Refills)</label>
                    <input type="number" name="bank_withdrawals" class="form-control" value="0">
                </div>
                <div class="mb-4">
                    <label class="fw-bold small text-primary">3. Closing Cash (Count Now)</label>
                    <input type="number" name="physical_cash_now" class="form-control form-control-lg border-primary fw-bold" required placeholder="0">
                </div>
                <button class="btn btn-dark w-100 rounded-pill py-3 fw-bold">Calculate Balance</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>