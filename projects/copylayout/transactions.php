
<?php
require_once __DIR__.'/auth.php';

// Handle webhook (from phone SMS forwarder) — NO session required
if (($_GET['action'] ?? '') === 'webhook') {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('POST only'); }
  $raw = file_get_contents('php://input');
  $json = json_decode($raw, true);
  if (!is_array($json)) { http_response_code(400); exit('bad json'); }
  if (($json['secret'] ?? '') !== WEBHOOK_SHARED_SECRET) { http_response_code(403); exit('forbidden'); }
  // Expecting: {"secret":"...","message":"<full sms text>"}
  $sms = (string)($json['message'] ?? '');
  // Parse amount (e.g., "Rs. 10,500" or "Rs 10500")
  $amount = 0.0;
  if (preg_match('/Rs\.?\s*([0-9,]+(\.[0-9]{1,2})?)/i', $sms, $m)) {
    $amount = (float) str_replace([','], [''], $m[1]);
  }
  // Parse TRX ID
  $trx = null;
  if (preg_match('/Trx\s*ID[:\s]+([A-Za-z0-9\-]+)/i', $sms, $m)) {
    $trx = $m[1];
  }
  // Determine direction
  $lower = strtolower($sms);
  $ttype = (strpos($lower, 'received') !== false || strpos($lower, 'deposit') !== false) ? 'Credit' : 'Debit';

  if ($amount > 0) {
    $stmt = $pdo->prepare("INSERT INTO transactions (trx_id, source, ttype, amount, memo) VALUES (?,?,?,?,?)");
    $stmt->execute([$trx, 'HBL', $ttype, $amount, substr($sms,0,250)]);
    echo json_encode(['ok'=>true,'amount'=>$amount,'type'=>$ttype]); exit;
  }
  echo json_encode(['ok'=>false,'reason'=>'no amount']); exit;
}

// From here: UI requires auth
guard();
csrf_check();

// Quick add forms
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $act = $_POST['action'] ?? '';
  if ($act==='quick-sale') {
    $amt = max(0, (float)$_POST['amount']);
    $memo= trim($_POST['memo'] ?? '');
    if ($amt>0) {
      $pdo->prepare("INSERT INTO ledger (ltype, amount, memo, user_id) VALUES ('Sale',?,?,?)")
          ->execute([$amt, $memo, $_SESSION['uid']]);
      $pdo->prepare("INSERT INTO transactions (source, ttype, amount, memo) VALUES ('Cash','Credit',?,?)")
          ->execute([$amt, $memo]);
    }
    header('Location: transactions.php'); exit;
  }
  if ($act==='quick-expense') {
    $amt = max(0, (float)$_POST['amount']);
    $memo= trim($_POST['memo'] ?? '');
    if ($amt>0) {
      $pdo->prepare("INSERT INTO ledger (ltype, amount, memo, user_id) VALUES ('Expense',?,?,?)")
          ->execute([$amt, $memo, $_SESSION['uid']]);
      $pdo->prepare("INSERT INTO transactions (source, ttype, amount, memo) VALUES ('Cash','Debit',?,?)")
          ->execute([$amt, $memo]);
    }
    header('Location: transactions.php'); exit;
  }
}

// Filters
$day   = $_GET['day'] ?? date('Y-m-d');
$rows  = $pdo->prepare("SELECT * FROM transactions WHERE DATE(happened_at)=? ORDER BY id DESC");
$rows->execute([$day]);
$data  = $rows->fetchAll();

$in  = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='Credit' AND DATE(happened_at)=?");
$in->execute([$day]);   $sumIn  = (float)$in->fetchColumn();
$out = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE ttype='Debit'  AND DATE(happened_at)=?");
$out->execute([$day]);  $sumOut = (float)$out->fetchColumn();
$net = $sumIn - $sumOut;

?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cashbook · <?=htmlspecialchars(APP_NAME)?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
  body{background:#0f1011;color:#eaeaea}
  .card{background:#16181a;border:1px solid #2a2d31}
</style>
</head><body>
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3>Cashbook & Webhook</h3>
    <div class="btn-group">
      <a class="btn btn-outline-light" href="index.php">Dashboard</a>
      <a class="btn btn-outline-light" href="tokens.php">Tokens</a>
      <a class="btn btn-outline-danger" href="auth.php?logout=1">Logout</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card p-3">
        <h5>Quick Sale</h5>
        <form method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <input type="hidden" name="action" value="quick-sale">
          <div class="input-group mb-2"><span class="input-group-text">Rs</span>
            <input name="amount" type="number" step="0.01" class="form-control" required></div>
          <input name="memo" class="form-control mb-2" placeholder="Memo (e.g., Lamination)">
          <button class="btn btn-success w-100">Add Sale</button>
        </form>
      </div>
      <div class="card p-3 mt-3">
        <h5>Quick Expense</h5>
        <form method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <input type="hidden" name="action" value="quick-expense">
          <div class="input-group mb-2"><span class="input-group-text">Rs</span>
            <input name="amount" type="number" step="0.01" class="form-control" required></div>
          <input name="memo" class="form-control mb-2" placeholder="Memo (e.g., A4 Rim)">
          <button class="btn btn-warning w-100">Add Expense</button>
        </form>
      </div>

      <div class="card p-3 mt-3">
        <h5>HBL Webhook URL</h5>
        <code><?=htmlspecialchars((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']))?>/transactions.php?action=webhook</code>
        <p class="small text-muted mt-2">
          Configure your Android SMS forwarder to POST JSON:<br>
          { "secret": "<?=WEBHOOK_SHARED_SECRET?>", "message": "%msg_content%" }
        </p>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card p-3">
        <form class="row g-2">
          <div class="col-auto"><h5 class="m-0">Transactions</h5></div>
          <div class="col-auto">
            <input type="date" name="day" class="form-control" value="<?=$day?>">
          </div>
          <div class="col-auto">
            <button class="btn btn-outline-light">Filter</button>
          </div>
          <div class="col ms-auto text-end">
            <span class="badge bg-success">IN: Rs <?=number_format($sumIn)?></span>
            <span class="badge bg-danger">OUT: Rs <?=number_format($sumOut)?></span>
            <span class="badge bg-info text-dark">NET: Rs <?=number_format($net)?></span>
          </div>
        </form>

        <div class="table-responsive mt-2">
          <table class="table table-dark table-striped table-sm">
            <thead><tr><th>Time</th><th>Type</th><th>Source</th><th class="text-end">Amount</th><th>Memo / Trx</th></tr></thead>
            <tbody>
              <?php foreach($data as $r): ?>
                <tr>
                  <td><?=date('Y-m-d h:i A', strtotime($r['happened_at']))?></td>
                  <td><?=$r['ttype']?></td>
                  <td><?=$r['source']?></td>
                  <td class="text-end"><?=number_format($r['amount'],2)?></td>
                  <td><?=htmlspecialchars($r['memo']??'')?> <?= $r['trx_id'] ? '<span class="badge bg-secondary">TRX '.$r['trx_id'].'</span>' : '' ?></td>
                </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
</body></html>
