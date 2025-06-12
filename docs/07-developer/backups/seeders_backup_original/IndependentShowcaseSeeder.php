<?php

namespace Database\Seeders;

use App\Models\Showcase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndependentShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🎯 Tạo Independent Showcases (không liên kết Thread/Post)...\n";

        $users = User::all();
        if ($users->count() === 0) {
            echo "❌ Không có users để tạo showcase\n";
            return;
        }

        $showcases = [
            [
                'title' => 'Robot hàn tự động cho sản xuất khung xe đạp',
                'description' => 'Dự án phát triển robot hàn TIG tự động chuyên dụng cho sản xuất khung xe đạp cao cấp. Robot được tích hợp AI vision để nhận diện vị trí hàn chính xác, đảm bảo chất lượng đường hàn đồng đều. Hệ thống có thể xử lý 50 khung/ngày với độ chính xác ±0.1mm.',
                'location' => 'Nhà máy ABC Bikes, Đồng Nai',
                'usage' => 'Sản xuất khung xe đạp carbon và aluminum',
                'floors' => 4,
                'category' => 'automation',
                'cover_image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800&h=600&fit=crop',
            ],
            [
                'title' => 'Máy CNC 5 trục gia công khuôn mẫu phức tạp',
                'description' => 'Thiết kế và chế tạo máy CNC 5 trục chuyên dụng cho gia công khuôn mẫu nhựa và kim loại. Máy có khả năng gia công các chi tiết có hình dạng phức tạp với độ chính xác cao. Tích hợp hệ thống làm mát và thu gom phoi tự động.',
                'location' => 'Xưởng CNC Precision, TP.HCM',
                'usage' => 'Gia công khuôn mẫu, prototype',
                'floors' => 3,
                'category' => 'manufacturing',
                'cover_image' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?w=800&h=600&fit=crop',
            ],
            [
                'title' => 'Hệ thống AGV tự động trong kho hàng',
                'description' => 'Phát triển hệ thống AGV (Automated Guided Vehicle) thông minh cho vận chuyển hàng hóa trong kho. Sử dụng LIDAR và AI để navigation tự động, tích hợp với WMS để tối ưu hóa luồng vận chuyển. Có thể xử lý tải trọng 500kg/xe.',
                'location' => 'Kho logistics XYZ, Bình Dương',
                'usage' => 'Logistics, kho bãi tự động',
                'floors' => 5,
                'category' => 'robotics',
                'cover_image' => 'https://images.unsplash.com/photo-1581092353792-4a5b65d4ea40?w=800&h=600&fit=crop',
            ],
            [
                'title' => 'Dây chuyền lắp ráp tự động động cơ ô tô',
                'description' => 'Thiết kế dây chuyền lắp ráp tự động cho động cơ ô tô với công suất 100 động cơ/ngày. Tích hợp robot lắp ráp, hệ thống kiểm tra chất lượng tự động và traceability hoàn chỉnh. Đảm bảo độ chính xác lắp ráp theo tiêu chuẩn ISO.',
                'location' => 'Nhà máy ô tô DEF Motors, Hải Phòng',
                'usage' => 'Sản xuất động cơ ô tô',
                'floors' => 5,
                'category' => 'automation',
                'cover_image' => 'https://images.unsplash.com/photo-1581093458791-9f3c3250e675?w=800&h=600&fit=crop',
            ],
            [
                'title' => 'Máy in 3D kim loại công nghiệp quy mô lớn',
                'description' => 'Phát triển máy in 3D kim loại sử dụng công nghệ SLM (Selective Laser Melting) cho sản xuất các chi tiết hàng không vũ trụ. Khả năng in các hợp kim titanium và inconel với độ chính xác ±25μm. Tích hợp hệ thống kiểm soát khí quyển và post-processing.',
                'location' => 'Trung tâm R&D Aerospace, Hà Nội',
                'usage' => 'Hàng không vũ trụ, y tế',
                'floors' => 4,
                'category' => 'manufacturing',
                'cover_image' => 'https://images.unsplash.com/photo-1581093588401-fbb62a02f120?w=800&h=600&fit=crop',
            ],
        ];

        $createdCount = 0;
        foreach ($showcases as $index => $showcaseData) {
            $user = $users->random();

            try {
                $slug = Str::slug($showcaseData['title']);
                $existingCount = Showcase::where('slug', 'like', $slug . '%')->count();
                if ($existingCount > 0) {
                    $slug = $slug . '-' . ($existingCount + 1);
                }

                $showcase = Showcase::create([
                    'user_id' => $user->id,
                    'title' => $showcaseData['title'],
                    'slug' => $slug,
                    'description' => $showcaseData['description'],
                    'location' => $showcaseData['location'],
                    'usage' => $showcaseData['usage'],
                    'floors' => $showcaseData['floors'],
                    'category' => $showcaseData['category'],
                    'cover_image' => $showcaseData['cover_image'],
                    'status' => 'approved',
                    'order' => $index + 1,
                    // Không set showcaseable_id và showcaseable_type => showcase độc lập
                ]);

                if ($showcase) {
                    echo "✅ Independent Showcase #{$showcase->id}: {$showcase->title} by {$user->name}\n";
                    $createdCount++;
                }
            } catch (\Exception $e) {
                echo "⚠️ Không thể tạo showcase '{$showcaseData['title']}': " . $e->getMessage() . "\n";
                continue;
            }
        }

        echo "🎉 Hoàn thành tạo {$createdCount} independent showcases!\n";
    }
}
