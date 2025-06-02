<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadThreadImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'threads:download-images 
                            {--limit=50 : Số lượng ảnh cần tải}
                            {--force : Tải lại tất cả ảnh, kể cả đã tồn tại}
                            {--thread= : Chỉ tải ảnh cho thread cụ thể}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tải hình ảnh thật từ internet thay thế placeholder images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');
        $threadId = $this->option('thread');

        $this->info('🌐 Đang tải hình ảnh thật từ internet...');

        // Xây dựng query
        $query = Media::whereNotNull('mediable_id')
            ->where('mediable_type', 'App\\Models\\Thread')
            ->where('file_type', 'like', 'image/%');

        if ($threadId) {
            $query->where('mediable_id', $threadId);
            $this->info("📍 Chỉ tải ảnh cho Thread #{$threadId}");
        }

        if (!$force) {
            // Chỉ lấy những ảnh chưa có file thực tế hoặc file size nhỏ (có thể là placeholder)
            $query->where(function($q) {
                $q->where('file_size', '<', 50000) // Nhỏ hơn 50KB có thể là placeholder
                  ->orWhereNull('file_size');
            });
        }

        $mediaList = $query->take($limit)->get();

        if ($mediaList->isEmpty()) {
            $this->info('✅ Không có media nào cần tải.');
            return 0;
        }

        $this->info("📥 Sẽ tải {$mediaList->count()} ảnh...");

        $progressBar = $this->output->createProgressBar($mediaList->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $successCount = 0;

        foreach ($mediaList as $media) {
            $progressBar->setMessage("Đang xử lý: {$media->file_name}");
            
            try {
                $success = $this->downloadImage($media);
                if ($success) {
                    $successCount++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Lỗi khi tải ảnh {$media->file_name}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        // Sync sang public/storage sau khi tải xong
        $this->info('🔄 Đang đồng bộ files sang public/storage...');
        $this->call('storage:sync');
        
        $this->info("✅ Hoàn thành! Đã tải thành công {$successCount}/{$mediaList->count()} ảnh.");
        
        return 0;
    }

    /**
     * Tải một ảnh từ internet với retry logic
     */
    private function downloadImage($media)
    {
        $force = $this->option('force');
        
        // Kiểm tra xem file đã tồn tại chưa (trừ khi force)
        if (!$force && Storage::disk('public')->exists($media->file_path)) {
            $this->line("  ✓ File đã tồn tại: {$media->file_name}");
            return true;
        }

        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                // Tạo URL ảnh
                $imageUrl = $this->generateUnsplashUrl($media);
                
                $this->line("  → Đang tải: {$media->file_name} từ " . parse_url($imageUrl, PHP_URL_HOST));

                // Tải ảnh từ internet với timeout
                $response = Http::timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ])
                    ->get($imageUrl);

                if ($response->successful() && $response->body()) {
                    // Kiểm tra content type
                    $contentType = $response->header('content-type');
                    if (!str_contains($contentType, 'image/')) {
                        throw new \Exception("Không phải file ảnh: {$contentType}");
                    }

                    // Lưu file vào storage (ghi đè nếu force)
                    Storage::disk('public')->put($media->file_path, $response->body());

                    // Cập nhật file size thực tế
                    $actualSize = Storage::disk('public')->size($media->file_path);
                    $media->update(['file_size' => $actualSize]);

                    $this->line("  ✓ Đã tải thành công: " . number_format($actualSize / 1024, 1) . " KB");
                    return true;
                }

                throw new \Exception("HTTP {$response->status()}");

            } catch (\Exception $e) {
                $retryCount++;
                $this->line("  ⚠ Lần thử {$retryCount}: {$e->getMessage()}");
                
                if ($retryCount < $maxRetries) {
                    sleep(1); // Đợi 1 giây trước khi thử lại
                }
            }
        }

        // Nếu không tải được sau nhiều lần thử, tạo placeholder
        $this->line("  → Tạo placeholder image cho: {$media->file_name}");
        return $this->createPlaceholderImage($media);
    }

    /**
     * Tạo URL hình ảnh từ nhiều nguồn khác nhau
     */
    private function generateUnsplashUrl($media)
    {
        // Lấy thông tin thread để xác định category
        $thread = $media->thread;
        $threadTitle = strtolower($thread->title ?? '');
        
        // Xác định category dựa trên nội dung thread
        $category = $this->detectCategoryFromThread($threadTitle);
        
        // Danh sách các nguồn ảnh chất lượng cao
        $sources = [
            // Unsplash với category cụ thể
            "https://source.unsplash.com/1200x800/?{$category}",
            // Picsum cho ảnh đẹp random
            "https://picsum.photos/1200/800",
            // Lorem Picsum với blur effect
            "https://picsum.photos/1200/800?random=" . rand(1, 1000),
        ];

        return $sources[array_rand($sources)];
    }

    /**
     * Phát hiện category phù hợp từ tiêu đề thread
     */
    private function detectCategoryFromThread($title)
    {
        $categories = [
            // Công nghệ & Kỹ thuật
            'circuit' => ['circuit', 'electronics', 'electrical', 'pcb', 'arduino', 'raspberry'],
            'mechanical' => ['mechanical', 'gear', 'machine', 'engine', 'motor', 'bearing'],
            'automotive' => ['car', 'auto', 'vehicle', 'truck', 'motorcycle', 'engine'],
            'construction' => ['building', 'construction', 'architecture', 'house', 'concrete'],
            'manufacturing' => ['factory', 'production', 'manufacturing', 'industrial', 'assembly'],
            'technology' => ['computer', 'software', 'coding', 'programming', 'tech', 'digital'],
            'tools' => ['tool', 'wrench', 'screwdriver', 'hammer', 'drill', 'equipment'],
            'energy' => ['solar', 'wind', 'energy', 'power', 'battery', 'electric'],
        ];

        // Tìm category phù hợp nhất
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) {
                    return $category . ',engineering,technical';
                }
            }
        }

        // Default category cho cộng đồng kỹ thuật
        return 'technology,engineering,industrial';
    }

    /**
     * Tạo placeholder image đẹp mắt nếu không tải được từ internet
     */
    private function createPlaceholderImage($media)
    {
        try {
            $width = 1200;
            $height = 800;

            $image = imagecreatetruecolor($width, $height);

            // Gradient background
            $colors = [
                [63, 81, 181],   // Indigo
                [33, 150, 243],  // Blue  
                [76, 175, 80],   // Green
                [255, 152, 0],   // Orange
                [156, 39, 176],  // Purple
            ];
            
            $colorSet = $colors[array_rand($colors)];
            
            // Tạo gradient
            for ($i = 0; $i < $height; $i++) {
                $ratio = $i / $height;
                $r = (int)($colorSet[0] * (1 - $ratio) + $colorSet[0] * 0.7 * $ratio);
                $g = (int)($colorSet[1] * (1 - $ratio) + $colorSet[1] * 0.7 * $ratio);
                $b = (int)($colorSet[2] * (1 - $ratio) + $colorSet[2] * 0.7 * $ratio);
                
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $i, $width, $i, $color);
            }

            // Text colors
            $white = imagecolorallocate($image, 255, 255, 255);
            $lightGray = imagecolorallocate($image, 200, 200, 200);

            // Main title
            $mainText = "MechaMap";
            $fontSize = 5;
            $textWidth = imagefontwidth($fontSize) * strlen($mainText);
            $x = ($width - $textWidth) / 2;
            $y = $height / 2 - 30;
            imagestring($image, $fontSize, $x, $y, $mainText, $white);

            // Subtitle  
            $subText = "Thread #{$media->mediable_id} Image";
            $subTextWidth = imagefontwidth(3) * strlen($subText);
            $subX = ($width - $subTextWidth) / 2;
            $subY = $y + 25;
            imagestring($image, 3, $subX, $subY, $subText, $lightGray);

            // Tech pattern overlay (optional)
            $patternColor = imagecolorallocatealpha($image, 255, 255, 255, 100);
            for ($i = 0; $i < 50; $i++) {
                $px = rand(0, $width);
                $py = rand(0, $height);
                imagesetpixel($image, $px, $py, $patternColor);
            }

            // Save image
            ob_start();
            imagejpeg($image, null, 85);
            $imageData = ob_get_contents();
            ob_end_clean();

            Storage::disk('public')->put($media->file_path, $imageData);

            // Update file size
            $actualSize = Storage::disk('public')->size($media->file_path);
            $media->update(['file_size' => $actualSize]);

            imagedestroy($image);

            $this->line("  ✓ Đã tạo placeholder: " . number_format($actualSize / 1024, 1) . " KB");
            return true;
            
        } catch (\Exception $e) {
            $this->line("  ❌ Không thể tạo placeholder: {$e->getMessage()}");
            return false;
        }
    }
}
