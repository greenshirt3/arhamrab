<?php 
require 'includes/header.php'; 

// --- SECURITY CHECK ---
if ($_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger text-center mt-5'>⛔ ACCESS DENIED: Only Admins can manage users.</div>";
    include 'includes/footer.php';
    exit();
}
// ----------------------

$msg = "";

// HANDLE ACTIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete User
    if (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_POST['delete_id']]);
        $msg = "User deleted successfully.";
    } 
    // Add/Update User
    else {
        $name = $_POST['full_name'];
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $role = $_POST['role'];

        if (!empty($pass)) {
            // New Password/User
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE full_name=?, password=?, role=?");
            $stmt->execute([$name, $user, $hash, $role, $name, $hash, $role]);
        } else {
            // Update Details Only
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, role=? WHERE username=?");
            $stmt->execute([$name, $role, $user]);
        }
        $msg = "User saved successfully!";
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white"><i class="fas fa-user-plus"></i> Add / Edit User</div>
            <div class="card-body">
                <?php if($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="e.g. Khadija Bibi">
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="e.g. khadija">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Only if changing">
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="staff">Staff (Limited)</option>
                            <option value="admin">Admin (Full Access)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Save User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">Current Users</div>
            <table class="table table-striped mb-0">
                <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $users = $pdo->query("SELECT * FROM users")->fetchAll();
                    foreach($users as $u) {
                        $badge = ($u['role'] == 'admin') ? 'bg-danger' : 'bg-info';
                        echo "<tr>
                            <td>{$u['full_name']}</td>
                            <td>{$u['username']}</td>
                            <td><span class='badge $badge'>" . strtoupper($u['role']) . "</span></td>
                            <td>
                                <form method='post' onsubmit='return confirm(\"Delete?\");' style='display:inline'>
                                    <input type='hidden' name='delete_id' value='{$u['id']}'>
                                    <button class='btn btn-sm btn-outline-danger'><i class='fas fa-trash'></i></button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
