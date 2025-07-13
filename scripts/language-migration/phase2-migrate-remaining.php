<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Filesystem\Filesystem;

class Phase2Migration
{
    private $filesystem;
    private $basePath;
    private $processed = 0;
    private $skipped = 0;
    private $errors = 0;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->basePath = realpath(__DIR__ . '/../../');
    }

    public function run()
    {
        echo "🚀 PHASE 2: Migrating Remaining Old References\n";
        echo "==============================================\n\n";

        $this->findAndUpdateOldReferences();
        $this->printSummary();
    }

    private function findAndUpdateOldReferences()
    {
        echo "🔍 Scanning for old language references...\n\n";

        $viewPaths = [
            'resources/views',
            'resources/views/components',
            'resources/views/admin',
            'resources/views/auth',
            'resources/views/threads',
            'resources/views/forums',
            'resources/views/showcase',
            'resources/views/marketplace',
            'resources/views/partials',
        ];

        foreach ($viewPaths as $path) {
            $fullPath = $this->basePath . '/' . $path;
            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath);
            }
        }
    }

    private function scanDirectory($directory)
    {
        $files = $this->filesystem->allFiles($directory);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $this->processFile($file->getPathname());
            }
        }
    }

    private function processFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $relativePath = str_replace($this->basePath . '/', '', $filePath);

        // Old patterns to update
        $patterns = [
            // Old direct translations
            "/'Trang chủ'/" => "__('nav.main.home')",
            "/'Diễn đàn'/" => "__('forum.forums.title')",
            "/'Thảo luận'/" => "__('ui.common.discussion')",
            "/'Cộng đồng'/" => "__('ui.common.community')",
            "/'Dự án'/" => "__('ui.common.showcase')",
            "/'Thị trường'/" => "__('ui.common.marketplace')",
            "/'Tìm kiếm'/" => "__('ui.actions.search')",
            "/'Đăng nhập'/" => "__('nav.auth.login')",
            "/'Đăng ký'/" => "__('nav.auth.register')",
            "/'Đăng xuất'/" => "__('auth.logout')",
            "/'Xem tất cả'/" => "__('buttons.view_all')",
            "/'Xem chi tiết'/" => "__('buttons.view_details')",
            "/'Tải thêm'/" => "__('ui.pagination.load_more')",
            "/'Lượt xem'/" => "__('ui.common.views')",
            "/'Trả lời'/" => "__('ui.common.replies')",
            "/'Ghim'/" => "__('forum.threads.pinned')",
            "/'Khóa'/" => "__('forum.threads.locked')",

            // Old @lang patterns
            "/@lang\('messages\./" => "@lang('ui.common.",
            "/@lang\('forms\./" => "@lang('forms.",
            "/@lang\('buttons\./" => "@lang('buttons.",
            "/@lang\('auth\./" => "@lang('auth.",

            // Old __() patterns with outdated keys
            "/__\('messages\./" => "__('ui.common.",
            "/__\('forms\./" => "__('forms.",
            "/__\('common\./" => "__('ui.common.",
        ];

        $hasChanges = false;

        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
                $hasChanges = true;
            }
        }

        // Update specific old keys
        $keyMappings = [
            '__("messages.home")' => '__("nav.main.home")',
            '__("messages.forums")' => '__("forum.forums.title")',
            '__("messages.community")' => '__("ui.common.community")',
            '__("messages.marketplace")' => '__("ui.common.marketplace")',
            '__("messages.search")' => '__("ui.actions.search")',
            '__("messages.login")' => '__("nav.auth.login")',
            '__("messages.register")' => '__("nav.auth.register")',
            '__("messages.logout")' => '__("auth.logout")',
            '__("common.view_all")' => '__("buttons.view_all")',
            '__("common.view_details")' => '__("buttons.view_details")',
            '__("common.load_more")' => '__("ui.pagination.load_more")',
            '__("common.views")' => '__("ui.common.views")',
            '__("common.replies")' => '__("ui.common.replies")',
            '__("forum.pinned")' => '__("forum.threads.pinned")',
            '__("forum.locked")' => '__("forum.threads.locked")',
        ];

        foreach ($keyMappings as $oldKey => $newKey) {
            if (strpos($content, $oldKey) !== false) {
                $content = str_replace($oldKey, $newKey, $content);
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            file_put_contents($filePath, $content);
            echo "  ✅ Updated: $relativePath\n";
            $this->processed++;
        } else {
            $this->skipped++;
        }
    }

    private function printSummary()
    {
        echo "\n📊 PHASE 2 MIGRATION SUMMARY\n";
        echo "============================\n\n";
        echo "✅ Files processed: {$this->processed}\n";
        echo "⏭️  Files skipped: {$this->skipped}\n";
        echo "❌ Errors: {$this->errors}\n\n";

        if ($this->processed > 0) {
            echo "🎉 Phase 2 migration completed successfully!\n";
            echo "💡 Next steps:\n";
            echo "   1. Clear Laravel cache\n";
            echo "   2. Test website functionality\n";
            echo "   3. Run validation scripts\n";
        } else {
            echo "ℹ️  No old references found to update.\n";
        }
    }
}

$migration = new Phase2Migration();
$migration->run();
