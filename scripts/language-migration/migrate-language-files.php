<?php
/**
 * Language Files Migration Script
 *
 * Purpose: Migrate keys from oversized messages.php to focused files
 * Author: MechaMap Development Team
 * Date: 2025-07-12
 *
 * Usage: php migrate-language-files.php [--dry-run] [--verbose]
 */

require_once __DIR__ . '/../../vendor/autoload.php';

class LanguageMigration
{
    private $basePath;
    private $backupPath;
    private $dryRun = false;
    private $verbose = false;
    private $migrationLog = [];

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: realpath(__DIR__ . '/../../resources/lang');
        $this->backupPath = __DIR__ . '/../../backup/language-files-' . date('Ymd_His');

        if (!$this->basePath) {
            throw new Exception("Language files path not found");
        }
    }

    public function run($options = [])
    {
        $this->dryRun = $options['dry-run'] ?? false;
        $this->verbose = $options['verbose'] ?? false;

        $this->log("🚀 Starting Language Files Migration");
        $this->log("Base Path: {$this->basePath}");
        $this->log("Dry Run: " . ($this->dryRun ? 'YES' : 'NO'));

        try {
            // Step 1: Create backup
            $this->createBackup();

            // Step 2: Load current messages.php
            $messagesVi = $this->loadLanguageFile('vi/messages.php');
            $messagesEn = $this->loadLanguageFile('en/messages.php');

            // Step 3: Create new file structures
            $this->createNewFileStructures($messagesVi, $messagesEn);

            // Step 4: Eliminate duplicates
            $this->eliminateDuplicates();

            // Step 5: Validate migration
            $this->validateMigration();

            $this->log("✅ Migration completed successfully!");
            $this->generateReport();

        } catch (Exception $e) {
            $this->log("❌ Migration failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function createBackup()
    {
        $this->log("📦 Creating backup...");

        if (!$this->dryRun) {
            if (!is_dir($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            // Use Windows-compatible copy command
            $sourceDir = str_replace('/', '\\', $this->basePath);
            $targetDir = str_replace('/', '\\', $this->backupPath);
            $this->executeCommand("xcopy \"{$sourceDir}\" \"{$targetDir}\" /E /I /Y");
        }

        $this->log("✅ Backup created at: {$this->backupPath}");
    }

    private function loadLanguageFile($relativePath)
    {
        $fullPath = $this->basePath . '/' . $relativePath;

        if (!file_exists($fullPath)) {
            throw new Exception("Language file not found: {$fullPath}");
        }

        $this->log("📖 Loading: {$relativePath}");
        return include $fullPath;
    }

    private function createNewFileStructures($messagesVi, $messagesEn)
    {
        $this->log("🏗️ Creating new file structures...");

        // Define migration mappings
        $mappings = [
            'nav.php' => $this->extractNavKeys($messagesVi, $messagesEn),
            'ui.php' => $this->extractUiKeys($messagesVi, $messagesEn),
            'auth.php' => $this->extractAuthKeys($messagesVi, $messagesEn),
            'marketplace.php' => $this->extractMarketplaceKeys($messagesVi, $messagesEn),
            'forum.php' => $this->extractForumKeys($messagesVi, $messagesEn),
            'common.php' => $this->extractCommonKeys($messagesVi, $messagesEn),
        ];

        foreach ($mappings as $filename => $data) {
            $this->createLanguageFile('vi/' . $filename, $data['vi']);
            $this->createLanguageFile('en/' . $filename, $data['en']);
        }
    }

    private function extractNavKeys($messagesVi, $messagesEn)
    {
        $navKeysVi = [
            'main' => [
                'home' => $messagesVi['navigation']['home'] ?? 'Trang chủ',
                'marketplace' => 'Thị trường', // Fix inconsistent value
                'community' => $messagesVi['navigation']['community'] ?? 'Cộng đồng',
                'forums' => $messagesVi['navigation']['forums'] ?? 'Diễn đàn',
                'showcases' => $messagesVi['navigation']['showcases'] ?? 'Dự án',
                'knowledge' => $messagesVi['navigation']['knowledge'] ?? 'Kiến thức',
                'help' => $messagesVi['navigation']['help'] ?? 'Trợ giúp',
                'about' => $messagesVi['navigation']['about'] ?? 'Giới thiệu',
                'contact' => $messagesVi['navigation']['contact'] ?? 'Liên hệ',
            ],
            'user' => [
                'profile' => $messagesVi['navigation']['profile'] ?? 'Hồ sơ',
                'settings' => $messagesVi['navigation']['settings'] ?? 'Cài đặt',
                'dashboard' => $messagesVi['navigation']['dashboard'] ?? 'Bảng điều khiển',
                'notifications' => $messagesVi['navigation']['notifications'] ?? 'Thông báo',
                'messages' => $messagesVi['navigation']['messages'] ?? 'Tin nhắn',
            ],
            'auth' => [
                'login' => $messagesVi['navigation']['login'] ?? 'Đăng nhập',
                'register' => $messagesVi['navigation']['register'] ?? 'Đăng ký',
                'logout' => $messagesVi['navigation']['logout'] ?? 'Đăng xuất',
            ],
        ];

        $navKeysEn = [
            'main' => [
                'home' => $messagesEn['navigation']['home'] ?? 'Home',
                'marketplace' => 'Marketplace',
                'community' => $messagesEn['navigation']['community'] ?? 'Community',
                'forums' => $messagesEn['navigation']['forums'] ?? 'Forums',
                'showcases' => $messagesEn['navigation']['showcases'] ?? 'Showcases',
                'knowledge' => $messagesEn['navigation']['knowledge'] ?? 'Knowledge',
                'help' => $messagesEn['navigation']['help'] ?? 'Help',
                'about' => $messagesEn['navigation']['about'] ?? 'About',
                'contact' => $messagesEn['navigation']['contact'] ?? 'Contact',
            ],
            'user' => [
                'profile' => $messagesEn['navigation']['profile'] ?? 'Profile',
                'settings' => $messagesEn['navigation']['settings'] ?? 'Settings',
                'dashboard' => $messagesEn['navigation']['dashboard'] ?? 'Dashboard',
                'notifications' => $messagesEn['navigation']['notifications'] ?? 'Notifications',
                'messages' => $messagesEn['navigation']['messages'] ?? 'Messages',
            ],
            'auth' => [
                'login' => $messagesEn['navigation']['login'] ?? 'Login',
                'register' => $messagesEn['navigation']['register'] ?? 'Register',
                'logout' => $messagesEn['navigation']['logout'] ?? 'Logout',
            ],
        ];

        return ['vi' => $navKeysVi, 'en' => $navKeysEn];
    }

    private function extractUiKeys($messagesVi, $messagesEn)
    {
        $uiKeysVi = [
            'actions' => [
                'save' => 'Lưu',
                'cancel' => 'Hủy',
                'delete' => 'Xóa',
                'edit' => 'Sửa',
                'view' => 'Xem',
                'create' => 'Tạo',
                'update' => 'Cập nhật',
                'submit' => 'Gửi',
                'reset' => 'Đặt lại',
                'search' => 'Tìm kiếm',
                'filter' => 'Lọc',
                'sort' => 'Sắp xếp',
            ],
            'status' => [
                'loading' => 'Đang tải...',
                'saving' => 'Đang lưu...',
                'success' => 'Thành công',
                'error' => 'Lỗi',
                'warning' => 'Cảnh báo',
                'info' => 'Thông tin',
            ],
            'common' => [
                'title' => 'Tiêu đề',
                'description' => 'Mô tả',
                'content' => 'Nội dung',
                'views' => 'Lượt xem',
                'comments' => 'Bình luận',
                'likes' => 'Thích',
                'updated' => 'Cập nhật',
            ],
        ];

        $uiKeysEn = [
            'actions' => [
                'save' => 'Save',
                'cancel' => 'Cancel',
                'delete' => 'Delete',
                'edit' => 'Edit',
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'submit' => 'Submit',
                'reset' => 'Reset',
                'search' => 'Search',
                'filter' => 'Filter',
                'sort' => 'Sort',
            ],
            'status' => [
                'loading' => 'Loading...',
                'saving' => 'Saving...',
                'success' => 'Success',
                'error' => 'Error',
                'warning' => 'Warning',
                'info' => 'Information',
            ],
            'common' => [
                'title' => 'Title',
                'description' => 'Description',
                'content' => 'Content',
                'views' => 'Views',
                'comments' => 'Comments',
                'likes' => 'Likes',
                'updated' => 'Updated',
            ],
        ];

        return ['vi' => $uiKeysVi, 'en' => $uiKeysEn];
    }

    private function extractAuthKeys($messagesVi, $messagesEn)
    {
        // Extract auth-related keys from messages.php
        $authKeysVi = [
            'login' => [
                'title' => 'Đăng nhập',
                'email' => 'Email',
                'password' => 'Mật khẩu',
                'remember' => 'Ghi nhớ đăng nhập',
                'forgot_password' => 'Quên mật khẩu?',
                'submit' => 'Đăng nhập',
            ],
            'register' => [
                'title' => 'Đăng ký',
                'name' => 'Họ tên',
                'username' => 'Tên đăng nhập',
                'email' => 'Email',
                'password' => 'Mật khẩu',
                'password_confirmation' => 'Xác nhận mật khẩu',
                'submit' => 'Đăng ký',
            ],
        ];

        $authKeysEn = [
            'login' => [
                'title' => 'Login',
                'email' => 'Email',
                'password' => 'Password',
                'remember' => 'Remember me',
                'forgot_password' => 'Forgot password?',
                'submit' => 'Login',
            ],
            'register' => [
                'title' => 'Register',
                'name' => 'Full name',
                'username' => 'Username',
                'email' => 'Email',
                'password' => 'Password',
                'password_confirmation' => 'Confirm password',
                'submit' => 'Register',
            ],
        ];

        return ['vi' => $authKeysVi, 'en' => $authKeysEn];
    }

    private function extractMarketplaceKeys($messagesVi, $messagesEn)
    {
        // Enhanced marketplace keys with cart functionality
        $marketplaceKeysVi = [
            'products' => [
                'title' => 'Sản phẩm',
                'all' => 'Tất cả sản phẩm',
                'featured' => 'Sản phẩm nổi bật',
                'add_to_cart' => 'Thêm vào giỏ',
                'buy_now' => 'Mua ngay',
            ],
            'cart' => [
                'title' => 'Giỏ hàng',
                'empty' => 'Giỏ hàng trống',
                'subtotal' => 'Tạm tính',
                'checkout' => 'Thanh toán',
                'remove' => 'Xóa',
            ],
            'categories' => [
                'title' => 'Danh mục',
                'all' => 'Tất cả danh mục',
            ],
        ];

        $marketplaceKeysEn = [
            'products' => [
                'title' => 'Products',
                'all' => 'All Products',
                'featured' => 'Featured Products',
                'add_to_cart' => 'Add to Cart',
                'buy_now' => 'Buy Now',
            ],
            'cart' => [
                'title' => 'Shopping Cart',
                'empty' => 'Cart is empty',
                'subtotal' => 'Subtotal',
                'checkout' => 'Checkout',
                'remove' => 'Remove',
            ],
            'categories' => [
                'title' => 'Categories',
                'all' => 'All Categories',
            ],
        ];

        return ['vi' => $marketplaceKeysVi, 'en' => $marketplaceKeysEn];
    }

    private function extractForumKeys($messagesVi, $messagesEn)
    {
        // Enhanced forum keys
        $forumKeysVi = [
            'forums' => [
                'title' => 'Diễn đàn',
                'categories' => 'Danh mục diễn đàn',
                'latest' => 'Mới nhất',
                'popular' => 'Phổ biến',
            ],
            'threads' => [
                'title' => 'Chủ đề',
                'create' => 'Tạo chủ đề',
                'reply' => 'Trả lời',
                'views' => 'Lượt xem',
                'replies' => 'Trả lời',
            ],
            'search' => [
                'title' => 'Tìm kiếm',
                'placeholder' => 'Tìm kiếm trong diễn đàn...',
                'advanced' => 'Tìm kiếm nâng cao',
                'results' => 'Kết quả',
            ],
        ];

        $forumKeysEn = [
            'forums' => [
                'title' => 'Forums',
                'categories' => 'Forum Categories',
                'latest' => 'Latest',
                'popular' => 'Popular',
            ],
            'threads' => [
                'title' => 'Threads',
                'create' => 'Create Thread',
                'reply' => 'Reply',
                'views' => 'Views',
                'replies' => 'Replies',
            ],
            'search' => [
                'title' => 'Search',
                'placeholder' => 'Search in forums...',
                'advanced' => 'Advanced Search',
                'results' => 'Results',
            ],
        ];

        return ['vi' => $forumKeysVi, 'en' => $forumKeysEn];
    }

    private function extractCommonKeys($messagesVi, $messagesEn)
    {
        $commonKeysVi = [
            'site' => [
                'name' => 'MechaMap',
                'tagline' => 'Diễn đàn cộng đồng',
                'description' => 'MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm',
            ],
            'time' => [
                'just_now' => 'Vừa xong',
                'minutes_ago' => 'phút trước',
                'hours_ago' => 'giờ trước',
                'days_ago' => 'ngày trước',
                'updated' => 'Cập nhật',
                'created' => 'Tạo',
            ],
        ];

        $commonKeysEn = [
            'site' => [
                'name' => 'MechaMap',
                'tagline' => 'Community Forum',
                'description' => 'MechaMap - Community forum for sharing knowledge and experience',
            ],
            'time' => [
                'just_now' => 'Just now',
                'minutes_ago' => 'minutes ago',
                'hours_ago' => 'hours ago',
                'days_ago' => 'days ago',
                'updated' => 'Updated',
                'created' => 'Created',
            ],
        ];

        return ['vi' => $commonKeysVi, 'en' => $commonKeysEn];
    }

    private function createLanguageFile($relativePath, $data)
    {
        $fullPath = $this->basePath . '/' . $relativePath;
        $this->log("📝 Creating: {$relativePath}");

        if (!$this->dryRun) {
            $content = "<?php\n\nreturn " . $this->arrayToPhp($data, 0) . ";\n";
            file_put_contents($fullPath, $content);
        }

        $this->migrationLog[] = [
            'action' => 'create',
            'file' => $relativePath,
            'keys_count' => $this->countKeys($data),
        ];
    }

    private function eliminateDuplicates()
    {
        $this->log("🔄 Eliminating duplicates...");

        $duplicateFiles = [
            'vi/buttons.php',
            'vi/forms.php',
            // Add other files with duplicates
        ];

        foreach ($duplicateFiles as $file) {
            $this->removeDuplicateKeys($file);
        }
    }

    private function removeDuplicateKeys($relativePath)
    {
        $fullPath = $this->basePath . '/' . $relativePath;

        if (!file_exists($fullPath)) {
            return;
        }

        $this->log("🧹 Cleaning duplicates from: {$relativePath}");

        if (!$this->dryRun) {
            $data = include $fullPath;

            // Remove specific duplicate keys
            $keysToRemove = ['home', 'login', 'search', 'marketplace'];

            foreach ($keysToRemove as $key) {
                if (isset($data[$key])) {
                    unset($data[$key]);
                    $this->log("  - Removed duplicate key: {$key}");
                }
            }

            $content = "<?php\n\nreturn " . $this->arrayToPhp($data, 0) . ";\n";
            file_put_contents($fullPath, $content);
        }
    }

    private function validateMigration()
    {
        $this->log("✅ Validating migration...");

        // Check if new files exist and are valid PHP
        $newFiles = ['nav.php', 'ui.php', 'auth.php', 'common.php'];

        foreach (['vi', 'en'] as $locale) {
            foreach ($newFiles as $file) {
                $path = $this->basePath . "/{$locale}/{$file}";

                if (!$this->dryRun && !file_exists($path)) {
                    throw new Exception("Migration failed: {$path} not created");
                }

                if (!$this->dryRun) {
                    $data = include $path;
                    if (!is_array($data)) {
                        throw new Exception("Migration failed: {$path} invalid format");
                    }
                }
            }
        }

        $this->log("✅ Migration validation passed");
    }

    private function generateReport()
    {
        $this->log("\n📊 MIGRATION REPORT");
        $this->log("==================");

        foreach ($this->migrationLog as $entry) {
            $this->log("✅ {$entry['action']}: {$entry['file']} ({$entry['keys_count']} keys)");
        }

        $this->log("\n🎯 Next Steps:");
        $this->log("1. Update view references from __('messages.*') to new structure");
        $this->log("2. Test language switching functionality");
        $this->log("3. Remove old oversized messages.php files");
    }

    private function arrayToPhp($array, $indent = 0)
    {
        $spaces = str_repeat('    ', $indent);
        $result = "[\n";

        foreach ($array as $key => $value) {
            $result .= $spaces . "    '{$key}' => ";

            if (is_array($value)) {
                $result .= $this->arrayToPhp($value, $indent + 1);
            } else {
                $result .= "'" . addslashes($value) . "'";
            }

            $result .= ",\n";
        }

        $result .= $spaces . "]";
        return $result;
    }

    private function countKeys($array)
    {
        $count = 0;
        foreach ($array as $value) {
            if (is_array($value)) {
                $count += $this->countKeys($value);
            } else {
                $count++;
            }
        }
        return $count;
    }

    private function executeCommand($command)
    {
        if ($this->verbose) {
            $this->log("🔧 Executing: {$command}");
        }

        if (!$this->dryRun) {
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new Exception("Command failed: {$command}");
            }
        }
    }

    private function log($message)
    {
        echo "[" . date('H:i:s') . "] {$message}\n";
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $options = [];

    foreach ($argv as $arg) {
        if ($arg === '--dry-run') {
            $options['dry-run'] = true;
        }
        if ($arg === '--verbose') {
            $options['verbose'] = true;
        }
    }

    try {
        $migration = new LanguageMigration();
        $migration->run($options);
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
