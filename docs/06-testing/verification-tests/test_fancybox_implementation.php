<?php
/**
 * Test Fancybox Implementation
 * Kiểm tra việc thay thế lightbox bằng Fancybox
 */

echo "🔍 TESTING FANCYBOX IMPLEMENTATION\n";
echo "=====================================\n\n";

// 1. Kiểm tra Fancybox được load trong layout
echo "1️⃣ Checking Fancybox in layout files...\n";
$layoutFile = 'resources/views/layouts/app.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Check Fancybox CSS
    if (strpos($content, 'fancybox.css') !== false) {
        echo "✅ Fancybox CSS loaded\n";
    } else {
        echo "❌ Fancybox CSS missing\n";
    }
    
    // Check Fancybox JS
    if (strpos($content, 'fancybox.umd.js') !== false) {
        echo "✅ Fancybox JS loaded\n";
    } else {
        echo "❌ Fancybox JS missing\n";
    }
    
    // Check initialization script
    if (strpos($content, 'Fancybox.bind') !== false) {
        echo "✅ Fancybox initialization script found\n";
    } else {
        echo "❌ Fancybox initialization script missing\n";
    }
    
    // Check for old lightbox references
    if (strpos($content, 'lightbox') !== false) {
        echo "⚠️  Old lightbox references still found\n";
    } else {
        echo "✅ No old lightbox references\n";
    }
} else {
    echo "❌ Layout file not found\n";
}

echo "\n";

// 2. Kiểm tra view files sử dụng data-fancybox
echo "2️⃣ Checking view files for data-fancybox...\n";
$viewFiles = [
    'resources/views/showcase/show.blade.php',
    'resources/views/threads/show.blade.php'
];

foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Count data-fancybox attributes
        $fancyboxCount = substr_count($content, 'data-fancybox');
        
        // Count old data-lightbox attributes
        $lightboxCount = substr_count($content, 'data-lightbox');
        
        echo "📄 " . basename($file) . ":\n";
        echo "   ✅ data-fancybox: $fancyboxCount instances\n";
        
        if ($lightboxCount > 0) {
            echo "   ⚠️  data-lightbox: $lightboxCount instances (should be 0)\n";
        } else {
            echo "   ✅ data-lightbox: 0 instances\n";
        }
        
        // Check for old lightbox CSS/JS references
        if (strpos($content, 'lightbox.min.css') !== false || strpos($content, 'lightbox.min.js') !== false) {
            echo "   ⚠️  Old lightbox CSS/JS references found\n";
        } else {
            echo "   ✅ No old lightbox CSS/JS references\n";
        }
    } else {
        echo "❌ File not found: $file\n";
    }
    echo "\n";
}

// 3. Kiểm tra file lightbox.init.js đã bị xóa
echo "3️⃣ Checking removed lightbox files...\n";
$oldFiles = [
    'public/assets/js/pages/lightbox.init.js'
];

foreach ($oldFiles as $file) {
    if (!file_exists($file)) {
        echo "✅ Removed: $file\n";
    } else {
        echo "⚠️  Still exists: $file\n";
    }
}

echo "\n";

// 4. Kiểm tra các pattern data-fancybox
echo "4️⃣ Checking Fancybox patterns...\n";
$patterns = [
    'data-fancybox="showcase-gallery"',
    'data-fancybox="thread-images"',
    'data-caption=',
    'Fancybox.bind'
];

$totalPatterns = 0;
foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        foreach ($patterns as $pattern) {
            $count = substr_count($content, $pattern);
            if ($count > 0) {
                $totalPatterns += $count;
                echo "✅ Found '$pattern' in " . basename($file) . " ($count times)\n";
            }
        }
    }
}

if ($totalPatterns > 0) {
    echo "✅ Total Fancybox patterns found: $totalPatterns\n";
} else {
    echo "❌ No Fancybox patterns found\n";
}

echo "\n";

// 5. Summary
echo "📊 SUMMARY\n";
echo "==========\n";

$layoutFile = 'resources/views/layouts/app.blade.php';
$layoutContent = file_exists($layoutFile) ? file_get_contents($layoutFile) : '';

$hasFancyboxCSS = strpos($layoutContent, 'fancybox.css') !== false;
$hasFancyboxJS = strpos($layoutContent, 'fancybox.umd.js') !== false;
$hasFancyboxInit = strpos($layoutContent, 'Fancybox.bind') !== false;
$hasOldLightbox = strpos($layoutContent, 'lightbox') !== false;

$showcaseFile = 'resources/views/showcase/show.blade.php';
$threadsFile = 'resources/views/threads/show.blade.php';

$showcaseFancybox = file_exists($showcaseFile) ? substr_count(file_get_contents($showcaseFile), 'data-fancybox') : 0;
$threadsFancybox = file_exists($threadsFile) ? substr_count(file_get_contents($threadsFile), 'data-fancybox') : 0;

$showcaseLightbox = file_exists($showcaseFile) ? substr_count(file_get_contents($showcaseFile), 'data-lightbox') : 0;
$threadsLightbox = file_exists($threadsFile) ? substr_count(file_get_contents($threadsFile), 'data-lightbox') : 0;

if ($hasFancyboxCSS && $hasFancyboxJS && $hasFancyboxInit && !$hasOldLightbox && 
    $showcaseFancybox > 0 && $threadsFancybox > 0 && 
    $showcaseLightbox == 0 && $threadsLightbox == 0) {
    echo "🎉 SUCCESS: Fancybox implementation complete!\n";
    echo "   ✅ Fancybox CSS/JS loaded\n";
    echo "   ✅ Fancybox initialization script added\n";
    echo "   ✅ View files updated to use data-fancybox\n";
    echo "   ✅ Old lightbox references removed\n";
} else {
    echo "⚠️  INCOMPLETE: Some issues found:\n";
    if (!$hasFancyboxCSS) echo "   ❌ Fancybox CSS not loaded\n";
    if (!$hasFancyboxJS) echo "   ❌ Fancybox JS not loaded\n";
    if (!$hasFancyboxInit) echo "   ❌ Fancybox initialization missing\n";
    if ($hasOldLightbox) echo "   ❌ Old lightbox references still exist\n";
    if ($showcaseFancybox == 0) echo "   ❌ Showcase not using Fancybox\n";
    if ($threadsFancybox == 0) echo "   ❌ Threads not using Fancybox\n";
    if ($showcaseLightbox > 0) echo "   ❌ Showcase still has lightbox references\n";
    if ($threadsLightbox > 0) echo "   ❌ Threads still has lightbox references\n";
}

echo "\n";
echo "🔗 Next steps:\n";
echo "1. Test image galleries in browser\n";
echo "2. Verify Fancybox functionality\n";
echo "3. Check console for any JavaScript errors\n";
echo "4. Test on mobile devices\n";

?>
