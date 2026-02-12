<?php
$data = json_decode(file_get_contents('data.json'), true);
$shop = $data['shop_info'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $shop['name']; ?> | Hyper Garage</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;700;900&family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #00f2ea; /* Cyber Blue */
            --secondary: #ff0050; /* Cyber Pink */
            --glass: rgba(255, 255, 255, 0.05);
            --border: 1px solid rgba(255, 255, 255, 0.1);
            --glow: 0 0 20px rgba(0, 242, 234, 0.3);
        }

        body { 
            margin: 0; background: #050505; color: #fff; font-family: 'Exo 2', sans-serif; 
            overflow-x: hidden; padding-bottom: 120px;
        }

        /* 3D BACKGROUND CANVAS */
        #canvas-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
        }

        /* LOADER */
        #loader {
            position: fixed; inset: 0; background: #000; z-index: 9999;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity 0.8s ease;
        }
        .loader-bar {
            width: 200px; height: 4px; background: #333; border-radius: 2px; overflow: hidden; margin-top: 20px;
        }
        .loader-progress {
            width: 100%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary));
            animation: loadAnim 1.5s infinite ease-in-out;
        }

        /* HEADER CARD */
        .hero-section {
            padding: 40px 20px; text-align: center; perspective: 1000px;
            margin-bottom: 20px;
        }
        
        .hero-card {
            background: rgba(10, 10, 10, 0.6);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px; padding: 40px 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.9);
            transform-style: preserve-3d;
            position: relative; overflow: hidden;
            animation: float 6s ease-in-out infinite;
        }

        /* Animated Gradient Border */
        .hero-card::after {
            content: ''; position: absolute; inset: 0; border-radius: 24px; padding: 2px;
            background: linear-gradient(45deg, var(--primary), transparent, var(--secondary));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            pointer-events: none;
        }

        .shop-title {
            font-size: 2.8rem; font-weight: 900; line-height: 1; margin: 0;
            background: linear-gradient(to right, #fff, #bbb);
            -webkit-background-clip: text; color: transparent;
            text-transform: uppercase; letter-spacing: -1px;
            transform: translateZ(40px);
        }

        .tagline {
            color: var(--primary); letter-spacing: 2px; font-size: 0.9rem;
            margin-top: 10px; font-weight: 700; text-transform: uppercase;
            transform: translateZ(30px); text-shadow: var(--glow);
        }

        .status-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px; background: rgba(0, 255, 100, 0.1);
            border: 1px solid #00ff66; color: #00ff66; border-radius: 50px;
            font-weight: bold; font-size: 0.8rem; margin-top: 20px;
            transform: translateZ(50px); box-shadow: 0 0 15px rgba(0, 255, 100, 0.2);
        }

        /* GRID SYSTEM */
        .section-header {
            text-align: center; margin: 40px 0 20px; position: relative; z-index: 2;
        }
        .section-header h2 {
            font-size: 1.5rem; text-transform: uppercase; letter-spacing: 3px; margin: 0;
            color: #fff; text-shadow: 0 0 10px var(--secondary);
        }

        .grid-container {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px; padding: 0 20px; max-width: 1000px; margin: 0 auto;
        }

        .service-card {
            background: var(--glass); border: var(--border);
            border-radius: 20px; padding: 25px 15px; text-align: center;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            opacity: 0; animation: fadeUp 0.6s forwards;
        }
        
        .service-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-10px) scale(1.02);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(0, 242, 234, 0.2);
        }

        .icon-circle {
            width: 60px; height: 60px; margin: 0 auto 15px;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: var(--primary); border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .svc-name { font-weight: 700; font-size: 1rem; margin-bottom: 5px; }
        .svc-price { 
            font-family: 'Rajdhani'; font-weight: 700; font-size: 1.4rem; color: var(--secondary);
            text-shadow: 0 0 10px rgba(255, 0, 80, 0.4);
        }

        /* BOTTOM DOCK */
        .dock-nav {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: rgba(20, 20, 20, 0.8); backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 10px; border-radius: 25px;
            display: flex; gap: 10px; z-index: 100;
            box-shadow: 0 10px 40px rgba(0,0,0,0.8);
        }

        .dock-btn {
            width: 50px; height: 50px; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none; font-size: 1.2rem;
            transition: 0.3s; background: rgba(255,255,255,0.05);
        }
        .dock-btn:hover { background: #fff; color: #000; transform: translateY(-5px); }

        .book-now {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            padding: 0 30px; border-radius: 20px; display: flex; align-items: center;
            font-weight: 800; text-transform: uppercase; color: #fff; text-decoration: none;
            letter-spacing: 1px; transition: 0.3s;
        }
        .book-now:hover { filter: brightness(1.2); transform: scale(1.05); }

        /* ANIMATIONS */
        @keyframes loadAnim { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    </style>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
</head>
<body>

    <div id="loader">
        <h2 style="font-family: 'Rajdhani'; letter-spacing: 5px; color: #fff;">START ENGINE</h2>
        <div class="loader-bar"><div class="loader-progress"></div></div>
    </div>

    <div id="canvas-bg"></div>

    <div class="hero-section">
        <div class="hero-card" data-tilt data-tilt-max="10" data-tilt-glare data-tilt-max-glare="0.4">
            <h1 class="shop-title"><?php echo $shop['name']; ?></h1>
            <div class="tagline"><?php echo $shop['tagline']; ?></div>
            
            <div class="status-badge">
                <i class="fas fa-circle" style="font-size: 0.5rem; color: #00ff66; box-shadow: 0 0 5px #00ff66;"></i>
                <?php echo $shop['is_open'] ? 'SYSTEM ONLINE' : 'OFFLINE'; ?>
            </div>
        </div>
    </div>

    <div class="section-header"><h2>Service Menu</h2></div>
    <div class="grid-container">
        <?php foreach($data['services'] as $i => $svc): ?>
        <div class="service-card" style="animation-delay: <?php echo $i * 0.1; ?>s;">
            <div class="icon-circle"><i class="fas <?php echo $svc['icon']; ?>"></i></div>
            <div class="svc-name"><?php echo $svc['name']; ?></div>
            <div class="svc-price"><?php echo $svc['price']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="section-header"><h2>Inventory</h2></div>
    <div class="grid-container" style="padding-bottom: 50px;">
        <?php foreach($data['products'] as $i => $prod): ?>
        <div class="service-card" style="animation-delay: <?php echo ($i * 0.1) + 0.5; ?>s;">
            <div class="icon-circle" style="border-color: var(--secondary); color: var(--secondary);">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="svc-name"><?php echo $prod['name']; ?></div>
            <div class="svc-price" style="color: #fff;"><?php echo $prod['price']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 40px; color: #555; font-size: 0.8rem;">
        DESIGNED BY <a href="#" style="color: var(--primary); text-decoration: none;">ARHAM PRINTERS</a>
    </div>

    <div class="dock-nav">
        <a href="tel:<?php echo $shop['phone']; ?>" class="dock-btn"><i class="fas fa-phone-alt"></i></a>
        <a href="<?php echo $shop['map_link']; ?>" class="dock-btn"><i class="fas fa-map-marked-alt"></i></a>
        <a href="https://wa.me/<?php echo $shop['phone']; ?>" class="book-now">
            BOOK NOW <i class="fas fa-bolt" style="margin-left: 8px;"></i>
        </a>
    </div>

    <script>
        // 1. Loader Logic
        window.addEventListener('load', () => {
            const loader = document.getElementById('loader');
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 800);
        });

        // 2. Initialize Tilt
        VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
            speed: 1000,
            scale: 1.05,
            perspective: 1000
        });

        // 3. THREE.JS WARP SPEED ENGINE
        const scene = new THREE.Scene();
        // Add subtle fog for depth
        scene.fog = new THREE.FogExp2(0x000000, 0.002);

        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.z = 1;
        camera.rotation.x = Math.PI / 2;

        const renderer = new THREE.WebGLRenderer({ alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('canvas-bg').appendChild(renderer.domElement);

        // CREATE STARS
        const points = [];
        const velocities = [];
        for (let i = 0; i < 4000; i++) {
            const star = new THREE.Vector3(
                Math.random() * 600 - 300,
                Math.random() * 600 - 300,
                Math.random() * 600 - 300
            );
            points.push(star);
            velocities.push(0); // Placeholder
        }

        const starGeo = new THREE.BufferGeometry().setFromPoints(points);
        
        // Custom texture for glow
        const canvas = document.createElement('canvas');
        canvas.width = 32; canvas.height = 32;
        const ctx = canvas.getContext('2d');
        const grad = ctx.createRadialGradient(16,16,0,16,16,16);
        grad.addColorStop(0, 'white');
        grad.addColorStop(1, 'transparent');
        ctx.fillStyle = grad;
        ctx.fillRect(0,0,32,32);
        const sprite = new THREE.CanvasTexture(canvas);

        const starMat = new THREE.PointsMaterial({
            color: 0x00f2ea, // Cyan stars
            size: 0.8,
            map: sprite,
            transparent: true
        });

        const stars = new THREE.Points(starGeo, starMat);
        scene.add(stars);

        // ANIMATION LOOP
        function animate() {
            const positions = stars.geometry.attributes.position.array;
            
            for(let i = 1; i < positions.length; i += 3) {
                // Move stars towards camera (Y axis in this rotation)
                positions[i] -= 2; // Speed
                
                // Reset if behind camera
                if (positions[i] < -200) {
                    positions[i] = 200;
                }
            }
            
            stars.geometry.attributes.position.needsUpdate = true;
            
            // Subtle rotation of the tunnel
            stars.rotation.y += 0.0005;
            
            renderer.render(scene, camera);
            requestAnimationFrame(animate);
        }
        animate();

        // RESIZE HANDLER
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
