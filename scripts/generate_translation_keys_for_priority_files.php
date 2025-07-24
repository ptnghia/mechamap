<?php

/**
 * 🔧 MechaMap Priority Files Translation Key Generator
 *
 * Automatically generates translation keys for the top 5 priority files
 * with the most hardcoded text based on audit results.
 *
 * Target Files:
 * 1. threads/partials/showcase.blade.php - 26 strings
 * 2. threads/create.blade.php - 25 strings
 * 3. showcase/show.blade.php - 22 strings
 * 4. devices/index.blade.php - 19 strings
 * 5. layouts/app.blade.php - 18 strings
 *
 * Usage: php scripts/generate_translation_keys_for_priority_files.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

class PriorityFilesTranslationGenerator
{
    private $priorityFiles = [
        'threads/partials/showcase.blade.php' => [
            'target_count' => 26,
            'vi_count' => 19,
            'en_count' => 7,
            'categories' => ['categories', 'ui', 'editor', 'placeholders']
        ],
        'threads/create.blade.php' => [
            'target_count' => 25,
            'vi_count' => 24,
            'en_count' => 1,
            'categories' => ['form', 'validation', 'actions', 'placeholders']
        ],
        'showcase/show.blade.php' => [
            'target_count' => 22,
            'vi_count' => 18,
            'en_count' => 4,
            'categories' => ['actions', 'status', 'content', 'navigation']
        ],
        'devices/index.blade.php' => [
            'target_count' => 19,
            'vi_count' => 19,
            'en_count' => 0,
            'categories' => ['table', 'filters', 'actions', 'status']
        ],
        'layouts/app.blade.php' => [
            'target_count' => 18,
            'vi_count' => 16,
            'en_count' => 2,
            'categories' => ['navigation', 'meta', 'ui', 'global']
        ]
    ];

    private $viewsPath;
    private $langPath;
    private $results = [];
    private $existingTranslations = [];
    private $existingKeys = [];

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../resources/views';
        $this->langPath = __DIR__ . '/../resources/lang';
        $this->loadExistingTranslations();
    }

    public function generate()
    {
        echo "🔧 Starting Priority Files Translation Key Generation...\n";
        echo "=====================================================\n\n";

        foreach ($this->priorityFiles as $file => $config) {
            echo "📁 Processing: {$file}\n";
            $this->processFile($file, $config);
            echo "\n";
        }

        $this->generateSummaryReport();
        $this->createConversionGuide();
    }

    private function loadExistingTranslations()
    {
        echo "📚 Loading existing translations...\n";

        foreach (['vi', 'en'] as $locale) {
            $localeDir = $this->langPath . '/' . $locale;
            if (!is_dir($localeDir)) continue;

            $files = glob($localeDir . '/*.php');
            foreach ($files as $file) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $translations = include $file;

                if (is_array($translations)) {
                    $this->existingTranslations[$locale][$fileName] = $translations;

                    // Flatten keys for easy lookup
                    $this->flattenKeys($translations, $fileName, '', $locale);
                }
            }
        }

        echo "   Loaded " . count($this->existingKeys) . " existing translation keys\n";
    }

    private function flattenKeys($array, $file, $prefix, $locale)
    {
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            $translationKey = "{$file}.{$fullKey}";

            if (is_array($value)) {
                $this->flattenKeys($value, $file, $fullKey, $locale);
            } else {
                $this->existingKeys[$translationKey][$locale] = $value;
            }
        }
    }

    private function processFile($relativePath, $config)
    {
        $fullPath = $this->viewsPath . '/' . $relativePath;

        if (!file_exists($fullPath)) {
            echo "❌ File not found: {$fullPath}\n";
            return;
        }

        $content = file_get_contents($fullPath);
        $hardcodedStrings = $this->extractHardcodedText($content);

        echo "   Found {$hardcodedStrings['total']} hardcoded strings\n";
        echo "   Vietnamese: {$hardcodedStrings['vietnamese']}\n";
        echo "   English: {$hardcodedStrings['english']}\n";

        // Generate translation keys based on file type
        $translationKeys = $this->generateKeysForFile($relativePath, $hardcodedStrings['strings'], $config);

        // Create translation files
        $this->createTranslationFiles($relativePath, $translationKeys);

        // Generate conversion suggestions
        $this->generateConversionSuggestions($relativePath, $hardcodedStrings['strings'], $translationKeys);

        $this->results[$relativePath] = [
            'config' => $config,
            'found' => $hardcodedStrings,
            'keys' => $translationKeys
        ];
    }

    private function extractHardcodedText($content)
    {
        $strings = [];
        $vietnamese = 0;
        $english = 0;

        $lines = explode("\n", $content);
        foreach ($lines as $lineNumber => $line) {
            // Skip lines with existing translation functions (comprehensive check)
            if ($this->hasExistingTranslationCall($line)) {
                continue;
            }

            // Vietnamese text pattern
            if (preg_match_all('/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/', $line, $matches)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match);
                    if ($this->isValidText($text) && !$this->hasExistingTranslation($text)) {
                        $strings[] = [
                            'text' => $text,
                            'line' => $lineNumber + 1,
                            'type' => 'vietnamese',
                            'context' => trim($line)
                        ];
                        $vietnamese++;
                    }
                }
            }

            // English text pattern
            if (preg_match_all('/["\']([A-Z][a-zA-Z\s]{3,50})["\']/', $line, $matches)) {
                foreach ($matches[1] as $match) {
                    $text = trim($match);
                    if ($this->isValidText($text) && $this->isEnglishText($text) && !$this->hasExistingTranslation($text)) {
                        $strings[] = [
                            'text' => $text,
                            'line' => $lineNumber + 1,
                            'type' => 'english',
                            'context' => trim($line)
                        ];
                        $english++;
                    }
                }
            }
        }

        return [
            'strings' => $strings,
            'total' => count($strings),
            'vietnamese' => $vietnamese,
            'english' => $english
        ];
    }

    private function isValidText($text)
    {
        if (strlen($text) < 2 || strlen($text) > 100) return false;
        if (preg_match('/^[\d\s\-_\.]+$/', $text)) return false;
        if (preg_match('/^[a-z\-_]+$/', $text)) return false;
        if (preg_match('/\.(css|js|php|html)$/', $text)) return false;
        if (preg_match('/^(http|https|mailto|tel):/', $text)) return false;
        return true;
    }

    private function hasExistingTranslationCall($line)
    {
        // Comprehensive check for existing translation functions
        $patterns = [
            '/(__\(|trans\(|@lang\(|@t\()/',           // Laravel standard functions
            '/t_(core|ui|user|feature|content)\(/',     // MechaMap helper functions
            '/\{\{\s*__\(/',                           // Blade echo with translation
            '/\{\!\!\s*__\(/',                         // Blade raw echo with translation
            '/@(core|ui|user|feature|content)\(/',     // MechaMap Blade directives
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    private function hasExistingTranslation($text)
    {
        // Check if this text already exists in any translation file
        foreach ($this->existingKeys as $key => $locales) {
            foreach ($locales as $locale => $value) {
                if (trim($value) === trim($text)) {
                    echo "   ⏭️  Skipping '{$text}' - already exists as key: {$key}\n";
                    return true;
                }
            }
        }

        return false;
    }

    private function isEnglishText($text)
    {
        return !preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/', $text);
    }

    private function generateKeysForFile($filePath, $strings, $config)
    {
        $keys = [];
        $fileKey = $this->getFileKey($filePath);
        $usedKeys = [];

        foreach ($strings as $index => $string) {
            $category = $this->categorizeString($string['text'], $string['context'], $config['categories']);
            $keyName = $this->generateKeyName($string['text']);

            $fullKey = "{$fileKey}.{$category}.{$keyName}";

            // Check for duplicate keys and make unique
            $originalKey = $fullKey;
            $counter = 1;
            while (isset($usedKeys[$fullKey]) || $this->keyExists($fullKey)) {
                $fullKey = $originalKey . '_' . $counter;
                $counter++;
            }
            $usedKeys[$fullKey] = true;

            if ($fullKey !== $originalKey) {
                echo "   🔄 Key conflict resolved: {$originalKey} → {$fullKey}\n";
            }

            $keys[] = [
                'key' => $fullKey,
                'original_text' => $string['text'],
                'type' => $string['type'],
                'category' => $category,
                'line' => $string['line'],
                'context' => $string['context'],
                'vietnamese_translation' => $string['type'] === 'vietnamese' ? $string['text'] : $this->translateToVietnamese($string['text']),
                'english_translation' => $string['type'] === 'english' ? $string['text'] : $this->translateToEnglish($string['text'])
            ];
        }

        return $keys;
    }

    private function keyExists($key)
    {
        return isset($this->existingKeys[$key]);
    }

    private function getFileKey($filePath)
    {
        $pathParts = explode('/', $filePath);
        $fileName = pathinfo(end($pathParts), PATHINFO_FILENAME);

        // Remove .blade from filename
        $fileName = str_replace('.blade', '', $fileName);

        if (count($pathParts) > 1) {
            return $pathParts[0] . '.' . $fileName;
        }

        return $fileName;
    }

    private function categorizeString($text, $context, $categories)
    {
        // Smart categorization based on context and content
        if (strpos($context, 'placeholder') !== false) return 'placeholders';
        if (strpos($context, 'option value') !== false) return 'options';
        if (strpos($context, 'btn') !== false || strpos($context, 'button') !== false) return 'actions';
        if (strpos($context, 'label') !== false) return 'labels';
        if (strpos($context, 'title') !== false || strpos($context, '<h') !== false) return 'titles';
        if (strpos($context, 'alert') !== false || strpos($context, 'message') !== false) return 'messages';

        // Default to first category
        return $categories[0] ?? 'general';
    }

    private function generateKeyName($text)
    {
        // Convert text to snake_case key
        $key = strtolower($text);
        $key = preg_replace('/[^a-zA-Z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', $key);
        $key = trim($key, '_');

        // Limit length
        if (strlen($key) > 50) {
            $words = explode('_', $key);
            $key = implode('_', array_slice($words, 0, 5));
        }

        return $key ?: 'text_' . substr(md5($text), 0, 8);
    }

    private function translateToVietnamese($englishText)
    {
        // Simple translation mapping for common terms
        $translations = [
            'Close' => 'Đóng',
            'Save' => 'Lưu',
            'Cancel' => 'Hủy',
            'Delete' => 'Xóa',
            'Edit' => 'Sửa',
            'Create' => 'Tạo',
            'View' => 'Xem',
            'Back' => 'Quay lại',
            'Next' => 'Tiếp theo',
            'Submit' => 'Gửi',
            'Search' => 'Tìm kiếm',
            'Filter' => 'Lọc',
            'Sort' => 'Sắp xếp',
            'Loading' => 'Đang tải',
            'Error' => 'Lỗi',
            'Success' => 'Thành công',
            'Warning' => 'Cảnh báo',
            'Info' => 'Thông tin'
        ];

        return $translations[$englishText] ?? "[VI] {$englishText}";
    }

    private function translateToEnglish($vietnameseText)
    {
        // Simple reverse translation for common terms
        $translations = [
            'Đóng' => 'Close',
            'Lưu' => 'Save',
            'Hủy' => 'Cancel',
            'Xóa' => 'Delete',
            'Sửa' => 'Edit',
            'Tạo' => 'Create',
            'Xem' => 'View',
            'Quay lại' => 'Back',
            'Tiếp theo' => 'Next',
            'Gửi' => 'Submit',
            'Tìm kiếm' => 'Search',
            'Lọc' => 'Filter',
            'Sắp xếp' => 'Sort',
            'Đang tải' => 'Loading',
            'Lỗi' => 'Error',
            'Thành công' => 'Success',
            'Cảnh báo' => 'Warning',
            'Thông tin' => 'Info'
        ];

        return $translations[$vietnameseText] ?? "[EN] {$vietnameseText}";
    }

    private function createTranslationFiles($filePath, $keys)
    {
        $fileKey = $this->getFileKey($filePath);
        $groupedKeys = [];

        // Group keys by category
        foreach ($keys as $key) {
            $groupedKeys[$key['category']][$key['key']] = [
                'vi' => $key['vietnamese_translation'],
                'en' => $key['english_translation']
            ];
        }

        // Create translation files for each category
        foreach ($groupedKeys as $category => $categoryKeys) {
            $this->createTranslationFile($fileKey, $category, $categoryKeys);
        }
    }

    private function createTranslationFile($fileKey, $category, $keys)
    {
        foreach (['vi', 'en'] as $locale) {
            $dir = $this->langPath . '/' . $locale;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $fileName = $fileKey . '_' . $category . '.php';
            $filePath = $dir . '/' . $fileName;

            $translations = [];
            foreach ($keys as $fullKey => $localeTexts) {
                // Extract just the key part after the last dot
                $keyParts = explode('.', $fullKey);
                $simpleKey = end($keyParts);
                $translations[$simpleKey] = $localeTexts[$locale];
            }

            // Check if file already exists and merge
            if (file_exists($filePath)) {
                $existingTranslations = include $filePath;
                if (is_array($existingTranslations)) {
                    $translations = array_merge($existingTranslations, $translations);
                    echo "   🔄 Merged with existing: {$locale}/{$fileName}\n";
                }
            }

            $content = "<?php\n\n";
            $content .= "/**\n";
            $content .= " * {$fileKey} {$category} translations ({$locale})\n";
            $content .= " * Auto-generated: " . date('Y-m-d H:i:s') . "\n";
            $content .= " * Keys: " . count($translations) . "\n";
            $content .= " */\n\n";
            $content .= "return " . var_export($translations, true) . ";\n";

            file_put_contents($filePath, $content);
            echo "   ✅ Created: {$locale}/{$fileName} (" . count($translations) . " keys)\n";
        }
    }

    private function generateConversionSuggestions($filePath, $strings, $keys)
    {
        $suggestions = [];

        foreach ($keys as $key) {
            $suggestions[] = [
                'line' => $key['line'],
                'original' => $key['original_text'],
                'replacement' => "__('${key['key']}')",
                'context' => $key['context']
            ];
        }

        // Save suggestions to file
        $suggestionsFile = __DIR__ . "/../storage/localization/conversion_suggestions_" . str_replace('/', '_', $filePath) . ".json";

        if (!is_dir(dirname($suggestionsFile))) {
            mkdir(dirname($suggestionsFile), 0755, true);
        }

        file_put_contents($suggestionsFile, json_encode($suggestions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "   📝 Conversion suggestions saved to: " . basename($suggestionsFile) . "\n";
    }

    private function generateSummaryReport()
    {
        echo "\n📊 GENERATION SUMMARY\n";
        echo "====================\n";

        $totalFiles = count($this->results);
        $totalStrings = 0;
        $totalKeys = 0;

        foreach ($this->results as $file => $result) {
            $totalStrings += $result['found']['total'];
            $totalKeys += count($result['keys']);

            echo sprintf("%-40s %3d strings → %3d keys\n",
                basename($file),
                $result['found']['total'],
                count($result['keys'])
            );
        }

        echo "\nTotal files processed: {$totalFiles}\n";
        echo "Total hardcoded strings: {$totalStrings}\n";
        echo "Total translation keys generated: {$totalKeys}\n";
        echo "\n✅ Translation key generation completed!\n";
    }

    private function createConversionGuide()
    {
        $guide = "# 📋 Manual Conversion Guide\n\n";
        $guide .= "This guide provides step-by-step instructions for manually converting hardcoded text to translation keys.\n\n";

        foreach ($this->results as $file => $result) {
            $guide .= "## File: {$file}\n\n";
            $guide .= "**Strings to convert**: {$result['found']['total']}\n";
            $guide .= "**Translation files created**: " . count($result['config']['categories']) . "\n\n";

            $guide .= "### Conversion Steps:\n";
            $guide .= "1. Open `resources/views/{$file}`\n";
            $guide .= "2. Use conversion suggestions in `storage/localization/conversion_suggestions_" . str_replace('/', '_', $file) . ".json`\n";
            $guide .= "3. Replace each hardcoded string with corresponding `__()` call\n";
            $guide .= "4. Test functionality after each replacement\n\n";
        }

        $guideFile = __DIR__ . "/../storage/localization/manual_conversion_guide.md";
        file_put_contents($guideFile, $guide);
        echo "📖 Manual conversion guide created: " . basename($guideFile) . "\n";
    }
}

// Run the generator
$generator = new PriorityFilesTranslationGenerator();
$generator->generate();
