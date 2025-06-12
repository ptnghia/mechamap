<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MechanicalEngineeringDataSeeder extends Seeder
{
    /**
     * Seed the database with realistic mechanical engineering forum data.
     */
    public function run(): void
    {
        $this->command->info('🔧 Bắt đầu seed dữ liệu MechaMap forum...');

        // 1. Create mechanical engineering users
        $users = $this->createUsers();
        $this->command->info('✅ Đã tạo ' . count($users) . ' users kỹ thuật cơ khí');

        // 2. Create mechanical engineering tags
        $tags = $this->createTags();
        $this->command->info('✅ Đã tạo ' . count($tags) . ' tags chuyên ngành');

        // 3. Create realistic threads with proper distribution
        $threads = $this->createThreads($users, $tags);
        $this->command->info('✅ Đã tạo ' . count($threads) . ' threads kỹ thuật');

        // 4. Create realistic comments/discussions
        $comments = $this->createComments($users, $threads);
        $this->command->info('✅ Đã tạo ' . count($comments) . ' comments thảo luận');

        // 5. Update forum statistics
        $this->updateForumStatistics();
        $this->command->info('✅ Đã cập nhật thống kê forum');

        $this->command->info('🎉 Hoàn thành seed dữ liệu MechaMap!');
    }

    private function createUsers(): array
    {
        $mechanicalEngineers = [
            [
                'name' => 'Nguyễn Kỹ sư CAD',
                'email' => 'cad.engineer@mechamap.com',
                'username' => 'cad_engineer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Trần CNC Master',
                'email' => 'cnc.master@mechamap.com',
                'username' => 'cnc_master',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lê Robot Expert',
                'email' => 'robot.expert@mechamap.com',
                'username' => 'robot_expert',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Phạm Materials Pro',
                'email' => 'materials.pro@mechamap.com',
                'username' => 'materials_pro',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Võ Design Guru',
                'email' => 'design.guru@mechamap.com',
                'username' => 'design_guru',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        ];

        $users = [];
        foreach ($mechanicalEngineers as $userData) {
            // Check if user already exists
            $existingUser = User::where('username', $userData['username'])->first();
            if ($existingUser) {
                $users[] = $existingUser;
            } else {
                $users[] = User::create($userData);
            }
        }

        // Also include existing users
        $existingUsers = User::all();
        foreach ($existingUsers as $user) {
            if (!in_array($user->id, array_column($users, 'id'))) {
                $users[] = $user;
            }
        }

        return $users;
    }

    private function createTags(): array
    {
        $mechanicalTags = [
            // CAD/Design tags
            ['name' => 'SolidWorks', 'slug' => 'solidworks'],
            ['name' => 'AutoCAD', 'slug' => 'autocad'],
            ['name' => 'ANSYS', 'slug' => 'ansys'],
            ['name' => 'FEA Analysis', 'slug' => 'fea-analysis'],
            ['name' => 'CFD Simulation', 'slug' => 'cfd-simulation'],

            // Manufacturing tags
            ['name' => 'CNC Machining', 'slug' => 'cnc-machining'],
            ['name' => 'Precision Turning', 'slug' => 'precision-turning'],
            ['name' => '3D Printing', 'slug' => '3d-printing'],
            ['name' => 'Additive Manufacturing', 'slug' => 'additive-manufacturing'],
            ['name' => 'Surface Treatment', 'slug' => 'surface-treatment'],

            // Automation tags
            ['name' => 'PLC Programming', 'slug' => 'plc-programming'],
            ['name' => 'Industrial Robot', 'slug' => 'industrial-robot'],
            ['name' => 'IoT Integration', 'slug' => 'iot-integration'],
            ['name' => 'Industry 4.0', 'slug' => 'industry-40'],

            // Materials tags
            ['name' => 'Steel Alloys', 'slug' => 'steel-alloys'],
            ['name' => 'Aluminum', 'slug' => 'aluminum'],
            ['name' => 'Composite Materials', 'slug' => 'composite-materials'],
            ['name' => 'Heat Treatment', 'slug' => 'heat-treatment'],

            // General tags
            ['name' => 'Troubleshooting', 'slug' => 'troubleshooting'],
            ['name' => 'Best Practices', 'slug' => 'best-practices'],
        ];

        $tags = [];
        foreach ($mechanicalTags as $tagData) {
            // Check if tag already exists
            $existingTag = Tag::where('slug', $tagData['slug'])->first();
            if ($existingTag) {
                $tags[] = $existingTag;
            } else {
                $tags[] = Tag::create($tagData);
            }
        }

        return $tags;
    }

    private function createThreads(array $users, array $tags): array
    {
        $forums = Forum::all();
        $threads = [];

        // Realistic mechanical engineering thread scenarios
        $threadScenarios = [
            // CAD/Design Forums
            [
                'forum_name' => 'SolidWorks & AutoCAD',
                'threads' => [
                    [
                        'title' => 'Hướng dẫn thiết kế bánh răng trong SolidWorks',
                        'content' => 'Mình đang thiết kế hộp số cho dự án xe máy điện. Cần tư vấn về cách tạo bánh răng với module 2.5, số răng 24/36. Các bạn có thể chia sẻ workflow và best practices không?

Thông số kỹ thuật:
- Module: 2.5mm
- Góc áp lực: 20°
- Chiều rộng răng: 15mm
- Vật liệu: Thép 40Cr

Hiện tại mình đang gặp khó khăn trong việc tạo profile răng chính xác và kiểm tra can thiệp.',
                        'tags' => ['SolidWorks', 'Best Practices'],
                        'technical_difficulty' => 'intermediate',
                        'project_type' => 'design',
                        'requires_calculations' => true,
                    ],
                    [
                        'title' => 'Tối ưu hóa assembly lớn trong SolidWorks - Giảm lag',
                        'content' => 'Dự án hiện tại có assembly với 500+ parts, SolidWorks chạy rất chậm. Laptop Dell Precision 7750, RAM 32GB, Quadro RTX 3000.

Đã thử:
- Large Design Review mode
- Lightweight components
- Hide components không cần thiết

Vẫn bị lag khi zoom/rotate. Các cao thủ có tip gì để tối ưu không?',
                        'tags' => ['SolidWorks', 'Troubleshooting'],
                        'technical_difficulty' => 'advanced',
                        'project_type' => 'troubleshooting',
                        'requires_calculations' => false,
                    ],
                ]
            ],
            [
                'forum_name' => 'ANSYS & Simulation',
                'threads' => [
                    [
                        'title' => 'Phân tích ứng suất trục quay bằng ANSYS Mechanical',
                        'content' => 'Đang phân tích trục quay máy nén khí công suất 50HP. Trục đường kính 80mm, chiều dài 1.2m, vật liệu thép C45.

Điều kiện biên:
- Tốc độ quay: 1800 RPM
- Moment xoắn: 250 Nm
- Lực radial từ bánh răng: 5000N

Kết quả FEA cho ứng suất max 180 MPa, nhưng trong thực tế thấy có vết nứt. Có phải do fatigue loading chưa được tính đến?',
                        'tags' => ['ANSYS', 'FEA Analysis'],
                        'technical_difficulty' => 'expert',
                        'project_type' => 'analysis',
                        'requires_calculations' => true,
                    ],
                ]
            ],
            // Manufacturing Forums
            [
                'forum_name' => 'CNC & Gia công chính xác',
                'threads' => [
                    [
                        'title' => 'Chương trình CNC cho chi tiết phức tạp - Titan Grade 5',
                        'content' => 'Cần gia công chi tiết hàng không vũ trụ từ Ti-6Al-4V. Độ chính xác yêu cầu ±0.02mm, độ nhám Ra 0.8.

Thách thức:
- Vật liệu khó gia công (work hardening)
- Tolerance chặt chẽ
- Surface finish cao

Setup hiện tại:
- Máy: DMG DMU 50 (5-axis)
- Dao: Carbide uncoated
- Tốc độ cắt: 120 m/min
- Feed: 0.1 mm/răng

Gặp vấn đề tool wear nhanh và chất lượng bề mặt không đạt. Ai có kinh nghiệm gia công titanium xin chia sẻ!',
                        'tags' => ['CNC Machining', 'Precision Turning'],
                        'technical_difficulty' => 'expert',
                        'project_type' => 'manufacturing',
                        'requires_calculations' => true,
                    ],
                    [
                        'title' => 'So sánh thông số cắt cho thép không gỉ 316L',
                        'content' => 'Đang research thông số tối ưu cho SS316L trên máy CNC. Hiện tại đang dùng:

Thô:
- Vc: 200 m/min
- fz: 0.3 mm/tooth
- ap: 3mm
- ae: 0.6D

Tinh:
- Vc: 250 m/min
- fz: 0.15 mm/tooth
- ap: 0.5mm
- ae: 0.3D

Tool life khoảng 45 phút. Mọi người có setup nào tốt hơn không?',
                        'tags' => ['CNC Machining', 'Best Practices'],
                        'technical_difficulty' => 'intermediate',
                        'project_type' => 'manufacturing',
                        'requires_calculations' => true,
                    ],
                ]
            ],
            // Automation Forums
            [
                'forum_name' => 'PLC & HMI Programming',
                'threads' => [
                    [
                        'title' => 'Ladder Logic cho hệ thống conveyor tự động',
                        'content' => 'Thiết kế hệ thống băng tải tự động cho nhà máy sản xuất ô tô. Yêu cầu:

- 3 trạm làm việc nối tiếp
- Sensor phát hiện sản phẩm tại mỗi trạm
- Logic interlock an toàn
- Tích hợp với SCADA system

PLC: Siemens S7-1500
HMI: KTP700 Basic

Đã code được basic sequence, nhưng gặp khó khăn với safety interlock và error handling. Có ai share được sample code không?',
                        'tags' => ['PLC Programming', 'Industry 4.0'],
                        'technical_difficulty' => 'advanced',
                        'project_type' => 'design',
                        'requires_calculations' => false,
                    ],
                ]
            ],
            // Materials Forums
            [
                'forum_name' => 'Vật liệu kỹ thuật',
                'threads' => [
                    [
                        'title' => 'Lựa chọn vật liệu cho bánh răng hộp số ô tô',
                        'content' => 'Đang thiết kế hộp số 5 cấp cho xe sedan 1.6L. Yêu cầu vật liệu bánh răng:

Điều kiện làm việc:
- Moment max: 140 Nm
- Tốc độ: 0-6000 RPM
- Tuổi thọ: 200,000 km
- Nhiệt độ: -20°C đến 120°C

Đang cân nhắc:
1. Thép carburizing 20CrMnTi
2. Thép hợp kim 18CrNiMo7-6
3. Powder metal steel

Chi phí quan trọng nhưng chất lượng là ưu tiên. Mọi người có kinh nghiệm gì về heat treatment cho gears?',
                        'tags' => ['Steel Alloys', 'Heat Treatment'],
                        'technical_difficulty' => 'advanced',
                        'project_type' => 'design',
                        'requires_calculations' => true,
                    ],
                ]
            ],
        ];

        foreach ($threadScenarios as $forumData) {
            $forum = $forums->where('name', $forumData['forum_name'])->first();
            if (!$forum) continue;

            foreach ($forumData['threads'] as $threadData) {
                $user = $users[array_rand($users)];

                $thread = Thread::create([
                    'title' => $threadData['title'],
                    'slug' => Str::slug($threadData['title']),
                    'content' => $threadData['content'],
                    'user_id' => $user->id,
                    'forum_id' => $forum->id,
                    'category_id' => $forum->category_id,
                    'moderation_status' => 'approved',
                    'view_count' => rand(50, 500),
                    'technical_difficulty' => $threadData['technical_difficulty'],
                    'project_type' => $threadData['project_type'],
                    'requires_calculations' => $threadData['requires_calculations'],
                    'last_activity_at' => now()->subHours(rand(1, 48)),
                ]);

                // Attach tags
                $threadTags = [];
                foreach ($threadData['tags'] as $tagName) {
                    $tag = collect($tags)->where('name', $tagName)->first();
                    if ($tag) {
                        $threadTags[] = $tag->id;
                    }
                }
                if (!empty($threadTags)) {
                    $thread->tags()->attach($threadTags);
                }

                $threads[] = $thread;
            }
        }

        return $threads;
    }

    private function createComments(array $users, array $threads): array
    {
        $comments = [];

        // Realistic engineering responses
        $responseTemplates = [
            'Kinh nghiệm của mình với {topic} là {solution}. Bạn có thể thử {method} để {result}.',
            'Theo tiêu chuẩn {standard}, thông số này nên là {value}. Mình đã áp dụng thành công trong dự án {project}.',
            'Gặp vấn đề tương tự trong {context}. Giải pháp là {solution} và kết quả {outcome}.',
            'Có thể do {cause}. Bạn đã kiểm tra {check_items} chưa? Theo kinh nghiệm thì {advice}.',
            'Thanks bạn chia sẻ! Mình nghĩ {opinion} và có thể cải thiện bằng {improvement}.',
        ];

        $technicalTerms = [
            'topics' => ['SolidWorks assembly', 'CNC programming', 'FEA analysis', 'PLC logic', 'vật liệu composite'],
            'solutions' => ['tối ưu hóa workflow', 'điều chỉnh thông số', 'sử dụng feature khác', 'áp dụng best practice'],
            'methods' => ['Large Design Review', 'adaptive clearing', 'mesh refinement', 'safety interlock'],
            'results' => ['giảm lag đáng kể', 'tăng tool life', 'cải thiện độ chính xác', 'ổn định hơn'],
            'standards' => ['ISO 6336', 'ASME Y14.5', 'DIN 867', 'JIS B1702'],
            'projects' => ['hộp số ô tô', 'máy CNC 5 trục', 'robot công nghiệp', 'băng tải tự động'],
        ];

        foreach ($threads as $thread) {
            $numComments = rand(1, 8);

            for ($i = 0; $i < $numComments; $i++) {
                $user = $users[array_rand($users)];
                $template = $responseTemplates[array_rand($responseTemplates)];

                // Generate realistic technical content
                $content = str_replace(
                    ['{topic}', '{solution}', '{method}', '{result}', '{standard}', '{value}', '{project}', '{context}', '{outcome}', '{cause}', '{check_items}', '{advice}', '{opinion}', '{improvement}'],
                    [
                        $technicalTerms['topics'][array_rand($technicalTerms['topics'])],
                        $technicalTerms['solutions'][array_rand($technicalTerms['solutions'])],
                        $technicalTerms['methods'][array_rand($technicalTerms['methods'])],
                        $technicalTerms['results'][array_rand($technicalTerms['results'])],
                        $technicalTerms['standards'][array_rand($technicalTerms['standards'])],
                        rand(1, 999) . (rand(0,1) ? 'mm' : 'MPa'),
                        $technicalTerms['projects'][array_rand($technicalTerms['projects'])],
                        $technicalTerms['topics'][array_rand($technicalTerms['topics'])],
                        $technicalTerms['results'][array_rand($technicalTerms['results'])],
                        'thiết lập ' . $technicalTerms['methods'][array_rand($technicalTerms['methods'])],
                        'thông số cutting speed và feed rate',
                        $technicalTerms['solutions'][array_rand($technicalTerms['solutions'])],
                        'approach này khá hiệu quả',
                        $technicalTerms['solutions'][array_rand($technicalTerms['solutions'])],
                    ],
                    $template
                );

                $comment = Comment::create([
                    'content' => $content,
                    'user_id' => $user->id,
                    'thread_id' => $thread->id,
                    'created_at' => $thread->created_at->addMinutes(rand(30, 2880)), // 30 min to 48 hours after thread
                ]);

                $comments[] = $comment;
            }
        }

        return $comments;
    }

    private function updateForumStatistics(): void
    {
        $forums = Forum::all();

        foreach ($forums as $forum) {
            $threadCount = $forum->threads()->count();
            $postCount = Comment::whereIn('thread_id', $forum->threads()->pluck('id'))->count();

            $forum->update([
                'thread_count' => $threadCount,
                'post_count' => $postCount,
                'last_activity_at' => $forum->threads()->max('last_activity_at') ?? now(),
            ]);
        }
    }
}
