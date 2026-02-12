
<?php
require_once __DIR__.'/auth.php';
guard();

// Quick stats
$todayIn  = (float) ($pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='Credit' AND DATE(happened_at)=CURDATE()")->fetchColumn());
$todayOut = (float) ($pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='Debit'  AND DATE(happened_at)=CURDATE()")->fetchColumn());
$net      = $todayIn - $todayOut;

$waiting  = (int) ($pdo->query("SELECT COUNT(*) FROM tokens WHERE status='waiting'")->fetchColumn());
$serving  = $pdo->query("SELECT token_number FROM tokens WHERE status='serving' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: '--';

?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars(APP_NAME)?> · Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
  body{background:#0f1011;color:#eaeaea}
  .card{background:#16181a;border:1px solid #2a2d31}
  .display-number{font-size:4.5rem;font-weight:800;color:#00f5a0}
</style>
<?php if (FEATURE_QZ): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qz-tray/2.2.4/qz-tray.js"></script>
<script>
// QZ: establish on load (will prompt the first time)
document.addEventListener('DOMContentLoaded', () => {
  if (!qz) return;
  qz.websocket.connect().catch(err => console.log('QZ connect:', err));
});
function printTokenViaQZ(num) {
  // ESC/POS minimal text
  const esc = '\x1B', gs = '\x1D';
  const lines = [
    esc + '@',                         // init
    esc + '!' + '\x38',                // double height+width + bold
    'ARHAM PRINTERS\n',
    esc + '!' + '\x20',                // double width
    'TOKEN: #' + num + '\n',
    esc + '!' + '\x00',
    new Date().toLocaleTimeString() + '\n\n',
    gs + 'V' + '\x00'                  // full cut
  ];
  const data = [{ type: 'raw', format: 'plain', data: lines.join('') }];
  const cfg = qz.configs.create('<?=PRN_THERMAL?>');
  qz.print(cfg, data).catch(console.error);
}
</script>
<?php endif; ?>
</head><body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand"><?=htmlspecialchars(APP_NAME)?></span>
    <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-light" href="tokens.php">Tokens</a>
      <a class="btn btn-sm btn-outline-light" href="transactions.php">Cashbook</a>
      <a class="btn btn-sm btn-outline-danger" href="?logout">Logout (<?=htmlspecialchars($_SESSION['uname'])?>)</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Quick actions</h5>
        <form class="d-grid gap-2" action="tokens.php" method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <input type="hidden" name="action" value="issue">
          <select name="service_type" class="form-select">
            <option value="BISP">BISP</option>
            <option value="Utility">Utility Bill</option>
            <option value="Printing">Printing</option>
            <option value="Other">Other</option>
          </select>
          <button class="btn btn-primary">🎫 Issue Token</button>
        </form>
        <hr>
        <form class="d-grid gap-2" action="transactions.php" method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <input type="hidden" name="action" value="quick-sale">
          <div class="input-group">
            <span class="input-group-text">Rs</span>
            <input name="amount" type="number" step="0.01" class="form-control" placeholder="Quick sale amount" required>
          </div>
          <input name="memo" class="form-control" placeholder="Memo (e.g., Lamination)">
          <button class="btn btn-success">💵 Add Sale</button>
        </form>
        <form class="d-grid gap-2 mt-2" action="transactions.php" method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <input type="hidden" name="action" value="quick-expense">
          <div class="input-group">
            <span class="input-group-text">Rs</span>
            <input name="amount" type="number" step="0.01" class="form-control" placeholder="Expense amount" required>
          </div>
          <input name="memo" class="form-control" placeholder="Memo (e.g., Paper Rim)">
          <button class="btn btn-warning">🧾 Add Expense</button>
        </form>
      </div>

      <div class="card p-3 mt-3">
        <h5>System status</h5>
        <div>Queue waiting: <strong><?=$waiting?></strong></div>
        <div>Now serving: <strong><?=$serving?></strong></div>
        <div>QZ Tray: <strong><?=FEATURE_QZ ? 'Enabled' : 'Disabled'?></strong></div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card p-4 text-center">
        <h3>Now Serving</h3>
        <div class="display-number"><?=$serving?></div>
        <div class="text-muted">Please wait for your turn</div>
      </div>

      <div class="row mt-3 g-3">
        <div class="col-md-4">
          <div class="card p-3 bg-success-subtle text-dark"><h6>Cash In (Today)</h6>
            <div class="fs-3">Rs <?=number_format($todayIn)?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 bg-danger-subtle text-dark"><h6>Cash Out (Today)</h6>
            <div class="fs-3">Rs <?=number_format($todayOut)?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 bg-info-subtle text-dark"><h6>Net Cash (Today)</h6>
            <div class="fs-3">Rs <?=number_format($net)?></div>
          </div>
        </div>
      </div>

      <div class="card p-3 mt-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="m-0">Recent Transactions (Today)</h5>
          <a class="btn btn-sm btn-outline-light" href="transactions.php">Open Cashbook</a>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-dark table-striped table-sm">
            <thead><tr><th>Time</th><th>Type</th><th>Source</th><th class="text-end">Amount</th><th>Memo</th></tr></thead>
            <tbody>
              <?php
              $rows = $pdo->query("SELECT happened_at, ttype, source, amount, LEFT(memo,80) memo FROM transactions WHERE DATE(happened_at)=CURDATE() ORDER BY id DESC LIMIT 8")->fetchAll();
              foreach($rows as $r): ?>
                <tr>
                  <td><?=date('h:i A', strtotime($r['happened_at']))?></td>
                  <td><?=$r['ttype']?></td>
                  <td><?=$r['source']?></td>
                  <td class="text-end"><?=number_format($r['amount'],2)?></td>
                  <td><?=htmlspecialchars($r['memo'] ?? '')?></td>
                </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// Auto-refresh key stats every 20 seconds (lightweight)
setTimeout(()=>location.reload(), 20000);
</script>
</body></html>
