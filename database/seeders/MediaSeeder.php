<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Showcase;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $threads = Thread::all();
        $posts = Post::all();
        $showcases = Showcase::all();
        $comments = Comment::all();
        $categories = Category::all();
        $forums = Forum::all();

        // Hình ảnh liên quan đến cơ khí và tự động hóa từ internet
        $mechanicalImages = [
            // Robot công nghiệp
            [
                'file_name' => 'industrial_robot_arm.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Robot Công Nghiệp ABB',
                'description' => 'Robot cánh tay 6 trục ABB IRB 6700 được sử dụng trong dây chuyền sản xuất ô tô',
            ],
            [
                'file_name' => 'welding_robot.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Robot Hàn Tự Động',
                'description' => 'Hệ thống robot hàn tự động KUKA trong nhà máy sản xuất thép',
            ],
            [
                'file_name' => 'assembly_line_robot.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Dây Chuyền Lắp Ráp Robot',
                'description' => 'Hệ thống robot Fanuc trong dây chuyền lắp ráp điện tử',
            ],

            // Máy CNC
            [
                'file_name' => 'cnc_machine.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Máy CNC DMG Mori',
                'description' => 'Máy phráy CNC 5 trục DMG Mori NTX 2000 chính xác cao',
            ],
            [
                'file_name' => 'cnc_milling.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092353792-4a5b65d4ea40?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Máy Phay CNC Vertical',
                'description' => 'Máy phay CNC đứng Haas VF-2 gia công chi tiết chính xác',
            ],

            // Hệ thống băng tải
            [
                'file_name' => 'conveyor_system.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Hệ Thống Băng Tải Tự Động',
                'description' => 'Băng tải tự động trong kho logistics với hệ thống WMS',
            ],
            [
                'file_name' => 'belt_conveyor.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Băng Tải Cao Su',
                'description' => 'Hệ thống băng tải cao su chống nhiệt trong nhà máy xi măng',
            ],

            // PLC và điều khiển
            [
                'file_name' => 'plc_panel.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Tủ Điều Khiển PLC Siemens',
                'description' => 'Tủ điều khiển PLC S7-1500 với HMI 15 inch cho dây chuyền sản xuất',
            ],
            [
                'file_name' => 'control_system.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Hệ Thống Điều Khiển SCADA',
                'description' => 'Giao diện SCADA giám sát và điều khiển nhà máy từ xa',
            ],

            // Sensor và IoT
            [
                'file_name' => 'industrial_sensors.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092786450-24ff4b2b0edd?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Sensor Công Nghiệp IoT',
                'description' => 'Bộ sensor nhiệt độ, áp suất, rung động kết nối IoT',
            ],
            [
                'file_name' => 'pressure_sensor.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Cảm Biến Áp Suất',
                'description' => 'Cảm biến áp suất Endress+Hauser cho hệ thống thủy lực',
            ],

            // Máy móc công nghiệp
            [
                'file_name' => 'industrial_machinery.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Máy Móc Công Nghiệp',
                'description' => 'Dây chuyền sản xuất tự động trong ngành dệt may',
            ],
            [
                'file_name' => 'hydraulic_press.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092353792-4a5b65d4ea40?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Máy Ép Thủy Lực',
                'description' => 'Máy ép thủy lực 500 tấn cho ngành ô tô',
            ],

            // CAD/CAM Design
            [
                'file_name' => 'cad_design.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Thiết Kế CAD 3D',
                'description' => 'Mô hình 3D SolidWorks của hệ thống truyền động bánh răng',
            ],
            [
                'file_name' => 'engineering_drawing.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Bản Vẽ Kỹ Thuật',
                'description' => 'Bản vẽ kỹ thuật AutoCAD chi tiết máy nén khí',
            ],

            // Automation Systems
            [
                'file_name' => 'smart_factory.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Nhà Máy Thông Minh',
                'description' => 'Hệ thống Industry 4.0 với AI và Big Data trong sản xuất',
            ],
            [
                'file_name' => 'automated_warehouse.jpg',
                'file_path' => 'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?w=800',
                'file_type' => 'image/jpeg',
                'title' => 'Kho Tự Động AGV',
                'description' => 'Hệ thống kho tự động với robot AGV và WMS',
            ]
        ];

        // Tạo media cho threads - đảm bảo TẤT CẢ threads có 1-3 ảnh
        if ($threads->count() > 0) {
            foreach ($threads as $thread) {
                $numImages = rand(1, 3); // Mỗi thread có 1-3 ảnh

                for ($i = 0; $i < $numImages; $i++) {
                    $imageIndex = ($thread->id + $i) % count($mechanicalImages);
                    $imageData = $mechanicalImages[$imageIndex];

                    Media::create([
                        'user_id' => $thread->user_id,
                        'file_name' => ($i === 0 ? '[Featured] ' : '') . 'thread_' . $thread->id . '_' . ($i + 1) . '_' . $imageData['file_name'],
                        'file_path' => $imageData['file_path'],
                        'file_type' => $imageData['file_type'],
                        'file_size' => rand(500000, 2000000), // 500KB - 2MB
                        'title' => ($i === 0 ? '[Featured] ' : '') . $imageData['title'],
                        'description' => $imageData['description'] . ($i === 0 ? ' (Ảnh đại diện)' : ''),
                        'mediable_id' => $thread->id,
                        'mediable_type' => Thread::class,
                        'thread_id' => $thread->id, // Để compatibility với existing structure
                    ]);
                }
            }
        }

        // Tạo media cho posts
        if ($posts->count() > 0) {
            foreach ($posts->take(20) as $index => $post) {
                $imageData = $mechanicalImages[$index % count($mechanicalImages)];
                Media::create([
                    'user_id' => $post->user_id,
                    'file_name' => 'post_' . $imageData['file_name'],
                    'file_path' => $imageData['file_path'],
                    'file_type' => $imageData['file_type'],
                    'file_size' => rand(300000, 1500000), // 300KB - 1.5MB
                    'title' => 'Post: ' . $imageData['title'],
                    'description' => $imageData['description'],
                    'mediable_id' => $post->id,
                    'mediable_type' => Post::class,
                ]);
            }
        }

        // Tạo media cho showcases - đảm bảo TẤT CẢ showcases có 1-3 ảnh
        if ($showcases->count() > 0) {
            foreach ($showcases as $showcase) {
                $numImages = rand(1, 3); // Mỗi showcase có 1-3 ảnh

                for ($i = 0; $i < $numImages; $i++) {
                    $imageIndex = ($showcase->id + $i + 10) % count($mechanicalImages);
                    $imageData = $mechanicalImages[$imageIndex];

                    Media::create([
                        'user_id' => $showcase->user_id,
                        'file_name' => ($i === 0 ? '[Featured] ' : '') . 'showcase_' . $showcase->id . '_' . ($i + 1) . '_' . $imageData['file_name'],
                        'file_path' => $imageData['file_path'],
                        'file_type' => $imageData['file_type'],
                        'file_size' => rand(800000, 3000000), // 800KB - 3MB
                        'title' => 'Showcase: ' . $imageData['title'],
                        'description' => $imageData['description'] . ($i === 0 ? ' (Ảnh đại diện showcase)' : ''),
                        'mediable_id' => $showcase->id,
                        'mediable_type' => Showcase::class,
                    ]);
                }
            }
        }

        // Tạo media cho comments
        if ($comments->count() > 0) {
            foreach ($comments->take(8) as $index => $comment) {
                $imageData = $mechanicalImages[$index % count($mechanicalImages)];
                Media::create([
                    'user_id' => $comment->user_id,
                    'file_name' => 'comment_' . $imageData['file_name'],
                    'file_path' => $imageData['file_path'],
                    'file_type' => $imageData['file_type'],
                    'file_size' => rand(200000, 1000000), // 200KB - 1MB
                    'title' => 'Comment: ' . $imageData['title'],
                    'description' => $imageData['description'],
                    'mediable_id' => $comment->id,
                    'mediable_type' => Comment::class,
                ]);
            }
        }

        // Tạo một số media độc lập không thuộc về object nào
        foreach (array_slice($mechanicalImages, 0, 5) as $imageData) {
            Media::create([
                'user_id' => $users->random()->id,
                'file_name' => 'standalone_' . $imageData['file_name'],
                'file_path' => $imageData['file_path'],
                'file_type' => $imageData['file_type'],
                'file_size' => rand(500000, 2000000),
                'title' => 'Library: ' . $imageData['title'],
                'description' => $imageData['description'],
                'mediable_id' => null,
                'mediable_type' => null,
            ]);
        }

        // Tạo representative images cho categories
        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $imageIndex = ($category->id + 5) % count($mechanicalImages);
                $imageData = $mechanicalImages[$imageIndex];

                Media::create([
                    'user_id' => $users->random()->id,
                    'file_name' => 'category_' . $category->id . '_' . $imageData['file_name'],
                    'file_path' => $imageData['file_path'],
                    'file_type' => $imageData['file_type'],
                    'file_size' => rand(400000, 1500000), // 400KB - 1.5MB
                    'title' => 'Category: ' . $category->name . ' - ' . $imageData['title'],
                    'description' => 'Ảnh đại diện cho danh mục ' . $category->name . '. ' . $imageData['description'],
                    'mediable_id' => $category->id,
                    'mediable_type' => Category::class,
                ]);
            }
        }

        // Tạo representative images cho forums
        if ($forums->count() > 0) {
            foreach ($forums as $forum) {
                $imageIndex = ($forum->id + 15) % count($mechanicalImages);
                $imageData = $mechanicalImages[$imageIndex];

                Media::create([
                    'user_id' => $users->random()->id,
                    'file_name' => 'forum_' . $forum->id . '_' . $imageData['file_name'],
                    'file_path' => $imageData['file_path'],
                    'file_type' => $imageData['file_type'],
                    'file_size' => rand(400000, 1500000), // 400KB - 1.5MB
                    'title' => 'Forum: ' . $forum->name . ' - ' . $imageData['title'],
                    'description' => 'Ảnh đại diện cho diễn đàn ' . $forum->name . '. ' . $imageData['description'],
                    'mediable_id' => $forum->id,
                    'mediable_type' => Forum::class,
                ]);
            }
        }
    }
}
