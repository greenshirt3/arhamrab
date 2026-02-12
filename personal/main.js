import * as THREE from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';

// --- CONFIGURATION ---
const FAMILY_DATA = [
    {
        name: "Saif Ullah",
        role: "The Visionary & Father",
        desc: "Founder of Arham Printers. The pillar of strength and guidance.",
        img: "assets/saif.jpg",
        color: 0x0dcaf0 // Cyan
    },
    {
        name: "Khadija Bibi",
        role: "The Heart & Mother",
        desc: "The source of compassion, patience, and family unity.",
        img: "assets/khadija.jpg",
        color: 0xff69b4 // Pink
    },
    {
        name: "Ayat Fatima",
        role: "Eldest Daughter",
        desc: "The first joy. Brilliant, kind, and a role model.",
        img: "assets/ayat.jpg",
        color: 0xffd700 // Gold
    },
    {
        name: "Iman Fatima",
        role: "Younger Daughter",
        desc: "The creative spark. Full of life and endless curiosity.",
        img: "assets/iman.jpg",
        color: 0x90ee90 // Light Green
    },
    {
        name: "Muhammad Arham",
        role: "Youngest Son",
        desc: "The future legacy. The reason we build for tomorrow.",
        img: "assets/arham.jpg",
        color: 0xff4500 // Orange Red
    }
];

// --- SETUP ---
const canvas = document.querySelector('#webgl');
const scene = new THREE.Scene();
// Deep Space Background
scene.background = new THREE.Color(0x020205); 
scene.fog = new THREE.FogExp2(0x020205, 0.02);

const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.toneMapping = THREE.ReinhardToneMapping;

// --- POST PROCESSING (BLOOM) ---
const renderScene = new RenderPass(scene, camera);
const bloomPass = new UnrealBloomPass(new THREE.Vector2(window.innerWidth, window.innerHeight), 1.5, 0.4, 0.85);
bloomPass.threshold = 0.2;
bloomPass.strength = 0.8; // Glow intensity
bloomPass.radius = 0.5;

const composer = new EffectComposer(renderer);
composer.addPass(renderScene);
composer.addPass(bloomPass);

// --- LIGHTING ---
const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
scene.add(ambientLight);

// --- ASSETS GENERATOR ---
// Creates a texture from text if image is missing
function createTextTexture(name, role, desc, colorHex) {
    const cvs = document.createElement('canvas');
    cvs.width = 1024; cvs.height = 1024;
    const ctx = cvs.getContext('2d');
    
    // Glass background effect
    ctx.fillStyle = 'rgba(0,0,0,0.8)';
    ctx.fillRect(0,0,1024,1024);
    
    // Border
    ctx.strokeStyle = colorHex;
    ctx.lineWidth = 20;
    ctx.strokeRect(40,40,944,944);

    // Text
    ctx.fillStyle = '#ffffff';
    ctx.textAlign = 'center';
    
    // Name
    ctx.font = 'bold 80px "Cinzel", serif';
    ctx.fillText(name.toUpperCase(), 512, 300);
    
    // Role
    ctx.fillStyle = colorHex;
    ctx.font = '50px "Montserrat", sans-serif';
    ctx.fillText(role.toUpperCase(), 512, 400);
    
    // Divider
    ctx.beginPath();
    ctx.moveTo(300, 450); ctx.lineTo(724, 450);
    ctx.stroke();

    // Desc (Wrap text logic simplified)
    ctx.fillStyle = '#cccccc';
    ctx.font = '40px "Montserrat", sans-serif';
    const words = desc.split(' ');
    let line = '';
    let y = 550;
    for(let n = 0; n < words.length; n++) {
        const testLine = line + words[n] + ' ';
        if (ctx.measureText(testLine).width > 800 && n > 0) {
            ctx.fillText(line, 512, y);
            line = words[n] + ' ';
            y += 60;
        } else {
            line = testLine;
        }
    }
    ctx.fillText(line, 512, y);

    return new THREE.CanvasTexture(cvs);
}

// --- BUILDING THE HELIX ---
const cards = [];
const curvePoints = [];
const textureLoader = new THREE.TextureLoader();

// HELIX PARAMETERS
const radius = 12;
const heightStep = 10; 
const initialY = 10;

FAMILY_DATA.forEach((member, i) => {
    // 1. Calculate Position on Helix
    const angle = i * 1.5; // Spread items around circle
    const x = Math.cos(angle) * radius;
    const z = Math.sin(angle) * radius;
    const y = initialY - (i * heightStep);
    
    const position = new THREE.Vector3(x, y, z);
    curvePoints.push(position);

    // 2. Create Photo Plane
    // Try load image, fallback to generated texture
    let map = createTextTexture(member.name, member.role, member.desc, '#' + member.color.toString(16));
    
    // Attempt real image load
    textureLoader.load(member.img, (tex) => {
        // If image loads, we overlay it or replace. 
        // For simplicity in this masterpiece, we keep the text card as the info 
        // and put the photo in a circle above it.
        const photoGeo = new THREE.CircleGeometry(3, 32);
        const photoMat = new THREE.MeshBasicMaterial({ map: tex });
        const photoMesh = new THREE.Mesh(photoGeo, photoMat);
        photoMesh.position.set(0, 5, 0.5); // Float above text card
        cardGroup.add(photoMesh);
    });

    // 3. Create Glass Card (The Base)
    const cardGeo = new THREE.PlaneGeometry(10, 10);
    const cardMat = new THREE.MeshPhysicalMaterial({
        map: map,
        color: 0xffffff,
        metalness: 0.1,
        roughness: 0.2,
        transmission: 0.6, // Glass effect
        thickness: 0.5,
        transparent: true,
        side: THREE.DoubleSide
    });

    const cardGroup = new THREE.Group();
    const cardMesh = new THREE.Mesh(cardGeo, cardMat);
    cardGroup.add(cardMesh);

    // 4. Add Glowing Rim
    const borderGeo = new THREE.EdgesGeometry(cardGeo);
    const borderMat = new THREE.LineBasicMaterial({ color: member.color, linewidth: 2 });
    const border = new THREE.LineSegments(borderGeo, borderMat);
    cardGroup.add(border);

    // 5. Position & Orient
    cardGroup.position.copy(position);
    cardGroup.lookAt(0, y, 0); // Face center initially
    
    scene.add(cardGroup);
    cards.push({ group: cardGroup, pos: position, data: member });

    // 6. Add Point Light to make it pop
    const light = new THREE.PointLight(member.color, 2, 20);
    light.position.set(x, y + 2, z + 2);
    scene.add(light);
});

// --- CONNECTION LINE (THE DNA) ---
const curve = new THREE.CatmullRomCurve3(curvePoints);
const tubeGeo = new THREE.TubeGeometry(curve, 64, 0.1, 8, false);
const tubeMat = new THREE.MeshBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.3 });
const tube = new THREE.Mesh(tubeGeo, tubeMat);
scene.add(tube);

// --- PARTICLES (STARDUST) ---
const particlesGeo = new THREE.BufferGeometry();
const particlesCnt = 2000;
const posArray = new Float32Array(particlesCnt * 3);

for(let i = 0; i < particlesCnt * 3; i++) {
    posArray[i] = (Math.random() - 0.5) * 100;
}
particlesGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
const particlesMat = new THREE.PointsMaterial({
    size: 0.1,
    color: 0xffffff,
    transparent: true,
    opacity: 0.8
});
const starField = new THREE.Points(particlesGeo, particlesMat);
scene.add(starField);

// --- SCROLL & CAMERA LOGIC ---
let scrollPercent = 0;
let targetScroll = 0;

// Handle Scroll
document.addEventListener('wheel', (e) => {
    targetScroll += e.deltaY * 0.0005;
    // Clamp between 0 (Top) and 1 (Bottom)
    targetScroll = Math.max(0, Math.min(1, targetScroll));
});

// Handle Touch (Mobile)
let touchStart = 0;
document.addEventListener('touchstart', e => touchStart = e.touches[0].clientY);
document.addEventListener('touchmove', e => {
    const delta = touchStart - e.touches[0].clientY;
    targetScroll += delta * 0.001;
    targetScroll = Math.max(0, Math.min(1, targetScroll));
    touchStart = e.touches[0].clientY;
});

// --- ANIMATION LOOP ---
const clock = new THREE.Clock();

function animate() {
    const time = clock.getElapsedTime();

    // 1. Smooth Scroll
    scrollPercent += (targetScroll - scrollPercent) * 0.05;

    // 2. Camera Path (Fly along the curve but offset)
    // We map scroll (0-1) to the curve points
    // But we want to stop at specific family members
    
    // Total vertical distance covered
    const totalHeight = FAMILY_DATA.length * heightStep;
    const currentY = initialY - (scrollPercent * totalHeight);
    
    // Spiral logic for camera
    const camAngle = scrollPercent * (FAMILY_DATA.length * 1.5) + Math.PI; // Offset angle
    const camX = Math.cos(camAngle) * 20; // 20 units away
    const camZ = Math.sin(camAngle) * 20;
    
    camera.position.set(camX, currentY + 2, camZ);
    camera.lookAt(0, currentY - 2, 0); // Look slightly down at the center axis

    // 3. Animate Cards (Hover effect)
    cards.forEach((card, index) => {
        // Floating motion
        card.group.position.y = card.pos.y + Math.sin(time + index) * 0.2;
        
        // Rotate cards to always face camera slightly
        card.group.lookAt(camera.position);
    });

    // 4. Animate Stars
    starField.rotation.y = time * 0.05;

    // 5. Render
    composer.render();
    requestAnimationFrame(animate);
}

// Handle Resize
window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
    composer.setSize(window.innerWidth, window.innerHeight);
});

// Remove Loader
window.onload = () => {
    setTimeout(() => {
        const loader = document.getElementById('loader');
        loader.style.opacity = '0';
        setTimeout(() => loader.style.display = 'none', 1000);
    }, 1500);
};

animate();
