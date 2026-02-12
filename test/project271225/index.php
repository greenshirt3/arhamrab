<?php include 'header.php'; ?>

<section id="home" class="d-flex align-items-center position-relative" style="min-height: 100vh; padding-top: 80px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="badge border border-secondary text-secondary mb-3 px-3 py-2 rounded-0">EST. 2005 • JALALPUR JATTAN</div>
                <h1 class="display-1 fw-bold mb-4" style="line-height: 0.9;">
                    <?php echo $data['hero']['headline']; ?>
                </h1>
                <p class="lead text-secondary w-75 mb-5"><?php echo $data['hero']['sub']; ?></p>
                
                <div class="d-flex gap-4">
                    <a href="#catalog" class="btn-neon">Explore Products</a>
                    
                    <div class="d-flex gap-4 align-items-center border-start border-secondary ps-4">
                        <?php foreach($data['hero']['stats'] as $stat): ?>
                        <div>
                            <h4 class="m-0 text-white"><?php echo $stat['num']; ?></h4>
                            <small class="text-secondary text-uppercase" style="font-size: 0.7rem;"><?php echo $stat['label']; ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 d-none d-lg-block" data-aos="zoom-in">
                <div class="tilt-element" data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.5">
                    <img src="<?php echo $data['hero']['image']; ?>" class="img-fluid" style="border-radius: 0; filter: contrast(1.2) saturation(0);">
                    <div class="position-absolute bottom-0 start-0 bg-black p-4 border-top border-end border-secondary">
                        <h5 class="m-0 text-cyan">PREMIUM<br>QUALITY</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="position-absolute top-0 end-0 w-50 h-100" style="background: radial-gradient(circle at center, rgba(0,242,234,0.05), transparent 70%); z-index: -1;"></div>
</section>

<div class="marquee-container">
    <div class="marquee-content">
        Offset Printing • Digital Marketing • Packaging Solutions • Web Development • Neon Signs • Branding • 
        Offset Printing • Digital Marketing • Packaging Solutions • Web Development • Neon Signs • Branding • 
    </div>
</div>

<section id="catalog" class="py-5">
    <div class="container py-5">
        <div class="row mb-5 align-items-end">
            <div class="col-md-6" data-aos="fade-up">
                <h6 class="text-pink">CATALOG</h6>
                <h2 class="display-4">Curated Products</h2>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="search-wrap">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search for cards, boxes, flex..." onkeyup="filterProducts()">
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-5" data-aos="fade-up">
            <button class="btn btn-outline-secondary rounded-0 active filter-btn" onclick="filterCat('all', this)">All</button>
            <?php foreach($data['categories'] as $cat): ?>
                <button class="btn btn-outline-secondary rounded-0 filter-btn" onclick="filterCat('<?php echo $cat['id']; ?>', this)"><?php echo $cat['name']; ?></button>
            <?php endforeach; ?>
        </div>

        <div class="row g-4" id="productGrid">
            <?php foreach($data['products'] as $prod): ?>
            <div class="col-md-6 col-lg-4 prod-item" data-cat="<?php echo $prod['category']; ?>" data-name="<?php echo strtolower($prod['name']); ?>">
                <div class="glass-card prod-card p-0 h-100 d-flex flex-column">
                    <div class="position-relative overflow-hidden">
                        <img src="<?php echo $prod['image']; ?>">
                        <div class="position-absolute top-0 end-0 bg-black text-white px-3 py-1 m-3 border border-secondary">
                            PKR <?php echo $prod['price']; ?>
                        </div>
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <small class="text-secondary text-uppercase mb-2">
                            <?php 
                                foreach($data['categories'] as $c) if($c['id'] == $prod['category']) echo $c['name']; 
                            ?>
                        </small>
                        <h4 class="mb-3"><?php echo $prod['name']; ?></h4>
                        <p class="text-secondary small mb-4 flex-grow-1"><?php echo $prod['desc']; ?></p>
                        
                        <button class="btn btn-outline-light w-100 rounded-0" 
                            onclick="openProductModal('<?php echo htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8'); ?>')">
                            Customize & Order
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="portfolio" class="py-5 bg-black">
    <div class="container py-5">
        <h6 class="text-cyan text-center">SELECTED WORKS</h6>
        <h2 class="display-4 text-center mb-5">Made at Arham</h2>
        
        <div class="row g-3" data-masonry='{"percentPosition": true }'>
            <?php foreach($data['portfolio'] as $img): ?>
            <div class="col-md-4 mb-3" data-aos="zoom-in">
                <div class="glass-card p-0">
                    <img src="<?php echo $img; ?>" class="w-100" style="filter: grayscale(100%); transition: 0.5s; cursor: pointer;" onmouseover="this.style.filter='grayscale(0%)'" onmouseout="this.style.filter='grayscale(100%)'">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product Name</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <img id="modalImg" src="" class="img-fluid border border-secondary">
                    </div>
                    <div class="col-md-7">
                        <form onsubmit="sendWhatsApp(event)">
                            <div class="mb-3">
                                <label class="text-secondary small">Select Variation</label>
                                <select id="modalVar" class="form-select"></select>
                            </div>
                            <div class="mb-3">
                                <label class="text-secondary small">Your Name</label>
                                <input type="text" id="custName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="text-secondary small">Instructions</label>
                                <textarea id="custMsg" class="form-control" rows="2" placeholder="Size, Qty, or Design details..."></textarea>
                            </div>
                            <button class="btn-neon w-100 mt-2">Confirm Order <i class="fab fa-whatsapp ms-2"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>

<script>
    // Init Animations
    AOS.init({ duration: 800, once: true });

    // --- SEARCH & FILTER LOGIC ---
    function filterProducts() {
        const term = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.prod-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            item.style.display = name.includes(term) ? 'block' : 'none';
        });
    }

    function filterCat(cat, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('btn-light', 'active'));
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-light', 'active');

        const items = document.querySelectorAll('.prod-item');
        items.forEach(item => {
            if(cat === 'all' || item.getAttribute('data-cat') === cat) item.style.display = 'block';
            else item.style.display = 'none';
        });
    }

    // --- MODAL & DEEP LINK LOGIC ---
    let currentProd = {};

    function openProductModal(prodJson) {
        // Handle input if it's already an object or a string
        const prod = typeof prodJson === 'string' ? JSON.parse(prodJson) : prodJson;
        currentProd = prod;
        
        document.getElementById('modalTitle').innerText = prod.name;
        document.getElementById('modalImg').src = prod.image;
        
        const sel = document.getElementById('modalVar');
        sel.innerHTML = '';
        if(prod.variants) {
            prod.variants.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v;
                opt.innerText = v;
                sel.appendChild(opt);
            });
        }
        
        new bootstrap.Modal(document.getElementById('orderModal')).show();
    }

    function sendWhatsApp(e) {
        e.preventDefault();
        const name = document.getElementById('custName').value;
        const vari = document.getElementById('modalVar').value;
        const msg = document.getElementById('custMsg').value;
        
        const text = `*New Order: ${currentProd.name}*%0aPrice: ${currentProd.price}%0aVariation: ${vari}%0aCustomer: ${name}%0aNote: ${msg}`;
        window.open(`https://wa.me/<?php echo $info['whatsapp']; ?>?text=${text}`, '_blank');
    }

    // --- DEEP LINK LISTENER (Compatible with Kaif/Raza/BoxCraft) ---
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const searchName = urlParams.get('product'); // Look for ?product=Name
        
        if (searchName) {
            // Find the product in the DOM data attributes
            const target = Array.from(document.querySelectorAll('.prod-item')).find(item => 
                item.getAttribute('data-name').includes(searchName.toLowerCase())
            );

            if (target) {
                // Scroll to it
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Simulate click on its button
                const btn = target.querySelector('button');
                if(btn) btn.click();
            }
        }
    });
</script>