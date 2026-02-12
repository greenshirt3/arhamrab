import { Engine } from './Engine.js';

export class App {
    constructor() {
        this.engine = new Engine('canvas-container');
        this.products = [];
        this.init();
    }

    async init() {
        try {
            // Fetch Products with Cache Busting
            const res = await fetch('data/products.json?v=' + Date.now());
            this.products = await res.json();

            this.setupUI();
            this.engine.renderProduct(this.products[0]); // Load first item
            
            // Remove Loader
            document.getElementById('loader').style.display = 'none';
        } catch (e) {
            alert("System Error: Could not load product database.");
            console.error(e);
        }
    }

    setupUI() {
        // Populate Dropdown
        const select = document.getElementById('product-select');
        this.products.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.innerText = p.name;
            select.appendChild(opt);
        });

        // Event: Change Product
        select.addEventListener('change', (e) => {
            const p = this.products.find(i => i.id === e.target.value);
            if(p) this.engine.renderProduct(p);
        });

        // Event: Uploads
        document.getElementById('file-front').addEventListener('change', (e) => {
            this.engine.applyTexture('front', e.target.files[0]);
        });
        document.getElementById('file-back').addEventListener('change', (e) => {
            this.engine.applyTexture('back', e.target.files[0]);
        });

        // Event: Flip
        document.getElementById('btn-flip').addEventListener('click', () => this.engine.toggleFlip());

        // Event: Finish
        window.setFinish = (type) => {
            this.engine.setFinish(type);
            // Toggle UI classes
            document.querySelectorAll('.finish-btn').forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
            document.getElementById('btn-'+type).classList.add('bg-blue-600', 'text-white');
        };

        // Event: Save
        document.getElementById('btn-save').addEventListener('click', () => this.saveOrder());

        // Event: Resize
        window.addEventListener('resize', () => this.engine.resize());
    }

    async saveOrder() {
        const btn = document.getElementById('btn-save');
        btn.innerText = "Saving...";
        
        const data = {
            product: document.getElementById('product-select').value,
            timestamp: Date.now()
        };

        try {
            const res = await fetch('api/save_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if(result.status === 'success') {
                alert("✅ Order Saved! Ref: " + result.ref);
            } else {
                alert("Error saving order.");
            }
        } catch(e) {
            alert("Network Error");
        }
        btn.innerText = "✅ Approve & Save";
    }
}