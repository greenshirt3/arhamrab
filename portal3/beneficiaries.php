<?php 
require 'includes/header.php'; 
requireAuth();

// Handle Search
$search = $_GET['search'] ?? '';
$sql = "SELECT b.*, 
        (SELECT COUNT(*) FROM transactions t WHERE t.beneficiary_id = b.id AND t.status='success') as txn_count,
        (SELECT SUM(amount) FROM transactions t WHERE t.beneficiary_id = b.id AND t.status='success') as total_received
        FROM beneficiaries b 
        WHERE b.name LIKE ? OR b.cnic LIKE ? OR b.phone LIKE ?
        ORDER BY b.id DESC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$rows = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/modern.css">

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold text-primary"><i class="fas fa-users me-2"></i> Beneficiary Database</h3>
    </div>
    <div class="col-md-6">
        <form class="glass-panel p-2 d-flex">
            <input type="text" id="global_search" name="search" class="form-control border-0 bg-transparent" placeholder="Search by CNIC, Name, or Phone..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="glass-panel p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light text-secondary small text-uppercase">
                <tr>
                    <th class="ps-4">Beneficiary Info</th>
                    <th>CNIC Number</th>
                    <th>History</th>
                    <th>Total Payout</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($rows) > 0): ?>
                    <?php foreach($rows as $r): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    <?php echo strtoupper(substr($r['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark"><?php echo $r['name']; ?></div>
                                    <div class="small text-muted"><?php echo $r['phone'] ?: 'No Phone'; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo $r['cnic']; ?></span></td>
                        <td><span class="badge bg-info text-dark"><?php echo $r['txn_count']; ?> Visits</span></td>
                        <td class="text-success fw-bold">Rs. <?php echo number_format($r['total_received']); ?></td>
                        <td class="text-end pe-4">
                            <a href="payout.php?cnic=<?php echo $r['cnic']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                Pay Now <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center p-5 text-muted">No beneficiaries found. Try a different search.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#global_search").autocomplete({
        source: "api_search.php?type=all",
        minLength: 2,
        select: function(event, ui) {
            $("#global_search").val(ui.item.value);
            $("form").submit(); 
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>