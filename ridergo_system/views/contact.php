<?php
// Load shop details
$shops = json_decode(file_get_contents('../data/shops.json'), true);
// Basic subdomain detection (you might need to adjust based on your server setup)
$host = $_SERVER['HTTP_HOST'];
$parts = explode('.', $host);
$subdomain = $parts[0]; 

$current_shop = null;
foreach($shops as $s) { if($s['subdomain'] == $subdomain) { $current_shop = $s; break; } }
// Fallback or error if not found
if(!$current_shop) {
    // For testing purposes, you might default to first shop or die
   // $current_shop = $shops[0]; 
   die("Shop not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | <?php echo $current_shop['name']; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        h1 { text-align: center; color: #333; }
        .contact-item { display: flex; align-items: center; gap: 15px; padding: 15px; border-bottom: 1px solid #eee; }
        .icon-box { width: 40px; height: 40px; background: <?php echo $current_shop['theme_color']; ?>; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; }
        
        .arham-credit {
            margin-top: 30px; padding-top: 20px; border-top: 2px dashed #eee;
            text-align: center; font-size: 0.85rem; color: #888;
        }
        .arham-credit a { color: #F37021; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Contact Us</h1>
        <p style="text-align:center; color:#666;">We are here to help!</p>
        
        <div class="contact-item">
            <div class="icon-box"><i class="fas fa-store"></i></div>
            <div>
                <strong>Shop Name</strong><br>
                <?php echo $current_shop['name']; ?>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon-box"><i class="fas fa-phone"></i></div>
            <div>
                <strong>Phone / WhatsApp</strong><br>
                <a href="tel:<?php echo $current_shop['phone']; ?>" style="color:inherit;"><?php echo $current_shop['phone']; ?></a>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <strong>Location</strong><br>
                <?php 
                    $lat = $current_shop['lat'] ?? '32.6384'; 
                    $lng = $current_shop['lng'] ?? '74.2040';
                ?>
                <a href="http://maps.google.com/?q=<?php echo $lat; ?>,<?php echo $lng; ?>" target="_blank">View on Google Maps</a>
            </div>
        </div>

        <div class="arham-credit">
            Platform Developed & Maintained by<br>
            <a href="https://arhamprinters.pk" target="_blank">Arham Printers</a><br>
            <span style="font-size:0.75rem">Printing | Web Development | Branding</span>
        </div>

        <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Store</a>
    </div>

</body>
</html>