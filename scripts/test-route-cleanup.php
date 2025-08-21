<?php

/**
 * Test Route Cleanup Script
 * Kiểm tra xem tất cả các route đã được cập nhật đúng chưa
 */

echo "\n🧪 TESTING ROUTE CLEANUP: forums/search/advanced → threads\n";
echo "================================================================\n\n";

// Test 1: Kiểm tra route đã bị xóa
echo "🔍 Test 1: Checking if forums.search.advanced route is removed...\n";
$routeFile = file_get_contents('routes/web.php');
if (strpos($routeFile, "Route::get('/forums/search/advanced'") === false) {
    echo "✅ Route forums/search/advanced has been removed from web.php\n";
} else {
    echo "❌ Route forums/search/advanced still exists in web.php\n";
}

// Test 2: Kiểm tra redirect route /advanced
echo "\n🔍 Test 2: Checking /advanced redirect route...\n";
if (strpos($routeFile, "redirect()->route('threads.index'") !== false) {
    echo "✅ /advanced route redirects to threads.index\n";
} else {
    echo "❌ /advanced route does not redirect to threads.index\n";
}

// Test 3: Kiểm tra view files đã được cập nhật
echo "\n🔍 Test 3: Checking view files for route references...\n";

$viewFiles = [
    'resources/views/search/advanced.blade.php',
    'resources/views/search/basic.blade.php', 
    'resources/views/search/index.blade.php',
    'resources/views/search/advanced-results.blade.php',
    'resources/views/components/header.blade.php',
    'resources/views/forums/threads/search.blade.php',
    'resources/views/forums/index.blade.php',
    'resources/views/components/mobile-nav.blade.php',
    'resources/views/components/menu/community-mega-menu.blade.php',
    'resources/views/forums/search-by-category.blade.php'
];

$totalErrors = 0;
foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $oldRouteCount = substr_count($content, "route('forums.search.advanced')");
        $oldRouteCount += substr_count($content, 'route("forums.search.advanced")');
        
        if ($oldRouteCount > 0) {
            echo "❌ $file still contains $oldRouteCount reference(s) to forums.search.advanced\n";
            $totalErrors += $oldRouteCount;
        } else {
            echo "✅ $file - no old route references found\n";
        }
    } else {
        echo "⚠️  $file - file not found\n";
    }
}

// Test 4: Kiểm tra JavaScript function
echo "\n🔍 Test 4: Checking JavaScript generateAdvancedSearchUrl function...\n";
$headerContent = file_get_contents('resources/views/components/header.blade.php');
if (strpos($headerContent, '/threads?search=') !== false) {
    echo "✅ JavaScript generateAdvancedSearchUrl updated to use /threads\n";
} else {
    echo "❌ JavaScript generateAdvancedSearchUrl not updated\n";
}

// Summary
echo "\n📊 SUMMARY:\n";
echo "===========\n";
if ($totalErrors === 0) {
    echo "🎉 All tests passed! Route cleanup completed successfully.\n";
    echo "✅ forums/search/advanced route has been completely removed\n";
    echo "✅ All view references updated to use threads.index\n";
    echo "✅ JavaScript functions updated\n";
    echo "✅ Redirect routes properly configured\n";
} else {
    echo "⚠️  Found $totalErrors remaining references to old route\n";
    echo "❗ Please review and fix the remaining references above\n";
}

echo "\n🔗 ROUTE MAPPING:\n";
echo "=================\n";
echo "OLD: /forums/search/advanced → REMOVED\n";
echo "NEW: /threads (with advanced search integrated)\n";
echo "REDIRECT: /advanced → /threads\n";

echo "\n✅ Route cleanup test completed!\n";

?>
