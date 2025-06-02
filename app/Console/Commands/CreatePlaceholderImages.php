<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreatePlaceholderImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'threads:create-placeholders {--limit=20 : Số lượng ảnh cần tạo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo placeholder images cho threads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        // Lấy media records của threads chưa có file thực tế
        $mediaList = Media::whereNotNull('thread_id')
            ->where('file_type', 'like', 'image/%')
            ->take($limit)
            ->get();

        if ($mediaList->isEmpty()) {
            $this->info('Không có media nào cần tạo placeholder.');
            return;
        }

        $this->info("Đang tạo {$mediaList->count()} ảnh placeholder...");

        $progressBar = $this->output->createProgressBar($mediaList->count());
        $successCount = 0;

        foreach ($mediaList as $media) {
            try {
                $success = $this->createPlaceholderImage($media);
                if ($success) {
                    $successCount++;
                }
            } catch (\Exception $e) {
                $this->error("Lỗi khi tạo ảnh {$media->file_name}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Hoàn thành! Đã tạo thành công {$successCount}/{$mediaList->count()} ảnh placeholder.");
    }

    /**
     * Tạo placeholder image đơn giản
     */
    private function createPlaceholderImage($media)
    {
        try {
            // Kiểm tra xem file đã tồn tại chưa
            if (Storage::disk('public')->exists($media->file_path)) {
                return true; // Đã tồn tại
            }

            // Tạo ảnh placeholder với GD
            $width = 800;
            $height = 600;

            // Tạo image
            $image = imagecreate($width, $height);

            // Màu sắc
            $colors = [
                ['bg' => [240, 248, 255], 'text' => [70, 130, 180]],    // AliceBlue với SteelBlue
                ['bg' => [245, 245, 220], 'text' => [139, 69, 19]],     // Beige với SaddleBrown
                ['bg' => [248, 248, 255], 'text' => [75, 0, 130]],      // GhostWhite với Indigo
                ['bg' => [240, 255, 240], 'text' => [34, 139, 34]],     // Honeydew với ForestGreen
                ['bg' => [255, 228, 225], 'text' => [178, 34, 34]],     // MistyRose với Firebrick
            ];

            $colorScheme = $colors[array_rand($colors)];

            $bgColor = imagecolorallocate($image, ...$colorScheme['bg']);
            $textColor = imagecolorallocate($image, ...$colorScheme['text']);
            $borderColor = imagecolorallocate($image, 200, 200, 200);

            // Vẽ border
            imagerectangle($image, 0, 0, $width - 1, $height - 1, $borderColor);
            imagerectangle($image, 1, 1, $width - 2, $height - 2, $borderColor);

            // Lấy thông tin thread
            $thread = $media->thread;
            $threadTitle = $thread ? $thread->title : "Thread Image";

            // Vẽ title (cắt ngắn nếu quá dài)
            $title = mb_strlen($threadTitle) > 50 ? mb_substr($threadTitle, 0, 47) . '...' : $threadTitle;
            $fontSize = 4;
            $titleWidth = imagefontwidth($fontSize) * strlen($title);
            $titleHeight = imagefontheight($fontSize);

            $titleX = ($width - $titleWidth) / 2;
            $titleY = ($height / 2) - 30;

            imagestring($image, $fontSize, $titleX, $titleY, $title, $textColor);

            // Vẽ thông tin thread
            $info = "Thread #{$media->thread_id} - Image {$this->getImageIndex($media)}";
            $infoWidth = imagefontwidth(3) * strlen($info);
            $infoX = ($width - $infoWidth) / 2;
            $infoY = $titleY + 40;

            imagestring($image, 3, $infoX, $infoY, $info, $textColor);

            // Vẽ category nếu có
            if ($thread && $thread->category) {
                $category = "Category: " . $thread->category->name;
                $catWidth = imagefontwidth(2) * strlen($category);
                $catX = ($width - $catWidth) / 2;
                $catY = $infoY + 30;

                imagestring($image, 2, $catX, $catY, $category, $textColor);
            }

            // Vẽ placeholder icon (simple rectangle)
            $iconSize = 60;
            $iconX = ($width - $iconSize) / 2;
            $iconY = 100;

            imagerectangle($image, $iconX, $iconY, $iconX + $iconSize, $iconY + $iconSize, $textColor);
            imagerectangle($image, $iconX + 10, $iconY + 10, $iconX + $iconSize - 10, $iconY + $iconSize - 10, $textColor);

            // Lưu ảnh
            ob_start();
            imagejpeg($image, null, 85);
            $imageData = ob_get_contents();
            ob_end_clean();

            Storage::disk('public')->put($media->file_path, $imageData);

            // Cập nhật file size
            $actualSize = Storage::disk('public')->size($media->file_path);
            $media->update(['file_size' => $actualSize]);

            imagedestroy($image);

            return true;
        } catch (\Exception $e) {
            $this->error("Lỗi chi tiết: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy index của ảnh từ filename
     */
    private function getImageIndex($media)
    {
        if (preg_match('/image-(\d+)\./', $media->file_name, $matches)) {
            return $matches[1];
        }
        return '0';
    }
}
