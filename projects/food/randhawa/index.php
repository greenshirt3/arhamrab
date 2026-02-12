<?php
// --- CONFIGURATION & DATABASE ---
$config = [
    "business" => [
        "name" => "Randhawa Tent & Pakwan",
        "tagline" => "آپ کا اعتماد ہماری پہچان",
        "address" => "Tanda Road, Domailas Chowk, Jalalpur Jattan",
        "map_link" => "https://maps.google.com/?q=Randhawa+Tent+Service+Jalalpur+Jattan",
        "phone_primary" => "923016435352",
        "contacts" => [
            "Shakeel Akhtar" => "+92 306 9869032",
            "Zubair Randhawa" => "+92 301 6435352",
            "Ismail Randhawa" => "+92 302 9432822"
        ],
        "currency" => "Rs."
    ],
    "categories" => [
        "rice" => "Shadi Rice (Deig)",
        "curry" => "Qorma & Salan",
        "bbq" => "BBQ & Roast",
        "decor" => "Tent Setup",
    ],
    "products" => [
        [ "id" => 1, "cat" => "rice", "name" => "Chicken Biryani (Super)", "price" => 14500, "unit" => "Per Daig (12kg)", "img" => "https://images.unsplash.com/photo-1633945274405-b6c8069047b0?q=80&w=800" ],
        [ "id" => 2, "cat" => "rice", "name" => "Mutton Pulao (Special)", "price" => 28000, "unit" => "Per Daig (10kg)", "img" => "https://images.unsplash.com/photo-1606471191009-63994c53433b?q=80&w=800" ],
        [ "id" => 3, "cat" => "curry", "name" => "Chicken Qorma (Shadi Style)", "price" => 16000, "unit" => "Per Daig", "img" => "https://images.unsplash.com/photo-1548943487-a2e4e43b485c?q=80&w=800" ],
        [ "id" => 4, "cat" => "curry", "name" => "Beef Haleem (Royal)", "price" => 18500, "unit" => "Per Daig", "img" => "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=800" ],
        [ "id" => 5, "cat" => "bbq", "name" => "Chicken Steam Roast", "price" => 1200, "unit" => "Per Piece (Whole)", "img" => "https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?q=80&w=800" ],
        [ "id" => 6, "cat" => "decor", "name" => "VIP Marquee Setup", "price" => 35000, "unit" => "Per Event", "img" => "https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?q=80&w=800" ],
        [ "id" => 7, "cat" => "decor", "name" => "Crockery & Waiters", "price" => 200, "unit" => "Per Head", "img" => "https://images.unsplash.com/photo-1556037843-347ea897a696?q=80&w=800" ],
        [ "id" => 8, "cat" => "rice", "name" => "Zarda / Mutanjan", "price" => 12500, "unit" => "Per Daig", "img" => "https://images.unsplash.com/photo-1589301760574-d9225abd1154?q=80&w=800" ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['business']['name']; ?></title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- DESIGN SYSTEM --- */
        :root {
            --primary: #991B1B; /* Red from Card */
            --gold: #D97706;    /* Gold from Card */
            --dark: #1F2937;
            --light: #F3F4F6;
            --white: #FFFFFF;
            --radius: 12px;
        }

        /* --- GLOBAL --- */
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { margin: 0; font-family: 'Montserrat', sans-serif; background: var(--light); color: var(--dark); overflow-x: hidden; padding-bottom: 80px; }
        h1, h2, h3 { font-family: 'Tajawal', sans-serif; font-weight: 700; margin: 0; }
        
        /* --- 1. NAVBAR (GLASS) --- */
        .navbar {
            position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05); padding: 15px 5%;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 30px rgba(0,0,0,0.03);
        }
        .logo { font-size: 1.2rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; font-weight: 800; }
        .logo span { color: var(--gold); }
        
        /* --- 2. HERO --- */
        .hero {
            height: 60vh; position: relative; display: flex; align-items: center; justify-content: center; text-align: center;
            background: url('https://images.unsplash.com/photo-1574966740793-275d33a9257e?q=80&w=1920');
            background-size: cover; background-position: center; margin-bottom: 30px;
        }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.8) 100%); }
        .hero-content { position: relative; z-index: 2; padding: 20px; color: white; }
        .hero-badge { background: var(--gold); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 15px; }
        .hero h1 { font-size: 3rem; margin-bottom: 15px; }
        
        /* --- 3. CATEGORY TABS --- */
        .tabs-container { 
            position: sticky; top: 70px; z-index: 900; padding: 10px 0; background: var(--light);
            overflow-x: auto; white-space: nowrap; -ms-overflow-style: none; scrollbar-width: none;
        }
        .tabs-wrapper { display: flex; gap: 10px; padding: 0 5%; width: max-content; margin: 0 auto; }
        .tab-chip {
            padding: 10px 25px; background: white; border: 1px solid #e5e7eb; border-radius: 30px;
            font-size: 0.9rem; font-weight: 600; color: #6b7280; cursor: pointer; transition: 0.3s;
        }
        .tab-chip.active { background: var(--primary); color: white; border-color: var(--primary); }

        /* --- 4. PRODUCTS --- */
        .container { max-width: 1200px; margin: 0 auto; padding: 20px 5%; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        
        .card {
            background: white; border-radius: var(--radius); overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s;
            border: 1px solid white; display: flex; flex-direction: column;
        }
        .card:hover { transform: translateY(-5px); border-color: var(--gold); }
        
        .card-img { width: 100%; height: 200px; object-fit: cover; }
        .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-unit { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 600; margin-bottom: 5px; }
        .card-title { font-size: 1.1rem; margin-bottom: 15px; font-weight: 600; line-height: 1.4; }
        
        .btn-add {
            margin-top: auto; width: 100%; padding: 12px; border: none; border-radius: 10px;
            background: #f3f4f6; color: var(--dark); font-weight: 700; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-add:hover { background: var(--primary); color: white; }

        /* --- 5. FOOTER (Professional Dark) --- */
        .footer {
            background: var(--dark); color: #9ca3af; padding: 60px 5% 30px; margin-top: 60px;
            border-top: 5px solid var(--primary);
        }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; }
        
        .footer h3 { color: white; margin-bottom: 20px; font-size: 1.2rem; }
        .footer p { font-size: 0.9rem; line-height: 1.6; }
        
        .contact-row { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 15px; }
        .contact-row i { color: var(--gold); margin-top: 5px; }
        .contact-row a { color: #d1d5db; text-decoration: none; transition: 0.3s; }
        .contact-row a:hover { color: var(--gold); }
        .contact-name { font-size: 0.8rem; color: #6b7280; display: block; }

        .arham-bar {
            border-top: 1px solid #374151; margin-top: 40px; padding-top: 20px;
            text-align: center; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .arham-link { color: var(--gold); font-weight: 700; text-decoration: none; font-family: 'Tajawal'; }

        /* --- 6. FLOATING ACTIONS --- */
        .float-container { position: fixed; bottom: 25px; right: 25px; display: flex; flex-direction: column; gap: 15px; z-index: 990; }
        
        .fab {
            width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: white; font-size: 24px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); cursor: pointer; transition: 0.3s;
            text-decoration: none; border: 2px solid white;
        }
        .fab-cart { background: var(--dark); position: relative; }
        .fab-wa { background: #25D366; }
        .fab-phone { background: var(--primary); }
        .fab:hover { transform: scale(1.1); }
        
        .badge {
            position: absolute; top: -5px; right: -5px; background: var(--gold); color: white;
            font-size: 0.75rem; font-weight: bold; width: 22px; height: 22px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; border: 2px solid var(--dark);
        }

        /* --- 7. CART DRAWER --- */
        .cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1001; display: none; }
        .cart-overlay.active { display: block; }
        
        .cart-drawer {
            position: fixed; top: 0; right: -100%; width: 100%; max-width: 380px; height: 100%;
            background: white; z-index: 1002; transition: 0.4s; display: flex; flex-direction: column;
            box-shadow: -10px 0 40px rgba(0,0,0,0.2);
        }
        .cart-drawer.open { right: 0; }
        
        .cart-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
        .cart-body { flex-grow: 1; overflow-y: auto; padding: 20px; }
        .cart-footer { padding: 25px; border-top: 1px solid #eee; background: white; }
        
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #eee; }
        .qty-box { display: flex; gap: 10px; align-items: center; background: #f3f4f6; padding: 5px; border-radius: 5px; }
        
        .btn-checkout { 
            width: 100%; background: #25D366; color: white; padding: 15px; border: none;
            border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; 
            display: flex; justify-content: center; align-items: center; gap: 10px;
        }

        /* Responsive */
        @media(max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">Randhawa <span>Pakwan</span></div>
        <a href="tel:<?php echo $config['business']['phone_primary']; ?>" style="color:var(--primary); text-decoration:none; font-weight:600;">
            <i class="fas fa-phone-alt"></i> Call Now
        </a>
    </nav>

    <header class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="hero-badge">Est. 2010</span>
            <h1 class="urdu-font"><?php echo $config['business']['tagline']; ?></h1>
            <p>Premium Catering & Tent Services in Jalalpur Jattan</p>
            <button onclick="document.getElementById('menu').scrollIntoView({behavior:'smooth'})" style="background:white; color:black; padding:12px 30px; border:none; border-radius:50px; font-weight:700; cursor:pointer;">
                Order Online
            </button>
        </div>
    </header>

    <div class="tabs-container" id="menu">
        <div class="tabs-wrapper">
            <button class="tab-chip active" onclick="filterMenu('all', this)">All Items</button>
            <?php foreach($config['categories'] as $key => $label): ?>
            <button class="tab-chip" onclick="filterMenu('<?php echo $key; ?>', this)"><?php echo $label; ?></button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="container">
        <div class="grid">
            <?php foreach($config['products'] as $item): ?>
            <div class="card" data-cat="<?php echo $item['cat']; ?>">
                <img src="<?php echo $item['img']; ?>" class="card-img" alt="<?php echo $item['name']; ?>" loading="lazy">
                <div class="card-body">
                    <div class="card-unit"><?php echo $item['unit']; ?></div>
                    <div class="card-title"><?php echo $item['name']; ?></div>
                    <div style="font-weight:700; color:var(--primary); font-size:1.1rem; margin-bottom:15px;">
                        <?php echo $config['business']['currency'] . number_format($item['price']); ?>
                    </div>
                    <button class="btn-add" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $item['name']; ?>', <?php echo $item['price']; ?>)">
                        <i class="fas fa-plus"></i> Add to Order
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-grid">
            
            <div>
                <h3>About Randhawa</h3>
                <p>We provide high-quality catering (Pakwan) and luxury tent setups for weddings, parties, and corporate events across Gujrat District. Trust us for hygiene and taste.</p>
                <div style="margin-top:20px; font-size:1.2rem;">
                    <i class="fab fa-facebook margin-right:15px;"></i>
                    <i class="fab fa-instagram"></i>
                </div>
            </div>

            <div>
                <h3>Management Team</h3>
                <?php foreach($config['business']['contacts'] as $name => $num): ?>
                <div class="contact-row">
                    <i class="fas fa-user-tie"></i>
                    <div>
                        <span class="contact-name"><?php echo $name; ?></span>
                        <a href="tel:<?php echo $num; ?>"><?php echo $num; ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div>
                <h3>Visit Us</h3>
                <div class="contact-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <a href="<?php echo $config['business']['map_link']; ?>" target="_blank">
                        <?php echo $config['business']['address']; ?>
                    </a>
                </div>
                <div class="contact-row">
                    <i class="fas fa-clock"></i>
                    <span>Mon - Sun: 9:00 AM - 10:00 PM</span>
                </div>
            </div>

        </div>

        <div class="arham-bar">
            <span>Powered by</span>
            <a href="https://arhamprinters.pk" class="arham-link" target="_blank">
                <i class="fas fa-cube"></i> ARHAM PRINTERS
            </a>
            <span>| Tech Partner</span>
        </div>
    </footer>

    <div class="float-container">
        <a href="tel:<?php echo $config['business']['phone_primary']; ?>" class="fab fab-phone">
            <i class="fas fa-phone"></i>
        </a>
        <a href="https://wa.me/<?php echo $config['business']['phone_primary']; ?>" class="fab fab-wa">
            <i class="fab fa-whatsapp"></i>
        </a>
        <div class="fab fab-cart" onclick="toggleCart()">
            <i class="fas fa-shopping-basket"></i>
            <div class="badge" id="fabCount">0</div>
        </div>
    </div>

    <div class="cart-overlay" onclick="toggleCart()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-header">
            <h3>Your Selection</h3>
            <button onclick="toggleCart()" style="background:none; border:none; font-size:1.5rem;">&times;</button>
        </div>
        <div class="cart-body" id="cartBody">
            <p style="text-align:center; color:#999; margin-top:50px;">Your cart is empty</p>
        </div>
        <div class="cart-footer">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-weight:700;">
                <span>Total</span>
                <span id="cartTotal">Rs. 0</span>
            </div>
            <button class="btn-checkout" onclick="checkout()">
                Order on WhatsApp <i class="fab fa-whatsapp"></i>
            </button>
        </div>
    </div>

    <script>
        let cart = [];
        const currency = "<?php echo $config['business']['currency']; ?>";
        const phone = "<?php echo $config['business']['phone_primary']; ?>";

        function addToCart(id, name, price) {
            let item = cart.find(i => i.id === id);
            if(item) item.qty++;
            else cart.push({ id, name, price, qty: 1 });
            renderCart();
            toggleCart(true);
        }

        function updateQty(id, change) {
            let item = cart.find(i => i.id === id);
            if(item) {
                item.qty += change;
                if(item.qty <= 0) cart = cart.filter(i => i.id !== id);
                renderCart();
            }
        }

        function renderCart() {
            const body = document.getElementById('cartBody');
            const totalEl = document.getElementById('cartTotal');
            const countEl = document.getElementById('fabCount');
            
            let total = 0;
            let count = 0;
            let html = '';

            if(cart.length === 0) {
                html = `<p style="text-align:center; color:#999; margin-top:50px;">Your cart is empty</p>`;
            } else {
                cart.forEach(item => {
                    total += item.price * item.qty;
                    count += item.qty;
                    html += `
                        <div class="cart-item">
                            <div>
                                <div style="font-weight:600;">${item.name}</div>
                                <small>${currency} ${item.price}</small>
                            </div>
                            <div class="qty-box">
                                <button onclick="updateQty(${item.id}, -1)">-</button>
                                <span>${item.qty}</span>
                                <button onclick="updateQty(${item.id}, 1)">+</button>
                            </div>
                        </div>
                    `;
                });
            }

            body.innerHTML = html;
            totalEl.innerText = currency + " " + total.toLocaleString();
            countEl.innerText = count;
        }

        function toggleCart(forceOpen = false) {
            const drawer = document.getElementById('cartDrawer');
            const overlay = document.querySelector('.cart-overlay');
            if(forceOpen || !drawer.classList.contains('open')) {
                drawer.classList.add('open');
                overlay.classList.add('active');
            } else {
                drawer.classList.remove('open');
                overlay.classList.remove('active');
            }
        }

        function filterMenu(cat, btn) {
            document.querySelectorAll('.tab-chip').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.card').forEach(card => {
                card.style.display = (cat === 'all' || card.dataset.cat === cat) ? 'flex' : 'none';
            });
        }

        function checkout() {
            if(cart.length === 0) return alert("Cart is empty");
            let msg = `*NEW ORDER*\n------------------\n`;
            let total = 0;
            cart.forEach(i => {
                msg += `${i.qty}x ${i.name} - ${i.price*i.qty}\n`;
                total += i.price * i.qty;
            });
            msg += `\n*TOTAL: ${currency} ${total.toLocaleString()}*`;
            msg += `\n------------------\nI want to confirm this order.`;
            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
        }
    </script>
</body>
</html>