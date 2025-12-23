<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Custom Document Printing - Arham Printers</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Online document printing service for A4, Legal, reports, and more." name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary: #f28b00;
            --bs-secondary: #ff5722;
            --bs-dark: #212529;
            --mobile-nav-height: 60px;
        }
        
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(img/banners/prints.webp) center center no-repeat;
            background-size: cover;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .choice-card {
            cursor: pointer;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.2s;
            flex: 1;
            min-width: 120px;
        }
        .choice-card:hover { border-color: var(--bs-secondary); background: #fff5f0; }
        .choice-card.selected { border-color: var(--bs-primary); background: var(--bs-primary); color: white; }
        .choice-card input { display: none; }
        
        #mobile-bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; height: var(--mobile-nav-height);
            background: white; border-top: 1px solid #ddd; z-index: 1030; display: flex;
            justify-content: space-around; align-items: center;
        }
        .mobile-nav-link {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-decoration: none; color: var(--bs-dark); font-size: 0.7rem;
        }
        .mobile-nav-link.active { color: var(--bs-primary); }
        .mobile-nav-link i { font-size: 1.2rem; margin-bottom: 2px; }
        
        body { padding-bottom: 80px; } 
        
        .price-display {
            font-size: 2rem;
            font-weight: 700;
            color: var(--bs-primary);
        }
        
        /* Drag and Drop Zone */
        #drop-zone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f9f9f9;
            transition: all 0.3s;
            cursor: pointer;
        }
        #drop-zone.dragover {
            background: #e9ecef;
            border-color: var(--bs-primary);
        }
    </style>
</head>

<body>
    <div class="container-fluid bg-dark text-white-50 py-2 px-0 d-none d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-7 px-5 text-start">
                <small class="fa fa-phone-alt me-2"></small> <small>+92 300 6238233</small>
            </div>
            <div class="col-lg-5 px-5 text-end">
               <small>Sat - Thu : 09 AM - 09 PM</small>
            </div>
        </div>
    </div>
    
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 d-none d-lg-block">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <a href="index.php" class="navbar-brand d-flex align-items-center me-4">
                <img src="img/logo2.webp" alt="Arham Printers" style="height: 40px; margin-right: 1rem;">
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-1" id="navbarCollapse">
                <div class="navbar-nav me-auto bg-light pe-4 py-3 py-lg-0 d-flex align-items-center">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="products.php" class="nav-item nav-link">Products</a>
                    <a href="wedding.php" class="nav-item nav-link">Wedding Cards</a>
                    <a href="#" class="nav-item nav-link active">Paper Printing</a>
                    <a href="#" onclick="window.location.href='index.php#contact-section'" class="nav-item nav-link">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <nav id="mobile-bottom-nav" class="d-lg-none">
        <a href="index.php" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="products.php" class="mobile-nav-link"><i class="fas fa-th-list"></i> Products</a>
        <a href="#" class="mobile-nav-link active"><i class="fas fa-print"></i> Prints</a>
        <a href="#" class="mobile-nav-link" onclick="Cart.toggleCart()"><i class="fas fa-shopping-cart"></i> Cart</a>
    </nav>

    <div class="container-fluid page-header mb-5">
        <h1 class="display-3 text-white text-center mb-3">Custom Document Printing</h1>
        <p class="text-white text-center fs-5">Fast, high-quality printing for your documents, assignments, and books.</p>
    </div>

    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="bg-light p-5 rounded shadow-sm">
                    <h3 class="mb-4 border-bottom pb-2">Configure Your Print</h3>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">1. Paper Size</label>
                        <div class="d-flex gap-3" id="options-Size">
                            </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">2. Print Type</label>
                        <div class="d-flex gap-3" id="options-ColorType">
                            </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">3. Paper Quality (GSM)</label>
                        <div class="d-flex gap-3 flex-wrap" id="options-GSM">
                            </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">4. Binding (Optional)</label>
                        <select class="form-control rounded-0" id="input-binding" onchange="updatePrice()">
                            <option value="None" data-price="0">No Binding</option>
                            <option value="Stapled" data-price="20">Stapled Corner (PKR 20)</option>
                            <option value="Spiral" data-price="100">Spiral Binding (PKR 100)</option>
                            <option value="Tape" data-price="50">Tape Binding (PKR 50)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">5. Total Pages</label>
                        <input type="number" id="input-pages" class="form-control p-3 fs-5" value="1" min="1" oninput="updatePrice()">
                        <small class="text-muted">Enter total number of pages in your document.</small>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">6. Upload File (PDF/DOCX/IMG)</label>
                        <div id="drop-zone">
                            <i class="fas fa-cloud-upload-alt fs-1 text-muted mb-2"></i>
                            <p class="mb-0">Drag & Drop file here or Click to Browse</p>
                            <input type="file" id="file-upload" class="d-none" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg">
                            <p id="file-name" class="mt-2 text-primary fw-bold small"></p>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle fs-4 me-3"></i>
                        <div>
                            <strong>Bulk Discounts Applied Automatically</strong><br>
                            Price per page decreases as quantity increases (100+, 500+ pages).
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="bg-white p-5 rounded shadow border sticky-top" style="top: 100px;">
                    <h3 class="mb-4">Cost Estimation</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Rate per Page:</span>
                        <span class="fw-bold" id="rate-display">PKR 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Pages:</span>
                        <span id="qty-display">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span>Binding:</span>
                        <span id="binding-cost-display">PKR 0</span>
                    </div>
                    <div class="text-center mb-4">
                        <span class="text-muted">Total Estimated Cost</span>
                        <div class="price-display" id="total-display">PKR 0</div>
                    </div>
                    
                    <button class="btn btn-primary w-100 py-3 rounded-0 mb-3" onclick="handleOrderSubmission()">
                        <i class="fas fa-check-circle me-2"></i> Place Order via WhatsApp
                    </button>
                    <button class="btn btn-outline-secondary w-100 py-2 rounded-0" onclick="addToCart()">
                        <i class="fas fa-cart-plus me-2"></i> Add to Cart (Upload Later)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cart-manager.js"></script>

    <script>
        const DATA_URL = 'all_prices.json';
        const WHATSAPP_NUMBER = '923006238233';
        
        let printData = {};
        let selections = { Size: '', ColorType: '', GSM: '' };
        let selectedFile = null;

        async function init() {
            try {
                const res = await fetch(DATA_URL);
                const json = await res.json();
                // Accessing the specific category from your JSON structure
                printData = json["B/W and Color Prints"] || {};
                
                // Initialize Options
                if(Object.keys(printData).length > 0) {
                    renderOptions('Size', Object.keys(printData));
                    updatePrice();
                }
            } catch (e) { console.error("Data Load Error", e); }
        }

        function renderOptions(key, options) {
            const container = document.getElementById(`options-${key}`);
            if(!container) return;
            
            // Auto-select first option if none selected
            if (!selections[key] && options.length > 0) selections[key] = options[0];
            
            container.innerHTML = options.map(opt => `
                <label class="choice-card ${selections[key] === opt ? 'selected' : ''}" onclick="select('${key}', '${opt}')">
                    <input type="radio" name="${key}" value="${opt}" ${selections[key] === opt ? 'checked' : ''}>
                    ${opt.replace('_', ' ')}
                </label>
            `).join('');
            
            // Cascade Logic (Size -> Color -> GSM)
            if (key === 'Size') {
                const colors = Object.keys(printData[selections.Size] || {});
                renderOptions('ColorType', colors);
            } else if (key === 'ColorType') {
                const gsms = Object.keys(printData[selections.Size][selections.ColorType] || {});
                renderOptions('GSM', gsms);
            }
        }

        function select(key, val) {
            selections[key] = val;
            
            // Reset downstream selections when upstream changes
            if (key === 'Size') { selections.ColorType = ''; selections.GSM = ''; }
            if (key === 'ColorType') { selections.GSM = ''; }
            
            renderOptions('Size', Object.keys(printData)); // Re-render to update UI classes
            updatePrice();
        }

        function updatePrice() {
            const qty = parseInt(document.getElementById('input-pages').value) || 1;
            const bindingSelect = document.getElementById('input-binding');
            const bindingCost = parseInt(bindingSelect.options[bindingSelect.selectedIndex].getAttribute('data-price')) || 0;
            
            document.getElementById('qty-display').textContent = qty;
            document.getElementById('binding-cost-display').textContent = `PKR ${bindingCost}`;
            
            if (!selections.Size || !selections.ColorType || !selections.GSM) return;
            
            // Fetch rate from tiers logic (1-100, 101-500, etc.)
            const tiers = printData[selections.Size][selections.ColorType][selections.GSM];
            let rate = 0;
            
            Object.keys(tiers).forEach(range => {
                const [min, max] = range.split('-').map(Number);
                // Handle "501-10000" or similar ranges
                if (qty >= min && (isNaN(max) || qty <= max)) {
                    rate = tiers[range];
                }
            });

            document.getElementById('rate-display').textContent = `PKR ${rate}`;
            const total = (rate * qty) + bindingCost;
            document.getElementById('total-display').textContent = `PKR ${total.toLocaleString()}`;
        }
        
        // File Upload Handling
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-upload');

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => handleFile(e.target.files[0]));
        
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', (e) => { 
            e.preventDefault(); 
            dropZone.classList.remove('dragover'); 
            handleFile(e.dataTransfer.files[0]); 
        });

        function handleFile(file) {
            if(file) {
                selectedFile = file;
                document.getElementById('file-name').textContent = `Selected: ${file.name}`;
            }
        }

        function handleOrderSubmission() {
            const qty = document.getElementById('input-pages').value;
            const binding = document.getElementById('input-binding').value;
            const total = document.getElementById('total-display').textContent;
            
            let msg = `*NEW PRINT ORDER*\n`;
            msg += `*Specs:* ${selections.Size} | ${selections.ColorType} | ${selections.GSM}\n`;
            msg += `*Binding:* ${binding}\n`;
            msg += `*Pages:* ${qty}\n`;
            msg += `*Est. Cost:* ${total}\n`;
            msg += `*File:* ${selectedFile ? 'Sending separately...' : 'No file selected'}`;
            
            window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        function addToCart() {
            if (typeof Cart === 'undefined') { alert("Cart Loading..."); return; }
            
            const qty = parseInt(document.getElementById('input-pages').value) || 1;
            const price = parseFloat(document.getElementById('total-display').textContent.replace('PKR ', '').replace(',', ''));
            const binding = document.getElementById('input-binding').value;

            Cart.addItem({
                id: Date.now(),
                productName: `Doc Print (${selections.Size}, ${selections.ColorType})`,
                quantity: qty,
                basePrice: price,
                finalItemPrice: price,
                options: { ...selections, Binding: binding },
                imageFile: 'printer' // Generic icon
            });
        }

        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>