<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Database\Seeder;

class ForumCategoryImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $forums = Forum::all();

        // Hình ảnh chuyên nghiệp cho các lĩnh vực kỹ thuật và kiến trúc
        $professionalImages = [
            // Kiến trúc và Xây dựng
            [
                'url' => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=800',
                'title' => 'Kiến Trúc Hiện Đại',
                'description' => 'Tòa nhà kiến trúc hiện đại với thiết kế độc đáo và bền vững',
                'category' => 'architecture'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800',
                'title' => 'Tòa Nhà Cao Tầng',
                'description' => 'Khu phức hợp cao tầng với thiết kế hiện đại',
                'category' => 'architecture'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1448630360428-65456885c650?w=800',
                'title' => 'Công Trình Xây Dựng',
                'description' => 'Công trình xây dựng với cấu trúc thép và kính',
                'category' => 'construction'
            ],

            // Cơ khí và Tự động hóa
            [
                'url' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800',
                'title' => 'Robot Công Nghiệp',
                'description' => 'Robot cánh tay công nghiệp trong dây chuyền sản xuất',
                'category' => 'mechanical'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?w=800',
                'title' => 'Hệ Thống Tự Động Hóa',
                'description' => 'Hệ thống tự động hóa trong nhà máy sản xuất',
                'category' => 'automation'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?w=800',
                'title' => 'Máy Móc Công Nghiệp',
                'description' => 'Máy móc và thiết bị công nghiệp hiện đại',
                'category' => 'machinery'
            ],

            // Giao thông và Hạ tầng
            [
                'url' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800',
                'title' => 'Cầu Đường Hiện Đại',
                'description' => 'Cầu vượt và hệ thống giao thông hiện đại',
                'category' => 'infrastructure'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800',
                'title' => 'Hệ Thống Đường Sắt',
                'description' => 'Ga tàu điện ngầm và hệ thống giao thông công cộng',
                'category' => 'transport'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800',
                'title' => 'Sân Bay Quốc Tế',
                'description' => 'Terminal sân bay với kiến trúc hiện đại',
                'category' => 'aviation'
            ],

            // Quy hoạch và Đô thị
            [
                'url' => 'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1f?w=800',
                'title' => 'Quy Hoạch Đô Thị',
                'description' => 'Khu đô thị hiện đại với quy hoạch khoa học',
                'category' => 'urban'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800',
                'title' => 'Khu Phức Hợp',
                'description' => 'Khu phức hợp thương mại và nhà ở',
                'category' => 'development'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=800',
                'title' => 'Không Gian Xanh',
                'description' => 'Công viên và không gian xanh trong đô thị',
                'category' => 'green'
            ],

            // Năng lượng và Môi trường
            [
                'url' => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=800',
                'title' => 'Năng Lượng Tái Tạo',
                'description' => 'Trang trại điện gió và năng lượng tái tạo',
                'category' => 'energy'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1497440001374-f26997328c1b?w=800',
                'title' => 'Panel Năng Lượng Mặt Trời',
                'description' => 'Hệ thống pin mặt trời trên mái nhà',
                'category' => 'solar'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800',
                'title' => 'Nhà Máy Điện',
                'description' => 'Cơ sở hạ tầng năng lượng và điện lực',
                'category' => 'power'
            ],

            // Công nghệ và Kỹ thuật số
            [
                'url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=800',
                'title' => 'Trung Tâm Dữ Liệu',
                'description' => 'Data center và hạ tầng công nghệ thông tin',
                'category' => 'technology'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1581092162384-8987c1d64718?w=800',
                'title' => 'Hệ Thống Điều Khiển',
                'description' => 'Phòng điều khiển và giám sát hệ thống',
                'category' => 'control'
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800',
                'title' => 'Smart City',
                'description' => 'Thành phố thông minh với IoT và công nghệ cao',
                'category' => 'smart'
            ]
        ];

        // Xóa các media cũ của categories và forums để tránh trùng lặp
        Media::whereIn('mediable_type', [Category::class, Forum::class])->delete();

        // Tạo hình ảnh cho Categories
        if ($categories->count() > 0) {
            foreach ($categories as $index => $category) {
                // Chọn hình ảnh phù hợp dựa trên tên category
                $imageData = $this->selectImageForCategory($category, $professionalImages, $index);

                Media::create([
                    'user_id' => $users->random()->id,
                    'file_name' => 'category_' . $category->id . '_' . $category->slug . '.jpg',
                    'file_path' => $imageData['url'],
                    'file_type' => 'image/jpeg',
                    'file_size' => rand(500000, 2000000), // 500KB - 2MB
                    'title' => $category->name . ' - ' . $imageData['title'],
                    'description' => 'Ảnh đại diện cho danh mục ' . $category->name . '. ' . $imageData['description'],
                    'mediable_id' => $category->id,
                    'mediable_type' => Category::class,
                ]);
            }
        }

        // Tạo hình ảnh cho Forums
        if ($forums->count() > 0) {
            foreach ($forums as $index => $forum) {
                // Chọn hình ảnh phù hợp dựa trên tên forum
                $imageData = $this->selectImageForForum($forum, $professionalImages, $index);

                Media::create([
                    'user_id' => $users->random()->id,
                    'file_name' => 'forum_' . $forum->id . '_' . $forum->slug . '.jpg',
                    'file_path' => $imageData['url'],
                    'file_type' => 'image/jpeg',
                    'file_size' => rand(500000, 2000000), // 500KB - 2MB
                    'title' => $forum->name . ' - ' . $imageData['title'],
                    'description' => 'Ảnh đại diện cho diễn đàn ' . $forum->name . '. ' . $imageData['description'],
                    'mediable_id' => $forum->id,
                    'mediable_type' => Forum::class,
                ]);
            }
        }

        $this->command->info('✅ Đã tạo ' . $categories->count() . ' hình ảnh cho Categories');
        $this->command->info('✅ Đã tạo ' . $forums->count() . ' hình ảnh cho Forums');
    }

    /**
     * Chọn hình ảnh phù hợp cho category dựa trên tên
     */
    private function selectImageForCategory($category, $images, $defaultIndex)
    {
        $categoryName = strtolower($category->name);

        // Map từ khóa trong tên category với loại hình ảnh
        $categoryMap = [
            'kiến trúc' => 'architecture',
            'architecture' => 'architecture',
            'xây dựng' => 'construction',
            'construction' => 'construction',
            'cơ khí' => 'mechanical',
            'mechanical' => 'mechanical',
            'tự động' => 'automation',
            'automation' => 'automation',
            'máy móc' => 'machinery',
            'machinery' => 'machinery',
            'giao thông' => 'transport',
            'transport' => 'transport',
            'hạ tầng' => 'infrastructure',
            'infrastructure' => 'infrastructure',
            'đô thị' => 'urban',
            'urban' => 'urban',
            'quy hoạch' => 'development',
            'planning' => 'development',
            'năng lượng' => 'energy',
            'energy' => 'energy',
            'điện' => 'power',
            'electric' => 'power',
            'môi trường' => 'green',
            'environment' => 'green',
            'công nghệ' => 'technology',
            'technology' => 'technology',
            'thông minh' => 'smart',
            'smart' => 'smart'
        ];

        // Tìm hình ảnh phù hợp
        foreach ($categoryMap as $keyword => $imageCategory) {
            if (str_contains($categoryName, $keyword)) {
                $matchingImages = array_filter($images, function ($img) use ($imageCategory) {
                    return $img['category'] === $imageCategory;
                });

                if (!empty($matchingImages)) {
                    return array_values($matchingImages)[0];
                }
            }
        }

        // Fallback về hình ảnh theo index
        return $images[$defaultIndex % count($images)];
    }

    /**
     * Chọn hình ảnh phù hợp cho forum dựa trên tên
     */
    private function selectImageForForum($forum, $images, $defaultIndex)
    {
        // Sử dụng logic tương tự như category
        return $this->selectImageForCategory($forum, $images, $defaultIndex);
    }
}
