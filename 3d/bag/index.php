<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Bag Studio - Precise Inch Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { margin: 0; overflow: hidden; background: #111; font-family: sans-serif; }
        #canvas-container { width: 100vw; height: 100vh; }
        .admin-panel { display: none; }
        .admin-panel.active { display: block; }
        #recording-status { display: none; }
        input[type="number"] { background: #f3f4f6; border: 1px solid #d1d5db; padding: 4px 8px; border-radius: 4px; width: 100%; }
    </style>
</head>
<body>

    <div id="admin-toggle" class="absolute left-6 top-6 z-50 p-3 bg-white rounded-xl cursor-pointer shadow-lg hover:scale-110 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
    </div>

    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-50 flex flex-col items-center gap-4">
        <div id="recording-status" class="bg-red-600 text-white px-6 py-2 rounded-full text-sm font-bold animate-pulse shadow-xl">
            🔴 RECORDING SLOW 360° VIEW...
        </div>
        <button id="download-video" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-10 rounded-full shadow-2xl transition-all flex items-center gap-3">
            <i class="fas fa-video"></i> Download Studio Quality Video
        </button>
    </div>

    <div id="admin-panel" class="admin-panel absolute right-0 top-0 h-full w-80 bg-white shadow-2xl z-40 p-6 overflow-y-auto border-l border-gray-200">
        <h2 class="text-xl font-bold mb-6 text-gray-800">Bag Configuration</h2>
        
        <div class="space-y-5">
            <section>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Dimensions (Inches)</label>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <span class="text-[10px]">Width</span>
                        <input type="number" id="width" value="10" step="0.5">
                    </div>
                    <div>
                        <span class="text-[10px]">Height</span>
                        <input type="number" id="height" value="12" step="0.5">
                    </div>
                    <div>
                        <span class="text-[10px]">Depth</span>
                        <input type="number" id="depth" value="4" step="0.5">
                    </div>
                </div>
            </section>

            <section>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Visual Colors</label>
                <div class="flex gap-4">
                    <div class="flex-1 text-[10px]">Bag Body <input type="color" id="bagColor" value="#ffffff" class="w-full h-8 mt-1"></div>
                    <div class="flex-1 text-[10px]">Handles <input type="color" id="handleColor" value="#1a1a1a" class="w-full h-8 mt-1"></div>
                </div>
            </section>

            <section class="space-y-3">
                <label class="block text-xs font-bold text-gray-500 uppercase">Textures (Branding)</label>
                <div class="text-[10px]">Front Image <input type="file" id="frontUpload" class="mt-1 block w-full text-xs"></div>
                <div class="text-[10px]">Back Image <input type="file" id="backUpload" class="mt-1 block w-full text-xs"></div>
                <div class="text-[10px]">Environment BG <input type="file" id="bgUpload" class="mt-1 block w-full text-xs"></div>
            </section>
        </div>
    </div>

    <div id="canvas-container"></div>

    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
            }
        }
    </script>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        let scene, camera, renderer, controls, bag, handleGroup;
        let isRecording = false;
        const loader = new THREE.TextureLoader();

        // Conversion Factor: We treat 1 Unit in Three.js as 10 Inches for visual scale
        const INCH_FACTOR = 0.1; 

        function init() {
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xf1f5f9);

            camera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.set(4, 3, 5);

            renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(window.devicePixelRatio);
            renderer.shadowMap.enabled = true;
            document.getElementById('canvas-container').appendChild(renderer.domElement);

            scene.add(new THREE.AmbientLight(0xffffff, 0.7));
            const sun = new THREE.DirectionalLight(0xffffff, 1.0);
            sun.position.set(5, 10, 7);
            sun.castShadow = true;
            scene.add(sun);

            // Bag Setup
            const geometry = new THREE.BoxGeometry(1, 1, 1);
            const materials = [
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Right
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Left
                new THREE.MeshStandardMaterial({ transparent: true, opacity: 0 }), // Top (Open)
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Bottom
                new THREE.MeshStandardMaterial({ color: 0xffffff }), // Front
                new THREE.MeshStandardMaterial({ color: 0xffffff })  // Back
            ];
            bag = new THREE.Mesh(geometry, materials);
            bag.castShadow = true;
            scene.add(bag);

            // Handle Setup (Bezier Cord)
            handleGroup = new THREE.Group();
            const hCurve = new THREE.CubicBezierCurve3(
                new THREE.Vector3(-0.3, 0, 0), new THREE.Vector3(-0.3, 0.4, 0),
                new THREE.Vector3(0.3, 0.4, 0), new THREE.Vector3(0.3, 0, 0)
            );
            const hGeo = new THREE.TubeGeometry(hCurve, 25, 0.02, 10, false);
            const hMat = new THREE.MeshStandardMaterial({ color: 0x1a1a1a });
            const hFront = new THREE.Mesh(hGeo, hMat);
            const hBack = new THREE.Mesh(hGeo, hMat);
            handleGroup.add(hFront, hBack);
            scene.add(handleGroup);

            updateSizing();

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            animate();
        }

        function updateSizing() {
            // Get Inch Values and scale them
            const w = parseFloat(document.getElementById('width').value) * INCH_FACTOR;
            const h = parseFloat(document.getElementById('height').value) * INCH_FACTOR;
            const d = parseFloat(document.getElementById('depth').value) * INCH_FACTOR;

            bag.scale.set(w, h, d);
            
            handleGroup.position.y = h / 2;
            handleGroup.scale.x = w * 0.8; // Scale handles slightly with width
            handleGroup.children[0].position.z = (d / 2);
            handleGroup.children[1].position.z = -(d / 2);
        }

        function animate() {
            requestAnimationFrame(animate);
            if (isRecording) {
                // SLOW ROTATION: 0.01 instead of 0.05
                bag.rotation.y += 0.012; 
                handleGroup.rotation.y += 0.012;
            }
            controls.update();
            renderer.render(scene, camera);
        }

        // --- VIDEO EXPORT (SLOW MOTION) ---
        document.getElementById('download-video').onclick = async () => {
            const stream = renderer.domElement.captureStream(60); // High quality capture
            const recorder = new MediaRecorder(stream, { mimeType: 'video/webm;codecs=vp9', bitsPerSecond: 5000000 });
            const chunks = [];

            recorder.ondataavailable = (e) => chunks.push(e.data);
            recorder.onstop = () => {
                const blob = new Blob(chunks, { type: 'video/webm' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'bag-design-360-slow.webm';
                a.click();
                isRecording = false;
                document.getElementById('recording-status').style.display = 'none';
            };

            isRecording = true;
            document.getElementById('recording-status').style.display = 'block';
            recorder.start();

            // Record for 10 seconds to allow for the slower rotation speed
            setTimeout(() => recorder.stop(), 10500); 
        };

        // UI Event Listeners
        document.getElementById('admin-toggle').onclick = () => document.getElementById('admin-panel').classList.toggle('active');
        ['width', 'height', 'depth'].forEach(id => document.getElementById(id).onchange = updateSizing);
        
        document.getElementById('bagColor').oninput = (e) => {
            bag.material.forEach((m, i) => { if(i !== 2) m.color.set(e.target.value); });
        };
        document.getElementById('handleColor').oninput = (e) => {
            handleGroup.children.forEach(h => h.material.color.set(e.target.value));
        };
        document.getElementById('frontUpload').onchange = (e) => {
            loader.load(URL.createObjectURL(e.target.files[0]), (t) => {
                bag.material[4].map = t; bag.material[4].needsUpdate = true;
            });
        };
        document.getElementById('backUpload').onchange = (e) => {
            loader.load(URL.createObjectURL(e.target.files[0]), (t) => {
                bag.material[5].map = t; bag.material[5].needsUpdate = true;
            });
        };
        document.getElementById('bgUpload').onchange = (e) => {
            loader.load(URL.createObjectURL(e.target.files[0]), (t) => { scene.background = t; });
        };

        init();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>