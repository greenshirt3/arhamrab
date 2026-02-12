<?php 
require 'includes/header.php'; 

// 1. ACCESS CONTROL (Admin Only)
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$allowed_ids = [1, 14, 15];
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role']) === 'admin');

if (!$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED</div></div>");
}

// 2. FETCH LOGS
// Join with users table to get usernames
$sql = "SELECT l.*, u.username 
        FROM system_logs l 
        LEFT JOIN users u ON l.user_id = u.id 
        ORDER BY l.id DESC 
        LIMIT 200";
        
$logs = $pdo->query($sql)->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold"><i class="fas fa-shield-alt text-danger me-2"></i> System Audit Logs</h3>
        <p class="text-muted">Tracking the last 200 system activities for security.</p>
    </div>
</div>

<div class="glass-panel p-0 overflow-hidden bg-white shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle small">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4">Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $l): ?>
                    <tr>
                        <td class="ps-4 text-muted" style="min-width: 150px;">
                            <?php echo date('d-M-Y h:i A', strtotime($l['created_at'])); ?>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">
                                <?php echo htmlspecialchars($l['username'] ?? 'System/Deleted'); ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary text-uppercase px-2">
                                <?php echo htmlspecialchars($l['action']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-wrap" style="max-width: 400px;">
                                <?php echo htmlspecialchars($l['details']); ?>
                            </div>
                        </td>
                        <td class="text-muted font-monospace">
                            <?php echo htmlspecialchars($l['ip_address']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            No logs found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>