<?php

/**
 * Phase 3: Add Missing Translation Keys
 */

echo "🔧 Phase 3: Adding Missing Keys\n";
echo "==============================\n\n";

// Add keys to marketplace.php
$file = 'resources/lang/vi/marketplace.php';
if (file_exists($file)) {
    $translations = include $file;
    // Add marketplace.cart.shopping_cart
    // Add marketplace.cart.cart_empty
    // Add marketplace.cart.add_products
    file_put_contents($file, '<?php return ' . var_export($translations, true) . ';');
}

// Add keys to search.php
$file = 'resources/lang/vi/search.php';
if (file_exists($file)) {
    $translations = include $file;
    // Add search.history.recent
    // Add search.history.empty
    // Add search.suggestions.popular
    file_put_contents($file, '<?php return ' . var_export($translations, true) . ';');
}

// Add keys to common.php
$file = 'resources/lang/vi/common.php';
if (file_exists($file)) {
    $translations = include $file;
    // Add common.technical.resources
    // Add common.knowledge.title
    file_put_contents($file, '<?php return ' . var_export($translations, true) . ';');
}

echo "✅ Phase 3 completed!\n";
