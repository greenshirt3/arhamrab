<?php include 'header.php'; ?>

<section id="home" class="position-relative py-5" style="background: url('<?php echo $data['hero']['bg_image']; ?>') no-repeat center center/cover;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white py-5">
                <div class="badge bg-warning text-dark mb-3 px-3 py-2 fw-bold animate__animated animate__fadeInDown">PREMIUM PACKAGING SOLUTIONS</div>
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInLeft"><?php echo $data['hero']['title']; ?></h1>
                <p class="lead mb-5 text-light opacity-75 animate__animated animate__fadeInUp"><?php echo $data['hero']['subtitle']; ?></p>
                
                <div class="d-flex gap-3 animate__animated animate__fadeInUp">
                    <a href="#products" class="btn btn-lg btn-light rounded-pill px-5 fw-bold text-primary">View Products</a>
                    <a href="#calculator" class="btn btn-lg btn-outline-warning rounded-pill px-5 fw-bold">Cost Estimate</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block animate__animated animate__zoomIn">
                <img src="https://cdn-icons-png.flaticon.com/512/679/679821.png" class="img-fluid drop-shadow" style="filter: drop-shadow(0 0 20px rgba(255,255,255,0.2)); width: 80%;">
            </div>
        </div>
    </div>
</section>

<section id="products" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h5 class="text-warning fw-bold">WHAT WE MANUFACTURE</h5>
            <h2 class="display-5 fw-bold text-dark">Packaging Categories</h2>
        </div>
        <div class="row g-4">
            <?php foreach($data['categories'] as $cat): ?>
            <div class="col-md-6 col-lg-3">
                <div class="box-card">
                    <div class="box-img-wrap">
                        <img src="<?php echo $cat['image']; ?>">
                    </div>
                    <div class="p-4">
                        <i class="fas <?php echo $cat['icon']; ?> fa-2x text-warning mb-3"></i>
                        <h4 class="fw-bold"><?php echo $cat['title']; ?></h4>
                        <p class="text-muted small"><?php echo $cat['desc']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="calculator" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center bg-light rounded-3 shadow overflow-hidden">
            <div class="col-lg-6 p-0 h-100 d-none d-lg-block" style="background: url('https://images.unsplash.com/photo-1595079676339-1534801fafde?auto=format&fit=crop&w=800&q=80') center/cover; min-height: 600px;">
                <div class="h-100 w-100 bg-primary opacity-50 d-flex align-items-center justify-content-center">
                    <div class="text-center text-white p-5">
                        <i class="fa fa-drafting-compass fa-4x mb-3"></i>
                        <h3>Custom Sizes Available</h3>
                        <p>Enter your dimensions and get an instant quote request on WhatsApp.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 p-5">
                <span class="text-primary fw-bold text-uppercase">Get a Quote</span>
                <h2 class="mb-4 fw-bold">Custom Box Calculator</h2>
                <form onsubmit="sendQuote(event)">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Box Type</label>
                            <select id="qType" class="form-select bg-white border-secondary">
                                <?php foreach($data['products'] as $prod) echo "<option>{$prod['name']}</option>"; ?>
                                <option>Other Custom Box</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Material</label>
                            <select id="qMat" class="form-select bg-white border-secondary">
                                <option>Bleach Card (Food Safe)</option>
                                <option>Kraft Card (Brown)</option>
                                <option>Corrugated (Shipping)</option>
                                <option>Rigid (Luxury)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Length (inch)</label>
                            <input type="number" id="qL" class="form-control" placeholder="L" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Width (inch)</label>
                            <input type="number" id="qW" class="form-control" placeholder="W" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Height (inch)</label>
                            <input type="number" id="qH" class="form-control" placeholder="H" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Quantity Needed</label>
                            <input type="range" class="form-range" min="100" max="5000" step="100" id="qQty" oninput="document.getElementById('qQtyDisplay').innerText = this.value">
                            <div class="text-end fw-bold text-primary" id="qQtyDisplay">2500</div>
                        </div>
                        <div class="col-12 mt-4">
                            <button class="btn btn-warning w-100 py-3 fw-bold text-dark">
                                Request Quote on WhatsApp <i class="fab fa-whatsapp ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section id="process" class="py-5" style="background-color: var(--brand-navy); color: white;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Our Production Process</h2>
            <p class="opacity-75">From design to delivery, powered by Boxify FS.</p>
        </div>
        <div class="row g-4">
            <?php foreach($data['process'] as $step): ?>
            <div class="col-md-3 text-center position-relative">
                <div class="display-1 fw-bold text-white opacity-25 mb-n4 position-relative" style="z-index: 0;"><?php echo $step['step']; ?></div>
                <h4 class="fw-bold position-relative" style="z-index: 1;"><?php echo $step['title']; ?></h4>
                <p class="text-white-50 small"><?php echo $step['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    function sendQuote(e) {
        e.preventDefault();
        const type = document.getElementById('qType').value;
        const mat = document.getElementById('qMat').value;
        const size = `${document.getElementById('qL').value}x${document.getElementById('qW').value}x${document.getElementById('qH').value}`;
        const qty = document.getElementById('qQty').value;
        
        const msg = `*New Box Quote Request*%0a------------------%0aType: ${type}%0aMaterial: ${mat}%0aSize (LxWxH): ${size} inches%0aQuantity: ${qty}%0a------------------%0aPlease provide best price.`;
        
        window.open(`https://wa.me/<?php echo str_replace(['+',' '], '', $info['phone']); ?>?text=${msg}`, '_blank');
    }
</script>

<?php include 'footer.php'; ?>