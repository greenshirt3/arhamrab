<?php 
require 'includes/header.php'; 
require_once 'includes/intelligence.php'; 

// --- DATA FETCHING ---
$today = date('Y-m-d');
$waiting_count = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE status='waiting'")->fetchColumn() ?: 0;
$served_count = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE status='served' AND DATE(served_at) = '$today'")->fetchColumn() ?: 0;

// --- AI ENGINE ---
// Initialize Intelligence
$predicted_wait = $AI->predictWaitTime($waiting_count);
$forecast = $AI->forecastDemand();

// --- FINANCIALS (Admin Only) ---
$total_profit = 0;
$total_disbursed = 0;
if($_SESSION['role'] == 'admin') {
    $total_disbursed = $pdo->query("SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = '$today' AND status='success'")->fetchColumn() ?: 0;
    $total_profit = $pdo->query("SELECT SUM(income) FROM transactions WHERE DATE(created_at) = '$today' AND status='success'")->fetchColumn() ?: 0;
}

// --- QUEUE LOGIC ---
$serving_now = $pdo->query("SELECT t.*, b.phone FROM queue_tokens t LEFT JOIN beneficiaries b ON t.cnic = b.cnic WHERE t.status='served' AND DATE(t.served_at) = '$today' ORDER BY t.served_at DESC LIMIT 1")->fetch();
$next_in_line = $pdo->query("SELECT t.*, b.phone FROM queue_tokens t LEFT JOIN beneficiaries b ON t.cnic = b.cnic WHERE t.status='waiting' ORDER BY t.id ASC LIMIT 1")->fetch();

// Handle Call Next
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['call_next_id'])) {
    $pdo->prepare("UPDATE queue_tokens SET status='served', served_at=NOW() WHERE id=?")->execute([$_POST['call_next_id']]);
    if(function_exists('logActivity')) { logActivity($pdo, "Call Token", "Called token ID: " . $_POST['call_next_id']); }
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-panel p-4 h-100 text-center position-relative">
            <span class="position-absolute top-0 end-0 m-3 ai-badge"><i class="fas fa-bolt"></i> AI Live</span>
            <h6 class="text-uppercase opacity-75 small fw-bold">Waiting Line</h6>
            <h1 class="display-3 fw-bold my-2"><?php echo $waiting_count; ?></h1>
            <p class="mb-0 text-muted small"><i class="fas fa-clock text-warning"></i> AI Est. Wait: <strong><?php echo $predicted_wait; ?> mins</strong></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-panel p-4 h-100 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h6 class="text-uppercase opacity-75 small fw-bold">Daily Forecast</h6>
            <h2 class="display-4 fw-bold my-2"><?php echo $forecast; ?></h2>
            <p class="mb-0 small text-white-50">Expected beneficiaries today based on history.</p>
        </div>
    </div>

    <?php if($_SESSION['role'] == 'admin'): ?>
    <div class="col-md-4">
        <div class="glass-panel p-4 h-100 text-center border-success" style="border-bottom: 4px solid #00E5FF;">
            <h6 class="text-uppercase text-success fw-bold small">Net Profit (Live)</h6>
            <h1 class="display-4 fw-bold text-success my-2">Rs. <?php echo number_format($total_profit); ?></h1>
            <div class="d-flex justify-content-between small text-muted mt-3">
                <span>Disbursed:</span>
                <span class="text-dark fw-bold">Rs. <?php echo number_format($total_disbursed); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="glass-panel p-4 h-100 text-center">
            <h6 class="text-uppercase opacity-50 mb-3">Counter Status</h6>
            <?php if($serving_now): ?>
                <h1 class="display-1 fw-bold text-primary mb-0" style="text-shadow: 0 0 20px rgba(0,229,255,0.3);"><?php echo $serving_now['token_number']; ?></h1>
                <div class="fs-4 text-muted mb-4"><?php echo $serving_now['name'] ?: 'Guest'; ?></div>
                <button onclick="speakUrdu('<?php echo $serving_now['token_number']; ?>')" class="btn btn-primary rounded-pill px-5 fw-bold shadow-lg">
                    <i class="fas fa-bullhorn me-2"></i> Announce Token
                </button>
            <?php else: ?>
                <h2 class="opacity-50 my-5">Counter Idle</h2>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="glass-panel p-4 h-100 d-flex flex-column justify-content-center text-center">
            <h6 class="text-uppercase text-warning fw-bold mb-3">Up Next</h6>
            <?php if($next_in_line): ?>
                <h1 class="display-2 fw-bold text-dark mb-2"><?php echo $next_in_line['token_number']; ?></h1>
                <div class="small text-muted mb-4"><?php echo $next_in_line['cnic']; ?></div>
                
                <div class="d-flex gap-2 justify-content-center">
                    <form method="post" class="w-50">
                        <input type="hidden" name="call_next_id" value="<?php echo $next_in_line['id']; ?>">
                        <button type="submit" class="btn btn-dark w-100 btn-lg fw-bold shadow-lg rounded-pill">CALL NEXT</button>
                    </form>
                    <?php if($next_in_line['phone']): ?>
                        <a href="https://wa.me/92<?php echo substr(preg_replace('/\D/', '', $next_in_line['phone']), -10); ?>?text=Your Token <?php echo $next_in_line['token_number']; ?> is ready." target="_blank" class="btn btn-success btn-lg shadow-lg rounded-circle">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="py-4">
                    <i class="fas fa-mug-hot fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Queue Empty</h4>
                    <a href="queue.php" class="btn btn-outline-primary mt-2 rounded-pill btn-sm">Issue New Token</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Urdu Voice Function
function speakUrdu(num) {
    if ('speechSynthesis' in window) {
        var msg = new SpeechSynthesisUtterance("Token Number " + num + ", Counter Par Aayien");
        msg.lang = 'ur-PK';
        window.speechSynthesis.speak(msg);
    } else { alert("Audio not supported."); }
}
</script>

<?php include 'includes/footer.php'; ?>