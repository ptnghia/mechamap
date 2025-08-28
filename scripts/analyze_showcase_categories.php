<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SHOWCASE CATEGORIES ANALYSIS ===" . PHP_EOL;
echo PHP_EOL;

// 1. Kiểm tra showcase_categories table
echo "1. SHOWCASE CATEGORIES TABLE:" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

try {
    $categories = DB::table('showcase_categories')->where('is_active', true)->get();
    echo "Total active categories: " . $categories->count() . PHP_EOL;
    
    foreach($categories as $cat) {
        echo "- {$cat->name} (slug: {$cat->slug})" . PHP_EOL;
        if($cat->description) {
            echo "  Description: {$cat->description}" . PHP_EOL;
        }
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// 2. Kiểm tra showcase_types table
echo "2. SHOWCASE TYPES TABLE:" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

try {
    $types = DB::table('showcase_types')->where('is_active', true)->get();
    echo "Total active types: " . $types->count() . PHP_EOL;
    
    foreach($types as $type) {
        echo "- {$type->name} (slug: {$type->slug})" . PHP_EOL;
        if($type->description) {
            echo "  Description: {$type->description}" . PHP_EOL;
        }
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// 3. Phân tích thống kê theo category hiện tại
echo "3. CURRENT CATEGORY USAGE STATISTICS:" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

$showcases = DB::table('showcases')
    ->where('is_public', true)
    ->whereIn('status', ['featured', 'approved'])
    ->get();

echo "Total public showcases: " . $showcases->count() . PHP_EOL;
echo PHP_EOL;

$categoryStats = [];

foreach($showcases as $showcase) {
    $category = $showcase->category ?? 'uncategorized';
    
    if(!array_key_exists($category, $categoryStats)) {
        $categoryStats[$category] = [
            'showcase_count' => 0,
            'file_count' => 0,
            'download_count' => 0
        ];
    }
    
    $categoryStats[$category]['showcase_count']++;
    $categoryStats[$category]['download_count'] += $showcase->download_count ?? 0;
    
    if($showcase->file_attachments) {
        $files = json_decode($showcase->file_attachments, true);
        if(is_array($files)) {
            $categoryStats[$category]['file_count'] += count($files);
            foreach($files as $file) {
                $categoryStats[$category]['download_count'] += $file['download_count'] ?? 0;
            }
        }
    }
}

// Sắp xếp theo số lượng showcase
arsort($categoryStats);

foreach($categoryStats as $category => $stats) {
    echo "Category: {$category}" . PHP_EOL;
    echo "  - Showcases: {$stats['showcase_count']}" . PHP_EOL;
    echo "  - Files: {$stats['file_count']}" . PHP_EOL;
    echo "  - Downloads: {$stats['download_count']}" . PHP_EOL;
    echo PHP_EOL;
}

// 4. Mapping giữa old categories và new categories
echo "4. CATEGORY MAPPING ANALYSIS:" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

$oldToNewMapping = [
    'design' => 'thiet-ke-co-khi',
    'manufacturing' => 'san-xuat-gia-cong', 
    'analysis' => 'phan-tich-fea-cfd'
];

foreach($oldToNewMapping as $oldCat => $newSlug) {
    $newCategory = $categories->firstWhere('slug', $newSlug);
    if($newCategory) {
        $oldCount = $categoryStats[$oldCat]['showcase_count'] ?? 0;
        echo "'{$oldCat}' -> '{$newCategory->name}' ({$oldCount} showcases to migrate)" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== ANALYSIS COMPLETE ===" . PHP_EOL;
