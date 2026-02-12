# News Studio Ultra

A **masterpiece**, mobile‑first, ultra‑simple news video creator. Your client records professional clips using **local video or the device camera**. Tickers, logo, and slug are composited in real time on an **HTML5 Canvas** and saved with one click.

## Why this is easy for low‑tech users
- One screen, top‑to‑bottom flow: **Source → Text → Record**
- Big buttons, large inputs, minimal jargon
- **Simple Mode** hides advanced sections (branding & audio) by default
- Works on **mobile and desktop**; includes **16:9** and **9:16** aspect toggle

## Features
- Local video **or** live camera input
- Authentic Urdu ticker via **Jameel Noori Nastaleeq** font
- Draggable logo, size & opacity control
- Theme color and ticker speed
- Mic + video audio mix into the recorded file
- Password protection (edit in `index.php` or set env `NEWS_STUDIO_PASSWORD`)

## Setup (5 steps)
1. Upload the `NewsStudioUltra/` folder to your hosting (PHP needed for login page).
2. **Place the font file** `JameelNooriNastaleeq.ttf` into `assets/` (exact name as written).
3. Enable **HTTPS** so camera/mic permissions work on mobile.
4. Change the password in `index.php` (`$PASSWORD`) or set env var.
5. Open the page → Camera/Upload → Type headlines (Urdu supported) → **Start Recording**.

## Export format
- Saves as **WebM** (`vp9`/`vp8`). Convert to MP4 if needed:

```bash
ffmpeg -i input.webm -c:v libx264 -preset veryfast -crf 20 -c:a aac output.mp4
```

## Notes on iOS/Safari
- Recent iOS versions support MediaRecorder; older ones may vary. If a specific device cannot record, use the device’s built‑in **Screen Recording** as fallback.

## White‑labeling
- Replace `assets/logo.png`
- Change brand title in `index.php`
- Adjust colors in `style.css`
