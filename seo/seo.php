<?php
// --- 1. INTELLIGENT DATA PARSING ---
// Ensure config exists to prevent errors
if (!isset($config)) { $config = []; }

// Helper Variables
$name    = $config['business']['name'] ?? "Arham Printers Client";
$tagline = $config['business']['tagline'] ?? "Premium Services";
$phone   = $config['business']['phone_primary'] ?? "";
$address = $config['business']['address'] ?? "Jalalpur Jattan, Pakistan";
$currency = $config['business']['currency'] ?? "PKR";

// Dynamic URL Calculation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Smart Description Generator
$seo_desc = "$name provides $tagline. Best services in $address. Contact us at $phone for orders and details.";

// Image for Social Media (Uses first product image if available, else a placeholder)
$social_img = isset($config['products'][0]['img']) ? $config['products'][0]['img'] : "https://arhamprinters.pk/img/logo.webp";
?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo "$name | $tagline"; ?></title>
<meta name="description" content="<?php echo $seo_desc; ?>">
<meta name="author" content="Arham Printers">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?php echo $current_url; ?>">

<meta name="theme-color" content="#991B1B">

<meta property="og:type" content="business.business">
<meta property="og:title" content="<?php echo $name; ?>">
<meta property="og:description" content="<?php echo $tagline; ?> - <?php echo $address; ?>">
<meta property="og:image" content="<?php echo $social_img; ?>">
<meta property="og:url" content="<?php echo $current_url; ?>">
<meta property="business:contact_data:street_address" content="<?php echo $address; ?>">
<meta property="business:contact_data:country_name" content="Pakistan">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $name; ?>">
<meta name="twitter:description" content="<?php echo $tagline; ?>">
<meta name="twitter:image" content="<?php echo $social_img; ?>">

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
  "@type": "LocalBusiness",
  "name": "<?php echo $name; ?>",
  "image": "<?php echo $social_img; ?>",
  "telephone": "<?php echo $phone; ?>",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "<?php echo $address; ?>",
    "addressLocality": "Jalalpur Jattan",
    "addressRegion": "Punjab",
    "postalCode": "50780",
    "addressCountry": "PK"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 32.6482613,
    "longitude": 74.213114
  },
  "url": "<?php echo $current_url; ?>",
  "priceRange": "<?php echo $currency; ?>",
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
      "opens": "00:00",
      "closes": "23:59"
    }
  ]
}
</script>