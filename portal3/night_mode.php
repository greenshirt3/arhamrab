<?php 
require 'includes/header.php'; 
requireAuth();

// CHECK ADMIN STATUS
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// --- ADMIN ACTIONS ---
if ($is_admin) {
    if (isset($_GET['mark_paid'])) {
        $pdo->prepare("UPDATE bill_queue SET status='paid' WHERE id=?")->execute([$_GET['mark_paid']]);
        header("Location: night_mode.php"); exit();
    }
    if (isset($_GET['delete_bill'])) {
        $pdo->prepare("DELETE FROM bill_queue WHERE id=?")->execute([$_GET['delete_bill']]);
        header("Location: night_mode.php"); exit();
    }
}

// FETCH DATA
$today = date('Y-m-d');
$bills = $pdo->query("SELECT * FROM bill_queue WHERE status='pending' ORDER BY created_at DESC")->fetchAll();

// STATS
$bisp_out = $pdo->query("SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = '$today' AND status='success'")->fetchColumn() ?: 0;
$bill_in = $pdo->query("SELECT SUM(amount) FROM bill_queue WHERE DATE(created_at) = '$today'")->fetchColumn() ?: 0;
$net_flow = $bisp_out - $bill_in; 
?>

<link rel="stylesheet" href="css/modern.css">

<div class="modal fade" id="auditModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel border-warning bg-dark">
            <div class="modal-header border-0">
                <h5 class="fw-bold text-warning"><i class="fas fa-search"></i> ENTRY DETAILS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center text-white">
                <div id="audit-result"></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold text-primary"><i class="fas fa-moon"></i> Night Shift</h3>
    <span class="badge bg-<?php echo $is_admin ? 'danger' : 'secondary'; ?>">
        Logged in as: <?php echo strtoupper($_SESSION['role'] ?? 'GUEST'); ?>
    </span>
</div>

<div class="row g-4">
    
    <?php if($is_admin): ?>
    <div class="col-12">
        <div class="glass-panel p-3 border-warning bg-dark">
            <div class="d-flex align-items-center gap-3">
                <div class="fw-bold text-warning text-nowrap"><i class="fas fa-shield-alt"></i> ADMIN AUDIT:</div>
                <div class="flex-grow-1 position-relative">
                    <input type="text" id="admin_search" class="form-control fw-bold bg-secondary text-white border-0" placeholder="Search any previous entry (Paid or Pending)...">
                    <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-md-4">
        <div class="glass-panel p-4 text-center bg-dark text-white border-secondary">
            <h6 class="fw-bold text-uppercase text-secondary mb-3">Today's Net Position</h6>
            
            <div class="d-flex justify-content-between mb-2 small">
                <span>BISP OUT (Dr):</span>
                <span class="text-danger fw-bold">- <?php echo number_format($bisp_out); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 small">
                <span>BILLS IN (Cr):</span>
                <span class="text-success fw-bold">+ <?php echo number_format($bill_in); ?></span>
            </div>
            
            <hr class="border-secondary">
            
            <h6 class="fw-bold">Required Bank Sync</h6>
            <h2 class="display-6 fw-bold <?php echo ($net_flow > 0 ? 'text-warning' : 'text-info'); ?>">
                Rs. <?php echo number_format(abs($net_flow)); ?>
            </h2>
            <small class="text-muted">
                <?php echo ($net_flow > 0) ? "WITHDRAW from Device" : "DEPOSIT to Device"; ?>
            </small>
        </div>
    </div>

    <div class="col-md-8">
        <div class="glass-panel p-0 overflow-hidden border-0">
            <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="m-0"><i class="fas fa-list-ul me-2"></i> PENDING QUEUE</h5>
                <span class="badge bg-light text-dark"><?php echo count($bills); ?> ITEMS</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small">
                        <tr>
                            <th class="ps-3">DETAILS</th>
                            <th>REFERENCE ID</th>
                            <th>AMOUNT</th>
                            <th class="text-end pe-3">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bills as $b): ?>
                        <tr id="row-<?php echo $b['id']; ?>">
                            <td class="ps-3">
                                <div class="fw-bold text-dark"><?php echo $b['consumer_name']; ?></div>
                                <span class="badge bg-light text-dark border"><?php echo $b['bill_type']; ?></span>
                            </td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 180px;">
                                    <input type="text" class="form-control font-monospace fw-bold bg-white" value="<?php echo $b['consumer_number']; ?>" id="ref-<?php echo $b['id']; ?>" readonly>
                                    <button class="btn btn-warning" onclick="copyText('<?php echo $b['id']; ?>')" title="Copy">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="fw-bold text-success">Rs. <?php echo number_format($b['amount']); ?></td>
                            <td class="text-end pe-3">
                                <?php if($is_admin): ?>
                                    <div class="btn-group">
                                        <a href="?mark_paid=<?php echo $b['id']; ?>" class="btn btn-sm btn-success" title="Mark Paid"><i class="fas fa-check"></i></a>
                                        <a href="?delete_bill=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')" title="Delete"><i class="fas fa-trash"></i></a>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-lock"></i> LOCKED</span>
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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-bold"><i class="fas fa-check-circle me-2"></i> Copied to Clipboard!</div>
    </div>
  </div>
</div>

<style>
/* Dropdown Styling */
.ui-autocomplete {
    max-height: 400px; overflow-y: auto; overflow-x: hidden;
    background: #212529; color: white; border: 1px solid #444;
    border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    z-index: 1060 !important;
}
.ui-menu-item .ui-menu-item-wrapper {
    padding: 12px; border-bottom: 1px solid #333;
}
.ui-menu-item .ui-menu-item-wrapper:hover {
    background: var(--brand-cyan); color: #000;
}
</style>

<script>
$(document).ready(function() {
    <?php if($is_admin): ?>
    // ADMIN AUTOCOMPLETE
    $("#admin_search").autocomplete({
        source: function(request, response) {
            $.getJSON("api_search.php", {
                type: 'bill_history',
                term: request.term
            }, response);
        },
        minLength: 0, // Autofetch on click
        select: function(event, ui) {
            // Show details in Modal
            var d = ui.item.data;
            var badge = (d.status === 'paid') ? '<span class="badge bg-success">PAID</span>' : '<span class="badge bg-warning text-dark">PENDING</span>';
            var html = `
                <div class="display-1 text-white mb-2">${badge}</div>
                <h3 class="text-white">${d.consumer_name}</h3>
                <h4 class="text-primary font-monospace">${d.consumer_number}</h4>
                <hr class="border-secondary">
                <div class="row text-start">
                    <div class="col-6 text-white-50">Type:</div><div class="col-6 fw-bold text-white text-end">${d.bill_type}</div>
                    <div class="col-6 text-white-50">Amount:</div><div class="col-6 fw-bold text-success text-end">Rs. ${d.amount}</div>
                    <div class="col-6 text-white-50">Date:</div><div class="col-6 fw-bold text-white text-end">${d.created_at}</div>
                </div>
            `;
            $('#audit-result').html(html);
            new bootstrap.Modal(document.getElementById('auditModal')).show();
            
            // Clear input
            $(this).val('');
            return false;
        }
    }).focus(function() {
        $(this).autocomplete("search", "");
    });
    <?php endif; ?>
});

function copyText(id) {
    var copyText = document.getElementById("ref-" + id);
    copyText.select();
    navigator.clipboard.writeText(copyText.value);
    
    var row = document.getElementById("row-" + id);
    row.style.transition = "background 0.5s";
    row.style.backgroundColor = "#d1e7dd";
    
    const toast = new bootstrap.Toast(document.getElementById('liveToast'));
    toast.show();
}
</script>

<?php include 'includes/footer.php'; ?>