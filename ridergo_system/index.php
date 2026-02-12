<?php
// RIDERGO MASTER ROUTER
// This script directs traffic based on the URL (Subdomain vs Main Domain)

// 1. Load Shop Data
$shops_file = 'data/shops.json';
$shops = file_exists($shops_file) ? json_decode(file_get_contents($shops_file), true) : [];

// 2. Get the Hostname (e.g., fames.arhamprinters.pk)
$host = $_SERVER['HTTP_HOST'];
$host_parts = explode('.', $host);
$subdomain = $host_parts[0]; // Gets 'fames', 'chandburger', or 'ridergo'

// 3. Define the Main System Subdomain (Where Admin/Landing lives)
$main_subdomain = 'ridergo'; 

// --- ROUTING LOGIC ---

// CASE A: It is a Shop Subdomain (e.g., fames.arhamprinters.pk)
if ($subdomain !== $main_subdomain && $subdomain !== 'www') {
    
    // Find the shop in database
    $current_shop = null;
    foreach ($shops as $shop) {
        if ($shop['subdomain'] === $subdomain) {
            $current_shop = $shop;
            break;
        }
    }

    if ($current_shop) {
        // Load the Menu Data for this shop
        $products_data = json_decode(file_get_contents('data/products.json'), true);
        
        // SHOW THE STOREFRONT
        include 'views/store.php'; 
        exit;
    } else {
        // Shop not found
        echo "<h1>404 - Shop Not Found</h1><p>This store ($subdomain) does not exist in our system.</p>";
        exit;
    }
}

// CASE B: It is the Main Domain (ridergo.arhamprinters.pk)
// Show the Landing Page / Sales Page
include 'views/landing.php';
?>