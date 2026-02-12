<?php
$packages = json_decode(file_get_contents('data/packages.json'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages | The Grand Royal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #D4AF37; --dark: #0f172a; }
        body { font-family: 'Lato', sans-serif; background: #f4f4f4; margin: 0; }
        
        .header { background: var(--dark); color: white; padding: 40px 20px; text-align: center; }
        .header h1 { font-family: 'Playfair Display'; color: var(--gold); margin: 0; font-size: 2.5rem; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        
        /* PACKAGES GRID */
        .pkg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 60px; }
        .pkg-card { background: white; border: 1px solid #ddd; padding: 30px; border-radius: 8px; position: relative; transition: 0.3s; }
        .pkg-card:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-color: var(--gold); }
        .pkg-name { font-family: 'Playfair Display'; font-size: 1.8rem; margin-bottom: 10px; color: var(--dark); }
        .pkg-price { color: var(--gold); font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; }
        .pkg-price span { font-size: 0.9rem; color: #666; font-weight: 400; }
        
        .item-list { list-style: none; padding: 0; margin: 0 0 20px 0; }
        .item-list li { padding: 8px 0; border-bottom: 1px dashed #eee; display: flex; align-items: center; gap: 10px; }
        .item-list li i { color: var(--gold); }

        /* CALCULATOR */
        .calc-section { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border-top: 5px solid var(--gold); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 8px; color: var(--dark); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit; }
        .total-box { background: var(--dark); color: var(--gold); padding: 20px; text-align: center; border-radius: 8px; font-size: 1.5rem; font-weight: 700; margin-top: 20px; }
        
        .btn-book { width: 100%; background: var(--gold); color: white; padding: 15px; border: none; font-size: 1.1rem; font-weight: 700; cursor: pointer; border-radius: 6px; margin-top: 20px; }
        .btn-book:hover { background: #b5952f; }

        @media(max-width:768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="header">
        <h1>Packages & Pricing</h1>
        <p>Choose the perfect menu for your special day</p>
        <a href="index.php" style="color:var(--gold); text-decoration:none;">&larr; Back Home</a>
    </div>

    <div class="container">
        
        <div class="pkg-grid">
            <?php foreach($packages as $pkg): ?>
            <div class="pkg-card" onclick="selectPkg('<?php echo $pkg['id']; ?>', <?php echo $pkg['price_per_head']; ?>)">
                <div class="pkg-name"><?php echo $pkg['name']; ?></div>
                <div class="pkg-price">Rs. <?php echo number_format($pkg['price_per_head']); ?> <span>/ head</span></div>
                <ul class="item-list">
                    <?php foreach($pkg['items'] as $item): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo $item; ?></li>
                    <?php endforeach; ?>
                </ul>
                <div style="font-size:0.9rem; color:#888;"><b>Includes:</b> <?php echo implode(', ', $pkg['features']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="calc-section" id="book">
            <h2 style="font-family:'Playfair Display'; margin-top:0;">Event Cost Calculator</h2>
            
            <form id="bookingForm" onsubmit="submitBooking(event)">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Select Package</label>
                        <select class="form-control" id="pkgSelect" onchange="calculate()">
                            <?php foreach($packages as $pkg): ?>
                                <option value="<?php echo $pkg['price_per_head']; ?>" data-name="<?php echo $pkg['name']; ?>">
                                    <?php echo $pkg['name']; ?> (Rs. <?php echo $pkg['price_per_head']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Number of Guests</label>
                        <input type="number" class="form-control" id="guests" value="100" min="50" onkeyup="calculate()" onchange="calculate()">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Event Date</label>
                        <input type="date" class="form-control" id="date" required>
                    </div>
                    <div class="form-group">
                        <label>Function Type</label>
                        <select class="form-control" id="type">
                            <option>Barat (Lunch)</option>
                            <option>Barat (Dinner)</option>
                            <option>Walima (Lunch)</option>
                            <option>Walima (Dinner)</option>
                            <option>Mehndi</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Contact Name & Phone</label>
                    <input type="text" class="form-control" id="contact" placeholder="e.g. Saif Ullah - 0300 1234567" required>
                </div>

                <div class="total-box">
                    Estimated Total: Rs. <span id="totalDisplay">0</span>
                    <div style="font-size:0.8rem; font-weight:400; color:white; margin-top:5px;">(Plus Tax & Hall Charges)</div>
                </div>

                <button class="btn-book">Request Booking</button>
            </form>
        </div>

    </div>

    <script>
        function calculate() {
            const price = parseInt(document.getElementById('pkgSelect').value);
            const guests = parseInt(document.getElementById('guests').value);
            const total = price * guests;
            document.getElementById('totalDisplay').innerText = total.toLocaleString();
        }

        function selectPkg(id, price) {
            const select = document.getElementById('pkgSelect');
            for(let i=0; i<select.options.length; i++) {
                if(select.options[i].value == price) {
                    select.selectedIndex = i;
                    break;
                }
            }
            calculate();
            document.getElementById('book').scrollIntoView({behavior: 'smooth'});
        }

        function submitBooking(e) {
            e.preventDefault();
            const select = document.getElementById('pkgSelect');
            const pkgName = select.options[select.selectedIndex].getAttribute('data-name');
            
            const data = {
                package: pkgName,
                guests: document.getElementById('guests').value,
                date: document.getElementById('date').value,
                type: document.getElementById('type').value,
                contact: document.getElementById('contact').value,
                est_total: document.getElementById('totalDisplay').innerText
            };

            // In a real app, send to API. Here we simulate alert and whatsapp.
            alert("Booking Request Sent! Total Est: Rs. " + data.est_total);
            
            // Redirect to WhatsApp
            const msg = `*New Hall Inquiry*\nName: ${data.contact}\nDate: ${data.date} (${data.type})\nPackage: ${data.package}\nGuests: ${data.guests}\nEst. Total: Rs. ${data.est_total}`;
            window.open(`https://wa.me/923001234567?text=${encodeURIComponent(msg)}`, '_blank');
        }

        // Init calc on load
        calculate();
    </script>
</body>
</html>