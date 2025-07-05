<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearAssetCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:clear-cache {--force : Force clear without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear browser cache for CSS/JS assets by touching files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will touch all CSS/JS files to force browser cache refresh. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Clearing asset cache...');

        // Danh sách các file CSS/JS cần touch
        $assetPaths = [
            'public/css',
            'public/js',
        ];

        $touchedFiles = 0;

        foreach ($assetPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                
                foreach ($files as $file) {
                    if (in_array($file->getExtension(), ['css', 'js'])) {
                        touch($file->getPathname());
                        $touchedFiles++;
                        $this->line("Touched: {$file->getRelativePathname()}");
                    }
                }
            }
        }

        // Clear Laravel caches
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->info("✅ Asset cache cleared successfully!");
        $this->info("📁 Touched {$touchedFiles} asset files");
        $this->info("🔄 Laravel caches cleared");
        $this->warn("💡 Tip: Browsers will now reload fresh CSS/JS files");

        return 0;
    }
}
