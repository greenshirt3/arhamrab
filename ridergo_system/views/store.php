<?php
// 1. FILTER PRODUCTS FOR THIS SHOP
$menu = [];
foreach($products_data as $p) {
    if($p['shop_id'] == $current_shop['id']) {
        $menu[$p['cat']][] = $p;
    }
}

// 2. DETERMINE SHOP TYPE (Food vs Retail layout)
$shop_type = $current_shop['type'] ?? 'food'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $current_shop['name']; ?> | Powered by RiderGo</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand: <?php echo $current_shop['theme_color']; ?>;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f1f5f9;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background: var(--light); color: var(--dark); -webkit-font-smoothing: antialiased; padding-bottom: 80px; }
        
        /* --- 1. HERO HEADER --- */
        .shop-hero {
            height: 35vh; min-height: 250px; position: relative;
            background: url('<?php echo $current_shop['banner']; ?>') center/cover no-repeat;
        }
        .hero-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.1) 100%);
            position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: flex-end; padding: 20px;
        }
        .shop-header-content { display: flex; align-items: flex-end; gap: 15px; max-width: 1000px; margin: 0 auto; width: 100%; }
        .shop-logo {
            width: 80px; height: 80px; background: var(--white); border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); object-fit: contain; padding: 5px;
        }
        .shop-details h1 { color: var(--white); margin: 0; font-size: 1.8rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .shop-details p { color: #e2e8f0; margin: 5px 0 0; font-size: 0.95rem; }
        .verified-badge { background: var(--white); color: var(--dark); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 8px; }

        /* --- 2. STICKY TOOLS --- */
        .sticky-tools { position: sticky; top: 0; z-index: 50; background: var(--white); box-shadow: var(--shadow); }
        
        .search-container { padding: 10px 20px; border-bottom: 1px solid #e2e8f0; max-width: 1000px; margin: 0 auto; }
        .search-box { position: relative; }
        .search-input {
            width: 100%; padding: 12px 15px 12px 45px; border-radius: 8px; border: 1px solid #cbd5e1;
            background: #f8fafc; font-size: 0.95rem; outline: none; transition: 0.2s; box-sizing: border-box;
        }
        .search-input:focus { border-color: var(--brand); background: var(--white); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray); }

        .nav-scroller {
            padding: 10px 20px; overflow-x: auto; white-space: nowrap; 
            -webkit-overflow-scrolling: touch; scrollbar-width: none;
            max-width: 1000px; margin: 0 auto;
        }
        .nav-scroller::-webkit-scrollbar { display: none; }
        .nav-pill {
            display: inline-block; padding: 8px 16px; margin-right: 8px;
            background: var(--light); border-radius: 50px; text-decoration: none;
            color: var(--gray); font-size: 0.9rem; font-weight: 500; transition: 0.2s;
        }
        .nav-pill.active { background: var(--dark); color: var(--white); }

        /* --- 3. PRODUCT GRID --- */
        .main-content { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .cat-section { margin-bottom: 30px; scroll-margin-top: 140px; }
        .cat-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 15px; color: var(--dark); border-left: 4px solid var(--brand); padding-left: 10px; }

        .product-grid { display: grid; gap: 15px; }
        
        /* ADAPTIVE LAYOUT LOGIC */
        <?php if($shop_type == 'food'): ?>
            .product-grid { grid-template-columns: 1fr; }
            .product-card { display: flex; flex-direction: row; }
            .p-img { width: 110px; height: 110px; }
        <?php else: ?>
            .product-grid { grid-template-columns: repeat(2, 1fr); }
            .product-card { display: flex; flex-direction: column; }
            .p-img { width: 100%; aspect-ratio: 1/1; }
            @media(min-width: 768px) { .product-grid { grid-template-columns: repeat(4, 1fr); } }
        <?php endif; ?>

        .product-card {
            background: var(--white); border-radius: 12px; padding: 12px;
            border: 1px solid #f1f5f9; position: relative; overflow: hidden;
            transition: transform 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .product-card:active { transform: scale(0.98); }
        .p-img { background-size: cover; background-position: center; border-radius: 8px; flex-shrink: 0; background-color: #eee; }
        .p-info { padding: 10px 0 0 0; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        <?php if($shop_type == 'food'): ?> .p-info { padding: 0 0 0 15px; } <?php endif; ?>

        .p-name { font-weight: 600; font-size: 1rem; margin-bottom: 4px; line-height: 1.3; }
        .p-desc { font-size: 0.8rem; color: var(--gray); margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .p-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
        .p-price { font-weight: 700; color: var(--dark); font-size: 1.1rem; }
        
        .btn-add {
            width: 35px; height: 35px; border-radius: 50%; border: none;
            background: var(--light); color: var(--brand); font-size: 1.2rem;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: 0.2s;
        }
        .btn-add:hover { background: var(--brand); color: white; }

        /* --- 4. CART DRAWER --- */
        .cart-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 998;
            opacity: 0; pointer-events: none; transition: 0.3s;
        }
        .cart-overlay.open { opacity: 1; pointer-events: all; }
        
        .cart-drawer {
            position: fixed; top: 0; right: 0; width: 100%; max-width: 400px; height: 100%;
            background: var(--white); z-index: 999; transform: translateX(100%);
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column;
        }
        .cart-drawer.open { transform: translateX(0); }
        
        .drawer-header { padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .drawer-title { font-weight: 700; font-size: 1.1rem; }
        .close-cart { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--gray); }
        
        .drawer-body { flex-grow: 1; overflow-y: auto; padding: 20px; }
        .cart-item { display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #e2e8f0; }
        .ci-details { flex-grow: 1; }
        .ci-name { font-weight: 600; font-size: 0.95rem; }
        .ci-price { color: var(--gray); font-size: 0.9rem; }
        .ci-controls { display: flex; align-items: center; gap: 10px; margin-top: 5px; }
        .qty-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-weight:bold; }
        
        .drawer-footer { padding: 20px; border-top: 1px solid #e2e8f0; background: #fff; box-shadow: 0 -4px 10px rgba(0,0,0,0.05); }
        .bill-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem; color: #555; }
        .bill-total { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #cbd5e1; font-weight: 800; font-size: 1.2rem; color: var(--dark); }
        
        .btn-checkout {
            width: 100%; padding: 16px; border: none; border-radius: 12px;
            background: var(--brand); color: white; font-weight: 700; font-size: 1rem;
            cursor: pointer; display: flex; justify-content: center; gap: 10px; align-items: center;
            margin-top: 15px; transition: 0.2s;
        }
        .btn-checkout:hover { opacity: 0.9; }
        .btn-checkout:disabled { opacity: 0.7; cursor: not-allowed; }

        /* --- 5. FLOATING BUTTON --- */
        .float-btn {
            position: fixed; bottom: 20px; left: 5%; width: 90%; z-index: 100;
            background: var(--dark); color: white; padding: 15px 20px; border-radius: 12px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3); transform: translateY(150%); transition: 0.3s; cursor: pointer;
        }
        .float-btn.visible { transform: translateY(0); }

        /* --- 6. WHATSAPP WIDGET --- */
        .wa-widget {
            position: fixed; bottom: 90px; right: 20px;
            width: 55px; height: 55px; background: #25D366; color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 28px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 90;
            transition: 0.3s; text-decoration: none; animation: float 3s ease-in-out infinite;
        }
        .wa-widget:hover { transform: scale(1.1); }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }

        /* --- 7. FOOTER --- */
        .main-footer {
            text-align: center; padding: 40px 20px; color: #888; font-size: 0.9rem; 
            background: #fff; margin-top: 30px; border-top: 1px solid #eee;
        }
        .footer-links a { color: inherit; text-decoration: none; margin: 0 10px; transition: 0.2s; }
        .footer-links a:hover { color: var(--brand); }
        .arham-tag a { color: #F37021; font-weight: bold; text-decoration: none; }

    </style>
</head>
<body>

    <header class="shop-hero">
        <div class="hero-overlay">
            <div class="shop-header-content">
                <img src="<?php echo $current_shop['logo']; ?>" class="shop-logo">
                <div class="shop-details">
                    <span class="verified-badge"><i class="fas fa-check-circle text-primary"></i> Official Store</span>
                    <h1><?php echo $current_shop['name']; ?></h1>
                    <p><i class="fas fa-map-marker-alt"></i> Powered by RiderGo &bull; <?php echo $current_shop['tagline']; ?></p>
                </div>
            </div>
        </div>
    </header>

    <div class="sticky-tools">
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" id="searchFilter" placeholder="Search products..." onkeyup="filterProducts()">
            </div>
        </div>
        <div class="nav-scroller">
            <a href="#" class="nav-pill active" onclick="filterCat('all', this)">All</a>
            <?php foreach($menu as $cat => $items): ?>
                <a href="#cat-<?php echo str_replace(' ', '', $cat); ?>" class="nav-pill" onclick="setActive(this)"><?php echo $cat; ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <main class="main-content">
        <?php foreach($menu as $cat => $items): ?>
        <div id="cat-<?php echo str_replace(' ', '', $cat); ?>" class="cat-section">
            <h3 class="cat-title"><?php echo $cat; ?></h3>
            <div class="product-grid">
                <?php foreach($items as $item): ?>
                <div class="product-card" data-name="<?php echo strtolower($item['name']); ?>">
                    <div class="p-img" style="background-image: url('<?php echo $item['img']; ?>');"></div>
                    <div class="p-info">
                        <div>
                            <div class="p-name"><?php echo $item['name']; ?></div>
                            <div class="p-desc">High quality <?php echo strtolower($item['name']); ?> available now.</div>
                        </div>
                        <div class="p-footer">
                            <div class="p-price">Rs. <?php echo $item['price']; ?></div>
                            <button class="btn-add" onclick="addToCart('<?php echo $item['name']; ?>', <?php echo $item['price']; ?>)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <footer class="main-footer">
            <div class="footer-links" style="margin-bottom: 20px;">
                <a href="views/track.php" style="border:1px solid #ddd; padding:5px 15px; border-radius:20px; font-weight:bold; color:#333;">
                    <i class="fas fa-search"></i> Track Order
                </a>
            </div>
            
            <div class="footer-links" style="margin-bottom: 15px;">
                <a href="views/contact.php">Contact Us</a> |
                <a href="views/legal.php">Terms & Policy</a> |
                <a href="../shop_dashboard.php">Partner Login</a>
            </div>
            
            <div style="margin-bottom: 10px;">
                © <?php echo date('Y'); ?> <b><?php echo $current_shop['name']; ?></b>
            </div>

            <div class="arham-tag" style="font-size: 0.8rem; opacity: 0.8;">
                Developed by <a href="https://arhamprinters.pk" target="_blank">Arham Printers</a> & RiderGo
            </div>
        </footer>
    </main>

    <a href="https://wa.me/<?php echo $current_shop['phone']; ?>?text=Salam,%20I%20have%20a%20question%20about%20your%20store." target="_blank" class="wa-widget">
        <i class="fab fa-whatsapp"></i>
    </a>

    <div class="float-btn" id="floatBtn" onclick="toggleCart(true)">
        <span style="font-weight:600"><span id="floatCount">0</span> items</span>
        <span style="font-weight:700">View Cart</span>
        <span style="font-weight:600">Rs. <span id="floatTotal">0</span></span>
    </div>

    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart(false)"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="drawer-header">
            <div class="drawer-title">Your Cart</div>
            <button class="close-cart" onclick="toggleCart(false)">&times;</button>
        </div>
        
        <div class="drawer-body" id="cartItems">
            <div style="text-align:center; margin-top:50px; color:#94a3b8;">
                <i class="fas fa-shopping-basket fa-3x"></i>
                <p>Your cart is empty</p>
            </div>
        </div>
        
        <div class="drawer-footer">
            <button onclick="getLocation()" style="width:100%; padding:10px; background:#f0f9ff; border:1px dashed #0284c7; color:#0284c7; border-radius:8px; cursor:pointer; margin-bottom:15px; font-weight:600;">
                <i class="fas fa-location-arrow"></i> Use My Current Location
            </button>
            <div id="locStatus" style="font-size:0.8rem; color:green; margin-bottom:10px; display:none; text-align:center;">
                <i class="fas fa-check-circle"></i> Location Captured
            </div>

            <textarea id="orderNote" style="width:100%; box-sizing:border-box; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:inherit; margin-bottom:15px;" rows="2" placeholder="Add a note (e.g. No mayo, Ring doorbell)"></textarea>
            
            <div class="bill-row"><span>Subtotal</span><span id="drawerSubTotal">Rs. 0</span></div>
            <div class="bill-row"><span>Delivery Fee</span><span>Rs. 50</span></div>
            <div class="bill-total">
                <span>Total</span><span id="drawerTotal">Rs. 0</span>
            </div>

            <button class="btn-checkout" onclick="checkout()">
                Checkout on WhatsApp <i class="fab fa-whatsapp"></i>
            </button>
        </div>
    </div>

    <script>
        let cart = [];
        let userLat = 0;
        let userLng = 0;
        const deliveryFee = 50;
        const shopPhone = "<?php echo $current_shop['phone']; ?>";
        const shopName = "<?php echo $current_shop['name']; ?>";

        // 1. ADD TO CART
        function addToCart(name, price) {
            const existing = cart.find(item => item.name === name);
            if(existing) {
                existing.qty++;
            } else {
                cart.push({name, price, qty: 1});
            }
            updateCartUI();
            toggleCart(true); 
        }

        // 2. UPDATE UI
        function updateCartUI() {
            const container = document.getElementById('cartItems');
            const floatBtn = document.getElementById('floatBtn');
            let subtotal = 0;
            let count = 0;

            if(cart.length === 0) {
                container.innerHTML = `<div style="text-align:center; margin-top:50px; color:#94a3b8;"><i class="fas fa-shopping-basket fa-3x"></i><p>Your cart is empty</p></div>`;
                floatBtn.classList.remove('visible');
            } else {
                let html = '';
                cart.forEach((item, index) => {
                    subtotal += item.price * item.qty;
                    count += item.qty;
                    html += `
                    <div class="cart-item">
                        <div class="ci-details">
                            <div style="font-weight:600">${item.name}</div>
                            <div style="color:#666; font-size:0.9rem">Rs. ${item.price} x ${item.qty}</div>
                            <div class="ci-controls">
                                <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
                                <span>${item.qty}</span>
                                <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
                            </div>
                        </div>
                        <div style="font-weight:600">Rs. ${item.price * item.qty}</div>
                    </div>`;
                });
                container.innerHTML = html;
                floatBtn.classList.add('visible');
            }

            const total = subtotal > 0 ? subtotal + deliveryFee : 0;

            // Update Elements
            document.getElementById('floatCount').innerText = count;
            document.getElementById('floatTotal').innerText = total;
            document.getElementById('drawerSubTotal').innerText = "Rs. " + subtotal;
            document.getElementById('drawerTotal').innerText = "Rs. " + total;
        }

        function changeQty(index, change) {
            cart[index].qty += change;
            if(cart[index].qty <= 0) cart.splice(index, 1);
            updateCartUI();
        }

        function toggleCart(open) {
            const drawer = document.getElementById('cartDrawer');
            const overlay = document.getElementById('cartOverlay');
            if(open) {
                drawer.classList.add('open');
                overlay.classList.add('open');
            } else {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
            }
        }

        // 3. SEARCH & NAV
        function filterProducts() {
            const term = document.getElementById('searchFilter').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.getAttribute('data-name');
                if(name.includes(term)) card.style.display = 'flex';
                else card.style.display = 'none';
            });
        }

        function setActive(el) {
            document.querySelectorAll('.nav-pill').forEach(n => n.classList.remove('active'));
            el.classList.add('active');
        }

        function filterCat(cat, el) {
            setActive(el);
            // Optional: Scroll to section logic could go here
            document.querySelectorAll('.cat-section').forEach(sec => sec.style.display = 'block');
        }

        // 4. GEOLOCATION
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    document.getElementById('locStatus').style.display = 'block';
                }, () => {
                    alert("Unable to retrieve your location.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // 5. CHECKOUT
        function checkout() {
            if(cart.length === 0) return alert("Cart is empty");

            const btn = document.querySelector('.btn-checkout');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const total = subtotal + deliveryFee;
            const note = document.getElementById('orderNote').value;

            // API Call
            fetch('api/place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    shop_id: "<?php echo $current_shop['id']; ?>",
                    shop_name: shopName,
                    cart: cart,
                    total: total,
                    note: note,
                    lat: userLat,
                    lng: userLng
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Build WhatsApp Message
                    let itemsList = "";
                    cart.forEach(i => { itemsList += `• ${i.qty}x ${i.name} (Rs.${i.price})\n`; });
                    
                    let msg = `*New Order #${data.order_id}*\n*Shop:* ${shopName}\n----------------\n${itemsList}----------------\nSubtotal: Rs. ${subtotal}\nDelivery: Rs. ${deliveryFee}\n*Total: Rs. ${total}*`;
                    if(note) msg += `\n*Note:* ${note}`;
                    if(userLat != 0) msg += `\n*Location:* https://maps.google.com/?q=${userLat},${userLng}`;
                    msg += `\n\n_Please confirm order._`;

                    // Redirect logic
                    window.open(`https://wa.me/${shopPhone}?text=${encodeURIComponent(msg)}`, '_blank');
                    window.location.href = `views/order_success.php?id=${data.order_id}`;
                } else {
                    alert("System error. Please try again.");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Connection failed");
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>