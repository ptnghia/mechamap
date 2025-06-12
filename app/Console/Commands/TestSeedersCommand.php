<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\SeoSetting;
use App\Models\PageSeo;
use Illuminate\Console\Command;

class TestSeedersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:seeders {--detailed : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and validate all seeded data for MechaMap platform';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing MechaMap Seeders...');
        $this->newLine();

        // Test Settings
        $this->testGeneralSettings();
        $this->testSeoSettings();
        $this->testPageSeoSettings();

        // Summary
        $this->newLine();
        $this->info('✅ All seeders validation completed!');

        if ($this->option('detailed')) {
            $this->showDetailedReport();
        }
    }

    private function testGeneralSettings()
    {
        $this->info('📊 Testing General Settings...');

        $groups = ['general', 'company', 'contact', 'social', 'forum', 'user', 'email', 'api', 'security', 'forum_advanced', 'copyright'];
        $totalSettings = 0;

        foreach ($groups as $group) {
            $count = Setting::where('group', $group)->count();
            $totalSettings += $count;

            if ($count > 0) {
                $this->line("   ✓ {$group}: {$count} settings");
            } else {
                $this->error("   ✗ {$group}: No settings found!");
            }
        }

        $this->info("   📈 Total General Settings: {$totalSettings}");

        // Test specific important settings
        $criticalSettings = [
            'site_name' => 'general',
            'company_name' => 'company',
            'contact_email' => 'contact',
            'forum_threads_per_page' => 'forum'
        ];

        foreach ($criticalSettings as $key => $group) {
            $value = Setting::get($key);
            if ($value) {
                $this->line("   ✓ {$key}: {$value}");
            } else {
                $this->error("   ✗ {$key}: Not found!");
            }
        }
    }

    private function testSeoSettings()
    {
        $this->info('🔍 Testing SEO Settings...');

        $groups = ['general', 'social', 'advanced', 'robots'];
        $totalSettings = 0;

        foreach ($groups as $group) {
            $count = SeoSetting::where('group', $group)->count();
            $totalSettings += $count;

            if ($count > 0) {
                $this->line("   ✓ {$group}: {$count} settings");
            } else {
                $this->error("   ✗ {$group}: No settings found!");
            }
        }

        $this->info("   📈 Total SEO Settings: {$totalSettings}");

        // Test specific SEO settings
        $criticalSeoSettings = [
            'site_title' => 'general',
            'site_description' => 'general',
            'og_title' => 'social',
            'twitter_card' => 'social'
        ];

        foreach ($criticalSeoSettings as $key => $group) {
            $value = SeoSetting::getValue($key);
            if ($value) {
                $displayValue = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
                $this->line("   ✓ {$key}: {$displayValue}");
            } else {
                $this->error("   ✗ {$key}: Not found!");
            }
        }
    }

    private function testPageSeoSettings()
    {
        $this->info('📄 Testing Page SEO Settings...');

        $totalPages = PageSeo::count();
        $activePages = PageSeo::where('is_active', true)->count();
        $noIndexPages = PageSeo::where('no_index', true)->count();

        $this->line("   📈 Total Page SEO configs: {$totalPages}");
        $this->line("   ✅ Active configs: {$activePages}");
        $this->line("   🔒 No-index pages: {$noIndexPages}");

        // Test specific routes
        $criticalRoutes = [
            'home',
            'forums.index',
            'threads.show',
            'showcase.index'
        ];

        foreach ($criticalRoutes as $route) {
            $pageSeo = PageSeo::where('route_name', $route)->first();
            if ($pageSeo) {
                $this->line("   ✓ {$route}: {$pageSeo->title}");
            } else {
                $this->error("   ✗ {$route}: No SEO config found!");
            }
        }
    }

    private function showDetailedReport()
    {
        $this->newLine();
        $this->info('📋 Detailed Report:');
        $this->newLine();

        // Settings by group
        $this->info('🏢 General Settings by Group:');
        $groups = Setting::distinct('group')->pluck('group');
        foreach ($groups as $group) {
            $settings = Setting::getGroup($group);
            $this->line("   {$group}: " . count($settings) . " settings");

            if ($this->option('detailed')) {
                foreach ($settings as $key => $value) {
                    $displayValue = strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value;
                    $this->line("     - {$key}: {$displayValue}");
                }
            }
        }

        $this->newLine();

        // SEO Settings by group
        $this->info('🔍 SEO Settings by Group:');
        $seoGroups = SeoSetting::distinct('group')->pluck('group');
        foreach ($seoGroups as $group) {
            $settings = SeoSetting::getGroup($group);
            $this->line("   {$group}: " . count($settings) . " settings");

            if ($this->option('detailed')) {
                foreach ($settings as $key => $value) {
                    $displayValue = strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value;
                    $this->line("     - {$key}: {$displayValue}");
                }
            }
        }

        $this->newLine();

        // Page SEO routes
        $this->info('📄 Page SEO Routes:');
        $pages = PageSeo::select('route_name', 'title', 'is_active')->get();
        foreach ($pages as $page) {
            $status = $page->is_active ? '✅' : '❌';
            $this->line("   {$status} {$page->route_name}: {$page->title}");
        }
    }
}
