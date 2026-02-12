<?php
// =================================================================
// JANI PIZZA ENGINE | ROBUST EDITION
// =================================================================

// 1. Load Data
$json_file = 'data.json';
if (!file_exists($json_file)) { die("Error: data.json not found."); }
$raw_data = json_decode(file_get_contents($json_file), true);

// 2. Fetch Shop Data
$shop_data = reset($raw_data);
if (!$shop_data) { die("Error: Invalid JSON format."); }

$s = $shop_data['settings'];
$c = $s['theme_colors'];
$hero = $shop_data['hero_banner'];

// 3. SAFE VARIABLES (Prevent JS Errors)
$currency = $s['currency_symbol'] ?? 'Rs. ';
$delivery_fee = isset($s['delivery_fee']) && is_numeric($s['delivery_fee']) ? $s['delivery_fee'] : 0;
$whatsapp = $s['whatsapp_number'] ?? '';

// Helper for Images
function getImg($url) {
    if (strpos($url, 'http') === 0) return $url;
    return "https://arhamprinters.pk/" . ltrim($url, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php echo $s['shop_name']; ?> | Order Online</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: <?php echo $c['primary']; ?>;
            --secondary: <?php echo $c['secondary']; ?>;
            --dark: <?php echo $c['dark']; ?>;
            --light: <?php echo $c['light']; ?>;
            --accent: <?php echo $c['accent']; ?>;
            --font: 'Poppins', sans-serif;
        }

        body {
            font-family: var(--font);
            background-color: #f4f4f4;
            color: var(--dark);
            padding-bottom: 80px;
        }

        /* --- Navbar --- */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 10px 0;
        }
        .navbar-brand { font-weight: 800; color: var(--dark); font-size: 1.4rem; }
        .cart-btn-nav { position: relative; color: var(--dark); font-size: 1.2rem; cursor: pointer; }
        .cart-badge { 
            position: absolute; top: -5px; right: -8px; 
            background: var(--accent); color: white; 
            border-radius: 50%; width: 18px; height: 18px; 
            font-size: 10px; display: flex; align-items: center; justify-content: center;
        }

        /* --- Hero --- */
        .hero-section {
            height: 280px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('<?php echo getImg($hero['imageFile']); ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
            color: white;
            padding-bottom: 40px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .hero-content h1 { font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.5); font-size: 2.2rem; }
        
        /* --- Sticky Categories --- */
        .category-scroll {
            position: sticky; top: 65px; z-index: 90;
            background: #f4f4f4; padding: 15px 0;
            overflow-x: auto; white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        .category-scroll::-webkit-scrollbar { display: none; }
        .cat-btn {
            display: inline-block;
            padding: 8px 20px;
            margin-right: 10px;
            background: white;
            border-radius: 50px;
            color: var(--dark);
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .cat-btn.active {
            background: var(--primary);
            color: var(--secondary);
            transform: scale(1.05);
            border-color: var(--dark);
        }

        /* --- Product Cards --- */
        .section-heading {
            font-weight: 800; margin: 30px 0 20px; 
            position: relative; padding-left: 15px;
        }
        .section-heading::before {
            content: ''; position: absolute; left: 0; top: 5px; bottom: 5px; 
            width: 5px; background: var(--primary); border-radius: 5px;
        }

        .food-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            height: 100%;
            border: none;
        }
        .food-card:active { transform: scale(0.98); }
        .img-wrap { height: 180px; overflow: hidden; position: relative; }
        .img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .card-content { padding: 15px; }
        .item-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; }
        .item-desc { font-size: 0.8rem; color: #666; height: 40px; overflow: hidden; }
        .item-price { font-weight: 800; color: var(--accent); font-size: 1.1rem; }
        
        .variant-box {
            background: #f8f9fa; border: 1px solid #eee;
            border-radius: 8px; padding: 5px; width: 100%;
            font-size: 0.85rem; margin-bottom: 10px;
        }

        .add-btn {
            background: var(--dark); color: white;
            width: 100%; border: none; padding: 10px;
            border-radius: 12px; font-weight: 700;
            display: flex; justify-content: space-between; align-items: center;
        }
        .add-btn:hover { background: var(--primary); color: var(--secondary); }

        /* --- Bottom Cart & Modal --- */
        .float-cart {
            position: fixed; bottom: 20px; left: 5%; width: 90%;
            background: var(--dark); color: white;
            padding: 15px 25px; border-radius: 50px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000;
            cursor: pointer; transform: translateY(150%);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .float-cart.visible { transform: translateY(0); }

        .offcanvas-header { background: var(--primary); }
        .cart-item { border-bottom: 1px dashed #ddd; padding: 10px 0; }
        .qty-control {
            display: inline-flex; align-items: center; gap: 10px;
            background: #f1f1f1; padding: 2px 10px; border-radius: 20px;
        }
        
        .whatsapp-btn {
            background: #25D366; color: white; width: 100%;
            padding: 15px; border-radius: 15px; font-weight: 800;
            border: none; margin-top: 10px; display: flex;
            justify-content: center; align-items: center; gap: 10px;
        }

        .location-btn {
            background: #e9ecef; color: var(--dark); width: 100%;
            padding: 10px; border-radius: 10px; border: 1px solid #ced4da;
            font-size: 0.9rem; font-weight: 600; display: flex;
            align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 15px; transition: 0.3s;
        }
        .location-btn.active { background: #d1e7dd; color: #0f5132; border-color: #badbcc; }

        /* --- Footer --- */
        .dual-footer {
            background: #111; color: #888;
            padding: 40px 0 20px; margin-top: 50px;
            font-size: 0.85rem;
        }
        .footer-brand { color: white; font-weight: 800; font-size: 1.2rem; margin-bottom: 10px; display: block;}
        .footer-icon { width: 30px; text-align: center; color: var(--primary); }
        .dev-tag { text-align: right; border-left: 1px solid #333; padding-left: 20px; }
        .dev-logo { color: #0dcaf0; font-weight: 800; letter-spacing: -0.5px; }
    </style>
</head>
<body>

    <nav class="navbar fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="#">
                <i class="fas fa-pizza-slice me-2" style="color: var(--primary)"></i> <?php echo $s['shop_name']; ?>
            </a>
            <div class="cart-btn-nav" data-bs-toggle="offcanvas" data-bs-target="#cartPanel">
                <i class="fas fa-shopping-basket"></i>
                <span class="cart-badge" id="navBadge">0</span>
            </div>
        </div>
    </nav>

    <section class="hero-section mt-5">
        <div class="container hero-content">
            <span class="badge bg-warning text-dark mb-2">Open Now</span>
            <h1><?php echo $hero['headline']; ?></h1>
            <p class="mb-0 text-white-50"><?php echo $hero['subheadline']; ?></p>
        </div>
    </section>

    <div class="category-scroll">
        <div class="container">
            <?php 
            $first = true;
            foreach($shop_data['categories'] as $catName => $items) { 
                $id = preg_replace('/[^a-zA-Z0-9]/', '', $catName);
                echo "<a href='#$id' class='cat-btn ".($first?'active':'')."' onclick='activateCat(this)'>$catName</a>";
                $first = false;
            } 
            ?>
        </div>
    </div>

    <div class="container py-4">
        <?php foreach($shop_data['categories'] as $catName => $products): 
            $catId = preg_replace('/[^a-zA-Z0-9]/', '', $catName);
        ?>
        <div id="<?php echo $catId; ?>" class="scroll-offset">
            <h3 class="section-heading"><?php echo $catName; ?></h3>
            <div class="row g-4">
                <?php foreach($products as $p): 
                    $hasVar = !empty($p['variants']);
                    $price = $p['base_price'];
                    $img = getImg($p['imageFile']);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="food-card h-100 d-flex flex-column">
                        <div class="img-wrap">
                            <img src="<?php echo $img; ?>" alt="<?php echo $p['name']; ?>" loading="lazy">
                        </div>
                        <div class="card-content d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h5 class="item-title"><?php echo $p['name']; ?></h5>
                                <span class="item-price"><?php echo $s['currency_symbol']; ?><span id="price-<?php echo $p['id']; ?>"><?php echo $price; ?></span></span>
                            </div>
                            <p class="item-desc"><?php echo $p['description']; ?></p>
                            
                            <div class="mt-auto">
                                <?php if($hasVar): ?>
                                <select class="variant-box" id="var-<?php echo $p['id']; ?>" onchange="updatePrice('<?php echo $p['id']; ?>', <?php echo $price; ?>, this)">
                                    <?php foreach($p['variants'] as $v): ?>
                                    <option value="<?php echo $v['name']; ?>" data-add="<?php echo $v['price_adjustment']; ?>">
                                        <?php echo $v['name']; ?> (+<?php echo $v['price_adjustment']; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php endif; ?>

                                <button class="add-btn shadow-sm" onclick="addToCart('<?php echo $p['id']; ?>', '<?php echo addslashes($p['name']); ?>', <?php echo $price; ?>, <?php echo $hasVar?'true':'false'; ?>)">
                                    <span>ADD TO CART</span>
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <footer class="dual-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6">
                    <span class="footer-brand text-warning"><?php echo $s['shop_name']; ?></span>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><i class="fas fa-map-marker-alt footer-icon"></i> <?php echo $s['address']; ?></li>
                        <li class="mb-1"><i class="fas fa-phone footer-icon"></i> <?php echo $s['contact_number']; ?></li>
                    </ul>
                </div>
                <div class="col-6 dev-tag">
                    <span class="d-block mb-1 text-white">Tech Partner</span>
                    <span class="dev-logo">ARHAM PRINTERS</span>
                    <p class="mb-0 small">Websites & Printing Solutions</p>
                    <a href="https://wa.me/923006238233" class="text-white text-decoration-none small"><i class="fab fa-whatsapp"></i> 0300-6238233</a>
                </div>
            </div>
            <div class="text-center mt-4 border-top border-secondary pt-3 text-secondary" style="font-size: 10px;">
                © <?php echo date('Y'); ?> All rights reserved.
            </div>
        </div>
    </footer>

    <div class="float-cart" id="floatBar" data-bs-toggle="offcanvas" data-bs-target="#cartPanel">
        <div>
            <div class="fw-bold"><span id="floatCount">0</span> Items</div>
            <div class="small text-white-50">View Cart</div>
        </div>
        <div class="fw-bold fs-5">
            <?php echo $s['currency_symbol']; ?><span id="floatTotal">0</span>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartPanel">
        <div class="offcanvas-header text-white">
            <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-bag me-2"></i> Your Order</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <div id="cartList" class="flex-grow-1 overflow-auto">
                <div class="text-center mt-5 text-muted">Cart is empty</div>
            </div>

            <div class="bg-light p-3 rounded-3 mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal:</span>
                    <span class="fw-bold"><?php echo $s['currency_symbol']; ?><span id="subTotal">0</span></span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-success">
                    <span>Delivery:</span>
                    <span><?php echo $delivery_fee == 0 ? 'Free' : $s['currency_symbol'].$delivery_fee; ?></span>
                </div>

                <h6 class="fw-bold mt-4 mb-2"><i class="fas fa-map-marker-alt me-1"></i> Delivery Details</h6>
                
                <button type="button" class="location-btn" id="locBtn" onclick="getLocation()">
                    <i class="fas fa-location-arrow"></i> Share My Current Location
                </button>
                <input type="hidden" id="geoLink" value="">

                <input type="text" id="cName" class="form-control mb-2" placeholder="Full Name">
                <input type="tel" id="cPhone" class="form-control mb-2" placeholder="Phone Number (03...)" maxlength="11">
                <textarea id="cAddr" class="form-control mb-3" rows="2" placeholder="House #, Street, Nearby Landmark..."></textarea>

                <button class="whatsapp-btn" onclick="checkout()">
                    <span>PLACE ORDER</span>
                    <i class="fab fa-whatsapp fa-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SAFE CONFIGURATION FOR JS
        const CURR = "<?php echo $currency; ?>";
        const FEE = <?php echo $delivery_fee; ?>;
        const WA_NUM = "<?php echo $whatsapp; ?>";
        
        let cart = [];

        // 1. Variant Logic
        function updatePrice(id, base, el) {
            let add = parseFloat(el.options[el.selectedIndex].getAttribute('data-add')) || 0;
            document.getElementById('price-'+id).innerText = base + add;
        }

        // 2. Cart Logic
        function addToCart(id, name, base, hasVar) {
            let price = base;
            let variant = '';

            if (hasVar) {
                let el = document.getElementById('var-'+id);
                variant = el.value;
                price += parseFloat(el.options[el.selectedIndex].getAttribute('data-add'));
            }

            let uid = id + variant;
            let exist = cart.find(x => x.uid === uid);

            if (exist) { exist.qty++; } 
            else { cart.push({ uid, id, name, price, variant, qty: 1 }); }

            renderCart();
            if(navigator.vibrate) navigator.vibrate(50);
        }

        function modQty(uid, change) {
            let item = cart.find(x => x.uid === uid);
            if (item) {
                item.qty += change;
                if (item.qty <= 0) cart = cart.filter(x => x.uid !== uid);
                renderCart();
            }
        }

        function renderCart() {
            let list = document.getElementById('cartList');
            let total = 0;
            let count = 0;

            if (cart.length === 0) {
                list.innerHTML = '<div class="text-center mt-5 text-muted opacity-50"><i class="fas fa-pizza-slice fa-3x mb-3"></i><p>Your cart is empty</p></div>';
                document.getElementById('floatBar').classList.remove('visible');
            } else {
                let html = '';
                cart.forEach(i => {
                    total += i.price * i.qty;
                    count += i.qty;
                    html += `
                    <div class="cart-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">${i.name}</div>
                            <div class="small text-muted">${i.variant}</div>
                            <div class="small text-danger fw-bold">${CURR}${i.price} x ${i.qty}</div>
                        </div>
                        <div class="qty-control">
                            <i class="fas fa-minus text-secondary" style="cursor:pointer" onclick="modQty('${i.uid}', -1)"></i>
                            <span class="mx-2 fw-bold">${i.qty}</span>
                            <i class="fas fa-plus text-success" style="cursor:pointer" onclick="modQty('${i.uid}', 1)"></i>
                        </div>
                    </div>`;
                });
                list.innerHTML = html;
                document.getElementById('floatBar').classList.add('visible');
            }

            document.getElementById('subTotal').innerText = total;
            document.getElementById('floatTotal').innerText = total + FEE;
            document.getElementById('floatCount').innerText = count;
            document.getElementById('navBadge').innerText = count;
        }

        // 3. Location Logic
        function getLocation() {
            let btn = document.getElementById('locBtn');
            if (navigator.geolocation) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting Location...';
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            let lat = position.coords.latitude;
            let long = position.coords.longitude;
            let link = `https://www.google.com/maps?q=${lat},${long}`;
            
            document.getElementById('geoLink').value = link;
            let btn = document.getElementById('locBtn');
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Location Attached';
            btn.classList.add('active');
        }

        function showError(error) {
            let btn = document.getElementById('locBtn');
            btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Location Failed';
            alert("Could not get location. Ensure GPS is on and permission granted.");
        }

        // 4. Checkout Logic
        function checkout() {
            if (cart.length === 0) return alert("Cart is empty");
            
            let name = document.getElementById('cName').value;
            let phone = document.getElementById('cPhone').value;
            let addr = document.getElementById('cAddr').value;
            let geo = document.getElementById('geoLink').value;

            if (!name || !phone || !addr) return alert("Please fill Name, Phone and Address");

            let total = parseInt(document.getElementById('subTotal').innerText);
            let final = total + FEE;

            let msg = `*NEW ORDER @ ${name}* 🍕\n\n`;
            msg += `📞 Phone: ${phone}\n`;
            msg += `🏠 Address: ${addr}\n`;
            if(geo) msg += `📍 Location: ${geo}\n`;
            msg += `--------------------------\n`;
            
            cart.forEach(i => {
                msg += `▪️ ${i.qty}x ${i.name} ${i.variant ? '('+i.variant+')' : ''} - ${i.price*i.qty}\n`;
            });

            msg += `--------------------------\n`;
            msg += `💰 *TOTAL: ${CURR} ${final}*`;
            if(FEE > 0) msg += ` (Incl. Delivery)`;

            window.open(`https://wa.me/${WA_NUM}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        // Active Category Highlighter
        function activateCat(el) {
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>