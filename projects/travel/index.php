<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Arham 3D - Basic</title>
    
    <style>
        body { margin: 0; background-color: #111; color: white; font-family: sans-serif; height: 100vh; overflow: hidden; }
        
        #status {
            position: absolute; top: 0; left: 0; width: 100%; padding: 10px;
            background: red; font-weight: bold; text-align: center; z-index: 999;
        }

        #canvas-box { width: 100%; height: 60%; background: #222; }

        #controls {
            width: 100%; height: 40%; background: #333; padding: 20px; box-sizing: border-box; overflow-y: auto;
        }

        .label { display: block; margin-bottom: 5px; color: #aaa; font-size: 12px; font-weight: bold; }
        input { width: 100%; padding: 10px; background: #222; border: 1px solid #444; color: white; margin-bottom: 15px; }
        button { width: 100%; padding: 15px; font-size: 16px; font-weight: bold; background: #2563eb; color: white; border: none; cursor: pointer; }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
</head>
<body>

    <div id="status">INITIALIZING...</div>

    <div id="canvas-box"></div>

    <div id="controls">
        <h3 style="margin-top:0;">Visiting Card Config</h3>
        
        <span class="label">FRONT IMAGE</span>
        <input type="file" id="input-front" onchange="upload(this, 'front')">

        <span class="label">BACK IMAGE</span>
        <input type="file" id="input-back" onchange="upload(this, 'back')">

        <button onclick="flip()">🔄 FLIP CARD</button>
    </div>

    <script>
        // GLOBALS
        var scene, camera, renderer, mesh, controls;
        var statusBox = document.getElementById('status');
        var container = document.getElementById('canvas-box');

        // ERROR TRAP
        window.onerror = function(msg) {
            statusBox.innerText = "ERROR: " + msg;
        };

        function start() {
            if (typeof THREE === 'undefined') {
                statusBox.innerText = "THREE.JS FAILED";
                return;
            }

            // 1. SCENE
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x222222);

            // 2. CAMERA
            var aspect = container.clientWidth / container.clientHeight;
            camera = new THREE.PerspectiveCamera(50, aspect, 0.1, 100);
            camera.position.z = 5;

            // 3. RENDERER
            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);

            // 4. CUBE (Using MeshBasicMaterial - NO LIGHTS NEEDED)
            var geometry = new THREE.BoxGeometry(3.5, 2.0, 0.05);
            
            // White Material (Basic)
            var mat = new THREE.MeshBasicMaterial({ color: 0xffffff });
            
            var materials = [
                mat.clone(), mat.clone(), mat.clone(), mat.clone(),
                mat.clone(), // Front
                mat.clone()  // Back
            ];

            mesh = new THREE.Mesh(geometry, materials);
            mesh.rotation.y = 0.5;
            scene.add(mesh);

            // 5. CONTROLS
            controls = new THREE.OrbitControls(camera, renderer.domElement);

            // SUCCESS
            statusBox.style.display = 'none';
            animate();
        }

        function upload(input, side) {
            var file = input.files[0];
            if(!file) return;

            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.onload = function() {
                    var tex = new THREE.Texture(img);
                    tex.needsUpdate = true;
                    
                    if(side === 'back') {
                        tex.wrapS = THREE.RepeatWrapping;
                        tex.repeat.x = -1;
                    }
                    tex.center.set(0.5, 0.5);

                    var index = (side === 'front') ? 4 : 5;
                    mesh.material[index].map = tex;
                    mesh.material[index].needsUpdate = true;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function flip() {
            if(mesh) mesh.rotation.y += Math.PI;
        }

        function animate() {
            requestAnimationFrame(animate);
            renderer.render(scene, camera);
        }

        // START
        window.onload = start;
    </script>
</body>
</html>