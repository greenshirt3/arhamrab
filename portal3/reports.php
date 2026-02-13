<?php 
require 'includes/header.php'; 
// Ensure only Admin
if ($_SESSION['role'] !== 'admin') { die("ACCESS DENIED"); }

$start_date = $_GET['start'] ?? date('Y-m-d', strtotime('-7 days'));
$end_date = $_GET['end'] ?? date('Y-m-d');

// SQL for Table
$rows = $pdo->prepare("SELECT t.*, b.name, b.cnic, u.username FROM transactions t JOIN beneficiaries b ON t.beneficiary_id = b.id LEFT JOIN users u ON t.agent_id = u.id WHERE DATE(t.created_at) BETWEEN ? AND ? ORDER BY t.created_at DESC");
$rows->execute([$start_date, $end_date]);
$data = $rows->fetchAll();

// SQL for Chart
$chart_sql = $pdo->prepare("SELECT DATE(created_at) as date, SUM(amount) as total FROM transactions WHERE DATE(created_at) BETWEEN ? AND ? AND status='success' GROUP BY DATE(created_at)");
$chart_sql->execute([$start_date, $end_date]);
$chart_data = $chart_sql->fetchAll();

$dates = []; $totals = [];
foreach($chart_data as $c) { $dates[] = $c['date']; $totals[] = $c['total']; }
?>

<link rel="stylesheet" href="css/modern.css">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold"><i class="fas fa-chart-pie me-2 text-primary"></i> Financial Reports</h3>
    
    <form class="d-flex gap-2 glass-panel p-2">
        <input type="date" name="start" class="form-control form-control-sm border-0 bg-transparent text-dark fw-bold" value="<?php echo $start_date; ?>">
        <span class="align-self-center text-muted">-</span>
        <input type="date" name="end" class="form-control form-control-sm border-0 bg-transparent text-dark fw-bold" value="<?php echo $end_date; ?>">
        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Filter</button>
    </form>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="glass-panel p-4">
            <h5 class="fw-bold mb-3">Disbursement Trend</h5>
            <canvas id="payoutChart" height="80"></canvas>
        </div>
    </div>
</div>

<div class="glass-panel p-0 overflow-hidden">
    <div class="p-3 bg-light border-bottom d-flex justify-content-between">
        <h6 class="fw-bold m-0 text-uppercase text-secondary">Transaction History</h6>
        <span class="badge bg-primary"><?php echo count($data); ?> Records</span>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light text-secondary small text-uppercase">
                <tr>
                    <th class="ps-4">Date</th>
                    <th>Beneficiary</th>
                    <th>CNIC</th>
                    <th>Amount</th>
                    <th>Agent</th>
                    <th class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $r): ?>
                <tr class="<?php echo ($r['status']=='void')?'opacity-50 text-decoration-line-through':''; ?>">
                    <td class="ps-4 fw-bold text-muted"><?php echo date('d M, h:i A', strtotime($r['created_at'])); ?></td>
                    <td class="fw-bold text-dark"><?php echo $r['name']; ?></td>
                    <td><span class="badge bg-light text-dark border"><?php echo $r['cnic']; ?></span></td>
                    <td class="text-success fw-bold">Rs. <?php echo number_format($r['amount']); ?></td>
                    <td><small class="text-uppercase"><?php echo ucfirst($r['username']); ?></small></td>
                    <td class="text-end pe-4">
                        <?php if($r['status'] == 'success'): ?>
                            <a href="print_receipt.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle" title="Print"><i class="fas fa-print"></i></a>
                            <a href="void_transaction.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger rounded-circle" title="Void"><i class="fas fa-ban"></i></a>
                        <?php else: ?>
                            <span class="badge bg-danger">VOIDED</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($data)==0): ?>
                    <tr><td colspan="6" class="text-center p-5 text-muted">No transactions found for this period.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// RENDER MODERN CHART
const ctx = document.getElementById('payoutChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Total Disbursed (Rs)',
            data: <?php echo json_encode($totals); ?>,
            borderColor: '#00E5FF',
            backgroundColor: 'rgba(0, 229, 255, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { borderDash: [5, 5] }, beginAtZero: true },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>