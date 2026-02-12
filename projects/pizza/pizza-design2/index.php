<?php
// --- CYBER-GLOW ENGINE ---
$json_file = 'data.json';
if (!file_exists($json_file)) die("Error: data.json not found.");
$raw_data = json_decode(file_get_contents($json_file), true);
$shop_data = reset($raw_data);
$s = $shop_data['settings'];
$c = $s['theme_colors'];

// Universal SEO
if (isset($shop_data)) { $data = $shop_data; }
$seo_paths = ['seo.php', 'seo/seo.php', '../seo.php', '../seo/seo.php'];
foreach ($seo_paths as $path) { if (file_exists(__DIR__ . '/' . $path)) { include_once __DIR__ . '/' . $path; break; } }

function img($u) { return strpos($u, 'http') === 0 ? $u : "https://arhamprinters.pk/" . ltrim($u, '/'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $s['shop_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --neon: <?php echo $c['primary']; ?>;
            --dark: #050505;
            --panel: #0a0a0a;
            --border: 1px solid rgba(<?php echo hexdec(substr($c['primary'],1,2)).','.hexdec(substr($c['primary'],3,2)).','.hexdec(substr($c['primary'],5,2)); ?>, 0.3);
        }
        body { background: var(--dark); color: #fff; font-family: 'Rajdhani', sans-serif; overflow-x: hidden; }
        
        /* Cyber UI */
        .font-cyber { font-family: 'Orbitron', sans-serif; letter-spacing: 2px; }
        .neon-text { color: var(--neon); text-shadow: 0 0 10px var(--neon); }
        .neon-box { border: var(--border); background: rgba(20,20,20,0.8); box-shadow: 0 0 15px rgba(0,0,0,0.5); backdrop-filter: blur(5px); }
        
        /* Navbar */
        .nav-cyber { background: rgba(0,0,0,0.9); border-bottom: 1px solid var(--neon); padding: 15px 0; }
        .nav-link { color: #888 !important; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        .nav-link:hover { color: var(--neon) !important; text-shadow: 0 0 8px var(--neon); }

        /* Hero */
        .hero-cyber {
            height: 85vh; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;
            background: radial-gradient(circle at center, rgba(<?php echo hexdec(substr($c['primary'],1,2)).','.hexdec(substr($c['primary'],3,2)).','.hexdec(substr($c['primary'],5,2)); ?>, 0.1), transparent 70%);
        }
        .hero-img { position: absolute; width: 100%; height: 100%; object-fit: cover; opacity: 0.3; filter: grayscale(100%); z-index: -1; }
        .glitch-title { font-size: 5rem; font-weight: 900; text-transform: uppercase; position: relative; color: #fff; }
        .btn-cyber {
            background: transparent; color: var(--neon); border: 1px solid var(--neon);
            padding: 15px 40px; font-family: 'Orbitron'; text-transform: uppercase;
            transition: 0.3s; position: relative; overflow: hidden;
        }
        .btn-cyber:hover { background: var(--neon); color: #000; box-shadow: 0 0 30px var(--neon); }

        /* Products */
        .cyber-card {
            background: var(--panel); border: 1px solid #222; position: relative; overflow: hidden; transition: 0.3s;
            clip-path: polygon(10% 0, 100% 0, 100% 90%, 90% 100%, 0 100%, 0 10%);
        }
        .cyber-card:hover { border-color: var(--neon); transform: translateY(-5px); box-shadow: 0 0 20px rgba(<?php echo hexdec(substr($c['primary'],1,2)).','.hexdec(substr($c['primary'],3,2)).','.hexdec(substr($c['primary'],5,2)); ?>, 0.2); }
        .cyber-img { width: 100%; height: 200px; object-fit: cover; filter: sepia(50%); transition: 0.5s; }
        .cyber-card:hover .cyber-img { filter: sepia(0%); transform: scale(1.1); }
        .price-badge { position: absolute; top: 10px; right: 10px; background: var(--neon); color: #000; font-weight: bold; padding: 5px 15px; font-family: 'Orbitron'; }

        /* Footer Split */
        .footer-split { border-top: 2px solid var(--neon); background: #020202; }
        .footer-left { padding: 50px; border-right: 1px solid #222; }
        .footer-right { padding: 50px; background: repeating-linear-gradient(45deg, #050505, #050505 10px, #080808 10px, #080808 20px); }
        .arham-tag { font-size: 3rem; font-weight: 900; color: #333; -webkit-text-stroke: 1px var(--neon); letter-spacing: 5px; opacity: 0.5; }

        /* Floating Cart */
        .hud-cart {
            position: fixed; bottom: 20px; right: 20px; border: 2px solid var(--neon); background: #000; color: var(--neon);
            width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; clip-path: polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%);
            font-size: 1.5rem; cursor: pointer; z-index: 100; box-shadow: 0 0 15px var(--neon);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg nav-cyber fixed-top">
        <div class="container">
            <a class="navbar-brand font-cyber neon-text" href="#"><?php echo $s['shop_name']; ?></a>
            <div class="d-flex text-white small align-items-center">
                <span class="me-3 d-none d-md-block"><i class="fa fa-phone me-2"></i> <?php echo $s['contact_number']; ?></span>
                <a href="#cartModal" data-bs-toggle="modal" class="btn btn-sm btn-outline-light rounded-0" style="border-color:var(--neon); color:var(--neon)">
                    CART [<span id="navCount">0</span>]
                </a>
            </div>
        </div>
    </nav>

    <section class="hero-cyber">
        <img src="<?php echo img($shop_data['hero_banner']['imageFile']); ?>" class="hero-img">
        <div class="text-center z-2" data-aos="zoom-in">
            <h4 class="font-cyber letter-spacing-4 text-white-50">SYSTEM ONLINE</h4>
            <h1 class="glitch-title mb-4"><?php echo $shop_data['hero_banner']['headline']; ?></h1>
            <a href="#menu" class="btn-cyber">ACCESS MENU_</a>
        </div>
    </section>

    <section id="menu" class="py-5">
        <div class="container">
            <div class="d-flex overflow-auto mb-5 gap-3 pb-2" style="scrollbar-width: thin; scrollbar-color: var(--neon) #000;">
                <button class="btn btn-outline-light rounded-0 px-4 py-2 font-cyber active cat-btn" onclick="filter('all', this)">ALL_DATA</button>
                <?php foreach($shop_data['categories'] as $cat => $items): 
                    $slug = strtolower(str_replace([' ','&'], ['-',''], $cat)); ?>
                    <button class="btn btn-outline-light rounded-0 px-4 py-2 font-cyber cat-btn" onclick="filter('<?php echo $slug; ?>', this)"><?php echo strtoupper($cat); ?></button>
                <?php endforeach; ?>
            </div>

            <div class="row g-4">
                <?php foreach($shop_data['categories'] as $cat => $items): 
                    $slug = strtolower(str_replace([' ','&'], ['-',''], $cat));
                    foreach($items as $p):
                        $hasVar = !empty($p['variants']);
                        $json = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-md-6 col-lg-3 shop-item <?php echo $slug; ?>" data-aos="fade-up">
                    <div class="cyber-card h-100">
                        <div class="position-relative overflow-hidden">
                            <img src="<?php echo img($p['imageFile']); ?>" class="cyber-img">
                            <div class="price-badge"><?php echo $s['currency_symbol'].$p['base_price']; ?></div>
                        </div>
                        <div class="p-4">
                            <h5 class="font-cyber text-white mb-1"><?php echo $p['name']; ?></h5>
                            <p class="text-white-50 small mb-3"><?php echo $p['description']; ?></p>
                            <button class="btn-cyber w-100 btn-sm" onclick="<?php echo $hasVar ? "openVar(this)" : "add(this)"; ?>" data-product='<?php echo $json; ?>'>
                                <?php echo $hasVar ? 'CONFIGURATE' : 'ADD TO SYSTEM'; ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer-split">
        <div class="row g-0">
            <div class="col-lg-6 footer-left">
                <h2 class="font-cyber neon-text mb-4"><?php echo $s['shop_name']; ?></h2>
                <div class="d-flex gap-4 mb-4 text-white-50">
                    <a href="#" class="text-white h4"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="text-white h4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white h4"><i class="fa fa-location-dot"></i></a>
                </div>
                <p class="text-white-50 small mb-0">POWERED BY NEXT-GEN TECH. <?php echo $s['address']; ?></p>
            </div>
            <div class="col-lg-6 footer-right position-relative overflow-hidden">
                <div class="arham-tag position-absolute top-50 start-50 translate-middle text-nowrap">ARHAM PRINTERS</div>
                <div class="position-relative z-2 text-end">
                    <h5 class="font-cyber text-white mb-2">NEED A SITE LIKE THIS?</h5>
                    <p class="small text-white-50 mb-3">We build high-performance digital assets.</p>
                    <a href="https://arhamprinters.pk" class="btn btn-sm btn-outline-light rounded-0 font-cyber">INITIALIZE PROJECT -></a>
                </div>
            </div>
        </div>
    </footer>

    <div class="hud-cart" data-bs-toggle="modal" data-bs-target="#cartModal"><i class="fa fa-shopping-basket"></i></div>
    
    <div class="modal fade" id="varModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-black border border-white text-white rounded-0"><div class="modal-body"><h4 id="vt" class="font-cyber neon-text"></h4><div id="vo" class="d-grid gap-2 my-3"></div><button id="va" class="btn-cyber w-100">CONFIRM</button></div></div></div></div>
    <div class="modal fade" id="cartModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content bg-black border border-white text-white rounded-0"><div class="modal-header border-bottom border-secondary"><h5 class="font-cyber">DATA LOG</h5><button class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="cl" class="mb-3"></div><input id="cn" class="form-control bg-dark text-white rounded-0 mb-2" placeholder="ID Name"><input id="cp" class="form-control bg-dark text-white rounded-0 mb-2" placeholder="Comm Link (Phone)"><textarea id="ca" class="form-control bg-dark text-white rounded-0 mb-3" placeholder="Drop Coordinates"></textarea><button onclick="checkout()" class="btn-cyber w-100">TRANSMIT ORDER</button></div></div></div></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init(); const CFG={cur:"<?php echo $s['currency_symbol']; ?>",ph:"<?php echo $s['whatsapp_number']; ?>"};
        let cart=JSON.parse(localStorage.getItem('cart'))||[],cp=null;
        
        function filter(c,b){$('.cat-btn').removeClass('active neon-text border-white');$(b).addClass('active neon-text border-white');
        $('.shop-item').hide();(c==='all')?$('.shop-item').fadeIn():$('.'+c).fadeIn();}
        
        function add(e){push(JSON.parse($(e).attr('data-product')),null);}
        function openVar(e){cp=JSON.parse($(e).attr('data-product'));$('#vt').text(cp.name);$('#vo').empty();
        cp.variants.forEach((v,i)=>$('#vo').append(`<button class="btn btn-outline-secondary text-start rounded-0" onclick="push(cp,'${v.name}',${cp.base_price+v.price_adjustment});$('#varModal').modal('hide')">${v.name} - ${CFG.cur}${cp.base_price+v.price_adjustment}</button>`));
        new bootstrap.Modal('#varModal').show();}
        
        function push(p,v,pr){let id=p.id+(v||''),x=cart.find(i=>i.id===id);x?x.q++:cart.push({id:id,n:p.name,v:v,p:pr||p.base_price,q:1});upd();}
        function upd(){localStorage.setItem('cart',JSON.stringify(cart));$('#navCount').text(cart.reduce((a,b)=>a+b.q,0));
        let h='',t=0;cart.forEach((i,x)=>{t+=i.p*i.q;h+=`<div class="d-flex justify-content-between mb-2 border-bottom border-secondary pb-2"><div>${i.n} <small class="text-muted">${i.v||''}</small></div><div>${i.q} x ${i.p} <i class="fa fa-times text-danger ms-2" style="cursor:pointer" onclick="cart.splice(${x},1);upd()"></i></div></div>`});
        $('#cl').html(h||'Empty');}
        function checkout(){if(!cart.length)return;let m=`ORDER:\n`;cart.forEach(i=>m+=`${i.q}x ${i.n} ${i.v||''} \n`);
        window.open(`https://wa.me/${CFG.ph}?text=${encodeURIComponent(m+'\nInfo: '+$('#cn').val()+' '+$('#ca').val())}`);cart=[];upd();bootstrap.Modal.getInstance('#cartModal').hide();}
        upd();
    </script>
</body>
</html>