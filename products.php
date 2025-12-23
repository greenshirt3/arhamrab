<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Arham Printers - Product Catalog</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Printing Services, Business Cards, Stationery, Marketing Materials" name="keywords">
    <meta content="Professional printing services product catalog." name="description">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    
    <style>
        /* Essential CSS for Layout */
        :root { --bs-primary: #f28b00; --bs-dark: #212529; }
        .section { display: none; }
        .section.active { display: block; }
        
        .product-item .card img {
            height: 200px;
            object-fit: cover;
            background-color: #f8f9fa;
        }
        
        .final-price-box {
            background: var(--bs-primary);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            display: none;
            text-align: center;
        }
        
        .choice-card {
            cursor: pointer;
            border: 2px solid #ddd;
            padding: 10px;
            text-align: center;
            min-width: 100px;
            flex: 1;
        }
        .choice-card.selected { border-color: var(--bs-primary); background-color: var(--bs-primary); color: white; }
        .choice-card input { display: none; }
        
        #mobile-bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; height: 60px;
            background: white; border-top: 1px solid #ddd; z-index: 1030;
            display: flex; justify-content: space-around; align-items: center;
        }
        .mobile-nav-link { text-decoration: none; color: var(--bs-dark); font-size: 0.7rem; text-align: center; }
        .mobile-nav-link.active { color: var(--bs-primary); }
        
        body { padding-bottom: 70px; } /* Space for mobile nav */
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top shadow-sm px-4">
        <a href="index.php" class="navbar-brand d-flex align-items-center">
            <img src="img/logo2.webp" alt="Arham Printers" style="height: 40px; margin-right: 10px;">
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-3">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="#" class="nav-item nav-link active">Products</a>
                <a href="wedding.php" class="nav-item nav-link">Wedding</a>
                <a href="prints.php" class="nav-item nav-link">Prints</a>
                <a href="#" onclick="showSection('contact')" class="nav-item nav-link">Contact</a>
            </div>
        </div>
    </nav>

    <nav id="mobile-bottom-nav" class="d-lg-none">
        <a href="index.php" class="mobile-nav-link"><i class="fas fa-home fa-lg mb-1"></i><br>Home</a>
        <a href="#" class="mobile-nav-link active"><i class="fas fa-th-list fa-lg mb-1"></i><br>Products</a>
        <a href="wedding.php" class="mobile-nav-link"><i class="fas fa-gift fa-lg mb-1"></i><br>Wedding</a>
        <a href="#" onclick="showSection('cart')" class="mobile-nav-link"><i class="fas fa-shopping-cart fa-lg mb-1"></i><br>Cart</a>
    </nav>

    <section id="shop-catalog-section" class="section active">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-6">Our Products</h2>
                <div class="input-group w-75 mx-auto mt-3">
                    <input type="text" id="search-input" class="form-control" placeholder="Search products..." onkeyup="filterProducts()">
                    <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
            
            <div id="debug-area" class="alert alert-danger" style="display:none;"></div>

            <div class="row g-4" id="main-product-catalog">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading Products...</p>
                </div>
            </div>
        </div>
    </section>

    <section id="product-detail-section" class="section">
        <div class="container py-5">
            <button class="btn btn-outline-secondary mb-4" onclick="showSection('shop-catalog')"><i class="fa fa-arrow-left"></i> Back</button>
            <div class="row g-5">
                <div class="col-lg-6 text-center">
                    <img id="detail-product-image" src="" class="img-fluid rounded shadow-sm" style="max-height:400px;" onerror="this.src='img/products/placeholder.png'">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold" id="detail-product-name">Product Name</h2>
                    <hr>
                    
                    <div id="area-inputs" style="display:none;" class="mb-3 p-3 bg-light rounded">
                        <label class="fw-bold mb-2">Size (Feet):</label>
                        <div class="d-flex gap-2">
                            <input type="number" id="input-width" class="form-control" placeholder="Width" oninput="updatePrice()">
                            <span class="align-self-center">x</span>
                            <input type="number" id="input-height" class="form-control" placeholder="Height" oninput="updatePrice()">
                        </div>
                        <small class="text-muted" id="area-display"></small>
                    </div>

                    <div id="standard-options"></div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Quantity:</label>
                        <input type="number" id="input-qty" class="form-control" value="1" min="1" oninput="updatePrice()">
                    </div>

                    <div class="final-price-box" id="finalPriceBox">
                        <h4>Total: <span id="finalPriceDisplay">PKR 0</span></h4>
                        <button class="btn btn-light w-100 mt-2 text-primary fw-bold" onclick="addToCart()">Add to Cart</button>
                    </div>
                    
                    <div class="alert alert-info mt-3" id="price-warning">Please enter details to see price.</div>
                </div>
            </div>
        </div>
    </section>

    <section id="cart-section" class="section">
        <div class="container py-5 text-center">
            <h2>Your Cart</h2>
            <div id="cart-items-list" class="my-4">Your cart is empty.</div>
            <button class="btn btn-primary" onclick="showSection('checkout')">Checkout</button>
        </div>
    </section>
    
    <section id="contact-section" class="section">
        <div class="container py-5 text-center">
            <h2>Contact Us</h2>
            <p>WhatsApp: +92 300 6238233</p>
            <a href="https://wa.me/923006238233" class="btn btn-success"><i class="fab fa-whatsapp"></i> Chat Now</a>
        </div>
    </section>

    <script src="js/main.js"></script>
    <script src="js/cart-manager.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const PUBLIC_DATA_URL = 'all_prices.json';
    let allProductGroups = {};
    let currentProduct = null;
    let currentSelections = {};

    // --- AREA BASED PRODUCTS ---
    const AREA_BASED_META = {
        "China": { rate: 35, min_area: 2 },
        "Star": { rate: 65, min_area: 2 },
        "Backlit": { rate: 100, min_area: 2 },
        "One Vision": { rate: 90, min_area: 2 },
        "Venyl Sticker": { rate: 90, min_area: 2 },
        "3D Wallpaper": { rate: 30, min_area: 20 }
    };

    // --- INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch(PUBLIC_DATA_URL);
            if (!response.ok) throw new Error("HTTP " + response.status);
            const data = await response.json();
            
            processData(data);
            renderCatalog();
        } catch (error) {
            console.error("Data Load Error:", error);
            document.getElementById('main-product-catalog').innerHTML = '';
            const debug = document.getElementById('debug-area');
            debug.style.display = 'block';
            debug.innerHTML = `<strong>Error Loading Products:</strong><br>
                1. Ensure <code>all_prices.json</code> is in the root folder.<br>
                2. Check browser console (F12) for details.<br>
                3. Technical Error: ${error.message}`;
        }
    });

    function processData(flatData) {
        Object.keys(flatData).forEach(cat => {
            if (cat === "Special Occasions (Wedding)") return; // Skip wedding
            
            Object.keys(flatData[cat]).forEach(prod => {
                const raw = flatData[cat][prod];
                let img = 'placeholder';
                
                // Robust Image Path Extraction
                if (raw.imageFile) img = raw.imageFile;
                else if (Array.isArray(raw) && raw[0].imageFile) img = raw[0].imageFile;
                
                // Clean path: "img/products/vcard.webp" -> "vcard"
                if (img.includes('/')) img = img.split('/').pop().split('.')[0];

                allProductGroups[prod] = {
                    name: prod,
                    category: cat,
                    image: img,
                    isArea: !!AREA_BASED_META[prod],
                    combinations: Array.isArray(raw) ? raw : []
                };
            });
        });
    }

    function renderCatalog() {
        const container = document.getElementById('main-product-catalog');
        let html = '';
        
        const cats = [...new Set(Object.values(allProductGroups).map(p => p.category))];
        cats.forEach(cat => {
            html += `<div class="col-12"><h4 class="border-bottom pb-2 mt-4">${cat}</h4></div>`;
            Object.values(allProductGroups).filter(p => p.category === cat).forEach(p => {
                html += `
                <div class="col-lg-3 col-6 product-item" data-name="${p.name}">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="img/products/${p.image}.webp" 
                             onerror="this.src='img/products/placeholder.png'; this.style.objectFit='contain';" 
                             class="card-img-top">
                        <div class="card-body p-2 text-center">
                            <h6 class="card-title mb-2">${p.name}</h6>
                            <button class="btn btn-primary btn-sm w-100" onclick="showDetails('${p.name}')">View Details</button>
                        </div>
                    </div>
                </div>`;
            });
        });
        container.innerHTML = html;
    }

    function showDetails(name) {
        currentProduct = allProductGroups[name];
        currentSelections = {};
        
        document.getElementById('detail-product-name').textContent = name;
        
        // Image Handling
        const imgEl = document.getElementById('detail-product-image');
        imgEl.src = `img/products/${currentProduct.image}.png`;
        imgEl.onerror = () => { imgEl.src = `img/products/${currentProduct.image}.webp`; }; // Try WebP if PNG fails
        
        const areaBox = document.getElementById('area-inputs');
        const stdBox = document.getElementById('standard-options');
        
        areaBox.style.display = currentProduct.isArea ? 'block' : 'none';
        stdBox.innerHTML = '';

        if (!currentProduct.isArea && currentProduct.combinations.length > 0) {
            // Render Options
            const keys = Object.keys(currentProduct.combinations[0]).filter(k => k !== 'Price' && k !== 'imageFile');
            keys.forEach(k => {
                let opts = [...new Set(currentProduct.combinations.map(c => c[k]))];
                let html = `<div class="mb-3"><label class="fw-bold mb-1">${k}:</label><div class="d-flex flex-wrap gap-2">`;
                opts.forEach(opt => {
                    html += `<label class="choice-card" onclick="selectOpt('${k}', '${opt}', this)">
                                <input type="radio" name="${k}" value="${opt}"> ${opt}
                             </label>`;
                });
                html += `</div></div>`;
                stdBox.innerHTML += html;
            });
        }

        showSection('product-detail');
        updatePrice();
    }

    function selectOpt(key, val, el) {
        currentSelections[key] = val;
        // Visual selection
        el.parentElement.querySelectorAll('.choice-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        updatePrice();
    }

    function updatePrice() {
        const qty = parseInt(document.getElementById('input-qty').value) || 1;
        let price = 0;
        let valid = false;

        if (currentProduct.isArea) {
            const w = parseFloat(document.getElementById('input-width').value) || 0;
            const h = parseFloat(document.getElementById('input-height').value) || 0;
            if (w > 0 && h > 0) {
                const area = w * h;
                const rate = AREA_BASED_META[currentProduct.name].rate;
                price = area * rate * qty;
                document.getElementById('area-display').textContent = `Total Area: ${(area * qty).toFixed(2)} sq ft`;
                valid = true;
            }
        } else {
            // Find combination
            const match = currentProduct.combinations.find(c => {
                return Object.keys(currentSelections).every(k => c[k] === currentSelections[k]);
            });
            
            // Check if all required options selected
            const reqKeys = Object.keys(currentProduct.combinations[0]).filter(k => k !== 'Price' && k !== 'imageFile');
            const allSelected = reqKeys.every(k => currentSelections[k]);

            if (match && allSelected) {
                price = match.Price * qty;
                valid = true;
            }
        }

        const box = document.getElementById('finalPriceBox');
        const warn = document.getElementById('price-warning');

        if (valid && price > 0) {
            document.getElementById('finalPriceDisplay').textContent = `PKR ${Math.round(price).toLocaleString()}`;
            box.style.display = 'block';
            warn.style.display = 'none';
        } else {
            box.style.display = 'none';
            warn.style.display = 'block';
        }
    }

    function showSection(id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.getElementById(id + '-section').classList.add('active');
        window.scrollTo(0,0);
        
        // Mobile Nav Active State
        document.querySelectorAll('.mobile-nav-link').forEach(l => l.classList.remove('active'));
        // Logic to highlight correct icon...
    }
    
    function filterProducts() {
        const q = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(i => {
            i.style.display = i.dataset.name.toLowerCase().includes(q) ? '' : 'none';
        });
    }
    
    // Fallback for Cart
    function addToCart() {
        if(typeof Cart !== 'undefined') {
            const price = document.getElementById('finalPriceDisplay').innerText.replace('PKR ','').replace(',','');
            Cart.addItem({
                id: Date.now(),
                productName: currentProduct.name,
                basePrice: parseInt(price),
                quantity: document.getElementById('input-qty').value,
                options: currentSelections
            });
        } else {
            alert("Cart system loading...");
        }
    }
    </script>
</body>
</html>
