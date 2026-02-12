<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>3D Market Architect | Arham Printers Project</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    
    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
        }
    }
    </script>

    <style>
        :root { --accent: #2563eb; --bg: #f3f4f6; --panel: #ffffff; }
        body { margin: 0; overflow: hidden; font-family: 'Outfit', sans-serif; background: var(--bg); }
        
        #canvas-container { width: 100%; height: 100vh; }

        /* --- CONTROL PANEL UI --- */
        .controls {
            position: absolute; top: 10px; left: 10px; width: 340px;
            background: var(--panel); padding: 0; border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;
            display: flex; flex-direction: column; z-index: 100;
        }
        
        .header { padding: 15px 20px; border-bottom: 1px solid #eee; background: #fafafa; border-radius: 12px 12px 0 0; }
        h2 { margin: 0; font-size: 1.1rem; color: #1f2937; display: flex; align-items: center; gap: 10px; }
        
        .tabs { display: flex; background: #eee; padding: 5px; gap: 5px; }
        .tab { flex: 1; padding: 8px; text-align: center; cursor: pointer; font-size: 0.8rem; font-weight: 700; color: #666; border-radius: 4px; }
        .tab.active { background: white; color: var(--accent); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        .panel-content { padding: 20px; display: none; }
        .panel-content.active { display: block; }

        .input-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
        .input-field { display: flex; flex-direction: column; margin-bottom: 10px; }
        label { font-size: 0.7rem; font-weight: 700; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; }
        input, select { 
            padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; 
            font-family: inherit; font-size: 0.9rem; outline: none; transition: 0.2s;
        }
        input:focus, select:focus { border-color: var(--accent); }
        input[type="color"] { width: 100%; height: 35px; padding: 0; border: none; cursor: pointer; }

        .btn {
            width: 100%; padding: 12px; border: none; border-radius: 8px;
            font-weight: 700; cursor: pointer; margin-top: 10px; transition: 0.2s; font-size: 0.9rem;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-danger { background: #fee2e2; color: #991b1b; }
        .btn-success { background: #dcfce7; color: #166534; }

        .section-title { font-size: 0.8rem; font-weight: 800; color: #111; margin: 15px 0 10px; border-bottom: 2px solid #eee; padding-bottom: 5px; }

        .hint { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.8); color: white; padding: 10px 20px; border-radius: 30px; font-size: 0.9rem; pointer-events: none; z-index: 10; white-space: nowrap; }
        
        #selection-status { padding: 10px 20px; background: #fffbe6; border-bottom: 1px solid #ffe58f; color: #d48806; font-size: 0.85rem; font-weight: 600; text-align: center; display: none; }

        /* Mobile Adjustments */
        @media(max-width: 600px) {
            .controls { width: 90%; left: 5%; top: 10px; max-height: 40vh; }
            .hint { display: none; }
        }
    </style>
</head>
<body>

    <div id="canvas-container"></div>

    <div class="controls">
        <div class="header">
            <h2>🏗️ 3D Layout Planner</h2>
        </div>
        
        <div id="selection-status">No Shop Selected</div>

        <div class="tabs">
            <div class="tab active" onclick="switchTab('structure')">📐 Size</div>
            <div class="tab" onclick="switchTab('materials')">🎨 Paint</div>
            <div class="tab" onclick="switchTab('openings')">🚪 Door</div>
        </div>

        <div id="tab-structure" class="panel-content active">
            <div class="input-group">
                <div class="input-field"><label>Width (ft)</label><input type="number" id="w" value="15"></div>
                <div class="input-field"><label>Depth (ft)</label><input type="number" id="d" value="20"></div>
            </div>
            <div class="input-field"><label>Height (ft)</label><input type="number" id="h" value="12"></div>
            
            <div class="section-title">Position</div>
            <div class="input-group">
                <div class="input-field"><label>X Position</label><input type="number" id="x" value="0"></div>
                <div class="input-field"><label>Z Position</label><input type="number" id="z" value="0"></div>
            </div>
            <div class="input-field"><label>Rotation (°)</label><input type="number" id="rot" value="0"></div>

            <button class="btn btn-primary" onclick="addShop()">+ CREATE SHOP</button>
            <button class="btn btn-success" onclick="updateSelected()">✓ UPDATE SELECTED</button>
            <button class="btn btn-danger" onclick="deleteSelected()">🗑️ DELETE</button>
        </div>

        <div id="tab-materials" class="panel-content">
            <div class="section-title">Wall Color</div>
            <div class="input-group">
                <div class="input-field"><input type="color" id="wallColor" value="#ffffff"></div>
            </div>

            <div class="section-title">Roof Color</div>
            <div class="input-group">
                <div class="input-field"><input type="color" id="roofColor" value="#333333"></div>
            </div>

            <div class="section-title">Floor Color</div>
            <div class="input-group">
                <div class="input-field"><input type="color" id="floorColor" value="#cccccc"></div>
            </div>
            
            <button class="btn btn-success" onclick="applyMaterials()">🎨 APPLY COLORS</button>
        </div>

        <div id="tab-openings" class="panel-content">
            <div class="input-group">
                <div class="input-field">
                    <label>Type</label>
                    <select id="openType">
                        <option value="door">Door 🚪</option>
                        <option value="window">Window 🪟</option>
                    </select>
                </div>
                <div class="input-field">
                    <label>Side</label>
                    <select id="openFace">
                        <option value="front">Front</option>
                        <option value="back">Back</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </div>
            </div>
            
            <div class="input-group">
                <div class="input-field"><label>W</label><input type="number" id="openW" value="4"></div>
                <div class="input-field"><label>H</label><input type="number" id="openH" value="7"></div>
            </div>
            
            <div class="input-field"><label>Shift Left/Right</label><input type="number" id="openPos" value="0"></div>

            <button class="btn btn-primary" onclick="addOpening()">+ ADD OPENING</button>
            <button class="btn btn-danger" onclick="clearOpenings()">x CLEAR ALL</button>
        </div>
    </div>

    <div class="hint">Left Click: Rotate | Right Click: Pan | Scroll: Zoom</div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        // --- STATE ---
        let scene, camera, renderer, controls, raycaster, mouse;
        let shops = [];
        let selectedShop = null;

        init();

        function init() {
            const container = document.getElementById('canvas-container');

            // 1. Scene
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xdcebf5); 
            scene.fog = new THREE.Fog(0xdcebf5, 30, 300);

            // 2. Camera
            camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 1000);
            camera.position.set(50, 50, 50);

            // 3. Renderer
            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            container.appendChild(renderer.domElement);

            // 4. Lighting
            const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.8);
            scene.add(hemiLight);

            const dirLight = new THREE.DirectionalLight(0xffffff, 1.5);
            dirLight.position.set(50, 100, 50);
            dirLight.castShadow = true;
            dirLight.shadow.mapSize.width = 2048;
            dirLight.shadow.mapSize.height = 2048;
            scene.add(dirLight);

            // 5. Ground
            const groundGeo = new THREE.PlaneGeometry(1000, 1000);
            const groundMat = new THREE.MeshStandardMaterial({ color: 0xe0e0e0 });
            const ground = new THREE.Mesh(groundGeo, groundMat);
            ground.rotation.x = -Math.PI / 2;
            ground.receiveShadow = true;
            scene.add(ground);
            
            const grid = new THREE.GridHelper(1000, 100, 0x000000, 0x000000);
            grid.material.opacity = 0.1;
            grid.material.transparent = true;
            scene.add(grid);

            // 6. Controls
            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            
            // 7. Interaction
            raycaster = new THREE.Raycaster();
            mouse = new THREE.Vector2();
            window.addEventListener('click', onMouseClick);
            window.addEventListener('resize', onResize);

            animate();
        }

        // --- SHOP CREATION ---
        window.addShop = function() {
            const cfg = getStructureConfig();
            createShopMesh(cfg);
            // Auto advance X position
            document.getElementById('x').value = parseFloat(cfg.x) + parseFloat(cfg.w) + 2; 
        };

        function createShopMesh(cfg) {
            const group = new THREE.Group();
            group.userData = { ...cfg, type: 'shop' };

            const geometry = new THREE.BoxGeometry(cfg.w, cfg.h, cfg.d);
            const mats = [
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Right
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Left
                new THREE.MeshStandardMaterial({ color: 0x333333 }), // Roof
                new THREE.MeshStandardMaterial({ color: 0xcccccc }), // Floor
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Front
                new THREE.MeshStandardMaterial({ color: 0xffffff })  // Back
            ];

            const mesh = new THREE.Mesh(geometry, mats);
            mesh.position.y = cfg.h / 2;
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            mesh.name = "structure";
            group.add(mesh);

            // Selection Outline
            const edges = new THREE.EdgesGeometry(geometry);
            const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x2563eb, visible: false }));
            line.position.y = cfg.h / 2;
            line.name = "outline";
            group.add(line);

            group.position.set(cfg.x, 0, cfg.z);
            group.rotation.y = THREE.MathUtils.degToRad(cfg.rot);

            scene.add(group);
            shops.push(group);
            selectShop(group);
        }

        // --- EDITING ---
        window.updateSelected = function() {
            if(!selectedShop) return;
            const cfg = getStructureConfig();
            
            // Remove old structure
            const oldMesh = selectedShop.getObjectByName("structure");
            selectedShop.remove(oldMesh);
            selectedShop.remove(selectedShop.getObjectByName("outline"));

            // Rebuild
            const geometry = new THREE.BoxGeometry(cfg.w, cfg.h, cfg.d);
            const mesh = new THREE.Mesh(geometry, oldMesh.material); // Keep old colors
            mesh.position.y = cfg.h / 2;
            mesh.castShadow = true; mesh.receiveShadow = true;
            mesh.name = "structure";
            selectedShop.add(mesh);

            const edges = new THREE.EdgesGeometry(geometry);
            const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x2563eb, visible: true }));
            line.position.y = cfg.h / 2;
            line.name = "outline";
            selectedShop.add(line);

            selectedShop.position.set(cfg.x, 0, cfg.z);
            selectedShop.rotation.y = THREE.MathUtils.degToRad(cfg.rot);
            selectedShop.userData = { ...selectedShop.userData, ...cfg };
        };

        window.applyMaterials = function() {
            if(!selectedShop) return alert("Select a shop first!");
            const mesh = selectedShop.getObjectByName("structure");
            const mats = mesh.material;
            
            // Walls: 0,1,4,5
            const wallC = document.getElementById('wallColor').value;
            [0,1,4,5].forEach(i => mats[i].color.set(wallC));
            
            // Roof: 2
            mats[2].color.set(document.getElementById('roofColor').value);
            
            // Floor: 3
            mats[3].color.set(document.getElementById('floorColor').value);
        };

        window.addOpening = function() {
            if(!selectedShop) return alert("Select a shop first!");
            
            const type = document.getElementById('openType').value;
            const face = document.getElementById('openFace').value;
            const w = parseFloat(document.getElementById('openW').value);
            const h = parseFloat(document.getElementById('openH').value);
            const pos = parseFloat(document.getElementById('openPos').value);
            
            const shopW = selectedShop.userData.w;
            const shopD = selectedShop.userData.d;

            const geo = new THREE.PlaneGeometry(w, h);
            const mat = new THREE.MeshStandardMaterial({ 
                color: type === 'door' ? 0x4a3c31 : 0x88ccff, 
                side: THREE.DoubleSide,
                roughness: 0.1
            });

            const mesh = new THREE.Mesh(geo, mat);
            mesh.name = "opening";
            const offset = 0.05;

            if(face === 'front') mesh.position.set(pos, h/2, shopD/2 + offset);
            if(face === 'back') { mesh.position.set(-pos, h/2, -shopD/2 - offset); mesh.rotation.y = Math.PI; }
            if(face === 'left') { mesh.position.set(-shopW/2 - offset, h/2, pos); mesh.rotation.y = -Math.PI/2; }
            if(face === 'right') { mesh.position.set(shopW/2 + offset, h/2, -pos); mesh.rotation.y = Math.PI/2; }

            if(type === 'window') mesh.position.y = (parseFloat(document.getElementById('h').value)/2);

            selectedShop.add(mesh);
        };

        window.clearOpenings = function() {
            if(!selectedShop) return;
            for(let i = selectedShop.children.length - 1; i >= 0; i--) {
                if(selectedShop.children[i].name === "opening") selectedShop.remove(selectedShop.children[i]);
            }
        };

        window.deleteSelected = function() {
            if(!selectedShop) return;
            scene.remove(selectedShop);
            shops = shops.filter(s => s !== selectedShop);
            deselectAll();
        };

        // --- HELPERS ---
        function onMouseClick(event) {
            if(event.target.closest('.controls')) return;
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObjects(shops, true);

            if (intersects.length > 0) {
                let target = intersects[0].object;
                while(target.parent && target.parent.type !== 'Scene') target = target.parent;
                selectShop(target);
            } else {
                deselectAll();
            }
        }

        function selectShop(shopGroup) {
            deselectAll();
            selectedShop = shopGroup;
            const outline = selectedShop.getObjectByName("outline");
            if(outline) outline.material.visible = true;

            const d = shopGroup.userData;
            document.getElementById('w').value = d.w;
            document.getElementById('h').value = d.h;
            document.getElementById('d').value = d.d;
            document.getElementById('x').value = d.x;
            document.getElementById('z').value = d.z;
            document.getElementById('rot').value = d.rot;
            document.getElementById('selection-status').style.display = 'block';
            document.getElementById('selection-status').innerText = "Selected: Shop ID " + shopGroup.id;
        }

        function deselectAll() {
            selectedShop = null;
            document.getElementById('selection-status').style.display = 'none';
            shops.forEach(s => {
                const outline = s.getObjectByName("outline");
                if(outline) outline.material.visible = false;
            });
        }

        function getStructureConfig() {
            return {
                w: parseFloat(document.getElementById('w').value),
                h: parseFloat(document.getElementById('h').value),
                d: parseFloat(document.getElementById('d').value),
                x: parseFloat(document.getElementById('x').value),
                z: parseFloat(document.getElementById('z').value),
                rot: parseFloat(document.getElementById('rot').value)
            };
        }

        window.switchTab = function(tabName) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.panel-content').forEach(c => c.classList.remove('active'));
            const tabs = ['structure', 'materials', 'openings'];
            document.querySelectorAll('.tab')[tabs.indexOf(tabName)].classList.add('active');
            document.getElementById('tab-'+tabName).classList.add('active');
        };

        function onResize() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        }

        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }
    </script>
</body>
</html>
