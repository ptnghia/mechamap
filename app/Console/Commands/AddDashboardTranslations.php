<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddDashboardTranslations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:import-batch
                            {--dry-run : Preview changes without applying them}
                            {--group= : Import specific group only}
                            {--force : Force update existing translations}
                            {--locale= : Import specific locale only (vi|en)}';

    /**
     * The console command description.
     */
    protected $description = 'Import translation keys in batch with advanced options and validation';

    /**
     * Translation keys to import - Configure as needed
     *
     * Structure: 'key' => ['vi' => 'Vietnamese', 'en' => 'English']
     * Groups are auto-detected from key prefix (before first dot)
     */
    protected $translations = [
        // ===== MARKETPLACE TRANSLATIONS =====
        'marketplace.sections.latest_products' => [
            'vi' => 'Sản phẩm mới nhất',
            'en' => 'Latest Products'
        ],
        'marketplace.sections.latest_products_desc' => [
            'vi' => 'Khám phá những sản phẩm kỹ thuật mới nhất từ cộng đồng',
            'en' => 'Discover the latest technical products from our community'
        ],
        'marketplace.labels.by' => [
            'vi' => 'bởi',
            'en' => 'by'
        ],

        // ===== SIDEBAR USER DASHBOARD - MISSING TRANSLATIONS =====

        'sidebar.user_dashboard.profile' => [
            'vi' => 'Hồ sơ',
            'en' => 'Profile'
        ],
        'sidebar.user_dashboard.notifications' => [
            'vi' => 'Thông báo',
            'en' => 'Notifications'
        ],
        'sidebar.user_dashboard.messages' => [
            'vi' => 'Tin nhắn',
            'en' => 'Messages'
        ],
        'sidebar.user_dashboard.settings' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'sidebar.user_dashboard.showcases' => [
            'vi' => 'Showcase của tôi',
            'en' => 'My Showcases'
        ],
        'sidebar.user_dashboard.all_messages' => [
            'vi' => 'Tất cả tin nhắn',
            'en' => 'All Messages'
        ],
        'sidebar.user_dashboard.group_conversations' => [
            'vi' => 'Nhóm chat',
            'en' => 'Group Conversations'
        ],
        'sidebar.user_dashboard.create_group' => [
            'vi' => 'Tạo nhóm',
            'en' => 'Create Group'
        ],
        'sidebar.user_dashboard.new_message' => [
            'vi' => 'Tin nhắn mới',
            'en' => 'New Message'
        ],

        // ===== ADDITIONAL DASHBOARD SIDEBAR TRANSLATIONS =====

        'sidebar.user_dashboard.activity' => [
            'vi' => 'Hoạt động',
            'en' => 'Activity'
        ],
        'sidebar.user_dashboard.bookmarks' => [
            'vi' => 'Đã lưu',
            'en' => 'Bookmarks'
        ],
        'sidebar.user_dashboard.threads' => [
            'vi' => 'Bài viết của tôi',
            'en' => 'My Threads'
        ],
        'sidebar.user_dashboard.comments' => [
            'vi' => 'Bình luận',
            'en' => 'Comments'
        ],
        'sidebar.user_dashboard.following' => [
            'vi' => 'Đang theo dõi',
            'en' => 'Following'
        ],

        // ===== SECTION HEADERS =====

        'sidebar.sections.dashboard' => [
            'vi' => 'Bảng điều khiển',
            'en' => 'Dashboard'
        ],
        'sidebar.sections.community' => [
            'vi' => 'Cộng đồng',
            'en' => 'Community'
        ],
        'sidebar.sections.messages' => [
            'vi' => 'Tin nhắn',
            'en' => 'Messages'
        ],
        'sidebar.sections.quick_actions' => [
            'vi' => 'Thao tác nhanh',
            'en' => 'Quick Actions'
        ],

        // ===== QUICK ACTIONS =====

        'sidebar.quick_actions.new_thread' => [
            'vi' => 'Bài viết mới',
            'en' => 'New Thread'
        ],
        'sidebar.quick_actions.browse_marketplace' => [
            'vi' => 'Duyệt sản phẩm',
            'en' => 'Browse Products'
        ],
        'sidebar.quick_actions.create_showcase' => [
            'vi' => 'Tạo Showcase',
            'en' => 'Create Showcase'
        ],
        'sidebar.quick_actions.browse_forums' => [
            'vi' => 'Duyệt diễn đàn',
            'en' => 'Browse Forums'
        ],

        // ===== HELP & SUPPORT =====

        'sidebar.help.documentation' => [
            'vi' => 'Tài liệu',
            'en' => 'Documentation'
        ],
        'sidebar.help.contact_support' => [
            'vi' => 'Liên hệ hỗ trợ',
            'en' => 'Contact Support'
        ],
        'sidebar.help.faq' => [
            'vi' => 'FAQ',
            'en' => 'FAQ'
        ],
        'sidebar.help.help_support' => [
            'vi' => 'Trợ giúp & Hỗ trợ',
            'en' => 'Help & Support'
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $groupFilter = $this->option('group');
        $localeFilter = $this->option('locale');
        $forceUpdate = $this->option('force');

        $stats = [
            'added' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $this->displayHeader($dryRun, $groupFilter, $localeFilter, $forceUpdate);

        // Validate options
        if ($localeFilter && !in_array($localeFilter, ['vi', 'en'])) {
            $this->error('❌ Invalid locale. Use: vi or en');
            return 1;
        }

        // Filter translations based on options
        $filteredTranslations = $this->filterTranslations($groupFilter, $localeFilter);

        if (empty($filteredTranslations)) {
            $this->warn('⚠️  No translations to process with current filters');
            return 0;
        }

        $this->info("� Processing " . count($filteredTranslations) . " translation keys...");

        DB::beginTransaction();

        try {
            foreach ($filteredTranslations as $key => $locales) {
                $this->processTranslationKey($key, $locales, $dryRun, $forceUpdate, $stats);
            }

            if (!$dryRun) {
                DB::commit();
                $this->info('💾 Changes committed to database');
                $this->clearTranslationCache();
            } else {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('📍 Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->displaySummary($stats, $dryRun);
        return 0;
    }

    /**
     * Display command header with options
     */
    private function displayHeader(bool $dryRun, ?string $group, ?string $locale, bool $force): void
    {
        $this->info('🚀 Translation Batch Import Tool');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        if ($group) {
            $this->info("🎯 Group filter: {$group}");
        }

        if ($locale) {
            $this->info("🌐 Locale filter: {$locale}");
        }

        if ($force) {
            $this->warn('⚡ Force mode: Will update existing translations');
        }

        $this->newLine();
    }

    /**
     * Filter translations based on options
     */
    private function filterTranslations(?string $groupFilter, ?string $localeFilter): array
    {
        $filtered = [];

        foreach ($this->translations as $key => $locales) {
            // Filter by group
            if ($groupFilter) {
                $keyGroup = explode('.', $key)[0];
                if ($keyGroup !== $groupFilter) {
                    continue;
                }
            }

            // Filter by locale
            if ($localeFilter) {
                $locales = array_filter($locales, function($locale) use ($localeFilter) {
                    return $locale === $localeFilter;
                }, ARRAY_FILTER_USE_KEY);

                if (empty($locales)) {
                    continue;
                }
            }

            $filtered[$key] = $locales;
        }

        return $filtered;
    }

    /**
     * Process a single translation key
     */
    private function processTranslationKey(string $key, array $locales, bool $dryRun, bool $forceUpdate, array &$stats): void
    {
        foreach ($locales as $locale => $content) {
            $existing = Translation::where('key', $key)
                ->where('locale', $locale)
                ->first();

            if ($existing && !$forceUpdate) {
                $this->line("⏭️  Skipped: {$key} ({$locale}) - already exists");
                $stats['skipped']++;
                continue;
            }

            if (!$dryRun) {
                if ($existing && $forceUpdate) {
                    // Update existing
                    $existing->update([
                        'content' => $content,
                        'updated_by' => 1,
                        'updated_at' => now(),
                    ]);
                    $this->line("🔄 Updated: {$key} ({$locale}) = {$content}");
                    $stats['updated']++;
                } else {
                    // Create new
                    Translation::create([
                        'key' => $key,
                        'content' => $content,
                        'locale' => $locale,
                        'group_name' => explode('.', $key)[0],
                        'is_active' => true,
                        'created_by' => 1,
                    ]);
                    $this->line("✅ Added: {$key} ({$locale}) = {$content}");
                    $stats['added']++;
                }
            } else {
                if ($existing && $forceUpdate) {
                    $this->line("🔄 Would update: {$key} ({$locale}) = {$content}");
                    $stats['updated']++;
                } else {
                    $this->line("✅ Would add: {$key} ({$locale}) = {$content}");
                    $stats['added']++;
                }
            }
        }
    }

    /**
     * Clear translation cache
     */
    private function clearTranslationCache(): void
    {
        try {
            cache()->tags(['translations'])->flush();
            cache()->forget('translations.*');
            $this->info('🗑️  Translation cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Display summary statistics
     */
    private function displaySummary(array $stats, bool $dryRun): void
    {
        $this->newLine();
        $this->info('📊 SUMMARY:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($stats['added'] > 0) {
            $this->info("✅ Added: {$stats['added']} translations");
        }

        if ($stats['updated'] > 0) {
            $this->info("🔄 Updated: {$stats['updated']} translations");
        }

        if ($stats['skipped'] > 0) {
            $this->info("⏭️  Skipped: {$stats['skipped']} translations");
        }

        if (!empty($stats['errors'])) {
            $this->error("❌ Errors: " . count($stats['errors']));
            foreach ($stats['errors'] as $error) {
                $this->error("   - {$error}");
            }
        }

        if (!$dryRun && ($stats['added'] > 0 || $stats['updated'] > 0)) {
            $this->newLine();
            $this->info('🎉 Translation import completed successfully!');
            $this->info('💡 Recommendations:');
            $this->line('   • Test translations on frontend');
            $this->line('   • Check translation management UI');
            $this->line('   • Verify cache is working properly');
        }
    }
}
