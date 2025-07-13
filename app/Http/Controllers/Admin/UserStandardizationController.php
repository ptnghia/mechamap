<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserStandardizationController extends Controller
{
    /**
     * 🔧 MechaMap User Standardization Controller
     *
     * Cung cấp interface để chuẩn hóa dữ liệu users một cách an toàn
     */

    /**
     * Hiển thị trang chuẩn hóa dữ liệu users
     */
    public function index()
    {
        // Thống kê hiện tại
        $stats = [
            'total_users' => User::count(),
            'users_with_avatar' => User::whereNotNull('avatar')->count(),
            'users_without_avatar' => User::whereNull('avatar')->count(),
            'users_missing_info' => User::where(function($q) {
                $q->whereNull('about_me')
                  ->orWhereNull('location')
                  ->orWhereNull('website')
                  ->orWhere('about_me', '')
                  ->orWhere('location', '')
                  ->orWhere('website', '');
            })->count(),
            'role_distribution' => User::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->orderBy('count', 'desc')
                ->get(),
            'status_distribution' => User::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        return view('admin.users.standardization', compact('stats'));
    }

    /**
     * Thực hiện backup dữ liệu users
     */
    public function backup()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupFile = storage_path("app/backups/users_backup_{$timestamp}.json");

            // Tạo thư mục backup nếu chưa có
            if (!file_exists(dirname($backupFile))) {
                mkdir(dirname($backupFile), 0755, true);
            }

            // Export users data as JSON
            $users = User::all()->toArray();
            file_put_contents($backupFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json([
                'success' => true,
                'message' => "Backup thành công! Đã backup {count($users)} users",
                'backup_file' => $backupFile,
                'backup_size' => $this->formatBytes(filesize($backupFile))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra tính toàn vẹn dữ liệu trước khi chuẩn hóa
     */
    public function checkIntegrity()
    {
        try {
            $issues = [];

            // Kiểm tra foreign key constraints
            $usersWithThreads = User::whereHas('threads')->count();
            $usersWithComments = User::whereHas('comments')->count();
            $usersWithOrders = User::whereHas('orders')->count();

            // Kiểm tra duplicate usernames/emails
            $duplicateUsernames = User::select('username', DB::raw('count(*) as count'))
                ->groupBy('username')
                ->having('count', '>', 1)
                ->get();

            $duplicateEmails = User::select('email', DB::raw('count(*) as count'))
                ->groupBy('email')
                ->having('count', '>', 1)
                ->get();

            if ($duplicateUsernames->count() > 0) {
                $issues[] = "Có {$duplicateUsernames->count()} username bị trùng lặp";
            }

            if ($duplicateEmails->count() > 0) {
                $issues[] = "Có {$duplicateEmails->count()} email bị trùng lặp";
            }

            return response()->json([
                'success' => true,
                'issues' => $issues,
                'stats' => [
                    'users_with_threads' => $usersWithThreads,
                    'users_with_comments' => $usersWithComments,
                    'users_with_orders' => $usersWithOrders,
                    'duplicate_usernames' => $duplicateUsernames->count(),
                    'duplicate_emails' => $duplicateEmails->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi kiểm tra tính toàn vẹn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thực hiện chuẩn hóa dữ liệu users
     */
    public function standardize(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'confirm_backup' => 'required|boolean',
                'groups' => 'required|array',
                'groups.*' => 'in:system_management,community_management,community_members,business_partners'
            ]);

            if (!$request->confirm_backup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng xác nhận đã tạo backup trước khi tiếp tục'
                ], 400);
            }

            // Tạo backup tự động
            $this->backup();

            // Thực hiện chuẩn hóa theo từng nhóm được chọn
            $results = [];

            foreach ($request->groups as $group) {
                $result = $this->standardizeGroup($group);
                $results[$group] = $result;
            }

            return response()->json([
                'success' => true,
                'message' => 'Chuẩn hóa dữ liệu thành công!',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi chuẩn hóa dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chuẩn hóa một nhóm users cụ thể
     */
    private function standardizeGroup(string $group): array
    {
        $updated = 0;

        switch ($group) {
            case 'system_management':
                $updated += $this->standardizeSystemManagement();
                break;
            case 'community_management':
                $updated += $this->standardizeCommunityManagement();
                break;
            case 'community_members':
                $updated += $this->standardizeCommunityMembers();
                break;
            case 'business_partners':
                $updated += $this->standardizeBusinessPartners();
                break;
        }

        return [
            'group' => $group,
            'updated_count' => $updated
        ];
    }

    /**
     * Chuẩn hóa System Management
     */
    private function standardizeSystemManagement(): int
    {
        $updated = 0;

        // Super Admin
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
            $updated++;
        }

        // System Admin
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
            $updated++;
        }

        // Content Admin
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
            $updated++;
        }

        // Admin (legacy)
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
            $updated++;
        }

        return $updated;
    }

    /**
     * Chuẩn hóa Community Management
     */
    private function standardizeCommunityManagement(): int
    {
        $updated = 0;

        // Content Moderator
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
            $updated++;
        }

        // Marketplace Moderator
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
            $updated++;
        }

        // Community Moderator
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
            $updated++;
        }

        return $updated;
    }

    /**
     * Chuẩn hóa Community Members
     */
    private function standardizeCommunityMembers(): int
    {
        $updated = 0;

        // Senior Member
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
            $updated++;
        }

        // Member
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
            $updated++;
        }

        // Guest
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
            $updated++;
        }

        return $updated;
    }

    /**
     * Chuẩn hóa Business Partners
     */
    private function standardizeBusinessPartners(): int
    {
        $updated = 0;

        // Verified Partner
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
            $updated++;
        }

        // Manufacturer
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
            $updated++;
        }

        // Supplier
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
            $updated++;
        }

        // Brand
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
            $updated++;
        }

        return $updated;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Cập nhật thông tin user
     */
    private function updateUser(User $user, array $data): void
    {
        $data['password'] = bcrypt('O!0omj-kJ6yP');
        $data['email_verified_at'] = now();
        $data['status'] = 'active';
        $data['is_active'] = true;
        $data['last_seen_at'] = now();
        $data['setup_progress'] = 100;

        $user->update($data);
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
