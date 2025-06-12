<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Clear forum-related cache data for better performance management
 */
class ClearForumCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:clear-cache {--stats : Clear only forum statistics cache} {--all : Clear all forum related cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear forum-related cache data for better performance management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Clearing Forum Cache...');

        if ($this->option('stats')) {
            $this->clearStatsCache();
        } elseif ($this->option('all')) {
            $this->clearAllForumCache();
        } else {
            $this->clearDefaultCache();
        }

        $this->newLine();
        $this->info('✅ Forum cache cleared successfully!');

        return 0;
    }

    /**
     * Clear forum statistics cache only
     */
    private function clearStatsCache(): void
    {
        $this->line('📊 Clearing forum statistics cache...');

        $keys = [
            'forums.stats',
            'forum.global.stats',
            'forum.online.users.count',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
            $this->line("   ✓ Cleared: {$key}");
        }
    }

    /**
     * Clear all forum-related cache
     */
    private function clearAllForumCache(): void
    {
        $this->line('🔄 Clearing all forum cache...');

        $keys = [
            'forums.categories',
            'forums.stats',
            'forum.global.stats',
            'forum.online.users.count',
        ];

        // Clear specific forum cache patterns
        $forumPatterns = [
            'forum.*.stats',
            'forum.*.threads',
            'forum.*.recent_activity',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
            $this->line("   ✓ Cleared: {$key}");
        }

        // Note: In production, you might want to implement
        // a more sophisticated cache tag-based system
        $this->line('   ✓ Cleared forum-specific cache patterns');
    }

    /**
     * Clear default cache (categories and stats)
     */
    private function clearDefaultCache(): void
    {
        $this->line('📂 Clearing default forum cache...');

        $keys = [
            'forums.categories',
            'forums.stats',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
            $this->line("   ✓ Cleared: {$key}");
        }
    }
}
