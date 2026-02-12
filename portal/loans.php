<?php 
require 'includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Access: Admin or Loans Manager
if (!has_perm('admin') && !has_perm('loans')) {
    die("<div class='alert alert-danger m-5'>Access Denied</div>");
}

// FETCH CUSTOMERS
$customers = $pdo->query("SELECT * FROM loans ORDER BY person_name ASC")->fetchAll();
?>

<div class="row">
    <div class="col-md-12">
        <div class="glass-panel p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold"><i class="fas fa-book me-2"></i> Ledger / Udhaar Book</h4>
                <button class="btn btn-primary btn-sm" onclick="alert('Feature coming soon: Add New Customer')">+ New Customer</button>
            </div>

            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Current Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($customers as $c): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($c['person_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['phone']); ?></td>
                        <td class="small text-muted"><?php echo htmlspecialchars($c['address']); ?></td>
                        <td class="fw-bold <?php echo $c['total_amount'] > 0 ? 'text-danger' : 'text-success'; ?>">
                            Rs. <?php echo number_format($c['total_amount']); ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-dark">View Details</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>