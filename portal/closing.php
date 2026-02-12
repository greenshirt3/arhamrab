<?php 
require 'includes/header.php'; 

// ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 1. GET EXPECTED SYSTEM BALANCE
$stmt = $pdo->prepare("SELECT current_balance FROM accounts WHERE account_name = 'Shop Cash Drawer'");
$stmt->execute();
$system_balance = (float) $stmt->fetchColumn();

// 2. HANDLE FORM SUBMISSION
$msg = "";
$difference = 0;
$physical_cash = 0;
$show_result = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Calculate Physical Cash based on Denominations
    $n5000 = (int)$_POST['n5000'] * 5000;
    $n1000 = (int)$_POST['n1000'] * 1000;
    $n500  = (int)$_POST['n500'] * 500;
    $n100  = (int)$_POST['n100'] * 100;
    $n50   = (int)$_POST['n50'] * 50;
    $n20   = (int)$_POST['n20'] * 20;
    $n10   = (int)$_POST['n10'] * 10;
    $coins = (float)$_POST['coins'];
    
    $physical_cash = $n5000 + $n1000 + $n500 + $n100 + $n50 + $n20 + $n10 + $coins;
    
    // Calculate Difference
    $difference = $physical_cash - $system_balance;
    $show_result = true;
    
    // Log the Closing Event
    $log_details = "Day Closing: System (Rs. $system_balance) vs Physical (Rs. $physical_cash). Difference: Rs. $difference";
    
    try {
        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, 'Day Closing', ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $log_details, $_SERVER['REMOTE_ADDR']]);
    } catch (Exception $e) {
        // Silently fail logging if error
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h3 class="fw-bold mb-4 text-center text-dark">
            <i class="fas fa-lock text-warning me-2"></i> Day Closing & Cash Count
        </h3>
        
        <?php if ($show_result): ?>
            <div class="glass-panel p-5 mb-4 text-center border-top border-5 <?php echo ($difference >= 0) ? 'border-success' : 'border-danger'; ?> bg-white shadow">
                <h5 class="text-muted text-uppercase fw-bold mb-4">Closing Summary</h5>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold">System Balance</small>
                        <h3 class="fw-bold text-dark mt-2">Rs. <?php echo number_format($system_balance); ?></h3>
                    </div>
                    <div class="col-md-4 border-start border-end">
                        <small class="text-muted d-block fw-bold">Physical Cash</small>
                        <h3 class="fw-bold text-primary mt-2">Rs. <?php echo number_format($physical_cash); ?></h3>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block fw-bold">Difference</small>
                        <h3 class="fw-bold <?php echo ($difference >= 0) ? 'text-success' : 'text-danger'; ?> mt-2">
                            <?php echo ($difference > 0) ? "+" : ""; ?>
                            <?php echo number_format($difference); ?>
                        </h3>
                    </div>
                </div>
                
                <div class="mt-5">
                    <?php if ($difference == 0): ?>
                        <div class="alert alert-success fw-bold py-3">
                            <i class="fas fa-check-circle me-2"></i> Cash Matched Perfectly! Great Job.
                        </div>
                    <?php elseif ($difference < 0): ?>
                        <div class="alert alert-danger fw-bold py-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> Cash Shortage! You are missing money.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info fw-bold py-3">
                            <i class="fas fa-info-circle me-2"></i> Cash Excess! You have extra money.
                        </div>
                    <?php endif; ?>
                </div>
                
                <a href="dashboard.php" class="btn btn-dark btn-lg px-5 mt-3 rounded-pill fw-bold">Return to Dashboard</a>
            </div>
        <?php else: ?>

            <div class="glass-panel p-4 bg-white shadow-sm">
                <div class="alert alert-info text-center fw-bold mb-4">
                    <i class="fas fa-info-circle me-2"></i> The System expects: Rs. <?php echo number_format($system_balance); ?>
                </div>
                
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">5000 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n5000" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">1000 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n1000" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">500 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n500" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">100 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n100" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">50 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n50" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">20 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n20" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="small fw-bold text-muted">10 Notes</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">x</span>
                                <input type="number" name="n10" class="form-control fw-bold" placeholder="0">
                            </div>
                        </div>
                        <div class="col-6 col-md-8">
                            <label class="small fw-bold text-muted">Loose Coins / Change</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rs.</span>
                                <input type="number" name="coins" class="form-control fw-bold" placeholder="Total Value">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <button class="btn btn-warning w-100 btn-lg fw-bold text-dark rounded-pill shadow-sm">
                        <i class="fas fa-calculator me-2"></i> CALCULATE & CLOSE
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>