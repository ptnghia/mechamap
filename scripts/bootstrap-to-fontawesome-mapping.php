<?php
/**
 * Bootstrap Icons to Font Awesome Mapping
 * Script để thay thế tất cả Bootstrap Icons bằng Font Awesome trong frontend views
 */

// Mapping Bootstrap Icons to Font Awesome
$iconMapping = [
    // Cart & Shopping
    'bi-cart3' => 'fas fa-shopping-cart',
    'bi-cart' => 'fas fa-shopping-cart',
    'bi-cart-x' => 'fas fa-shopping-cart',
    'bi-credit-card' => 'fas fa-credit-card',

    // Navigation & UI
    'bi-search' => 'fas fa-search',
    'bi-moon' => 'fas fa-moon',
    'bi-sun' => 'fas fa-sun',
    'bi-arrow-right' => 'fas fa-arrow-right',
    'bi-arrow-left' => 'fas fa-arrow-left',
    'bi-x' => 'fas fa-times',
    'bi-x-lg' => 'fas fa-times',
    'bi-plus' => 'fas fa-plus',
    'bi-plus-lg' => 'fas fa-plus',
    'bi-sliders' => 'fas fa-sliders-h',

    // User & Profile
    'bi-person-plus' => 'fas fa-user-plus',
    'bi-person-fill' => 'fas fa-user',
    'bi-people' => 'fas fa-users',
    'bi-people-fill' => 'fas fa-users',
    'bi-gear' => 'fas fa-cog',
    'bi-briefcase' => 'fas fa-briefcase',
    'bi-star' => 'fas fa-star',
    'bi-star-fill' => 'fas fa-star',
    'bi-box-arrow-right' => 'fas fa-sign-out-alt',

    // Communication
    'bi-bell' => 'fas fa-bell',
    'bi-bell-fill' => 'fas fa-bell',
    'bi-chat' => 'fas fa-comment',
    'bi-chat-dots' => 'fas fa-comment-dots',
    'bi-chat-text' => 'fas fa-comment-alt',
    'bi-chat-quote' => 'fas fa-quote-left',
    'bi-reply' => 'fas fa-reply',
    'bi-send' => 'fas fa-paper-plane',

    // Actions
    'bi-hand-thumbs-up' => 'fas fa-thumbs-up',
    'bi-bookmark' => 'far fa-bookmark',
    'bi-bookmark-fill' => 'fas fa-bookmark',
    'bi-share' => 'fas fa-share-alt',
    'bi-pencil' => 'fas fa-edit',
    'bi-trash' => 'fas fa-trash',
    'bi-eye' => 'fas fa-eye',
    'bi-clipboard' => 'fas fa-clipboard',

    // Media & Files
    'bi-image' => 'fas fa-image',
    'bi-cloud-upload' => 'fas fa-cloud-upload-alt',
    'bi-file-earmark' => 'fas fa-file',
    'bi-download' => 'fas fa-download',
    'bi-upload' => 'fas fa-upload',

    // Time & Calendar
    'bi-clock' => 'fas fa-clock',
    'bi-calendar-check' => 'fas fa-calendar-check',
    'bi-hourglass-split' => 'fas fa-hourglass-half',

    // Status & Info
    'bi-exclamation-triangle' => 'fas fa-exclamation-triangle',
    'bi-check' => 'fas fa-check',
    'bi-collection-fill' => 'fas fa-folder',
    'bi-globe2' => 'fas fa-globe',
    'bi-graph-up' => 'fas fa-chart-line',

    // Social Media
    'bi-facebook' => 'fab fa-facebook-f',
    'bi-twitter' => 'fab fa-twitter',
    'bi-whatsapp' => 'fab fa-whatsapp',

    // Additional icons found in frontend
    'bi-check-all' => 'fas fa-check-double',
    'bi-funnel' => 'fas fa-filter',
    'bi-check-lg' => 'fas fa-check',
    'bi-x-circle' => 'fas fa-times-circle',
    'bi-info-circle' => 'fas fa-info-circle',
    'bi-chat-left-text' => 'fas fa-comment-alt',
    'bi-chat-right' => 'fas fa-comment',
    'bi-bookmark-x' => 'fas fa-bookmark',
    'bi-bar-chart' => 'fas fa-chart-bar',
    'bi-envelope' => 'fas fa-envelope',
    'bi-telephone' => 'fas fa-phone',
    'bi-geo-alt' => 'fas fa-map-marker-alt',
    'bi-house' => 'fas fa-home',
    'bi-list' => 'fas fa-list',
    'bi-grid' => 'fas fa-th',
    'bi-filter' => 'fas fa-filter',
    'bi-sort-down' => 'fas fa-sort-down',
    'bi-sort-up' => 'fas fa-sort-up',
    'bi-heart' => 'fas fa-heart',
    'bi-heart-fill' => 'fas fa-heart',
    'bi-flag' => 'fas fa-flag',
    'bi-shield' => 'fas fa-shield-alt',
    'bi-lock' => 'fas fa-lock',
    'bi-unlock' => 'fas fa-unlock',
    'bi-key' => 'fas fa-key',
    'bi-file-text' => 'fas fa-file-alt',
    'bi-file-pdf' => 'fas fa-file-pdf',
    'bi-file-word' => 'fas fa-file-word',
    'bi-file-excel' => 'fas fa-file-excel',
    'bi-file-powerpoint' => 'fas fa-file-powerpoint',
    'bi-file-zip' => 'fas fa-file-archive',
    'bi-camera' => 'fas fa-camera',
    'bi-video' => 'fas fa-video',
    'bi-music-note' => 'fas fa-music',
    'bi-headphones' => 'fas fa-headphones',
    'bi-mic' => 'fas fa-microphone',
    'bi-volume-up' => 'fas fa-volume-up',
    'bi-volume-down' => 'fas fa-volume-down',
    'bi-volume-mute' => 'fas fa-volume-mute',
    'bi-play' => 'fas fa-play',
    'bi-pause' => 'fas fa-pause',
    'bi-stop' => 'fas fa-stop',
    'bi-skip-backward' => 'fas fa-step-backward',
    'bi-skip-forward' => 'fas fa-step-forward',
    'bi-fast-forward' => 'fas fa-fast-forward',
    'bi-rewind' => 'fas fa-fast-backward',
];

// Get all frontend blade files (exclude admin directory)
function getAllFrontendBladeFiles() {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('resources/views/')
    );

    foreach ($iterator as $file) {
        if ($file->isFile() &&
            $file->getExtension() === 'php' &&
            strpos($file->getFilename(), '.blade.php') !== false &&
            strpos($file->getPathname(), '/admin/') === false) {
            $files[] = str_replace('\\', '/', $file->getPathname());
        }
    }

    return $files;
}

$filesToProcess = getAllFrontendBladeFiles();

echo "🔄 BOOTSTRAP ICONS TO FONT AWESOME CONVERSION\n";
echo "=============================================\n";
echo "Converting Bootstrap Icons to Font Awesome in frontend views...\n\n";

$totalReplacements = 0;
$processedFiles = 0;

foreach ($filesToProcess as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }

    $content = file_get_contents($file);
    $originalContent = $content;
    $fileReplacements = 0;

    // Replace class patterns like "bi bi-icon-name" first
    foreach ($iconMapping as $bootstrapIcon => $fontAwesome) {
        $iconName = str_replace('bi-', '', $bootstrapIcon);
        $pattern = '/bi\s+bi-' . preg_quote($iconName, '/') . '\b/';
        $replacementCount = 0;
        $content = preg_replace($pattern, $fontAwesome, $content, -1, $replacementCount);
        $fileReplacements += $replacementCount;
    }

    // Then replace standalone Bootstrap Icons
    foreach ($iconMapping as $bootstrapIcon => $fontAwesome) {
        // Replace exact class matches
        $pattern = '/\b' . preg_quote($bootstrapIcon, '/') . '\b/';
        $replacementCount = 0;
        $content = preg_replace($pattern, $fontAwesome, $content, -1, $replacementCount);
        $fileReplacements += $replacementCount;
    }

    if ($fileReplacements > 0) {
        file_put_contents($file, $content);
        echo "✅ $file: $fileReplacements replacements\n";
        $processedFiles++;
        $totalReplacements += $fileReplacements;
    } else {
        echo "ℹ️  $file: No Bootstrap Icons found\n";
    }
}

echo "\n📊 CONVERSION SUMMARY\n";
echo "====================\n";
echo "Files processed: $processedFiles\n";
echo "Total replacements: $totalReplacements\n";
echo "Icon mapping entries: " . count($iconMapping) . "\n";

if ($totalReplacements > 0) {
    echo "\n✅ Conversion completed successfully!\n";
    echo "🔧 Next steps:\n";
    echo "1. Remove Bootstrap Icons CSS from layout\n";
    echo "2. Test all pages for icon display\n";
    echo "3. Update any remaining icons manually\n";
} else {
    echo "\nℹ️  No Bootstrap Icons found to convert.\n";
}
