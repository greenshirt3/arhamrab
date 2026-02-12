<?php
$shops = json_decode(file_get_contents('../data/shops.json'), true);
$host = $_SERVER['HTTP_HOST'];
$parts = explode('.', $host);
$subdomain = $parts[0]; 

$current_shop = null;
foreach($shops as $s) { if($s['subdomain'] == $subdomain) { $current_shop = $s; break; } }
if(!$current_shop) die("Shop not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions | <?php echo $current_shop['name']; ?></title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: <?php echo $current_shop['theme_color']; ?>; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        h2 { margin-top: 30px; font-size: 1.2rem; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <a href="../index.php" class="back-link">← Back to Shop</a>
    
    <h1>Terms & Conditions</h1>
    <p>Last Updated: <?php echo date('F Y'); ?></p>

    <h2>1. General</h2>
    <p>Welcome to <b><?php echo $current_shop['name']; ?></b>. By placing an order through the RiderGo platform (Powered by Arham Printers), you agree to these terms.</p>

    <h2>2. Orders & Delivery</h2>
    <p>All orders are subject to availability. Delivery times are estimates and depend on rider availability in Jalalpur Jattan. A standard delivery fee applies to all orders.</p>

    <h2>3. Returns & Refunds</h2>
    <p>
        <?php if(($current_shop['type'] ?? 'food') == 'food'): ?>
        For food items, we do not accept returns once delivered unless the item is spoiled or incorrect. Please check your order upon receipt.
        <?php else: ?>
        For retail items, exchanges are allowed within 3 days if the product is unused and in original packaging.
        <?php endif; ?>
    </p>

    <h2>4. Contact</h2>
    <p>For order issues, contact the shop at <b><?php echo $current_shop['phone']; ?></b>.</p>
    <p>For technical issues with this website, please contact <b>Arham Printers</b>.</p>

    <br><br>
    <hr style="border: 0; border-top: 1px solid #eee;">
    <p style="font-size:0.8rem; color:#999; text-align:center;">
        Technology Partner: <a href="https://arhamprinters.pk" style="color:#F37021; text-decoration:none;">Arham Printers</a>
    </p>
</body>
</html>