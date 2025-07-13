<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UserDataStandardizationSeeder extends Seeder
{
    /**
     * 🔧 MechaMap User Data Standardization Seeder
     *
     * Chuẩn hóa dữ liệu users theo quy tắc thống nhất:
     * - Naming convention chuẩn
     * - Avatar phù hợp với role
     * - Thông tin profile đầy đủ
     * - Mật khẩu thống nhất: O!0omj-kJ6yP
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu chuẩn hóa dữ liệu users...');

        // Backup trước khi thực hiện
        $this->createBackup();

        // Chuẩn hóa theo từng nhóm
        $this->standardizeSystemManagement();
        $this->standardizeCommunityManagement();
        $this->standardizeCommunityMembers();
        $this->standardizeBusinessPartners();

        $this->command->info('✅ Hoàn thành chuẩn hóa dữ liệu users!');
    }

    /**
     * Tạo backup trước khi chuẩn hóa
     */
    private function createBackup(): void
    {
        $this->command->info('📦 Tạo backup dữ liệu...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/users_backup_{$timestamp}.json");

        // Tạo thư mục backup nếu chưa có
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        // Export users data as JSON (safer than SQL)
        $users = User::all()->toArray();
        file_put_contents($backupFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->command->info("✅ Backup tạo tại: {$backupFile}");
        $this->command->info("📊 Đã backup " . count($users) . " users");
    }

    /**
     * 🏛️ Chuẩn hóa System Management (Level 1-3)
     */
    private function standardizeSystemManagement(): void
    {
        $this->command->info('🏛️ Chuẩn hóa System Management...');

        // Super Admin (Level 1)
        $superAdmins = User::where('role', 'super_admin')->get();
        foreach ($superAdmins as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Super Admin {$num}",
                'username' => "superadmin{$num}",
                'email' => "superadmin{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/super_admin.jpg',
                'about_me' => 'Quản trị viên tối cao MechaMap - Chuyên trách quản lý và vận hành toàn bộ hệ thống',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Super Administrator 👑',
                'points' => 15000,
                'reaction_score' => 800,
            ]);
        }

        // System Admin (Level 2)
        $systemAdmins = User::where('role', 'system_admin')->get();
        foreach ($systemAdmins as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "System Admin {$num}",
                'username' => "sysadmin{$num}",
                'email' => "sysadmin{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/admin.jpg',
                'about_me' => 'Quản trị viên hệ thống MechaMap - Chuyên trách bảo trì và phát triển hệ thống kỹ thuật',
                'location' => 'Hà Nội, Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap System Administrator 🔧',
                'points' => 12000,
                'reaction_score' => 600,
            ]);
        }

        // Content Admin (Level 3)
        $contentAdmins = User::where('role', 'content_admin')->get();
        foreach ($contentAdmins as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Content Admin {$num}",
                'username' => "contentadmin{$num}",
                'email' => "contentadmin{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/admin.jpg',
                'about_me' => 'Quản trị viên nội dung MechaMap - Chuyên trách quản lý và kiểm duyệt nội dung',
                'location' => 'Đà Nẵng, Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Content Administrator 📝',
                'points' => 10000,
                'reaction_score' => 500,
            ]);
        }

        // Admin (legacy role)
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Admin {$num}",
                'username' => "admin{$num}",
                'email' => "admin{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/admin.jpg',
                'about_me' => 'Quản trị viên MechaMap - Quản lý và vận hành hệ thống',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Administrator ⚙️',
                'points' => 10000,
                'reaction_score' => 500,
            ]);
        }
    }

    /**
     * 👥 Chuẩn hóa Community Management (Level 4-6)
     */
    private function standardizeCommunityManagement(): void
    {
        $this->command->info('👥 Chuẩn hóa Community Management...');

        // Content Moderator (Level 4)
        $contentMods = User::where('role', 'content_moderator')->get();
        foreach ($contentMods as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Content Moderator {$num}",
                'username' => "contentmod{$num}",
                'email' => "contentmod{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/moderator.jpg',
                'about_me' => 'Kiểm duyệt viên nội dung MechaMap - Đảm bảo chất lượng nội dung cộng đồng',
                'location' => 'Hà Nội, Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Content Moderator 📋',
                'points' => 8000,
                'reaction_score' => 400,
            ]);
        }

        // Marketplace Moderator (Level 5)
        $marketplaceMods = User::where('role', 'marketplace_moderator')->get();
        foreach ($marketplaceMods as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Marketplace Moderator {$num}",
                'username' => "marketplacemod{$num}",
                'email' => "marketplacemod{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/moderator.jpg',
                'about_me' => 'Kiểm duyệt viên marketplace MechaMap - Quản lý và kiểm duyệt sản phẩm, giao dịch',
                'location' => 'TP. Hồ Chí Minh, Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Marketplace Moderator 🛒',
                'points' => 7000,
                'reaction_score' => 350,
            ]);
        }

        // Community Moderator (Level 6)
        $communityMods = User::where('role', 'community_moderator')->get();
        foreach ($communityMods as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Community Moderator {$num}",
                'username' => "communitymod{$num}",
                'email' => "communitymod{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/moderator.jpg',
                'about_me' => 'Kiểm duyệt viên cộng đồng MechaMap - Hỗ trợ và quản lý hoạt động cộng đồng',
                'location' => 'Đà Nẵng, Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Community Moderator 👥',
                'points' => 6000,
                'reaction_score' => 300,
            ]);
        }
    }

    /**
     * 🧑‍🤝‍🧑 Chuẩn hóa Community Members (Level 7-9)
     */
    private function standardizeCommunityMembers(): void
    {
        $this->command->info('🧑‍🤝‍🧑 Chuẩn hóa Community Members...');

        // Senior Member (Level 7)
        $seniorMembers = User::where('role', 'senior_member')->get();
        foreach ($seniorMembers as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Senior Member {$num}",
                'username' => "seniormember{$num}",
                'email' => "seniormember{$num}@mechamap.com",
                'avatar' => $this->getRandomAvatar(),
                'about_me' => 'Thành viên cao cấp MechaMap - Chuyên gia cơ khí với kinh nghiệm phong phú',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Senior Member ⭐',
                'points' => rand(2000, 5000),
                'reaction_score' => rand(100, 300),
            ]);
        }

        // Member (Level 8)
        $members = User::where('role', 'member')->get();
        foreach ($members as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Member {$num}",
                'username' => "member{$num}",
                'email' => "member{$num}@mechamap.com",
                'avatar' => $this->getRandomAvatar(),
                'about_me' => 'Thành viên MechaMap - Đam mê cơ khí và công nghệ, tích cực tham gia cộng đồng',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Member ⚙️',
                'points' => rand(100, 2000),
                'reaction_score' => rand(10, 100),
            ]);
        }

        // Guest (Level 9)
        $guests = User::where('role', 'guest')->get();
        foreach ($guests as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Guest {$num}",
                'username' => "guest{$num}",
                'email' => "guest{$num}@mechamap.com",
                'avatar' => '/images/users/avatars/default.jpg',
                'about_me' => 'Khách tham quan MechaMap - Quan tâm đến lĩnh vực cơ khí',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Guest 👋',
                'points' => rand(0, 100),
                'reaction_score' => rand(0, 10),
            ]);
        }
    }

    /**
     * 🤝 Chuẩn hóa Business Partners (Level 10-13)
     */
    private function standardizeBusinessPartners(): void
    {
        $this->command->info('🤝 Chuẩn hóa Business Partners...');

        // Verified Partner (Level 10)
        $verifiedPartners = User::where('role', 'verified_partner')->get();
        foreach ($verifiedPartners as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Verified Partner {$num}",
                'username' => "verifiedpartner{$num}",
                'email' => "verifiedpartner{$num}@mechamap.com",
                'avatar' => $this->getRandomAvatar(),
                'about_me' => 'Đối tác đã xác minh MechaMap - Cung cấp sản phẩm và dịch vụ cơ khí chất lượng',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Verified Partner ✅',
                'points' => rand(1000, 3000),
                'reaction_score' => rand(50, 200),
            ]);
        }

        // Manufacturer (Level 11)
        $manufacturers = User::where('role', 'manufacturer')->get();
        foreach ($manufacturers as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Manufacturer {$num}",
                'username' => "manufacturer{$num}",
                'email' => "manufacturer{$num}@mechamap.com",
                'avatar' => $this->getRandomAvatar(),
                'about_me' => 'Nhà sản xuất MechaMap - Chuyên sản xuất thiết bị và linh kiện cơ khí',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Manufacturer 🏭',
                'points' => rand(2000, 5000),
                'reaction_score' => rand(100, 300),
            ]);
        }

        // Supplier (Level 12)
        $suppliers = User::whereIn('role', ['supplier', 'supplier_verified'])->get();
        foreach ($suppliers as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Supplier {$num}",
                'username' => "supplier{$num}",
                'email' => "supplier{$num}@mechamap.com",
                'role' => 'supplier', // Chuẩn hóa role
                'avatar' => $this->getRandomAvatar(),
                'about_me' => 'Nhà cung cấp MechaMap - Cung cấp vật liệu và thiết bị cơ khí chất lượng',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Supplier 📦',
                'points' => rand(1500, 4000),
                'reaction_score' => rand(75, 250),
            ]);
        }

        // Brand (Level 13)
        $brands = User::where('role', 'brand')->get();
        foreach ($brands as $index => $user) {
            $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $this->updateUser($user, [
                'name' => "Brand {$num}",
                'username' => "brand{$num}",
                'email' => "brand{$num}@mechamap.com",
                'avatar' => $this->getRandomAvatar(),
                'about_me' => 'Thương hiệu MechaMap - Đại diện thương hiệu uy tín trong ngành cơ khí',
                'location' => 'Việt Nam',
                'website' => 'https://mechamap.com',
                'signature' => 'MechaMap Brand Representative 🏷️',
                'points' => rand(3000, 6000),
                'reaction_score' => rand(150, 400),
            ]);
        }
    }

    /**
     * Cập nhật thông tin user
     */
    private function updateUser(User $user, array $data): void
    {
        $data['password'] = Hash::make('O!0omj-kJ6yP');
        $data['email_verified_at'] = now();
        $data['status'] = 'active';
        $data['is_active'] = true;
        $data['last_seen_at'] = now();
        $data['setup_progress'] = 100;

        $user->update($data);
        $this->command->info("✅ Cập nhật user ID {$user->id}: {$data['name']}");
    }

    /**
     * Lấy avatar ngẫu nhiên
     */
    private function getRandomAvatar(): string
    {
        $avatars = [
            '/images/users/avatars/avatar-1.jpg',
            '/images/users/avatars/avatar-2.jpg',
            '/images/users/avatars/avatar-3.jpg',
            '/images/users/avatars/avatar-4.jpg',
            '/images/users/avatars/avatar-5.jpg',
            '/images/users/avatars/avatar-6.jpg',
            '/images/users/avatars/avatar-7.jpg',
            '/images/users/avatars/avatar-8.jpg',
            '/images/users/avatars/avatar-9.jpg',
            '/images/users/avatars/avatar-10.jpg',
        ];

        return $avatars[array_rand($avatars)];
    }
}
