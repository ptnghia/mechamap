<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MechaMapUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder này tạo user đầy đủ với phân quyền theo README:
     * - Admin: Toàn quyền quản lý hệ thống
     * - Moderator: Quản lý nội dung và kiểm duyệt
     * - Senior: Thành viên cao cấp với nhiều tính năng
     * - Member: Thành viên cơ bản
     * - Guest: Chỉ xem nội dung
     */
    public function run(): void
    {
        // ====================================================================
        // ADMIN USERS - Quản trị viên toàn quyền
        // ====================================================================

        $adminUsers = [
            [
                'name' => 'Nguyễn Quản Trị',
                'username' => 'admin',
                'email' => 'admin@mechamap.vn',
                'password' => Hash::make('AdminMecha2024@'),
                'role' => 'admin',
                'avatar' => '/images/avatars/admin.jpg',
                'about_me' => 'Quản trị viên hệ thống MechaMap - Chuyên gia về cơ khí và tự động hóa với 15+ năm kinh nghiệm trong lĩnh vực.',
                'website' => 'https://mechamap.vn',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Building the future of Vietnamese mechanical engineering community 🔧',
                'points' => 10000,
                'reaction_score' => 500,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now(),
                'setup_progress' => 100,
            ],
            [
                'name' => 'Trần Hệ Thống',
                'username' => 'sysadmin',
                'email' => 'sysadmin@mechamap.vn',
                'password' => Hash::make('SysAdmin2024@'),
                'role' => 'admin',
                'about_me' => 'System Administrator - Chuyên trách bảo trì và phát triển hệ thống kỹ thuật.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'System reliability & performance optimization specialist',
                'points' => 8500,
                'reaction_score' => 420,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHour(),
                'setup_progress' => 100,
            ]
        ];

        foreach ($adminUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remove role from mass assignment
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign Spatie Permission role
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
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
                'role' => 'moderator',
                'avatar' => '/images/avatars/moderator.jpg',
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
                'role' => 'moderator',
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
            ]
        ];

        foreach ($moderatorUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remove role from mass assignment
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign Spatie Permission role
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
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
                'role' => 'senior',
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
                'role' => 'senior',
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
                'role' => 'senior',
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
                'role' => 'senior',
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
            ]
        ];

        foreach ($seniorUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remove role from mass assignment
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign Spatie Permission role
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
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
                'role' => 'member',
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
            ]
        ];

        foreach ($memberUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remove role from mass assignment
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign Spatie Permission role
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
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
            ]
        ];

        foreach ($guestUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Remove role from mass assignment
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign Spatie Permission role
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        // ====================================================================
        // OUTPUT CONFIRMATION
        // ====================================================================
        $this->command->info('✅ MechaMap User Seeder completed successfully!');
        $this->command->info('👑 Created 2 Admin users with full permissions');
        $this->command->info('🛡️ Created 2 Moderator users with content management permissions');
        $this->command->info('⭐ Created 4 Senior users with advanced member privileges');
        $this->command->info('👤 Created 5 Member users with basic permissions');
        $this->command->info('👁️ Created 1 Guest user with view-only access');
        $this->command->info('🔧 Total users: 14 (representing Vietnamese mechanical engineering community)');
        $this->command->info('🎯 All users have realistic profiles and mechanical engineering backgrounds');
        $this->command->info('🌟 Role-based permissions assigned according to MechaMap hierarchy');
    }
}
