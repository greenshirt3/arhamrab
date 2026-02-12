<?php
session_start();
$ADMIN_PASS = "2733"; // CHANGE THIS PASSWORD
$DB_FILE = "cards_db.json";

// --- 1. HANDLE LOGIN ---
if (isset($_POST['login'])) {
    if ($_POST['password'] === $ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Incorrect Password";
    }
}

// --- 2. HANDLE CARD SAVING (Admin Only) ---
$new_link = "";
if (isset($_POST['save_card']) && isset($_SESSION['admin_logged_in'])) {
    // Ensure uploads dir exists
    if (!is_dir('uploads')) mkdir('uploads', 0777, true);

    $id = uniqid();
    $front_path = "uploads/" . $id . "_front_" . basename($_FILES['front']['name']);
    $back_path = "uploads/" . $id . "_back_" . basename($_FILES['back']['name']);

    if (move_uploaded_file($_FILES['front']['tmp_name'], $front_path) && 
        move_uploaded_file($_FILES['back']['tmp_name'], $back_path)) {
        
        // Save to JSON Database
        $db = file_exists($DB_FILE) ? json_decode(file_get_contents($DB_FILE), true) : [];
        $db[$id] = [
            'front' => $front_path,
            'back' => $back_path,
            'created' => date('Y-m-d H:i:s')
        ];
        file_put_contents($DB_FILE, json_encode($db));
        
        $new_link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?id=" . $id;
    } else {
        $error = "File upload failed. Check folder permissions.";
    }
}

// --- 3. DETERMINE VIEW MODE ---
$mode = "login";
$card_data = null;

// Case A: Client View (Public Link)
if (isset($_GET['id'])) {
    $mode = "client";
    $db = file_exists($DB_FILE) ? json_decode(file_get_contents($DB_FILE), true) : [];
    if (isset($db[$_GET['id']])) {
        $card_data = $db[$_GET['id']];
    } else {
        die("<h2 style='color:white; text-align:center; font-family:sans-serif; margin-top:50px;'>Card Not Found or Expired.</h2><style>body{background:#111;}</style>");
    }
}
// Case B: Admin Editor
elseif (isset($_SESSION['admin_logged_in'])) {
    $mode = "admin";
}
// Case C: Login Screen
else {
    $mode = "login";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Card Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root { --primary: #F37021; --dark: #0f172a; --panel: rgba(15, 23, 42, 0.9); }
        body { margin: 0; overflow: hidden; background: #050505; font-family: 'Inter', sans-serif; color: white; }

        /* --- LOGIN SCREEN --- */
        .login-wrapper {
            position: fixed; inset: 0; display: flex; align-items: center; justify-content: center;
            background: radial-gradient(circle, #1a1a1a 0%, #000 100%); z-index: 1000;
        }
        .login-box {
            background: var(--dark); padding: 40px; border-radius: 12px; border: 1px solid #333;
            width: 100%; max-width: 350px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .form-input {
            width: 100%; padding: 12px; margin: 15px 0; background: #222; border: 1px solid #444;
            color: white; border-radius: 6px; box-sizing: border-box;
        }
        .btn-primary {
            width: 100%; padding: 12px; background: var(--primary); color: white; border: none;
            border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s;
        }
        .btn-primary:hover { filter: brightness(1.1); }

        /* --- EDITOR UI (ADMIN) --- */
        .ui-panel {
            position: absolute; top: 20px; left: 20px; width: 340px;
            background: var(--panel); backdrop-filter: blur(10px);
            padding: 25px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);
            z-index: 10; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s;
        }
        .ui-minimized { transform: translateX(-120%); }
        
        .toggle-ui {
            position: absolute; top: 20px; left: 20px; z-index: 9;
            background: var(--dark); color: white; border: 1px solid #333;
            padding: 10px; border-radius: 8px; cursor: pointer;
        }

        .upload-row { margin-bottom: 15px; }
        .upload-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            padding: 10px; border: 1px dashed #555; border-radius: 8px;
            cursor: pointer; color: #aaa; font-size: 0.9rem; transition: 0.2s;
        }
        .upload-btn:hover { border-color: var(--primary); color: white; background: rgba(255,255,255,0.05); }

        /* --- SUCCESS MODAL --- */
        .modal {
            position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: white; color: black; padding: 30px; border-radius: 12px;
            text-align: center; z-index: 200; box-shadow: 0 0 100px rgba(0,0,0,0.8);
            display: <?php echo $new_link ? 'block' : 'none'; ?>;
        }
        .link-box {
            background: #eee; padding: 10px; border-radius: 4px; font-family: monospace;
            margin: 15px 0; word-break: break-all; border: 1px solid #ccc;
        }

        /* --- CLIENT VIEW UI --- */
        .client-controls {
            position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);
            display: flex; gap: 15px; z-index: 10;
        }
        .pill-btn {
            background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2);
            color: white; padding: 10px 25px; border-radius: 50px; cursor: pointer; transition: 0.2s;
        }
        .pill-btn:hover { background: white; color: black; }

        /* --- 3D CANVAS --- */
        #canvas-container { position: fixed; inset: 0; outline: none; }
    </style>
</head>
<body>

    <?php if ($mode === 'login'): ?>
    <div class="login-wrapper">
        <form method="POST" class="login-box">
            <h2 style="margin-top:0"><i class="fas fa-cube" style="color:var(--primary)"></i> Card Studio</h2>
            <p style="color:#888;">Admin Access Required</p>
            <?php if(isset($error)) echo "<p style='color:#ef4444; font-size:0.9rem;'>$error</p>"; ?>
            <input type="password" name="password" class="form-input" placeholder="Enter Password" required>
            <button type="submit" name="login" class="btn-primary">ENTER STUDIO</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($mode === 'admin'): ?>
    <div class="ui-panel">
        <h2 style="margin:0 0 15px 0; font-size:1.2rem;">Create Mockup</h2>
        <form method="POST" enctype="multipart/form-data">
            
            <div class="upload-row">
                <label style="font-size:0.8rem; font-weight:bold; color:#888;">FRONT IMAGE</label>
                <label class="upload-btn">
                    <input type="file" name="front" id="frontInput" accept="image/*" style="display:none;" required>
                    <i class="fas fa-image"></i> <span>Select Front...</span>
                </label>
            </div>

            <div class="upload-row">
                <label style="font-size:0.8rem; font-weight:bold; color:#888;">BACK IMAGE</label>
                <label class="upload-btn">
                    <input type="file" name="back" id="backInput" accept="image/*" style="display:none;" required>
                    <i class="fas fa-image"></i> <span>Select Back...</span>
                </label>
            </div>

            <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 20px 0;">

            <button type="submit" name="save_card" class="btn-primary">
                <i class="fas fa-save"></i> SAVE & GET LINK
            </button>
        </form>
        <p style="font-size:0.8rem; color:#666; margin-top:15px; text-align:center;">
            Preview updates instantly upon file selection.
        </p>
    </div>

    <?php if ($new_link): ?>
    <div class="modal">
        <i class="fas fa-check-circle" style="font-size:3rem; color:#10B981;"></i>
        <h3>Mockup Created!</h3>
        <p>Send this link to your client:</p>
        <div class="link-box"><?php echo $new_link; ?></div>
        <button onclick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>'" class="btn-primary">Create Another</button>
        <button onclick="window.open('<?php echo $new_link; ?>', '_blank')" class="btn-primary" style="background:#333; margin-top:10px;">View Now</button>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($mode === 'client'): ?>
    <div class="client-controls">
        <button class="pill-btn" onclick="toggleSpin()">
            <i class="fas fa-sync"></i> Auto Spin
        </button>
        <button class="pill-btn" onclick="resetCam()">
            <i class="fas fa-compress"></i> Reset View
        </button>
    </div>
    <div style="position:absolute; top:20px; left:20px; color:rgba(255,255,255,0.5); font-size:0.9rem;">
        <i class="fas fa-mouse"></i> Drag to Rotate • Scroll to Zoom
    </div>
    <?php endif; ?>

    <?php if ($mode !== 'login'): ?>
    <div id="canvas-container"></div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    
    <script>
        // --- CONFIGURATION ---
        const DEFAULT_TEX = "https://via.placeholder.com/512x300/333/888?text=Upload+Image"; // Placeholder
        
        // PHP DATA INJECTION
        const VIEW_MODE = "<?php echo $mode; ?>";
        let frontUrl = "<?php echo ($card_data) ? $card_data['front'] : ''; ?>";
        let backUrl = "<?php echo ($card_data) ? $card_data['back'] : ''; ?>";

        // --- 1. SCENE SETUP ---
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x0a0a0a); // Dark Studio Background
        
        // Add subtle environmental fog
        scene.fog = new THREE.Fog(0x0a0a0a, 5, 20);

        const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 100);
        camera.position.set(0, 0, 6);

        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.0;
        document.getElementById('canvas-container').appendChild(renderer.domElement);

        // --- 2. LIGHTING (Cinematic) ---
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
        scene.add(ambientLight);

        // Key Light
        const spotLight = new THREE.SpotLight(0xffffff, 1.5);
        spotLight.position.set(5, 5, 5);
        spotLight.castShadow = true;
        spotLight.shadow.bias = -0.0001;
        scene.add(spotLight);

        // Fill Light (Blueish cool fill)
        const fillLight = new THREE.PointLight(0x4c5c75, 0.8);
        fillLight.position.set(-5, 0, 5);
        scene.add(fillLight);

        // Rim Light (Orange Brand Color)
        const rimLight = new THREE.PointLight(0xF37021, 1.2);
        rimLight.position.set(0, 5, -5);
        scene.add(rimLight);

        // --- 3. MESH (The Card) ---
        const geometry = new THREE.BoxGeometry(4.0, 3.5, 0.02); // Standard Card Size with Thickness
        const loader = new THREE.TextureLoader();
        
        // Load Textures
        function loadTex(url) {
            return url ? loader.load(url) : loader.load(DEFAULT_TEX);
        }

        const frontTex = loadTex(frontUrl);
        const backTex = loadTex(backUrl);

        // Materials (6 sides: Right, Left, Top, Bottom, Front, Back)
        const paperMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.8 }); // Edges
        const faceMatParams = { roughness: 0.4, metalness: 0.1 };
        
        const materials = [
            paperMat, paperMat, paperMat, paperMat,
            new THREE.MeshStandardMaterial({ map: frontTex, ...faceMatParams }), // Front
            new THREE.MeshStandardMaterial({ map: backTex, ...faceMatParams })   // Back
        ];

        const card = new THREE.Mesh(geometry, materials);
        card.castShadow = true;
        card.receiveShadow = true;
        scene.add(card);

        // --- 4. CONTROLS ---
        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.minDistance = 3;
        controls.maxDistance = 10;

        // --- 5. ANIMATION LOOP ---
        let autoSpin = (VIEW_MODE === 'client'); // Default spin for clients
        
        function animate() {
            requestAnimationFrame(animate);
            if (autoSpin) { card.rotation.y += 0.005; }
            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        // --- 6. JS FUNCTIONS ---
        
        // Admin: Live Preview Handler
        if (VIEW_MODE === 'admin') {
            function handleUpload(inputId, matIndex) {
                document.getElementById(inputId).addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const newTex = loader.load(ev.target.result);
                            card.material[matIndex].map = newTex;
                            card.material[matIndex].needsUpdate = true;
                            // Update UI Text
                            this.parentNode.querySelector('span').innerText = "Image Loaded!";
                            this.parentNode.style.borderColor = "#F37021";
                            this.parentNode.style.color = "white";
                        }.bind(this);
                        reader.readAsDataURL(file);
                    }
                });
            }
            handleUpload('frontInput', 4); // Index 4 = Front
            handleUpload('backInput', 5);  // Index 5 = Back
        }

        // Client: Controls
        function toggleSpin() { autoSpin = !autoSpin; }
        function resetCam() { 
            autoSpin = false;
            camera.position.set(0,0,6);
            card.rotation.set(0,0,0);
            controls.reset();
        }

        // Window Resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
    <?php endif; ?>
</body>
</html>