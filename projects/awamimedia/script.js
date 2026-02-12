
/* ============================================================
   Arham News Studio — Ultimate Pro Editor (Upgrade Y)
   - Dual tickers with auto-rotation
   - Theme packs
   - Animated lower third
   - Picture-in-Picture
   - WebAudio mixer (mic + music mixed into recording)
   - Presets (localStorage)
   - MP4 export via ffmpeg.wasm (CDN 0.12.15)
   - Urdu via Noto Nastaliq Urdu (Google Fonts)
   ============================================================ */

// ====== Elements ======
const canvas = document.getElementById('broadcastCanvas');
if (!canvas) throw new Error('Canvas #broadcastCanvas not found in DOM.');
const ctx = canvas.getContext('2d');

const mainVideo = document.getElementById('mainVideo');
const pipVideo  = document.getElementById('pipVideo');

const btnRec  = document.getElementById('btnRec');
const btnStop = document.getElementById('btnStop');
const btnMP4  = document.getElementById('btnMP4');
const recBadge= document.getElementById('recBadge');
const recTimer= document.getElementById('recTimer');

// Tickers
const topInput    = document.getElementById('topText');
const btnAddTop   = document.getElementById('btnAddTop');
const topListEl   = document.getElementById('topList');
const topSpeedEl  = document.getElementById('topSpeed');

const bottomInput   = document.getElementById('bottomText');
const btnAddBottom  = document.getElementById('btnAddBottom');
const bottomListEl  = document.getElementById('bottomList');
const bottomSpeedEl = document.getElementById('bottomSpeed');

// Lower third / Headline
const slugIn   = document.getElementById('slugIn');
const headlineIn = document.getElementById('headlineIn');
const btnShowLT = document.getElementById('btnShowLT');
const btnHideLT = document.getElementById('btnHideLT');
const btnBreak  = document.getElementById('btnBreak');

// Logo
const logoIn     = document.getElementById('logoIn');
const logoPosEl  = document.getElementById('logoPos');
const logoSizeEl = document.getElementById('logoSize');

// Themes
const themePackEl = document.getElementById('themePack');
const colorEl     = document.getElementById('inColor');

// Sources
const btnMainCam  = document.getElementById('btnMainCam');
const inMainFile  = document.getElementById('inMainFile');

const pipEnableEl = document.getElementById('pipEnable');
const pipControls = document.getElementById('pipControls');
const btnPipCam   = document.getElementById('btnPipCam');
const inPipFile   = document.getElementById('inPipFile');
const pipSizeEl   = document.getElementById('pipSize');
const pipPosEl    = document.getElementById('pipPos');

// Audio
const audioBg     = document.getElementById('audioBg');
const audioSfx    = document.getElementById('audioSfx');
const bgFile      = document.getElementById('bgFile');
const btnMusic    = document.getElementById('btnMusic');
const btnSfx      = document.getElementById('btnSfx');
const micVolEl    = document.getElementById('micVol');
const musicVolEl  = document.getElementById('musicVol');

// Presets
const presetNameEl   = document.getElementById('presetName');
const presetListEl   = document.getElementById('presetList');
const btnSavePreset  = document.getElementById('btnSavePreset');
const btnLoadPreset  = document.getElementById('btnLoadPreset');
const btnDeletePreset= document.getElementById('btnDeletePreset');

// ====== State ======
const state = {
  theme: { name: 'bbc', accent: '#bb1919' }, // default accent; can be overridden by theme
  breaking: false,

  topTicker:    { items: [], speed: 2.5, x: 1280, idx: 0 },
  bottomTicker: { items: [], speed: 3.0,  x: 1280, idx: 0 },

  lt: { slug:'', headline:'', visible:false, anim:0, animSpeed:0.12 },

  logo: { img:null, pos:'tr', size:110 },

  pip: { enabled:false, pos:'br', size:0.25 },

  audio: { musicOn:false, micGain:1, musicGain:0.3 },

  lastWebM: null
};

// Theme packs (approximate brand feel)
const THEMES = {
  bbc: { accent:'#bb1919' },
  cnn: { accent:'#cc0000' },
  geo: { accent:'#123b8c' },
  ary: { accent:'#0f2f6a' }
};

// ====== Helpers ======
function renderList(listEl, arr, onDelete){
  listEl.innerHTML = '';
  arr.forEach((t,i)=>{
    const li = document.createElement('li');
    const span = document.createElement('span');
    span.textContent = t;
    const btn = document.createElement('button');
    btn.className = 'x'; btn.textContent = '×';
    btn.onclick = () => onDelete(i);
    li.appendChild(span); li.appendChild(btn);
    listEl.appendChild(li);
  });
}
function currentTickerText(tk){
  if (!tk.items.length) return '';
  // advance to next when completely off-screen
  const current = tk.items[tk.idx];
  if (tk.x < -ctx.measureText(current).width - 40) {
    tk.idx = (tk.idx + 1) % tk.items.length;
    tk.x = canvas.width + 20;
  }
  return current;
}

// ====== Sources: Main ======
btnMainCam.onclick = async () => {
  try {
    const s = await navigator.mediaDevices.getUserMedia({ video:{ facingMode:'environment' }, audio:true });
    if (mainVideo.srcObject) mainVideo.srcObject.getTracks().forEach(t=>t.stop());
    mainVideo.srcObject = s;
    mainVideo.removeAttribute('src');
    mainVideo.muted = false; // routed via WebAudio
    await mainVideo.play();
    connectAudioGraph();
  } catch (e) {
    alert('Camera permission denied.');
    console.error(e);
  }
};

inMainFile.onchange = e => {
  const f = e.target.files?.[0]; if(!f) return;
  if (mainVideo.srcObject) mainVideo.srcObject.getTracks().forEach(t=>t.stop());
  mainVideo.srcObject = null;
  mainVideo.src = URL.createObjectURL(f);
  mainVideo.muted = false; // we route via WebAudio
  mainVideo.play();
  connectAudioGraph();
};

// ====== PiP ======
pipEnableEl.onchange = () => {
  state.pip.enabled = pipEnableEl.checked;
  pipControls.style.display = state.pip.enabled ? 'block' : 'none';
  if (!state.pip.enabled) {
    if (pipVideo.srcObject) pipVideo.srcObject.getTracks().forEach(t=>t.stop());
    pipVideo.srcObject = null;
    pipVideo.removeAttribute('src');
  }
};
btnPipCam.onclick = async () => {
  if (!state.pip.enabled) return alert('Enable PiP first.');
  try {
    const s = await navigator.mediaDevices.getUserMedia({ video:true, audio:true });
    if (pipVideo.srcObject) pipVideo.srcObject.getTracks().forEach(t=>t.stop());
    pipVideo.srcObject = s;
    pipVideo.removeAttribute('src');
    pipVideo.muted = false;
    await pipVideo.play();
    connectAudioGraph();
  } catch (e) {
    alert('PiP camera permission denied.');
    console.error(e);
  }
};
inPipFile.onchange = e => {
  if (!state.pip.enabled) return alert('Enable PiP first.');
  const f = e.target.files?.[0]; if(!f) return;
  if (pipVideo.srcObject) pipVideo.srcObject.getTracks().forEach(t=>t.stop());
  pipVideo.srcObject = null;
  pipVideo.src = URL.createObjectURL(f);
  pipVideo.muted = false;
  pipVideo.play();
  connectAudioGraph();
};
pipSizeEl.oninput = () => state.pip.size = parseFloat(pipSizeEl.value);
pipPosEl.onchange  = () => state.pip.pos  = pipPosEl.value;

// ====== Logo ======
logoIn.onchange = e => {
  const f = e.target.files?.[0]; if(!f) return;
  const img = new Image();
  img.onload = () => state.logo.img = img;
  img.src = URL.createObjectURL(f);
};
logoPosEl.onchange = () => state.logo.pos = logoPosEl.value;
logoSizeEl.oninput = () => state.logo.size = parseInt(logoSizeEl.value,10);

// ====== Themes ======
themePackEl.onchange = () => {
  const v = themePackEl.value;
  state.theme.name = v;
  if (v !== 'custom') {
    state.theme.accent = THEMES[v].accent;
    colorEl.value = state.theme.accent;
  }
};
colorEl.oninput = () => {
  state.theme.accent = colorEl.value;
  themePackEl.value = 'custom';
};

// ====== Tickers ======
btnAddTop.onclick = () => {
  const t = (topInput.value||'').trim(); if(!t) return;
  state.topTicker.items.push(t);
  topInput.value = '';
  renderList(topListEl, state.topTicker.items, i => {
    state.topTicker.items.splice(i,1);
    renderList(topListEl, state.topTicker.items, arguments.callee);
  });
};
btnAddBottom.onclick = () => {
  const t = (bottomInput.value||'').trim(); if(!t) return;
  state.bottomTicker.items.push(t);
  bottomInput.value = '';
  renderList(bottomListEl, state.bottomTicker.items, i => {
    state.bottomTicker.items.splice(i,1);
    renderList(bottomListEl, state.bottomTicker.items, arguments.callee);
  });
};
topSpeedEl.oninput    = e => state.topTicker.speed    = parseFloat(e.target.value);
bottomSpeedEl.oninput = e => state.bottomTicker.speed = parseFloat(e.target.value);

// ====== Lower Third ======
btnShowLT.onclick = () => {
  state.lt.slug     = slugIn.value;
  state.lt.headline = headlineIn.value;
  state.lt.visible  = true;
};
btnHideLT.onclick = () => { state.lt.visible = false; };
btnBreak.onclick  = () => {
  state.breaking = !state.breaking;
  btnBreak.textContent = state.breaking ? 'BREAKING ON' : 'NORMAL MODE';
  btnBreak.style.background = state.breaking ? 'red' : '#d35400';
  flashOpacity = 1.0; playSfx();
};

// ====== Audio / WebAudio Mixer ======
let audioCtx, destNode, micGain, musicGain;
let mainSrc, pipSrc, bgSrc; // MediaElementSources

function ensureAudioCtx(){
  if (!audioCtx) {
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    destNode = audioCtx.createMediaStreamDestination();
    micGain  = audioCtx.createGain();  micGain.gain.value  = state.audio.micGain;
    musicGain= audioCtx.createGain();  musicGain.gain.value= state.audio.musicGain;
  }
}
micVolEl.oninput   = e => { state.audio.micGain   = parseFloat(e.target.value); if(micGain)  micGain.gain.value  = state.audio.micGain; };
musicVolEl.oninput = e => { state.audio.musicGain = parseFloat(e.target.value); if(musicGain)musicGain.gain.value= state.audio.musicGain; };

bgFile.onchange = e => {
  const f = e.target.files?.[0]; if(!f) return;
  audioBg.src = URL.createObjectURL(f);
};

btnMusic.onclick = async () => {
  state.audio.musicOn = !state.audio.musicOn;
  try { ensureAudioCtx(); await audioCtx.resume(); } catch {}
  if (state.audio.musicOn) {
    audioBg.loop = true;
    audioBg.play().catch(()=>{});
    btnMusic.textContent = 'MUSIC: ON';
    btnMusic.style.background = '#27ae60';
  } else {
    audioBg.pause();
    btnMusic.textContent = 'MUSIC: OFF';
    btnMusic.style.background = '#2980b9';
  }
};
btnSfx.onclick = () => playSfx();
function playSfx(){ audioSfx.currentTime = 0; audioSfx.play().catch(()=>{}); }

// Build/refresh audio graph safely (avoid double-connecting)
function disconnectNode(n){ try { n && n.disconnect(); } catch {} }

function connectAudioGraph(){
  ensureAudioCtx();
  // Disconnect prior ME sources
  [mainSrc, pipSrc, bgSrc].forEach(disconnectNode);

  // MAIN
  if (mainVideo.srcObject) {
    // For live camera: MediaStreamSource (mic)
    const micNode = audioCtx.createMediaStreamSource(mainVideo.srcObject);
    micNode.connect(micGain).connect(destNode);
  } else if (mainVideo.src) {
    mainSrc = audioCtx.createMediaElementSource(mainVideo);
    mainSrc.connect(micGain).connect(destNode);
    mainSrc.connect(audioCtx.destination); // monitor
  }

  // PIP
  if (state.pip.enabled) {
    if (pipVideo.srcObject) {
      const pipNode = audioCtx.createMediaStreamSource(pipVideo.srcObject);
      pipNode.connect(micGain).connect(destNode);
    } else if (pipVideo.src) {
      pipSrc = audioCtx.createMediaElementSource(pipVideo);
      pipSrc.connect(micGain).connect(destNode);
      pipSrc.connect(audioCtx.destination); // monitor
    }
  }

  // Music
  bgSrc = audioCtx.createMediaElementSource(audioBg);
  bgSrc.connect(musicGain).connect(destNode);
  bgSrc.connect(audioCtx.destination);
}

// Ensure audio can start on mobile after first tap
window.addEventListener('pointerdown', async () => {
  if (audioCtx && audioCtx.state === 'suspended') {
    try { await audioCtx.resume(); } catch {}
  }
}, { once:true });

// ====== Render Engine ======
let flashOpacity = 0.0;

function drawTickerBarTop(){
  // bar
  ctx.fillStyle = 'rgba(0,0,0,0.80)';
  ctx.fillRect(0, 0, canvas.width, 58);
  ctx.fillStyle = '#222';
  ctx.fillRect(0, 56, canvas.width, 2);

  // wedge label
  ctx.fillStyle = '#ffd230';
  ctx.beginPath();
  ctx.moveTo(0,0); ctx.lineTo(170,0); ctx.lineTo(150,58); ctx.lineTo(0,58); ctx.closePath(); ctx.fill();
  ctx.fillStyle = '#000';
  ctx.font = '900 22px Arial';
  ctx.fillText('UPDATES', 18, 36);

  // text
  const txt = currentTickerText(state.topTicker);
  ctx.fillStyle = '#ffd230';
  ctx.font = 'bold 24px Arial';
  ctx.fillText(txt, state.topTicker.x, 36);
  state.topTicker.x -= state.topTicker.speed;
}

function drawLowerThird(){
  const target = state.lt.visible ? 1 : 0;
  state.lt.anim += (target - state.lt.anim) * state.lt.animSpeed;
  const baseY = canvas.height - 120;
  const y = baseY + (1 - state.lt.anim) * 120;

  const accent = state.breaking ? '#cc0000' : state.theme.accent;

  // main strip
  const grad = ctx.createLinearGradient(0, y, 0, y+100);
  grad.addColorStop(0, state.breaking ? '#500' : '#300');
  grad.addColorStop(1, accent);
  ctx.fillStyle = grad;
  ctx.fillRect(0, y, canvas.width, 100);

  // highlight edge
  ctx.fillStyle = state.breaking ? 'red' : '#ff4444';
  ctx.fillRect(0, y, canvas.width, 3);

  // left label triangle
  ctx.fillStyle = state.breaking ? 'red' : '#000';
  ctx.beginPath();
  ctx.moveTo(0,y); ctx.lineTo(180,y); ctx.lineTo(150,y+100); ctx.lineTo(0,y+100); ctx.closePath(); ctx.fill();

  // label text
  ctx.fillStyle = '#fff';
  ctx.font = '900 28px Arial';
  ctx.fillText(state.breaking ? 'BREAKING' : 'NEWS', 10, y+70);

  // slug tag
  ctx.fillStyle = accent;
  const slugY = y - 40;
  ctx.beginPath();
  ctx.moveTo(20, slugY);
  ctx.lineTo(220, slugY);
  ctx.lineTo(200, slugY+38);
  ctx.lineTo(20, slugY+38);
  ctx.closePath();
  ctx.fill();

  ctx.fillStyle = '#fff';
  ctx.font = '900 20px Arial';
  ctx.fillText(state.lt.slug || '', 30, slugY + 26);

  // headline (Urdu)
  ctx.fillStyle = '#fff';
  ctx.font = "48px 'Noto Nastaliq Urdu', Arial";
  ctx.fillText(state.lt.headline || '', 240, y + 70);
}

function drawBottomTicker(){
  const t = currentTickerText(state.bottomTicker);
  const y = canvas.height - 22;
  ctx.fillStyle = '#fff';
  ctx.font = "44px 'Noto Nastaliq Urdu', Arial";
  ctx.fillText(t, state.bottomTicker.x, y);
  state.bottomTicker.x -= state.bottomTicker.speed;
}

function drawLogo(){
  if (!state.logo.img) return;
  const w = state.logo.size, h = state.logo.size;
  let x=0,y=0, pad=20;
  switch(state.logo.pos){
    case 'tr': x = canvas.width - w - pad; y = pad; break;
    case 'tl': x = pad; y = pad; break;
    case 'br': x = canvas.width - w - pad; y = canvas.height - h - 140; break;
    case 'bl': x = pad; y = canvas.height - h - 140; break;
  }
  ctx.drawImage(state.logo.img, x, y, w, h);
}

function drawLiveBadge(){
  ctx.fillStyle = 'red';
  ctx.fillRect(20, 80, 70, 28);
  ctx.fillStyle = '#fff';
  ctx.font = 'bold 16px Arial';
  ctx.fillText('LIVE', 35, 100);
}

function drawPip(){
  if (!state.pip.enabled) return;
  const scale = state.pip.size;
  const w = Math.round(canvas.width * scale);
  const h = Math.round(w * 9 / 16);
  const pad = 16;
  let x=0,y=0;
  switch(state.pip.pos){
    case 'br': x = canvas.width - w - pad; y = canvas.height - h - 16 - 100; break;
    case 'bl': x = pad; y = canvas.height - h - 16 - 100; break;
    case 'tr': x = canvas.width - w - pad; y = pad; break;
    case 'tl': x = pad; y = pad; break;
  }
  // rounded mask
  ctx.save();
  const r = 12;
  ctx.beginPath();
  ctx.moveTo(x+r, y);
  ctx.arcTo(x+w, y, x+w, y+h, r);
  ctx.arcTo(x+w, y+h, x, y+h, r);
  ctx.arcTo(x, y+h, x, y, r);
  ctx.arcTo(x, y, x+w, y, r);
  ctx.closePath(); ctx.clip();

  ctx.fillStyle = '#000';
  ctx.fillRect(x, y, w, h);
  if (pipVideo.readyState >= 2) ctx.drawImage(pipVideo, x, y, w, h);
  ctx.restore();

  ctx.strokeStyle = 'rgba(255,255,255,0.6)';
  ctx.lineWidth = 2;
  ctx.strokeRect(x, y, w, h);
}

function draw(){
  // Base video
  if (mainVideo.readyState >= 2) ctx.drawImage(mainVideo, 0, 0, canvas.width, canvas.height);
  else { ctx.fillStyle = '#000'; ctx.fillRect(0,0,canvas.width,canvas.height); }

  // Overlays
  drawTickerBarTop();
  drawLogo();
  drawLiveBadge();
  drawLowerThird();
  drawBottomTicker();
  drawPip();

  // Breaking flash
  if (flashOpacity > 0) {
    ctx.fillStyle = `rgba(255,255,255,${flashOpacity})`;
    ctx.fillRect(0,0,canvas.width,canvas.height);
    flashOpacity -= 0.08;
  }

  requestAnimationFrame(draw);
}
draw();

// ====== Recording + MP4 export ======
let mediaRecorder, chunks = [], recInterval, seconds=0;

btnRec.onclick = async () => {
  ensureAudioCtx();
  connectAudioGraph();

  // Canvas video stream (30 fps)
  const canvasStream = canvas.captureStream(30);

  // Add mixed audio track
  const audioTrack = destNode.stream.getAudioTracks()[0];
  if (audioTrack) canvasStream.addTrack(audioTrack);

  // WebM recording; most modern browsers support this (iOS 17+ included)
  mediaRecorder = new MediaRecorder(canvasStream, { mimeType: 'video/webm;codecs=vp9,opus' });
  mediaRecorder.ondataavailable = e => { if (e.data.size) chunks.push(e.data); };
  mediaRecorder.onstop = () => {
    state.lastWebM = new Blob(chunks, { type:'video/webm' });
    chunks = [];
    btnMP4.disabled = false;
    stopUI();
  };

  mediaRecorder.start();
  startUI();
};

btnStop.onclick = () => { try { mediaRecorder.stop(); } catch {} };

function startUI(){
  btnRec.disabled = true;
  btnStop.disabled = false;
  btnMP4.disabled  = true;
  recBadge.style.display = 'block';
  seconds = 0;
  recInterval = setInterval(()=>{
    seconds++;
    const m = String(Math.floor(seconds/60)).padStart(2,'0');
    const s = String(seconds%60).padStart(2,'0');
    recTimer.textContent = `${m}:${s}`;
  }, 1000);
}
function stopUI(){
  btnRec.disabled = false;
  btnStop.disabled = true;
  recBadge.style.display = 'none';
  clearInterval(recInterval);
}

// MP4 export via ffmpeg.wasm
btnMP4.onclick = async () => {
  if (!state.lastWebM) return alert('No recording to export.');

  const { createFFmpeg, fetchFile } = FFmpeg;
  const ffmpeg = createFFmpeg({ log:false });
  await ffmpeg.load();

  await ffmpeg.FS('writeFile', 'input.webm', await fetchFile(state.lastWebM));
  await ffmpeg.run(
    '-i','input.webm',
    '-c:v','libx264','-preset','veryfast','-pix_fmt','yuv420p',
    '-c:a','aac','-b:a','128k',
    'output.mp4'
  );
  const data = ffmpeg.FS('readFile','output.mp4');
  const url = URL.createObjectURL(new Blob([data.buffer], { type:'video/mp4' }));

  const a = document.createElement('a');
  a.href = url;
  a.download = `News_${Date.now()}.mp4`;
  a.click();
};

// ====== Presets (localStorage) ======
const LS_KEY = 'arhamNewsPresets_v2';

function loadPresetList(){
  const all = JSON.parse(localStorage.getItem(LS_KEY) || '{}');
  presetListEl.innerHTML = '';
  Object.keys(all).forEach(name=>{
    const opt = document.createElement('option');
    opt.value = name; opt.textContent = name;
    presetListEl.appendChild(opt);
  });
}
loadPresetList();

btnSavePreset.onclick = () => {
  const name = (presetNameEl.value||'').trim();
  if (!name) return alert('Preset name required.');
  const all = JSON.parse(localStorage.getItem(LS_KEY) || '{}');
  all[name] = {
    theme: state.theme,
    breaking: state.breaking,
    top: { items: state.topTicker.items, speed: state.topTicker.speed },
    bottom: { items: state.bottomTicker.items, speed: state.bottomTicker.speed },
    lt: { slug: slugIn.value, headline: headlineIn.value },
    logo: { pos: state.logo.pos, size: state.logo.size },
    pip: { enabled: state.pip.enabled, pos: state.pip.pos, size: state.pip.size }
  };
  localStorage.setItem(LS_KEY, JSON.stringify(all));
  loadPresetList();
  alert('Preset saved.');
};

btnLoadPreset.onclick = () => {
  const name = presetListEl.value;
  if (!name) return;
  const all = JSON.parse(localStorage.getItem(LS_KEY) || '{}');
  const p = all[name]; if (!p) return;

  state.theme = p.theme || state.theme;
  themePackEl.value = (state.theme.name || 'custom');
  colorEl.value = state.theme.accent || '#dd0000';

  state.breaking = !!p.breaking;
  btnBreak.textContent = state.breaking ? 'BREAKING ON' : 'NORMAL MODE';
  btnBreak.style.background = state.breaking ? 'red' : '#d35400';

  state.topTicker.items = p.top?.items || [];
  state.topTicker.speed = p.top?.speed || 2.5;
  topSpeedEl.value = state.topTicker.speed;

  state.bottomTicker.items = p.bottom?.items || [];
  state.bottomTicker.speed = p.bottom?.speed || 3;
  bottomSpeedEl.value = state.bottomTicker.speed;

  slugIn.value = p.lt?.slug || '';
  headlineIn.value = p.lt?.headline || '';
  state.lt.slug = slugIn.value;
  state.lt.headline = headlineIn.value;

  state.logo.pos = p.logo?.pos || 'tr';
  state.logo.size = p.logo?.size || 110;
  logoPosEl.value = state.logo.pos;
  logoSizeEl.value = state.logo.size;

  state.pip.enabled = !!p.pip?.enabled; pipEnableEl.checked = state.pip.enabled;
  state.pip.pos = p.pip?.pos || 'br';   pipPosEl.value = state.pip.pos;
  state.pip.size = p.pip?.size || 0.25; pipSizeEl.value = state.pip.size;
  pipControls.style.display = state.pip.enabled ? 'block' : 'none';

  renderList(topListEl, state.topTicker.items, i => {
    state.topTicker.items.splice(i,1);
    renderList(topListEl, state.topTicker.items, arguments.callee);
  });
  renderList(bottomListEl, state.bottomTicker.items, i => {
    state.bottomTicker.items.splice(i,1);
    renderList(bottomListEl, state.bottomTicker.items, arguments.callee);
  });

  alert('Preset loaded.');
};

btnDeletePreset.onclick = () => {
  const name = presetListEl.value;
  if (!name) return;
  const all = JSON.parse(localStorage.getItem(LS_KEY) || '{}');
  delete all[name];
  localStorage.setItem(LS_KEY, JSON.stringify(all));
  loadPresetList();
  alert('Preset deleted.');
};

// ====== Seed with sample items (optional) ======
state.topTicker.items = [
  'Welcome to Arham News Studio — Ultimate Pro Editor',
  'Save your configuration as a Preset for next time',
  'Use PiP for interviews or commentary'
];
state.bottomTicker.items = [
  'اہم خبر: نچلے ٹِکر میں اردو سرخیوں کی فہرست شامل کریں',
  'اپنا لوگو اپ لوڈ کریں اور تھیم منتخب کریں',
  'ریکارڈنگ کے بعد MP4 میں ایکسپورٹ کریں'
];
renderList(topListEl, state.topTicker.items, i => { state.topTicker.items.splice(i,1); renderList(topListEl, state.topTicker.items, arguments.callee); });
renderList(bottomListEl, state.bottomTicker.items, i => { state.bottomTicker.items.splice(i,1); renderList(bottomListEl, state.bottomTicker.items, arguments.callee); });
``
