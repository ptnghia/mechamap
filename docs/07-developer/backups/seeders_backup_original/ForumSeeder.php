<?php

namespace Database\Seeders;

use App\Models\Forum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các chuyên mục chính
        $categories = [
            [
                'name' => 'Thảo luận chung',
                'description' => 'Thảo luận chung về các chủ đề cơ khí và tự động hóa',
            ],
            [
                'name' => 'Công nghệ',
                'description' => 'Thảo luận về công nghệ, thiết bị và phần mềm kỹ thuật',
            ],
            [
                'name' => 'Cộng đồng',
                'description' => 'Sự kiện cộng đồng, thông báo và phản hồi',
            ],
        ];

        foreach ($categories as $index => $category) {
            $forum = Forum::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'order' => $index,
            ]);

            // Tạo chuyên mục con cho mỗi chuyên mục chính
            if ($forum->name === 'Thảo luận chung') {
                $this->createSubforums($forum, [
                    [
                        'name' => 'Giới thiệu bản thân',
                        'description' => 'Giới thiệu bản thân với cộng đồng MechaMap',
                    ],
                    [
                        'name' => 'Chủ đề tự do',
                        'description' => 'Thảo luận các chủ đề không thuộc các chuyên mục khác',
                    ],
                    [
                        'name' => 'Tin tức & Thông báo',
                        'description' => 'Tin tức mới nhất và thông báo quan trọng',
                    ],
                ]);
            } elseif ($forum->name === 'Công nghệ') {
                $this->createSubforums($forum, [
                    [
                        'name' => 'Phần cứng',
                        'description' => 'Thảo luận về máy móc, thiết bị cơ khí và linh kiện',
                    ],
                    [
                        'name' => 'Phần mềm',
                        'description' => 'Thảo luận về phần mềm CAD, CAM, simulation và ứng dụng kỹ thuật',
                    ],
                    [
                        'name' => 'Lập trình',
                        'description' => 'Lập trình PLC, HMI, SCADA và các ngôn ngữ tự động hóa',
                    ],
                    [
                        'name' => 'IoT & Industry 4.0',
                        'description' => 'Internet of Things, sensors và công nghệ Industry 4.0',
                    ],
                ]);
            } elseif ($forum->name === 'Cộng đồng') {
                $this->createSubforums($forum, [
                    [
                        'name' => 'Sự kiện',
                        'description' => 'Sự kiện cộng đồng và buổi gặp gỡ kỹ sư',
                    ],
                    [
                        'name' => 'Phản hồi',
                        'description' => 'Đóng góp ý kiến về cộng đồng MechaMap',
                    ],
                    [
                        'name' => 'Hỗ trợ & Trợ giúp',
                        'description' => 'Nhận sự trợ giúp và hỗ trợ từ cộng đồng',
                    ],
                ]);
            }
        }
    }

    /**
     * Create subforums for a parent forum.
     */
    private function createSubforums(Forum $parent, array $subforums): void
    {
        foreach ($subforums as $index => $subforum) {
            Forum::create([
                'name' => $subforum['name'],
                'slug' => Str::slug($subforum['name']),
                'description' => $subforum['description'],
                'parent_id' => $parent->id,
                'order' => $index,
            ]);
        }
    }
}
