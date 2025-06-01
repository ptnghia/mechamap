<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các user với role khác nhau và thông tin phong phú về cơ khí
        $users = [
            // Admin users
            [
                'name' => 'Nguyễn Văn Admin',
                'username' => 'admin_mechamap',
                'email' => 'admin@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'about_me' => 'Chuyên gia hàng đầu về tự động hóa công nghiệp tại Việt Nam với hơn 15 năm kinh nghiệm trong lĩnh vực thiết kế và triển khai hệ thống robot công nghiệp.',
                'website' => 'https://mechaengineering.vn',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Đổi mới trong Tự động hóa - Xây dựng Tương lai Sản xuất',
                'points' => 50000,
                'reaction_score' => 15000,
                'email_verified_at' => now(),
                'last_seen_at' => now(),
            ],

            // Moderator users
            [
                'name' => 'Trần Thị Moderator',
                'username' => 'mod_automation',
                'email' => 'moderator@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'moderator',
                'status' => 'active',
                'about_me' => 'Kỹ sư cơ khí chuyên về hệ thống băng tải và automation. Có kinh nghiệm 10 năm trong ngành sản xuất ô tô và điện tử.',
                'website' => 'https://automation-vietnam.com',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Chuyên gia Tự động hóa | Lập trình CNC | Thiết kế Công nghiệp',
                'points' => 25000,
                'reaction_score' => 8500,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(2),
            ],

            // Senior members
            [
                'name' => 'Lê Minh Expert',
                'username' => 'cnc_master',
                'email' => 'expert1@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'senior',
                'status' => 'active',
                'about_me' => 'Chuyên gia CNC với 12 năm kinh nghiệm. Đã thiết kế và lập trình hơn 200 máy CNC cho các nhà máy sản xuất linh kiện ô tô.',
                'website' => 'https://cncvietnam.expert',
                'location' => 'Bình Dương, Việt Nam',
                'signature' => 'Thạc sĩ CNC | Sản xuất Chính xác | Chuyên gia CAD/CAM',
                'points' => 18000,
                'reaction_score' => 6200,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(1),
            ],
            [
                'name' => 'Phạm Văn Robot',
                'username' => 'robot_engineer',
                'email' => 'robot.engineer@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'senior',
                'status' => 'active',
                'about_me' => 'Kỹ sư robotics chuyên về robot công nghiệp ABB, KUKA, Fanuc. Đã triển khai hơn 50 dây chuyền robot tại các nhà máy FDI.',
                'website' => 'https://robotvietnam.tech',
                'location' => 'Đông Nai, Việt Nam',
                'signature' => 'Robot Công nghiệp | Tích hợp AI | Sản xuất Thông minh',
                'points' => 16500,
                'reaction_score' => 5800,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subMinutes(30),
            ],

            // Active members
            [
                'name' => 'Hoàng Thị Conveyor',
                'username' => 'conveyor_specialist',
                'email' => 'conveyor@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'status' => 'active',
                'about_me' => 'Chuyên gia hệ thống băng tải và logistics automation. Đã thiết kế hệ thống băng tải cho nhiều kho hàng và nhà máy.',
                'location' => 'Hà Nam, Việt Nam',
                'signature' => 'Hệ thống Băng tải | Vận chuyển Vật liệu | Tự động hóa Kho',
                'points' => 8200,
                'reaction_score' => 2100,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(3),
            ],
            [
                'name' => 'Ngô Văn PLC',
                'username' => 'plc_programmer',
                'email' => 'plc.pro@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'status' => 'active',
                'about_me' => 'Lập trình viên PLC Siemens, Mitsubishi, Omron với 7 năm kinh nghiệm. Chuyên về hệ thống điều khiển tự động trong sản xuất.',
                'location' => 'Vĩnh Phúc, Việt Nam',
                'signature' => 'Lập trình PLC | SCADA | Mạng Công nghiệp',
                'points' => 6800,
                'reaction_score' => 1850,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(5),
            ],
            [
                'name' => 'Vũ Thị CAD',
                'username' => 'cad_designer',
                'email' => 'cad.design@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'status' => 'active',
                'about_me' => 'Thiết kế cơ khí CAD/CAM với SolidWorks, AutoCAD, Inventor. Chuyên thiết kế khuôn mẫu và máy móc công nghiệp.',
                'website' => 'https://caddesign.vn',
                'location' => 'Hải Phòng, Việt Nam',
                'signature' => 'Thiết kế CAD | Mô hình 3D | Phát triển Sản phẩm',
                'points' => 5200,
                'reaction_score' => 1420,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(4),
            ],
            [
                'name' => 'Đặng Minh Hydraulic',
                'username' => 'hydraulic_expert',
                'email' => 'hydraulic@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'status' => 'active',
                'about_me' => 'Chuyên gia hệ thống thủy lực và khí nén trong công nghiệp. Có kinh nghiệm thiết kế và bảo trì máy nén, máy ép thủy lực.',
                'location' => 'Quảng Ninh, Việt Nam',
                'signature' => 'Thủy lực | Khí nén | Hệ thống Lưu chất',
                'points' => 4600,
                'reaction_score' => 1200,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(6),
            ],
            [
                'name' => 'Bùi Văn Sensor',
                'username' => 'sensor_tech',
                'email' => 'sensor@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'status' => 'active',
                'about_me' => 'Kỹ thuật viên chuyên về sensor và IoT trong công nghiệp. Thiết kế hệ thống giám sát và thu thập dữ liệu tự động.',
                'location' => 'Cần Thơ, Việt Nam',
                'signature' => 'Sensor IoT | Thu thập Dữ liệu | Công nghiệp 4.0',
                'points' => 3800,
                'reaction_score' => 980,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(8),
            ],
            [
                'name' => 'Lý Thị Motor',
                'username' => 'motor_specialist',
                'email' => 'motor@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'status' => 'active',
                'about_me' => 'Chuyên gia động cơ và biến tần. Có kinh nghiệm sửa chữa và lắp đặt các loại motor AC/DC, servo motor trong công nghiệp.',
                'location' => 'Đà Nẵng, Việt Nam',
                'signature' => 'Động cơ | Biến tần | Điện tử Công suất',
                'points' => 3200,
                'reaction_score' => 850,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(10),
            ],

            // New members
            [
                'name' => 'Nguyễn Văn Newbie',
                'username' => 'mecha_newbie',
                'email' => 'newbie1@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'guest',
                'status' => 'active',
                'about_me' => 'Sinh viên năm cuối ngành Cơ khí Tự động hóa, đang tìm hiểu về robot công nghiệp và hệ thống PLC.',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'signature' => 'Học Tự động hóa | Kỹ sư Tương lai',
                'points' => 150,
                'reaction_score' => 45,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(2),
            ],
            [
                'name' => 'Trần Thị Student',
                'username' => 'automation_student',
                'email' => 'student@mechamap.com',
                'password' => Hash::make('password123'),
                'role' => 'guest',
                'status' => 'active',
                'about_me' => 'Sinh viên Kỹ thuật Điều khiển và Tự động hóa, quan tâm đến Industry 4.0 và smart manufacturing.',
                'location' => 'Hà Nội, Việt Nam',
                'signature' => 'Sinh viên Kỹ thuật Điều khiển | Đam mê Công nghiệp 4.0',
                'points' => 75,
                'reaction_score' => 20,
                'email_verified_at' => now(),
                'last_seen_at' => now()->subHours(1),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Gán role dựa trên thuộc tính role
            switch ($userData['role']) {
                case 'admin':
                    $user->assignRole('admin');
                    break;
                case 'moderator':
                    $user->assignRole('moderator');
                    break;
                case 'senior':
                    $user->assignRole('senior');
                    break;
                case 'member':
                    $user->assignRole('member');
                    break;
                case 'guest':
                    $user->assignRole('guest');
                    break;
            }
        }
    }
}
