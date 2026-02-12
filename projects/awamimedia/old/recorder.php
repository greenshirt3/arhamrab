<?php
include 'config.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = json_decode(file_get_contents($dataFile), true);
$video = null;
foreach ($data as $v) { if ($v['id'] == $id) { $video = $v; break; } }
if (!$video) die("Video not found.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Studio Recorder</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; overflow: hidden; }
        .toolbar { position: fixed; top: 0; z-index: 999; width: 100%; background: #222; padding: 10px; display: flex; gap: 10px; justify-content: center; }
        button { cursor: pointer; padding: 10px 20px; font-weight: bold; border: none; border-radius: 4px; }
    </style>
</head>
<body>

<div class="toolbar">
    <button id="startBtn" style="background:red; color:white;">● REC</button>
    <button id="stopBtn" style="background:white; color:black; display:none;">■ STOP & SAVE</button>
</div>

<div class="broadcast-screen" style="margin-top: 60px;">
    <iframe src="https://www.youtube.com/embed/<?php echo $video['youtube_id']; ?>?modestbranding=1&rel=0&controls=1" allowfullscreen></iframe>
    <div class="overlay-layer">
        <?php if(!empty($video['top_ticker'])): ?>
            <div class="top-ticker"><div class="top-text"><?php echo htmlspecialchars($video['top_ticker']); ?></div></div>
        <?php endif; ?>
        <img src="logo.png" class="brand-logo">
        <div class="bottom-ticker-area">
            <div class="breaking-label"><?php echo htmlspecialchars($video['label_text']); ?></div>
            <div class="scrolling-area" style="background: <?php echo htmlspecialchars($video['ribbon_color']); ?>;">
                <div class="scrolling-text"><?php echo htmlspecialchars($video['bottom_ticker']); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
    let mediaRecorder, chunks = [];
    document.getElementById('startBtn').onclick = async () => {
        try {
            const stream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true });
            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = e => { if(e.data.size > 0) chunks.push(e.data); };
            mediaRecorder.onstop = () => {
                const blob = new Blob(chunks, { type: 'video/mp4' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'News_Broadcast.mp4';
                a.click();
                chunks = [];
                document.getElementById('startBtn').style.display = 'block';
                document.getElementById('stopBtn').style.display = 'none';
            };
            mediaRecorder.start();
            document.getElementById('startBtn').style.display = 'none';
            document.getElementById('stopBtn').style.display = 'block';
        } catch(e) { alert("Recording Failed: " + e); }
    };
    document.getElementById('stopBtn').onclick = () => mediaRecorder.stop();
</script>

</body>
</html>
