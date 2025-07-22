<?php

/**
 * Test Laravel translation system directly
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Laravel Translation System\n";
echo "=====================================\n\n";

// Check current locale
echo "Current locale: " . app()->getLocale() . "\n";

// Test if translation files can be loaded
$translator = app('translator');

echo "Testing translator directly:\n";
echo "- translator->get('common.buttons.view_all'): '" . $translator->get('common.buttons.view_all') . "'\n";
echo "- translator->get('common.buttons.create'): '" . $translator->get('common.buttons.create') . "'\n";
echo "- translator->get('common.buttons.explore'): '" . $translator->get('common.buttons.explore') . "'\n";
echo "- translator->get('common.buttons.join'): '" . $translator->get('common.buttons.join') . "'\n";

echo "\nTesting if we can access the namespace directly:\n";

// Try to load the language file directly through Laravel's system
$loader = $translator->getLoader();
$messages = $loader->load(app()->getLocale(), 'common');

echo "Messages loaded: " . (empty($messages) ? 'NO' : 'YES') . "\n";
echo "Buttons section exists: " . (isset($messages['buttons']) ? 'YES' : 'NO') . "\n";

if (isset($messages['buttons'])) {
    echo "Buttons keys: " . implode(', ', array_keys($messages['buttons'])) . "\n";

    $testKeys = ['view_all', 'create', 'explore', 'join'];
    foreach ($testKeys as $key) {
        echo "- buttons.$key: " . (isset($messages['buttons'][$key]) ? "'{$messages['buttons'][$key]}'" : 'NOT FOUND') . "\n";
    }
}

// Test using helper functions
echo "\nTesting with helper functions:\n";
try {
    echo "- t_common('buttons.view_all'): '" . t_common('buttons.view_all') . "'\n";
} catch (Exception $e) {
    echo "- t_common('buttons.view_all'): ERROR - " . $e->getMessage() . "\n";
}

echo "\n";

?>
