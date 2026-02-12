<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arham Tower - Luxury Skyscraper</title>
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
        :root { --primary: #00d4ff; --bg: #0a0a0a; --text: #fff; }
        body { margin: 0; overflow: hidden; background: var(--bg); font-family: 'Outfit', sans-serif; color: var(--text); user-select: none; }
        #canvas-container { width: 100%; height: 100vh; position: absolute; top:0; left:0; z-index: 1; }

        /* --- UI LAYER --- */
        .ui-layer { pointer-events: none; position: fixed; inset: 0; z-index: 10; display: flex; flex-direction: column; justify-content: space-between; padding: 30px; }
        
        .brand { text-align: left; pointer-events: auto; }
        .brand h1 { font-weight: 800; font-size: 2rem; margin: 0; letter-spacing: -1px; color: #fff; text-transform: uppercase; }
        .brand span { color: var(--primary); }
        
        /* Quick Jump Menu */
        .floor-selector {
            position: absolute; left: 30px; top: 50%; transform: translateY(-50%);
            display: flex; flex-direction: column; gap: 5px; pointer-events: auto;
            max-height: 60vh; overflow-y: auto; padding-right: 10px;
        }
        .floor-selector::-webkit-scrollbar { width: 3px; }
        .floor-selector::-webkit-scrollbar-thumb { background: var(--primary); }
        
        .jump-btn {
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            color: #fff; padding: 5px 12px; border-radius: 4px; cursor: pointer;
            font-size: 0.7rem; transition: 0.2s; text-align: center;
        }
        .jump-btn:hover, .jump-btn.active { background: var(--primary); color: #000; font-weight: bold; }

        /* Elevator Panel */
        .elevator-ui {
            position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
            display: flex; flex-direction: column; gap: 20px; pointer-events: auto;
            background: rgba(0,0,0,0.6); padding: 25px 15px; border-radius: 50px; backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1); align-items: center;
        }

        .nav-btn {
            width: 50px; height: 50px; border-radius: 50%;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; cursor: pointer; transition: 0.3s;
        }
        .nav-btn:hover { background: var(--primary); color: #000; box-shadow: 0 0 15px var(--primary); }
        
        .floor-display { text-align: center; }
        .floor-number { font-size: 1.8rem; color: var(--primary); display: block; font-weight: 700; }
        .floor-label { font-size: 0.6rem; text-transform: uppercase; opacity: 0.7; letter-spacing: 1px; }

        .wa-float {
            position: fixed; bottom: 80px; right: 100px; z-index: 50;
            background: #25D366; color: white; padding: 12px 25px; border-radius: 50px;
            text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); pointer-events: auto; transition: 0.3s;
        }

        #loader { position: fixed; inset: 0; background: #000; z-index: 100; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--primary); }
    </style>
</head>
<body>

    <div id="loader">
        <div style="letter-spacing: 5px;">ASCENDING TOWER...</div>
    </div>

    <div id="canvas-container"></div>

    <div class="ui-layer">
        <div class="brand">
            <h1>Arham<span>Tower</span></h1>
            <div id="room-name">Lobby</div>
        </div>

        <div class="floor-selector" id="floor-menu">
            </div>

        <div class="elevator-ui">
            <div class="nav-btn" onclick="window.gotoRoom(activeRoomIdx + 1)"><i class="fas fa-chevron-up"></i></div>
            <div class="floor-display">
                <span class="floor-label">Floor</span>
                <span class="floor-number" id="floor-num">00</span>
            </div>
            <div class="nav-btn" onclick="window.gotoRoom(activeRoomIdx - 1)"><i class="fas fa-chevron-down"></i></div>
        </div>
    </div>

    <a href="https://wa.me/923006238233" target="_blank" class="wa-float">
        <i class="fab fa-whatsapp"></i> ORDER DESIGN
    </a>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
        import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
        import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
        import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';
        import * as TWEEN from '@tweenjs/tween.js';

        window.activeRoomIdx = 0;
        const FLOOR_HEIGHT = 30; 
        let configData = { rooms: [] };

        let scene, camera, renderer, controls, worldGroup, composer;
        const texLoader = new THREE.TextureLoader();

        fetch('data.json?v=' + Date.now()) 
            .then(res => res.json())
            .then(data => {
                configData = data;
                init();
                document.getElementById('loader').style.opacity = 0;
                setTimeout(() => document.getElementById('loader').style.display = 'none', 800);
            });

        function init() {
            scene = new THREE.Scene();
            scene.fog = new THREE.FogExp2(0x000000, 0.005);

            // 360 Background
            texLoader.load('uploads/background.webp', (t) => {
                t.mapping = THREE.EquirectangularReflectionMapping;
                scene.background = t;
                scene.environment = t;
            });

            camera = new THREE.PerspectiveCamera(65, window.innerWidth/window.innerHeight, 0.1, 1000);
            camera.position.set(15, 10, 15);

            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            document.getElementById('canvas-container').appendChild(renderer.domElement);

            composer = new EffectComposer(renderer);
            composer.addPass(new RenderPass(scene, camera));
            composer.addPass(new UnrealBloomPass(new THREE.Vector2(window.innerWidth, window.innerHeight), 0.3, 0.4, 0.85));
            composer.addPass(new OutputPass());

            scene.add(new THREE.AmbientLight(0xffffff, 0.6));
            const pLight = new THREE.PointLight(0xffffff, 200);
            pLight.position.set(0, 20, 0);
            scene.add(pLight);

            worldGroup = new THREE.Group();
            scene.add(worldGroup);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.target.set(0, 5, 0);

            buildTower();
            createJumpMenu();
            updateUI();
            animate();
            
            window.addEventListener('resize', onResize);
        }

        function buildTower() {
            // FIX: Accessing configData.rooms instead of configData
            configData.rooms.forEach((room, idx) => {
                const floor = new THREE.Group();
                floor.position.y = idx * FLOOR_HEIGHT;

                const getMat = (side) => {
                    const s = room.surfaces[side];
                    const mat = new THREE.MeshStandardMaterial({ side: THREE.BackSide });
                    if(side === 'front') { mat.transparent = true; mat.opacity = 0.2; } // Glass Window
                    
                    if(s && s.tex) {
                        texLoader.load(s.tex, (t) => {
                            t.colorSpace = THREE.SRGBColorSpace;
                            mat.map = t; mat.opacity = 1; mat.needsUpdate = true;
                        });
                    }
                    if(s && s.col) mat.color.set(s.col);
                    return mat;
                };

                const mats = [getMat('right'), getMat('left'), getMat('ceil'), getMat('floor'), getMat('front'), getMat('back')];
                const shell = new THREE.Mesh(new THREE.BoxGeometry(room.w, room.h, room.d), mats);
                shell.position.y = room.h / 2;
                floor.add(shell);
                worldGroup.add(floor);
            });
        }

        function createJumpMenu() {
            const menu = document.getElementById('floor-menu');
            configData.rooms.forEach((room, idx) => {
                const btn = document.createElement('div');
                btn.className = 'jump-btn';
                btn.innerText = "F" + (idx + 30); // Labeling floors 30, 31...
                btn.onclick = () => window.gotoRoom(idx);
                menu.appendChild(btn);
            });
        }

        window.gotoRoom = function(idx) {
            if(idx < 0 || idx >= configData.rooms.length) return;
            window.activeRoomIdx = idx;
            const targetY = idx * FLOOR_HEIGHT;

            new TWEEN.Tween(camera.position)
                .to({ y: targetY + 10 }, 2000)
                .easing(TWEEN.Easing.Quadratic.InOut)
                .start();

            new TWEEN.Tween(controls.target)
                .to({ y: targetY + 5 }, 2000)
                .easing(TWEEN.Easing.Quadratic.InOut)
                .start();

            updateUI();
        };

        function updateUI() {
            const room = configData.rooms[window.activeRoomIdx];
            document.getElementById('room-name').innerText = room.name;
            document.getElementById('floor-num').innerText = (window.activeRoomIdx + 30);
            
            // Highlight active button
            const btns = document.querySelectorAll('.jump-btn');
            btns.forEach((b, i) => b.classList.toggle('active', i === window.activeRoomIdx));
        }

        function onResize() {
            camera.aspect = window.innerWidth/window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
            composer.setSize(window.innerWidth, window.innerHeight);
        }

        function animate() {
            requestAnimationFrame(animate);
            TWEEN.update();
            controls.update();
            composer.render();
        }
    </script>
</body>
</html>