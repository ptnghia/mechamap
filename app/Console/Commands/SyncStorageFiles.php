<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncStorageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync {--force : Force overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ files từ storage/app/public sang public/storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourcePath = storage_path('app/public');
        $targetPath = public_path('storage');

        $this->info('Đang đồng bộ storage files...');

        // Tạo thư mục target nếu chưa có
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
            $this->info("Đã tạo thư mục: {$targetPath}");
        }

        // Đệ quy copy tất cả files
        $this->copyDirectory($sourcePath, $targetPath);

        $this->info('✅ Hoàn thành đồng bộ storage files!');

        return 0;
    }

    /**
     * Copy thư mục đệ quy
     */
    private function copyDirectory($source, $target)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $copiedFiles = 0;

        foreach ($iterator as $item) {
            $targetPath = $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                $shouldCopy = $this->option('force') || !file_exists($targetPath) ||
                    filemtime($item) > filemtime($targetPath);

                if ($shouldCopy) {
                    copy($item, $targetPath);
                    $copiedFiles++;

                    if ($copiedFiles % 50 === 0) {
                        $this->info("Đã copy {$copiedFiles} files...");
                    }
                }
            }
        }

        $this->info("Tổng cộng đã copy: {$copiedFiles} files");
    }
}
