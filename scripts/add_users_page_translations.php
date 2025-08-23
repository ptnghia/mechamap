<?php

/**
 * Script để thêm tất cả translation keys còn lại cho trang /users
 * Tự động bỏ qua nếu key đã tồn tại
 *
 * Usage: php scripts/add_users_page_translations.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Script thêm translation keys cho trang /users\n";
echo "===============================================\n\n";

// Danh sách translation keys cần thêm
$translations = [
    // Page Structure
    [
        'key' => 'ui.users.page_title',
        'group_name' => 'ui',
        'vi' => 'Thành viên Cộng đồng',
        'en' => 'Community Members'
    ],
    [
        'key' => 'ui.users.member_count',
        'group_name' => 'ui',
        'vi' => 'thành viên',
        'en' => 'members'
    ],

    // Navigation/Tabs
    [
        'key' => 'ui.users.all_members',
        'group_name' => 'ui',
        'vi' => 'Tất cả thành viên',
        'en' => 'All Members'
    ],
    [
        'key' => 'ui.users.online_members',
        'group_name' => 'ui',
        'vi' => 'Đang trực tuyến',
        'en' => 'Online'
    ],
    [
        'key' => 'ui.users.staff',
        'group_name' => 'ui',
        'vi' => 'Ban quản trị',
        'en' => 'Staff'
    ],
    [
        'key' => 'ui.users.leaderboard',
        'group_name' => 'ui',
        'vi' => 'Bảng xếp hạng',
        'en' => 'Leaderboard'
    ],

    // Search & Filter
    [
        'key' => 'ui.users.search_profiles',
        'group_name' => 'ui',
        'vi' => 'Tìm kiếm hồ sơ',
        'en' => 'Search Profiles'
    ],
    [
        'key' => 'ui.users.filter_by_role',
        'group_name' => 'ui',
        'vi' => 'Lọc theo vai trò',
        'en' => 'Filter by Role'
    ],
    [
        'key' => 'ui.users.sort_by',
        'group_name' => 'ui',
        'vi' => 'Sắp xếp theo',
        'en' => 'Sort by'
    ],
    [
        'key' => 'ui.users.search',
        'group_name' => 'ui',
        'vi' => 'Tìm kiếm',
        'en' => 'Search'
    ],
    [
        'key' => 'ui.users.reset',
        'group_name' => 'ui',
        'vi' => 'Đặt lại',
        'en' => 'Reset'
    ],

    // Role Options
    [
        'key' => 'ui.users.all_roles',
        'group_name' => 'ui',
        'vi' => 'Tất cả vai trò',
        'en' => 'All Roles'
    ],
    [
        'key' => 'ui.users.all_admin',
        'group_name' => 'ui',
        'vi' => 'Tất cả Admin',
        'en' => 'All Admin'
    ],
    [
        'key' => 'ui.users.all_moderator',
        'group_name' => 'ui',
        'vi' => 'Tất cả Moderator',
        'en' => 'All Moderator'
    ],
    [
        'key' => 'ui.users.all_members_role',
        'group_name' => 'ui',
        'vi' => 'Tất cả thành viên',
        'en' => 'All Members'
    ],
    [
        'key' => 'ui.users.all_partners',
        'group_name' => 'ui',
        'vi' => 'Tất cả đối tác',
        'en' => 'All Partners'
    ],

    // Sort Options
    [
        'key' => 'ui.users.newest',
        'group_name' => 'ui',
        'vi' => 'Mới nhất',
        'en' => 'Newest'
    ],
    [
        'key' => 'ui.users.oldest',
        'group_name' => 'ui',
        'vi' => 'Cũ nhất',
        'en' => 'Oldest'
    ],
    [
        'key' => 'ui.users.by_name',
        'group_name' => 'ui',
        'vi' => 'Theo tên A-Z',
        'en' => 'By Name A-Z'
    ],
    [
        'key' => 'ui.users.by_posts',
        'group_name' => 'ui',
        'vi' => 'Số bài viết',
        'en' => 'Post Count'
    ],
    [
        'key' => 'ui.users.by_threads',
        'group_name' => 'ui',
        'vi' => 'Số chủ đề',
        'en' => 'Thread Count'
    ],

    // Sidebar Stats
    [
        'key' => 'ui.users.community_stats',
        'group_name' => 'ui',
        'vi' => 'Thống kê cộng đồng',
        'en' => 'Community Statistics'
    ],
    [
        'key' => 'ui.users.total_members',
        'group_name' => 'ui',
        'vi' => 'Tổng số thành viên:',
        'en' => 'Total Members:'
    ],
    [
        'key' => 'ui.users.newest_member',
        'group_name' => 'ui',
        'vi' => 'Thành viên mới nhất:',
        'en' => 'Newest Member:'
    ],
    [
        'key' => 'ui.users.online_count',
        'group_name' => 'ui',
        'vi' => 'Đang trực tuyến:',
        'en' => 'Online:'
    ],
    [
        'key' => 'ui.users.top_contributors',
        'group_name' => 'ui',
        'vi' => 'Top đóng góp tháng này',
        'en' => 'Top Contributors This Month'
    ],
    [
        'key' => 'ui.users.view_leaderboard',
        'group_name' => 'ui',
        'vi' => 'Xem bảng xếp hạng',
        'en' => 'View Leaderboard'
    ],
    [
        'key' => 'ui.users.staff_members',
        'group_name' => 'ui',
        'vi' => 'Ban quản trị',
        'en' => 'Staff Members'
    ],
    [
        'key' => 'ui.users.view_all',
        'group_name' => 'ui',
        'vi' => 'Xem tất cả',
        'en' => 'View All'
    ],

    // Additional keys
    [
        'key' => 'ui.users.community_members_group',
        'group_name' => 'ui',
        'vi' => 'Thành viên cộng đồng',
        'en' => 'Community Members'
    ],
    [
        'key' => 'ui.users.no_members_found',
        'group_name' => 'ui',
        'vi' => 'Không tìm thấy thành viên nào',
        'en' => 'No members found'
    ],
    [
        'key' => 'ui.users.try_different_filters',
        'group_name' => 'ui',
        'vi' => 'Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.',
        'en' => 'Try changing filters or search keywords.'
    ],

    // Role Group Labels
    [
        'key' => 'ui.users.system_management_group',
        'group_name' => 'ui',
        'vi' => 'Quản lý hệ thống',
        'en' => 'System Management'
    ],
    [
        'key' => 'ui.users.community_management_group',
        'group_name' => 'ui',
        'vi' => 'Quản lý cộng đồng',
        'en' => 'Community Management'
    ],
    [
        'key' => 'ui.users.business_partners_group',
        'group_name' => 'ui',
        'vi' => 'Đối tác kinh doanh',
        'en' => 'Business Partners'
    ],
];

try {
    $now = Carbon::now();
    $totalAdded = 0;
    $totalSkipped = 0;

    echo "📝 Bắt đầu xử lý " . count($translations) . " translation keys...\n\n";

    foreach ($translations as $index => $translation) {
        $key = $translation['key'];
        $groupName = $translation['group_name'];
        $viContent = $translation['vi'];
        $enContent = $translation['en'];

        echo "🔍 [" . ($index + 1) . "/" . count($translations) . "] Xử lý key: {$key}\n";

        $addedForThisKey = 0;

        // Kiểm tra và thêm Vietnamese translation
        $existingVi = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'vi')
            ->first();

        if (!$existingVi) {
            DB::table('translations')->insert([
                'key' => $key,
                'locale' => 'vi',
                'content' => $viContent,
                'group_name' => $groupName,
                'namespace' => null,
                'is_active' => 1,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            echo "   ✅ Thêm VI: '{$viContent}'\n";
            $addedForThisKey++;
        } else {
            echo "   ⏭️  VI đã tồn tại: '{$existingVi->content}'\n";
        }

        // Kiểm tra và thêm English translation
        $existingEn = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'en')
            ->first();

        if (!$existingEn) {
            DB::table('translations')->insert([
                'key' => $key,
                'locale' => 'en',
                'content' => $enContent,
                'group_name' => $groupName,
                'namespace' => null,
                'is_active' => 1,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            echo "   ✅ Thêm EN: '{$enContent}'\n";
            $addedForThisKey++;
        } else {
            echo "   ⏭️  EN đã tồn tại: '{$existingEn->content}'\n";
        }

        if ($addedForThisKey > 0) {
            $totalAdded += $addedForThisKey;
        } else {
            $totalSkipped++;
        }

        echo "\n";
    }

    // Tổng kết
    echo "🎉 Hoàn thành!\n";
    echo "📊 Thống kê:\n";
    echo "   - Tổng số keys xử lý: " . count($translations) . "\n";
    echo "   - Translations đã thêm: {$totalAdded}\n";
    echo "   - Keys đã tồn tại (bỏ qua): {$totalSkipped}\n";

    if ($totalAdded > 0) {
        echo "\n💡 Lưu ý: Cache sẽ được clear tự động khi sử dụng translation.\n";
    }

    // Hiển thị thống kê theo group
    echo "\n📈 Thống kê translations trong group 'ui':\n";
    $groupStats = DB::table('translations')
        ->selectRaw('locale, COUNT(*) as count')
        ->where('group_name', 'ui')
        ->groupBy('locale')
        ->orderBy('locale')
        ->get();

    foreach ($groupStats as $stat) {
        echo "   - {$stat->locale}: {$stat->count} keys\n";
    }

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    exit(1);
}

echo "\n✨ Script hoàn thành!\n";
