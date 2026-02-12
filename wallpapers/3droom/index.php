<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arham Printers - Wallpaper Studio (Pro-Focus)</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/",
            "@tweenjs/tween.js": "https://unpkg.com/@tweenjs/tween.js@23.1.1/dist/tween.esm.js"
        }
    }
    </script>

    <style>
        :root { --primary: #00d4ff; --bg: #111; }
        body { margin: 0; overflow: hidden; background: var(--bg); font-family: 'Outfit', sans-serif; color: white; }
        #canvas-container { width: 100%; height: 100vh; position: absolute; top:0; left:0; z-index: 1; }

        /* UI Layer */
        .ui-header { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10; text-align: center; pointer-events: none; }
        .room-tag { background: rgba(0,212,255,0.2); border: 1px solid var(--primary); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; backdrop-filter: blur(10px); }

        /* Control Panel */
        .rec-panel {
            position: fixed; bottom: 80px; right: 20px; 
            background: rgba(0,0,0,0.9); padding: 20px; border-radius: 15px;
            border: 1px solid #333; z-index: 100; pointer-events: auto; width: 240px;
        }
        .rec-panel h4 { margin: 0 0 15px 0; color: var(--primary); font-size: 1rem; text-align: center; }
        .input-group { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.8rem; }
        .input-group input { width: 60px; background: #222; border: 1px solid #444; color: white; padding: 5px; border-radius: 5px; text-align: center; }
        
        #rec-btn {
            width: 100%; padding: 12px; border-radius: 10px; border: none;
            background: #ff3e3e; color: white; font-weight: bold; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.3s;
        }
        #rec-btn.active { background: #000; border: 2px solid #ff3e3e; animation: blink 1.5s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }

        #status-msg { font-size: 0.7rem; text-align: center; margin-top: 10px; color: #aaa; }
    </style>
</head>
<body>

    <div id="canvas-container"></div>

    <div class="ui-header">
        <h2 style="margin:0; letter-spacing:3px;">ARHAM PRINTERS</h2>
        <div class="room-tag" id="room-display">LOADING ROOMS...</div>
    </div>

    <div class="rec-panel">
        <h4>STUDIO RECORDER</h4>
        <div class="input-group">
            <span>Start Room:</span>
            <input type="number" id="start-room" value="1" min="1">
        </div>
        <div class="input-group">
            <span>End Room:</span>
            <input type="number" id="end-room" value="1" min="1">
        </div>
        <button id="rec-btn" onclick="handleRecording()">
            <i class="fas fa-video"></i> START CAPTURE
        </button>
        <div id="status-msg">Ready to record</div>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
        import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
        import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
        import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';
        import * as TWEEN from '@tweenjs/tween.js';

        let scene, camera, renderer, controls, composer, worldGroup;
        let config = { rooms: [] };
        const texLoader = new THREE.TextureLoader();
        const ROOM_DIST = 60;
        
        // Cache Buster Timestamp
        const VERSION = Date.now();

        // 1. Fetch JSON with cache busting
        fetch(`data.json?v=${VERSION}`).then(res => res.json()).then(data => {
            config = data;
            document.getElementById('end-room').value = config.rooms.length;
            init();
        });

        function init() {
            scene = new THREE.Scene();
            camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
            
            renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            document.getElementById('canvas-container').appendChild(renderer.domElement);

            composer = new EffectComposer(renderer);
            composer.addPass(new RenderPass(scene, camera));
            composer.addPass(new UnrealBloomPass(new THREE.Vector2(window.innerWidth, window.innerHeight), 0.4, 0.4, 0.85));
            composer.addPass(new OutputPass());

            scene.add(new THREE.AmbientLight(0xffffff, 1.2));
            worldGroup = new THREE.Group();
            scene.add(worldGroup);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;

            buildShowroom();
            jumpTo(0);
            animate();
        }

        function buildShowroom() {
            config.rooms.forEach((room, idx) => {
                const group = new THREE.Group();
                group.position.x = idx * ROOM_DIST;
                const sides = ['right', 'left', 'ceil', 'floor', 'front', 'back'];
                
                const mats = sides.map(side => {
                    const s = room.surfaces[side];
                    const mat = new THREE.MeshStandardMaterial({ side: THREE.BackSide });
                    
                    // 2. Load Textures with cache busting
                    if(s?.tex && s.tex !== "") {
                        texLoader.load(`${s.tex}?v=${VERSION}`, t => { 
                            t.colorSpace = THREE.SRGBColorSpace; 
                            mat.map = t; 
                            mat.needsUpdate = true; 
                        });
                    }
                    if(s?.col) mat.color.set(s.col);
                    return mat;
                });

                const box = new THREE.Mesh(new THREE.BoxGeometry(room.w, room.h, room.d), mats);
                box.position.y = room.h / 2;
                group.add(box);
                worldGroup.add(group);
            });
        }

        function jumpTo(idx) {
            if(!config.rooms[idx]) return;
            const tx = idx * ROOM_DIST;
            camera.position.set(tx + 8, 5, 8);
            controls.target.set(tx, 4, 0);
            document.getElementById('room-display').innerText = config.rooms[idx].name || `Room ${idx + 1}`;
        }

        function animate() {
            requestAnimationFrame(animate);
            TWEEN.update();
            controls.update();
            composer.render();
        }

        // --- RECORDING LOGIC ---
        let mediaRecorder, recordedChunks = [], isRecording = false;

        window.handleRecording = async function() {
            if(isRecording) { location.reload(); return; }

            const startIdx = Math.max(0, parseInt(document.getElementById('start-room').value) - 1);
            const endIdx = Math.min(config.rooms.length - 1, parseInt(document.getElementById('end-room').value) - 1);

            const stream = renderer.domElement.captureStream(30);
            mediaRecorder = new MediaRecorder(stream, { mimeType: 'video/webm; codecs=vp9' });
            mediaRecorder.ondataavailable = e => recordedChunks.push(e.data);
            mediaRecorder.onstop = () => {
                const blob = new Blob(recordedChunks, { type: 'video/webm' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = "Arham_Wallpapers_Showcase.webm";
                link.click();
            };

            mediaRecorder.start();
            isRecording = true;
            document.getElementById('rec-btn').classList.add('active');
            document.getElementById('rec-btn').innerText = "STOPPING...";

            for(let i = startIdx; i <= endIdx; i++) {
                if(!isRecording) break;
                
                const tx = i * ROOM_DIST;
                document.getElementById('status-msg').innerText = `Capturing: ${config.rooms[i].name}`;
                document.getElementById('room-display').innerText = config.rooms[i].name;

                // Move
                new TWEEN.Tween(camera.position).to({ x: tx + 8, y: 5, z: 8 }, 1200).start();
                new TWEEN.Tween(controls.target).to({ x: tx, y: 4, z: 0 }, 1200).start();
                await new Promise(r => setTimeout(r, 1300));

                // Rotate 360
                controls.autoRotate = true;
                controls.autoRotateSpeed = 4.5;
                await new Promise(r => setTimeout(r, 8500));
                controls.autoRotate = false;

                // Look up at roof
                new TWEEN.Tween(controls.target).to({ y: 15 }, 1500).easing(TWEEN.Easing.Quadratic.Out).start();
                await new Promise(r => setTimeout(r, 2500));

                // Reset
                new TWEEN.Tween(controls.target).to({ y: 4 }, 800).start();
                await new Promise(r => setTimeout(r, 850));
            }

            mediaRecorder.stop();
            document.getElementById('status-msg').innerText = "Download Starting...";
        }

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            composer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>