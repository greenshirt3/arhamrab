<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'seo/seo.php'; ?>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="srcs/css/bootstrap.min.css" rel="stylesheet">
    <link href="srcs/css/all.min.css" rel="stylesheet">
    <link href="srcs/lib/animate/animate.min.css" rel="stylesheet">
    <link href="srcs/css/agency.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a href="#" class="navbar-brand">
                <h2 class="m-0">Arham<span style="color: var(--brand-cyan);">Printers</span></h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fas fa-bars text-dark"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto">
                    <a href="#home" class="nav-item nav-link active">Home</a>
                    <a href="#catalog" class="nav-item nav-link">Catalog</a>
                    <a href="#" class="nav-item nav-link" onclick="openGeneralContact()">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <div id="home" class="hero-header">
        <div class="container">
            <h4 class="hero-subtitle text-white-50 animate__animated animate__fadeInDown">EST. 2022 • JALALPUR JATTAN</h4>
            <h1 class="hero-title text-white animate__animated animate__fadeInUp">WE DESIGN YOUR<br>BRAND LEGACY</h1>
            <p class="lead text-white-50 mb-5 animate__animated animate__fadeInUp">Offset Printing • Large Format • Digital Solutions</p>
            <a href="#catalog" class="hero-btn animate__animated animate__fadeInUp">Browse Collection</a>
        </div>
    </div>

    <div id="catalog" class="catalog-section">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase fw-bold">Our Collection</h6>
                <h2 class="display-5 fw-bold text-dark">Product Showcase</h2>
            </div>

            <div class="d-flex justify-content-center flex-wrap mb-5" id="category-tabs">
                <button class="tab-btn active" onclick="setFilter('all', this)">All Products</button>
                <button class="tab-btn" onclick="setFilter('Business Essentials', this)">Business</button>
                <button class="tab-btn" onclick="setFilter('Marketing Materials', this)">Marketing</button>
                <button class="tab-btn" onclick="setFilter('Wedding & Events', this)">Wedding</button>
                <button class="tab-btn" onclick="setFilter('Gifts & Custom', this)">Gifts</button>
            </div>

            <div class="row g-4" id="product-grid">
                </div>

            <div class="text-center mt-5" id="load-more-container" style="display: none;">
                <button class="hero-btn" onclick="showAllItems()">View All Products</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="modalTitle">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <img id="modalImg" src="" class="detail-img">
                        </div>
                        <div class="col-md-6">
                            <h3 id="prodName" style="color: var(--brand-blue); font-weight: 800;"></h3>
                            <p id="prodCat" class="text-muted text-uppercase small mb-3"></p>
                            
                            <label class="fw-bold">Select Variation:</label>
                            <select id="modalVariation" class="form-select mb-3"></select>

                            <hr>

                            <h5 class="fw-bold text-dark">Confirm Checkout</h5>
                            <input type="text" id="cName" class="form-control mb-2" placeholder="Your Name" required>
                            <input type="text" id="cPhone" class="form-control mb-3" placeholder="Phone Number" required>

                            <div class="payment-note">
                                <i class="fas fa-info-circle me-1"></i> Order preparation will be done after full payment.
                            </div>

                            <label class="fw-bold">Shipping & Collection:</label>
                            <select id="deliveryType" class="form-select mb-2" onchange="updateShipping()">
                                <option value="pickup">Instore Pick (Self Collection)</option>
                                <option value="delivery">Home Delivery</option>
                            </select>

                            <div id="locationTabs" style="display: none;" class="mb-2">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="locBtn" id="locJPJ" checked onchange="updateShipping()">
                                    <label class="btn btn-outline-info" for="locJPJ">Jalalpur Jattan</label>

                                    <input type="radio" class="btn-check" name="locBtn" id="locOther" onchange="updateShipping()">
                                    <label class="btn btn-outline-info" for="locOther">Other City</label>
                                </div>
                            </div>

                            <div id="shippingMsg" class="shipping-msg"></div>

                            <button class="hero-btn w-100 mt-4" onclick="submitOrder()">Confirm Order <i class="fab fa-whatsapp ms-2"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="footer-logo">Arham Printers</a>
                    <p class="mb-4">Your #1 source for premium printing & advertising in Jalalpur Jattan.</p>
                    
                    <div class="mb-4">
                        <h6 class="text-white mb-2" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Opening Hours</h6>
                        <p class="mb-1 text-white-50"><i class="fas fa-clock me-2 text-info"></i> Sat - Thu: 09:00 AM - 08:00 PM</p>
                        <p class="text-danger"><i class="fas fa-times-circle me-2"></i> Friday: Closed</p>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/arhamprinters.pk"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5>Shop</h5>
                    <a href="##catalog">All Products</a>
                    <a href="##catalog">Business Cards</a>
                    <a href="##catalog">Marketing</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Support</h5>
                    <a href="shipping_policy.php">Shipping Policy</a>
                    <a href="return_policy.php">Return Policy</a>
                    <a href="#" onclick="openGeneralContact()">Help Center</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-3 d-flex align-items-center">
                            <a href="https://maps.app.goo.gl/r7peyma84JdVSecPA" target="_blank" class="me-3 text-decoration-none">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                </div>
                            </a>
                            <span class="text-white-50">Tanda Road, Jalalpur Jattan.</span>
                        </li>

                        <li class="mb-3 d-flex align-items-center">
                            <a href="tel:+923006238233" class="me-3 text-decoration-none">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-phone-alt text-success"></i>
                                </div>
                            </a>
                            <span class="text-white-50">+92 300 6238233</span>
                        </li>

                        <li class="d-flex align-items-center">
                            <a href="mailto:info@arhamprinters.pk" class="me-3 text-decoration-none">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                            </a>
                            <span class="text-white-50">info@arhamprinters.pk</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="m-0">© 2025 Arham Printers. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <a href="#" onclick="openGeneralContact()" class="whatsapp-float">
        <i class="fab fa-whatsapp fa-lg"></i> Chat
    </a>

    <script src="srcs/js/bootstrap.bundle.min.js"></script>
    <script src="srcs/js/wow.min.js"></script>
    
    <script>
        const WHATSAPP_NUMBER = "923006238233";
        let allProducts = {};
        let currentFilter = 'all';
        let isExpanded = false;
        let currentProdData = {};

        document.addEventListener('DOMContentLoaded', async () => {
            // Initialize WOW locally
            if(typeof WOW !== 'undefined') new WOW().init();
            
            try {
                const response = await fetch('json/all_prices2.json');
                if(!response.ok) throw new Error("JSON 404");
                allProducts = await response.json();
                renderProducts();
            } catch (err) { console.error("Error loading products:", err); }
        });

        function renderProducts() {
            const container = document.getElementById('product-grid');
            const loadBtnDiv = document.getElementById('load-more-container');
            container.innerHTML = '';
            let itemsToRender = [];

            for (const [catName, products] of Object.entries(allProducts)) {
                if (currentFilter !== 'all' && catName !== currentFilter) continue;
                const entries = Array.isArray(products) ? products.entries() : Object.entries(products);

                for (const [prodName, variants] of entries) {
                    let vList = Array.isArray(variants) ? variants : [variants];
                    let img = vList[0].imageFile || 'img/arhamdata/arhamproducts/placeholder.webp';
                    let displayName = typeof prodName === 'string' ? prodName : "Product";
                    
                    let safeData = encodeURIComponent(JSON.stringify({name: displayName, cat: catName, img: img, variants: vList}));

                    let loadingAttr = itemsToRender.length < 4 ? 'eager' : 'lazy';

                    let html = `
                        <div class="col-6 col-md-4 col-lg-3 wow fadeInUp">
                            <div class="product-card">
                                <div class="card-header-blue">${displayName}</div>
                                <div class="prod-img-wrap">
                                    <img src="${img}" class="prod-img" loading="${loadingAttr}">
                                </div>
                                <div class="card-footer-area">
                                    <button class="btn-view-details" onclick="openModal('${safeData}')">
                                        View Details <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    itemsToRender.push(html);
                }
            }

            let visibleItems = isExpanded ? itemsToRender : itemsToRender.slice(0, 4);
            container.innerHTML = visibleItems.join('');
            loadBtnDiv.style.display = (itemsToRender.length > 4 && !isExpanded) ? 'block' : 'none';
        }

        function openModal(dataString) {
            const data = JSON.parse(decodeURIComponent(dataString));
            currentProdData = data;

            document.getElementById('modalImg').src = data.img;
            document.getElementById('prodName').innerText = data.name;
            document.getElementById('prodCat').innerText = data.cat;

            const sel = document.getElementById('modalVariation');
            sel.innerHTML = '';
            data.variants.forEach(v => {
                let parts = [];
                if(v.variant_1) parts.push(v.variant_1);
                if(v.variant_3) parts.push(v.variant_3);
                if(v.single_side) parts.push(v.single_side);
                if(v.finish) parts.push(v.finish);
                let txt = parts.join(' - ') || 'Standard';
                let opt = document.createElement('option');
                opt.value = txt; opt.innerText = txt;
                sel.appendChild(opt);
            });

            document.getElementById('cName').value = '';
            document.getElementById('cPhone').value = '';
            document.getElementById('deliveryType').value = 'pickup';
            updateShipping();

            new bootstrap.Modal(document.getElementById('productModal')).show();
        }

        function updateShipping() {
            const type = document.getElementById('deliveryType').value;
            const tabs = document.getElementById('locationTabs');
            const msg = document.getElementById('shippingMsg');
            const isJPJ = document.getElementById('locJPJ').checked;

            if (type === 'pickup') {
                tabs.style.display = 'none';
                msg.innerHTML = "Note: You can collect your order from our shop in Jalalpur Jattan.";
            } else {
                tabs.style.display = 'block'; 
                if (isJPJ) {
                    msg.innerHTML = "Shipping charges <b>PKR 100</b> will be added later in final bill.";
                } else {
                    msg.innerHTML = "Shipping charges will be determined later according to delivery address and product.";
                }
            }
        }

        function submitOrder() {
            const name = document.getElementById('cName').value;
            const phone = document.getElementById('cPhone').value;
            const variant = document.getElementById('modalVariation').value;
            const type = document.getElementById('deliveryType').value;
            const isJPJ = document.getElementById('locJPJ').checked;

            if(!name || !phone) { alert("Please enter Name and Phone."); return; }

            let shipText = "";
            if(type === 'pickup') shipText = "Instore Pickup";
            else shipText = isJPJ ? "Home Delivery (Jalalpur Jattan)" : "Home Delivery (Other City)";

            let msg = `*NEW ORDER REQUEST*\n`;
            msg += `Product: ${currentProdData.name}\n`;
            msg += `Variation: ${variant}\n`;
            msg += `Name: ${name}\n`;
            msg += `Phone: ${phone}\n`;
            msg += `Shipping: ${shipText}\n`;
            msg += `*Please confirm order amount.*`;

            window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        function setFilter(cat, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = cat;
            isExpanded = false;
            renderProducts();
        }
        function showAllItems() { isExpanded = true; renderProducts(); }
        function openGeneralContact() { window.open(`https://wa.me/${WHATSAPP_NUMBER}`, '_blank'); }
    </script>
    <script>
    // --- Deep Link Logic: Open Product Modal from URL ---
    // Usage: https://arhamprinters.pk/?product=Visiting Cards
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const productToOpen = urlParams.get('product');

        if (productToOpen) {
            // Wait slightly for JSON to load (since load is async)
            const checkData = setInterval(() => {
                if (Object.keys(allProducts).length > 0) {
                    clearInterval(checkData);
                    findAndOpenProduct(productToOpen);
                }
            }, 500);
        }
    });

    function findAndOpenProduct(searchName) {
        for (const [catName, products] of Object.entries(allProducts)) {
            const entries = Array.isArray(products) ? products.entries() : Object.entries(products);
            
            for (const [prodName, variants] of entries) {
                // Check if name matches (loose check)
                if (prodName.toLowerCase().includes(searchName.toLowerCase())) {
                    let vList = Array.isArray(variants) ? variants : [variants];
                    let img = vList[0].imageFile || 'img/arhamdata/arhamproducts/placeholder.webp';
                    
                    // Construct data object expected by openModal
                    let dataString = encodeURIComponent(JSON.stringify({
                        name: prodName, 
                        cat: catName, 
                        img: img, 
                        variants: vList
                    }));
                    
                    // Open the modal
                    openModal(dataString);
                    return; // Stop after finding first match
                }
            }
        }
    }
</script>
</body>
</html>