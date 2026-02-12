<?php
session_start();
include 'config.php';

// ==========================================
// 1. ADMIN SECURITY SETTINGS (EDIT HERE)
// ==========================================
$admin_user = "admin";
$admin_pass = "arham2026"; 

// ==========================================
// 2. LOGOUT LOGIC
// ==========================================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// ==========================================
// 3. LOGIN LOGIC
// ==========================================
if (isset($_POST['login'])) {
    $user = $_POST['user_input'];
    $pass = $_POST['pass_input'];
    
    // Check against the settings above
    if ($user === $admin_user && $pass === $admin_pass) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "Wrong Username or Password!";
    }
}

// ==========================================
// 4. ACCESS CHECK (Show Login Form if not logged in)
// ==========================================
if (!isset($_SESSION['logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Studio Login</title>
        <style>
            body { background: #000; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .login-box { border: 1px solid #333; padding: 40px; border-radius: 8px; text-align: center; width: 300px; background: #111; box-shadow: 0 0 20px rgba(255,0,0,0.2); }
            input { width: 100%; padding: 12px; margin-bottom: 15px; background: #222; border: 1px solid #444; color: white; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #cc0000; color: white; border: none; font-weight: bold; cursor: pointer; }
            button:hover { background: #ff0000; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2 style="margin-top:0; color:#cc0000;">STUDIO CONTROL</h2>
            <form method="post">
                <input type="text" name="user_input" placeholder="Username" required>
                <input type="password" name="pass_input" placeholder="Password" required>
                <button type="submit" name="login">SECURE LOGIN</button>
            </form>
            <?php if(isset($error)) echo "<p style='color:red; margin-top:10px;'>$error</p>"; ?>
        </div>
    </body>
    </html>
    <?php
    exit(); // Stop loading the rest of the page
}

// ==========================================
// 5. DASHBOARD LOGIC (Only runs if logged in)
// ==========================================

// Load Data
$data = json_decode(file_get_contents($dataFile), true);
if (!$data) $data = [];

// Delete Feature
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $data = array_filter($data, function($v) use ($id) { return $v['id'] != $id; });
    file_put_contents($dataFile, json_encode(array_values($data)));
    header("Location: admin.php"); exit();
}

// Edit Mode Setup
$edit_mode = false; $item = [];
if (isset($_GET['edit'])) {
    foreach($data as $v) { if($v['id'] == $_GET['edit']) { $edit_mode = true; $item = $v; break; } }
}

// Save/Update Feature
if (isset($_POST['save'])) {
    $vid = getYoutubeID($_POST['url']);
    if ($vid) {
        $entry = [
            'id' => $edit_mode ? $item['id'] : time(),
            'title' => $_POST['title'],
            'youtube_id' => $vid,
            'top_ticker' => $_POST['top_ticker'],
            'bottom_ticker' => $_POST['bottom_ticker'],
            'intro_text' => $_POST['intro_text'],
            'label_text' => $_POST['label_text'],
            'ribbon_color' => $_POST['ribbon_color'],
            'created_at' => $edit_mode ? $item['created_at'] : date("Y-m-d H:i:s")
        ];
        
        if ($edit_mode) {
            foreach ($data as $k => $v) { if ($v['id'] == $entry['id']) { $data[$k] = $entry; break; } }
        } else {
            array_unshift($data, $entry);
        }
        file_put_contents($dataFile, json_encode(array_values($data)));
        header("Location: admin.php"); exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Studio Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .panel-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; padding: 20px; max-width: 1400px; margin: 0 auto; }
        .box { background: #1a1a1a; padding: 25px; border: 1px solid #333; border-radius: 5px; }
        input, textarea { width: 100%; background: #222; border: 1px solid #444; color: white; padding: 10px; margin-bottom: 15px; box-sizing: border-box; }
        label { font-size: 11px; color: #f1c40f; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 5px; }
        .btn-green { background: #27ae60; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; }
        .row-item { display: flex; justify-content: space-between; border-bottom: 1px solid #333; padding: 10px 0; font-size: 13px; }
        .action-link { margin-left: 10px; color: #aaa; text-decoration: none; font-size: 11px; border: 1px solid #444; padding: 2px 5px; }
        .action-link:hover { color: white; border-color: white; }
    </style>
</head>
<body>
    <div style="background:#000; padding:15px; border-bottom:2px solid #cc0000; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-weight:bold; font-size:18px;">BROADCAST <span style="color:#cc0000">CONTROLLER</span></span>
        <div><a href="index.php" style="color:white; margin-right:15px;">View Site</a> <a href="admin.php?logout=1" style="color:red;">Logout</a></div>
    </div>

    <div class="panel-grid">
        <div class="box">
            <h3 style="margin-top:0; border-bottom:1px solid #444; padding-bottom:10px;">
                <?php echo $edit_mode ? "EDITING MODE" : "NEW BROADCAST SETUP"; ?>
            </h3>
            <form method="post">
                <label>1. YouTube Video Link</label>
                <input type="text" name="url" value="<?php echo $edit_mode?$item['youtube_id']:''; ?>" required placeholder="https://youtube.com/...">

                <label>2. Main Headline (Title)</label>
                <input type="text" name="title" value="<?php echo $edit_mode?$item['title']:''; ?>" required>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div>
                        <label>3. Top Ticker (Yellow)</label>
                        <input type="text" name="top_ticker" value="<?php echo $edit_mode?$item['top_ticker']:''; ?>" placeholder="Business, Weather...">
                    </div>
                    <div>
                        <label>4. Location / Label</label>
                        <input type="text" name="label_text" value="<?php echo $edit_mode?$item['label_text']:'ISLAMABAD'; ?>">
                    </div>
                </div>

                <label>5. Intro / Event Paragraph (Left Side Overlay)</label>
                <textarea name="intro_text" rows="4" placeholder="Enter text here to show a summary box at the start of the video. Leave empty to hide."><?php echo $edit_mode?$item['intro_text']:''; ?></textarea>

                <label>6. Main Bottom Ticker (Urdu)</label>
                <textarea name="bottom_ticker" rows="2" style="text-align:right; font-size:16px;" required><?php echo $edit_mode?$item['bottom_ticker']:''; ?></textarea>

                <label>7. Theme Color</label>
                <input type="color" name="ribbon_color" value="<?php echo $edit_mode?$item['ribbon_color']:'#900000'; ?>" style="height:40px; padding:0;">

                <button name="save" class="btn-green"><?php echo $edit_mode ? "UPDATE SETTINGS" : "GO LIVE"; ?></button>
                <?php if($edit_mode): ?><a href="admin.php" style="display:block; text-align:center; margin-top:10px; color:#aaa;">Cancel</a><?php endif; ?>
            </form>
        </div>

        <div class="box">
            <h3 style="margin-top:0; border-bottom:1px solid #444; padding-bottom:10px;">ARCHIVE</h3>
            <?php foreach($data as $v): ?>
                <div class="row-item">
                    <div style="font-weight:bold; color:#f1c40f;"><?php echo htmlspecialchars($v['title']); ?></div>
                    <div>
                        <a href="recorder.php?id=<?php echo $v['id']; ?>" target="_blank" class="action-link">REC</a>
                        <a href="admin.php?edit=<?php echo $v['id']; ?>" class="action-link">EDIT</a>
                        <a href="admin.php?del=<?php echo $v['id']; ?>" class="action-link" style="color:#ff4444;" onclick="return confirm('Delete?');">DEL</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
