<?php
$host = $_SERVER['HTTP_HOST'];
$parts = explode('.', $host);
$subdomain = $parts[0];

$json_file = 'customers.json';
if (!file_exists($json_file)) { die("Error: Customer database missing."); }
$customers = json_decode(file_get_contents($json_file), true);

if (!isset($customers[$subdomain])) {
    header("Location: https://arhamprinters.pk");
    exit();
}

$c = $customers[$subdomain];
$theme = $c['color'] ?? '#0d6efd';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $c['company']; ?> | Digital Card</title>
    
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    
    <style>
        :root { --primary: <?php echo $theme; ?>; }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .card-wrapper {
            width: 100%;
            max-width: 420px;
            background: white;
            position: relative;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* 1. TOP HEADER (Geometric Style) */
        .header-bg {
            height: 200px;
            position: relative;
            /* Geometric Pattern Fallback */
            background: conic-gradient(from 45deg at 50% 50%, #fff 0deg, var(--primary) 180deg, #f1f1f1 360deg);
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
        }
        
        .header-overlay-img {
            width: 100%; height: 100%; object-fit: cover; opacity: 0.6;
        }

        /* The Big "Company Logo" Circle */
        .big-logo-circle {
            position: absolute;
            top: 40px;
            right: 20px;
            width: 140px;
            height: 140px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .big-logo-circle img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        /* 2. PROFILE SECTION (Left Aligned) */
        .profile-section {
            display: flex;
            align-items: center;
            padding: 20px 25px;
            margin-top: 10px;
        }
        
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #81ecec; /* Light Cyan Border like image */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .profile-text {
            margin-left: 20px;
        }
        .profile-name { font-weight: 800; font-size: 1.4rem; color: #333; line-height: 1.2; }
        .profile-title { font-size: 0.9rem; color: #777; margin-bottom: 5px; }
        .profile-contact { font-size: 0.85rem; font-weight: 600; color: #333; }

        /* 3. MAIN BODY GRID */
        .body-grid {
            display: flex;
            padding: 10px 25px;
            gap: 15px;
        }

        /* LEFT COL: Company Info & Text Actions */
        .left-col { flex: 2; }
        
        .company-label { font-weight: 800; font-size: 1.1rem; color: #333; margin-bottom: 2px; }
        .address-label { font-size: 0.85rem; color: #666; margin-bottom: 25px; line-height: 1.4; }

        .text-action-btn {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .text-action-btn:hover { color: var(--primary); padding-left: 5px; }
        .text-action-btn i { width: 30px; font-size: 1.1rem; color: #555; }

        /* RIGHT COL: Vertical Icon Stack */
        .right-col {
            flex: 0 0 60px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
            padding-top: 10px;
        }

        .circle-icon {
            width: 50px; height: 50px;
            border-radius: 50%;
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: transform 0.2s;
        }
        .circle-icon:hover { transform: scale(1.1); color: white; }

        /* Specific Colors */
        .bg-call { background-color: #00b894; }
        .bg-loc { background-color: #b2bec3; }
        .bg-wa { background-color: #25D366; }
        .bg-mail { background-color: #ff7675; }
        .bg-web { background-color: #0984e3; }

        /* 4. FOOTER SOCIALS */
        .social-row {
            margin-top: auto;
            padding: 30px 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
            background: #fff;
        }
        .social-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .powered-by {
            text-align: center;
            font-size: 0.7rem;
            color: #ccc;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="card-wrapper">
    
    <div class="header-bg">
        <?php if(!empty($c['cover'])): ?>
            <img src="<?php echo $c['cover']; ?>" class="header-overlay-img">
        <?php endif; ?>
        
        <div class="big-logo-circle">
            <img src="<?php echo $c['logo']; ?>" onerror="this.src='../img/logo2.webp'">
        </div>
    </div>

    <div class="profile-section">
        <img src="<?php echo $c['logo']; ?>" class="profile-pic" onerror="this.src='../img/logo2.webp'">
        
        <div class="profile-text">
            <div class="profile-name"><?php echo $c['name']; ?></div>
            <div class="profile-title"><?php echo $c['designation']; ?></div>
            <div class="profile-contact"><?php echo $c['phone']; ?></div>
            <?php if(!empty($c['email'])): ?>
                <div class="profile-contact" style="font-weight:400;"><?php echo $c['email']; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="body-grid">
        
        <div class="left-col">
            <div class="company-label"><?php echo $c['company']; ?></div>
            <div class="address-label"><?php echo $c['address']; ?></div>

            <a href="#" onclick="downloadVCard()" class="text-action-btn">
                <i class="fas fa-file-download"></i> Save to Contacts
            </a>
            
            <a href="#" onclick="shareCard()" class="text-action-btn">
                <i class="fas fa-share-alt"></i> Share
            </a>
            
            <a href="https://arhamprinters.pk" class="text-action-btn">
                <i class="fas fa-id-card"></i> Create Your Own
            </a>
        </div>

        <div class="right-col">
            <a href="tel:<?php echo $c['phone']; ?>" class="circle-icon bg-call">
                <i class="fas fa-phone-alt"></i>
            </a>
            
            <?php if(!empty($c['map_link'])): ?>
            <a href="<?php echo $c['map_link']; ?>" target="_blank" class="circle-icon bg-loc">
                <i class="fas fa-map-marker-alt"></i>
            </a>
            <?php endif; ?>

            <a href="https://wa.me/<?php echo $c['whatsapp']; ?>" class="circle-icon bg-wa">
                <i class="fab fa-whatsapp"></i>
            </a>

            <?php if(!empty($c['email'])): ?>
            <a href="mailto:<?php echo $c['email']; ?>" class="circle-icon bg-mail">
                <i class="fas fa-envelope"></i>
            </a>
            <?php endif; ?>

            <?php if(!empty($c['website'])): ?>
            <a href="<?php echo $c['website']; ?>" target="_blank" class="circle-icon bg-web">
                <i class="fas fa-globe"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="social-row">
        <?php if(isset($c['social'])): ?>
            <?php if(isset($c['social']['facebook'])): ?>
            <a href="<?php echo $c['social']['facebook']; ?>" class="social-btn" style="background:#3b5998;"><i class="fab fa-facebook-f"></i></a>
            <?php endif; ?>
            
            <?php if(isset($c['social']['youtube'])): ?>
            <a href="<?php echo $c['social']['youtube']; ?>" class="social-btn" style="background:#FF0000;"><i class="fab fa-youtube"></i></a>
            <?php endif; ?>

            <?php if(isset($c['social']['instagram'])): ?>
            <a href="<?php echo $c['social']['instagram']; ?>" class="social-btn" style="background:#E1306C;"><i class="fab fa-instagram"></i></a>
            <?php endif; ?>
        <?php endif; ?>
        
        <a href="https://arhamprinters.pk" class="social-btn" style="background:#333;"><i class="fas fa-print"></i></a>
    </div>

    <div class="powered-by">Powered by Arham Printers</div>

</div>

<script>
    function downloadVCard() {
        // vCard logic same as before
        const vcard = `BEGIN:VCARD
VERSION:3.0
FN:<?php echo $c['name']; ?>

ORG:<?php echo $c['company']; ?>

TITLE:<?php echo $c['designation']; ?>

TEL;TYPE=CELL:<?php echo $c['phone']; ?>

TEL;TYPE=WORK,VOICE:<?php echo $c['whatsapp']; ?>

EMAIL:<?php echo $c['email']; ?>

ADR;TYPE=WORK:;;<?php echo $c['address']; ?>;;;
END:VCARD`;

        const blob = new Blob([vcard], { type: 'text/vcard' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.download = "<?php echo str_replace(' ', '_', $c['name']); ?>.vcf";
        link.href = url;
        link.click();
    }

    function shareCard() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo $c['company']; ?>',
                url: window.location.href
            });
        } else {
            alert("Link copied!");
            navigator.clipboard.writeText(window.location.href);
        }
    }
</script>

</body>
</html>
