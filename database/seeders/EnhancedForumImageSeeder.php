<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Database\Seeder;

class EnhancedForumImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $forums = Forum::all();

        // Bộ sưu tập hình ảnh chất lượng cao theo chủ đề kỹ thuật
        $engineeringImages = [
            // Kiến trúc và Thiết kế
            [
                'url' => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=1200&q=80',
                'title' => 'Kiến Trúc Hiện Đại Bền Vững',
                'description' => 'Tòa nhà với thiết kế kiến trúc xanh và công nghệ bền vững',
                'tags' => ['architecture', 'modern', 'sustainable', 'green']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=80',
                'title' => 'Khu Phức Hợp Cao Tầng',
                'description' => 'Quần thể tòa nhà cao tầng với kiến trúc đương đại',
                'tags' => ['skyscraper', 'complex', 'urban', 'architecture']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1448630360428-65456885c650?w=1200&q=80',
                'title' => 'Nhà Ga Tàu Điện Hiện Đại',
                'description' => 'Kiến trúc ga tàu với cấu trúc thép và kính',
                'tags' => ['station', 'steel', 'glass', 'transport']
            ],

            // Cơ khí và Sản xuất
            [
                'url' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=1200&q=80',
                'title' => 'Robot Công Nghiệp ABB',
                'description' => 'Hệ thống robot tự động trong dây chuyền sản xuất ô tô',
                'tags' => ['robot', 'industrial', 'automation', 'manufacturing']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?w=1200&q=80',
                'title' => 'Dây Chuyền Sản Xuất Tự Động',
                'description' => 'Hệ thống sản xuất hoàn toàn tự động với robot KUKA',
                'tags' => ['automation', 'production', 'kuka', 'assembly']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?w=1200&q=80',
                'title' => 'Máy CNC Chính Xác Cao',
                'description' => 'Máy gia công CNC 5 trục với độ chính xác cao',
                'tags' => ['cnc', 'machining', 'precision', 'manufacturing']
            ],

            // Giao thông và Hạ tầng
            [
                'url' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=1200&q=80',
                'title' => 'Cầu Cáp Hiện Đại',
                'description' => 'Cầu dây văng với thiết kế kỹ thuật tiên tiến',
                'tags' => ['bridge', 'cable', 'infrastructure', 'engineering']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1200&q=80',
                'title' => 'Tàu Điện Ngầm Hiện Đại',
                'description' => 'Ga tàu điện ngầm với thiết kế hiện đại và tiện ích',
                'tags' => ['subway', 'metro', 'transport', 'modern']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=1200&q=80',
                'title' => 'Terminal Sân Bay Quốc Tế',
                'description' => 'Nhà ga sân bay với kiến trúc và hệ thống hiện đại',
                'tags' => ['airport', 'terminal', 'aviation', 'architecture']
            ],

            // Quy hoạch và Đô thị
            [
                'url' => 'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1f?w=1200&q=80',
                'title' => 'Khu Đô Thị Thông Minh',
                'description' => 'Khu đô thị với quy hoạch khoa học và công nghệ cao',
                'tags' => ['smart_city', 'urban', 'planning', 'technology']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1200&q=80',
                'title' => 'Công Viên Đô Thị Xanh',
                'description' => 'Không gian xanh hài hòa trong quy hoạch đô thị',
                'tags' => ['park', 'green', 'urban', 'landscape']
            ],

            // Năng lượng và Môi trường
            [
                'url' => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=1200&q=80',
                'title' => 'Trang Trại Điện Gió',
                'description' => 'Hệ thống tuabin gió cho năng lượng tái tạo',
                'tags' => ['wind', 'energy', 'renewable', 'turbine']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1497440001374-f26997328c1b?w=1200&q=80',
                'title' => 'Hệ Thống Pin Mặt Trời',
                'description' => 'Tấm pin mặt trời trên mái nhà và khu công nghiệp',
                'tags' => ['solar', 'panels', 'renewable', 'green']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=1200&q=80',
                'title' => 'Nhà Máy Điện Hiện Đại',
                'description' => 'Cơ sở hạ tầng năng lượng với công nghệ tiên tiến',
                'tags' => ['power_plant', 'energy', 'infrastructure', 'technology']
            ],

            // Công nghệ và Số hóa
            [
                'url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=1200&q=80',
                'title' => 'Trung Tâm Dữ Liệu',
                'description' => 'Server farm và hạ tầng công nghệ thông tin',
                'tags' => ['datacenter', 'servers', 'technology', 'it']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092162384-8987c1d64718?w=1200&q=80',
                'title' => 'Phòng Điều Khiển Tự Động',
                'description' => 'Trung tâm điều khiển và giám sát hệ thống',
                'tags' => ['control_room', 'monitoring', 'automation', 'scada']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1200&q=80',
                'title' => 'Thành Phố Thông Minh IoT',
                'description' => 'Hệ thống IoT và thành phố thông minh',
                'tags' => ['iot', 'smart_city', 'sensors', 'connectivity']
            ],

            // Thêm hình ảnh CAD và Thiết kế
            [
                'url' => 'https://images.unsplash.com/photo-1551522435-a13afa10f103?w=1200&q=80',
                'title' => 'Thiết Kế CAD 3D',
                'description' => 'Mô hình 3D của linh kiện cơ khí trên màn hình CAD',
                'tags' => ['cad', '3d', 'design', 'modeling']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?w=1200&q=80',
                'title' => 'Bản Vẽ Kỹ Thuật',
                'description' => 'Bản vẽ kỹ thuật chi tiết với kích thước và dung sai',
                'tags' => ['blueprint', 'technical_drawing', 'engineering', 'design']
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1551522435-a84c3dd1ed6d?w=1200&q=80',
                'title' => 'Phần Mềm Mô Phỏng',
                'description' => 'Mô phỏng FEA và CFD trong thiết kế kỹ thuật',
                'tags' => ['simulation', 'fea', 'cfd', 'analysis']
            ]
        ];

        // Tạo nhiều hình ảnh cho mỗi category (2-3 hình/category)
        foreach ($categories as $category) {
            $relevantImages = $this->findRelevantImages($category, $engineeringImages);

            // Tạo 2-3 hình ảnh cho mỗi category
            $imageCount = rand(2, 3);
            for ($i = 0; $i < $imageCount && $i < count($relevantImages); $i++) {
                $imageData = $relevantImages[$i];

                Media::create([
                    'user_id' => $users->random()->id,
                    'file_name' => 'enhanced_category_' . $category->id . '_' . ($i + 1) . '.jpg',
                    'file_path' => $imageData['url'],
                    'file_type' => 'image/jpeg',
                    'file_size' => rand(800000, 3000000), // 800KB - 3MB (High quality)
                    'title' => $category->name . ' - ' . $imageData['title'],
                    'description' => 'Hình ảnh chất lượng cao cho ' . $category->name . ': ' . $imageData['description'],
                    'mediable_id' => $category->id,
                    'mediable_type' => Category::class,
                ]);
            }
        }

        // Tạo nhiều hình ảnh cho mỗi forum (2-4 hình/forum)
        foreach ($forums as $forum) {
            $relevantImages = $this->findRelevantImages($forum, $engineeringImages);

            // Tạo 2-4 hình ảnh cho mỗi forum
            $imageCount = rand(2, 4);
            for ($i = 0; $i < $imageCount && $i < count($relevantImages); $i++) {
                $imageData = $relevantImages[$i];

                Media::create([
                    'user_id' => $users->random()->id,
                    'file_name' => 'enhanced_forum_' . $forum->id . '_' . ($i + 1) . '.jpg',
                    'file_path' => $imageData['url'],
                    'file_type' => 'image/jpeg',
                    'file_size' => rand(800000, 3000000), // 800KB - 3MB (High quality)
                    'title' => $forum->name . ' - ' . $imageData['title'],
                    'description' => 'Hình ảnh chất lượng cao cho diễn đàn ' . $forum->name . ': ' . $imageData['description'],
                    'mediable_id' => $forum->id,
                    'mediable_type' => Forum::class,
                ]);
            }
        }

        $this->command->info('✅ Đã tạo thêm hình ảnh chất lượng cao cho ' . $categories->count() . ' Categories');
        $this->command->info('✅ Đã tạo thêm hình ảnh chất lượng cao cho ' . $forums->count() . ' Forums');
    }

    /**
     * Tìm hình ảnh phù hợp dựa trên tên và mô tả
     */
    private function findRelevantImages($entity, $images)
    {
        $entityName = strtolower($entity->name . ' ' . ($entity->description ?? ''));
        $scoredImages = [];

        foreach ($images as $image) {
            $score = 0;

            // Tính điểm dựa trên tags
            foreach ($image['tags'] as $tag) {
                if (
                    str_contains($entityName, $tag) ||
                    str_contains($entityName, str_replace('_', ' ', $tag))
                ) {
                    $score += 10;
                }
            }

            // Tính điểm dựa trên từ khóa
            $keywords = [
                'kiến trúc' => ['architecture', 'building', 'design'],
                'cơ khí' => ['mechanical', 'machine', 'engineering'],
                'tự động' => ['automation', 'robot', 'control'],
                'giao thông' => ['transport', 'bridge', 'infrastructure'],
                'năng lượng' => ['energy', 'power', 'renewable'],
                'công nghệ' => ['technology', 'digital', 'smart'],
                'đô thị' => ['urban', 'city', 'planning'],
                'môi trường' => ['green', 'sustainable', 'environment']
            ];

            foreach ($keywords as $vietnamese => $englishTerms) {
                if (str_contains($entityName, $vietnamese)) {
                    foreach ($englishTerms as $term) {
                        if (in_array($term, $image['tags'])) {
                            $score += 15;
                        }
                    }
                }
            }

            $scoredImages[] = ['image' => $image, 'score' => $score];
        }

        // Sắp xếp theo điểm và trả về
        usort($scoredImages, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        return array_map(function ($item) {
            return $item['image'];
        }, $scoredImages);
    }
}
