<?php
// =================================================================
// NEXT-GEN SINGLE SHOP ENGINE | DEVELOPED BY ARHAM PRINTERS
// =================================================================

// 1. Load Data
$json_file = 'data.json';
if (!file_exists($json_file)) { die("Error: data.json not found."); }
$raw_data = json_decode(file_get_contents($json_file), true);

// 2. Smart Fetch: Get the first shop automatically
$shop_data = reset($raw_data);
if (!$shop_data) { die("Error: Invalid JSON format."); }

$s = $shop_data['settings'];
$c = $s['theme_colors'];

// --- UNIVERSAL SEO LOADER ---
if (isset($shop_data)) { $data = $shop_data; }
$seo_paths = ['seo.php', 'seo/seo.php', '../seo.php', '../seo/seo.php', '../../seo.php'];
foreach ($seo_paths as $path) { if (file_exists(__DIR__ . '/' . $path)) { include_once __DIR__ . '/' . $path; break; } }
// ----------------------------

function getImg($url) {
    if (strpos($url, 'http') === 0) return $url;
    return "https://arhamprinters.pk/" . ltrim($url, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* =========================================
           THEME ENGINE
           ========================================= */
        :root {
            --primary: <?php echo $c['primary']; ?>;
            --secondary: <?php echo $c['secondary']; ?>;
            --dark: <?php echo $c['dark']; ?>;
            --light: <?php echo $c['light']; ?>;
            --accent: <?php echo $c['accent']; ?>;
            --font-head: 'Bebas Neue', sans-serif;
            --font-body: 'Outfit', sans-serif;
            --font-tech: 'Rajdhani', sans-serif;
            --glass: rgba(20, 20, 20, 0.6);
            --glass-border: 1px solid rgba(255, 255, 255, 0.1);
            --neon-glow: 0 0 15px rgba(<?php echo hexdec(substr($c['primary'],1,2)).','.hexdec(substr($c['primary'],3,2)).','.hexdec(substr($c['primary'],5,2)); ?>, 0.5);
        }

        body {
            background-color: var(--dark);
            color: var(--light);
            font-family: var(--font-body);
            overflow-x: hidden;
        }

        /* --- LOADER --- */
        #loader {
            position: fixed; inset: 0; background: #000; z-index: 9999;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transition: opacity 0.5s;
        }
        .loader-logo { font-family: var(--font-head); font-size: 3rem; color: var(--primary); letter-spacing: 2px; animation: pulse 1s infinite; }
        
        /* --- NAVBAR --- */
        .navbar-glass {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: var(--glass-border);
            padding: 15px 0;
        }
        .navbar-brand { font-family: var(--font-head); font-size: 2rem; color: #fff !important; text-shadow: 0 0 20px var(--primary); }
        .nav-link { color: #aaa !important; font-family: var(--font-tech); text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; }
        .nav-link:hover { color: var(--primary) !important; text-shadow: 0 0 10px var(--primary); }

        /* --- HERO --- */
        .hero-section {
            height: 90vh; position: relative; display: flex; align-items: center; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background: url('<?php echo getImg($shop_data['hero_banner']['imageFile']); ?>') center/cover;
            filter: brightness(0.3) contrast(1.2);
            transform: scale(1.1);
            animation: zoomSlow 20s infinite alternate;
        }
        @keyframes zoomSlow { to { transform: scale(1.2); } }
        
        .hero-title {
            font-family: var(--font-head);
            font-size: clamp(4rem, 12vw, 9rem);
            line-height: 0.85;
            text-transform: uppercase;
            background: linear-gradient(to bottom, #fff, #aaa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 10px 30px rgba(0,0,0,0.5));
        }
        .hero-highlight { color: var(--primary); -webkit-text-fill-color: var(--primary); text-shadow: 0 0 40px var(--primary); }

        /* --- MENU CARDS (3D HOVER) --- */
        .food-card {
            background: var(--glass);
            border: var(--glass-border);
            border-radius: 24px;
            padding: 25px;
            position: relative;
            margin-top: 80px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
        }
        .food-card:hover {
            transform: translateY(-15px) rotateX(5deg);
            border-color: var(--primary);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5), inset 0 0 20px rgba(255,255,255,0.05);
        }
        
        .food-img-box {
            width: 180px; height: 180px; margin: -90px auto 20px;
            border-radius: 50%; border: 5px solid var(--dark);
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
            overflow: hidden; transition: 0.5s;
            position: relative; z-index: 2;
        }
        .food-img-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .food-card:hover .food-img-box img { transform: scale(1.1) rotate(10deg); }
        .food-card:hover .food-img-box { border-color: var(--primary); box-shadow: 0 0 30px var(--primary); }

        /* --- FOOTER (The Professional Split) --- */
        .footer-split {
            background: #050505; border-top: 1px solid #222;
            position: relative; overflow: hidden;
        }
        .footer-col { padding: 60px 40px; position: relative; z-index: 2; }
        
        /* Left Side: Shop */
        .shop-side { background: radial-gradient(circle at 0% 0%, rgba(255,255,255,0.03), transparent 50%); border-right: 1px solid #222; }
        .social-btn {
            width: 50px; height: 50px; border-radius: 50%; background: #111; border: 1px solid #333;
            display: inline-flex; align-items: center; justify-content: center; color: #fff;
            margin-right: 10px; transition: 0.3s; text-decoration: none; font-size: 1.2rem;
        }
        .social-btn:hover { background: var(--primary); color: #000; transform: translateY(-5px); border-color: var(--primary); }

        /* Right Side: Arham Printers */
        .dev-side { 
            background: radial-gradient(circle at 100% 100%, rgba(255, 215, 0, 0.05), transparent 60%); 
            position: relative;
        }
        .dev-tag {
            background: linear-gradient(90deg, #FFD700, #FFA500);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            font-weight: 900; font-family: var(--font-head); letter-spacing: 1px;
        }
        .dev-services span {
            background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 20px;
            font-size: 0.75rem; margin-right: 5px; margin-bottom: 5px; display: inline-block;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-dev {
            background: transparent; color: #FFD700; border: 1px solid #FFD700;
            padding: 12px 30px; font-family: var(--font-tech); text-transform: uppercase;
            font-weight: 700; letter-spacing: 2px; transition: 0.3s; text-decoration: none;
            display: inline-block; margin-top: 20px;
        }
        .btn-dev:hover { background: #FFD700; color: #000; box-shadow: 0 0 30px rgba(255, 215, 0, 0.4); }

        /* --- FLOATING CART --- */
        .float-cart {
            position: fixed; bottom: 30px; right: 30px; width: 70px; height: 70px;
            background: var(--primary); color: var(--dark); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem;
            box-shadow: 0 0 30px var(--primary); z-index: 1000; cursor: pointer;
            transition: 0.3s; animation: bounce 2s infinite;
        }
        .float-cart:hover { transform: scale(1.1); animation: none; }
        
        /* --- UTILS --- */
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% {transform: translateY(0);} 40% {transform: translateY(-10px);} 60% {transform: translateY(-5px);} }
        .fly-img { position: fixed; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; pointer-events: none; border: 2px solid var(--primary); object-fit: cover; }
        
        /* Mobile Fixes */
        @media (max-width: 768px) {
            .shop-side { border-right: none; border-bottom: 1px solid #222; }
            .hero-section { height: 70vh; }
        }
    </style>
</head>
<body>

    <div id="loader">
        <div class="loader-logo"><?php echo $s['shop_name']; ?></div>
        <small class="text-muted mt-2 letter-spacing-2">LOADING MENU...</small>
    </div>

    <nav class="navbar navbar-expand-lg navbar-glass fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo $s['shop_name']; ?></a>
            <div class="d-flex align-items-center">
                <a href="https://wa.me/<?php echo $s['whatsapp_number']; ?>" class="btn btn-outline-light rounded-pill px-4 btn-sm d-none d-md-block">
                    <i class="fab fa-whatsapp me-2"></i> Order Now
                </a>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="container position-relative z-2 text-center" data-aos="zoom-in" data-aos-duration="1500">
            <h5 class="text-uppercase letter-spacing-4 mb-3" style="color:var(--primary);">Welcome to Taste</h5>
            <h1 class="hero-title"><?php echo $shop_data['hero_banner']['headline']; ?> <br> <span class="hero-highlight">DELICIOUS</span></h1>
            <p class="lead text-white-50 w-75 mx-auto mb-5"><?php echo $shop_data['hero_banner']['subheadline']; ?></p>
            <a href="#menu" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-uppercase" style="letter-spacing: 2px;">View Full Menu</a>
        </div>
    </section>

    <section id="menu" class="py-5" style="position: relative; z-index: 2;">
        <div class="container">
            
            <div class="d-flex overflow-auto pb-4 mb-4 gap-2 justify-content-lg-center" style="scrollbar-width:none;">
                <button class="btn btn-outline-light rounded-pill px-4 active cat-filter" onclick="filterMenu('all', this)">All Items</button>
                <?php foreach($shop_data['categories'] as $cat => $items): 
                    $slug = strtolower(str_replace([' ','&'], ['-',''], $cat));
                ?>
                <button class="btn btn-outline-light rounded-pill px-4 cat-filter text-nowrap" onclick="filterMenu('<?php echo $slug; ?>', this)"><?php echo $cat; ?></button>
                <?php endforeach; ?>
            </div>

            <div class="row g-4">
                <?php foreach($shop_data['categories'] as $cat => $items): 
                    $slug = strtolower(str_replace([' ','&'], ['-',''], $cat));
                    foreach($items as $prod):
                        $hasVar = !empty($prod['variants']);
                        $json = htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-md-6 col-lg-3 menu-item <?php echo $slug; ?>" data-aos="fade-up">
                    <div class="food-card">
                        <?php if(!empty($prod['is_deal'])): ?>
                            <div class="position-absolute top-0 end-0 m-3 badge bg-danger rounded-pill px-3">HOT DEAL</div>
                        <?php endif; ?>
                        
                        <div class="food-img-box">
                            <img src="<?php echo getImg($prod['imageFile']); ?>" id="img-<?php echo $prod['id']; ?>">
                        </div>
                        
                        <div class="text-center">
                            <h4 class="font-head text-white mb-1"><?php echo $prod['name']; ?></h4>
                            <p class="small text-white-50 mb-3 text-truncate"><?php echo $prod['description']; ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center bg-dark rounded-pill p-1 ps-3 border border-secondary">
                                <div class="fw-bold text-primary"><?php echo $s['currency_symbol'] . $prod['base_price']; ?></div>
                                <button class="btn btn-light rounded-circle shadow-lg" style="width: 40px; height: 40px;" 
                                        onclick="<?php echo $hasVar ? "openVar(this)" : "addToCart(this)"; ?>" data-product='<?php echo $json; ?>'>
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; endforeach; ?>
            </div>

        </div>
    </section>

    <footer class="footer-split">
        <div class="row g-0">
            
            <div class="col-lg-6 shop-side footer-col">
                <h2 class="font-head text-white display-5 mb-4"><?php echo $s['shop_name']; ?></h2>
                <p class="text-white-50 mb-5 w-75">
                    We serve the best food in town. Crafted with passion, delivered with love. Experience the taste of authentic recipes.
                </p>
                
                <div class="d-flex flex-wrap gap-3 mb-5">
                    <a href="https://wa.me/<?php echo $s['whatsapp_number']; ?>" class="social-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="tel:<?php echo $s['contact_number']; ?>" class="social-btn" title="Call Us"><i class="fa fa-phone"></i></a>
                    <a href="https://maps.google.com/?q=<?php echo urlencode($s['address']); ?>" target="_blank" class="social-btn" title="Location"><i class="fa fa-location-dot"></i></a>
                </div>

                <div class="text-white-50 small">
                    <i class="fa fa-map-pin me-2 text-primary"></i> <?php echo $s['address']; ?>
                </div>
            </div>

            <div class="col-lg-6 dev-side footer-col">
                <div class="d-flex align-items-center mb-3">
                    <i class="fa fa-laptop-code text-warning fs-3 me-3"></i>
                    <h5 class="m-0 text-uppercase letter-spacing-2 text-white">Developed By</h5>
                </div>
                
                <h2 class="dev-tag display-4 mb-3">ARHAM PRINTERS</h2>
                <h5 class="text-white mb-4">Want a Premium Digital Menu like this?</h5>
                
                <p class="text-white-50 mb-4 small">
                    Boost your business sales with our High-Performance Digital Solutions. 
                    We build modern websites, branding materials, and marketing assets that attract customers.
                </p>

                <div class="dev-services mb-4">
                    <span>Web Development</span>
                    <span>Digital Menus</span>
                    <span>Flex Printing</span>
                    <span>Social Media Marketing</span>
                    <span>SEO & Branding</span>
                </div>

                <a href="https://wa.me/923006238233?text=I%20want%20a%20website%20like%20this" target="_blank" class="btn-dev">
                    Get Your Website <i class="fa fa-arrow-right ms-2"></i>
                </a>
            </div>

        </div>
        
        <div class="text-center py-3 bg-black border-top border-secondary text-white-50 small">
            &copy; <?php echo date('Y'); ?> <?php echo $s['shop_name']; ?> | Powered by <a href="https://arhamprinters.pk" class="text-decoration-none text-warning">Arham Printers</a>
        </div>
    </footer>

    <div class="float-cart" data-bs-toggle="modal" data-bs-target="#cartModal">
        <i class="fa fa-basket-shopping"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" id="cartBadge">0</span>
    </div>

    <div class="modal fade" id="varModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title font-head">Customize</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4 id="vTitle" class="mb-3 text-primary"></h4>
                    <div id="vOptions" class="d-grid gap-2"></div>
                </div>
                <div class="modal-footer border-secondary justify-content-between">
                    <h3 class="font-head m-0" id="vPrice"></h3>
                    <button class="btn btn-light fw-bold" id="vAddBtn">ADD TO CART</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title font-head fs-3">Your Basket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-7 p-3 border-end border-secondary">
                            <div id="cartItems" style="max-height: 400px; overflow-y: auto;"></div>
                        </div>
                        <div class="col-md-5 p-4 bg-black">
                            <h6 class="text-uppercase text-secondary small mb-3">Delivery Info</h6>
                            <input type="text" id="cName" class="form-control bg-dark text-white border-secondary mb-2" placeholder="Full Name">
                            <input type="tel" id="cPhone" class="form-control bg-dark text-white border-secondary mb-2" placeholder="Phone Number">
                            <textarea id="cAddr" class="form-control bg-dark text-white border-secondary mb-3" rows="2" placeholder="Address"></textarea>
                            
                            <div class="d-flex justify-content-between mb-1 text-white-50"><span>Subtotal:</span> <span id="cSub">0</span></div>
                            <div class="d-flex justify-content-between mb-3 text-white-50"><span>Delivery:</span> <span id="cDel">0</span></div>
                            <div class="d-flex justify-content-between fs-4 fw-bold text-white border-top border-secondary pt-3"><span>Total:</span> <span id="cTot" class="text-primary">0</span></div>
                            
                            <button onclick="checkout()" class="btn btn-success w-100 mt-4 fw-bold py-3">
                                <i class="fab fa-whatsapp me-2"></i> CONFIRM ORDER
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        // Init
        window.addEventListener('load', () => { 
            document.getElementById('loader').style.opacity = '0'; 
            setTimeout(()=>document.getElementById('loader').remove(), 500); 
        });
        AOS.init({ duration: 800, once: true });

        // Config
        const CFG = { 
            curr: "<?php echo $s['currency_symbol']; ?>", 
            fee: <?php echo $s['delivery_fee']; ?>, 
            free: <?php echo $s['free_delivery_over']; ?>, 
            phone: "<?php echo $s['whatsapp_number']; ?>" 
        };
        let cart = JSON.parse(localStorage.getItem('myCart')) || [], curProd = null;

        // Filter
        function filterMenu(cat, btn) {
            document.querySelectorAll('.cat-filter').forEach(b => b.classList.remove('active','bg-light','text-dark'));
            btn.classList.add('active','bg-light','text-dark');
            document.querySelectorAll('.menu-item').forEach(el => {
                el.style.display = (cat === 'all' || el.classList.contains(cat)) ? 'block' : 'none';
            });
            AOS.refresh();
        }

        // Animation
        function flyImg(btn, imgId) {
            const src = document.getElementById(imgId);
            const dest = document.querySelector('.float-cart');
            if(src && dest) {
                const fly = src.cloneNode();
                fly.classList.add('fly-img');
                document.body.appendChild(fly);
                const s = src.getBoundingClientRect(), d = dest.getBoundingClientRect();
                gsap.set(fly, { top: s.top, left: s.left, width: s.width });
                gsap.to(fly, { 
                    top: d.top, left: d.left, width: 20, height: 20, opacity: 0, duration: 0.8, ease: "power2.inOut",
                    onComplete: () => { fly.remove(); gsap.fromTo('#cartBadge', {scale:1.5}, {scale:1, duration:0.3}); }
                });
            }
        }

        // Logic
        function addToCart(btn) {
            const p = JSON.parse(btn.getAttribute('data-product'));
            flyImg(btn, 'img-'+p.id);
            pushItem(p, null, p.base_price);
        }

        function openVar(btn) {
            const p = JSON.parse(btn.getAttribute('data-product'));
            curProd = p;
            document.getElementById('vTitle').innerText = p.name;
            const con = document.getElementById('vOptions'); con.innerHTML = '';
            
            p.variants.forEach((v,i) => {
                const price = p.base_price + v.price_adjustment;
                con.innerHTML += `
                    <input type="radio" class="btn-check" name="vo" id="v${i}" ${i===0?'checked':''} data-n="${v.name}" data-p="${price}">
                    <label class="btn btn-outline-light d-flex justify-content-between p-3" for="v${i}">
                        <span>${v.name}</span> <b>${CFG.curr}${price}</b>
                    </label>`;
            });
            updPrice();
            con.addEventListener('change', updPrice);
            new bootstrap.Modal('#varModal').show();
        }

        function updPrice() {
            const el = document.querySelector('input[name="vo"]:checked');
            if(el) document.getElementById('vPrice').innerText = CFG.curr + el.dataset.p;
        }

        document.getElementById('vAddBtn').addEventListener('click', () => {
            const el = document.querySelector('input[name="vo"]:checked');
            pushItem(curProd, el.dataset.n, parseInt(el.dataset.p));
            bootstrap.Modal.getInstance('#varModal').hide();
            gsap.fromTo('#cartBadge', {scale:1.5}, {scale:1, duration:0.3});
        });

        function pushItem(p, v, pr) {
            const id = p.id + (v||'');
            const ex = cart.find(x => x.uid === id);
            if(ex) ex.qty++; else cart.push({uid:id, name:p.name, var:v, price:pr, img:p.imageFile, qty:1});
            save();
        }

        function modQty(id, n) {
            const i = cart.findIndex(x => x.uid === id);
            if(i > -1) { cart[i].qty += n; if(cart[i].qty <= 0) cart.splice(i, 1); save(); }
        }

        function save() { localStorage.setItem('myCart', JSON.stringify(cart)); render(); }

        function render() {
            let tot=0, cnt=0, html='';
            if(cart.length === 0) html = '<div class="text-center py-5 text-muted">Basket is Empty</div>';
            
            cart.forEach(i => {
                tot += i.price * i.qty; cnt += i.qty;
                let img = i.img.startsWith('http') ? i.img : 'https://arhamprinters.pk/'+i.img;
                html += `
                    <div class="d-flex align-items-center mb-3 bg-black p-2 rounded border border-secondary">
                        <img src="${img}" class="rounded-circle me-3" style="width:50px;height:50px;object-fit:cover;">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${i.name}</div>
                            <div class="small text-muted">${i.var||''}</div>
                            <div class="text-primary">${CFG.curr}${i.price}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-secondary" onclick="modQty('${i.uid}',-1)">-</button>
                            <span class="mx-2 fw-bold">${i.qty}</span>
                            <button class="btn btn-sm btn-primary" onclick="modQty('${i.uid}',1)">+</button>
                        </div>
                    </div>`;
            });
            
            document.getElementById('cartItems').innerHTML = html;
            document.getElementById('cartBadge').innerText = cnt;
            
            const fee = (tot >= CFG.free || tot === 0) ? 0 : CFG.fee;
            document.getElementById('cSub').innerText = CFG.curr + tot;
            document.getElementById('cDel').innerText = fee === 0 ? 'Free' : CFG.curr + fee;
            document.getElementById('cTot').innerText = CFG.curr + (tot + fee);
        }

        function checkout() {
            if(cart.length === 0) return alert("Cart is empty");
            const n=document.getElementById('cName').value, p=document.getElementById('cPhone').value, a=document.getElementById('cAddr').value;
            if(!n || !p || !a) return alert("Fill all details");
            
            let msg = `*NEW ORDER* %0aName: ${n} %0aPhone: ${p} %0aAddr: ${a} %0a----------------%0a`;
            cart.forEach(i => msg += `${i.qty}x ${i.name} ${i.var||''} @ ${i.price} %0a`);
            msg += `----------------%0aTotal: ${document.getElementById('cTot').innerText}`;
            
            window.open(`https://wa.me/${CFG.phone}?text=${msg}`, '_blank');
            cart = []; save(); bootstrap.Modal.getInstance('#cartModal').hide();
        }

        render();
    </script>
</body>
</html>