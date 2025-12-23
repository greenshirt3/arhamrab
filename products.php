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
    const WHATSAPP_NUMBER = '923006238233';
    
    let allProductGroups = {};
    let currentProduct = null;
    let currentSelectedVariations = {};
    let currentShippingZone = 'Within City'; 
    let currentDeliveryType = 'HomeDelivery';

    const SHIPPING_RATES = {
        'small': { 'Within City': 100, 'Same Province': 347, 'Cross Province': 359 },
        'medium': { 'Within City': 100, 'Same Province': 529, 'Cross Province': 541 },
        'overland': { 'Within City': 150, 'Same Province': 1254, 'Cross Province': 1254 }
    };

    const AREA_BASED_META = {
        "China": { rate: 35, min_area: 2, category: 'Banners' },
        "Star": { rate: 65, min_area: 2, category: 'Banners' },
        "Backlit": { rate: 100, min_area: 2, category: 'Banners' },
        "One Vision": { rate: 90, min_area: 2, category: 'Banners' }, 
        "Venyl Sticker": { rate: 90, min_area: 2, category: 'Banners' }, 
        "3D Wallpaper": { rate: 30, min_area: 20, category: '3D Wallpaper' }
    };
    const AREA_BASED_PRODUCT_NAMES = Object.keys(AREA_BASED_META);

    const DESIGN_FEE_TIERS = [
        { min_sqft: 1, max_sqft: 10, base_fee: 300 },
        { min_sqft: 11, max_sqft: 24, base_fee: 400 },
        { min_sqft: 25, max_sqft: 50, base_fee: 500 },
        { min_sqft: 51, max_sqft: Infinity, base_fee: 700 },
    ];

    const AREA_PROFIT_TIERS = [
        { min_qty: 1, max_qty: 10, multiplier: 1.20 },
        { min_qty: 11, max_qty: 20, multiplier: 1.18 },
        { min_qty: 21, max_qty: 50, multiplier: 1.14 },
        { min_qty: 51, max_qty: Infinity, multiplier: 1.10 },
    ];

    async function initialAppLoad() {
        try {
            const response = await fetch(PUBLIC_DATA_URL);
            const rawData = await response.json();
            initialProductSetup(rawData);
            renderCatalog();
            showSection('shop-catalog');
        } catch (error) {
            console.error("Error loading all_prices.json", error);
            document.getElementById('main-product-catalog').innerHTML = '<p class="alert alert-danger">Failed to load products. Check all_prices.json.</p>';
        }
    }

    function initialProductSetup(flatData) {
        Object.keys(flatData).forEach(catKey => {
            if (catKey === "B/W and Color Prints") {
                // Handled in prints.php usually, but good to have here to prevent errors
                return;
            }
            if (catKey === "Special Occasions (Wedding)") return; 

            const catData = flatData[catKey];
            Object.keys(catData).forEach(prodKey => {
                const data = catData[prodKey];
                let isArea = AREA_BASED_PRODUCT_NAMES.includes(prodKey);
                let img = 'placeholder';
                
                if (data.imageFile) img = data.imageFile;
                else if (Array.isArray(data) && data[0].imageFile) img = data[0].imageFile;
                
                if(img.includes('/')) img = img.split('/').pop().split('.')[0];

                allProductGroups[prodKey] = {
                    baseName: prodKey,
                    imageFile: img,
                    isAreaBased: isArea,
                    mainCategory: catKey,
                    isBundleTiered: !!data.isBundleTiered,
                    bundles: data.bundles || null,
                    combinations: Array.isArray(data) ? data : []
                };
            });
        });
    }

    function renderCatalog() {
        const container = document.getElementById('main-product-catalog');
        let html = '';
        const categories = [...new Set(Object.values(allProductGroups).map(p => p.mainCategory))];
        categories.forEach(cat => {
            html += `<div class="col-12"><h4 class="border-bottom pb-2 mt-4">${cat}</h4></div>`;
            Object.values(allProductGroups).filter(p => p.mainCategory === cat).forEach(p => {
                html += `
                <div class="col-lg-3 col-6 product-item" data-name="${p.baseName}">
                    <div class="card h-100 shadow-sm border-0">   
                        <img src="img/products/${p.imageFile}.webp" onerror="this.src='img/products/placeholder.png'" class="card-img-top">
                        <div class="product-footer-overlay"><button class="btn btn-primary w-100 rounded-0 p-3" onclick="showProductDetails('${p.baseName}')">${p.baseName}</button></div>
                    </div>
                </div>`;
            });
        });
        container.innerHTML = html;
    }

    function showProductDetails(name) {
        currentProduct = allProductGroups[name];
        currentSelectedVariations = {};
        
        document.getElementById('detail-product-name-body').textContent = name;
        
        // Image Handling
        const imgEl = document.getElementById('detail-product-image');
        imgEl.src = `img/products/${currentProduct.imageFile}.png`;
        imgEl.onerror = () => { imgEl.src = `img/products/${currentProduct.imageFile}.webp`; }; 
        
        const areaBox = document.getElementById('custom-area-inputs');
        const stdBox = document.getElementById('standard-options-fields');
        
        areaBox.style.display = currentProduct.isAreaBased ? 'block' : 'none';
        stdBox.innerHTML = '';

        if (currentProduct.isBundleTiered) {
            let h = `<div class="product-option-group"><label>Select Bundle:</label><div class="d-flex flex-wrap gap-2">`;
            Object.keys(currentProduct.bundles).forEach(b => {
                h += `<label class="choice-card" onclick="handleCardSelection('bundle', '${b}', false)">
                        <input type="radio" name="bundle" value="${b}"> ${b}
                      </label>`;
            });
            stdBox.innerHTML = h + `</div></div><div class="product-option-group"><label>Qty:</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
        } else if (!currentProduct.isAreaBased && currentProduct.combinations.length > 0) {
            const keys = Object.keys(currentProduct.combinations[0]).filter(k => k!=='Price' && k!=='FullName' && k!=='imageFile');
            keys.forEach(k => {
                stdBox.innerHTML += `<div class="product-option-group"><label>${k}:</label><div class="d-flex flex-wrap gap-2" id="choices-${k}"></div></div>`;
            });
            stdBox.innerHTML += `<div class="product-option-group"><label>Qty:</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
            
            // Populate Options logic
            keys.forEach(k => {
                let options = [...new Set(currentProduct.combinations.map(c => c[k]))];
                let html = '';
                options.forEach(opt => {
                    html += `<label class="choice-card" onclick="handleCardSelection('${k}', '${opt}', true)">
                                <input type="radio" name="${k}" value="${opt}"> ${opt}
                             </label>`;
                });
                document.getElementById(`choices-${k}`).innerHTML = html;
            });

            // Auto-select first option for the first key
            if(keys.length > 0) {
                const firstKey = keys[0];
                const firstInput = document.querySelector(`#choices-${firstKey} input`);
                if(firstInput) {
                    firstInput.checked = true;
                    handleCardSelection(firstKey, firstInput.value, true);
                }
            }
        }
        
        showSection('product-detail');
        updateFinalPrice();
    }

    function handleCardSelection(key, value, cascade) {
        currentSelectedVariations[key] = value;
        // Visual Update
        const group = document.getElementById(`choices-${key}`);
        if(group) {
            group.querySelectorAll('.choice-card').forEach(c => c.classList.remove('selected'));
            const selected = group.querySelector(`input[value="${value}"]`);
            if(selected) selected.closest('.choice-card').classList.add('selected');
        }
        updateFinalPrice();
    }

    function calculateItemPrice() {
        if(!currentProduct) return { error: true };
        
        let qty = parseInt(document.getElementById('input-standard-qty')?.value || document.getElementById('input-custom-qty')?.value || 1);
        let price = 0;

        if(currentProduct.isAreaBased) {
            const w = parseFloat(document.getElementById('input-width').value || 0);
            const h = parseFloat(document.getElementById('input-height').value || 0);
            if(w*h === 0) return { error: true };
            
            const area = w * h;
            const rate = AREA_BASED_META[currentProduct.baseName].rate;
            // Simplified fee logic for robustness
            const designFee = 0; 
            const multiplier = 1.1; 
            
            price = ((area * rate * qty) + designFee) * multiplier;
        } 
        else if (currentProduct.combinations.length) {
            const match = currentProduct.combinations.find(c => {
                return Object.keys(currentSelectedVariations).every(k => c[k] === currentSelectedVariations[k]);
            });
            if(match) price = match.Price * qty;
            else return { error: true };
        }
        else if (currentProduct.isBundleTiered) {
            if(currentSelectedVariations.bundle) price = currentProduct.bundles[currentSelectedVariations.bundle] * qty;
            else return { error: true };
        }

        return { basePrice: Math.round(price), productName: currentProduct.baseName };
    }

    function updateFinalPrice() {
        const res = calculateItemPrice();
        const box = document.getElementById('finalPriceBox');
        
        if(res.error) {
            box.style.display = 'none';
        } else {
            let shipping = 0;
            if(currentDeliveryType !== 'SelfPickUp') {
                shipping = 200; // Flat rate fallback
            }
            document.getElementById('finalPriceDisplay').textContent = `PKR ${(res.basePrice + shipping).toLocaleString()}`;
            document.getElementById('shipping-cost-display').textContent = `PKR ${shipping}`;
            box.style.display = 'block';
        }
    }

    // Standard Nav & Cart Functions
    function showSection(id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        const el = document.getElementById(id + '-section');
        if(el) el.classList.add('active');
        window.scrollTo(0,0);
    }
    
    function manualLocationCheck() {
        const val = document.getElementById('input-city').value.toLowerCase();
        const zoneEl = document.getElementById('zone-name');
        if(val.includes('jalalpur')) {
            currentShippingZone = 'Within City';
            zoneEl.className = 'fw-bold text-success';
        } else {
            currentShippingZone = 'Cross Province'; 
            zoneEl.className = 'fw-bold text-danger';
        }
        zoneEl.textContent = currentShippingZone;
        updateFinalPrice();
    }
    
    function filterProducts() {
        const q = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(i => {
            i.style.display = i.dataset.name.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    function addToCart() {
        if(typeof Cart !== 'undefined') {
            const price = document.getElementById('finalPriceDisplay').innerText.replace('PKR ','').replace(',','');
            Cart.addItem({
                id: Date.now(),
                productName: currentProduct.baseName,
                basePrice: parseInt(price),
                quantity: document.getElementById('input-standard-qty')?.value || 1,
                options: currentSelectedVariations
            });
        } else {
            alert("Cart system loading...");
        }
    }

    document.addEventListener('DOMContentLoaded', initialAppLoad);    </script>
</body>
</html>

