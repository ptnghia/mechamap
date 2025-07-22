<?php

/**
 * CHECK TRANSLATION FILE SYNTAX
 * Kiểm tra cú pháp và cấu trúc của tất cả file translation
 */

echo "=== CHECKING TRANSLATION FILE SYNTAX ===\n\n";

$languages = ['vi', 'en'];
$errors = [];
$warnings = [];
$totalFiles = 0;
$validFiles = 0;

foreach ($languages as $lang) {
    $langDir = __DIR__ . "/resources/lang/$lang/";

    if (!is_dir($langDir)) {
        echo "❌ Language directory not found: $langDir\n";
        continue;
    }

    echo "🔍 Checking $lang translation files...\n";

    $files = glob($langDir . '*.php');

    foreach ($files as $file) {
        $filename = basename($file);
        $totalFiles++;

        echo "  📄 Checking $filename... ";

        try {
            // Check if file can be included without errors
            $content = @include $file;

            if ($content === false) {
                $errors[] = "$lang/$filename - Failed to include file";
                echo "❌ FAILED\n";
                continue;
            }

            if (!is_array($content)) {
                $errors[] = "$lang/$filename - Does not return an array";
                echo "❌ NOT ARRAY\n";
                continue;
            }

            // Check for nested array structure issues
            $hasNestedArrayIssues = false;
            $nestedArrayCount = 0;

            $checkNestedStructure = function($array, $path = '') use (&$checkNestedStructure, &$hasNestedArrayIssues, &$nestedArrayCount) {
                foreach ($array as $key => $value) {
                    $currentPath = $path ? "$path.$key" : $key;

                    if (is_array($value)) {
                        $nestedArrayCount++;

                        // Check if nested array has proper structure
                        if (empty($value)) {
                            $hasNestedArrayIssues = true;
                            return "Empty nested array at: $currentPath";
                        }

                        $result = $checkNestedStructure($value, $currentPath);
                        if ($result) {
                            return $result;
                        }
                    } elseif (!is_string($value) && !is_numeric($value) && $value !== null) {
                        $hasNestedArrayIssues = true;
                        return "Invalid value type at: $currentPath (type: " . gettype($value) . ")";
                    }
                }

                return null;
            };

            $structureError = $checkNestedStructure($content);

            if ($structureError) {
                $errors[] = "$lang/$filename - Structure issue: $structureError";
                echo "❌ STRUCTURE\n";
                continue;
            }

            // Check for common issues
            $issues = [];

            // Check for mixed array syntax
            $fileContent = file_get_contents($file);
            if (strpos($fileContent, 'array (') !== false && strpos($fileContent, '[') !== false) {
                $issues[] = "Mixed array syntax (both array() and [])";
            }

            // Check for proper PHP opening tag
            if (!str_starts_with($fileContent, '<?php')) {
                $issues[] = "Missing or incorrect PHP opening tag";
            }

            // Check for proper return statement
            if (strpos($fileContent, 'return') === false) {
                $issues[] = "Missing return statement";
            }

            if (!empty($issues)) {
                $warnings[] = "$lang/$filename - " . implode(', ', $issues);
                echo "⚠️  WARNINGS\n";
            } else {
                $validFiles++;
                echo "✅ OK\n";
            }

        } catch (ParseError $e) {
            $errors[] = "$lang/$filename - Parse error: " . $e->getMessage();
            echo "❌ PARSE ERROR\n";
        } catch (Exception $e) {
            $errors[] = "$lang/$filename - Error: " . $e->getMessage();
            echo "❌ ERROR\n";
        }
    }

    echo "\n";
}

// Summary report
echo "=== SYNTAX CHECK SUMMARY ===\n";
echo "Total files checked: $totalFiles\n";
echo "Valid files: $validFiles\n";
echo "Files with errors: " . count($errors) . "\n";
echo "Files with warnings: " . count($warnings) . "\n\n";

if (!empty($errors)) {
    echo "🚨 ERRORS FOUND:\n";
    foreach ($errors as $error) {
        echo "  ❌ $error\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS FOUND:\n";
    foreach ($warnings as $warning) {
        echo "  ⚠️  $warning\n";
    }
    echo "\n";
}

if (empty($errors) && empty($warnings)) {
    echo "🎉 All translation files are syntactically correct!\n";
} elseif (empty($errors)) {
    echo "✅ No critical errors found, only warnings.\n";
} else {
    echo "❌ Critical errors found that need to be fixed.\n";
}

echo "\n✅ Syntax check completed at " . date('Y-m-d H:i:s') . "\n";
?>
