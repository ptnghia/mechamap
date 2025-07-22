<?php

/**
 * FIND ARRAY VALUES IN TRANSLATION FILES
 * Tìm các key có giá trị là array thay vì string
 */

echo "=== FINDING ARRAY VALUES IN TRANSLATION FILES ===\n\n";

$languages = ['vi', 'en'];
$arrayValues = [];

function scanForArrayValues($array, $prefix = '', $file = '') {
    global $arrayValues;
    
    foreach ($array as $key => $value) {
        $fullKey = $prefix ? "$prefix.$key" : $key;
        
        if (is_array($value)) {
            // Check if this array contains only string values (valid nested structure)
            $hasOnlyStrings = true;
            $hasNestedArrays = false;
            
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue)) {
                    $hasNestedArrays = true;
                    // Recursively scan nested arrays
                    scanForArrayValues($value, $fullKey, $file);
                } elseif (!is_string($subValue) && !is_numeric($subValue)) {
                    $hasOnlyStrings = false;
                }
            }
            
            // If this array has numeric keys and string values, it's likely an error
            if (!$hasNestedArrays && count($value) > 0) {
                $firstKey = array_key_first($value);
                if (is_numeric($firstKey)) {
                    $arrayValues[] = [
                        'file' => $file,
                        'key' => $fullKey,
                        'value' => $value,
                        'type' => 'numeric_array'
                    ];
                }
            }
        }
    }
}

foreach ($languages as $lang) {
    $langDir = __DIR__ . "/resources/lang/$lang/";
    
    if (!is_dir($langDir)) {
        continue;
    }
    
    echo "🔍 Checking $lang translation files...\n";
    
    $files = glob($langDir . '*.php');
    
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        
        try {
            $content = include $file;
            
            if (is_array($content)) {
                scanForArrayValues($content, '', "$lang/$filename.php");
            }
            
        } catch (Exception $e) {
            echo "❌ Error loading $filename.php: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== RESULTS ===\n";

if (empty($arrayValues)) {
    echo "✅ No problematic array values found!\n";
} else {
    echo "⚠️  Found " . count($arrayValues) . " problematic array values:\n\n";
    
    foreach ($arrayValues as $item) {
        echo "📁 File: {$item['file']}\n";
        echo "🔑 Key: {$item['key']}\n";
        echo "📄 Value: " . json_encode($item['value'], JSON_UNESCAPED_UNICODE) . "\n";
        echo "🏷️  Type: {$item['type']}\n";
        echo "---\n";
    }
    
    echo "\n💡 Recommendations:\n";
    echo "1. Convert numeric arrays to single string values\n";
    echo "2. Use the first value if multiple identical values exist\n";
    echo "3. Clear view cache after fixing: php artisan view:clear\n";
}

echo "\n✅ Array values scan completed at " . date('Y-m-d H:i:s') . "\n";
?>
