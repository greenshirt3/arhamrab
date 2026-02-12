import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';

export class Engine {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.mesh = null;
        this.controls = null;
        this.init();
    }

    init() {
        // 1. Scene & Fog (Depth)
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x0a0a0a);
        
        // 2. Camera (Cinematic FOV)
        const aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera = new THREE.PerspectiveCamera(40, aspect, 0.1, 100);

        // 3. Renderer (High Fidelity)
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: "high-performance" });
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.container.appendChild(this.renderer.domElement);

        // 4. Controls
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.maxPolarAngle = Math.PI / 1.6; // Prevent looking from under floor
        this.controls.minDistance = 2;
        this.controls.maxDistance = 20;

        // 5. Studio Lighting
        this.setupLights();
        
        this.animate();
    }

    setupLights() {
        const ambient = new THREE.HemisphereLight(0xffffff, 0x222222, 1.2);
        this.scene.add(ambient);

        const mainLight = new THREE.DirectionalLight(0xffffff, 2.0);
        mainLight.position.set(5, 8, 5);
        mainLight.castShadow = true;
        mainLight.shadow.mapSize.set(1024, 1024);
        this.scene.add(mainLight);

        const rimLight = new THREE.SpotLight(0x4455ff, 4.0);
        rimLight.position.set(-5, 4, -5);
        this.scene.add(rimLight);
    }

    renderProduct(data) {
        if(this.mesh) this.scene.remove(this.mesh);

        // Geometry: Use RoundedBox for cards, Box for paper
        let geometry;
        if(data.radius > 0) {
            geometry = new RoundedBoxGeometry(data.width, data.height, data.depth, 4, data.radius);
        } else {
            geometry = new THREE.BoxGeometry(data.width, data.height, data.depth);
        }

        // Material: Physical (Realism)
        const matSettings = {
            color: 0xffffff,
            roughness: (data.finish_type === 'gloss' ? 0.2 : 0.7),
            metalness: 0.0,
            clearcoat: (data.finish_type === 'gloss' ? 1.0 : 0.0),
            clearcoatRoughness: 0.1
        };

        const baseMat = new THREE.MeshPhysicalMaterial(matSettings);
        
        // 6 Sides
        const materials = [
            baseMat.clone(), baseMat.clone(), baseMat.clone(), baseMat.clone(),
            baseMat.clone(), // Front
            baseMat.clone()  // Back
        ];

        this.mesh = new THREE.Mesh(geometry, materials);
        this.mesh.castShadow = true;
        this.mesh.receiveShadow = true;
        this.mesh.rotation.y = 0.3; // Slight stylish angle
        this.scene.add(this.mesh);

        // Move Camera
        this.camera.position.set(0, 1, data.camZ);
        this.controls.target.set(0,0,0);
        this.controls.update();
    }

    applyTexture(side, file) {
        if(!this.mesh || !file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const texture = new THREE.Texture(img);
                texture.colorSpace = THREE.SRGBColorSpace;
                texture.anisotropy = 16;
                
                // MIRROR FIX FOR BACK SIDE
                if(side === 'back') {
                    texture.wrapS = THREE.RepeatWrapping;
                    texture.repeat.x = -1; 
                }
                
                texture.center.set(0.5, 0.5);
                texture.needsUpdate = true;

                const index = (side === 'front') ? 4 : 5;
                this.mesh.material[index].map = texture;
                this.mesh.material[index].needsUpdate = true;

                if(side === 'back') this.flipTo('back');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    flipTo(side) {
        if(!this.mesh) return;
        this.mesh.rotation.x = 0;
        this.mesh.rotation.y = (side === 'back') ? Math.PI : 0;
    }

    toggleFlip() {
        if(!this.mesh) return;
        const current = Math.abs(this.mesh.rotation.y);
        this.mesh.rotation.y = (current < 0.1) ? Math.PI : 0;
    }

    setFinish(type) {
        if(!this.mesh) return;
        const isGloss = (type === 'gloss');
        this.mesh.material.forEach(m => {
            m.roughness = isGloss ? 0.2 : 0.7;
            m.clearcoat = isGloss ? 1.0 : 0.0;
            m.needsUpdate = true;
        });
    }

    resize() {
        this.camera.aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
    }

    animate() {
        requestAnimationFrame(this.animate.bind(this));
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }
}