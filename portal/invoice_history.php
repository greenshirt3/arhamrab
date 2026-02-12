<?php 
require 'includes/header.php'; 

// SEARCH LOGIC
$search = $_GET['search'] ?? '';
$where = "1";
$params = [];

if ($search) {
    // Search Customer, Invoice #, or even Amount
    $where .= " AND (customer_name LIKE ? OR invoice_no LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// FETCH DATA
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE $where ORDER BY id DESC LIMIT 50");
$stmt->execute($params);
$invoices = $stmt->fetchAll();

// CUSTOMER INTELLIGENCE (If searching a person)
$cust_stats = null;
if ($search && count($invoices) > 0) {
    $cust_name = $invoices[0]['customer_name'];
    $sql = "SELECT 
            SUM(subtotal) as lifetime_sales, 
            SUM(paid_amount) as lifetime_paid,
            (SELECT (total_amount - paid_amount) FROM loans WHERE person_name = ? AND status='active') as current_debt 
            FROM invoices WHERE customer_name = ?";
    $cstmt = $pdo->prepare($sql);
    $cstmt->execute([$cust_name, $cust_name]);
    $cust_stats = $cstmt->fetch();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary"><i class="fas fa-history"></i> Invoice Archives</h3>
    <form class="glass-panel p-2 d-flex" style="margin-bottom:0;">
        <input type="text" name="search" class="form-control border-0 bg-transparent fw-bold" placeholder="Search Invoice or Customer..." value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-dark rounded-pill px-4"><i class="fas fa-search"></i></button>
    </form>
</div>

<?php if($cust_stats): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-panel p-4" style="background: linear-gradient(135deg, #111 0%, #2c3e50 100%); color: white;">
            <div class="row align-items-center">
                <div class="col-md-4 border-end border-secondary">
                    <small class="text-uppercase text-white-50" style="letter-spacing:1px;">Customer Profile</small>
                    <h2 class="fw-bold text-info m-0" style="color: var(--brand-cyan) !important;"><?php echo $invoices[0]['customer_name']; ?></h2>
                </div>
                <div class="col-md-8">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-white-50">Lifetime Business</small>
                            <h4 class="fw-bold">Rs. <?php echo number_format($cust_stats['lifetime_sales']); ?></h4>
                        </div>
                        <div class="col-4">
                            <small class="text-white-50">Total Paid</small>
                            <h4 class="fw-bold text-success">Rs. <?php echo number_format($cust_stats['lifetime_paid']); ?></h4>
                        </div>
                        <div class="col-4">
                            <small class="text-white-50">Current Debt</small>
                            <h4 class="fw-bold text-danger">Rs. <?php echo number_format($cust_stats['current_debt'] ?? 0); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="glass-panel p-0 overflow-hidden">
    <table class="table table-hover mb-0 align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th class="ps-4">Invoice #</th>
                <th>Date</th>
                <th>Customer / Supplier</th>
                <th>Type</th>
                <th>Total Bill</th>
                <th>Paid</th>
                <th>Status</th>
                <th class="text-end pe-4">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($invoices as $r): 
                $due = $r['grand_total'] - $r['paid_amount'];
            ?>
            <tr>
                <td class="ps-4 fw-bold" style="color: var(--brand-cyan);"><?php echo $r['invoice_no']; ?></td>
                <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                <td class="fw-bold"><?php echo $r['customer_name']; ?></td>
                <td><span class="badge bg-secondary text-uppercase"><?php echo $r['type']; ?></span></td>
                <td class="fw-bold">Rs. <?php echo number_format($r['grand_total']); ?></td>
                <td class="text-success">Rs. <?php echo number_format($r['paid_amount']); ?></td>
                <td>
                    <?php if($due > 0): ?>
                        <span class="badge bg-danger">DUE: <?php echo number_format($due); ?></span>
                    <?php else: ?>
                        <span class="badge bg-success">PAID</span>
                    <?php endif; ?>
                </td>
                <td class="text-end pe-4">
                    <a href="invoice.php?id=<?php echo $r['invoice_no']; ?>" class="btn btn-sm btn-dark rounded-pill px-3">
                        View Invoice
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>