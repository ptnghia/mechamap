<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeVideo;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeCategory;
use App\Models\User;

class KnowledgeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }

        // Get categories
        $categories = KnowledgeCategory::all();
        if ($categories->isEmpty()) {
            return;
        }

        // Create sample articles
        $articles = [
            [
                'title' => 'Hướng Dẫn Thiết Kế Bánh Răng Cơ Bản',
                'slug' => 'huong-dan-thiet-ke-banh-rang-co-ban',
                'excerpt' => 'Tìm hiểu các nguyên lý cơ bản trong thiết kế bánh răng, từ tính toán mô-đun đến chọn vật liệu phù hợp.',
                'content' => '<h2>Giới thiệu về Bánh Răng</h2><p>Bánh răng là một trong những chi tiết máy quan trọng nhất trong cơ khí, được sử dụng để truyền chuyển động và lực giữa các trục.</p><h3>Các Loại Bánh Răng</h3><ul><li>Bánh răng trụ răng thẳng</li><li>Bánh răng trụ răng nghiêng</li><li>Bánh răng côn</li><li>Bánh răng vít</li></ul><h3>Tính Toán Thiết Kế</h3><p>Để thiết kế bánh răng, cần tính toán các thông số sau:</p><ol><li>Mô-đun (m)</li><li>Số răng (z)</li><li>Đường kính chia (d)</li><li>Chiều rộng răng (b)</li></ol>',
                'category_id' => $categories->where('slug', 'thiet-ke-co-khi')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'difficulty_level' => 'beginner',
                'tags' => ['bánh răng', 'thiết kế', 'cơ khí', 'tính toán'],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(5),
                'views_count' => 245,
                'rating_average' => 4.5,
                'rating_count' => 12,
            ],
            [
                'title' => 'Phân Tích Ứng Suất Trong Sức Bền Vật Liệu',
                'slug' => 'phan-tich-ung-suat-trong-suc-ben-vat-lieu',
                'excerpt' => 'Hướng dẫn chi tiết về cách phân tích ứng suất và biến dạng trong các chi tiết máy.',
                'content' => '<h2>Khái Niệm Ứng Suất</h2><p>Ứng suất là đại lượng đặc trưng cho cường độ của lực nội tại một điểm trong vật thể.</p><h3>Các Loại Ứng Suất</h3><ul><li>Ứng suất pháp tuyến</li><li>Ứng suất tiếp tuyến</li><li>Ứng suất tương đương</li></ul>',
                'category_id' => $categories->where('slug', 'suc-ben-vat-lieu')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'difficulty_level' => 'intermediate',
                'tags' => ['ứng suất', 'sức bền', 'phân tích', 'vật liệu'],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'views_count' => 156,
                'rating_average' => 4.2,
                'rating_count' => 8,
            ],
            [
                'title' => 'Lập Trình PLC Siemens S7-1200',
                'slug' => 'lap-trinh-plc-siemens-s7-1200',
                'excerpt' => 'Hướng dẫn từ cơ bản đến nâng cao về lập trình PLC Siemens S7-1200.',
                'content' => '<h2>Giới Thiệu PLC S7-1200</h2><p>PLC S7-1200 là dòng PLC compact của Siemens, phù hợp cho các ứng dụng tự động hóa vừa và nhỏ.</p>',
                'category_id' => $categories->where('slug', 'plc')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'difficulty_level' => 'advanced',
                'tags' => ['PLC', 'Siemens', 'tự động hóa', 'lập trình'],
                'status' => 'draft',
                'is_featured' => false,
                'published_at' => null,
                'views_count' => 0,
                'rating_average' => 0,
                'rating_count' => 0,
            ]
        ];

        foreach ($articles as $articleData) {
            KnowledgeArticle::create($articleData);
        }

        // Create sample videos
        $videos = [
            [
                'title' => 'Hướng Dẫn Sử Dụng AutoCAD 2025',
                'slug' => 'huong-dan-su-dung-autocad-2025',
                'description' => 'Video hướng dẫn chi tiết cách sử dụng AutoCAD 2025 từ cơ bản đến nâng cao.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_type' => 'youtube',
                'duration' => 1800, // 30 minutes
                'category_id' => $categories->where('slug', 'autocad')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'difficulty_level' => 'beginner',
                'tags' => ['AutoCAD', 'CAD', 'thiết kế', 'tutorial'],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(7),
                'views_count' => 892,
                'rating_average' => 4.7,
                'rating_count' => 23,
            ],
            [
                'title' => 'Thiết Kế 3D Với SolidWorks',
                'slug' => 'thiet-ke-3d-voi-solidworks',
                'description' => 'Học cách tạo mô hình 3D phức tạp trong SolidWorks.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_type' => 'youtube',
                'duration' => 2400, // 40 minutes
                'category_id' => $categories->where('slug', 'solidworks')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'difficulty_level' => 'intermediate',
                'tags' => ['SolidWorks', '3D', 'modeling', 'thiết kế'],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(2),
                'views_count' => 445,
                'rating_average' => 4.3,
                'rating_count' => 15,
            ]
        ];

        foreach ($videos as $videoData) {
            KnowledgeVideo::create($videoData);
        }

        // Create sample documents
        $documents = [
            [
                'title' => 'Tiêu Chuẩn ISO 9001:2015',
                'slug' => 'tieu-chuan-iso-9001-2015',
                'description' => 'Tài liệu tiêu chuẩn quản lý chất lượng ISO 9001:2015 đầy đủ.',
                'file_path' => 'knowledge/documents/iso-9001-2015.pdf',
                'file_type' => 'pdf',
                'file_size' => 2048576, // 2MB
                'original_filename' => 'ISO_9001_2015_Standard.pdf',
                'category_id' => $categories->where('slug', 'vat-lieu-ky-thuat')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'tags' => ['ISO', 'tiêu chuẩn', 'chất lượng', 'quản lý'],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now()->subDays(10),
                'download_count' => 67,
                'rating_average' => 4.8,
                'rating_count' => 19,
            ],
            [
                'title' => 'Bảng Tra Cứu Vật Liệu Thép',
                'slug' => 'bang-tra-cuu-vat-lieu-thep',
                'description' => 'Bảng tra cứu đầy đủ các loại thép và tính chất cơ học.',
                'file_path' => 'knowledge/documents/steel-materials-table.xlsx',
                'file_type' => 'xlsx',
                'file_size' => 512000, // 500KB
                'original_filename' => 'Steel_Materials_Reference_Table.xlsx',
                'category_id' => $categories->where('slug', 'vat-lieu-ky-thuat')->first()?->id ?? $categories->first()->id,
                'author_id' => $admin->id,
                'tags' => ['thép', 'vật liệu', 'tra cứu', 'tính chất'],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now()->subDays(4),
                'download_count' => 134,
                'rating_average' => 4.4,
                'rating_count' => 11,
            ]
        ];

        foreach ($documents as $documentData) {
            KnowledgeDocument::create($documentData);
        }
    }
}
