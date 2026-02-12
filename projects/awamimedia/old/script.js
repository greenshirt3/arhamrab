const canvas = document.getElementById('newsCanvas');
const ctx = canvas.getContext('2d');
const video = document.getElementById('sourceVideo');
const logo = document.getElementById('sourceLogo');

// STATE
let topText = document.getElementById('inTop').value;
let bottomText = document.getElementById('inBottom').value;
let slugText = document.getElementById('inSlug').value;
let themeColor = document.getElementById('inColor').value;
let logoImg = logo;

// UPDATE LISTENERS
document.getElementById('inTop').oninput = e => topText = e.target.value;
document.getElementById('inBottom').oninput = e => bottomText = e.target.value;
document.getElementById('inSlug').oninput = e => slugText = e.target.value;
document.getElementById('inColor').oninput = e => themeColor = e.target.value;

document.getElementById('inLogo').onchange = e => {
    const file = e.target.files[0];
    if(file) {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        logoImg = img;
    }
};

// 1. INPUT SOURCES
window.startCamera = async () => {
    try {
        // Mobile back camera preference
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: true });
        video.srcObject = stream;
        video.play();
    } catch(e) { alert("Camera Error: " + e); }
};

document.getElementById('fileIn').onchange = e => {
    const file = e.target.files[0];
    if(file) {
        video.srcObject = null;
        video.src = URL.createObjectURL(file);
        video.play();
    }
};

// 2. THE RENDER LOOP (Draws the TV Screen)
let tickerX = 1280;
let topX = 0;

function draw() {
    // A. Draw Video Layer
    if (video.readyState === 4) {
        ctx.drawImage(video, 0, 0, 1280, 720);
    } else {
        ctx.fillStyle = "#000";
        ctx.fillRect(0, 0, 1280, 720); // Black screen if no video
    }

    // B. Draw Top Bar (Yellow)
    ctx.fillStyle = "rgba(0,0,0,0.8)";
    ctx.fillRect(0, 0, 1280, 50);
    ctx.fillStyle = "#f1c40f";
    ctx.fillRect(0, 48, 1280, 2); // Yellow Line
    
    // Top Label
    ctx.fillStyle = "#f1c40f";
    ctx.fillRect(0, 0, 150, 50);
    ctx.fillStyle = "#000";
    ctx.font = "bold 20px Arial";
    ctx.fillText("UPDATES", 20, 32);

    // Top Ticker Text
    ctx.fillStyle = "#f1c40f";
    ctx.font = "24px Arial";
    ctx.fillText(topText, topX + 160, 34);
    topX -= 2; // Speed
    if (topX < -(ctx.measureText(topText).width + 200)) topX = 1280;

    // C. Draw Logo (Top Right)
    if(logoImg) ctx.drawImage(logoImg, 1150, 60, 100, 100);

    // D. Draw Bottom Complex
    const bottomY = 620;

    // Slug Box (Location)
    ctx.fillStyle = themeColor;
    ctx.beginPath();
    ctx.moveTo(20, bottomY - 30);
    ctx.lineTo(200, bottomY - 30);
    ctx.lineTo(180, bottomY);
    ctx.lineTo(20, bottomY);
    ctx.fill();
    
    ctx.fillStyle = "#fff";
    ctx.font = "bold 20px Arial";
    ctx.fillText(slugText, 40, bottomY - 8);

    // Main Ticker Strip (Gradient Red)
    const grad = ctx.createLinearGradient(0, bottomY, 0, 720);
    grad.addColorStop(0, "#900000");
    grad.addColorStop(1, themeColor);
    ctx.fillStyle = grad;
    ctx.fillRect(0, bottomY, 1280, 100);
    
    // Ticker Head (NEWS label)
    ctx.fillStyle = "#000";
    ctx.beginPath();
    ctx.moveTo(0, bottomY);
    ctx.lineTo(150, bottomY);
    ctx.lineTo(130, 720);
    ctx.lineTo(0, 720);
    ctx.fill();
    ctx.fillStyle = "#fff";
    ctx.font = "bold 30px Arial";
    ctx.fillText("NEWS", 20, 680);

    // Main Urdu Ticker Text
    ctx.fillStyle = "#fff";
    ctx.font = "40px 'UrduFont', Arial"; 
    // Note: Canvas doesn't handle RTL automatically well, so we draw normally but expect Urdu chars
    ctx.fillText(bottomText, tickerX, 685);
    tickerX -= 3; // Speed
    if (tickerX < -(ctx.measureText(bottomText).width)) tickerX = 1280;

    requestAnimationFrame(draw);
}
draw(); // Start Loop

// 3. RECORDER LOGIC
let mediaRecorder;
let chunks = [];
let isRecording = false;
let timerInt;
let seconds = 0;

const btnRec = document.getElementById('btnRec');
const timerDisplay = document.getElementById('timer');

btnRec.onclick = () => {
    if (!isRecording) {
        // START RECORDING
        const stream = canvas.captureStream(30); // 30 FPS
        // Add Audio Track from video
        if (video.srcObject) {
             const audioTracks = video.srcObject.getAudioTracks();
             if (audioTracks.length > 0) stream.addTrack(audioTracks[0]);
        }
        
        // Support various codecs for mobile compatibility
        let options = { mimeType: 'video/webm;codecs=vp9' };
        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
             options = { mimeType: 'video/webm' };
        }

        mediaRecorder = new MediaRecorder(stream, options);
        
        mediaRecorder.ondataavailable = e => { if (e.data.size > 0) chunks.push(e.data); };
        
        mediaRecorder.onstop = () => {
            const blob = new Blob(chunks, { type: 'video/webm' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `News_${Date.now()}.webm`;
            a.click();
            chunks = [];
            
            // Reset UI
            btnRec.innerText = "START RECORDING";
            btnRec.style.background = "#cc0000";
            clearInterval(timerInt);
            timerDisplay.innerText = "SAVED!";
            isRecording = false;
        };

        mediaRecorder.start();
        isRecording = true;
        
        // UI Updates
        btnRec.innerText = "STOP & SAVE";
        btnRec.style.background = "#333";
        seconds = 0;
        timerInt = setInterval(() => {
            seconds++;
            timerDisplay.innerText = new Date(seconds * 1000).toISOString().substr(14, 5);
        }, 1000);

    } else {
        // STOP RECORDING
        mediaRecorder.stop();
    }
};
