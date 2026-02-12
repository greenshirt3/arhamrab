
<?php
require_once __DIR__.'/auth.php';
guard();
csrf_check();

// Handle actions
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $act = $_POST['action'] ?? '';
  if ($act==='issue') {
    $type = $_POST['service_type'] ?? 'Other';
    $last = $pdo->query("SELECT MAX(token_number) FROM tokens WHERE DATE(created_at)=CURDATE()")->fetchColumn();
    $num  = (int)$last + 1;
    $stmt = $pdo->prepare("INSERT INTO tokens (token_number, service_type, status) VALUES (?,?, 'waiting')");
    $stmt->execute([$num, $type]);
    $_SESSION['just_issued'] = $num;
    header('Location: tokens.php'); exit;
  }
  if ($act==='serve') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE tokens SET status='serving' WHERE id=?")->execute([$id]);
    header('Location: tokens.php'); exit;
  }
  if ($act==='done') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE tokens SET status='done' WHERE id=?")->execute([$id]);
    header('Location: tokens.php'); exit;
  }
  if ($act==='cancel') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE tokens SET status='cancelled' WHERE id=?")->execute([$id]);
    header('Location: tokens.php'); exit;
  }
}

// Fetch queues
$waiting = $pdo->query("SELECT * FROM tokens WHERE status='waiting' ORDER BY id ASC")->fetchAll();
$serving = $pdo->query("SELECT * FROM tokens WHERE status='serving' ORDER BY id DESC LIMIT 1")->fetch();
$recent  = $pdo->query("SELECT * FROM tokens WHERE status IN ('done','cancelled') ORDER BY id DESC LIMIT 10")->fetchAll();
$just    = $_SESSION['just_issued'] ?? null; unset($_SESSION['just_issued']);

?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tokens · <?=htmlspecialchars(APP_NAME)?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
  body{background:#0f1011;color:#eaeaea}
  .card{background:#16181a;border:1px solid #2a2d31}
  .display-number{font-size:4rem;font-weight:800;color:#00e0ff}
</style>
<?php if (FEATURE_QZ): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qz-tray/2.2.4/qz-tray.js"></script>
<script>
document.addEventListener('DOMContentLoaded', ()=>{ qz?.websocket.connect().catch(()=>{}); });
function printTokenQZ(num){ // thermal ESC/POS sample
  const esc='\x1B', gs='\x1D';
  const data=[{type:'raw',format:'plain',data: esc+'@'+esc+'!'+'\x38'+'ARHAM PRINTERS\n'+esc+'!'+'\x20'+'TOKEN #'+num+'\n'+esc+'!'+'\x00'+new Date().toLocaleTimeString()+ '\n\n'+gs+'V'+'\x00'}];
  const cfg=qz.configs.create('<?=PRN_THERMAL?>'); qz.print(cfg,data).catch(console.error);
}
</script>
<?php endif;?>
</head><body>
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3>Token Manager</h3>
    <div class="btn-group">
      <a class="btn btn-outline-light" href="index.php">Dashboard</a>
      <a class="btn btn-outline-light" href="transactions.php">Cashbook</a>
      <a class="btn btn-outline-danger" href="auth.php?logout=1">Logout</a>
    </div>
  </div>

  <?php if($just): ?>
    <div class="alert alert-success">Token #<?=$just?> issued.</div>
    <?php if (FEATURE_QZ): ?><script>printTokenQZ(<?=$just?>)</script><?php endif; ?>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Issue New Token</h5>
        <form method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <input type="hidden" name="action" value="issue">
          <select name="service_type" class="form-select mb-2">
            <option value="BISP">BISP</option>
            <option value="Utility">Utility</option>
            <option value="Printing">Printing</option>
            <option value="Other">Other</option>
          </select>
          <button class="btn btn-primary w-100">🎫 Issue</button>
        </form>
      </div>

      <div class="card p-3 mt-3">
        <h5>Now Serving</h5>
        <div class="display-number"><?=$serving['token_number'] ?? '--'?></div>
        <div class="text-muted"><?=$serving ? $serving['service_type'] : ''?></div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card p-3">
        <h5>Waiting Queue</h5>
        <div class="table-responsive">
          <table class="table table-dark table-striped table-sm align-middle">
            <thead><tr><th>#</th><th>Type</th><th>Since</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach($waiting as $w): ?>
              <tr>
                <td><?=$w['token_number']?></td>
                <td><?=$w['service_type']?></td>
                <td><?=date('h:i A', strtotime($w['created_at']))?></td>
                <td class="d-flex gap-1">
                  <form method="post" class="m-0">
                    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
                    <input type="hidden" name="action" value="serve">
                    <input type="hidden" name="id" value="<?=$w['id']?>">
                    <button class="btn btn-sm btn-warning">Serve</button>
                  </form>
                  <form method="post" class="m-0">
                    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="id" value="<?=$w['id']?>">
                    <button class="btn btn-sm btn-outline-danger">Cancel</button>
                  </form>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card p-3 mt-3">
        <h5>Recent (Done/Cancelled)</h5>
        <div class="table-responsive">
          <table class="table table-dark table-striped table-sm">
            <thead><tr><th>#</th><th>Type</th><th>Status</th><th>Time</th></tr></thead>
            <tbody>
              <?php foreach($recent as $r): ?>
                <tr>
                  <td><?=$r['token_number']?></td>
                  <td><?=$r['service_type']?></td>
                  <td><?=$r['status']?></td>
                  <td><?=date('h:i A', strtotime($r['created_at']))?></td>
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
// Auto-refresh queue board
setTimeout(()=>location.reload(), 15000);
</script>
</body></html>
