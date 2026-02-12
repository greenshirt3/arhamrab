<?php
// 1. ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/header.php';

// 2. ACCESS CONTROL - Only Admin can manage users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$allowed_ids = [1, 14, 15];
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role']) === 'admin');

if (!$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED: Only Administrators can manage users.</div></div>");
}

// 3. HANDLE FORM ACTIONS
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. ADD/EDIT USER
    if (isset($_POST['save_user'])) {
        $id = $_POST['user_id'];
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];
        $status = $_POST['status'];
        
        // Build permissions array
        $permissions = [
            'admin' => isset($_POST['perm_admin']) ? 1 : 0,
            'bisp' => isset($_POST['perm_bisp']) ? 1 : 0,
            'hbl' => isset($_POST['perm_hbl']) ? 1 : 0,
            'loans' => isset($_POST['perm_loans']) ? 1 : 0,
            'shop' => isset($_POST['perm_shop']) ? 1 : 0,
            'closing' => isset($_POST['perm_closing']) ? 1 : 0
        ];
        
        $permissions_json = json_encode($permissions);
        
        if (empty($username)) {
            $error = "Username is required.";
        } else {
            try {
                if ($id) {
                    // UPDATE EXISTING USER
                    if (!empty($password)) {
                        // Update with new password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET username=?, password=?, role=?, status=?, permissions=? WHERE id=?");
                        $stmt->execute([$username, $hashed_password, $role, $status, $permissions_json, $id]);
                    } else {
                        // Update without changing password
                        $stmt = $pdo->prepare("UPDATE users SET username=?, role=?, status=?, permissions=? WHERE id=?");
                        $stmt->execute([$username, $role, $status, $permissions_json, $id]);
                    }
                    $msg = "User updated successfully!";
                } else {
                    // CREATE NEW USER
                    if (empty($password)) {
                        $error = "Password is required for new users.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status, permissions, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                        $stmt->execute([$username, $hashed_password, $role, $status, $permissions_json]);
                        $msg = "New user created successfully!";
                    }
                }
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $error = "Username already exists!";
                } else {
                    $error = "Database Error: " . $e->getMessage();
                }
            }
        }
    }
    
    // B. DELETE USER
    if (isset($_POST['delete_user'])) {
        $id = $_POST['delete_id'];
        
        // Prevent deleting yourself
        if ($id == $_SESSION['user_id']) {
            $error = "You cannot delete your own account!";
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $msg = "User deleted successfully!";
            } catch (PDOException $e) {
                $error = "Error deleting user: " . $e->getMessage();
            }
        }
    }
    
    // C. RESET PASSWORD
    if (isset($_POST['reset_password'])) {
        $id = $_POST['reset_id'];
        $new_password = $_POST['new_password'];
        
        if (empty($new_password)) {
            $error = "Please enter a new password.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $id]);
            $msg = "Password reset successfully!";
        }
    }
}

// 4. FETCH ALL USERS
$users = $pdo->query("SELECT * FROM users ORDER BY role DESC, username ASC")->fetchAll();

// 5. PRESET ROLES FOR QUICK ASSIGNMENT
$preset_roles = [
    'owner' => [
        'name' => 'Owner (Full Access)',
        'perms' => ['admin' => 1, 'bisp' => 1, 'hbl' => 1, 'loans' => 1, 'shop' => 1, 'closing' => 1],
        'description' => 'Complete system access. Can view all financials, delete records, and manage settings.'
    ],
    'manager' => [
        'name' => 'Manager',
        'perms' => ['admin' => 0, 'bisp' => 1, 'hbl' => 1, 'loans' => 1, 'shop' => 1, 'closing' => 0],
        'description' => 'Can manage operations but cannot see profits or delete records.'
    ],
    'bisp_cashier' => [
        'name' => 'BISP Cashier',
        'perms' => ['admin' => 0, 'bisp' => 1, 'hbl' => 0, 'loans' => 0, 'shop' => 0, 'closing' => 0],
        'description' => 'Only handles BISP customers - token issuance and payout processing.'
    ],
    'bill_clerk' => [
        'name' => 'Bill Entry Clerk',
        'perms' => ['admin' => 0, 'bisp' => 0, 'hbl' => 1, 'loans' => 0, 'shop' => 0, 'closing' => 0],
        'description' => 'Only types bills, cannot mark as paid or delete entries.'
    ],
    'viewer' => [
        'name' => 'Viewer / Helper',
        'perms' => ['admin' => 0, 'bisp' => 0, 'hbl' => 0, 'loans' => 0, 'shop' => 0, 'closing' => 0],
        'description' => 'Can login but cannot perform any actions. For observation only.'
    ],
    'custom' => [
        'name' => 'Custom Role',
        'perms' => ['admin' => 0, 'bisp' => 0, 'hbl' => 0, 'loans' => 0, 'shop' => 0, 'closing' => 0],
        'description' => 'Create custom permission set.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - ARHAM ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: var(--shadow);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .glass-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.25);
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,192C672,181,768,139,864,138.7C960,139,1056,181,1152,197.3C1248,213,1344,203,1392,197.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .role-badge.admin {
            background: linear-gradient(45deg, #ff8a00, #e52e71);
            color: white;
        }

        .role-badge.staff {
            background: linear-gradient(45deg, #3498db, #2ecc71);
            color: white;
        }

        .permission-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .permission-card:hover {
            border-color: var(--secondary);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
        }

        .permission-card.active {
            border-color: var(--success);
            background: rgba(46, 204, 113, 0.05);
        }

        .permission-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .permission-icon.admin { background: linear-gradient(45deg, #ff8a00, #e52e71); color: white; }
        .permission-icon.bisp { background: linear-gradient(45deg, #3498db, #2ecc71); color: white; }
        .permission-icon.hbl { background: linear-gradient(45deg, #9b59b6, #8e44ad); color: white; }
        .permission-icon.loans { background: linear-gradient(45deg, #f39c12, #d35400); color: white; }
        .permission-icon.shop { background: linear-gradient(45deg, #1abc9c, #16a085); color: white; }
        .permission-icon.closing { background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; }

        .preset-role-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .preset-role-card:hover {
            transform: translateY(-5px);
            border-color: var(--secondary);
        }

        .preset-role-card.selected {
            border-color: var(--success);
            background: rgba(46, 204, 113, 0.05);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.active { background: rgba(46, 204, 113, 0.2); color: var(--success); }
        .status-badge.inactive { background: rgba(231, 76, 60, 0.2); color: var(--danger); }

        .action-btn {
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .action-btn.edit { background: rgba(52, 152, 219, 0.1); color: var(--secondary); }
        .action-btn.delete { background: rgba(231, 76, 60, 0.1); color: var(--danger); }
        .action-btn.reset { background: rgba(243, 156, 18, 0.1); color: var(--warning); }

        .tab-content {
            padding: 20px;
            background: white;
            border-radius: 0 0 20px 20px;
            border: 1px solid #e0e0e0;
            border-top: none;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 600;
            padding: 15px 25px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary);
            border-color: rgba(44, 62, 80, 0.3);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: transparent;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 20px;
            }
            
            .nav-tabs .nav-link {
                padding: 10px 15px;
                font-size: 14px;
            }
        }

        .permission-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }

        .permission-details ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .permission-details li {
            padding: 5px 0;
            font-size: 13px;
        }

        .permission-details li i {
            width: 20px;
            text-align: center;
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 40px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .user-table tr {
            transition: all 0.3s ease;
        }

        .user-table tr:hover {
            background-color: #f8f9fa;
        }

        .floating-action-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--success), var(--info));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(46, 204, 113, 0.3);
            z-index: 1000;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .floating-action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(46, 204, 113, 0.4);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .permission-matrix {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
        }

        .matrix-table th,
        .matrix-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        .matrix-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .matrix-table .role-header {
            background: var(--primary);
            color: white;
        }

        .access-yes {
            color: var(--success);
            font-weight: bold;
        }

        .access-no {
            color: var(--danger);
            opacity: 0.5;
        }

        .access-partial {
            color: var(--warning);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Main Container -->
    <div class="container-fluid py-4">
        <!-- Dashboard Header -->
        <div class="dashboard-header animate__animated animate__fadeInDown">
            <div class="header-content">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="display-5 fw-bold mb-2">User Management</h1>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-users me-2"></i>Manage system users, roles, and permissions
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="role-badge admin">
                            <i class="fas fa-crown me-2"></i>Administrator
                        </span>
                        <div class="mt-2">
                            <small class="opacity-75">
                                <i class="fas fa-shield-alt me-1"></i> Security Level: Maximum
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if($msg): ?>
            <div class="alert alert-success alert-dismissible fade show glass-panel animate__animated animate__fadeInUp" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show glass-panel animate__animated animate__shakeX" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- User Statistics -->
        <div class="user-stats animate__animated animate__fadeIn">
            <?php
                $total_users = count($users);
                $active_users = 0;
                $admin_users = 0;
                $staff_users = 0;
                
                foreach($users as $user) {
                    if($user['status'] == 1) $active_users++;
                    if(strtolower($user['role']) == 'admin') $admin_users++;
                    else $staff_users++;
                }
            ?>
            <div class="stat-card">
                <div class="stat-value text-primary"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success"><?php echo $active_users; ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning"><?php echo $admin_users; ?></div>
                <div class="stat-label">Administrators</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-info"><?php echo $staff_users; ?></div>
                <div class="stat-label">Staff Members</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Users Table -->
                <div class="glass-panel p-4 mb-4">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-users-cog me-2"></i>System Users
                        </h2>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="userSearch" class="form-control" placeholder="Search users...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover user-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                <?php foreach($users as $user): 
                                    $perms = json_decode($user['permissions'] ?? '{}', true);
                                    $initials = strtoupper(substr($user['username'], 0, 2));
                                    $is_current_user = ($user['id'] == $_SESSION['user_id']);
                                ?>
                                <tr class="animate__animated animate__fadeInUp" data-username="<?php echo strtolower($user['username']); ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <?php echo $initials; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
                                                <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                                <?php if($is_current_user): ?>
                                                    <span class="badge bg-info ms-2">Current</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="role-badge <?php echo strtolower($user['role']) == 'admin' ? 'admin' : 'staff'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php if($perms['admin'] ?? 0): ?>
                                                <span class="badge bg-danger" title="Admin Access">A</span>
                                            <?php endif; ?>
                                            <?php if($perms['bisp'] ?? 0): ?>
                                                <span class="badge bg-primary" title="BISP Access">B</span>
                                            <?php endif; ?>
                                            <?php if($perms['hbl'] ?? 0): ?>
                                                <span class="badge bg-purple" title="Bill Access">H</span>
                                            <?php endif; ?>
                                            <?php if($perms['loans'] ?? 0): ?>
                                                <span class="badge bg-warning" title="Loans Access">L</span>
                                            <?php endif; ?>
                                            <?php if($perms['shop'] ?? 0): ?>
                                                <span class="badge bg-success" title="Shop Access">S</span>
                                            <?php endif; ?>
                                            <?php if($perms['closing'] ?? 0): ?>
                                                <span class="badge bg-dark" title="Closing Access">C</span>
                                            <?php endif; ?>
                                            <?php if(array_sum($perms) == 0): ?>
                                                <span class="badge bg-secondary">No Access</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $user['status'] == 1 ? 'active' : 'inactive'; ?>">
                                            <?php echo $user['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?php echo $user['last_login'] ? date('d M, h:i A', strtotime($user['last_login'])) : 'Never'; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="action-btn edit" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-btn reset" onclick="resetPasswordModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-key"></i> Reset
                                            </button>
                                            <?php if(!$is_current_user): ?>
                                            <button class="action-btn delete" onclick="deleteUserModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Permission Matrix -->
                <div class="permission-matrix animate__animated animate__fadeInUp">
                    <h4 class="fw-bold mb-3">
                        <i class="fas fa-table me-2"></i>Role Permission Matrix
                    </h4>
                    <div class="table-responsive">
                        <table class="matrix-table">
                            <thead>
                                <tr>
                                    <th class="role-header">Permission</th>
                                    <th class="role-header">Owner</th>
                                    <th>Manager</th>
                                    <th>BISP Cashier</th>
                                    <th>Bill Clerk</th>
                                    <th>Viewer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-bold">Admin Access</td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">BISP Access</td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">Bill Access</td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">Loans Access</td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">Shop Access</td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold">Closing Access</td>
                                    <td class="access-yes"><i class="fas fa-check-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                    <td class="access-no"><i class="fas fa-times-circle"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- User Form -->
                <div class="glass-panel p-4 mb-4">
                    <h3 class="fw-bold mb-3" id="formTitle">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </h3>
                    
                    <form method="POST" id="userForm">
                        <input type="hidden" name="save_user" value="1">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                            <div class="form-text">Choose a unique username for login</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <div class="form-text" id="passwordHelp">
                                Leave blank to keep existing password when editing
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="admin">Administrator</option>
                                    <option value="staff" selected>Staff Member</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Preset Roles -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Quick Role Presets</label>
                            <div class="row g-2" id="presetRoles">
                                <?php foreach($preset_roles as $key => $preset): ?>
                                <div class="col-6">
                                    <div class="preset-role-card p-3 text-center" 
                                         onclick="applyPreset('<?php echo $key; ?>')"
                                         data-preset="<?php echo $key; ?>">
                                        <div class="fw-bold mb-1"><?php echo $preset['name']; ?></div>
                                        <small class="text-muted"><?php echo $preset['description']; ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Individual Permissions -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Detailed Permissions</label>
                            
                            <div class="permission-card" id="permAdminCard">
                                <div class="d-flex align-items-center">
                                    <div class="permission-icon admin me-3">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">Admin Access</h6>
                                                <small class="text-muted">Full system control</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="perm_admin" id="perm_admin" value="1">
                                            </div>
                                        </div>
                                        <div class="permission-details" style="display: none;">
                                            <ul>
                                                <li><i class="fas fa-check text-success"></i> View all financials</li>
                                                <li><i class="fas fa-check text-success"></i> Delete records</li>
                                                <li><i class="fas fa-check text-success"></i> Change system settings</li>
                                                <li><i class="fas fa-check text-success"></i> Override staff actions</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="permission-card" id="permBispCard">
                                <div class="d-flex align-items-center">
                                    <div class="permission-icon bisp me-3">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">BISP Access</h6>
                                                <small class="text-muted">Cash disbursement</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="perm_bisp" id="perm_bisp" value="1">
                                            </div>
                                        </div>
                                        <div class="permission-details" style="display: none;">
                                            <ul>
                                                <li><i class="fas fa-check text-success"></i> Issue BISP tokens</li>
                                                <li><i class="fas fa-check text-success"></i> Process cash payouts</li>
                                                <li><i class="fas fa-check text-success"></i> View waiting queue</li>
                                                <li><i class="fas fa-times text-danger"></i> Cannot see commissions</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="permission-card" id="permHblCard">
                                <div class="d-flex align-items-center">
                                    <div class="permission-icon hbl me-3">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">Bill Access</h6>
                                                <small class="text-muted">Utility bill management</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="perm_hbl" id="perm_hbl" value="1">
                                            </div>
                                        </div>
                                        <div class="permission-details" style="display: none;">
                                            <ul>
                                                <li><i class="fas fa-check text-success"></i> Add new bills</li>
                                                <li><i class="fas fa-check text-success"></i> View pending queue</li>
                                                <li><i class="fas fa-times text-danger"></i> Cannot mark as paid</li>
                                                <li><i class="fas fa-times text-danger"></i> Cannot delete bills</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="permission-card" id="permLoansCard">
                                <div class="d-flex align-items-center">
                                    <div class="permission-icon loans me-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">Loans Access</h6>
                                                <small class="text-muted">Customer credit management</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="perm_loans" id="perm_loans" value="1">
                                            </div>
                                        </div>
                                        <div class="permission-details" style="display: none;">
                                            <ul>
                                                <li><i class="fas fa-check text-success"></i> View customer balances</li>
                                                <li><i class="fas fa-check text-success"></i> Record credit transactions</li>
                                                <li><i class="fas fa-times text-danger"></i> Cannot edit cash drawer</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="permission-card" id="permShopCard">
                                <div class="d-flex align-items-center">
                                    <div class="permission-icon shop me-3">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">Shop Access</h6>
                                                <small class="text-muted">POS and inventory</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="perm_shop" id="perm_shop" value="1">
                                            </div>
                                        </div>
                                        <div class="permission-details" style="display: none;">
                                            <ul>
                                                <li><i class="fas fa-check text-success"></i> Process sales</li>
                                                <li><i class="fas fa-check text-success"></i> View inventory</li>
                                                <li><i class="fas fa-check text-success"></i> Manage stock</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="permission-card" id="permClosingCard">
                                <div class="d-flex align-items-center">
                                    <div class="permission-icon closing me-3">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">Closing Access</h6>
                                                <small class="text-muted">Day end reconciliation</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="perm_closing" id="perm_closing" value="1">
                                            </div>
                                        </div>
                                        <div class="permission-details" style="display: none;">
                                            <ul>
                                                <li><i class="fas fa-check text-success"></i> Perform day closing</li>
                                                <li><i class="fas fa-check text-success"></i> Count physical cash</li>
                                                <li><i class="fas fa-check text-success"></i> Calculate differences</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                <i class="fas fa-save me-2"></i> Save User
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-times me-2"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Quick Guide -->
                <div class="glass-panel p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-question-circle me-2"></i>Permission Guide
                    </h5>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Best Practices</h6>
                        <ul class="mb-0">
                            <li>Assign minimum required permissions</li>
                            <li>Use preset roles for common positions</li>
                            <li>Regularly review user activity</li>
                            <li>Deactivate unused accounts</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Security Notes</h6>
                        <small>
                            <strong>Admin Access</strong> gives complete system control.
                            Only assign to trusted personnel.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action-btn" onclick="resetForm()">
        <i class="fas fa-user-plus"></i>
    </div>

    <!-- Modals -->
    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger small">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        This action cannot be undone. All user data will be permanently removed.
                    </p>
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="delete_user" value="1">
                        <input type="hidden" name="delete_id" id="deleteUserId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteForm').submit()">
                        <i class="fas fa-trash me-2"></i> Delete User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="fas fa-key me-2"></i>Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Reset password for user: <strong id="resetUserName"></strong></p>
                    <form method="POST" id="resetForm">
                        <input type="hidden" name="reset_password" value="1">
                        <input type="hidden" name="reset_id" id="resetUserId">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <div class="form-text">Enter a strong password for the user</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="document.getElementById('resetForm').submit()">
                        <i class="fas fa-sync-alt me-2"></i> Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize modals
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const resetModal = new bootstrap.Modal(document.getElementById('resetModal'));

        // Search functionality
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#userTableBody tr');
            
            rows.forEach(row => {
                const username = row.getAttribute('data-username');
                if (username.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Edit user function
        function editUser(user) {
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit User';
            document.getElementById('user_id').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('role').value = user.role;
            document.getElementById('status').value = user.status;
            document.getElementById('password').required = false;
            document.getElementById('passwordHelp').textContent = 'Leave blank to keep existing password';
            
            // Parse permissions
            const perms = JSON.parse(user.permissions || '{}');
            document.getElementById('perm_admin').checked = perms.admin == 1;
            document.getElementById('perm_bisp').checked = perms.bisp == 1;
            document.getElementById('perm_hbl').checked = perms.hbl == 1;
            document.getElementById('perm_loans').checked = perms.loans == 1;
            document.getElementById('perm_shop').checked = perms.shop == 1;
            document.getElementById('perm_closing').checked = perms.closing == 1;
            
            updatePermissionCards();
            
            // Scroll to form
            document.getElementById('userForm').scrollIntoView({ behavior: 'smooth' });
        }

        // Apply preset role
        function applyPreset(presetKey) {
            const preset = {
                'owner': { admin: 1, bisp: 1, hbl: 1, loans: 1, shop: 1, closing: 1 },
                'manager': { admin: 0, bisp: 1, hbl: 1, loans: 1, shop: 1, closing: 0 },
                'bisp_cashier': { admin: 0, bisp: 1, hbl: 0, loans: 0, shop: 0, closing: 0 },
                'bill_clerk': { admin: 0, bisp: 0, hbl: 1, loans: 0, shop: 0, closing: 0 },
                'viewer': { admin: 0, bisp: 0, hbl: 0, loans: 0, shop: 0, closing: 0 },
                'custom': { admin: 0, bisp: 0, hbl: 0, loans: 0, shop: 0, closing: 0 }
            };
            
            const perms = preset[presetKey];
            
            document.getElementById('perm_admin').checked = perms.admin == 1;
            document.getElementById('perm_bisp').checked = perms.bisp == 1;
            document.getElementById('perm_hbl').checked = perms.hbl == 1;
            document.getElementById('perm_loans').checked = perms.loans == 1;
            document.getElementById('perm_shop').checked = perms.shop == 1;
            document.getElementById('perm_closing').checked = perms.closing == 1;
            
            updatePermissionCards();
            
            // Highlight selected preset
            document.querySelectorAll('.preset-role-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-preset="${presetKey}"]`).classList.add('selected');
        }

        // Update permission card visuals
        function updatePermissionCards() {
            const checkboxes = ['admin', 'bisp', 'hbl', 'loans', 'shop', 'closing'];
            
            checkboxes.forEach(perm => {
                const checkbox = document.getElementById(`perm_${perm}`);
                const card = document.getElementById(`perm${perm.charAt(0).toUpperCase() + perm.slice(1)}Card`);
                const details = card.querySelector('.permission-details');
                
                if (checkbox.checked) {
                    card.classList.add('active');
                    details.style.display = 'block';
                } else {
                    card.classList.remove('active');
                    details.style.display = 'none';
                }
            });
        }

        // Reset form
        function resetForm() {
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('user_id').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passwordHelp').textContent = 'Enter a password for the new user';
            document.getElementById('role').value = 'staff';
            document.getElementById('status').value = '1';
            
            // Reset all permissions to unchecked
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            
            // Reset preset selection
            document.querySelectorAll('.preset-role-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            updatePermissionCards();
        }

        // Delete user modal
        function deleteUserModal(userId, username) {
            document.getElementById('deleteUserName').textContent = username;
            document.getElementById('deleteUserId').value = userId;
            deleteModal.show();
        }

        // Reset password modal
        function resetPasswordModal(userId, username) {
            document.getElementById('resetUserName').textContent = username;
            document.getElementById('resetUserId').value = userId;
            resetModal.show();
        }

        // Permission card click handlers
        document.querySelectorAll('.permission-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.classList.contains('form-check-input')) {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    updatePermissionCards();
                }
            });
        });

        // Initialize permission cards
        document.addEventListener('DOMContentLoaded', function() {
            updatePermissionCards();
            
            // Add event listeners to checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', updatePermissionCards);
            });
            
            // Auto-select custom preset when manually changing permissions
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    document.querySelectorAll('.preset-role-card').forEach(card => {
                        card.classList.remove('selected');
                    });
                    document.querySelector('[data-preset="custom"]').classList.add('selected');
                });
            });
            
            // Tooltips for permission badges
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Form validation
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const userId = document.getElementById('user_id').value;
            const password = document.getElementById('password').value;
            
            if (!userId && !password) {
                e.preventDefault();
                alert('Password is required for new users');
                document.getElementById('password').focus();
                return false;
            }
            
            if (username.length < 3) {
                e.preventDefault();
                alert('Username must be at least 3 characters long');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Saving...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds in case of error
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + N for new user
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                resetForm();
                document.getElementById('username').focus();
            }
            
            // Escape to reset form
            if (e.key === 'Escape') {
                resetForm();
            }
        });
    </script>
</body>
</html>