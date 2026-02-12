<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arham Printers - Wallpaper Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'seo.php'; ?>
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
        :root { --primary: #00d4ff; --bg: #e0e0e0; --text: #222; }
        body { margin: 0; overflow: hidden; background: var(--bg); font-family: 'Outfit', sans-serif; color: var(--text); user-select: none; }
        #canvas-container { width: 100%; height: 100vh; position: absolute; top:0; left:0; z-index: 1; }

        /* --- UI LAYER --- */
        .ui-layer { pointer-events: none; position: fixed; inset: 0; z-index: 10; display: flex; flex-direction: column; justify-content: space-between; padding: 20px; }
        
        .brand { text-align: center; margin-top: 10px; pointer-events: auto; }
        .brand h1 { font-weight: 800; font-size: 1.5rem; margin: 0; letter-spacing: 2px; color: #000; text-transform: uppercase; text-shadow: 0 2px 10px rgba(255,255,255,0.8); }
        .brand span { color: var(--primary); }
        .room-info { font-size: 0.9rem; color: #444; margin-top: 5px; font-weight: 600; background: rgba(255,255,255,0.6); display: inline-block; padding: 2px 10px; border-radius: 10px; backdrop-filter: blur(5px); }

        .nav-btn {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 50px; height: 50px; border-radius: 50%;
            background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5); color: #000;
            backdrop-filter: blur(5px);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; cursor: pointer; pointer-events: auto;
            transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .nav-btn:hover { background: var(--primary); color: white; transform: translateY(-50%) scale(1.1); }
        .nav-prev { left: 15px; }
        .nav-next { right: 15px; }

        /* WhatsApp Button */
        .wa-float {
            position: fixed; bottom: 70px; left: 50%; transform: translateX(-50%); z-index: 50;
            background: #25D366; color: white; padding: 12px 30px; border-radius: 50px;
            text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3); pointer-events: auto; font-size: 1rem;
        }
        .wa-float:hover { transform: translateX(-50%) translateY(-5px); box-shadow: 0 10px 25px rgba(37, 211, 102, 0.5); }

        /* Ticker */
        .neon-ticker-wrap {
            position: fixed; bottom: 0; left: 0; width: 100%; height: 60px;
            background: #111; border-top: 2px solid var(--primary);
            z-index: 90; display: flex; align-items: center; overflow: hidden; pointer-events: none;
        }
        .ticker-text {
            white-space: nowrap; font-family: 'Outfit', sans-serif; font-weight: 700; text-transform: uppercase;
            color: #fff; text-shadow: 0 0 10px var(--primary); font-size: 1.2rem; letter-spacing: 2px;
            animation: tickerAnim 25s linear infinite; padding-left: 100%;
        }
        @keyframes tickerAnim { 0% { transform: translate3d(0, 0, 0); } 100% { transform: translate3d(-100%, 0, 0); } }

        /* Loader */
        #loader { position: fixed; inset: 0; background: #eee; z-index: 100; display: flex; align-items: center; justify-content: center; font-weight: bold; letter-spacing: 2px; transition: opacity 0.5s; }
    </style>
</head>
<body>

    <div id="loader">LOADING SHOWROOM...</div>
    <div id="canvas-container"></div>

    <div class="ui-layer">
        <div class="brand">
            <h1>Arham<span>Printers</span></h1>
            <div class="room-info" id="room-name">Loading...</div>
        </div>
        <div class="nav-btn nav-prev" onclick="window.moveRoom(-1)">&#10094;</div>
        <div class="nav-btn nav-next" onclick="window.moveRoom(1)">&#10095;</div>
    </div>

    <a href="https://wa.me/923006238233" target="_blank" class="wa-float">
        <i class="fab fa-whatsapp" style="font-size:1.3rem;"></i> ORDER THIS DESIGN
    </a>

    <div class="neon-ticker-wrap">
        <div class="ticker-text">
            ARHAM PRINTERS &nbsp;✦&nbsp; BANNERS &nbsp;✦&nbsp; 3D WALLPAPERS &nbsp;✦&nbsp; WEDDING CARDS &nbsp;✦&nbsp; OFFICE AND BUSINESS ESSENTIALS &nbsp;✦&nbsp; MARKETING MATERIAL &nbsp;✦&nbsp; GIFTS &nbsp;✦&nbsp; PAPER AND PHOTO PRINTS
        </div>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
        import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
        import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
        import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';
        import * as TWEEN from '@tweenjs/tween.js';

        // --- GLOBAL VARIABLES ---
        let activeRoomIdx = 0;
        const ROOM_SPACING = 50; 
        let config = { rooms: [] };

        // THREE.JS VARS
        let scene, camera, renderer, controls, worldGroup;
        let composer; // Post-processing manager
        const texLoader = new THREE.TextureLoader();

        // --- FETCH JSON DATA ---
        fetch('data.json?v=' + Date.now()) 
            .then(response => response.json())
            .then(data => {
                config = data;
                init();
                document.getElementById('loader').style.opacity = 0;
                setTimeout(() => document.getElementById('loader').style.display = 'none', 500);
            })
            .catch(err => {
                document.getElementById('loader').innerText = "ERROR LOADING DATA.JSON";
                console.error(err);
            });

        function init() {
            const container = document.getElementById('canvas-container');
            
            // 1. Scene & 3D Background
scene = new THREE.Scene();

// Load the 360 Background Image
const bgLoader = new THREE.TextureLoader();
bgLoader.load('uploads/background.webp', function(texture) {
    texture.mapping = THREE.EquirectangularReflectionMapping; 
    texture.colorSpace = THREE.SRGBColorSpace;
    
    // Set the image as the background
    scene.background = texture;
    
    // OPTIONAL: This makes your walls/floors reflect the background image!
    // If it looks too messy, you can comment this line out.
    scene.environment = texture; 
});

// IMPORTANT: Reduce Fog density! 
// If fog is too thick (0.02), it will hide your background. 
// Change it to 0.002 or remove it completely.
scene.fog = new THREE.FogExp2(0x111111, 0.005); 

            // 2. Camera (Wide Corner View)
            const isMobile = window.innerWidth < 768;
            const fov = isMobile ? 85 : 65; 
            camera = new THREE.PerspectiveCamera(fov, window.innerWidth/window.innerHeight, 0.1, 1000);
            camera.position.set(8, 6, 8); 

            // 3. Renderer
            renderer = new THREE.WebGLRenderer({ antialias: false, alpha: false }); // Antialias false because composer handles it
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio); // High quality
            renderer.toneMapping = THREE.ACESFilmicToneMapping; // Cinematic colors
            renderer.toneMappingExposure = 1.0;
            container.appendChild(renderer.domElement);

            // 4. POST-PROCESSING (The Cinematic Effect)
            const renderScene = new RenderPass(scene, camera);

            // Bloom Parameters: Resolution, Strength, Radius, Threshold
            const bloomPass = new UnrealBloomPass( new THREE.Vector2( window.innerWidth, window.innerHeight ), 1.5, 0.4, 0.85 );
            bloomPass.threshold = 0.8;  // Only very bright things glow
            bloomPass.strength = 0.35;  // Intensity of glow
            bloomPass.radius = 0.5;     // Softness

            const outputPass = new OutputPass(); // Handles color correction

            composer = new EffectComposer(renderer);
            composer.addPass(renderScene);
            composer.addPass(bloomPass);
            composer.addPass(outputPass);

            // 5. Lighting
            const ambient = new THREE.AmbientLight(0xffffff, 0.8);
            scene.add(ambient);
            
            // Main bulb - brighter to trigger bloom
            const bulb = new THREE.PointLight(0xfffaed, 2.5, 30);
            bulb.position.set(0, 8, 0); 
            scene.add(bulb);

            // Fill light for soft shadows
            const fill = new THREE.DirectionalLight(0xffffff, 1.2); 
            fill.position.set(5, 5, 5);
            scene.add(fill);

            // 6. World Group
            worldGroup = new THREE.Group(); 
            scene.add(worldGroup);

            // 7. Controls
            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.enablePan = false;
            controls.minDistance = 2; 
            controls.maxDistance = 14; 
            controls.target.set(0, 4, 0);

            // 8. Events
            window.addEventListener('resize', onResize);
            
            buildWorld();
            updateUI();
            animate();
        }

        // --- BUILD ROOMS FROM JSON ---
        function buildWorld() {
            while(worldGroup.children.length) worldGroup.remove(worldGroup.children[0]);

            config.rooms.forEach((room, idx) => {
                const g = new THREE.Group();
                g.position.x = idx * ROOM_SPACING;

                const getMat = (surfName) => {
                    const s = room.surfaces[surfName];
                    const mat = new THREE.MeshStandardMaterial({ 
                        side: THREE.BackSide,
                        roughness: 0.4, // Slight reflection for cinematic look
                        metalness: 0.1 
                    }); 
                    
                    if(s && s.tex && s.tex !== "") {
                        texLoader.load(s.tex, (t) => {
                            t.colorSpace = THREE.SRGBColorSpace;
                            t.wrapS = THREE.ClampToEdgeWrapping;
                            t.wrapT = THREE.ClampToEdgeWrapping;
                            t.repeat.set(1, 1); 
                            mat.map = t; mat.needsUpdate = true;
                        });
                    } 
                    if(s && s.col) mat.color.set(s.col);
                    return mat;
                };

                const mats = [
                    getMat('right'), getMat('left'), getMat('ceil'), 
                    getMat('floor'), getMat('front'), getMat('back')
                ];

                const shell = new THREE.Mesh(new THREE.BoxGeometry(room.w, room.h, room.d), mats);
                shell.position.y = room.h / 2;
                g.add(shell);

                worldGroup.add(g);
            });
        }

        // --- NAVIGATION ---
        window.moveRoom = function(dir) {
            const next = activeRoomIdx + dir;
            if(next >= 0 && next < config.rooms.length) {
                activeRoomIdx = next;
                
                const targetX = activeRoomIdx * ROOM_SPACING;
                
                // Fly to NEW ROOM
                new TWEEN.Tween(camera.position)
                    .to({ x: targetX + 8, y: 6, z: 8 }, 1500)
                    .easing(TWEEN.Easing.Cubic.InOut)
                    .start();
                    
                new TWEEN.Tween(controls.target)
                    .to({ x: targetX, y: 4, z: 0 }, 1500)
                    .easing(TWEEN.Easing.Cubic.InOut)
                    .start();

                updateUI();
            }
        }

        function updateUI() {
            if(!config.rooms[activeRoomIdx]) return;
            const rName = config.rooms[activeRoomIdx].name || ("Room " + (activeRoomIdx + 1));
            const count = (activeRoomIdx + 1) + " / " + config.rooms.length;
            document.getElementById('room-name').innerText = rName + " (" + count + ")";
            
            document.querySelector('.nav-prev').style.opacity = activeRoomIdx === 0 ? 0.3 : 1;
            document.querySelector('.nav-next').style.opacity = activeRoomIdx === config.rooms.length-1 ? 0.3 : 1;
        }

        function onResize() {
            const isMobile = window.innerWidth < 768;
            camera.fov = isMobile ? 85 : 65; 
            camera.aspect = window.innerWidth/window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            composer.setSize(window.innerWidth, window.innerHeight); // Resize composer too
        }

        function animate() {
            requestAnimationFrame(animate);
            TWEEN.update();
            controls.update();
            
            // Cinematic "Breathing" Camera Drift
            // Very subtle vertical movement to make the shot feel alive
            const time = Date.now() * 0.0005;
            camera.position.y += Math.sin(time) * 0.002;

            // RENDER WITH COMPOSER (NOT RENDERER)
            composer.render();
        }
    </script>
</body>
</html>