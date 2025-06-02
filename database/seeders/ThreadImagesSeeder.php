<?php

namespace Database\Seeders;

use App\Models\Thread;
use App\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ThreadImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đảm bảo thư mục thread-images tồn tại
        Storage::disk('public')->makeDirectory('thread-images');

        // Lấy tất cả threads
        $threads = Thread::all();

        // Danh sách các category để tạo ảnh phù hợp
        $imageCategories = [
            'technology' => 'technology,engineering,circuit,electronics',
            'mechanical' => 'machinery,mechanical,industrial,equipment',
            'automotive' => 'automotive,car,engine,vehicle',
            'construction' => 'construction,building,architecture,civil',
            'manufacturing' => 'manufacturing,factory,production,assembly',
            'robotics' => 'robotics,automation,robot,artificial-intelligence',
            'renewable' => 'renewable-energy,solar,wind,sustainable',
            'aerospace' => 'aerospace,aviation,aircraft,space'
        ];

        foreach ($threads as $thread) {
            // Kiểm tra xem thread đã có media chưa
            if ($thread->media()->count() > 0) {
                continue; // Bỏ qua nếu đã có media
            }

            // Chọn category ảnh dựa trên thread category hoặc forum
            $imageCategory = $this->selectImageCategory($thread, $imageCategories);

            // Tạo 1-3 ảnh cho mỗi thread
            $imageCount = rand(1, 3);

            for ($i = 0; $i < $imageCount; $i++) {
                $this->createThreadImage($thread, $i, $imageCategory);
            }
        }

        $this->command->info('Thread images seeded successfully!');
    }

    /**
     * Chọn category ảnh phù hợp với thread
     */
    private function selectImageCategory($thread, $imageCategories)
    {
        // Mapping dựa trên tên category hoặc forum
        $threadName = strtolower($thread->title ?? '');
        $categoryName = strtolower($thread->category->name ?? '');
        $forumName = strtolower($thread->forum->name ?? '');

        $searchText = $threadName . ' ' . $categoryName . ' ' . $forumName;

        if (str_contains($searchText, 'mechanical') || str_contains($searchText, 'máy')) {
            return $imageCategories['mechanical'];
        } elseif (str_contains($searchText, 'automotive') || str_contains($searchText, 'ôtô') || str_contains($searchText, 'xe')) {
            return $imageCategories['automotive'];
        } elseif (str_contains($searchText, 'construction') || str_contains($searchText, 'xây dựng')) {
            return $imageCategories['construction'];
        } elseif (str_contains($searchText, 'robot') || str_contains($searchText, 'automation')) {
            return $imageCategories['robotics'];
        } elseif (str_contains($searchText, 'renewable') || str_contains($searchText, 'năng lượng')) {
            return $imageCategories['renewable'];
        } elseif (str_contains($searchText, 'aerospace') || str_contains($searchText, 'hàng không')) {
            return $imageCategories['aerospace'];
        } elseif (str_contains($searchText, 'manufacturing') || str_contains($searchText, 'sản xuất')) {
            return $imageCategories['manufacturing'];
        } else {
            return $imageCategories['technology'];
        }
    }

    /**
     * Tạo ảnh cho thread
     */
    private function createThreadImage($thread, $index, $imageCategory)
    {
        // Kích thước ảnh khác nhau
        $sizes = ['800x600', '1024x768', '1200x800'];
        $size = $sizes[array_rand($sizes)];

        // Tạo tên file
        $fileName = "thread-{$thread->id}-image-{$index}.jpg";
        $filePath = "thread-images/{$fileName}";

        // URL Unsplash với category cụ thể
        $imageUrl = "https://source.unsplash.com/{$size}/?{$imageCategory}";

        // Tạo các title và description phù hợp
        $titles = [
            'Thiết kế kỹ thuật chi tiết',
            'Hình ảnh dự án thực tế',
            'Mô hình 3D và CAD drawing',
            'Kết quả thử nghiệm và testing',
            'Quy trình sản xuất và assembly',
            'Báo cáo kỹ thuật và analysis',
            'Prototype và mô hình thử nghiệm',
            'Sản phẩm hoàn thiện và deployment'
        ];

        $descriptions = [
            'Hình ảnh chi tiết về thiết kế và quy trình kỹ thuật của dự án',
            'Kết quả thực tế từ quá trình phát triển và testing',
            'Mô hình 3D và technical drawing với specifications đầy đủ',
            'Báo cáo đánh giá hiệu suất và quality assurance',
            'Quy trình sản xuất tối ưu và quality control measures',
            'Phân tích kỹ thuật và performance metrics chi tiết',
            'Prototype testing và validation results',
            'Sản phẩm cuối cùng với full documentation và guidelines'
        ];

        // Tạo media record
        Media::create([
            'user_id' => $thread->user_id,
            'thread_id' => $thread->id,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => 'image/jpeg',
            'file_size' => rand(800000, 3000000), // 800KB - 3MB
            'title' => ($index === 0 ? '[Featured] ' : '') . $titles[array_rand($titles)],
            'description' => $descriptions[array_rand($descriptions)] . ($index === 0 ? ' (Ảnh đại diện)' : ''),
            'mediable_id' => $thread->id,
            'mediable_type' => Thread::class,
        ]);

        $this->command->info("Created media for thread {$thread->id}: {$fileName}");
    }
}
