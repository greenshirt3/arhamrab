<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Mug - Full Wrap 10.5"</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    
    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
            }
        }
    </script>

    <style>
        :root { --bg-color: #f8fafc; --panel-bg: #ffffff; --accent: #0f172a; }
        body { margin: 0; overflow: hidden; font-family: 'Inter', sans-serif; background: var(--bg-color); }
        #canvas-container { width: 100%; height: 100vh; }

        /* CONTROLS */
        .controls {
            position: absolute; top: 20px; left: 20px; width: 300px;
            background: var(--panel-bg); padding: 24px; border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-height: 90vh; overflow-y: auto;
        }
        
        h1 { margin: 0 0 5px 0; font-size: 1.1rem; font-weight: 800; color: var(--accent); }
        .info { 
            font-size: 0.75rem; color: #64748b; background: #f1f5f9; 
            padding: 10px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e2e8f0;
            line-height: 1.5;
        }

        .label { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; display: block; }

        /* BUTTONS */
        .btn-upload {
            background: var(--accent); color: white; border: none; width: 100%; padding: 12px;
            border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.9rem;
        }
        .btn-upload:hover { background: #334155; }
        .file-input { display: none; }

        /* COLORS */
        .color-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        input[type="color"] {
            -webkit-appearance: none; border: none; width: 100%; height: 40px; 
            border-radius: 8px; cursor: pointer; padding: 0; background: none;
        }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type="color"]::-webkit-color-swatch { border: 1px solid #cbd5e1; border-radius: 8px; }

        .checkbox-row { display: flex; align-items: center; justify-content: space-between; font-size: 0.9rem; color: #334155; margin-top: 15px; font-weight: 500; }

        #loading { position: fixed; inset: 0; background: white; z-index: 99; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--accent); letter-spacing: 1px; transition: 0.5s; }
    </style>
</head>
<body>

    <div id="loading">CALIBRATING 10.5" PRINT AREA...</div>
    <div id="canvas-container"></div>

    <div class="controls">
        <h1>Full Wrap Customizer</h1>
        <div class="info">
            <strong>Full Wrap Mode:</strong><br>
            • Total Circumference: 11"<br>
            • Handle Gap: 0.5" (Unprinted)<br>
            • Print Area: 10.5" x 3.8" (Covered)
        </div>

        <span class="label">1. Upload Full Design</span>
        <button class="btn-upload" onclick="document.getElementById('imgInp').click()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Select Image
        </button>
        <input type="file" id="imgInp" class="file-input" accept="image/*">

        <br><br>

        <span class="label">2. Colors</span>
        <div class="color-row">
            <div>
                <span class="label" style="font-weight:400; color:#666;">Inner</span>
                <input type="color" id="colInner" value="#ffffff">
            </div>
            <div>
                <span class="label" style="font-weight:400; color:#666;">Handle</span>
                <input type="color" id="colHandle" value="#ffffff">
            </div>
        </div>
        <div>
            <span class="label" style="font-weight:400; color:#666;">Handle Gap Color</span>
            <input type="color" id="colBase" value="#ffffff">
        </div>

        <div class="checkbox-row">
            <span>Auto Rotate</span>
            <input type="checkbox" id="chkRotate" checked>
        </div>
        
        <button class="btn-upload" style="background:#2563eb; margin-top:20px;" onclick="saveSnap()">
            Download Preview
        </button>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        // --- PRECISE DIMENSIONS (INCHES) ---
        const CIRCUMFERENCE = 11.0;
        const H_MUG = 3.8;
        const GAP_WIDTH = 0.5; // The unprintable handle area
        
        // Calculated Radius (C = 2*PI*r  =>  r = C / 2*PI)
        const R_MUG = CIRCUMFERENCE / (2 * Math.PI); 
        
        // --- 3D SCENE ---
        let scene, camera, renderer, controls;
        let mug, body, inner, handle, rim, bottom;
        let rotating = true;
        let currentImg = null;

        init();

        function init() {
            const cont = document.getElementById('canvas-container');
            
            // Scene
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xf0f2f5);

            // Camera
            camera = new THREE.PerspectiveCamera(40, window.innerWidth/window.innerHeight, 0.1, 100);
            camera.position.set(9, 5, 9);

            // Renderer
            renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            cont.appendChild(renderer.domElement);

            // Light
            const amb = new THREE.AmbientLight(0xffffff, 0.7);
            scene.add(amb);
            const dir = new THREE.DirectionalLight(0xffffff, 1.3);
            dir.position.set(5, 8, 5);
            dir.castShadow = true;
            dir.shadow.mapSize.width = 2048;
            dir.shadow.mapSize.height = 2048;
            scene.add(dir);
            const fill = new THREE.DirectionalLight(0xffffff, 0.5);
            fill.position.set(-5, 0, -5);
            scene.add(fill);

            // Mug
            createMug();

            // Controls
            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.minDistance = 5;
            controls.maxDistance = 20;

            // Events
            window.addEventListener('resize', onResize);
            bindUI();

            // Init Texture
            updateTexture('#ffffff');

            setTimeout(() => {
                document.getElementById('loading').style.opacity = 0;
                setTimeout(() => document.getElementById('loading').remove(), 500);
            }, 600);

            loop();
        }

        function createMug() {
            mug = new THREE.Group();
            const segments = 128; 
            const thick = 0.15;

            // Matte Material
            const matte = new THREE.MeshStandardMaterial({ 
                color: 0xffffff, roughness: 0.8, metalness: 0.0 
            });

            // 1. OUTER SHELL (Print Surface)
            const geoBody = new THREE.CylinderGeometry(R_MUG, R_MUG, H_MUG, segments, 1, true);
            geoBody.rotateY(-Math.PI / 2); // Align seam to +X (Handle)
            
            const matBody = new THREE.MeshStandardMaterial({ 
                color: 0xffffff, roughness: 0.8, metalness: 0.0, side: THREE.FrontSide 
            });
            body = new THREE.Mesh(geoBody, matBody);
            body.castShadow = true; body.receiveShadow = true;
            mug.add(body);

            // 2. INNER SHELL
            const geoInner = new THREE.CylinderGeometry(R_MUG-thick, R_MUG-thick, H_MUG-thick, segments, 1, false);
            geoInner.translate(0, thick/2, 0);
            inner = new THREE.Mesh(geoInner, matte.clone());
            inner.receiveShadow = true;
            mug.add(inner);

            // 3. RIM
            const geoRim = new THREE.RingGeometry(R_MUG-thick, R_MUG, segments);
            geoRim.rotateX(-Math.PI/2);
            geoRim.translate(0, H_MUG/2, 0);
            rim = new THREE.Mesh(geoRim, matte.clone());
            mug.add(rim);

            // 4. HANDLE
            const geoHandle = new THREE.TorusGeometry(H_MUG*0.35, thick, 24, 64, Math.PI*1.3);
            geoHandle.rotateZ(Math.PI/1.3);
            geoHandle.translate(R_MUG + (H_MUG*0.1), 0, 0);
            handle = new THREE.Mesh(geoHandle, matte.clone());
            handle.castShadow = true;
            mug.add(handle);

            // 5. BOTTOM
            const geoBot = new THREE.CircleGeometry(R_MUG, segments);
            geoBot.rotateX(Math.PI/2);
            geoBot.translate(0, -H_MUG/2, 0);
            bottom = new THREE.Mesh(geoBot, matte.clone());
            mug.add(bottom);

            // Shadow Plane
            const planeGeo = new THREE.PlaneGeometry(15, 15);
            const planeMat = new THREE.ShadowMaterial({ opacity: 0.1 });
            const plane = new THREE.Mesh(planeGeo, planeMat);
            plane.rotation.x = -Math.PI/2;
            plane.position.y = -H_MUG/2 - 0.01;
            plane.receiveShadow = true;
            mug.add(plane);

            scene.add(mug);
        }

        // --- FULL WRAP LOGIC ---
        function updateTexture(baseHex) {
            const PPI = 100; // Pixels per Inch
            
            // Total Canvas (11" x 3.8")
            const wCanvas = Math.ceil(CIRCUMFERENCE * PPI); 
            const hCanvas = Math.ceil(H_MUG * PPI);         
            
            const cvs = document.createElement('canvas');
            cvs.width = wCanvas;
            cvs.height = hCanvas;
            const ctx = cvs.getContext('2d');

            // 1. Fill Background (Gap Color)
            ctx.fillStyle = baseHex;
            ctx.fillRect(0, 0, wCanvas, hCanvas);

            // 2. Draw Image (Filling 10.5")
            // The printable area is 11 - 0.5 = 10.5 inches.
            // We want the image to COVER this area fully.
            
            if (currentImg) {
                const wPrintable = (CIRCUMFERENCE - GAP_WIDTH) * PPI; // 10.5 inches * PPI
                const hPrintable = H_MUG * PPI; // Full height (3.8 inches * PPI)

                // Starting X position (0.25 inches in, to center the print area leaving 0.25 gap on each side)
                const startX = (GAP_WIDTH / 2) * PPI; 

                // Draw image to fill the Printable Rectangle exactly
                ctx.drawImage(currentImg, startX, 0, wPrintable, hPrintable);
            }

            const tex = new THREE.CanvasTexture(cvs);
            tex.colorSpace = THREE.SRGBColorSpace;
            tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
            
            body.material.map = tex;
            body.material.needsUpdate = true;
        }

        function bindUI() {
            // Upload
            document.getElementById('imgInp').addEventListener('change', (e) => {
                const f = e.target.files[0];
                if(!f) return;
                const r = new FileReader();
                r.onload = (ev) => {
                    const img = new Image();
                    img.src = ev.target.result;
                    img.onload = () => {
                        currentImg = img;
                        updateTexture(document.getElementById('colBase').value);
                    };
                };
                r.readAsDataURL(f);
            });

            // Colors
            document.getElementById('colBase').addEventListener('input', (e) => updateTexture(e.target.value));
            
            const setCol = (mesh, hex) => mesh.material.color.set(hex);
            document.getElementById('colInner').addEventListener('input', (e) => setCol(inner, e.target.value));
            document.getElementById('colHandle').addEventListener('input', (e) => setCol(handle, e.target.value));

            // Rotate
            document.getElementById('chkRotate').addEventListener('change', (e) => rotating = e.target.checked);
        }

        window.saveSnap = function() {
            renderer.render(scene, camera);
            const a = document.createElement('a');
            a.download = 'full-wrap-preview.png';
            a.href = renderer.domElement.toDataURL('image/png');
            a.click();
        }

        function onResize() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        }

        function loop() {
            requestAnimationFrame(loop);
            controls.update();
            if(rotating && mug) mug.rotation.y += 0.005;
            renderer.render(scene, camera);
        }
    </script>
</body>
</html>