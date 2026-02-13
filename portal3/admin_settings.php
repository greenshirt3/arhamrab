<?php 
require 'includes/header.php'; 

// SECURITY: ADMIN ONLY
if ($_SESSION['role'] !== 'admin') { die("ACCESS DENIED"); }

$msg = "";

// HANDLE ACTIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. UPDATE SETTINGS
    if (isset($_POST['action']) && $_POST['action'] == 'update_settings') {
        
        // Helper to save settings safely
        function save($pdo, $k, $v) {
            // Check if key exists
            $check = $pdo->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
            $check->execute([$k]);
            
            if($check->rowCount() > 0) {
                $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$v, $k]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
                $stmt->execute([$k, $v]);
            }
        }

        // Save Standard Settings
        save($pdo, 'status_color', $_POST['status_color']);
        
        // Save Urdu Settings (The DB is now UTF8mb4 so this will work)
        save($pdo, 'status_text', $_POST['status_text']); 
        save($pdo, 'announcement', $_POST['announcement']); 
        
        // Save Time Settings
        save($pdo, 'shop_open_time', $_POST['shop_open_time']);
        save($pdo, 'shop_close_time', $_POST['shop_close_time']);
        save($pdo, 'enable_auto_hours', isset($_POST['enable_auto_hours']) ? '1' : '0');
        
        // Save Limits
        save($pdo, 'daily_token_limit', $_POST['daily_token_limit']);
        save($pdo, 'service_speed_mins', $_POST['service_speed_mins']);
        
        // Save Features
        save($pdo, 'break_mode', isset($_POST['break_mode']) ? '1' : '0');
        save($pdo, 'require_phone', isset($_POST['require_phone']) ? '1' : '0');

        $msg = "<div class='alert alert-success shadow-sm rounded-3 fw-bold'>Settings Updated Successfully!</div>";
    }

    // 2. RESET QUEUE (Danger Zone)
    if (isset($_POST['action']) && $_POST['action'] == 'reset_queue') {
        $pdo->exec("DELETE FROM queue_tokens WHERE DATE(issued_at) = CURDATE()");
        $msg = "<div class='alert alert-danger shadow-sm rounded-3 fw-bold'>Queue has been reset for today.</div>";
    }
}

// FETCH CURRENT SETTINGS
$s = [];
$rows = $pdo->query("SELECT * FROM system_settings")->fetchAll();
foreach($rows as $r) $s[$r['setting_key']] = $r['setting_value'];
?>

<link rel="stylesheet" href="css/modern.css">

<style>
    /* FIX: font-display: swap added to prevent slow network warning */
    @font-face {
        font-family: 'Jameel Noori Nastaleeq';
        src: url('Jameel Noori Nastaleeq.ttf') format('truetype');
        font-display: swap; 
    }
    .urdu-input {
        font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', serif;
        direction: rtl;
        font-size: 1.3rem;
        line-height: 2;
        text-align: right;
    }
    .card-header-custom { background: linear-gradient(135deg, #212529, #343a40); color: white; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <?php if($msg) echo $msg; ?>

        <form method="POST" accept-charset="UTF-8">
            <input type="hidden" name="action" value="update_settings">

            <div class="glass-panel mb-4 p-0">
                <div class="card-header-custom p-3"><h5 class="mb-0 text-white"><i class="fas fa-power-off me-2"></i> System Status</h5></div>
                <div class="p-4">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="status_color" id="success" value="success" <?php echo ($s['status_color']=='success')?'checked':''; ?>>
                            <label class="btn btn-outline-success w-100 fw-bold py-3 rounded-3" for="success">🟢 ONLINE</label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="status_color" id="warning" value="warning" <?php echo ($s['status_color']=='warning')?'checked':''; ?>>
                            <label class="btn btn-outline-warning w-100 fw-bold py-3 rounded-3" for="warning">⚠️ RUSH</label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" class="btn-check" name="status_color" id="danger" value="danger" <?php echo ($s['status_color']=='danger')?'checked':''; ?>>
                            <label class="btn btn-outline-danger w-100 fw-bold py-3 rounded-3" for="danger">🔴 OFFLINE</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-panel mb-4 p-0">
                <div class="card-header-custom p-3"><h5 class="mb-0 text-white"><i class="fas fa-bullhorn me-2"></i> Announcements (Urdu)</h5></div>
                <div class="p-4 bg-light">
                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill me-1" onclick="setUrdu('خوش آمدید', 'خوش آمدید! ٹوکن بکنگ جاری ہے۔')">🟢 Online</button>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill me-1" onclick="setUrdu('وقفہ', 'نماز اور کھانے کا وقفہ ہے۔')">🕌 Break</button>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill me-1" onclick="setUrdu('سسٹم بند ہے', 'دکان کا وقت ختم ہو چکا ہے۔ کل تشریف لائیں۔')">🔴 Closed</button>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-secondary">Main Heading (Hero Text)</label>
                        <input type="text" name="status_text" id="statusField" class="form-control urdu-input border-primary" value="<?php echo htmlspecialchars($s['status_text']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-secondary">Ticker Text (Top Bar)</label>
                        <textarea name="announcement" id="tickerField" class="form-control urdu-input border-primary" rows="2"><?php echo htmlspecialchars($s['announcement']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="glass-panel h-100 p-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Shop Timings</h6>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="enable_auto_hours" id="autoHours" <?php echo ($s['enable_auto_hours']=='1')?'checked':''; ?>>
                            <label class="form-check-label fw-bold" for="autoHours">Auto Open/Close</label>
                        </div>
                        
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light">Open</span>
                            <input type="time" name="shop_open_time" class="form-control" value="<?php echo $s['shop_open_time']; ?>">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Close</span>
                            <input type="time" name="shop_close_time" class="form-control" value="<?php echo $s['shop_close_time']; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="glass-panel h-100 p-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Queue Limits</h6>
                        
                        <label class="fw-bold small text-muted">Daily Tokens Limit</label>
                        <input type="number" name="daily_token_limit" class="form-control mb-2" value="<?php echo $s['daily_token_limit']; ?>">
                        
                        <label class="fw-bold small text-muted">Avg Speed (Min/Person)</label>
                        <input type="number" name="service_speed_mins" class="form-control" value="<?php echo $s['service_speed_mins']; ?>">
                    </div>
                </div>
            </div>

            <div class="glass-panel mb-5 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="break_mode" id="breakMode" <?php echo ($s['break_mode']=='1')?'checked':''; ?>>
                            <label class="form-check-label fw-bold text-warning" for="breakMode">Lunch Break</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="require_phone" id="reqPhone" <?php echo ($s['require_phone']=='1')?'checked':''; ?>>
                            <label class="form-check-label fw-bold" for="reqPhone">Require Phone</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow hover-effect">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </div>
            </div>

        </form>

        <div class="card border-danger border-1 shadow-sm rounded-4 mb-5">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="fas fa-trash"></i> Danger Zone
            </div>
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold text-danger mb-0">Reset Queue</h6>
                    <small class="text-muted">Delete all tokens for today (Start from 01)</small>
                </div>
                <form method="POST" onsubmit="return confirm('This will delete all tokens for today. Are you sure?');">
                    <input type="hidden" name="action" value="reset_queue">
                    <button type="submit" class="btn btn-outline-danger fw-bold">Reset Now</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function setUrdu(title, ticker) {
    document.getElementById('statusField').value = title;
    document.getElementById('tickerField').value = ticker;
}
</script>

<?php include 'includes/footer.php'; ?>