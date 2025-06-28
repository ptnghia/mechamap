<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MechaMapUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * MechaMap User Management Restructure - Phase 1
     * Tạo user theo cấu trúc mới với 4 nhóm chính:
     *
     * 🔧 SYSTEM MANAGEMENT:
     * - Super Admin: Toàn quyền hệ thống (1-2 users)
     * - System Admin: Quản lý hệ thống & users (2-3 users)
     * - Content Admin: Quản lý nội dung & forum (2-3 users)
     *
     * 👥 COMMUNITY MANAGEMENT:
     * - Content Moderator: Kiểm duyệt nội dung (3-5 users)
     * - Marketplace Moderator: Quản lý marketplace (2-3 users)
     * - Community Moderator: Quản lý cộng đồng (2-3 users)
     *
     * 🌟 COMMUNITY MEMBERS:
     * - Senior Member: Thành viên cao cấp (5-10 users)
     * - Member: Thành viên cơ bản (20-30 users)
     * - Guest: Khách tham quan (5-10 users)
     * - Student: Sinh viên (10-15 users)
     *
     * 🏢 BUSINESS PARTNERS:
     * - Manufacturer: Nhà sản xuất (3-5 users)
     * - Supplier: Nhà cung cấp (4-6 users)
     * - Brand: Nhãn hàng (3-5 users)
     * - Verified Partner: Đối tác chiến lược (2-3 users)
     */
    public function run(): void
    {
        // ====================================================================
        // 🔧 SYSTEM MANAGEMENT - Quản lý hệ thống cấp cao
        // ====================================================================

        // 👑 SUPER ADMIN - Toàn quyền hệ thống
        $superAdminUsers = [
            [
                'name' => 'Nguyễn Quản Trị',
                'username' => 'superadmin',
                'email' => 'superadmin@mechamap.vn',
                'password' => Hash::make('SuperAdmin2024@'),
                'role' => 'super_admin',
                'avatar' => '/images/avatars/super_admin.jpg',
                'about_me' => 'Super Administrator MechaMap - Founder & CEO với 20+ năm kinh nghiệm trong ngành cơ khí và công nghệ. Chịu trách nhiệm chiến lược phát triển toàn diện.',
                'website' => 'https://mechamap.vn',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Building the future of Vietnamese mechanical engineering community �',
                'points' => 15000,
                'reaction_score' => 800,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now(),
                'setup_progress' => 100,
            ]
        ];

        // 🛡️ SYSTEM ADMIN - Quản lý hệ thống & users
        $systemAdminUsers = [
            [
                'name' => 'Trần Hệ Thống',
                'username' => 'sysadmin',
                'email' => 'sysadmin@mechamap.vn',
                'password' => Hash::make('SysAdmin2024@'),
                'role' => 'system_admin',
                'avatar' => '/images/avatars/system_admin1.jpg',
                'about_me' => 'System Administrator - Chuyên trách bảo trì, phát triển hệ thống kỹ thuật và quản lý infrastructure. Master DevOps & Cloud Architecture.',
                'website' => 'https://sysadmin.mechamap.vn',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'System reliability & performance optimization specialist 🔧',
                'points' => 12000,
                'reaction_score' => 600,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(30),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Lê Database Master',
                'username' => 'dbadmin',
                'email' => 'database@mechamap.vn',
                'password' => Hash::make('DbAdmin2024@'),
                'role' => 'system_admin',
                'avatar' => '/images/avatars/system_admin2.jpg',
                'about_me' => 'Database Administrator & Data Architect - Chuyên gia về database optimization, backup/recovery và data security. 10+ năm kinh nghiệm với enterprise systems.',
                'location' => 'Đà Nẵng, Việt Nam',
                'signature' => 'Data integrity & system performance guardian 🛡️',
                'points' => 10500,
                'reaction_score' => 520,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHour(),
                'setup_progress' => 100,
            ]
        ];

        // 🎯 CONTENT ADMIN - Quản lý nội dung & forum
        $contentAdminUsers = [
            [
                'name' => 'Phạm Content Master',
                'username' => 'contentadmin',
                'email' => 'content@mechamap.vn',
                'password' => Hash::make('ContentAdmin2024@'),
                'role' => 'content_admin',
                'avatar' => '/images/avatars/content_admin1.jpg',
                'about_me' => 'Content Administrator - Chuyên gia quản lý nội dung kỹ thuật và forum community. Kỹ sư Cơ khí với chuyên môn về technical writing và content strategy.',
                'website' => 'https://content.mechamap.vn',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Quality content drives community growth 📝',
                'points' => 9500,
                'reaction_score' => 480,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(15),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Võ Forum Expert',
                'username' => 'forumadmin',
                'email' => 'forum@mechamap.vn',
                'password' => Hash::make('ForumAdmin2024@'),
                'role' => 'content_admin',
                'avatar' => '/images/avatars/content_admin2.jpg',
                'about_me' => 'Forum Administrator - Chuyên gia quản lý diễn đàn kỹ thuật và community engagement. Background về Mechanical Engineering và Community Management.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Fostering technical discussions & knowledge sharing 💬',
                'points' => 8800,
                'reaction_score' => 420,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(45),
                'setup_progress' => 100,
            ]
        ];

        // Combine all admin users
        $allAdminUsers = array_merge($superAdminUsers, $systemAdminUsers, $contentAdminUsers);

        foreach ($allAdminUsers as $userData) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );

                // Assign Spatie role
                if ($user && !$user->hasRole($userData['role'])) {
                    $user->assignRole($userData['role']);
                }

                echo "✅ Created admin: {$user->name} with role: {$userData['role']}\n";
            } catch (\Exception $e) {
                echo "❌ Failed to create admin: {$userData['name']} - " . $e->getMessage() . "\n";
            }
        }

        // ====================================================================
        // MODERATOR USERS - Điều hành viên
        // ====================================================================

        $moderatorUsers = [
            [
                'name' => 'Lê Kiểm Duyệt',
                'username' => 'moderator',
                'email' => 'moderator@mechamap.vn',
                'password' => Hash::make('ModeratorMecha2024@'),
                'role' => 'content_moderator',
                'avatar' => '/images/avatars/moderator1.jpg',
                'about_me' => 'Moderator chuyên nghiệp - Kỹ sư Cơ khí chuyên ngành CAD/CAM với 10 năm kinh nghiệm. Đảm bảo chất lượng nội dung và hỗ trợ cộng đồng.',
                'website' => 'https://cadcam-expert.com',
                'location' => 'Đà Nẵng, Việt Nam',
                'signature' => 'Quality content for better community 📝',
                'points' => 5000,
                'reaction_score' => 380,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(30),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Phạm Nội Dung',
                'username' => 'content_mod',
                'email' => 'content@mechamap.vn',
                'password' => Hash::make('ContentMod2024@'),
                'role' => 'marketplace_moderator',
                'avatar' => '/images/avatars/moderator2.jpg',
                'about_me' => 'Content Moderator - Chuyên gia về vật liệu kỹ thuật và quy trình sản xuất. Hỗ trợ review và kiểm duyệt nội dung chuyên môn.',
                'location' => 'Hải Phòng, Việt Nam',
                'signature' => 'Technical accuracy & community guidelines',
                'points' => 4200,
                'reaction_score' => 310,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(2),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Ngô Quản Lý',
                'username' => 'ngo_manager',
                'email' => 'manager@mechamap.vn',
                'password' => Hash::make('Manager2024@'),
                'role' => 'community_moderator',
                'avatar' => '/images/avatars/moderator3.jpg',
                'about_me' => 'Community Manager - Chuyên gia quản lý cộng đồng với background về Mechanical Engineering. Đảm bảo môi trường thảo luận tích cực và chuyên nghiệp.',
                'website' => 'https://community-expert.vn',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Building stronger engineering community 🤝',
                'points' => 4800,
                'reaction_score' => 350,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(45),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Sarah Wilson',
                'username' => 'sarah_moderator',
                'email' => 'sarah.wilson@mechamap.vn',
                'password' => Hash::make('SarahMod2024@'),
                'role' => 'content_moderator',
                'avatar' => '/images/avatars/moderator4.jpg',
                'about_me' => 'International Moderator - Mechanical Engineer from Australia working in Vietnam. Helps bridge international engineering standards and practices.',
                'website' => 'https://intl-engineering.com',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Global engineering perspectives 🌏',
                'points' => 3900,
                'reaction_score' => 280,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(1),
                'setup_progress' => 100,
            ]
        ];

        foreach ($moderatorUsers as $userData) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );

                // Assign Spatie role
                if ($user && !$user->hasRole($userData['role'])) {
                    $user->assignRole($userData['role']);
                }

                echo "✅ Created moderator: {$user->name} with role: {$userData['role']}\n";
            } catch (\Exception $e) {
                echo "❌ Failed to create moderator: {$userData['name']} - " . $e->getMessage() . "\n";
            }
        }

        // ====================================================================
        // SENIOR USERS - Thành viên cao cấp
        // ====================================================================

        $seniorUsers = [
            [
                'name' => 'Hoàng Cơ Khí',
                'username' => 'hoang_engineer',
                'email' => 'hoang.engineer@gmail.com',
                'password' => Hash::make('SeniorMecha2024@'),
                'role' => 'senior_member',
                'avatar' => '/images/avatars/senior1.jpg',
                'about_me' => 'Senior Mechanical Engineer tại Samsung Vietnam. Chuyên gia thiết kế sản phẩm điện tử với 8+ năm kinh nghiệm. Passionate về automation và Industry 4.0.',
                'website' => 'https://linkedin.com/in/hoang-engineer',
                'location' => 'Bắc Ninh, Việt Nam',
                'signature' => 'Innovation through precision engineering ⚙️',
                'points' => 3500,
                'reaction_score' => 240,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(15),
                'setup_progress' => 95,
            ],
            [
                'name' => 'Đỗ Tự Động',
                'username' => 'do_automation',
                'email' => 'do.automation@company.vn',
                'password' => Hash::make('AutoMecha2024@'),
                'role' => 'senior_member',
                'about_me' => 'Automation Engineer chuyên PLC/SCADA. Lead Engineer tại một công ty OEM hàng đầu. Có kinh nghiệm triển khai hơn 50 dự án tự động hóa.',
                'website' => 'https://automation-blog.vn',
                'location' => 'Bình Dương, Việt Nam',
                'signature' => 'Automate everything! 🤖',
                'points' => 3200,
                'reaction_score' => 195,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHour(),
                'setup_progress' => 90,
            ],
            [
                'name' => 'Vũ CAD Master',
                'username' => 'vu_cadmaster',
                'email' => 'vu.cad@designfirm.vn',
                'password' => Hash::make('CADMaster2024@'),
                'role' => 'senior_member',
                'about_me' => 'Senior CAD/CAM Specialist. Master trong SolidWorks, Fusion 360, và CNC programming. Trainer chính thức của SolidWorks Vietnam.',
                'website' => 'https://cadtraining.vn',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Design with precision, manufacture with excellence 📐',
                'points' => 4100,
                'reaction_score' => 320,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(45),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Lý Vật Liệu',
                'username' => 'ly_materials',
                'email' => 'ly.materials@research.edu.vn',
                'password' => Hash::make('Materials2024@'),
                'role' => 'senior_member',
                'avatar' => '/images/avatars/senior4.jpg',
                'about_me' => 'Materials Engineer & Researcher tại ĐH Bách Khoa TP.HCM. PhD in Materials Science. Chuyên gia về composite và smart materials.',
                'website' => 'https://materials-research.edu.vn',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Advanced materials for future technology 🧪',
                'points' => 2800,
                'reaction_score' => 180,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(3),
                'setup_progress' => 85,
            ],
            [
                'name' => 'Michael Chen',
                'username' => 'michael_robotics',
                'email' => 'michael.chen@robotics.vn',
                'password' => Hash::make('Robotics2024@'),
                'role' => 'senior_member',
                'avatar' => '/images/avatars/senior5.jpg',
                'about_me' => 'Robotics Engineer - Chuyên gia về robot công nghiệp và AI. Lead Engineer tại công ty robotics hàng đầu Việt Nam.',
                'website' => 'https://robotics-vietnam.com',
                'location' => 'Bình Dương, Việt Nam',
                'signature' => 'Robots are the future of manufacturing 🤖',
                'points' => 3800,
                'reaction_score' => 290,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(20),
                'setup_progress' => 95,
            ],
            [
                'name' => 'Trương Thiết Kế',
                'username' => 'truong_design',
                'email' => 'truong.design@consultant.vn',
                'password' => Hash::make('Design2024@'),
                'role' => 'senior_member',
                'avatar' => '/images/avatars/senior6.jpg',
                'about_me' => 'Senior Design Consultant - 12 năm kinh nghiệm thiết kế sản phẩm công nghiệp. Chuyên gia về Design for Manufacturing (DFM).',
                'website' => 'https://design-consultant.vn',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Good design is good business 💡',
                'points' => 3300,
                'reaction_score' => 220,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(2),
                'setup_progress' => 90,
            ]
        ];

        foreach ($seniorUsers as $userData) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
                echo "✅ Created senior: {$user->name}\n";
            } catch (\Exception $e) {
                echo "❌ Failed to create senior: {$userData['name']} - " . $e->getMessage() . "\n";
            }
        }

        // ====================================================================
        // MEMBER USERS - Thành viên cơ bản
        // ====================================================================

        $memberUsers = [
            [
                'name' => 'Nguyễn Học Viên',
                'username' => 'nguyen_student',
                'email' => 'nguyen.student@university.edu.vn',
                'password' => Hash::make('Student2024@'),
                'role' => 'student',
                'avatar' => '/images/avatars/member1.jpg',
                'about_me' => 'Sinh viên năm 4 ngành Cơ khí tại ĐH Bách Khoa Hà Nội. Đang tìm hiểu về thiết kế máy và automation. Mong muốn học hỏi từ các anh chị trong ngành.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Learning every day to become better engineer 📚',
                'points' => 150,
                'reaction_score' => 25,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(4),
                'setup_progress' => 60,
            ],
            [
                'name' => 'Trần Fresher',
                'username' => 'tran_fresher',
                'email' => 'tran.fresher@gmail.com',
                'password' => Hash::make('Fresher2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member2.jpg',
                'about_me' => 'Fresh Graduate Engineer vừa ra trường. Đang tìm việc trong lĩnh vực thiết kế cơ khí. Có kinh nghiệm thực tập tại công ty sản xuất auto parts.',
                'location' => 'Đồng Nai, Việt Nam',
                'signature' => 'Ready to contribute to engineering world! 🚀',
                'points' => 80,
                'reaction_score' => 12,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(6),
                'setup_progress' => 45,
            ],
            [
                'name' => 'Lê Kỹ Thuật',
                'username' => 'le_technician',
                'email' => 'le.technician@workshop.vn',
                'password' => Hash::make('Technician2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member3.jpg',
                'about_me' => 'Kỹ thuật viên CNC với 3 năm kinh nghiệm. Chuyên gia công chi tiết chính xác và setup máy. Muốn nâng cao kiến thức về programming và troubleshooting.',
                'location' => 'Bình Dương, Việt Nam',
                'signature' => 'Precision machining specialist 🔩',
                'points' => 320,
                'reaction_score' => 45,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subDays(1),
                'setup_progress' => 70,
            ],
            [
                'name' => 'Phạm Tìm Hiểu',
                'username' => 'pham_curious',
                'email' => 'pham.curious@engineer.com',
                'password' => Hash::make('Curious2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member4.jpg',
                'about_me' => 'Junior Engineer yêu thích tìm tòi công nghệ mới. Đang học thêm về IoT và Smart Manufacturing. Thích chia sẻ và học hỏi kinh nghiệm từ cộng đồng.',
                'location' => 'Cần Thơ, Việt Nam',
                'signature' => 'Curiosity drives innovation 💡',
                'points' => 180,
                'reaction_score' => 28,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(8),
                'setup_progress' => 55,
            ],
            [
                'name' => 'Bùi Thực Tập',
                'username' => 'bui_intern',
                'email' => 'bui.intern@company.vn',
                'password' => Hash::make('Intern2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member5.jpg',
                'about_me' => 'Intern Engineer tại công ty sản xuất thiết bị y tế. Đang học về quality control và lean manufacturing. Mong muốn phát triển career trong ngành cơ khí chính xác.',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Every expert was once a beginner 🌱',
                'points' => 90,
                'reaction_score' => 15,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subDays(2),
                'setup_progress' => 40,
            ],
            [
                'name' => 'Nguyễn Công Nghệ',
                'username' => 'nguyen_tech',
                'email' => 'nguyen.tech@factory.vn',
                'password' => Hash::make('Tech2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member6.jpg',
                'about_me' => 'Kỹ thuật viên bảo trì máy móc tại nhà máy dệt may. Chuyên về troubleshooting và preventive maintenance. Muốn học thêm về predictive maintenance.',
                'location' => 'Nam Định, Việt Nam',
                'signature' => 'Keep machines running smoothly ⚙️',
                'points' => 220,
                'reaction_score' => 35,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(12),
                'setup_progress' => 65,
            ],
            [
                'name' => 'Emma Johnson',
                'username' => 'emma_engineer',
                'email' => 'emma.johnson@international.com',
                'password' => Hash::make('Emma2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member7.jpg',
                'about_me' => 'Mechanical Engineer from UK working in Vietnam. Interested in sustainable manufacturing and green technology solutions.',
                'location' => 'Đà Nẵng, Việt Nam',
                'signature' => 'Engineering for a sustainable future 🌱',
                'points' => 280,
                'reaction_score' => 42,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(5),
                'setup_progress' => 75,
            ],
            [
                'name' => 'Đặng Sản Xuất',
                'username' => 'dang_production',
                'email' => 'dang.production@manufacturing.vn',
                'password' => Hash::make('Production2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member8.jpg',
                'about_me' => 'Production Engineer tại công ty sản xuất linh kiện điện tử. Chuyên về process optimization và lean manufacturing.',
                'location' => 'Bắc Giang, Việt Nam',
                'signature' => 'Optimize processes, maximize efficiency 📈',
                'points' => 350,
                'reaction_score' => 58,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(3),
                'setup_progress' => 80,
            ],
            [
                'name' => 'Võ Chất Lượng',
                'username' => 'vo_quality',
                'email' => 'vo.quality@qc.vn',
                'password' => Hash::make('Quality2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member9.jpg',
                'about_me' => 'Quality Control Engineer với 4 năm kinh nghiệm. Chuyên về ISO standards và quality management systems.',
                'location' => 'Hải Dương, Việt Nam',
                'signature' => 'Quality is not an act, it is a habit ✅',
                'points' => 420,
                'reaction_score' => 68,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(1),
                'setup_progress' => 85,
            ],
            [
                'name' => 'Lê Nghiên Cứu',
                'username' => 'le_research',
                'email' => 'le.research@university.edu.vn',
                'password' => Hash::make('Research2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member10.jpg',
                'about_me' => 'Nghiên cứu sinh ngành Cơ khí tại ĐH Bách Khoa Hà Nội. Đang nghiên cứu về additive manufacturing và 3D printing.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Research today, innovate tomorrow 🔬',
                'points' => 190,
                'reaction_score' => 28,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(6),
                'setup_progress' => 70,
            ],
            [
                'name' => 'James Smith',
                'username' => 'james_expat',
                'email' => 'james.smith@expat.com',
                'password' => Hash::make('James2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member11.jpg',
                'about_me' => 'Expat Engineer from Canada working in Vietnam manufacturing sector. Passionate about technology transfer and knowledge sharing.',
                'location' => 'Vũng Tàu, Việt Nam',
                'signature' => 'Bridging global engineering practices 🌍',
                'points' => 310,
                'reaction_score' => 48,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(4),
                'setup_progress' => 78,
            ],
            [
                'name' => 'Hoàng Năng Lượng',
                'username' => 'hoang_energy',
                'email' => 'hoang.energy@renewable.vn',
                'password' => Hash::make('Energy2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member12.jpg',
                'about_me' => 'Kỹ sư năng lượng tái tạo. Chuyên về thiết kế và lắp đặt hệ thống solar và wind power. Quan tâm đến sustainable engineering.',
                'location' => 'Ninh Thuận, Việt Nam',
                'signature' => 'Clean energy for a better future ☀️',
                'points' => 260,
                'reaction_score' => 38,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(8),
                'setup_progress' => 72,
            ],
            [
                'name' => 'Phan Ô Tô',
                'username' => 'phan_automotive',
                'email' => 'phan.automotive@car.vn',
                'password' => Hash::make('Auto2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member13.jpg',
                'about_me' => 'Automotive Engineer tại công ty lắp ráp ô tô. Chuyên về engine systems và vehicle dynamics. Đam mê xe điện và autonomous vehicles.',
                'location' => 'Quảng Nam, Việt Nam',
                'signature' => 'Driving the future of mobility 🚗',
                'points' => 380,
                'reaction_score' => 55,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(2),
                'setup_progress' => 82,
            ],
            [
                'name' => 'Ngô Hàng Không',
                'username' => 'ngo_aerospace',
                'email' => 'ngo.aerospace@aviation.vn',
                'password' => Hash::make('Aerospace2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member14.jpg',
                'about_me' => 'Aerospace Engineer làm việc tại ngành hàng không Việt Nam. Chuyên về aircraft maintenance và aviation safety systems.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Safety first in aviation ✈️',
                'points' => 290,
                'reaction_score' => 44,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(7),
                'setup_progress' => 76,
            ],
            [
                'name' => 'Trần Hàng Hải',
                'username' => 'tran_marine',
                'email' => 'tran.marine@shipyard.vn',
                'password' => Hash::make('Marine2024@'),
                'role' => 'member',
                'avatar' => '/images/avatars/member15.jpg',
                'about_me' => 'Marine Engineer tại xưởng đóng tàu. Chuyên về ship design và marine propulsion systems. Quan tâm đến green shipping technology.',
                'location' => 'Hải Phòng, Việt Nam',
                'signature' => 'Engineering the seas 🚢',
                'points' => 240,
                'reaction_score' => 36,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(10),
                'setup_progress' => 68,
            ]
        ];

        foreach ($memberUsers as $userData) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
                echo "✅ Created member: {$user->name}\n";
            } catch (\Exception $e) {
                echo "❌ Failed to create member: {$userData['name']} - " . $e->getMessage() . "\n";
            }
        }

        // ====================================================================
        // GUEST USERS - Khách tham quan (nếu cần)
        // ====================================================================

        $guestUsers = [
            [
                'name' => 'Demo Guest',
                'username' => 'demo_guest',
                'email' => 'guest@mechamap.vn',
                'password' => Hash::make('Guest2024@'),
                'role' => 'guest',
                'avatar' => '/images/avatars/guest1.jpg',
                'about_me' => 'Tài khoản demo cho khách tham quan hệ thống MechaMap.',
                'location' => 'Vietnam',
                'signature' => 'Welcome to MechaMap Community!',
                'points' => 0,
                'reaction_score' => 0,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => false,
                'last_seen_at' => now()->subWeek(),
                'setup_progress' => 10,
            ],
            [
                'name' => 'Khách Tham Quan',
                'username' => 'visitor_user',
                'email' => 'visitor@example.com',
                'password' => Hash::make('Visitor2024@'),
                'role' => 'guest',
                'avatar' => '/images/avatars/guest2.jpg',
                'about_me' => 'Khách tham quan quan tâm đến ngành cơ khí. Đang tìm hiểu về cộng đồng kỹ sư Việt Nam.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Exploring the engineering world 👀',
                'points' => 5,
                'reaction_score' => 1,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => false,
                'last_seen_at' => now()->subDays(3),
                'setup_progress' => 15,
            ],
            [
                'name' => 'Observer User',
                'username' => 'observer_guest',
                'email' => 'observer@guest.com',
                'password' => Hash::make('Observer2024@'),
                'role' => 'guest',
                'avatar' => '/images/avatars/guest3.jpg',
                'about_me' => 'Người quan sát muốn tìm hiểu về xu hướng công nghệ cơ khí hiện đại.',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Learning by observing 📖',
                'points' => 2,
                'reaction_score' => 0,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => false,
                'last_seen_at' => now()->subDays(5),
                'setup_progress' => 12,
            ]
        ];

        foreach ($guestUsers as $userData) {
            try {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
                echo "✅ Created guest: {$user->name}\n";
            } catch (\Exception $e) {
                echo "❌ Failed to create guest: {$userData['name']} - " . $e->getMessage() . "\n";
            }
        }

        // ====================================================================
        // OUTPUT CONFIRMATION
        // ====================================================================
        $this->command->info('✅ MechaMap User Seeder completed successfully!');
        $this->command->info('� Created ' . count($allAdminUsers) . ' System Management users');
        $this->command->info('� Created ' . count($moderatorUsers) . ' Community Management users');
        $this->command->info('⭐ Created ' . count($seniorUsers) . ' Senior Member users');
        $this->command->info('👤 Created ' . count($memberUsers) . ' Member users');
        $this->command->info('👁️ Created ' . count($guestUsers) . ' Guest users');

        $totalUsers = count($allAdminUsers) + count($moderatorUsers) + count($seniorUsers) + count($memberUsers) + count($guestUsers);
        $this->command->info('� Total community users: ' . $totalUsers . ' (representing Vietnamese mechanical engineering community)');
        $this->command->info('🎯 All users have realistic profiles and mechanical engineering backgrounds');
        $this->command->info('🌟 New role-based permissions assigned according to MechaMap Restructure Plan');
        $this->command->info('🖼️ Avatar URLs configured for /images/avatars/ directory');
    }
}
