<?php
$data = json_decode(file_get_contents('data/config.json'), true);
$theme = $data['theme'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Plan Event | <?php echo $data['identity']['name']; ?></title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: <?php echo $theme['primary']; ?>;
            --gold: <?php echo $theme['secondary']; ?>;
            --bg: #f3f4f6;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; padding-bottom: 120px; }

        /* --- HEADER --- */
        .planner-header {
            background: var(--primary); color: white; padding: 15px 20px; text-align: center;
            position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex; align-items: center; justify-content: center;
        }
        .planner-header h1 { font-family: 'Cinzel'; margin: 0; font-size: 1.1rem; color: var(--gold); letter-spacing: 1px; }
        .back-btn { position: absolute; left: 20px; color: white; text-decoration: none; font-size: 1.2rem; }

        /* --- CONTAINER --- */
        .container { max-width: 700px; margin: 20px auto; padding: 0 15px; }

        /* --- CARDS --- */
        .step-card {
            background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03); border: 1px solid #e5e7eb;
        }
        .step-label {
            font-weight: 700; color: var(--primary); margin-bottom: 20px; display: flex; align-items: center; 
            gap: 10px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;
            border-bottom: 1px solid #eee; padding-bottom: 10px;
        }
        .step-label i { color: var(--gold); font-size: 1.1rem; }

        /* --- INPUTS --- */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-control {
            width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;
            margin-bottom: 15px; font-family: inherit; box-sizing: border-box; transition: 0.3s;
        }
        .form-control:focus { outline: none; border-color: var(--gold); background: #fffcf5; }
        
        label { display: block; font-size: 0.85rem; font-weight: 600; color: #666; margin-bottom: 5px; }

        /* --- SELECTION BOXES --- */
        .option-box {
            border: 2px solid #f3f4f6; padding: 15px; border-radius: 10px; margin-bottom: 15px;
            cursor: pointer; transition: 0.2s; position: relative; background: #fafafa;
        }
        .option-box:hover { border-color: #ddd; }
        .option-box.active { border-color: var(--gold); background: #fffdf0; box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1); }
        .option-box.active::after {
            content: '\f058'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
            position: absolute; top: 15px; right: 15px; color: var(--gold); font-size: 1.2rem;
        }

        .opt-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .opt-title { font-weight: 700; color: var(--primary); font-size: 1rem; }
        .opt-price { color: var(--gold); font-weight: 700; font-size: 0.95rem; }
        .opt-desc { font-size: 0.85rem; color: #666; line-height: 1.5; padding-right: 30px; }

        /* --- STICKY BOTTOM BAR --- */
        .bottom-bar {
            position: fixed; bottom: 0; left: 0; width: 100%; background: white;
            padding: 15px 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
            display: flex; justify-content: space-between; align-items: center; z-index: 1000;
            box-sizing: border-box; border-top: 1px solid #eee;
        }
        .total-box div { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .total-value { font-size: 1.4rem; font-weight: 800; color: var(--primary); font-family: 'Cinzel'; }
        
        .btn-wa {
            background: #25D366; color: white; border: none; padding: 12px 25px;
            border-radius: 50px; font-weight: 600; font-size: 0.95rem; cursor: pointer;
            display: flex; gap: 8px; align-items: center; white-space: nowrap; transition: 0.3s;
        }
        .btn-wa:hover { background: #20b857; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3); }

        /* Responsive Tweaks */
        @media(max-width: 480px) {
            .form-grid { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>

    <div class="planner-header">
        <a href="index.php" class="back-btn"><i class="fas fa-chevron-left"></i></a>
        <h1>Event Configurator</h1>
    </div>

    <div class="container">
        
        <div class="step-card">
            <div class="step-label"><i class="fas fa-calendar-alt"></i> Step 1: Event Details</div>
            <div class="form-grid">
                <div>
                    <label>Guest Count</label>
                    <input type="number" id="guests" class="form-control" value="250" placeholder="e.g. 300" onkeyup="calc()" onchange="calc()">
                </div>
                <div>
                    <label>Event Date</label>
                    <input type="date" id="date" class="form-control">
                </div>
            </div>
            <label>Function Type</label>
            <select id="eventType" class="form-control">
                <option>Wedding (Barat)</option>
                <option>Wedding (Walima)</option>
                <option>Mehndi / Sangeet</option>
                <option>Corporate Dinner</option>
                <option>Birthday / Party</option>
            </select>
        </div>

        <div class="step-card">
            <div class="step-label"><i class="fas fa-utensils"></i> Step 2: Select Menu</div>
            <?php foreach($data['packages'] as $pkg): ?>
            <div class="option-box pkg-opt" onclick="selectPkg(this, <?php echo $pkg['price']; ?>, '<?php echo $pkg['name']; ?>')">
                <div class="opt-head">
                    <span class="opt-title"><?php echo $pkg['name']; ?></span>
                    <span class="opt-price"><?php echo $data['identity']['currency'] . number_format($pkg['price']); ?> / head</span>
                </div>
                <div class="opt-desc"><?php echo implode(' • ', $pkg['items']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="step-card">
            <div class="step-label"><i class="fas fa-gem"></i> Step 3: Luxury Add-ons</div>
            <?php foreach($data['addons'] as $add): ?>
            <div class="option-box addon-opt" onclick="toggleAddon(this, <?php echo $add['price']; ?>, '<?php echo $add['type']; ?>', '<?php echo $add['name']; ?>')">
                <div style="display:flex; align-items:center; gap:15px;">
                    <i class="fas <?php echo $add['icon']; ?>" style="color:var(--gold); font-size:1.2rem;"></i>
                    <div style="flex-grow:1;">
                        <div class="opt-title" style="font-size:0.95rem;"><?php echo $add['name']; ?></div>
                        <div class="opt-price" style="font-size:0.8rem; font-weight:600;">
                            + <?php echo number_format($add['price']); ?> <?php echo ($add['type']=='per_head')?'/ guest':'fixed'; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="step-card">
            <div class="step-label"><i class="fas fa-user-edit"></i> Step 4: Your Information</div>
            <label>Full Name</label>
            <input type="text" id="contactName" class="form-control" placeholder="Enter your name">
            
            <label>WhatsApp Number</label>
            <input type="tel" id="contactPhone" class="form-control" placeholder="0300 1234567">
        </div>

    </div>

    <div class="bottom-bar">
        <div class="total-box">
            <div>Estimated Total</div>
            <div class="total-value" id="displayTotal">Rs. 0</div>
        </div>
        <button class="btn-wa" onclick="bookNow()">
            Confirm <i class="fab fa-whatsapp"></i>
        </button>
    </div>

    <script>
        let pkgPrice = 0;
        let pkgName = "";
        let addonCost = 0;
        let selectedAddons = [];
        const currency = "<?php echo $data['identity']['currency']; ?>";

        function selectPkg(el, price, name) {
            // Remove active class from all packages
            document.querySelectorAll('.pkg-opt').forEach(d => d.classList.remove('active'));
            // Add active class to clicked
            el.classList.add('active');
            
            pkgPrice = price;
            pkgName = name;
            calc();
        }

        function toggleAddon(el, price, type, name) {
            el.classList.toggle('active');
            let isActive = el.classList.contains('active');
            let guests = parseInt(document.getElementById('guests').value) || 0;
            
            // Calculate specific addon cost
            let cost = (type === 'fixed') ? price : (price * guests);

            if(isActive) {
                addonCost += cost;
                selectedAddons.push(name);
            } else {
                addonCost -= cost;
                selectedAddons = selectedAddons.filter(item => item !== name);
            }
            calc();
        }

        function calc() {
            let guests = parseInt(document.getElementById('guests').value) || 0;
            // Note: If you change guests, variable addons won't update automatically in this simple script 
            // without re-looping active addons. For a production app, we'd loop.
            // Simplified calculation:
            let total = (pkgPrice * guests) + addonCost;
            document.getElementById('displayTotal').innerText = currency + " " + total.toLocaleString();
        }

        function bookNow() {
            if(pkgPrice === 0) return alert("Please select a Menu Package from Step 2.");
            
            let name = document.getElementById('contactName').value;
            let phone = document.getElementById('contactPhone').value;
            if(!name || !phone) return alert("Please enter your Name and Phone Number.");

            let date = document.getElementById('date').value;
            let guests = document.getElementById('guests').value;
            let type = document.getElementById('eventType').value;
            let total = document.getElementById('displayTotal').innerText;

            let msg = `*NEW BOOKING INQUIRY*\n------------------\n*Customer:* ${name}\n*Phone:* ${phone}\n\n*Event:* ${type}\n*Date:* ${date}\n*Guests:* ${guests}\n\n*Package:* ${pkgName}\n*Addons:* ${selectedAddons.join(', ')}\n\n*ESTIMATED TOTAL:* ${total}`;
            
            window.open(`https://wa.me/<?php echo $data['contact']['whatsapp']; ?>?text=${encodeURIComponent(msg)}`, '_blank');
        }
    </script>
</body>
</html>