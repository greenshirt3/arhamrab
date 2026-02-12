<?php
// --- RUSTIC VILLA ENGINE ---
$json_file = 'data.json';
if (!file_exists($json_file)) die("Error: data.json not found.");
$raw_data = json_decode(file_get_contents($json_file), true);
$shop_data = reset($raw_data);
$s = $shop_data['settings'];
$c = $s['theme_colors'];

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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --gold: #c5a059; --paper: #fdfbf7; --text: #2c1810; }
        body { background-color: var(--paper); color: var(--text); font-family: 'Lato', sans-serif; background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png'); }
        
        h1, h2, h3, h4, h5 { font-family: 'Playfair Display', serif; }
        
        .navbar-rustic { background: #fff; box-shadow: 0 2px 20px rgba(0,0,0,0.05); padding: 20px 0; }
        .brand-rustic { font-size: 2rem; font-weight: 700; color: var(--text); letter-spacing: -1px; }
        
        .hero-rustic { padding: 100px 0; text-align: center; background: url('<?php echo img($shop_data['hero_banner']['imageFile']); ?>') center/cover fixed; position: relative; }
        .hero-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.4); }
        .hero-card { background: #fff; padding: 60px; max-width: 700px; margin: 0 auto; position: relative; border: 1px solid #ddd; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .hero-card::after { content:''; position: absolute; inset: 10px; border: 1px solid var(--gold); pointer-events: none; }
        
        .btn-gold { background: var(--gold); color: #fff; padding: 15px 40px; border-radius: 0; font-family: 'Playfair Display'; font-style: italic; font-size: 1.2rem; transition: 0.3s; border: none; }
        .btn-gold:hover { background: var(--text); color: var(--gold); }

        .cat-nav { border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 20px 0; margin-bottom: 50px; text-align: center; }
        .cat-link { color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 2px; margin: 0 15px; text-decoration: none; transition: 0.3s; }
        .cat-link.active, .cat-link:hover { color: var(--gold); border-bottom: 2px solid var(--gold); padding-bottom: 5px; }

        .rustic-card { background: #fff; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: 0.3s; height: 100%; }
        .rustic-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
        .card-img-top { height: 220px; object-fit: cover; border-bottom: 3px solid var(--gold); }
        .price { font-family: 'Playfair Display'; font-size: 1.5rem; color: var(--gold); font-style: italic; }

        /* Footer Split - Classic Style */
        .footer-wrap { background: #1a1a1a; color: #aaa; margin-top: 100px; }
        .foot-col-shop { padding: 80px 50px; border-right: 1px solid #333; text-align: center; }
        .foot-col-dev { padding: 80px 50px; background: #111; text-align: center; }
        .dev-logo { font-family: 'Playfair Display'; font-size: 2rem; color: #fff; letter-spacing: 2px; margin-bottom: 20px; }
        .social-circle { width: 40px; height: 40px; border: 1px solid #444; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #fff; margin: 0 5px; transition:0.3s; }
        .social-circle:hover { background: var(--gold); border-color: var(--gold); }

        .float-btn { position: fixed; bottom: 30px; right: 30px; background: var(--text); color: var(--gold); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; cursor: pointer; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-rustic sticky-top">
        <div class="container text-center justify-content-center">
            <a class="navbar-brand brand-rustic" href="#"><?php echo $s['shop_name']; ?></a>
        </div>
    </nav>

    <section class="hero-rustic">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-card">
                <span class="text-muted text-uppercase small ls-2">Est. 2025</span>
                <h1 class="display-4 my-3"><?php echo $shop_data['hero_banner']['headline']; ?></h1>
                <p class="text-muted mb-4 font-italic"><?php echo $shop_data['hero_banner']['subheadline']; ?></p>
                <a href="#menu" class="btn-gold">View Our Menu</a>
            </div>
        </div>
    </section>

    <div class="container mt-5" id="menu">
        <div class="cat-nav d-flex justify-content-center overflow-auto">
            <a href="#" class="cat-link active" onclick="filter('all', this); return false;">All Menu</a>
            <?php foreach($shop_data['categories'] as $cat => $items): 
                $slug = strtolower(str_replace([' ','&'], ['-',''], $cat)); ?>
                <a href="#" class="cat-link text-nowrap" onclick="filter('<?php echo $slug; ?>', this); return false;"><?php echo $cat; ?></a>
            <?php endforeach; ?>
        </div>

        <div class="row g-5">
            <?php foreach($shop_data['categories'] as $cat => $items): 
                $slug = strtolower(str_replace([' ','&'], ['-',''], $cat));
                foreach($items as $p):
                    $hasVar = !empty($p['variants']);
                    $json = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="col-md-6 col-lg-4 shop-item <?php echo $slug; ?>">
                <div class="rustic-card">
                    <img src="<?php echo img($p['imageFile']); ?>" class="card-img-top">
                    <div class="p-4 text-center">
                        <h5><?php echo $p['name']; ?></h5>
                        <p class="small text-muted mb-3"><?php echo $p['description']; ?></p>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="price"><?php echo $s['currency_symbol'].$p['base_price']; ?></span>
                            <button class="btn btn-outline-dark btn-sm rounded-0 text-uppercase" onclick="<?php echo $hasVar ? "openVar(this)" : "add(this)"; ?>" data-product='<?php echo $json; ?>'>
                                <?php echo $hasVar ? 'Select Option' : 'Add to Order'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; endforeach; ?>
        </div>
    </div>

    <footer class="footer-wrap">
        <div class="row g-0">
            <div class="col-md-6 foot-col-shop">
                <h3 class="text-white mb-4"><?php echo $s['shop_name']; ?></h3>
                <p><?php echo $s['address']; ?></p>
                <p class="mb-4"><?php echo $s['contact_number']; ?></p>
                <div>
                    <a href="#" class="social-circle"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" class="social-circle"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-md-6 foot-col-dev">
                <div class="dev-logo">ARHAM PRINTERS</div>
                <p class="small mb-4 w-75 mx-auto">Crafting premium digital experiences for businesses worldwide. Elevate your brand with our bespoke web solutions.</p>
                <a href="https://arhamprinters.pk" class="text-white text-uppercase small" style="letter-spacing:2px; border-bottom:1px solid #555; text-decoration:none;">Visit Agency</a>
            </div>
        </div>
    </footer>

    <div class="float-btn" data-bs-toggle="modal" data-bs-target="#cartModal"><i class="fa fa-utensils"></i><span class="position-absolute top-0 start-100 translate-middle badge bg-danger rounded-pill" id="cnt">0</span></div>
    
    <div class="modal fade" id="cartModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content rounded-0 border-0"><div class="modal-header border-0"><h5 class="modal-title font-italic">Your Selection</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="cl"></div><hr><input id="cn" class="form-control mb-2 rounded-0" placeholder="Name"><input id="cp" class="form-control mb-2 rounded-0" placeholder="Phone"><textarea id="ca" class="form-control mb-3 rounded-0" placeholder="Address"></textarea><button onclick="checkout()" class="btn-gold w-100">Place Order on WhatsApp</button></div></div></div></div>
    
    <div class="modal fade" id="varModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-0"><div class="modal-body text-center"><h4 id="vt" class="mb-4 font-italic"></h4><div id="vo" class="d-grid gap-2"></div></div></div></div></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const CFG={cur:"<?php echo $s['currency_symbol']; ?>",ph:"<?php echo $s['whatsapp_number']; ?>"};
        let cart=JSON.parse(localStorage.getItem('rcart'))||[],cp=null;
        function filter(c,b){$('.cat-link').removeClass('active');$(b).addClass('active');$('.shop-item').hide();(c==='all')?$('.shop-item').fadeIn():$('.'+c).fadeIn();}
        function add(e){push(JSON.parse($(e).attr('data-product')),null);}
        function openVar(e){cp=JSON.parse($(e).attr('data-product'));$('#vt').text(cp.name);$('#vo').empty();
        cp.variants.forEach(v=>$('#vo').append(`<button class="btn btn-outline-dark rounded-0" onclick="push(cp,'${v.name}',${cp.base_price+v.price_adjustment});$('#varModal').modal('hide')">${v.name} - ${CFG.cur}${cp.base_price+v.price_adjustment}</button>`));
        new bootstrap.Modal('#varModal').show();}
        function push(p,v,pr){let id=p.id+(v||''),x=cart.find(i=>i.id===id);x?x.q++:cart.push({id:id,n:p.name,v:v,p:pr||p.base_price,q:1});upd();}
        function upd(){localStorage.setItem('rcart',JSON.stringify(cart));$('#cnt').text(cart.reduce((a,b)=>a+b.q,0));
        let h='';cart.forEach((i,x)=>h+=`<div class="d-flex justify-content-between mb-2"><span>${i.q}x ${i.n} ${i.v||''}</span><span>${i.p*i.q} <i class="fa fa-times ms-2 text-muted" onclick="cart.splice(${x},1);upd()"></i></span></div>`);$('#cl').html(h||'Cart Empty');}
        function checkout(){if(!cart.length)return;let m='New Order:\n';cart.forEach(i=>m+=`${i.q}x ${i.n} ${i.v||''} \n`);
        window.open(`https://wa.me/${CFG.ph}?text=${encodeURIComponent(m+'\nDetails: '+$('#cn').val()+' '+$('#ca').val())}`);cart=[];upd();bootstrap.Modal.getInstance('#cartModal').hide();}
        upd();
    </script>
</body>
</html>