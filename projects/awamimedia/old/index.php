<?php
session_start();
$PASSWORD = "arham2026"; 

if (isset($_POST['pass']) && $_POST['pass'] === $PASSWORD) {
    $_SESSION['access'] = true;
    header("Location: index.php"); exit();
}

if (!isset($_SESSION['access'])) {
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <body style="background:#000; color:white; font-family:sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;">
        <form method="post" style="text-align:center; padding:20px; border:1px solid #333; border-radius:10px; background:#111;">
            <h3 style="color:red; margin:0 0 10px;">STUDIO LOGIN</h3>
            <input type="password" name="pass" placeholder="Password" style="padding:10px; width:100%; box-sizing:border-box;">
            <button style="margin-top:10px; padding:10px; width:100%; background:red; color:white; border:none; font-weight:bold;">ENTER</button>
        </form>
    </body>
    <?php exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arham Studio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="canvas-container">
    <canvas id="newsCanvas" width="1280" height="720"></canvas>
    <div id="liveBadge">LIVE</div>
</div>

<div class="controls">
    
    <button id="btnRec" class="btn-big-red">START RECORDING</button>
    <div id="timer" style="color:red; text-align:center; font-family:monospace; margin-top:5px;">00:00</div>

    <hr style="border-color:#333; opacity:0.3; margin:15px 0;">

    <div class="row">
        <button class="btn-gray" onclick="startCamera()">📷 Use Camera</button>
        <button class="btn-gray" onclick="document.getElementById('fileIn').click()">📂 Upload Video</button>
    </div>
    <input type="file" id="fileIn" accept="video/*" style="display:none;">

    <div class="group">
        <label>TOP TICKER (YELLOW)</label>
        <input type="text" id="inTop" value="BREAKING: GOLD PRICE REACHES ALL TIME HIGH...">
    </div>

    <div class="group">
        <label>BOTTOM TICKER (URDU)</label>
        <input type="text" id="inBottom" value="یہاں اپنی اہم خبر لکھیں..." style="text-align:right;">
    </div>

    <div class="row">
        <div class="group" style="width:48%">
            <label>SLUG / LOCATION</label>
            <input type="text" id="inSlug" value="ISLAMABAD">
        </div>
        <div class="group" style="width:48%">
            <label>THEME COLOR</label>
            <input type="color" id="inColor" value="#cc0000" style="width:100%; height:35px;">
        </div>
    </div>
    
    <div class="group">
        <label>LOGO IMAGE</label>
        <input type="file" id="inLogo" accept="image/*">
    </div>

</div>

<video id="sourceVideo" autoplay playsinline loop muted style="display:none;"></video>
<img id="sourceLogo" src="logo.png" style="display:none;">

<script src="script.js"></script>
</body>
</html>
