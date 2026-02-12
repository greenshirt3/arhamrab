<?php 
// 1. ENABLE ERROR REPORTING & SET TIMEZONE
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');

require 'includes/header.php'; 

// 2. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 3. FETCH DATA
// We fetch only active records first to keep it fast
$stmt = $pdo->query("SELECT * FROM beneficiaries ORDER BY last_visit DESC LIMIT 200");
$bens = $stmt->fetchAll();
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold"><i class="fas fa-users text-info me-2"></i> Beneficiaries List</h3>
        <p class="text-muted">Track BISP & Cash customers.</p>
    </div>
    <div class="col-md-6">
        <input type="text" id="benSearch" class="form-control form-control-lg border-primary shadow-sm" placeholder="Search by CNIC or Name...">
    </div>
</div>

<div class="glass-panel p-0 overflow-hidden bg-white shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle" id="benTable">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4">CNIC Number</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Last Visit</th>
                    <th class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bens) > 0): ?>
                    <?php foreach($bens as $b): 
                        // FIX: Check if date exists before formatting
                        if (!empty($b['last_visit'])) {
                            $visit_date = date('d M Y', strtotime($b['last_visit']));
                        } else {
                            $visit_date = '<span class="text-muted small">First Visit</span>';
                        }
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold font-monospace text-primary fs-5">
                            <?php echo htmlspecialchars($b['cnic']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($b['name'] ?? 'Unknown'); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($b['phone'] ?? '-'); ?>
                        </td>
                        <td>
                            <?php echo $visit_date; ?>
                        </td>
                        <td class="text-end pe-4">
                            <span class="badge bg-success rounded-pill px-3">Active</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3 text-secondary"></i><br>
                            No beneficiaries found yet. <br>
                            <small>They will appear here automatically when you issue a Payout.</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// CLIENT-SIDE SEARCH SCRIPT
document.getElementById('benSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let table = document.getElementById('benTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        // Search in CNIC (col 0) and Name (col 1)
        let tdCNIC = tr[i].getElementsByTagName('td')[0];
        let tdName = tr[i].getElementsByTagName('td')[1];
        
        if (tdCNIC || tdName) {
            let txtCNIC = tdCNIC.textContent || tdCNIC.innerText;
            let txtName = tdName.textContent || tdName.innerText;
            
            if (txtCNIC.toUpperCase().indexOf(filter) > -1 || txtName.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>