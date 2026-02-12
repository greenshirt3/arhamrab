<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Invite - Ali Raza</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* BACKGROUND GRADIENT FOR FLOWERS */
        body { margin: 0; overflow: hidden; background: linear-gradient(to bottom, #1a0b0b, #2c1e1e); font-family: 'Montserrat', sans-serif; }
        
        #loader { position: fixed; inset: 0; background: #000; z-index: 999; display: flex; align-items: center; justify-content: center; flex-direction: column; transition: opacity 1s; }
        .gold-ring { width: 60px; height: 60px; border: 2px solid #333; border-top: 2px solid #fff; border-radius: 50%; animation: spin 1s infinite linear; }
        
        .ticker-wrap { position: fixed; bottom: 0; width: 100%; height: 40px; background: #000; border-top: 1px solid #fff; z-index: 50; display: flex; align-items: center; overflow: hidden; }
        .ticker-move { white-space: nowrap; color: #fff; font-size: 0.8rem; letter-spacing: 2px; animation: ticker 30s linear infinite; padding-left: 100%; }
        
        .wa-float { position: fixed; bottom: 60px; z-index: 60; color: #fff; background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); padding: 10px 20px; border-radius: 30px; text-decoration: none; border: 1px solid rgba(255,255,255,0.2); font-size: 0.85rem; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .wa-float:hover { background: #fff; color: #000; }
        .wa-left { left: 20px; } .wa-right { right: 20px; }
        
        .btn-main { 
            position: fixed; top: 85%; left: 50%; transform: translate(-50%, -50%);
            padding: 15px 40px; border-radius: 50px;
            background: #fff; color: #000; border: none; cursor: pointer;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.4); z-index: 70;
            font-family: 'Cinzel', serif; font-weight: 700; letter-spacing: 2px;
            font-size: 1rem; transition: 0.3s; white-space: nowrap;
        }
        .btn-main:hover { transform: translate(-50%, -50%) scale(1.05); box-shadow: 0 0 40px rgba(255, 255, 255, 0.8); }

        .btn-music {
            position: fixed; top: 20px; right: 20px; z-index: 70;
            width: 50px; height: 50px; border-radius: 50%;
            background: rgba(0,0,0,0.5); border: 1px solid #fff; color: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.3s; font-size: 1.2rem;
        }
        .btn-music:hover { background: #fff; color: #000; }
        .music-anim { animation: pulse-music 1.5s infinite; }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes ticker { 100% { transform: translateX(-100%); } }
        @keyframes pulse-music { 0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(255, 255, 255, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); } }
    </style>
    <script type="importmap">{ "imports": { "three": "https://unpkg.com/three@0.160.0/build/three.module.js", "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/", "@tweenjs/tween.js": "https://unpkg.com/@tweenjs/tween.js@23.1.1/dist/tween.esm.js" } }</script>
</head>
<body>
    <div id="loader"><div class="gold-ring"></div><div style="color:#fff;margin-top:20px;letter-spacing:3px;">PREPARING INVITE...</div></div>
    
    <audio id="bg-music" loop><source src="audio.mp3" type="audio/mpeg"></audio>

    <div class="ticker-wrap"><div class="ticker-move" id="ticker-content">...</div></div>
    <a href="#" id="wa-arham" class="wa-float wa-left" target="_blank"><i class="fab fa-whatsapp"></i> Arham Printers</a>
    <a href="#" id="wa-host" class="wa-float wa-right" target="_blank"><i class="fab fa-whatsapp"></i> Contact Host</a>
    <button class="btn-music" onclick="toggleMusic()" title="Play Music"><i id="music-icon" class="fa fa-music"></i></button>
    <button class="btn-main" onclick="window.toggleCard()" id="main-btn">OPEN INVITATION</button>

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
        import * as TWEEN from '@tweenjs/tween.js';

        let scene, camera, renderer, controls, cardGroup, hinge, data, isOpen = false;
        let flowerSystem;
        const music = document.getElementById('bg-music');
        let isPlaying = false;

        init();

        async function init() {
            // SCENE
            scene = new THREE.Scene(); 
            // We set background to null so CSS Gradient shows through for the "Atmosphere"
            scene.background = null; 

            // WIDE VIEW CAMERA
            const isMobile = window.innerWidth < 768;
            camera = new THREE.PerspectiveCamera(40, window.innerWidth/window.innerHeight, 0.1, 100);
            const startZ = isMobile ? 16 : 12; 
            camera.position.set(0, 2, startZ);

            // RENDERER with Alpha for Background
            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.shadowMap.enabled = true; 
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            document.body.appendChild(renderer.domElement);

            // HIGH KEY LIGHTING (Bright White Card)
            const ambient = new THREE.AmbientLight(0xffffff, 2.0); 
            scene.add(ambient);
            
            const spotKey = new THREE.SpotLight(0xffffff, 40); 
            spotKey.position.set(5, 12, 10);
            spotKey.castShadow = true; spotKey.shadow.bias = -0.0001; scene.add(spotKey);
            
            const spotRim = new THREE.SpotLight(0xffffff, 10);
            spotRim.position.set(-8, 5, -5); scene.add(spotRim);

            // FLOWERS
            createFlowers();

            // DATA
            try {
                const res = await fetch('data.json?v='+Date.now());
                data = await res.json();
                updateUI(); await buildCard();
                document.getElementById('loader').style.opacity = 0;
                setTimeout(()=>document.getElementById('loader').style.display='none',1500);
            } catch(e) { console.error(e); }

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true; controls.autoRotate = true; controls.autoRotateSpeed = 0.5;
            controls.minDistance = 5; controls.maxDistance = 25;
            window.addEventListener('resize', onResize);
            animate();
        }

        // --- BACKGROUND FLOWER SYSTEM ---
        function createFlowers() {
            const partGeo = new THREE.BufferGeometry();
            const partCount = 400; // Number of petals
            const posArray = new Float32Array(partCount * 3);
            const velArray = []; // Store velocities

            for(let i=0; i<partCount*3; i+=3) {
                posArray[i] = (Math.random() - 0.5) * 30;   // X
                posArray[i+1] = (Math.random() - 0.5) * 30; // Y
                posArray[i+2] = (Math.random() - 0.5) * 20 - 5; // Z (Behind card)
                
                // Random fall speed
                velArray.push({
                    y: Math.random() * 0.02 + 0.01,
                    x: (Math.random() - 0.5) * 0.01,
                    rot: Math.random() * 0.02
                });
            }
            
            partGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
            
            // Create a simple circular/petal shape texture manually (no image load)
            const canvas = document.createElement('canvas');
            canvas.width = 32; canvas.height = 32;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffb7b2'; // Rose Pink
            ctx.beginPath();
            ctx.ellipse(16, 16, 8, 14, Math.PI/4, 0, 2*Math.PI);
            ctx.fill();
            const tex = new THREE.CanvasTexture(canvas);

            const partMat = new THREE.PointsMaterial({
                size: 0.8, map: tex, transparent: true, opacity: 0.8, 
                depthWrite: false, blending: THREE.AdditiveBlending
            });

            flowerSystem = new THREE.Points(partGeo, partMat);
            flowerSystem.userData = { velocities: velArray };
            scene.add(flowerSystem);
        }

        function animateFlowers() {
            if(!flowerSystem) return;
            const positions = flowerSystem.geometry.attributes.position.array;
            const vels = flowerSystem.userData.velocities;

            for(let i=0; i<vels.length; i++) {
                // Move down
                positions[i*3 + 1] -= vels[i].y;
                // Drift X
                positions[i*3] += vels[i].x;

                // Reset to top if too low
                if(positions[i*3 + 1] < -15) {
                    positions[i*3 + 1] = 15;
                    positions[i*3] = (Math.random() - 0.5) * 30;
                }
            }
            flowerSystem.geometry.attributes.position.needsUpdate = true;
            flowerSystem.rotation.y += 0.001; // Global spin
        }

        window.toggleMusic = function() {
            const btn = document.querySelector('.btn-music');
            const icon = document.getElementById('music-icon');
            if(isPlaying) { music.pause(); btn.classList.remove('music-anim'); icon.className="fa fa-music"; }
            else { music.play().catch(e=>alert("Click page first")); btn.classList.add('music-anim'); icon.className="fa fa-pause"; }
            isPlaying = !isPlaying;
        }

        function updateUI() {
            document.getElementById('ticker-content').innerText = data.config.ticker;
            document.getElementById('wa-arham').href = `https://wa.me/${data.contacts.arham_wa}`;
            document.getElementById('wa-host').href = `https://wa.me/${data.contacts.reception_wa}`;
        }

        function floatText(ctx, txt, x, y, font, color, shadowBlur=0) {
            ctx.font = font;
            // No blur, sharp text for black
            ctx.fillStyle = color; ctx.fillText(txt, x, y);
        }

        function generateTexture(type) {
            const cvs = document.createElement('canvas');
            const w = 1024; const h = 1448; cvs.width = w; cvs.height = h;
            const ctx = cvs.getContext('2d');
            
            // PURE WHITE PAPER
            ctx.fillStyle = data.config.paper_color; ctx.fillRect(0,0,w,h);
            
            // Border (Black)
            ctx.strokeStyle = "#000"; ctx.lineWidth = 4; 
            ctx.strokeRect(60,60,w-120,h-120); 
            // Corner Accents
            ctx.beginPath(); ctx.moveTo(60, 150); ctx.lineTo(60, 60); ctx.lineTo(150, 60); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(w-60, 150); ctx.lineTo(w-60, 60); ctx.lineTo(w-150, 60); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(60, h-150); ctx.lineTo(60, h-60); ctx.lineTo(150, h-60); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(w-60, h-150); ctx.lineTo(w-60, h-60); ctx.lineTo(w-150, h-60); ctx.stroke();

            const cx = w/2; ctx.textAlign = 'center';
            const black = data.config.text_dark; 
            const f = data.card_content.front; const l = data.card_content.left; const r = data.card_content.right;

            if(type === 'front') {
                floatText(ctx, f.bismillah.text, cx, f.bismillah.y, `italic ${f.bismillah.s}px Montserrat`, black);
                floatText(ctx, f.hosts.text.toUpperCase(), cx, f.hosts.y, `bold ${f.hosts.s}px Cinzel`, black);
                floatText(ctx, f.invite_line.text, cx, f.invite_line.y, `italic ${f.invite_line.s}px Montserrat`, black);
                floatText(ctx, f.groom.text, cx, f.groom.y, `bold ${f.groom.s}px Cinzel`, black);
                floatText(ctx, f.groom_last.text, cx, f.groom_last.y, `bold ${f.groom_last.s}px Cinzel`, black);
                floatText(ctx, f.ampersand.text, cx, f.ampersand.y, `italic ${f.ampersand.s}px Great Vibes`, black);
                floatText(ctx, f.bride.text, cx, f.bride.y, `bold ${f.bride.s}px Cinzel`, black);
                
                // Black Line for Date
                ctx.fillStyle = black; ctx.fillRect(cx-100, f.save_date.y - 70, 200, 3);
                floatText(ctx, f.save_date.text, cx, f.save_date.y, `bold ${f.save_date.s}px Montserrat`, black);
            }
            else if(type === 'left') {
                floatText(ctx, l.rsvp_title.text, cx, l.rsvp_title.y, `bold ${l.rsvp_title.s}px Cinzel`, black);
                l.rsvp_names.forEach(n => floatText(ctx, n.text, cx, n.y, `${n.s}px Montserrat`, black));
                floatText(ctx, l.wishes_title.text, cx, l.wishes_title.y, `bold ${l.wishes_title.s}px Cinzel`, black);
                l.wishes_names.forEach(n => floatText(ctx, n.text, cx, n.y, `${n.s}px Montserrat`, black));
            }
            else if(type === 'right') {
                r.events.forEach(evt => {
                    let y = evt.y;
                    // Light Grey Event Box
                    ctx.fillStyle = "#f5f5f5"; ctx.fillRect(100, y-60, w-200, 250);
                    floatText(ctx, evt.title, cx, y, "bold 60px Cinzel", black);
                    floatText(ctx, evt.date, cx, y+70, "bold 40px Montserrat", black);
                    floatText(ctx, evt.detail, cx, y+120, "italic 30px Montserrat", "#333");
                    floatText(ctx, "Venue: "+evt.venue, cx, y+160, "bold 25px Montserrat", black);
                });
            }
            const tex = new THREE.CanvasTexture(cvs); tex.anisotropy=16; tex.colorSpace = THREE.SRGBColorSpace; return tex;
        }

        async function buildCard() {
            cardGroup = new THREE.Group();
            const matPaper = new THREE.MeshStandardMaterial({ color: data.config.paper_color, roughness: 0.6, metalness: 0.1 });
            const w=4.5, h=6.5, d=0.03; const geo = new THREE.BoxGeometry(w,h,d);
            
            const tf = generateTexture('front'); const tl = generateTexture('left'); const tr = generateTexture('right');
            const mf = new THREE.MeshStandardMaterial({map:tf, roughness:0.6});
            const ml = new THREE.MeshStandardMaterial({map:tl, roughness:0.6});
            const mr = new THREE.MeshStandardMaterial({map:tr, roughness:0.6});

            const right = new THREE.Mesh(geo, [matPaper, matPaper, matPaper, matPaper, mr, mf]); 
            right.position.x = w/2; right.castShadow=true; right.receiveShadow=true;
            
            const left = new THREE.Mesh(geo, [matPaper, matPaper, matPaper, matPaper, mf, ml]); 
            left.position.x = -w/2; left.castShadow=true; left.receiveShadow=true;

            hinge = new THREE.Group(); hinge.add(left);
            cardGroup.add(right); cardGroup.add(hinge);
            
            hinge.rotation.y = Math.PI; cardGroup.rotation.x = -0.1;
            scene.add(cardGroup);
        }

        window.toggleCard = function() {
            const isMobile = window.innerWidth < 768;
            const ty = isOpen ? Math.PI : 0.05; const tx = isOpen ? -0.1 : 0; 
            const zoomIn = isMobile ? 12 : 9; const zoomOut = isMobile ? 16 : 12;
            const tz = isOpen ? zoomOut : zoomIn;
            
            new TWEEN.Tween(hinge.rotation).to({y:ty}, 1500).easing(TWEEN.Easing.Cubic.InOut).start();
            new TWEEN.Tween(cardGroup.rotation).to({x:tx}, 1500).start();
            new TWEEN.Tween(camera.position).to({z:tz, y:isOpen?2:1}, 1500).easing(TWEEN.Easing.Cubic.Out).start();
            document.getElementById('main-btn').innerText = isOpen ? "OPEN INVITATION" : "CLOSE CARD";
            isOpen = !isOpen;
        }

        function onResize() {
            camera.aspect = window.innerWidth/window.innerHeight;
            camera.updateProjectionMatrix(); renderer.setSize(window.innerWidth, window.innerHeight);
        }

        function animate() { 
            requestAnimationFrame(animate); 
            TWEEN.update(); 
            controls.update(); 
            animateFlowers(); // Move the petals
            renderer.render(scene, camera); 
        }
    </script>
</body>
</html>