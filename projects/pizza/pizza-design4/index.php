<?php
// --- MODERN POP ENGINE ---
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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --pop: <?php echo $c['primary']; ?>; --bg: #f4f5f7; }
        body { background: var(--bg); font-family: 'DM Sans', sans-serif; color: #333; }
        
        .sidebar { height: 100vh; position: sticky; top: 0; background: #fff; padding: 40px; overflow-y: auto; border-right: 1px solid #eee; }
        .main-content { padding: 40px; }
        
        .brand-pop { font-size: 1.8rem; font-weight: 700; color: #000; text-decoration: none; display: block; margin-bottom: 40px; }
        .nav-pills .nav-link { color: #555; font-weight: 500; padding: 12px 20px; border-radius: 12px; margin-bottom: 10px; text-align: left; }
        .nav-pills .nav-link.active { background: var(--pop); color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .hero-pop { background: #fff; border-radius: 24px; padding: 40px; display: flex; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 40px; }
        .hero-pop img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-right: 30px; }
        
        .pop-card { background: #fff; border-radius: 20px; padding: 20px; border: 1px solid #f0f0f0; transition: 0.2s; height: 100%; cursor: pointer; }
        .pop-card:hover { transform: translateY(-5px); border-color: var(--pop); }
        .pop-img { width: 100%; height: 180px; object-fit: cover; border-radius: 15px; margin-bottom: 15px; }
        .pop-price { font-weight: 700; color: var(--pop); font-size: 1.1rem; }
        .btn-circle { width: 40px; height: 40px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: none; color: #333; transition: 0.2s; }
        .pop-card:hover .btn-circle { background: var(--pop); color: #fff; }

        /* Footer Grid */
        .grid-footer { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 60px; }
        .footer-box { background: #fff; padding: 30px; border-radius: 24px; }
        .dev-box { background: #222; color: #fff; padding: 30px; border-radius: 24px; }
        
        /* Mobile */
        @media(max-width: 991px) {
            .sidebar { height: auto; position: relative; padding: 20px; }
            .grid-footer { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="container-fluid g-0">
        <div class="row g-0">
            <div class="col-lg-3 sidebar d-none d-lg-block">
                <a href="#" class="brand-pop"><?php echo $s['shop_name']; ?></a>
                <div class="nav flex-column nav-pills">
                    <a class="nav-link active" href="#" onclick="filter('all', this)">All Items</a>
                    <?php foreach($shop_data['categories'] as $cat => $items): 
                        $slug = strtolower(str_replace([' ','&'], ['-',''], $cat)); ?>
                        <a class="nav-link" href="#" onclick="filter('<?php echo $slug; ?>', this)"><?php echo $cat; ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 p-3 bg-light rounded-4">
                    <small class="text-muted fw-bold">CART TOTAL</small>
                    <h3 class="fw-bold" id="sideTotal"><?php echo $s['currency_symbol']; ?>0</h3>
                    <button class="btn btn-dark w-100 rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#cartModal">Checkout</button>
                </div>
            </div>

            <div class="col-lg-9 main-content">
                
                <div class="d-lg-none mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold m-0"><?php echo $s['shop_name']; ?></h4>
                    <button class="btn btn-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#cartModal"><i class="fa fa-shopping-bag"></i></button>
                </div>

                <div class="hero-pop">
                    <img src="<?php echo img($shop_data['hero_banner']['imageFile']); ?>">
                    <div>
                        <h1 class="fw-bold mb-2"><?php echo $shop_data['hero_banner']['headline']; ?></h1>
                        <p class="text-muted m-0"><?php echo $shop_data['hero_banner']['subheadline']; ?></p>
                    </div>
                </div>

                <div class="d-lg-none d-flex overflow-auto gap-2 mb-4">
                    <button class="btn btn-white rounded-pill border px-4 active cat-m-btn" onclick="filter('all', this)">All</button>
                    <?php foreach($shop_data['categories'] as $cat => $items): 
                        $slug = strtolower(str_replace([' ','&'], ['-',''], $cat)); ?>
                        <button class="btn btn-white rounded-pill border px-4 cat-m-btn" onclick="filter('<?php echo $slug; ?>', this)"><?php echo $cat; ?></button>
                    <?php endforeach; ?>
                </div>

                <div class="row g-4">
                    <?php foreach($shop_data['categories'] as $cat => $items): 
                        $slug = strtolower(str_replace([' ','&'], ['-',''], $cat));
                        foreach($items as $p):
                            $hasVar = !empty($p['variants']);
                            $json = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="col-6 col-md-4 col-xl-3 shop-item <?php echo $slug; ?>">
                        <div class="pop-card" onclick="<?php echo $hasVar ? "openVar(this)" : "add(this)"; ?>" data-product='<?php echo $json; ?>'>
                            <img src="<?php echo img($p['imageFile']); ?>" class="pop-img">
                            <h6 class="fw-bold mb-1 text-truncate"><?php echo $p['name']; ?></h6>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="pop-price"><?php echo $s['currency_symbol'].$p['base_price']; ?></span>
                                <button class="btn-circle"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endforeach; ?>
                </div>

                <footer class="grid-footer">
                    <div class="footer-box">
                        <h5><?php echo $s['shop_name']; ?></h5>
                        <p class="text-muted small"><?php echo $s['address']; ?></p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-light rounded-circle"><i class="fa fa-phone"></i></button>
                            <button class="btn btn-light rounded-circle"><i class="fab fa-whatsapp"></i></button>
                        </div>
                    </div>
                    <div class="dev-box d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white-50 fw-bold">DEVELOPED BY</small>
                            <h4 class="mb-0">ARHAM PRINTERS</h4>
                        </div>
                        <a href="https://arhamprinters.pk" class="btn btn-light rounded-pill px-4 fw-bold">Visit</a>
                    </div>
                </footer>

            </div>
        </div>
    </div>

    <div class="modal fade" id="varModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 p-3"><h5 class="fw-bold mb-3" id="vt"></h5><div id="vo" class="d-grid gap-2"></div></div></div></div>
    
    <div class="modal fade" id="cartModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 p-0"><div class="modal-header border-0"><h5 class="fw-bold">Your Order</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="cl" class="d-flex flex-column gap-2 mb-3"></div><div class="bg-light p-3 rounded-3 mb-3"><div class="d-flex justify-content-between fw-bold"><span>Total</span><span id="ct">0</span></div></div><input id="cn" class="form-control rounded-3 mb-2" placeholder="Your Name"><input id="cp" class="form-control rounded-3 mb-2" placeholder="Phone"><textarea id="ca" class="form-control rounded-3 mb-3" placeholder="Address"></textarea><button onclick="checkout()" class="btn btn-dark w-100 rounded-pill py-3 fw-bold">Send to WhatsApp</button></div></div></div></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const CFG={cur:"<?php echo $s['currency_symbol']; ?>",ph:"<?php echo $s['whatsapp_number']; ?>"};
        let cart=JSON.parse(localStorage.getItem('mcart'))||[],cp=null;
        
        function filter(c,b){
            $('.nav-link, .cat-m-btn').removeClass('active bg-dark text-white'); $(b).addClass('active');
            if($(b).hasClass('cat-m-btn')) $(b).addClass('bg-dark text-white');
            $('.shop-item').hide(); (c==='all')?$('.shop-item').fadeIn():$('.'+c).fadeIn();
        }
        function add(e){push(JSON.parse($(e).attr('data-product')),null);}
        function openVar(e){cp=JSON.parse($(e).attr('data-product'));$('#vt').text(cp.name);$('#vo').empty();
        cp.variants.forEach(v=>$('#vo').append(`<button class="btn btn-outline-dark rounded-3 py-3 d-flex justify-content-between" onclick="push(cp,'${v.name}',${cp.base_price+v.price_adjustment});$('#varModal').modal('hide')"><span>${v.name}</span><strong>${CFG.cur}${cp.base_price+v.price_adjustment}</strong></button>`));
        new bootstrap.Modal('#varModal').show();}
        function push(p,v,pr){let id=p.id+(v||''),x=cart.find(i=>i.id===id);x?x.q++:cart.push({id:id,n:p.name,v:v,p:pr||p.base_price,q:1});upd();}
        function upd(){localStorage.setItem('mcart',JSON.stringify(cart));
        let t=cart.reduce((a,b)=>a+(b.p*b.q),0), h='';
        $('#sideTotal, #ct').text(CFG.cur+t);
        cart.forEach((i,x)=>h+=`<div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3"><div><b>${i.q}x</b> ${i.n} <small>${i.v||''}</small></div><i class="fa fa-trash text-danger" style="cursor:pointer" onclick="cart.splice(${x},1);upd()"></i></div>`);
        $('#cl').html(h||'<div class="text-center text-muted py-4">Cart Empty</div>');}
        function checkout(){if(!cart.length)return;let m='Web Order:\n';cart.forEach(i=>m+=`${i.q}x ${i.n} ${i.v||''} \n`);
        window.open(`https://wa.me/${CFG.ph}?text=${encodeURIComponent(m+'\nInfo: '+$('#cn').val()+' '+$('#ca').val())}`);cart=[];upd();bootstrap.Modal.getInstance('#cartModal').hide();}
        upd();
    </script>
</body>
</html>