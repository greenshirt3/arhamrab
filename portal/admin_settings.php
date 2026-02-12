<?php 
require 'includes/header.php'; 

// 1. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$allowed_ids = [1, 14, 15];
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role']) === 'admin');

if (!$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED</div></div>");
}

// 2. CHECK/CREATE TABLE (Safety)
$pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    setting_key VARCHAR(50) UNIQUE, 
    setting_value TEXT
)");

// 3. HANDLE SAVE
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settings_to_save = ['shop_name', 'shop_address', 'shop_phone', 'invoice_footer'];
    
    foreach ($settings_to_save as $key) {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            
            // Check if exists
            $stmt = $pdo->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            
            if ($stmt->fetch()) {
                // Update
                $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
                $stmt->execute([$key, $value]);
            }
        }
    }
    $msg = "Settings saved successfully!";
}

// 4. FETCH CURRENT SETTINGS
$current_settings = [];
$rows = $pdo->query("SELECT * FROM system_settings")->fetchAll();
foreach ($rows as $row) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold"><i class="fas fa-cogs text-primary me-2"></i> System Configuration</h3>
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success fw-bold"><?php echo $msg; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="glass-panel p-4 bg-white shadow-sm">
            <h5 class="fw-bold border-bottom pb-2 mb-4">Invoice / Receipt Settings</h5>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="fw-bold small">Shop Name</label>
                    <input type="text" name="shop_name" class="form-control" value="<?php echo htmlspecialchars($current_settings['shop_name'] ?? 'ARHAM PRINTERS'); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold small">Shop Address</label>
                    <input type="text" name="shop_address" class="form-control" value="<?php echo htmlspecialchars($current_settings['shop_address'] ?? 'Jalalpur Jattan'); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold small">Phone Number</label>
                    <input type="text" name="shop_phone" class="form-control" value="<?php echo htmlspecialchars($current_settings['shop_phone'] ?? '0300-1234567'); ?>">
                </div>
                
                <div class="mb-4">
                    <label class="fw-bold small">Receipt Footer Message</label>
                    <input type="text" name="invoice_footer" class="form-control" value="<?php echo htmlspecialchars($current_settings['invoice_footer'] ?? 'No Return / No Exchange'); ?>">
                </div>
                
                <button class="btn btn-primary w-100 fw-bold rounded-pill">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="glass-panel p-4 bg-light border text-center text-muted">
            <i class="fas fa-tools fa-3x mb-3"></i>
            <h5>More Settings Coming Soon</h5>
            <p>Future updates will include Backup Settings, SMS API configuration, and User Roles management.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>