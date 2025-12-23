<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Arham Printers - Product Catalog</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Printing Services, Business Cards, Stationery, Marketing Materials, Full Catalog" name="keywords">
    <meta content="Explore the full range of professional printing services and product catalog from Arham Printers." name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary: #f28b00; 
            --bs-secondary: #ff5722; 
            --bs-dark: #212529;
            --mobile-nav-height: 60px;
        }
        .section { display: none; }
        .section.active { display: block; }
        
        .product-footer-overlay button {
            text-align: center;
            padding: 10px 15px !important;
            line-height: 1.2;
            font-size: 0.85rem;
        }
        .final-price-box {
            background: var(--bs-primary);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
            text-align: center;
            display: none; 
        }
        .final-price-box h4 { color: white; }
        
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url(img/banners/productdesk.webp) center center no-repeat;
            background-size: cover;
            min-height: 250px;
        }
        @media (min-width: 768px) {
            .page-header {
                background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url(img/banners/productdesk.webp) center center no-repeat;
                background-size: cover;
                min-height: 350px;
            }
        }
        
        .choice-card {
            cursor: pointer;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            text-align: center;
            transition: all 0.2s ease;
            flex-grow: 1;
            min-width: 120px;
        }
        .choice-card:hover { border-color: var(--bs-secondary); background-color: #fff8f8; }
        .choice-card.selected { border-color: var(--bs-primary); background-color: var(--bs-primary); color: white; box-shadow: 0 0 10px rgba(242, 139, 0, 0.3); }
        .choice-card.disabled { opacity: 0.5; pointer-events: none; border-color: #eee; background-color: #f7f7f7; }
        .choice-card input { display: none; }
        .choice-card .label-text { font-weight: 500; color: var(--bs-dark); display: block; }
        .choice-card.selected .label-text { color: white; }
        
        .product-option-group { margin-bottom: 1.5rem; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; }
        .product-option-group label { font-weight: 600; color: var(--bs-dark); margin-bottom: 0.75rem; display: block; font-size: 1.1rem; }
        
        .search-wrapper { display: flex; border: 2px solid var(--bs-primary); border-radius: 50px; overflow: hidden; max-width: 100%; height: 50px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        .search-input { flex-grow: 1; border: none !important; padding: 0 20px; font-size: 1rem; height: 100%; }
        .search-input:focus { box-shadow: none !important; }
        .search-button { width: 60px; height: 100%; display: flex; align-items: center; justify-content: center; background-color: var(--bs-primary); border: none; color: white; }
        
        #mobile-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; height: var(--mobile-nav-height); background: white; border-top: 1px solid #ddd; box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1); z-index: 1030; display: flex; align-items: center; justify-content: space-around; }
        .mobile-nav-link { flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center; text-decoration: none; color: var(--bs-dark); font-size: 0.6rem; padding: 5px 0; }
        .mobile-nav-link.active { color: var(--bs-primary); }
        .mobile-nav-link i { font-size: 1.1rem; margin-bottom: 2px; }
        
        /* Padding for fixed bottom nav */
        #shop-catalog-section, #product-detail-section, #quote-section, #contact-section, #cart-section, #checkout-section { padding-bottom: 80px; }
    </style>
</head>

<body>
    <div class="container-fluid bg-dark text-white-50 py-2 px-0 d-none d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-7 px-5 text-start">
                <small class="fa fa-phone-alt me-2"></small> <small>+92 300 6238233</small>
                <small class="far fa-envelope-open me-2 ms-4"></small> <small>info@arhamprinters.pk</small>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <a class="text-white-50 ms-4" href=""><i class="fab fa-facebook-f"></i></a>
                <a class="text-white-50 ms-4" href=""><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 d-none d-lg-block">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <a href="index.php" class="navbar-brand d-flex align-items-center me-4">
                <img src="img/logo2.webp" alt="Arham Printers" style="height: 40px; margin-right: 1rem;">
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-1" id="navbarCollapse">
                <div class="navbar-nav me-auto bg-light pe-4 py-3 py-lg-0 d-flex align-items-center">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="#" class="nav-item nav-link active" onclick="showSection('shop-catalog')">Products</a>
                    <a href="wedding.php" class="nav-item nav-link">Wedding Cards</a>
                    <a href="prints.php" class="nav-item nav-link">Paper Printing</a>
                    <a href="#" onclick="showSection('contact')" class="nav-item nav-link">Contact</a>
                </div>
                <div class="h-100 d-lg-inline-flex align-items-center">
                    <a class="btn-sm-square bg-white text-dark rounded-circle me-4 position-relative" href="#" onclick="showSection('cart')">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="desktop-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.6em; padding: 4px;">0</span>
                    </a>
                    <a href="#" onclick="showSection('quote')" class="btn btn-primary py-2 px-3 rounded-0 d-none d-lg-block">Get a Quote</a>
                </div>
            </div>
        </div>
    </nav>

    <nav id="mobile-bottom-nav" class="d-lg-none">
        <a href="index.php" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="#" class="mobile-nav-link active" onclick="showSection('shop-catalog')"><i class="fas fa-th-list"></i> Products</a>
        <a href="wedding.php" class="mobile-nav-link"><i class="fas fa-gift"></i> Wedding</a>
        <a href="prints.php" class="mobile-nav-link"><i class="fas fa-print"></i> Prints</a>
        <a href="#" class="mobile-nav-link" onclick="showSection('cart')">
            <i class="fas fa-shopping-cart"></i>
            <span id="cart-count" class="badge rounded-pill bg-primary" style="position: absolute; top: 5px; right: 10px; font-size: 0.6em;">0</span>
            Cart
        </a>
    </nav>

    <section id="shop-catalog-section" class="section active">
        <div class="container-fluid page-header py-5">
            <div class="text-center"><h2 class="bg-white px-3 py-2 d-inline-block rounded-1">Products & Services</h2></div>
        </div>  
        <div class="container py-5">
            <div class="search-wrapper mb-4">
                <input type="text" id="search-input" class="form-control search-input" placeholder="Search products..." onkeyup="filterProducts()">
                <button class="search-button"><i class="fa fa-search"></i></button>
            </div>
            <div id="search-results-message" class="alert alert-info" style="display:none;"></div>
            <div class="row g-4" id="main-product-catalog"></div>
        </div>
    </section>

    <section id="product-detail-section" class="section">
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-dark display-6" id="detail-product-name-header">Product Details</h1>
        </div>
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="d-flex justify-content-center">
                        <img id="detail-product-image" src="img/products/placeholder.png" class="img-fluid rounded" style="max-height: 400px;">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4" id="detail-product-name-body">Product Name</h2>
                    
                    <div id="custom-area-inputs" style="display: none;">
                        <div class="product-option-group">
                            <label>Dimensions (Feet):</label>
                            <div class="d-flex gap-3">
                                <input type="number" id="input-width" min="1" placeholder="Width" class="form-control" oninput="updateFinalPrice()">
                                <span class="align-self-center">x</span>
                                <input type="number" id="input-height" min="1" placeholder="Height" class="form-control" oninput="updateFinalPrice()">
                            </div>
                            <small class="text-muted mt-2 d-block" id="areaDisplay"></small>
                        </div>
                        <div class="product-option-group">
                            <label>Quantity:</label>
                            <input type="number" id="input-custom-qty" min="1" value="1" class="form-control" oninput="updateFinalPrice()">
                        </div>
                        <div class="alert alert-danger mt-3" id="areaConstraintWarning" style="display: none;"></div>
                    </div>

                    <div id="standard-options-fields"></div>
                    
                    <div class="product-option-group mt-4">
                        <label><i class="fas fa-truck me-2"></i> Delivery Method:</label>
                        <div class="d-flex flex-wrap gap-3">
                            <label class="choice-card selected" id="delivery-home" onclick="setDeliveryType('HomeDelivery')">
                                <input type="radio" name="option-DeliveryType" value="HomeDelivery" checked> Home Delivery
                            </label>
                            <label class="choice-card" id="delivery-pickup" onclick="setDeliveryType('SelfPickUp')">
                                <input type="radio" name="option-DeliveryType" value="SelfPickUp"> Self Pick Up
                            </label>
                        </div>
                    </div>
                    <div class="product-option-group" id="shipping-zone-selector">
                        <label>Shipping Zone:</label>
                        <div class="d-flex gap-2 mb-2">
                            <input type="text" id="input-city" placeholder="Enter City Name" class="form-control" oninput="manualLocationCheck()">
                        </div>
                        <small class="text-muted" id="current-location-display">Zone: <span id="zone-name" class="fw-bold text-success">Jalalpur Jattan (Default)</span></small>
                    </div>

                    <div class="final-price-box" id="finalPriceBox">
                        <h4 class="mb-2">Total Price</h4>
                        <h3 class="display-6 fw-bold" id="finalPriceDisplay">PKR 0</h3>
                        <div class="alert alert-light mt-3 p-3 text-dark" id="shipping-display-box">
                            <h5 class="mb-1 text-success">Shipping: <span id="shipping-cost-display">PKR 0</span></h5>
                            <small id="shipping-note-display"></small>
                        </div>
                        <button class="btn btn-success py-2 px-4 w-100 mt-3" onclick="addItemToCart()"><i class="fas fa-cart-plus me-2"></i> Add to Cart</button>
                    </div>
                    
                    <div class="alert alert-info mt-3" id="selectionWarning">Please select options to see price.</div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cart-manager.js"></script> 
    
    <script>
    const PUBLIC_DATA_URL = 'all_prices.json'; // Linked to root file
    const WHATSAPP_NUMBER = '923006238233';
    
    let allProductGroups = {};
    let currentProduct = null;
    let currentSelectedVariations = {};
    let customprintspricing = {};
    let currentShippingZone = 'Within City'; 
    let currentDeliveryType = 'HomeDelivery';

    // --- LOGIC: Shipping Rates ---
    const SHIPPING_RATES = {
        'small': { 'Within City': 100, 'Same Province': 347, 'Cross Province': 359 },
        'medium': { 'Within City': 100, 'Same Province': 529, 'Cross Province': 541 },
        'overland': { 'Within City': 150, 'Same Province': 1254, 'Cross Province': 1254 }
    };

    // --- LOGIC: Area Based Meta Data (For Banners/Wallpapers) ---
    const AREA_BASED_META = {
        "China": { rate: 35, min_area: 2, category: 'Banners' },
        "Star": { rate: 65, min_area: 2, category: 'Banners' },
        "Backlit": { rate: 100, min_area: 2, category: 'Banners' },
        "One Vision": { rate: 90, min_area: 2, category: 'Banners' }, 
        "Venyl Sticker": { rate: 90, min_area: 2, category: 'Banners' }, 
        "3D Wallpaper": { rate: 30, min_area: 20, category: '3D Wallpaper' }
    };
    const AREA_BASED_PRODUCT_NAMES = Object.keys(AREA_BASED_META);

    // --- LOGIC: Design Fees ---
    const DESIGN_FEE_TIERS = [
        { min_sqft: 1, max_sqft: 10, base_fee: 300 },
        { min_sqft: 11, max_sqft: 24, base_fee: 400 },
        { min_sqft: 25, max_sqft: 50, base_fee: 500 },
        { min_sqft: 51, max_sqft: Infinity, base_fee: 700 },
    ];

    // --- LOGIC: Profit Multipliers ---
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
            // Fallback UI or Alert
            document.getElementById('main-product-catalog').innerHTML = '<p class="alert alert-danger">Failed to load products. Check all_prices.json.</p>';
        }
    }

    function initialProductSetup(flatData) {
        Object.keys(flatData).forEach(catKey => {
            if (catKey === "B/W and Color Prints") {
                customprintspricing["Paper Prints"] = flatData[catKey];
                allProductGroups["Paper Prints"] = { baseName: "Paper Prints", imageFile: 'printer', mainCategory: catKey, isDigitalTiered: true };
                return;
            }
            if (catKey === "Special Occasions (Wedding)") return; // Handled in wedding.php

            const catData = flatData[catKey];
            Object.keys(catData).forEach(prodKey => {
                const data = catData[prodKey];
                let isArea = AREA_BASED_PRODUCT_NAMES.includes(prodKey);
                let img = 'placeholder';
                
                // Image Logic
                if (data.imageFile) img = data.imageFile;
                else if (Array.isArray(data) && data[0].imageFile) img = data[0].imageFile;
                
                // Clean Image Path (remove extension/folder)
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
        // Group by category
        const categories = [...new Set(Object.values(allProductGroups).map(p => p.mainCategory))];
        categories.forEach(cat => {
            html += `<h3 class="w-100 pt-4 border-bottom pb-2">${cat}</h3>`;
            Object.values(allProductGroups).filter(p => p.mainCategory === cat).forEach(p => {
                html += `
                <div class='col-lg-3 col-6 mb-4 product-item' data-name="${p.baseName}">
                    <div class='card h-100 shadow-sm border-0'>   
                        <img src='img/products/${p.imageFile}.webp' onerror="this.src='img/products/placeholder.png'" class='w-100'>
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
        document.getElementById('detail-product-image').src = `img/products/${currentProduct.imageFile}.png`;
        
        const areaFields = document.getElementById('custom-area-inputs');
        const stdFields = document.getElementById('standard-options-fields');
        
        areaFields.style.display = currentProduct.isAreaBased ? 'block' : 'none';
        stdFields.innerHTML = ''; // Reset

        if (currentProduct.isDigitalTiered) {
            // Render digital print options (Size, Color, GSM)
            renderDigitalOptions();
        } else if (currentProduct.isBundleTiered) {
            // Render Bundle Radios
            let h = `<div class="product-option-group"><label>Select Bundle:</label><div class="d-flex flex-wrap gap-2">`;
            Object.keys(currentProduct.bundles).forEach(b => {
                h += `<label class="choice-card"><input type="radio" name="bundle" value="${b}" onclick="selectBundle('${b}')">${b}</label>`;
            });
            stdFields.innerHTML = h + `</div></div><div class="product-option-group"><label>Qty:</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
        } else if (!currentProduct.isAreaBased) {
            // Render Standard Combinations
            const keys = Object.keys(currentProduct.combinations[0]).filter(k => k!=='Price' && k!=='FullName' && k!=='imageFile');
            keys.forEach(k => {
                stdFields.innerHTML += `<div class="product-option-group"><label>${k}:</label><div class="d-flex flex-wrap gap-2" id="choices-${k}"></div></div>`;
            });
            stdFields.innerHTML += `<div class="product-option-group"><label>Qty:</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
            
            // Populate first attribute
            populateOptions(keys[0]); 
        }
        
        showSection('product-detail');
        updateFinalPrice();
    }

    // --- HELPER: Calculation Logic ---
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
            const designFee = calculateDesignFee(area, qty); // Uses Logic Array
            const multiplier = getAreaProfitMultiplier(qty); // Uses Logic Array
            
            price = ((area * rate * qty) + designFee) * multiplier;
        } 
        else if (currentProduct.combinations.length) {
            // Find matching combination
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
        const warn = document.getElementById('selectionWarning');
        
        if(res.error) {
            box.style.display = 'none';
            warn.style.display = 'block';
        } else {
            // Add Shipping
            let shipping = 0;
            const pktType = getShippingPacketType(res.productName);
            if(currentDeliveryType !== 'SelfPickUp') {
                shipping = SHIPPING_RATES[pktType][currentShippingZone] || 350;
            }
            
            document.getElementById('finalPriceDisplay').textContent = `PKR ${(res.basePrice + shipping).toLocaleString()}`;
            document.getElementById('shipping-cost-display').textContent = `PKR ${shipping}`;
            box.style.display = 'block';
            warn.style.display = 'none';
        }
    }

    function getShippingPacketType(name) {
        if(AREA_BASED_PRODUCT_NAMES.includes(name)) return 'overland';
        if(name.includes('Mug') || name.includes('Book')) return 'medium';
        return 'small';
    }

    // --- HELPER: Navigation ---
    function showSection(id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        const el = document.getElementById(id + '-section');
        if(el) el.classList.add('active');
        window.scrollTo(0,0);
    }
    
    function manualLocationCheck() {
        const val = document.getElementById('input-city').value.toLowerCase();
        const zoneEl = document.getElementById('zone-name');
        if(val.includes('jalalpur') || val.includes('jattan')) {
            currentShippingZone = 'Within City';
            zoneEl.className = 'fw-bold text-success';
        } else {
            currentShippingZone = 'Cross Province'; // Simplified fallback
            zoneEl.className = 'fw-bold text-danger';
        }
        zoneEl.textContent = currentShippingZone;
        updateFinalPrice();
    }

    document.addEventListener('DOMContentLoaded', initialAppLoad);
    </script>
</body>
</html>