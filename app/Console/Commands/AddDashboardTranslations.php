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
        // ===== PROFILE ENHANCEMENT TRANSLATIONS =====

        // Profile Header
        'profile.follow' => [
            'vi' => 'Theo dõi',
            'en' => 'Follow'
        ],
        'profile.contact' => [
            'vi' => 'Liên hệ',
            'en' => 'Contact'
        ],
        'profile.report' => [
            'vi' => 'Báo cáo',
            'en' => 'Report'
        ],
        'profile.active' => [
            'vi' => 'Hoạt động',
            'en' => 'Active'
        ],
        'profile.verified' => [
            'vi' => 'Đã xác thực',
            'en' => 'Verified'
        ],
        'profile.verified_business' => [
            'vi' => 'Doanh nghiệp đã xác thực',
            'en' => 'Verified Business'
        ],
        'profile.phone' => [
            'vi' => 'Điện thoại',
            'en' => 'Phone'
        ],
        'profile.business_phone' => [
            'vi' => 'Điện thoại doanh nghiệp',
            'en' => 'Business Phone'
        ],
        'profile.business_email' => [
            'vi' => 'Email doanh nghiệp',
            'en' => 'Business Email'
        ],
        'profile.profile_views' => [
            'vi' => 'Lượt xem',
            'en' => 'Profile Views'
        ],
        'profile.products' => [
            'vi' => 'Sản phẩm',
            'en' => 'Products'
        ],
        'profile.reviews' => [
            'vi' => 'Đánh giá',
            'en' => 'Reviews'
        ],
        'profile.rating' => [
            'vi' => 'Xếp hạng',
            'en' => 'Rating'
        ],
        'profile.business_score' => [
            'vi' => 'Điểm doanh nghiệp',
            'en' => 'Business Score'
        ],
        'profile.more' => [
            'vi' => 'thêm',
            'en' => 'more'
        ],

        // Experience levels
        'profile.experience_0_1' => [
            'vi' => 'Mới bắt đầu (0-1 năm)',
            'en' => 'Beginner (0-1 years)'
        ],
        'profile.experience_1_3' => [
            'vi' => 'Cơ bản (1-3 năm)',
            'en' => 'Junior (1-3 years)'
        ],
        'profile.experience_3_5' => [
            'vi' => 'Trung cấp (3-5 năm)',
            'en' => 'Mid-level (3-5 years)'
        ],
        'profile.experience_5_10' => [
            'vi' => 'Cao cấp (5-10 năm)',
            'en' => 'Senior (5-10 years)'
        ],
        'profile.experience_10+' => [
            'vi' => 'Chuyên gia (10+ năm)',
            'en' => 'Expert (10+ years)'
        ],

        // Tab Navigation
        'profile.overview' => [
            'vi' => 'Tổng quan',
            'en' => 'Overview'
        ],
        'profile.business_info' => [
            'vi' => 'Thông tin doanh nghiệp',
            'en' => 'Business Info'
        ],
        'profile.professional_info' => [
            'vi' => 'Thông tin chuyên môn',
            'en' => 'Professional Info'
        ],
        'profile.my_threads' => [
            'vi' => 'Bài viết của tôi',
            'en' => 'My Threads'
        ],
        'profile.portfolio' => [
            'vi' => 'Portfolio',
            'en' => 'Portfolio'
        ],

        // Professional Info Section
        'profile.business_information' => [
            'vi' => 'Thông tin doanh nghiệp',
            'en' => 'Business Information'
        ],
        'profile.professional_information' => [
            'vi' => 'Thông tin chuyên môn',
            'en' => 'Professional Information'
        ],
        'profile.company_name' => [
            'vi' => 'Tên công ty',
            'en' => 'Company Name'
        ],
        'profile.business_type' => [
            'vi' => 'Loại hình doanh nghiệp',
            'en' => 'Business Type'
        ],
        'profile.business_description' => [
            'vi' => 'Mô tả doanh nghiệp',
            'en' => 'Business Description'
        ],
        'profile.business_categories' => [
            'vi' => 'Danh mục kinh doanh',
            'en' => 'Business Categories'
        ],
        'profile.verification_status' => [
            'vi' => 'Trạng thái xác thực',
            'en' => 'Verification Status'
        ],
        'profile.verified_on' => [
            'vi' => 'Đã xác thực vào',
            'en' => 'Verified on'
        ],
        'profile.pending_verification' => [
            'vi' => 'Chờ xác thực',
            'en' => 'Pending Verification'
        ],
        'profile.no_categories_specified' => [
            'vi' => 'Chưa chỉ định danh mục',
            'en' => 'No categories specified'
        ],
        'profile.job_title' => [
            'vi' => 'Chức vụ',
            'en' => 'Job Title'
        ],
        'profile.company' => [
            'vi' => 'Công ty',
            'en' => 'Company'
        ],
        'profile.experience_years' => [
            'vi' => 'Kinh nghiệm',
            'en' => 'Experience'
        ],
        'profile.bio' => [
            'vi' => 'Tiểu sử',
            'en' => 'Bio'
        ],
        'profile.skills' => [
            'vi' => 'Kỹ năng',
            'en' => 'Skills'
        ],
        'profile.no_skills_listed' => [
            'vi' => 'Chưa liệt kê kỹ năng',
            'en' => 'No skills listed'
        ],

        // My Threads Section
        'profile.create_new_thread' => [
            'vi' => 'Tạo bài viết mới',
            'en' => 'Create New Thread'
        ],
        'profile.pinned' => [
            'vi' => 'Đã ghim',
            'en' => 'Pinned'
        ],
        'profile.locked' => [
            'vi' => 'Đã khóa',
            'en' => 'Locked'
        ],
        'profile.updated' => [
            'vi' => 'Cập nhật',
            'en' => 'Updated'
        ],
        'profile.views' => [
            'vi' => 'Lượt xem',
            'en' => 'Views'
        ],
        'profile.attachments' => [
            'vi' => 'Tệp đính kèm',
            'en' => 'Attachments'
        ],
        'profile.no_threads_yet' => [
            'vi' => 'Chưa có bài viết nào',
            'en' => 'No threads yet'
        ],
        'profile.create_first_thread_message' => [
            'vi' => 'Hãy tạo bài viết đầu tiên để chia sẻ kiến thức với cộng đồng',
            'en' => 'Create your first thread to share knowledge with the community'
        ],
        'profile.create_first_thread' => [
            'vi' => 'Tạo bài viết đầu tiên',
            'en' => 'Create First Thread'
        ],
        'profile.hasnt_created_threads_yet' => [
            'vi' => 'chưa tạo bài viết nào',
            'en' => 'hasn\'t created any threads yet'
        ],

        // Portfolio Section
        'profile.portfolio_projects' => [
            'vi' => 'Portfolio & Dự án',
            'en' => 'Portfolio & Projects'
        ],
        'profile.add_project' => [
            'vi' => 'Thêm dự án',
            'en' => 'Add Project'
        ],
        'profile.files' => [
            'vi' => 'tệp',
            'en' => 'files'
        ],
        'profile.view_all_projects' => [
            'vi' => 'Xem tất cả dự án',
            'en' => 'View All Projects'
        ],
        'profile.no_portfolio_items' => [
            'vi' => 'Chưa có dự án nào',
            'en' => 'No portfolio items'
        ],
        'profile.showcase_your_work_message' => [
            'vi' => 'Hãy showcase công việc của bạn để thu hút sự chú ý',
            'en' => 'Showcase your work to attract attention'
        ],
        'profile.create_showcase' => [
            'vi' => 'Tạo Showcase',
            'en' => 'Create Showcase'
        ],
        'profile.create_thread' => [
            'vi' => 'Tạo bài viết',
            'en' => 'Create Thread'
        ],
        'profile.hasnt_shared_projects_yet' => [
            'vi' => 'chưa chia sẻ dự án nào',
            'en' => 'hasn\'t shared any projects yet'
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
