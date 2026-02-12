<?php
$data = json_decode(file_get_contents('data.json'), true);
$shop = $data['shop_info'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $shop['name']; ?> | 3D Experience</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --neon-red: #ff3333;
            --neon-orange: #ff8c00;
            --carbon: #111;
            --glass: rgba(20, 20, 20, 0.6);
            --border: 1px solid rgba(255, 51, 51, 0.3);
        }

        body { 
            margin: 0; background: #000; color: #fff; font-family: 'Rajdhani', sans-serif; 
            overflow-x: hidden; padding-bottom: 100px;
        }

        /* 3D BACKGROUND CANVAS */
        #canvas-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1; opacity: 0.6; pointer-events: none;
        }

        /* LOADER */
        #loader {
            position: fixed; inset: 0; background: #000; z-index: 999;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity 1s;
        }
        .gear-spinner {
            font-size: 3rem; color: var(--neon-red);
            animation: spin 2s infinite linear;
            filter: drop-shadow(0 0 10px var(--neon-red));
        }

        /* HEADER 3D CARD */
        .hero-container {
            perspective: 1000px; padding: 20px; text-align: center; margin-top: 20px;
        }
        .hero-card {
            background: linear-gradient(135deg, rgba(30,30,30,0.9), rgba(10,10,10,0.95));
            border: var(--border); border-radius: 20px; padding: 40px 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
            transform-style: preserve-3d;
            backdrop-filter: blur(10px);
            position: relative; overflow: hidden;
        }
        
        /* Neon Glow Animation */
        .hero-card::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 51, 51, 0.1), transparent);
            transform: rotate(45deg); animation: shine 6s infinite;
        }

        .shop-title {
            font-family: 'Orbitron', sans-serif; font-size: 2.5rem; 
            text-transform: uppercase; margin: 0;
            background: linear-gradient(to bottom, #fff, #aaa);
            -webkit-background-clip: text; color: transparent;
            text-shadow: 0 0 20px rgba(255,255,255,0.2);
            transform: translateZ(50px); /* 3D Pop */
        }
        
        .status-pill {
            display: inline-block; padding: 5px 20px; border-radius: 50px;
            background: rgba(0, 255, 0, 0.1); border: 1px solid #0f0; color: #0f0;
            font-weight: bold; letter-spacing: 2px; margin-top: 15px;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.2);
            transform: translateZ(30px);
        }

        /* SERVICE GRID */
        .grid-container {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px; padding: 20px; max-width: 800px; margin: 0 auto;
        }

        .service-card {
            background: var(--glass); border: var(--border); border-radius: 15px;
            padding: 20px; text-align: center; position: relative;
            transition: 0.3s; transform-style: preserve-3d;
        }
        
        .service-card:hover {
            background: rgba(255, 51, 51, 0.1);
            transform: translateY(-10px) rotateX(10deg);
            border-color: var(--neon-red);
            box-shadow: 0 0 20px rgba(255, 51, 51, 0.4);
        }

        .icon-box {
            font-size: 2rem; color: var(--neon-orange); margin-bottom: 10px;
            filter: drop-shadow(0 0 5px var(--neon-orange));
            transform: translateZ(40px);
        }
        
        .service-name { font-size: 1.1rem; font-weight: 700; transform: translateZ(20px); }
        .service-price { 
            font-family: 'Orbitron'; color: var(--neon-red); font-size: 1.4rem; 
            margin-top: 5px; display: block; transform: translateZ(30px);
            text-shadow: 0 0 10px rgba(255, 51, 51, 0.5);
        }

        /* SECTION HEADERS */
        .h-title {
            text-align: center; font-family: 'Orbitron'; color: #fff;
            margin: 40px 0 20px; font-size: 1.8rem; letter-spacing: 4px;
            text-shadow: 0 0 10px var(--neon-orange);
            position: relative; display: inline-block; left: 50%; transform: translateX(-50%);
        }
        .h-title::after {
            content: ''; display: block; width: 100%; height: 2px;
            background: var(--neon-orange); box-shadow: 0 0 10px var(--neon-orange);
        }

        /* BOTTOM NAV (Cyberpunk Style) */
        .cyber-nav {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            width: 90%; max-width: 400px; height: 60px;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(10px);
            border: 1px solid var(--neon-red); border-radius: 50px;
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 10px; z-index: 100;
            box-shadow: 0 0 20px rgba(255, 51, 51, 0.3);
        }
        
        .nav-btn {
            width: 45px; height: 45px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none; font-size: 1.2rem;
            transition: 0.3s;
        }
        .nav-btn:hover { background: var(--neon-red); box-shadow: 0 0 15px var(--neon-red); }
        
        .book-btn {
            background: var(--neon-red); color: #000; padding: 0 25px;
            height: 40px; border-radius: 30px; display: flex; align-items: center;
            font-weight: 900; font-family: 'Orbitron'; text-decoration: none;
            box-shadow: 0 0 15px var(--neon-red); transition: 0.3s;
            animation: pulse 2s infinite;
        }
        .book-btn:hover { background: #fff; letter-spacing: 1px; }

        .arham-credit {
            text-align: center; margin-top: 50px; font-size: 0.8rem; color: #555;
        }
        .arham-credit a { color: var(--neon-orange); text-decoration: none; }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes shine { 0% { left: -50%; } 100% { left: 150%; } }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(255, 51, 51, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 51, 51, 0); } }

    </style>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
</head>
<body>

    <div id="loader"><i class="fas fa-cog gear-spinner"></i></div>

    <div id="canvas-bg"></div>

    <div class="hero-container">
        <div class="hero-card" data-tilt data-tilt-glare data-tilt-max-glare="0.5">
            <h1 class="shop-title"><?php echo $shop['name']; ?></h1>
            <p style="color: #aaa; letter-spacing: 2px; margin-top: 10px; transform: translateZ(20px);">
                <?php echo $shop['tagline']; ?>
            </p>
            <div class="status-pill">
                <i class="fas fa-bolt"></i> <?php echo $shop['is_open'] ? 'WORKSHOP OPEN' : 'CLOSED'; ?>
            </div>
        </div>
    </div>

    <h2 class="h-title">CORE SERVICES</h2>
    <div class="grid-container">
        <?php foreach($data['services'] as $svc): ?>
        <div class="service-card" data-tilt>
            <div class="icon-box"><i class="fas <?php echo $svc['icon']; ?>"></i></div>
            <div class="service-name"><?php echo $svc['name']; ?></div>
            <span class="service-price"><?php echo $svc['price']; ?></span>
            <?php if(isset($svc['note'])): ?>
                <div style="font-size: 0.7rem; color: #777; margin-top: 5px;"><?php echo $svc['note']; ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 class="h-title">PARTS & LUBES</h2>
    <div class="grid-container">
        <?php foreach($data['products'] as $prod): ?>
        <div class="service-card" data-tilt style="border-color: rgba(255, 140, 0, 0.3);">
            <div class="icon-box" style="color: #fff;"><i class="fas fa-oil-can"></i></div>
            <div class="service-name"><?php echo $prod['name']; ?></div>
            <span class="service-price" style="color: var(--neon-orange);"><?php echo $prod['price']; ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="arham-credit">
        DIGITAL EXPERIENCE BY <a href="#">ARHAM PRINTERS</a>
    </div>

    <div class="cyber-nav">
        <a href="tel:<?php echo $shop['phone']; ?>" class="nav-btn"><i class="fas fa-phone"></i></a>
        <a href="https://wa.me/<?php echo $shop['phone']; ?>" class="book-btn">
            BOOK NOW <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
        </a>
        <a href="<?php echo $shop['map_link']; ?>" class="nav-btn"><i class="fas fa-map-marker-alt"></i></a>
    </div>

    <script>
        // 1. LOADER
        window.onload = function() {
            document.getElementById('loader').style.opacity = '0';
            setTimeout(() => document.getElementById('loader').style.display = 'none', 1000);
        }

        // 2. VANILLA TILT INIT
        VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
            max: 15, speed: 400, glare: true, "max-glare": 0.2
        });

        // 3. THREE.JS SPARK ENGINE (The "Masterpiece" Background)
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('canvas-bg').appendChild(renderer.domElement);

        // Sparks Particles
        const geometry = new THREE.BufferGeometry();
        const count = 300;
        const positions = new Float32Array(count * 3);
        const speeds = new Float32Array(count);

        for(let i=0; i<count * 3; i++) {
            positions[i] = (Math.random() - 0.5) * 20; // Spread X Y Z
        }
        for(let i=0; i<count; i++) {
            speeds[i] = Math.random() * 0.05 + 0.02;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

        // Create a simple glowing dot texture manually
        const canvas = document.createElement('canvas');
        canvas.width = 32; canvas.height = 32;
        const context = canvas.getContext('2d');
        const grad = context.createRadialGradient(16,16,0,16,16,16);
        grad.addColorStop(0, '#ff8c00'); // Orange core
        grad.addColorStop(1, 'transparent');
        context.fillStyle = grad; context.fillRect(0,0,32,32);
        const texture = new THREE.CanvasTexture(canvas);

        const material = new THREE.PointsMaterial({
            size: 0.15, map: texture, transparent: true, 
            blending: THREE.AdditiveBlending, depthWrite: false
        });

        const particles = new THREE.Points(geometry, material);
        scene.add(particles);

        camera.position.z = 5;

        // Animation Loop
        function animate() {
            requestAnimationFrame(animate);
            
            const positions = particles.geometry.attributes.position.array;
            
            for(let i=1; i<count * 3; i+=3) {
                // Move sparks UP (Y axis)
                positions[i] += speeds[(i-1)/3]; 
                
                // If too high, reset to bottom
                if(positions[i] > 10) {
                    positions[i] = -10;
                    positions[i-1] = (Math.random() - 0.5) * 20; // Random X
                }
            }
            
            particles.geometry.attributes.position.needsUpdate = true;
            particles.rotation.y += 0.002; // Rotate whole system
            
            renderer.render(scene, camera);
        }
        animate();

        // Handle Resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
