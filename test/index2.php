<?php
// =========================================================
// 1. CONFIGURATION
// =========================================================
$config = [
    // BASIC INFO
    "mother_name" => "Mrs. Ch. Muhammad Arshad",
    "son_name" => "Ch. Faiz Rasool Warraich, Ch. Imran Arshad Warraich",
    "event_title" => "Hatm-e-Qul Ceremony",
    "date" => "05 Jan 2026",
    "day" => "Monday",
    "location" => "Mangowal Sharqi, Jalalpur Jattan",
    
    // VIP GUESTS (Photos required: javed.webp, yousaf.webp)
    "guest_mna" => ["name" => "Ch. Javed Iqbal", "title" => "MNA (Rahim Yar Khan)", "photo" => "javed.webp"],
    "guest_mpa" => ["name" => "Ch. Abdullah Yousaf", "title" => "MPA (PP-30)", "photo" => "yousaf.webp"],

    // GUEST LIST
    "guest_list" => [
        "Ch. Asif Warraich (RYK)", "Ch. Asjad Iqbal Warraich (RYK)", 
        "Ch. Mehandi Khan (Vice Chairman)", "Ch. Nadeem Aftab Warraich (Vice Chairman)", 
        "Syed Maqsood Shah (Vice Chairman)", "DSP Shahzad Asghar Virk", 
        "ASI Ch. Zulqarnain (FIA)", "Malik Zaffar", "Malik Sajid", 
        "Ch. Bilal Warraich"
    ],

    // IMAGES
    "main_photo" => "youtube.webp", 
    "gallery" => [
        "image1.webp", "image2.webp", "image3.webp", 
        "image4.webp", "image5.webp", "image6.webp"
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Status - <?php echo $config['mother_name']; ?></title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Oswald:wght@500;700&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">

    <style>
        /* --- RESET --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background: #222; display: flex; justify-content: center; align-items: center;
            min-height: 100vh; font-family: 'Roboto Condensed', sans-serif;
        }

        /* --- WHATSAPP STATUS CONTAINER (9:16 RATIO) --- */
        .poster {
            width: 100%;
            height: 100vh; /* Full Mobile Height */
            max-width: 450px; 
            aspect-ratio: 9/16;
            background: linear-gradient(180deg, #004d00 0%, #002200 100%);
            border: 2px solid #fff; 
            position: relative; overflow: hidden;
            display: flex; 
            flex-direction: column;
            justify-content: space-between; /* FIX: Distributes space evenly */
        }

        /* --- 1. HEADER --- */
        .header {
            text-align: center; padding: 10px 5px;
            background: #fff;
            border-bottom: 4px solid #ffcc00;
            flex-shrink: 0;
        }
        .bismillah { width: 120px; margin-bottom: 2px; }
        .event-tag { 
            background: #cc0000; color: white; display: inline-block; padding: 2px 10px; 
            font-size: 0.8rem; font-weight: bold; text-transform: uppercase; border-radius: 4px;
        }
        .main-title {
            font-family: 'Anton', sans-serif; font-size: 1.8rem; color: #004d00;
            text-transform: uppercase; letter-spacing: 1px;
            line-height: 1; margin-top: 5px;
        }

        /* --- 2. HERO (MAIN PHOTO) --- */
        .hero {
            position: relative; height: 22vh; /* Adjusted for balance */
            background: #000; flex-shrink: 0;
        }
        .hero-img { width: 100%; height: 100%; object-fit: cover; opacity: 0.8; }
        .hero-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%;
            background: linear-gradient(to top, #002200 20%, transparent 100%);
            padding: 5px; text-align: center;
        }
        .deceased-label { color: #ffcc00; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; }
        .deceased-name { 
            font-family: 'Oswald', sans-serif; font-size: 1.4rem; color: #fff; 
            text-shadow: 2px 2px 4px #000; font-weight: 700; text-transform: uppercase;
            line-height: 1.2;
        }

        /* --- 3. VIP GUESTS --- */
        .vip-strip {
            background: #f0f0f0; padding: 5px; display: flex; justify-content: space-around;
            border-top: 3px solid #cc0000; border-bottom: 3px solid #cc0000;
            flex-shrink: 0;
        }
        .vip-box { text-align: center; width: 48%; }
        .vip-photo { 
            width: 50px; height: 50px; border-radius: 50%; object-fit: cover; 
            border: 2px solid #004d00; margin: 0 auto; display: block;
        }
        .vip-name { font-size: 0.75rem; font-weight: 900; color: #004d00; text-transform: uppercase; margin-top: 2px; line-height: 1; }
        .vip-title { font-size: 0.6rem; color: #cc0000; font-weight: 700; }

        /* --- 4. GUEST LIST (GRID) --- */
        .guest-section { 
            padding: 5px 10px; text-align: center;
            /* REMOVED flex-grow: 1; */
            background: url('https://www.transparenttextures.com/patterns/green-dust-and-scratches.png');
        }
        .section-head { 
            color: #ffcc00; font-family: 'Oswald'; font-size: 1rem; 
            text-transform: uppercase; text-decoration: underline; margin-bottom: 5px; 
        }
        .guest-grid-box {
            display: grid; grid-template-columns: 1fr 1fr; gap: 4px;
        }
        .g-name-item {
            background: rgba(0,0,0,0.4); border: 1px solid #ffcc00; padding: 3px;
            color: #fff; font-size: 0.6rem; text-transform: uppercase;
            text-align: center; border-radius: 4px; display: flex; align-items: center; justify-content: center;
            line-height: 1.1; font-weight: 600;
        }

        /* --- 5. GALLERY (SMALL STRIP) --- */
        .gallery-section { padding: 5px 10px; background: rgba(0,0,0,0.2); flex-shrink: 0; }
        .gallery-grid {
            display: grid; grid-template-columns: repeat(6, 1fr); gap: 3px;
        }
        .g-img { 
            width: 100%; height: 40px; object-fit: cover; 
            border: 1px solid #fff; 
        }

        /* --- 6. FOOTER --- */
        .footer {
            background: #cc0000; color: white; padding: 8px; text-align: center;
            border-top: 3px solid #fff; flex-shrink: 0;
        }
        .host-label { font-size: 0.7rem; color: #ffcc00; font-weight: bold; text-transform: uppercase; }
        .host-names { font-family: 'Oswald'; font-size: 0.85rem; line-height: 1.2; text-transform: uppercase; }
        .location-strip {
            background: #000; color: #fff; font-size: 0.75rem; padding: 4px;
            margin-top: 5px; font-weight: bold; text-transform: uppercase; border-radius: 4px;
        }
        .date-strip {
            margin-top: 4px; font-size: 0.9rem; color: #ffcc00; font-weight: 800; text-transform: uppercase;
        }

    </style>
</head>
<body>

    <div class="poster">
        
        <div class="header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/27/Basmala.svg" class="bismillah">
            <div><span class="event-tag">In Loving Memory</span></div>
            <div class="main-title"><?php echo $config['event_title']; ?></div>
        </div>

        <div class="hero">
            <img src="<?php echo $config['main_photo']; ?>" class="hero-img">
            <div class="hero-overlay">
                <div class="deceased-label">Esal-e-Sawab For</div>
                <div class="deceased-name"><?php echo $config['mother_name']; ?></div>
            </div>
        </div>

        <div class="vip-strip">
            <div class="vip-box">
                <img src="<?php echo $config['guest_mna']['photo']; ?>" class="vip-photo">
                <div class="vip-name"><?php echo $config['guest_mna']['name']; ?></div>
                <div class="vip-title"><?php echo $config['guest_mna']['title']; ?></div>
            </div>
            <div class="vip-box">
                <img src="<?php echo $config['guest_mpa']['photo']; ?>" class="vip-photo">
                <div class="vip-name"><?php echo $config['guest_mpa']['name']; ?></div>
                <div class="vip-title"><?php echo $config['guest_mpa']['title']; ?></div>
            </div>
        </div>

        <div class="guest-section">
            <div class="section-head">Notable Attendees</div>
            <div class="guest-grid-box">
                <?php foreach($config['guest_list'] as $guest): ?>
                <div class="g-name-item"><?php echo $guest; ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="gallery-section">
            <div class="gallery-grid">
                <?php foreach($config['gallery'] as $img): ?>
                <img src="<?php echo $img; ?>" class="g-img">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer">
            <div class="host-label">Minjanib (Hosts):</div>
            <div class="host-names"><?php echo $config['son_name']; ?></div>
            <div class="date-strip">
                <i class="far fa-calendar-alt"></i> <?php echo $config['date']; ?> (<?php echo $config['day']; ?>)
            </div>
            <div class="location-strip">
                <i class="fas fa-map-marker-alt"></i> <?php echo $config['location']; ?>
            </div>
        </div>

    </div>

</body>
</html>
