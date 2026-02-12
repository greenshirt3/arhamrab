
<?php
require_once __DIR__.'/db.php';
session_name(SESSION_NAME);
session_start();

// ---------- Helpers ----------
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}
function csrf_check() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
    if (!$ok) { http_response_code(403); exit('Invalid CSRF'); }
  }
}
function authed(): bool { return !empty($_SESSION['uid']); }
function guard() { if (!authed()) { header('Location: ?login'); exit; } }

// ---------- Bootstrap a default admin (first run) ----------
try {
  $pdo->query("SELECT 1 FROM users LIMIT 1");
} catch (Throwable $e) {
  // Create tables on first run
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) UNIQUE NOT NULL,
      pass_hash VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    INSERT INTO users (username, pass_hash)
    VALUES ('admin', '".password_hash('admin123', PASSWORD_DEFAULT)."')
    ON DUPLICATE KEY UPDATE username=username;

    CREATE TABLE IF NOT EXISTS tokens (
      id INT AUTO_INCREMENT PRIMARY KEY,
      token_number INT NOT NULL,
      service_type ENUM('BISP','Utility','Printing','Other') NOT NULL,
      status ENUM('waiting','serving','done','cancelled') NOT NULL DEFAULT 'waiting',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS transactions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      trx_id VARCHAR(64),
      source ENUM('HBL','Cash','Nadra','Other') NOT NULL,
      ttype  ENUM('Debit','Credit') NOT NULL, -- Debit: money out; Credit: money in
      amount DECIMAL(12,2) NOT NULL,
      memo TEXT,
      happened_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX (source), INDEX (ttype), INDEX (happened_at), INDEX (trx_id)
    );

    CREATE TABLE IF NOT EXISTS ledger (
      id INT AUTO_INCREMENT PRIMARY KEY,
      ltype ENUM('Sale','Expense','Adjustment') NOT NULL,
      amount DECIMAL(12,2) NOT NULL,
      memo VARCHAR(255),
      happened_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      user_id INT,
      FOREIGN KEY (user_id) REFERENCES users(id)
    );
  ");
}

// ---------- Handle login/logout ----------
if (isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./');
  exit;
}

if (isset($_GET['login'])) {
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();
    if ($user && password_verify($p, $user['pass_hash'])) {
      $_SESSION['uid'] = $user['id'];
      $_SESSION['uname'] = $user['username'];
      header('Location: ./'); exit;
    }
    $err = "Invalid credentials";
  }
  // Login page
  ?>
  <!doctype html><html lang="en"><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login · <?=htmlspecialchars(APP_NAME)?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>body{background:#111;color:#eee} .card{background:#1b1b1b;border:1px solid #333}</style>
  </head><body class="d-flex align-items-center" style="min-height:100vh">
    <div class="container">
      <div class="row justify-content-center"><div class="col-md-4">
        <div class="card p-4">
          <h3 class="mb-3 text-center"><?=htmlspecialchars(APP_NAME)?></h3>
          <?php if(!empty($err)):?><div class="alert alert-danger"><?=$err?></div><?php endif;?>
          <form method="post">
            <input type="hidden" name="csrf" value="<?=csrf_token()?>">
            <div class="mb-3"><label class="form-label">Username</label>
              <input name="username" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required></div>
            <button class="btn btn-primary w-100">Sign in</button>
          </form>
          <p class="mt-3 small text-muted">Default: admin / admin123 (change later)</p>
        </div>
      </div></div>
    </div>
  </body></html>
  <?php exit;
}
