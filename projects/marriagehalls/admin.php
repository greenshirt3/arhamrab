<?php
// Simple Security
session_start();
if(isset($_POST['pass']) && $_POST['pass']=='ridergo') $_SESSION['admin']=true;
if(!isset($_SESSION['admin'])) { echo '<form method="post" style="text-align:center; padding:50px;"><input type="password" name="pass" placeholder="Password"><button>Login</button></form>'; exit; }

$file = 'data/config.json';
// SAVE LOGIC
if(isset($_POST['save'])) {
    file_put_contents($file, $_POST['json_data']);
    echo "<script>alert('Settings Saved!'); window.location='admin.php';</script>";
}

$json = file_get_contents($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f8f9fa; padding: 20px; } textarea { font-family: monospace; font-size: 14px; }</style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>⚙️ Hall Configuration</h2>
            <a href="index.php" target="_blank" class="btn btn-outline-primary">View Website</a>
        </div>
        
        <div class="alert alert-info">
            <b>For Owners:</b> Edit the text below to change your website prices, menus, and colors instantly.
            Be careful with commas and brackets!
        </div>

        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Configuration Data (JSON)</label>
                <textarea name="json_data" class="form-control" rows="20"><?php echo $json; ?></textarea>
            </div>
            <button name="save" class="btn btn-success fw-bold w-100 p-3">SAVE CHANGES</button>
        </form>
    </div>
</body>
</html>