<?php 
// ==========================================
// 1. INIT & SECURITY
// ==========================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'includes/header.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// STRICT ADMIN CHECK
$is_admin = (in_array($_SESSION['user_id'], [1, 14, 15]) || (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin'));
// Also check via helper if available
if (!$is_admin && function_exists('has_perm')) {
    $is_admin = has_perm('admin');
}

if (!$is_admin) {
    die("<div class='container p-5 text-center text-danger fw-bold'><h3>⛔ ACCESS DENIED</h3><p>Only Super Admins can access System Settings.</p></div>");
}

$msg = "";
$error = "";

// ==========================================
// 2. HANDLE ACTIONS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // --- A. UPDATE GENERAL SETTINGS ---
    if (isset($_POST['update_settings'])) {
        try {
            $settings_to_update = [
                'shop_name' => trim($_POST['shop_name']),
                'shop_address' => trim($_POST['shop_address']),
                'shop_phone' => trim($_POST['shop_phone']),
                'receipt_footer' => trim($_POST['receipt_footer']),
                'currency_symbol' => trim($_POST['currency_symbol'])
            ];

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            
            foreach ($settings_to_update as $key => $val) {
                $stmt->execute([$key, $val]);
            }
            
            $pdo->commit();
            $msg = "✅ Shop Settings Updated Successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating settings: " . $e->getMessage();
        }
    }

    // --- B. ADD NEW USER ---
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role']; // admin, manager, agent
        
        // Build Permissions JSON
        $perms = [
            'admin' => isset($_POST['perm_admin']) ? 1 : 0,
            'bisp' => isset($_POST['perm_bisp']) ? 1 : 0,
            'hbl' => isset($_POST['perm_hbl']) ? 1 : 0,
            'shop' => isset($_POST['perm_shop']) ? 1 : 0,
            'loans' => isset($_POST['perm_loans']) ? 1 : 0
        ];
        $perm_json = json_encode($perms);

        if (empty($username) || empty($password)) {
            $error = "Username and Password are required.";
        } else {
            try {
                // Check duplicate
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $error = "Username already exists!";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, permissions, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$username, $hash, $role, $perm_json]);
                    $msg = "👤 User '$username' Added Successfully!";
                }
            } catch (Exception $e) { $error = $e->getMessage(); }
        }
    }

    // --- C. DELETE USER ---
    if (isset($_POST['delete_user'])) {
        $del_id = $_POST['user_id'];
        // Prevent self-deletion
        if ($del_id == $_SESSION['user_id']) {
            $error = "⛔ You cannot delete your own account!";
        } else {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$del_id]);
            $msg = "🗑️ User Deleted.";
        }
    }
}

// ==========================================
// 3. FETCH DATA
// ==========================================

// Fetch Settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM system_settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) { /* Ignore if table missing */ }

// Defaults
$s_name = $settings['shop_name'] ?? 'Arham Printers';
$s_addr = $settings['shop_address'] ?? 'Domela Chowk, Jalalpur Jattan';
$s_phone = $settings['shop_phone'] ?? '0300-1234567';
$s_foot = $settings['receipt_footer'] ?? 'Thank you for your business!';
$s_curr = $settings['currency_symbol'] ?? 'Rs.';

// Fetch Users
$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

// Fetch Recent Logs (Last 20)
$logs = [];
try {
    $logs = $pdo->query("SELECT s.*, u.username FROM system_logs s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC LIMIT 20")->fetchAll();
} catch (Exception $e) {}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h3 class="fw-bold text-dark mb-4"><i class="fas fa-cogs me-2"></i> System Settings</h3>
            
            <?php if($msg): ?><div class="alert alert-success fw-bold shadow-sm"><?php echo $msg; ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-danger fw-bold shadow-sm"><?php echo $error; ?></div><?php endif; ?>

            <ul class="nav nav-tabs mb-4" id="settingTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                        <i class="fas fa-store me-2"></i> Shop Config
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">
                        <i class="fas fa-users-cog me-2"></i> User Management
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button">
                        <i class="fas fa-shield-alt me-2"></i> Security Logs
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="general">
                    <div class="glass-panel p-4 bg-white shadow-sm" style="max-width: 800px;">
                        <form method="POST">
                            <input type="hidden" name="update_settings" value="1">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="fw-bold small text-muted">Shop Name</label>
                                    <input type="text" name="shop_name" class="form-control" value="<?php echo htmlspecialchars($s_name); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold small text-muted">Shop Phone</label>
                                    <input type="text" name="shop_phone" class="form-control" value="<?php echo htmlspecialchars($s_phone); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted">Shop Address</label>
                                <input type="text" name="shop_address" class="form-control" value="<?php echo htmlspecialchars($s_addr); ?>">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="fw-bold small text-muted">Receipt Footer Message</label>
                                    <input type="text" name="receipt_footer" class="form-control" value="<?php echo htmlspecialchars($s_foot); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold small text-muted">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" class="form-control" value="<?php echo htmlspecialchars($s_curr); ?>">
                                </div>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade" id="users">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="glass-panel p-4 bg-white shadow-sm mb-3">
                                <h5 class="fw-bold border-bottom pb-2 mb-3">Add New User</h5>
                                <form method="POST">
                                    <input type="hidden" name="add_user" value="1">
                                    
                                    <div class="mb-2">
                                        <label class="small fw-bold">Username</label>
                                        <input type="text" name="username" class="form-control" required autocomplete="off">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold">Password</label>
                                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold">Role Category</label>
                                        <select name="role" class="form-select">
                                            <option value="agent">Staff / Agent</option>
                                            <option value="manager">Manager</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>

                                    <label class="small fw-bold mb-2">Access Permissions:</label>
                                    <div class="bg-light p-2 rounded border mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="perm_shop" id="p_shop" checked>
                                            <label class="form-check-label small" for="p_shop">Shop POS Access</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="perm_hbl" id="p_hbl" checked>
                                            <label class="form-check-label small" for="p_hbl">Utility Bills (HBL)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="perm_bisp" id="p_bisp">
                                            <label class="form-check-label small" for="p_bisp">BISP Portal</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="perm_loans" id="p_loans">
                                            <label class="form-check-label small" for="p_loans">Ledger / Loans</label>
                                        </div>
                                        <div class="form-check border-top mt-1 pt-1">
                                            <input class="form-check-input" type="checkbox" name="perm_admin" id="p_admin">
                                            <label class="form-check-label small text-danger fw-bold" for="p_admin">Super Admin (Full Access)</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 fw-bold">Create User</button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="glass-panel p-0 bg-white shadow-sm overflow-hidden">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th class="ps-3">ID</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Permissions</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($users as $u): 
                                            $p = json_decode($u['permissions'] ?? '{}', true);
                                            $badges = "";
                                            if(!empty($p['shop'])) $badges .= "<span class='badge bg-info text-dark me-1'>POS</span>";
                                            if(!empty($p['hbl'])) $badges .= "<span class='badge bg-primary me-1'>Bills</span>";
                                            if(!empty($p['bisp'])) $badges .= "<span class='badge bg-warning text-dark me-1'>BISP</span>";
                                            if(!empty($p['admin'])) $badges .= "<span class='badge bg-danger'>ADMIN</span>";
                                        ?>
                                        <tr>
                                            <td class="ps-3 text-muted">#<?php echo $u['id']; ?></td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($u['username']); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo ucfirst($u['role']); ?></span></td>
                                            <td><?php echo $badges ?: '<span class="text-muted small">None</span>'; ?></td>
                                            <td class="text-end pe-3">
                                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" onsubmit="return confirm('Delete user <?php echo $u['username']; ?>?');" style="display:inline;">
                                                    <input type="hidden" name="delete_user" value="1">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                                <?php else: ?>
                                                    <span class="text-muted small">Current</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="logs">
                    <div class="glass-panel p-0 bg-white shadow-sm">
                        <table class="table table-striped mb-0">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th class="ps-3">Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($logs)): ?>
                                    <tr><td colspan="5" class="text-center p-4">No logs found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($logs as $log): ?>
                                    <tr>
                                        <td class="ps-3 small text-muted"><?php echo $log['created_at']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($log['action']); ?></span></td>
                                        <td class="small"><?php echo htmlspecialchars($log['details']); ?></td>
                                        <td class="small text-muted font-monospace"><?php echo $log['ip_address']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>