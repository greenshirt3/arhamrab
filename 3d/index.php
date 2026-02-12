<?php
// SERVER SIDE: IMAGE UPLOADER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['design'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $filename = time() . "_" . basename($_FILES["design"]["name"]);
    if (move_uploaded_file($_FILES["design"]["tmp_name"], $target_dir . $filename)) {
        echo json_encode(["status" => "success", "file" => $filename]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    exit;
}

// CLIENT SIDE: URL PARAMETERS
$img = isset($_GET['img']) ? 'uploads/' . $_GET['img'] : null;
$inner = isset($_GET['inner']) ? '#' . $_GET['inner'] : '#ffffff';
$handle = isset($_GET['handle']) ? '#' . $_GET['handle'] : '#ffffff';
$outer = isset($_GET['outer']) ? '#' . $_GET['outer'] : '#ffffff';
$bg = isset($_GET['bg']) ? '#' . $_GET['bg'] : '#0a0a0a'; // New Background Param
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360° Mug Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
        }
    }
    </script>

    <style>
        body { margin: 0; overflow: hidden; background: #000; font-family: 'Outfit', sans-serif; }
        #canvas-container { width: 100%; height: 100vh; cursor: grab; }
        
        /* BRANDING */
        .brand-header { position: absolute; top: 0; left: 0; width: 100%; text-align: center; padding-top: 25px; pointer-events: none; z-index: 10; }
        .neon-text { font-size: clamp(1.8rem, 6vw, 3rem); font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 3px; text-shadow: 0 0 10px #fff, 0 0 20px #00d2ff; }

        /* ADMIN UI */
        .admin-lock { position: fixed; bottom: 20px; right: 20px; width: 45px; height: 45px; background: rgba(255,255,255,0.1); color: #888; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 50; }
        .controls { display: none; position: absolute; top: 100px; left: 20px; width: 320px; background: rgba(15, 15, 15, 0.95); padding: 25px; border-radius: 12px; border: 1px solid #333; max-height: 80vh; overflow-y: auto; z-index: 20; color: white; }
        .controls.active { display: block; }
        
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 0.75rem; color: #aaa; margin-bottom: 6px; }
        .file-btn { background: #222; border: 1px solid #444; color: white; padding: 12px; border-radius: 6px; cursor: pointer; text-align: center; font-size: 0.85rem; }
        input[type="file"] { display: none; }
        input[type="color"] { -webkit-appearance: none; border: none; width: 100%; height: 40px; cursor: pointer; padding: 0; background: none; }
        input[type="color"]::-webkit-color-swatch { border: 1px solid #444; border-radius: 6px; }
        
        .btn-action { width: 100%; padding: 14px; background: #00d2ff; color: #000; border: none; border-radius: 6px; font-weight: 800; margin-top: 15px; cursor: pointer; }
        #loading { position: fixed; inset: 0; background: #000; z-index: 99; display: flex; align-items: center; justify-content: center; color: #00d2ff; font-weight: 700; letter-spacing: 3px; transition: 0.5s; }
    </style>
</head>
<body>

    <div id="loading">LOADING...</div>
    <div class="brand-header"><div class="neon-text">ARHAM PRINTERS</div></div>
    <div id="canvas-container"></div>
    <div class="admin-lock" onclick="unlockAdmin()"><i class="fas fa-lock"></i></div>

    <div class="controls" id="adminPanel">
        <div class="input-group">
            <label>1. Upload Design</label>
            <div class="file-btn" onclick="document.getElementById('imgInp').click()"><i class="fas fa-upload"></i> Upload Image</div>
            <input type="file" id="imgInp" accept="image/*">
            <div id="uploadStatus" style="font-size:0.7rem; color:#00d2ff; margin-top:5px; text-align:center;"></div>
        </div>

        <div class="input-group"><label>Screen Background</label><input type="color" id="colBg" value="<?php echo $bg; ?>"></div>
        <div class="input-group"><label>Mug Background (Behind Image)</label><input type="color" id="colOuter" value="<?php echo $outer; ?>"></div>
        <div class="input-group"><label>Inner Color</label><input type="color" id="colInner" value="<?php echo $inner; ?>"></div>
        <div class="input-group"><label>Handle Color</label><input type="color" id="colHandle" value="<?php echo $handle; ?>"></div>

        <button class="btn-action" onclick="generateLink()">Copy Shared Link</button>
    </div>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

        const CIRCUMFERENCE = 11.0; 
        const HEIGHT = 3.8;
        const R_MUG = CIRCUMFERENCE / (2 * Math.PI); 

        let scene, camera, renderer, controls;
        let mug, body, inner, handle;
        
        let currentImgUrl = "<?php echo $img; ?>";
        window.uploadedFilename = "<?php echo isset($_GET['img']) ? $_GET['img'] : ''; ?>"; 

        init();

        function init() {
            const container = document.getElementById('canvas-container');

            scene = new THREE.Scene();
            // --- UPDATED: USE PHP VARIABLE FOR BACKGROUND ---
            scene.background = new THREE.Color("<?php echo $bg; ?>");

            camera = new THREE.PerspectiveCamera(35, window.innerWidth/window.innerHeight, 0.1, 100);
            camera.position.set(12, 5, 12); 

            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            container.appendChild(renderer.domElement);

            const amb = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(amb);
            const spot = new THREE.SpotLight(0xffffff, 10);
            spot.position.set(10, 10, 10);
            scene.add(spot);
            const rimLight = new THREE.SpotLight(0x00d2ff, 5);
            rimLight.position.set(-10, 5, -10);
            scene.add(rimLight);

            createMug();

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.autoRotate = true;
            controls.autoRotateSpeed = 2.0;

            if(currentImgUrl) {
                new THREE.TextureLoader().load(currentImgUrl, (tex) => updateTexture(tex.image, document.getElementById('colOuter').value));
            } else {
                updateTexture(null, document.getElementById('colOuter').value);
            }

            setTimeout(() => {
                document.getElementById('loading').style.opacity = 0;
                setTimeout(() => document.getElementById('loading').remove(), 500);
            }, 800);

            bindAdminEvents();
            animate();
        }

        function createMug() {
            mug = new THREE.Group();
            const segs = 128; const thick = 0.15;
            const matBase = new THREE.MeshStandardMaterial({ roughness: 0.5, metalness: 0.1 });

            // OUTER (Print)
            const geoBody = new THREE.CylinderGeometry(R_MUG, R_MUG, HEIGHT, segs, 1, true);
            geoBody.rotateY(-Math.PI / 2); 
            const matPrint = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.5, side: THREE.FrontSide });
            body = new THREE.Mesh(geoBody, matPrint);
            mug.add(body);

            // INNER
            const geoInner = new THREE.CylinderGeometry(R_MUG-thick, R_MUG-thick, HEIGHT-thick, segs, 1, false);
            geoInner.translate(0, thick/2, 0);
            inner = new THREE.Mesh(geoInner, matBase.clone());
            inner.material.color.set(document.getElementById('colInner').value);
            mug.add(inner);

            // HANDLE
            const geoHandle = new THREE.TorusGeometry(HEIGHT*0.35, thick, 24, 48, Math.PI*1.3);
            geoHandle.rotateZ(Math.PI/1.3);
            geoHandle.translate(R_MUG + (HEIGHT*0.1), 0, 0);
            handle = new THREE.Mesh(geoHandle, matBase.clone());
            handle.material.color.set(document.getElementById('colHandle').value);
            mug.add(handle);

            // RIM & BOTTOM
            const geoRim = new THREE.RingGeometry(R_MUG-thick, R_MUG, segs);
            geoRim.rotateX(-Math.PI/2);
            geoRim.translate(0, HEIGHT/2, 0);
            const rim = new THREE.Mesh(geoRim, matBase.clone());
            rim.material.color.set(document.getElementById('colInner').value);
            mug.add(rim);

            const geoBot = new THREE.CircleGeometry(R_MUG, segs);
            geoBot.rotateX(Math.PI/2);
            geoBot.translate(0, -HEIGHT/2, 0);
            const bot = new THREE.Mesh(geoBot, matBase.clone());
            bot.material.color.set(document.getElementById('colOuter').value);
            mug.add(bot);

            scene.add(mug);
        }

        function updateTexture(imgObj, outerHex) {
            const PPI = 100;
            const wCanvas = Math.ceil(CIRCUMFERENCE * PPI);
            const hCanvas = Math.ceil(HEIGHT * PPI);
            
            const cvs = document.createElement('canvas');
            cvs.width = wCanvas;
            cvs.height = hCanvas;
            const ctx = cvs.getContext('2d');

            ctx.fillStyle = outerHex;
            ctx.fillRect(0, 0, wCanvas, hCanvas);

            if (imgObj) ctx.drawImage(imgObj, 0, 0, wCanvas, hCanvas);

            const tex = new THREE.CanvasTexture(cvs);
            tex.colorSpace = THREE.SRGBColorSpace;
            body.material.map = tex;
            body.material.needsUpdate = true;
        }

        function bindAdminEvents() {
            document.getElementById('imgInp').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                const formData = new FormData();
                formData.append("design", file);
                document.getElementById('uploadStatus').innerText = "Uploading...";
                fetch('', { method: "POST", body: formData }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.uploadedFilename = data.file;
                        document.getElementById('uploadStatus').innerText = "Success!";
                        const r = new FileReader();
                        r.onload = (ev) => {
                            const img = new Image();
                            img.src = ev.target.result;
                            img.onload = () => updateTexture(img, document.getElementById('colOuter').value);
                        };
                        r.readAsDataURL(file);
                    }
                });
            });

            document.getElementById('colInner').addEventListener('input', (e) => inner.material.color.set(e.target.value));
            document.getElementById('colHandle').addEventListener('input', (e) => handle.material.color.set(e.target.value));
            
            // --- NEW: Update Screen Background Live ---
            document.getElementById('colBg').addEventListener('input', (e) => {
                scene.background = new THREE.Color(e.target.value);
            });

            document.getElementById('colOuter').addEventListener('input', (e) => {
                const img = new Image();
                if(body.material.map) img.src = body.material.map.image.toDataURL(); 
                img.onload = () => updateTexture(img, e.target.value);
            });
        }

        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }
        
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>

    <script>
        function unlockAdmin() {
            if(prompt("Password:") === "admin123") {
                document.getElementById('adminPanel').classList.add('active');
                document.querySelector('.admin-lock').style.display = 'none';
            }
        }

        function generateLink() {
            if(!window.uploadedFilename) { alert("Upload image first."); return; }
            const baseUrl = window.location.href.split('?')[0];
            const inner = document.getElementById('colInner').value.substring(1);
            const handle = document.getElementById('colHandle').value.substring(1);
            const outer = document.getElementById('colOuter').value.substring(1);
            const bg = document.getElementById('colBg').value.substring(1); // Get BG Color
            
            const fullLink = `${baseUrl}?img=${window.uploadedFilename}&inner=${inner}&handle=${handle}&outer=${outer}&bg=${bg}`;
            
            navigator.clipboard.writeText(fullLink).then(() => {
                alert("Link Copied!\n\n" + fullLink);
            }).catch(() => prompt("Copy this:", fullLink));
        }
    </script>
</body>
</html>