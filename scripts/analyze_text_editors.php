<?php
/**
 * Text Editor Analysis Script for MechaMap
 * Phân tích và thống nhất hệ thống trình soạn thảo văn bản
 */

echo "🔍 PHÂN TÍCH HỆ THỐNG TEXT EDITOR - MECHAMAP\n";
echo "=============================================\n\n";

$basePath = __DIR__ . '/../';
$results = [
    'tinymce' => [],
    'ckeditor' => [],
    'other_editors' => [],
    'conflicts' => [],
    'recommendations' => []
];

// 1. Tìm kiếm TinyMCE usage
echo "📝 1. PHÂN TÍCH TINYMCE USAGE\n";
echo "-----------------------------\n";

// TinyMCE CDN patterns
$tinymcePatterns = [
    'cdn_cloud' => '/cdn\.tiny\.cloud.*tinymce/i',
    'local_tinymce' => '/tinymce\.min\.js/i',
    'tinymce_init' => '/tinymce\.init\(/i',
    'tinymce_component' => '/tinymce-editor\.blade\.php/i'
];

// 2. Tìm kiếm CKEditor usage
echo "📝 2. PHÂN TÍCH CKEDITOR USAGE\n";
echo "------------------------------\n";

$ckeditorPatterns = [
    'ckeditor_cdn' => '/cdn\.ckeditor\.com/i',
    'classic_editor' => '/ClassicEditor\.create/i',
    'ckeditor_component' => '/ckeditor.*\.blade\.php/i',
    'ckeditor_script' => '/ckeditor.*\.js/i'
];

// 3. Tìm kiếm các editor khác
echo "📝 3. PHÂN TÍCH EDITOR KHÁC\n";
echo "---------------------------\n";

$otherEditorPatterns = [
    'quill' => '/quill.*\.js/i',
    'summernote' => '/summernote.*\.js/i',
    'froala' => '/froala.*\.js/i',
    'medium_editor' => '/medium-editor/i'
];

// Function để scan files (optimized)
function scanDirectory($directory, $patterns, $fileExtensions = ['php', 'js']) {
    $results = [];

    // Specific directories to scan
    $scanDirs = [
        'resources/views',
        'public/js',
        'public/assets/js',
        'app/View/Components'
    ];

    foreach ($scanDirs as $scanDir) {
        $fullPath = $directory . $scanDir;
        if (!is_dir($fullPath)) continue;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getPathname();
                $fileName = $file->getFilename();

                // Check file extension
                $validExtension = false;
                foreach ($fileExtensions as $ext) {
                    if (str_ends_with($fileName, '.' . $ext) ||
                        str_ends_with($fileName, '.blade.php')) {
                        $validExtension = true;
                        break;
                    }
                }

                if (!$validExtension) continue;

                $content = file_get_contents($filePath);
                $relativePath = str_replace($GLOBALS['basePath'], '', $filePath);

                foreach ($patterns as $patternName => $pattern) {
                    if (preg_match($pattern, $content, $matches)) {
                        if (!isset($results[$patternName])) {
                            $results[$patternName] = [];
                        }
                        $results[$patternName][] = [
                            'file' => $relativePath,
                            'match' => $matches[0] ?? 'Found'
                        ];
                    }
                }
            }
        }
    }

    return $results;
}

// Scan toàn bộ project
$GLOBALS['basePath'] = $basePath;

echo "🔍 Scanning TinyMCE usage...\n";
$results['tinymce'] = scanDirectory($basePath, $tinymcePatterns);

echo "🔍 Scanning CKEditor usage...\n";
$results['ckeditor'] = scanDirectory($basePath, $ckeditorPatterns);

echo "🔍 Scanning other editors...\n";
$results['other_editors'] = scanDirectory($basePath, $otherEditorPatterns);

// 4. Phân tích kết quả
echo "\n📊 KẾT QUẢ PHÂN TÍCH\n";
echo "====================\n\n";

// TinyMCE Analysis
echo "🟢 TINYMCE USAGE:\n";
if (!empty($results['tinymce'])) {
    foreach ($results['tinymce'] as $type => $files) {
        echo "  • $type: " . count($files) . " files\n";
        foreach ($files as $file) {
            echo "    - {$file['file']}\n";
        }
    }
} else {
    echo "  ❌ Không tìm thấy TinyMCE usage\n";
}

echo "\n🟡 CKEDITOR USAGE:\n";
if (!empty($results['ckeditor'])) {
    foreach ($results['ckeditor'] as $type => $files) {
        echo "  • $type: " . count($files) . " files\n";
        foreach ($files as $file) {
            echo "    - {$file['file']}\n";
        }
    }
} else {
    echo "  ✅ Không tìm thấy CKEditor usage\n";
}

echo "\n🔴 OTHER EDITORS:\n";
if (!empty($results['other_editors'])) {
    foreach ($results['other_editors'] as $type => $files) {
        echo "  • $type: " . count($files) . " files\n";
        foreach ($files as $file) {
            echo "    - {$file['file']}\n";
        }
    }
} else {
    echo "  ✅ Không tìm thấy editor khác\n";
}

// 5. Phát hiện conflicts
echo "\n⚠️  PHÁT HIỆN CONFLICTS:\n";
$hasConflicts = false;

// Check if multiple editors in same file
$allFiles = [];
foreach (['tinymce', 'ckeditor', 'other_editors'] as $editorType) {
    foreach ($results[$editorType] as $type => $files) {
        foreach ($files as $file) {
            $filePath = $file['file'];
            if (!isset($allFiles[$filePath])) {
                $allFiles[$filePath] = [];
            }
            $allFiles[$filePath][] = $editorType . ':' . $type;
        }
    }
}

foreach ($allFiles as $file => $editors) {
    if (count($editors) > 1) {
        echo "  🚨 CONFLICT in $file:\n";
        foreach ($editors as $editor) {
            echo "    - $editor\n";
        }
        $hasConflicts = true;
    }
}

if (!$hasConflicts) {
    echo "  ✅ Không phát hiện conflicts\n";
}

echo "\n📋 ĐÁNH GIÁ VÀ KHUYẾN NGHỊ\n";
echo "===========================\n";

// Count usage
$tinymceCount = 0;
$ckeditorCount = 0;
$otherCount = 0;

foreach ($results['tinymce'] as $files) {
    $tinymceCount += count($files);
}
foreach ($results['ckeditor'] as $files) {
    $ckeditorCount += count($files);
}
foreach ($results['other_editors'] as $files) {
    $otherCount += count($files);
}

echo "📊 Thống kê usage:\n";
echo "  • TinyMCE: $tinymceCount files\n";
echo "  • CKEditor: $ckeditorCount files\n";
echo "  • Other editors: $otherCount files\n\n";

// Recommendations
if ($tinymceCount > $ckeditorCount && $tinymceCount > $otherCount) {
    echo "✅ KHUYẾN NGHỊ: Sử dụng TinyMCE làm editor chính\n";
    echo "   - TinyMCE đã được sử dụng nhiều nhất\n";
    echo "   - Có component thống nhất: tinymce-editor.blade.php\n";
    echo "   - Có cấu hình tập trung: tinymce-config.js\n\n";
} elseif ($ckeditorCount > 0) {
    echo "⚠️  KHUYẾN NGHỊ: Migrate từ CKEditor sang TinyMCE\n";
    echo "   - CKEditor vẫn còn được sử dụng\n";
    echo "   - Cần thống nhất để tránh conflicts\n\n";
}

echo "🔧 HÀNH ĐỘNG CẦN THỰC HIỆN:\n";
echo "1. ✅ TinyMCE đã được thiết lập làm editor chính\n";
echo "2. 🔄 Cần kiểm tra và migrate các CKEditor còn lại\n";
echo "3. 🗑️  Loại bỏ các editor không sử dụng\n";
echo "4. 🌐 Chuyển từ TinyMCE Cloud sang self-hosted\n";
echo "5. 📝 Cập nhật documentation\n\n";

echo "✅ Phân tích hoàn tất!\n";
