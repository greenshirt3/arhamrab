<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Arham Printers - Wedding Cards</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        :root { --bs-primary: #f28b00; --bs-secondary: #ff5722; --bs-dark: #212529; }
        .section { display: none; }
        .section.active { display: block; }
        .final-price-box { background: var(--bs-primary); color: white; padding: 1.5rem; display: none; margin-top: 20px; }
        .choice-card { cursor: pointer; border: 2px solid #ddd; padding: 10px; text-align: center; flex: 1; }
        .choice-card.selected { background: var(--bs-primary); color: white; border-color: var(--bs-primary); }
        .choice-card input { display: none; }
        body { padding-bottom: 80px; } /* Space for mobile nav */
        
        #mobile-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; height: 60px; background: white; border-top: 1px solid #ddd; z-index: 1030; display: flex; align-items: center; justify-content: space-around; }
        .mobile-nav-link { flex: 1; text-align: center; color: var(--bs-dark); text-decoration: none; font-size: 0.7rem; }
        .mobile-nav-link.active { color: var(--bs-primary); }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 d-none d-lg-block">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <a href="index.php" class="navbar-brand d-flex align-items-center me-4">
                <img src="img/logo2.webp" alt="Arham Printers" style="height: 40px; margin-right: 1rem;">
            </a>
            <div class="collapse navbar-collapse flex-grow-1" id="navbarCollapse">
                <div class="navbar-nav me-auto bg-light pe-4 py-3 py-lg-0 d-flex align-items-center">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="products.php" class="nav-item nav-link">Products</a>
                    <a href="#" class="nav-item nav-link active" onclick="showSection('shop-catalog')">Wedding Cards</a>
                    <a href="prints.php" class="nav-item nav-link">Paper Printing</a>
                </div>
            </div>
        </div>
    </nav>

    <nav id="mobile-bottom-nav" class="d-lg-none">
        <a href="index.php" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="products.php" class="mobile-nav-link"><i class="fas fa-th-list"></i> Products</a>
        <a href="#" class="mobile-nav-link active"><i class="fas fa-gift"></i> Wedding</a>
        <a href="prints.php" class="mobile-nav-link"><i class="fas fa-print"></i> Prints</a>
    </nav>

    <section id="shop-catalog-section" class="section active">
        <div class="container py-5">
            <h2 class="text-center mb-5">Wedding Card Collection</h2>
            <div class="row g-4" id="wedding-card-grid">
                </div>
        </div>
    </section>

    <section id="product-detail-section" class="section">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-6">
                    <img id="detail-card-image" src="" class="img-fluid w-100">
                </div>
                <div class="col-lg-6">
                    <h2 id="detail-card-name">Card Name</h2>
                    
                    <div class="product-option-group mt-4">
                        <label>Quantity (Pieces):</label>
                        <input type="number" id="input-card-qty" class="form-control" value="100" min="1" oninput="updatePrice()">
                    </div>
                    
                    <div class="product-option-group mt-3" id="inner-option-group" style="display:none;">
                        <label>Include Inner Card?</label>
                        <div class="d-flex gap-3">
                            <label class="choice-card" onclick="setInner(true); this.classList.add('selected'); this.nextElementSibling.classList.remove('selected');">
                                <input type="radio"> Yes
                            </label>
                            <label class="choice-card selected" onclick="setInner(false); this.classList.add('selected'); this.previousElementSibling.classList.remove('selected');">
                                <input type="radio"> No
                            </label>
                        </div>
                    </div>

                    <div class="final-price-box" id="finalPriceBox" style="display:block;">
                        <h4>Total Price</h4>
                        <h3 id="finalPriceDisplay">PKR 0</h3>
                        <button class="btn btn-success w-100 mt-2" onclick="addToCart()">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cart-manager.js"></script>

    <script>
    const PUBLIC_DATA_URL = 'all_prices.json'; 
    let allCards = {};
    let currentCard = null;
    let hasInner = false;

    // --- LOGIC: Cost Constants from your old file ---
    const BULK_QTY = 100;
    const INNER_COST_PER_CARD = 10;
    const FIXED_OVERHEAD = 500;
    const PROFIT_TIERS = [
        { min: 1, max: 50, multiplier: 1.15 },    
        { min: 51, max: 100, multiplier: 1.12 },   
        { min: 101, max: 300, multiplier: 1.10 },  
        { min: 301, max: Infinity, multiplier: 1.09 }
    ];

    function getPrintingChargePerCard(qty) {
        if(qty <= 50) return 18;
        if(qty <= 100) return 10;
        if(qty <= 300) return 8;
        return 6;
    }

    async function init() {
        try {
            const res = await fetch(PUBLIC_DATA_URL);
            const data = await res.json();
            const weddingData = data['Special Occasions (Wedding)'];
            
            // Render Cards
            const grid = document.getElementById('wedding-card-grid');
            let html = '';
            
            Object.keys(weddingData).forEach(cat => {
                const cards = weddingData[cat].cards;
                Object.keys(cards).forEach(name => {
                    const c = cards[name];
                    allCards[name] = { ...c, name: name };
                    
                    html += `
                    <div class="col-lg-3 col-6">
                        <div class="card shadow-sm border-0">
                            <img src="${c.imageFile}" class="card-img-top">
                            <div class="card-body text-center">
                                <h5>${name}</h5>
                                <button class="btn btn-primary btn-sm" onclick="showDetails('${name}')">Details</button>
                            </div>
                        </div>
                    </div>`;
                });
            });
            grid.innerHTML = html;
        } catch(e) { console.error(e); }
    }

    function showDetails(name) {
        currentCard = allCards[name];
        document.getElementById('detail-card-name').textContent = name;
        document.getElementById('detail-card-image').src = currentCard.imageFile;
        
        // Inner Card Logic
        const innerGroup = document.getElementById('inner-option-group');
        innerGroup.style.display = currentCard.hasInnerCardOption ? 'block' : 'none';
        hasInner = false; // Reset
        
        document.getElementById('shop-catalog-section').classList.remove('active');
        document.getElementById('product-detail-section').classList.add('active');
        updatePrice();
    }

    function updatePrice() {
        const qty = parseInt(document.getElementById('input-card-qty').value) || 0;
        if(qty < 1) return;

        // --- CORE LOGIC from index (2).php ---
        const printingCharge = getPrintingChargePerCard(qty);
        // Reverse engineer unit cost from bulk price
        // (BulkPrice100 - Fixed) / 100 = Raw Unit Cost roughly
        const rawUnitCost = (currentCard.bulkPrice100 - FIXED_OVERHEAD) / 100; 
        
        let totalCost = (rawUnitCost + printingCharge) * qty;
        totalCost += FIXED_OVERHEAD;
        if(hasInner) totalCost += (INNER_COST_PER_CARD * qty);

        // Apply Profit Multiplier
        let mult = 1.09;
        PROFIT_TIERS.forEach(t => {
            if(qty >= t.min && qty <= t.max) mult = t.multiplier;
        });

        const finalPrice = Math.round(totalCost * mult);
        document.getElementById('finalPriceDisplay').textContent = `PKR ${finalPrice.toLocaleString()}`;
    }

    function setInner(val) {
        hasInner = val;
        updatePrice();
    }
    
    function showSection(id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.getElementById(id + '-section').classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>