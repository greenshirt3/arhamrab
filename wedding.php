<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Royal Wedding Collection | Arham Printers</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <?php include 'seo/seo.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    
    <link href="srcs/css/bootstrap.min.css" rel="stylesheet">
    <link href="srcs/lib/animate/animate.min.css" rel="stylesheet">
    <link href="srcs/css/agency.css" rel="stylesheet">

    <style>
        :root {
            --brand-cyan: #0dcaf0;
            --brand-blue: #0f172a;
            --brand-gradient: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
            --glass: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        /* --- Navbar --- */
        .navbar {
            background: var(--glass) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 30px rgba(0,0,0,0.05);
        }
        .navbar-brand h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            color: var(--brand-blue);
            letter-spacing: -1px;
        }

        /* --- Hero --- */
        .royal-hero {
            position: relative;
            height: 60vh; /* Slightly smaller for mobile optimization */
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.7)), url('img/arhamdata/wedding/1555 (1).webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .hero-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 1rem;
            line-height: 1.1;
            color: white;
        }
        .hero-title span {
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- Filter Toolbar --- */
        .filter-toolbar {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 50px;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .form-control-custom:focus {
            border-color: var(--brand-cyan);
            box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
        }

        /* --- Tabs --- */
        .category-scroll {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 2rem;
        }
        .tab-btn {
            background: transparent;
            border: 2px solid #e9ecef;
            color: var(--brand-blue);
            padding: 8px 20px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .tab-btn.active, .tab-btn:hover {
            background: var(--brand-cyan);
            border-color: var(--brand-cyan);
            color: white;
            transform: translateY(-2px);
        }

        /* --- Cards --- */
        .card-perspective { perspective: 1000px; height: 100%; }
        .wedding-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            transform-style: preserve-3d;
            transform: translateZ(0);
            transition: transform 0.3s;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .wedding-card:hover { transform: translateY(-5px); }
        
        .card-img-container {
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        .card-img-main {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .wedding-card:hover .card-img-main { transform: scale(1.1); }
        
        .card-details {
            padding: 15px;
            text-align: center;
            background: white;
            flex-grow: 1;
        }
        .card-title {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: var(--brand-blue);
            font-size: 1.1rem;
        }
        .card-price-badge {
            background: var(--brand-blue);
            color: var(--brand-cyan);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
            margin-top: 5px;
        }

        /* --- Modal --- */
        .modal-content { border-radius: 20px; overflow: hidden; border:none; }
        .thumb-img {
            width: 60px; height: 60px; object-fit: cover;
            cursor: pointer; opacity: 0.6; border-radius: 8px; border: 2px solid transparent;
        }
        .thumb-img:hover, .thumb-img.active { opacity: 1; border-color: var(--brand-cyan); }
        
        .price-tag-large {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            color: var(--brand-cyan);
            font-weight: 800;
        }

        /* --- Buttons --- */
        .btn-brand {
            background: var(--brand-cyan);
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            transition: 0.3s;
        }
        .btn-brand:hover { background: #0bb5d8; color: white; transform: translateY(-2px); }

        /* --- Float WA --- */
        .wa-float {
            position: fixed; bottom: 30px; right: 30px;
            background: #25D366; width: 60px; height: 60px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: white; font-size: 30px; box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4);
            z-index: 999; transition: transform 0.3s;
        }
        .wa-float:hover { transform: scale(1.1) rotate(10deg); color: white; }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <h2 class="m-0">Arham<span style="color: var(--brand-cyan);">Printers</span></h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navC">
                <span class="fas fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navC">
                <div class="navbar-nav ms-auto">
                    <a href="index.php" class="nav-item nav-link fw-bold">Home</a>
                    <a href="#" class="nav-item nav-link active fw-bold" style="color: var(--brand-cyan);">Wedding Cards</a>
                    <a href="#" class="nav-item nav-link fw-bold" onclick="openWA()">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="royal-hero">
        <div class="hero-content animate__animated animate__fadeInUp">
            <h1 class="hero-title">Royal Wedding<br><span>Collection</span></h1>
            <p class="text-white-50">Exclusive Designs • Premium Papers • Custom Printing</p>
            <button class="btn-brand mt-2" onclick="document.getElementById('catalog').scrollIntoView({behavior:'smooth'})">
                Browse
            </button>
        </div>
    </section>

    <section id="catalog" class="py-5">
        <div class="container py-4">
            
            <div class="text-center mb-4">
                <h6 class="text-uppercase fw-bold" style="color: var(--brand-cyan);">Our Collection</h6>
                <h2 class="display-6 fw-bold text-dark">Find Your Perfect Card</h2>
            </div>

            <div class="filter-toolbar animate__animated animate__fadeIn">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6 col-lg-5">
                        <label class="small fw-bold text-muted ms-2 mb-1">Search Card Number/Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="search-input" class="form-control form-control-custom border-start-0 rounded-end-pill" placeholder="e.g. 1555" oninput="applyFilters()">
                        </div>
                    </div>
                    
                    <div class="col-6 col-md-3 col-lg-3">
                        <label class="small fw-bold text-muted ms-2 mb-1">Min Price (PKR)</label>
                        <input type="number" id="price-min" class="form-control form-control-custom" placeholder="0" oninput="applyFilters()">
                    </div>
                    <div class="col-6 col-md-3 col-lg-3">
                        <label class="small fw-bold text-muted ms-2 mb-1">Max Price (PKR)</label>
                        <input type="number" id="price-max" class="form-control form-control-custom" placeholder="Max" oninput="applyFilters()">
                    </div>
                </div>
                <div class="text-end mt-2">
                    <small class="text-muted fst-italic">*Prices shown are estimated per unit for 100 cards.</small>
                </div>
            </div>

            <div class="category-scroll" id="tabs-container">
                </div>

            <div class="row g-4" id="cards-container">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-info" role="status"></div>
                </div>
            </div>

            <div class="text-center mt-5" id="load-more-wrapper" style="display: none;">
                <button class="btn-brand" onclick="loadMoreItems()">
                    Show More Cards <i class="fas fa-arrow-down ms-2"></i>
                </button>
            </div>
            
            <div id="no-results" class="text-center py-5 d-none">
                <h4 class="text-muted">No cards match your search.</h4>
                <button class="btn btn-outline-dark btn-sm rounded-pill mt-2" onclick="resetFilters()">Clear Filters</button>
            </div>
        </div>
    </section>

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="row g-0 h-100">
                    <div class="col-lg-7 bg-light text-center p-3">
                        <img id="modal-main-img" src="" class="img-fluid rounded shadow-sm" style="max-height: 500px; object-fit: contain;">
                        <div class="d-flex justify-content-center gap-2 mt-3" id="modal-thumbs"></div>
                    </div>

                    <div class="col-lg-5 p-4 bg-white d-flex flex-column justify-content-center">
                        <button type="button" class="btn-close ms-auto mb-2" data-bs-dismiss="modal"></button>
                        
                        <h2 id="modal-title" style="font-family: 'Cinzel'; color: var(--brand-blue); font-weight: 700;"></h2>
                        <p id="modal-desc" class="text-muted small mb-4"></p>
                        
                        <div class="p-4 rounded mb-4" style="background: #f0f9ff; border: 1px solid #bae6fd;">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted small"><i class="fas fa-calculator me-2"></i>Price Estimator</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small fw-bold">Quantity (Min 50)</label>
                                    <input type="number" id="calc-qty" class="form-control" value="100" min="50" oninput="calculateModal()">
                                </div>
                                <div class="col-6" id="inner-opt-box">
                                    <label class="small fw-bold">Inner Card</label>
                                    <select id="calc-inner" class="form-select" onchange="calculateModal()">
                                        <option value="no">No</option>
                                        <option value="yes">Yes (+ Cost)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3 text-center pt-2 border-top border-white">
                                <div class="small text-muted">Estimated Total</div>
                                <div id="price-total" class="price-tag-large">PKR 0</div>
                                <div id="price-unit" class="small text-success fw-bold"></div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2">Order Details</h6>
                        <input type="text" id="cust-name" class="form-control mb-2" placeholder="Your Name">
                        <input type="text" id="cust-phone" class="form-control mb-2" placeholder="WhatsApp Number">
                        <select id="ship-method" class="form-select mb-3" onchange="updateShipUI()">
                            <option value="pickup">Instore Pickup (Jalalpur Jattan)</option>
                            <option value="delivery">Home Delivery (Courier)</option>
                        </select>
                        
                        <div id="ship-details" class="mb-3 d-none">
                            <div class="btn-group w-100">
                                <input type="radio" class="btn-check" name="cityBtn" id="cityJPJ" checked onchange="updateShipUI()">
                                <label class="btn btn-outline-info btn-sm" for="cityJPJ">JPJ City</label>
                                <input type="radio" class="btn-check" name="cityBtn" id="cityOther" onchange="updateShipUI()">
                                <label class="btn btn-outline-info btn-sm" for="cityOther">Other City</label>
                            </div>
                            <small id="ship-msg" class="d-block mt-1 text-muted fst-italic"></small>
                        </div>

                        <button class="btn-brand w-100 py-3" onclick="sendOrder()">
                            <i class="fab fa-whatsapp me-2"></i> Confirm Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="#" onclick="openWA()" class="wa-float"><i class="fab fa-whatsapp"></i></a>

    <footer class="bg-dark text-white py-4 text-center mt-5">
        <div class="container">
            <h4 class="mb-2">Arham<span style="color: var(--brand-cyan);">Printers</span></h4>
            <p class="small opacity-50 mb-0">© 2025 Arham Printers. All Rights Reserved. Jalalpur Jattan.</p>
        </div>
    </footer>

    <script src="srcs/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

    <script>
        // --- CONFIG ---
        const JSON_URL = "json/wedding_prices.json";
        const WA_NUM = "923006238233";
        const ITEM_LIMIT = 8;
        
        // --- PRICING LOGIC ---
        const PROFIT_TIERS = [
            { max: 50, mul: 1.20 }, { max: 100, mul: 1.15 }, 
            { max: 300, mul: 1.12 }, { max: Infinity, mul: 1.10 }
        ];
        const PRINT_COSTS = { 50: 20, 100: 12, 300: 8, max: 6 };
        const FIXED_OVERHEAD = 500;
        const INNER_COST = 15;

        // --- STATE ---
        let allItems = []; // Flat array of all cards with calculated prices
        let currentFilteredItems = []; // Items after search/filter
        let visibleCount = 0;
        let activeCard = {};
        let activeCategory = 'All';

        // --- INIT ---
        document.addEventListener('DOMContentLoaded', async () => {
            await fetchData();
        });

        // 1. Fetch & Pre-Calculate Prices for Filtering
        async function fetchData() {
            try {
                const res = await fetch(JSON_URL);
                const json = await res.json();
                const data = json["Special Occasions (Wedding)"];
                
                // Flatten data and Pre-calculate price for 100 cards (standard for filtering)
                allItems = [];
                let categories = ['All'];

                Object.keys(data).forEach(cat => {
                    categories.push(cat);
                    const cards = data[cat].cards;
                    Object.keys(cards).forEach(cName => {
                        let obj = cards[cName];
                        obj._name = cName;
                        obj._cat = cat;
                        // Calculate standard price for sorting/filtering (Qty 100, Inner No)
                        obj._sortPrice = calculateCardPrice(obj, 100, false).unit; 
                        allItems.push(obj);
                    });
                });

                renderTabs(categories);
                applyFilters(); // Initial render

            } catch (e) {
                console.error(e);
                document.getElementById('cards-container').innerHTML = `<div class="col-12 text-center text-danger">Error loading data.</div>`;
            }
        }

        // Helper: Calculate Price (Used for Filter Logic AND Modal)
        function calculateCardPrice(cardObj, qty, hasInner) {
            if(qty < 1) return { total: 0, unit: 0 };

            const base = cardObj.bulkPrice100 / 100;
            let print = PRINT_COSTS.max;
            if(qty <= 50) print = PRINT_COSTS[50];
            else if(qty <= 100) print = PRINT_COSTS[100];
            else if(qty <= 300) print = PRINT_COSTS[300];

            const innerAdd = hasInner ? INNER_COST : 0;
            const rawCost = (base + print + innerAdd) * qty;

            let mul = 1.10;
            for(let t of PROFIT_TIERS) {
                if(qty <= t.max) { mul = t.mul; break; }
            }

            const total = Math.round((rawCost * mul) + FIXED_OVERHEAD);
            const unit = Math.round(total / qty);
            
            return { total, unit };
        }

        function renderTabs(cats) {
            const cont = document.getElementById('tabs-container');
            let html = '';
            cats.forEach(c => {
                html += `<button class="tab-btn ${c==='All'?'active':''}" onclick="changeCategory('${c}', this)">${c}</button>`;
            });
            cont.innerHTML = html;
        }

        function changeCategory(cat, btn) {
            activeCategory = cat;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            if(btn) btn.classList.add('active');
            applyFilters();
        }

        function resetFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('price-min').value = '';
            document.getElementById('price-max').value = '';
            applyFilters();
        }

        // --- MAIN FILTER ENGINE ---
        function applyFilters() {
            const search = document.getElementById('search-input').value.toLowerCase();
            const min = parseInt(document.getElementById('price-min').value) || 0;
            const max = parseInt(document.getElementById('price-max').value) || 99999;

            currentFilteredItems = allItems.filter(item => {
                // 1. Category Check
                if (activeCategory !== 'All' && item._cat !== activeCategory) return false;

                // 2. Search Check (Name)
                if (search && !item._name.toLowerCase().includes(search)) return false;

                // 3. Price Check (Using calculated sortPrice)
                if (item._sortPrice < min || item._sortPrice > max) return false;

                return true;
            });

            visibleCount = 0;
            document.getElementById('cards-container').innerHTML = '';
            document.getElementById('no-results').classList.add('d-none');
            
            renderNextBatch();
        }

        // --- RENDER ---
        function renderNextBatch() {
            const container = document.getElementById('cards-container');
            const btnWrap = document.getElementById('load-more-wrapper');

            if(currentFilteredItems.length === 0) {
                document.getElementById('no-results').classList.remove('d-none');
                btnWrap.style.display = 'none';
                return;
            }

            const nextLimit = visibleCount + ITEM_LIMIT;
            const batch = currentFilteredItems.slice(visibleCount, nextLimit);

            batch.forEach(item => {
                let img = 'img/arhamdata/wedding/placeholder.webp';
                if(item.galleryImages && item.galleryImages.length > 0) img = item.galleryImages[0];
                else if(item.imageFile) img = item.imageFile;

                const safeStr = encodeURIComponent(JSON.stringify(item));

                const html = `
                    <div class="col-6 col-md-4 col-lg-3 card-perspective animate__animated animate__fadeInUp">
                        <div class="wedding-card" onclick="openModal('${safeStr}')">
                            <div class="card-img-container">
                                <img src="${img}" class="card-img-main" loading="lazy">
                            </div>
                            <div class="card-details">
                                <div class="card-title">${item._name}</div>
                                <div class="small text-muted mb-2">${item._cat}</div>
                                <div class="card-price-badge">
                                    ~PKR ${item._sortPrice} <span style="font-size:0.7em; font-weight:400;">/card</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            });

            visibleCount = nextLimit;
            
            // Tilt Effect
            if(typeof VanillaTilt !== 'undefined') {
                VanillaTilt.init(document.querySelectorAll(".wedding-card"), { max: 10, speed: 400 });
            }

            btnWrap.style.display = (visibleCount >= currentFilteredItems.length) ? 'none' : 'block';
        }

        function loadMoreItems() { renderNextBatch(); }

        // --- MODAL ---
        function openModal(str) {
            activeCard = JSON.parse(decodeURIComponent(str));
            
            document.getElementById('modal-title').innerText = activeCard._name;
            document.getElementById('modal-desc').innerText = activeCard.description || "Premium Wedding Invitation";

            // Images
            const mainImg = document.getElementById('modal-main-img');
            const thumbCont = document.getElementById('modal-thumbs');
            thumbCont.innerHTML = '';

            let images = [];
            if(activeCard.galleryImages && activeCard.galleryImages.length) images = activeCard.galleryImages;
            else if(activeCard.imageFile) images = [activeCard.imageFile];
            
            mainImg.src = images[0] || '';

            if(images.length > 1) {
                images.forEach((src, idx) => {
                    let img = document.createElement('img');
                    img.src = src;
                    img.className = `thumb-img ${idx===0 ? 'active':''}`;
                    img.onclick = () => {
                        mainImg.src = src;
                        document.querySelectorAll('.thumb-img').forEach(t => t.classList.remove('active'));
                        img.classList.add('active');
                    };
                    thumbCont.appendChild(img);
                });
            }

            document.getElementById('inner-opt-box').style.visibility = activeCard.hasInnerCardOption ? 'visible' : 'hidden';
            
            // Defaults
            document.getElementById('calc-qty').value = 100;
            document.getElementById('calc-inner').value = 'no';
            calculateModal();
            updateShipUI();

            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        function calculateModal() {
            const qty = parseInt(document.getElementById('calc-qty').value) || 0;
            const inner = document.getElementById('calc-inner').value === 'yes';
            
            // Use the shared calc function
            const res = calculateCardPrice(activeCard, qty, inner);
            
            document.getElementById('price-total').innerText = "PKR " + res.total.toLocaleString();
            document.getElementById('price-unit').innerText = `(${res.unit} per card)`;
            activeCard._finalTotal = res.total;
        }

        function updateShipUI() {
            const method = document.getElementById('ship-method').value;
            const isJPJ = document.getElementById('cityJPJ').checked;
            const detailBox = document.getElementById('ship-details');
            
            if(method === 'pickup') detailBox.classList.add('d-none');
            else {
                detailBox.classList.remove('d-none');
                document.getElementById('ship-msg').innerHTML = isJPJ ? "Shipping: <b>PKR 100</b> (added later)" : "Shipping calculated by weight/city.";
            }
        }

        function sendOrder() {
            const name = document.getElementById('cust-name').value;
            const phone = document.getElementById('cust-phone').value;
            const qty = document.getElementById('calc-qty').value;
            const inner = document.getElementById('calc-inner').value;
            const method = document.getElementById('ship-method').value;
            const isJPJ = document.getElementById('cityJPJ').checked;

            if(!name || !phone) { alert("Please enter Name & Phone."); return; }

            let shipText = method === 'pickup' ? "Instore Pickup" : (isJPJ ? "Delivery (JPJ)" : "Delivery (Other)");

            let txt = `*WEDDING CARD ORDER*\n`;
            txt += `Design: ${activeCard._name} (${activeCard._cat})\n`;
            txt += `Qty: ${qty} | Inner: ${inner}\n`;
            txt += `Est. Total: PKR ${activeCard._finalTotal}\n`;
            txt += `----------------\n`;
            txt += `Name: ${name}\n`;
            txt += `Phone: ${phone}\n`;
            txt += `Ship: ${shipText}`;

            window.open(`https://wa.me/${WA_NUM}?text=${encodeURIComponent(txt)}`, '_blank');
        }

        function openWA() { window.open(`https://wa.me/${WA_NUM}`, '_blank'); }
    </script>
</body>
</html>