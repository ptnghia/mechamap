<?php

/**
 * Fix notification translation keys to use correct format
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Translation;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Fixing notification translation keys format...\n";
echo "=" . str_repeat("=", 50) . "\n";

// Keys that need to be updated
$keysToUpdate = [
    'ui.header' => 'notifications.ui.header',
    'ui.manage' => 'notifications.ui.manage'
];

$updated = 0;

foreach ($keysToUpdate as $oldKey => $newKey) {
    echo "\n📝 Processing: {$oldKey} -> {$newKey}\n";
    
    // Get all translations with this key in notifications group
    $translations = Translation::where('group_name', 'notifications')
        ->where('key', $oldKey)
        ->get();
    
    foreach ($translations as $translation) {
        echo "   🔄 Updating {$translation->locale}: {$translation->content}\n";
        $translation->update(['key' => $newKey]);
        $updated++;
    }
}

// Also update all types.* keys
echo "\n📝 Processing notification types keys...\n";

$typeKeys = Translation::where('group_name', 'notifications')
    ->where('key', 'LIKE', 'types.%')
    ->get();

foreach ($typeKeys as $translation) {
    $newKey = 'notifications.' . $translation->key;
    echo "   🔄 Updating: {$translation->key} -> {$newKey} ({$translation->locale})\n";
    $translation->update(['key' => $newKey]);
    $updated++;
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Updated: {$updated} translation entries\n";

// Test the updated keys
echo "\n🧪 TESTING UPDATED KEYS:\n";
echo "=" . str_repeat("=", 30) . "\n";

$testKeys = [
    'notifications.ui.header',
    'notifications.ui.manage',
    'notifications.types.seller_message',
    'notifications.types.thread_created'
];

foreach ($testKeys as $key) {
    $viResult = Translation::where('key', $key)->where('locale', 'vi')->first();
    $enResult = Translation::where('key', $key)->where('locale', 'en')->first();
    
    echo "\n🔍 Key: {$key}\n";
    echo "   VI: " . ($viResult ? $viResult->content : 'NOT FOUND') . "\n";
    echo "   EN: " . ($enResult ? $enResult->content : 'NOT FOUND') . "\n";
}

echo "\n🎉 Notification keys format fixed!\n";
echo "📋 Next steps:\n";
echo "   1. Clear cache: php artisan cache:clear\n";
echo "   2. Refresh notification dropdown to test\n";
echo "   3. Verify translations display correctly\n";
