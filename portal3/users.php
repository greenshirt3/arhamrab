<?php 
require 'includes/header.php'; 
if ($_SESSION['role'] !== 'admin') { die("ACCESS DENIED"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_POST['delete_id']]);
    } else {
        $name = $_POST['full_name'];
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $role = $_POST['role'];
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)")->execute([$name, $user, $hash, $role]);
    }
}
$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<link rel="stylesheet" href="css/modern.css">

<div class="row g-4">
    <div class="col-md-4">
        <div class="glass-panel p-4">
            <h5 class="fw-bold mb-3">Add New Staff</h5>
            <form method="POST">
                <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>
                <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                <input type="text" name="password" class="form-control mb-2" placeholder="Password" required>
                <select name="role" class="form-select mb-3">
                    <option value="user">Staff</option>
                    <option value="admin">Admin</option>
                </select>
                <button class="btn btn-primary w-100 rounded-pill fw-bold">Create User</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="glass-panel p-0 overflow-hidden">
            <table class="table table-hover mb-0">
                <thead class="bg-light"><tr><th class="ps-4">Name</th><th>Username</th><th>Role</th><th></th></tr></thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?php echo $u['full_name']; ?></td>
                        <td><?php echo $u['username']; ?></td>
                        <td><span class="badge bg-<?php echo ($u['role']=='admin'?'danger':'info'); ?>"><?php echo strtoupper($u['role']); ?></span></td>
                        <td class="text-end pe-4">
                            <form method="post" onsubmit="return confirm('Delete?');">
                                <input type="hidden" name="delete_id" value="<?php echo $u['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>