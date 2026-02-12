<?php
// feed.php - v5 (The Smart Merger)
// Uses products.json for Structure + all_prices.json for Cost
header("Content-Type: application/xml; charset=utf-8");

$domain = "https://arhamprinters.pk";
$structure_file = "products.json";    // Your Website Structure
$pricing_file = "all_prices.json";    // Your Price Database

echo '<?xml version="1.0"?>';
?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
<channel>
    <title>Arham Printers Catalog</title>
    <link><?php echo $domain; ?></link>
    <description>Premium Printing Services in Jalalpur Jattan</description>

<?php
if (file_exists($structure_file) && file_exists($pricing_file)) {
    
    // Load Data
    $catalog = json_decode(file_get_contents($structure_file), true);
    $price_db = json_decode(file_get_contents($pricing_file), true);
    
    // --- HELPER: FLATTEN PRICES FOR SEARCHING ---
    // We flatten the price DB into a simple list of [Product Name -> Variant String -> Price]
    $flat_prices = [];
    function index_prices($data, &$index, $parent_name = '') {
        foreach ($data as $key => $val) {
            // Case A: It's a Product List (Array of Variants)
            if (isset($val[0]) && is_array($val[0])) {
                foreach ($val as $v) {
                    // Extract Price
                    $p = 0;
                    if (isset($v['price'])) $p = $v['price'];
                    elseif (isset($v['bulkPrice100'])) $p = $v['bulkPrice100'];
                    elseif (isset($v['bundles'])) $p = min($v['bundles']);
                    
                    if ($p > 0) {
                        // Create a "signature" for this variant (e.g., "Single Side Matte")
                        $specs = [];
                        foreach ($v as $k2 => $v2) {
                            if (in_array($k2, ['variant_1','variant_3','finish','shape','single_side'])) $specs[] = strtolower($v2);
                        }
                        $index[$key][] = ['sig' => implode(" ", $specs), 'price' => $p];
                    }
                }
            } 
            // Case B: Category
            elseif (is_array($val)) {
                index_prices($val, $index, $key);
            }
        }
    }
    index_prices($price_db, $flat_prices);

    // --- HELPER: FIND PRICE ---
    function find_smart_price($prod_name, $variant, $flat_prices) {
        // 1. Generate signature for current item
        $specs = [];
        foreach ($variant as $k => $v) {
             if (in_array($k, ['variant_1','variant_3','finish','shape','single_side'])) $specs[] = strtolower($v);
        }
        $my_sig = implode(" ", $specs);

        // 2. Find best Product Match in DB
        // (Mapping common name differences)
        $target_name = $prod_name;
        if ($prod_name == "Letterheads") $target_name = "Letterpads";
        if ($prod_name == "Luxury Cards") $target_name = "Mat Cards"; // Fallback search
        if ($prod_name == "Flyers (Ishtihar)") $target_name = "Ishtihar";
        if ($prod_name == "Brochures") $target_name = "Pamphlets & Brochures";

        // 3. Search for Price
        if (isset($flat_prices[$target_name])) {
            foreach ($flat_prices[$target_name] as $candidate) {
                // If variant signature matches loosely
                if ($candidate['sig'] == $my_sig || strpos($candidate['sig'], $my_sig) !== false || strpos($my_sig, $candidate['sig']) !== false) {
                    return $candidate['price'];
                }
            }
            // If no variant match, return first price found (better than nothing)
            return $flat_prices[$target_name][0]['price'];
        }
        
        return 100; // Final Fallback
    }

    $id_counter = 1001;

    // --- MAIN LOOP ---
    foreach ($catalog as $category => $products) {
        foreach ($products as $prod_name => $variants) {
            foreach ($variants as $variant) {
                
                // 1. CLEAN TITLE
                $specs = [];
                $ignore = ['imageFile', 'price', 'description', 'galleryImages'];
                foreach ($variant as $k => $v) {
                    if (!in_array($k, $ignore) && is_string($v) && $v !== "default") {
                        $specs[] = $v;
                    }
                }
                $variant_str = implode(" ", $specs);
                $full_title = htmlspecialchars(trim("$prod_name $variant_str"));
                $safe_cat = htmlspecialchars($category);

                // 2. FIND PRICE
                $price = find_smart_price($prod_name, $variant, $flat_prices);

                // 3. IMAGE
                $img = isset($variant['imageFile']) ? $variant['imageFile'] : "img/logo.png";
                $image_link = $domain . "/" . ltrim($img, '/');
                $link = $domain . "/products.html?id=" . $id_counter;

                echo "
        <item>
            <g:id>AP-$id_counter</g:id>
            <g:title>$full_title</g:title>
            <g:description>High quality $full_title. Category: $safe_cat. Order from Arham Printers.</g:description>
            <g:link>$link</g:link>
            <g:image_link>$image_link</g:image_link>
            <g:condition>new</g:condition>
            <g:availability>in_stock</g:availability>
            <g:price>$price PKR</g:price>
            <g:brand>Arham Printers</g:brand>
            <g:identifier_exists>no</g:identifier_exists>
            <g:google_product_category>Business &amp; Industrial &gt; Printing Services</g:google_product_category>
        </item>";
                $id_counter++;
            }
        }
    }
}
?>
</channel>
</rss>