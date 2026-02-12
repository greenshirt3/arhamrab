<?php
// --- INTELLIGENT SEO GENERATOR FOR TRIBUTE PAGE ---

// 1. Data Extraction (Safe Fallbacks)
$title       = "Tribute & Dua - " . ($config['mother_name'] ?? "In Loving Memory");
$event       = $config['event_title'] ?? "Hatm-e-Qul Ceremony";
$date        = $config['date_gregorian'] ?? "";
$location    = $config['location_name'] ?? "";
$hosts       = $config['son_name'] ?? "The Family";
$image       = $config['main_photo'] ?? "youtube.webp";

// 2. Protocol & URL Calculation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$full_image_url = (strpos($image, 'http') === 0) ? $image : $protocol . $_SERVER['HTTP_HOST'] . '/' . $image;

// 3. Smart Description
$description = "Join us for the $event of $config[mother_name]. Hosted by $hosts at $location on $date. Please recite Surah Fatiha for the departed soul.";
?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo $description; ?>">
<meta name="author" content="Arham Printers">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?php echo $current_url; ?>">
<meta name="theme-color" content="#050505">

<meta property="og:type" content="event">
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $event; ?> at <?php echo $location; ?>. Click to view tribute and details.">
<meta property="og:image" content="<?php echo $full_image_url; ?>">
<meta property="og:url" content="<?php echo $current_url; ?>">
<meta property="og:site_name" content="Tribute & Dua">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $title; ?>">
<meta name="twitter:description" content="In loving memory of <?php echo $config['mother_name']; ?>.">
<meta name="twitter:image" content="<?php echo $full_image_url; ?>">

<script>
  var _paq = window._paq = window._paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//arhamprinters.pk/analytics/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '1']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>

<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "ur8auaqqg4");
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-K2QLNQZ4X3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-K2QLNQZ4X3');
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SocialEvent",
  "name": "<?php echo $event; ?> for <?php echo $config['mother_name']; ?>",
  "startDate": "<?php echo date('Y-m-d', strtotime($date)); ?>T11:00",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "location": {
    "@type": "Place",
    "name": "<?php echo $location; ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?php echo $location; ?>",
      "addressLocality": "Jalalpur Jattan",
      "addressCountry": "PK"
    }
  },
  "image": [
    "<?php echo $full_image_url; ?>"
   ],
  "description": "<?php echo $description; ?>",
  "organizer": {
    "@type": "Person",
    "name": "<?php echo $hosts; ?>"
  }
}
</script>