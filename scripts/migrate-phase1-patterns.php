<?php

/**
 * Phase 1: Fix Translation Patterns
 */

echo "🔧 Phase 1: Fixing Translation Patterns\n";
echo "===================================\n\n";

$fixes = [
    'ui.common.' => 'common.',
    'ui/common.' => 'common.',
    'ui.navigation.' => 'navigation.',
    'content/alerts.' => 'common.messages.',
    'core/messages.' => 'common.messages.',
    'marketplace.cart.shopping_cart' => 'marketplace.cart.title',
    'marketplace.cart.cart_empty' => 'marketplace.cart.empty_message',
    'marketplace.cart.add_products' => 'marketplace.cart.add_items',
    'forum.search.recent_searches' => 'search.history.recent',
    'forum.search.no_recent_searches' => 'search.history.empty',
    'forum.search.popular_searches' => 'search.suggestions.popular',
];

$bladeFiles = glob('resources/views/**/*.blade.php');
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $changed = false;
    foreach ($fixes as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $changed = true;
        }
    }
    if ($changed) {
        file_put_contents($file, $content);
        echo "✅ Updated: $file\n";
    }
}
echo "✅ Phase 1 completed!\n";
