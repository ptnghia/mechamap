<?php
/**
 * View References Update Script
 *
 * Purpose: Update 527+ view references from __('messages.*') to new structure
 * Author: MechaMap Development Team
 * Date: 2025-07-12
 *
 * Usage: php update-view-references.php [--dry-run] [--batch=1|2] [--verbose]
 */

class ViewReferencesUpdater
{
    private $viewsPath;
    private $dryRun = false;
    private $verbose = false;
    private $batch = 'all';
    private $updateLog = [];

    // Mapping from old messages.* keys to new structure
    private $keyMappings = [
        // Navigation mappings
        "__('messages.navigation.home')" => "__('nav.main.home')",
        "__('messages.navigation.marketplace')" => "__('nav.main.marketplace')",
        "__('messages.navigation.forums')" => "__('nav.main.forums')",
        "__('messages.navigation.community')" => "__('nav.main.community')",
        "__('messages.navigation.login')" => "__('nav.auth.login')",
        "__('messages.navigation.register')" => "__('nav.auth.register')",
        "__('messages.navigation.logout')" => "__('nav.auth.logout')",
        "__('messages.nav.home')" => "__('nav.main.home')",
        "__('messages.nav.marketplace')" => "__('nav.main.marketplace')",

        // UI/Common mappings
        "__('messages.common.views')" => "__('ui.common.views')",
        "__('messages.common.comments')" => "__('ui.common.comments')",
        "__('messages.common.updated')" => "__('ui.common.updated')",
        "__('messages.common.title')" => "__('ui.common.title')",
        "__('messages.common.description')" => "__('ui.common.description')",
        "__('messages.common.search')" => "__('ui.actions.search')",
        "__('messages.common.save')" => "__('ui.actions.save')",
        "__('messages.common.cancel')" => "__('ui.actions.cancel')",
        "__('messages.common.delete')" => "__('ui.actions.delete')",
        "__('messages.common.edit')" => "__('ui.actions.edit')",
        "__('messages.common.view')" => "__('ui.actions.view')",
        "__('messages.common.load_more')" => "__('ui.pagination.load_more')",
        "__('messages.common.search_placeholder')" => "__('forum.search.placeholder')",

        // Auth mappings
        "__('messages.auth.login')" => "__('auth.login.title')",
        "__('messages.auth.register')" => "__('auth.register.title')",
        "__('messages.auth.email')" => "__('auth.login.email')",
        "__('messages.auth.password')" => "__('auth.login.password')",
        "__('messages.auth.remember')" => "__('auth.login.remember')",
        "__('messages.auth.forgot_password')" => "__('auth.login.forgot_password')",
        "__('messages.login')" => "__('nav.auth.login')",
        "__('messages.register')" => "__('nav.auth.register')",
        "__('messages.logout')" => "__('nav.auth.logout')",
        "__('messages.welcome_back')" => "__('auth.login.welcome_back')",
        "__('messages.email_or_username')" => "__('auth.login.email_or_username')",
        "__('messages.remember_login')" => "__('auth.login.remember')",
        "__('messages.remember_me')" => "__('auth.login.remember')",
        "__('messages.or_login_with')" => "__('auth.login.or_login_with')",
        "__('messages.login_with_google')" => "__('auth.login.login_with_google')",
        "__('messages.login_with_facebook')" => "__('auth.login.login_with_facebook')",
        "__('messages.dont_have_account')" => "__('auth.login.dont_have_account')",
        "__('messages.create_business_account')" => "__('auth.register.create_business_account')",
        "__('messages.forgot_password_description')" => "__('auth.password.forgot_description')",
        "__('messages.send_reset_link')" => "__('auth.password.send_reset_link')",
        "__('messages.back_to_login')" => "__('auth.login.back_to_login')",

        // Forum mappings
        "__('messages.forums.forums')" => "__('forum.forums.title')",
        "__('messages.forums.categories')" => "__('forum.forums.categories')",
        "__('messages.forums.high_activity')" => "__('forum.forums.high_activity')",
        "__('messages.forums.medium_activity')" => "__('forum.forums.medium_activity')",
        "__('messages.forums.low_activity')" => "__('forum.forums.low_activity')",
        "__('messages.forums.forums_in_category')" => "__('forum.forums.forums_in_category')",
        "__('messages.threads.create')" => "__('forum.threads.create')",
        "__('messages.threads.reply')" => "__('forum.threads.reply')",
        "__('messages.threads.views')" => "__('forum.threads.views')",
        "__('messages.threads.replies')" => "__('forum.threads.replies')",
        "__('messages.threads.create_new_post')" => "__('forum.threads.create')",
        "__('messages.threads.posts')" => "__('forum.threads.title')",
        "__('messages.threads.forums_title')" => "__('forum.forums.title')",
        "__('messages.threads.new_thread')" => "__('forum.threads.create')",
        "__('messages.threads.category')" => "__('ui.common.category')",
        "__('messages.threads.all_categories')" => "__('marketplace.categories.all')",
        "__('messages.threads.forum')" => "__('forum.forums.title')",
        "__('messages.threads.all_forums')" => "__('forum.forums.all')",
        "__('messages.threads.sort_by')" => "__('ui.actions.sort')",
        "__('messages.threads.latest')" => "__('ui.common.latest')",
        "__('messages.threads.oldest')" => "__('ui.common.oldest')",
        "__('messages.threads.most_viewed')" => "__('ui.common.most_viewed')",
        "__('messages.threads.most_commented')" => "__('ui.common.most_commented')",
        "__('messages.threads.search')" => "__('ui.actions.search')",
        "__('messages.threads.search_placeholder')" => "__('forum.search.placeholder')",
        "__('messages.threads.apply_filters')" => "__('ui.actions.apply_filters')",
        "__('messages.threads.threads_title')" => "__('forum.threads.title')",
        "__('messages.threads.threads_count')" => "__('forum.threads.count')",
        "__('messages.threads.no_threads_found')" => "__('forum.threads.no_threads_found')",
        "__('messages.threads.clear_filters')" => "__('ui.actions.clear_filters')",

        // Marketplace mappings
        "__('messages.marketplace.products')" => "__('marketplace.products.title')",
        "__('messages.marketplace.categories')" => "__('marketplace.categories.title')",
        "__('messages.marketplace.cart')" => "__('marketplace.cart.title')",
        "__('messages.marketplace.checkout')" => "__('marketplace.cart.checkout')",
        "__('messages.marketplace.title')" => "__('marketplace.products.title')",
        "__('messages.marketplace.product_categories')" => "__('marketplace.categories.title')",
        "__('messages.marketplace.popular_products')" => "__('marketplace.products.popular')",
        "__('messages.marketplace.subcategories')" => "__('marketplace.categories.subcategories')",
        "__('messages.marketplace.verified')" => "__('marketplace.products.verified')",
        "__('messages.marketplace.service')" => "__('marketplace.products.service')",
        "__('messages.marketplace.manufacturer')" => "__('marketplace.products.manufacturer')",
        "__('messages.cart.empty')" => "__('marketplace.cart.empty')",
        "__('messages.cart.subtotal')" => "__('marketplace.cart.subtotal')",
        "__('messages.cart.remove')" => "__('marketplace.cart.remove')",

        // Site/Common mappings
        "__('messages.site.community_title')" => "__('common.site.tagline')",
        "__('messages.site.description')" => "__('common.site.description')",
        "__('messages.site.name')" => "__('common.site.name')",

        // Search mappings
        "__('messages.search.placeholder')" => "__('forum.search.placeholder')",
        "__('messages.search.advanced')" => "__('forum.search.advanced')",
        "__('messages.search.results')" => "__('forum.search.results')",
        "__('messages.search.title')" => "__('forum.search.title')",

        // Pagination mappings
        "__('messages.page')" => "__('ui.pagination.page')",
        "__('messages.of')" => "__('ui.pagination.of')",
        "__('messages.previous')" => "__('ui.pagination.previous')",
        "__('messages.next')" => "__('ui.pagination.next')",
        "__('messages.go')" => "__('ui.pagination.go_to_page')",

        // Common actions and states
        "__('messages.by')" => "__('ui.common.by')",
        "__('messages.replies')" => "__('ui.common.replies')",
        "__('messages.view_thread')" => "__('ui.actions.view_thread')",
        "__('messages.download')" => "__('ui.actions.download')",
        "__('messages.view_details')" => "__('ui.actions.view_details')",
        "__('messages.view_full_showcase')" => "__('ui.actions.view_full_showcase')",

        // What's new section
        "__('messages.whats_new')" => "__('nav.main.whats_new')",
        "__('messages.create_new_thread')" => "__('forum.threads.create')",
        "__('messages.new_posts')" => "__('forum.posts.new')",
        "__('messages.popular')" => "__('ui.common.popular')",
        "__('messages.new_threads')" => "__('forum.threads.new')",
        "__('messages.new_showcases')" => "__('showcase.new')",
        "__('messages.new_media')" => "__('media.new')",
        "__('messages.looking_for_replies')" => "__('forum.threads.looking_for_replies')",

        // Thread status
        "__('messages.thread_status.sticky')" => "__('forum.threads.sticky')",
        "__('messages.thread_status.locked')" => "__('forum.threads.locked')",

        // Showcase related
        "__('messages.related_showcase')" => "__('showcase.related')",
        "__('messages.showcase_for_thread')" => "__('showcase.for_thread')",

        // Members section
        "__('messages.started_by_me')" => "__('forum.threads.started_by_me')",

        // Language switching
        "__('messages.language.switch_language')" => "__('common.language.switch')",
        "__('messages.language.select_language')" => "__('common.language.select')",
        "__('messages.language.auto_detect')" => "__('common.language.auto_detect')",
    ];

    // High-priority files for Batch 1 (core layout)
    private $batch1Files = [
        'layouts/app.blade.php',
        'components/header.blade.php',
        'components/footer.blade.php',
        'components/sidebar.blade.php',
        'components/auth-modal.blade.php',
        'categories/show.blade.php',
        'marketplace/index.blade.php',
        'forums/index.blade.php',
    ];

    public function __construct($viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?: realpath(__DIR__ . '/../../resources/views');

        if (!$this->viewsPath) {
            throw new Exception("Views path not found");
        }
    }

    public function run($options = [])
    {
        $this->dryRun = $options['dry-run'] ?? false;
        $this->verbose = $options['verbose'] ?? false;
        $this->batch = $options['batch'] ?? 'all';

        $this->log("🔄 Starting View References Update");
        $this->log("Views Path: {$this->viewsPath}");
        $this->log("Batch: {$this->batch}");
        $this->log("Dry Run: " . ($this->dryRun ? 'YES' : 'NO'));

        try {
            // Get files to process based on batch
            $filesToProcess = $this->getFilesToProcess();

            $this->log("📁 Files to process: " . count($filesToProcess));

            // Process each file
            foreach ($filesToProcess as $file) {
                $this->processFile($file);
            }

            $this->generateReport();
            $this->log("✅ View references update completed!");

        } catch (Exception $e) {
            $this->log("❌ Update failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function getFilesToProcess()
    {
        if ($this->batch === '1') {
            // Batch 1: High-priority core files
            $files = [];
            foreach ($this->batch1Files as $file) {
                $fullPath = $this->viewsPath . '/' . $file;
                if (file_exists($fullPath)) {
                    $files[] = $fullPath;
                }
            }
            return $files;
        } elseif ($this->batch === '2') {
            // Batch 2: All other files
            $allFiles = $this->getAllViewFiles();
            $batch1FullPaths = array_map(function($file) {
                return $this->viewsPath . '/' . $file;
            }, $this->batch1Files);

            return array_diff($allFiles, $batch1FullPaths);
        } else {
            // All files
            return $this->getAllViewFiles();
        }
    }

    private function getAllViewFiles()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->viewsPath)
        );

        $files = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                // Exclude admin views
                $relativePath = str_replace($this->viewsPath . '/', '', $file->getPathname());
                if (!str_starts_with($relativePath, 'admin/')) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }

    private function processFile($filePath)
    {
        $relativePath = str_replace($this->viewsPath . '/', '', $filePath);

        if (!file_exists($filePath)) {
            $this->log("⚠️ File not found: {$relativePath}");
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;
        $updatesCount = 0;

        // Apply all key mappings
        foreach ($this->keyMappings as $oldKey => $newKey) {
            $beforeCount = substr_count($content, $oldKey);
            $content = str_replace($oldKey, $newKey, $content);
            $afterCount = substr_count($content, $oldKey);

            $replacements = $beforeCount - $afterCount;
            if ($replacements > 0) {
                $updatesCount += $replacements;
                if ($this->verbose) {
                    $this->log("  ✓ {$oldKey} → {$newKey} ({$replacements}x)");
                }
            }
        }

        // Handle dynamic key patterns
        $updatesCount += $this->processDynamicPatterns($content);

        if ($updatesCount > 0) {
            $this->log("📝 {$relativePath}: {$updatesCount} updates");

            if (!$this->dryRun) {
                file_put_contents($filePath, $content);
            }

            $this->updateLog[] = [
                'file' => $relativePath,
                'updates' => $updatesCount,
                'size_before' => strlen($originalContent),
                'size_after' => strlen($content),
            ];
        } else {
            if ($this->verbose) {
                $this->log("📄 {$relativePath}: No updates needed");
            }
        }
    }

    private function processDynamicPatterns(&$content)
    {
        $updatesCount = 0;

        // Pattern 1: __('messages.forums.forums_in_category', [...])
        $pattern1 = "/__\('messages\.forums\.forums_in_category'/";
        $replacement1 = "__('forum.forums.forums_in_category'";
        $beforeCount = preg_match_all($pattern1, $content);
        $content = preg_replace($pattern1, $replacement1, $content);
        $afterCount = preg_match_all($pattern1, $content);
        $updatesCount += ($beforeCount - $afterCount);

        // Pattern 2: @lang('messages.*')
        $pattern2 = "/@lang\('messages\.([^']+)'\)/";
        $content = preg_replace_callback($pattern2, function($matches) {
            $oldKey = $matches[1];
            $newKey = $this->mapOldKeyToNew($oldKey);
            return "@lang('{$newKey}')";
        }, $content);

        // Pattern 3: {{ __('messages.*') }}
        $pattern3 = "/\{\{\s*__\('messages\.([^']+)'\)\s*\}\}/";
        $content = preg_replace_callback($pattern3, function($matches) {
            $oldKey = $matches[1];
            $newKey = $this->mapOldKeyToNew($oldKey);
            return "{{ __('{$newKey}') }}";
        }, $content);

        return $updatesCount;
    }

    private function mapOldKeyToNew($oldKey)
    {
        // Map old key structure to new structure
        $keyParts = explode('.', $oldKey);

        if (count($keyParts) >= 2) {
            $section = $keyParts[0];
            $subsection = $keyParts[1];

            switch ($section) {
                case 'navigation':
                    return "nav.main.{$subsection}";
                case 'common':
                    return "ui.common.{$subsection}";
                case 'auth':
                    return "auth.login.{$subsection}";
                case 'forums':
                case 'threads':
                    return "forum.{$section}.{$subsection}";
                case 'marketplace':
                case 'cart':
                    return "marketplace.{$section}.{$subsection}";
                case 'site':
                    return "common.site.{$subsection}";
                case 'search':
                    return "forum.search.{$subsection}";
                default:
                    return "ui.common.{$subsection}";
            }
        }

        return "ui.common.{$oldKey}";
    }

    private function generateReport()
    {
        $this->log("\n📊 UPDATE REPORT");
        $this->log("================");

        $totalFiles = count($this->updateLog);
        $totalUpdates = array_sum(array_column($this->updateLog, 'updates'));

        $this->log("📁 Files processed: {$totalFiles}");
        $this->log("🔄 Total updates: {$totalUpdates}");

        if ($this->verbose && !empty($this->updateLog)) {
            $this->log("\n📋 Detailed breakdown:");
            foreach ($this->updateLog as $entry) {
                $this->log("  ✅ {$entry['file']}: {$entry['updates']} updates");
            }
        }

        $this->log("\n🎯 Next Steps:");
        if ($this->batch === '1') {
            $this->log("1. Run batch 2: php update-view-references.php --batch=2");
            $this->log("2. Test core layout functionality");
        } elseif ($this->batch === '2') {
            $this->log("1. Test all view functionality");
            $this->log("2. Run language switching tests");
        } else {
            $this->log("1. Test language switching functionality");
            $this->log("2. Verify no missing translation keys");
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
        if (str_starts_with($arg, '--batch=')) {
            $options['batch'] = substr($arg, 8);
        }
    }

    try {
        $updater = new ViewReferencesUpdater();
        $updater->run($options);
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
