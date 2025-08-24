<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Load Laravel configuration
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Adding Missing Translation Keys for MechaMap\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Missing translation keys
$translationKeys = [
    // UI Keys
    'ui.yes' => [
        'vi' => 'Có',
        'en' => 'Yes',
        'group' => 'ui'
    ],
    'ui.cancel' => [
        'vi' => 'Hủy bỏ',
        'en' => 'Cancel',
        'group' => 'ui'
    ],
    'ui.processing' => [
        'vi' => 'Đang xử lý',
        'en' => 'Processing',
        'group' => 'ui'
    ],
    'ui.sending' => [
        'vi' => 'Đang gửi',
        'en' => 'Sending',
        'group' => 'ui'
    ],
    'ui.please_wait' => [
        'vi' => 'Vui lòng đợi...',
        'en' => 'Please wait...',
        'group' => 'ui'
    ],

    // Conversations Keys
    'conversations.index.heading' => [
        'vi' => 'Quản lý tin nhắn',
        'en' => 'Manage Conversations',
        'group' => 'conversations'
    ],
    'conversations.index.description' => [
        'vi' => 'Xem và quản lý tất cả cuộc hội thoại của bạn',
        'en' => 'View and manage all your conversations',
        'group' => 'conversations'
    ],
    'conversations.index.new_conversation' => [
        'vi' => 'Tạo cuộc hội thoại mới',
        'en' => 'New Conversation',
        'group' => 'conversations'
    ],
    'conversations.index.search_placeholder' => [
        'vi' => 'Tìm kiếm cuộc hội thoại...',
        'en' => 'Search conversations...',
        'group' => 'conversations'
    ],
    'conversations.index.all_status' => [
        'vi' => 'Tất cả trạng thái',
        'en' => 'All Status',
        'group' => 'conversations'
    ],
    'conversations.index.unread' => [
        'vi' => 'Chưa đọc',
        'en' => 'Unread',
        'group' => 'conversations'
    ],
    'conversations.index.read' => [
        'vi' => 'Đã đọc',
        'en' => 'Read',
        'group' => 'conversations'
    ],
    'conversations.index.archived' => [
        'vi' => 'Đã lưu trữ',
        'en' => 'Archived',
        'group' => 'conversations'
    ],
    'conversations.index.sort_latest' => [
        'vi' => 'Mới nhất',
        'en' => 'Latest',
        'group' => 'conversations'
    ],
    'conversations.index.sort_oldest' => [
        'vi' => 'Cũ nhất',
        'en' => 'Oldest',
        'group' => 'conversations'
    ],
    'conversations.index.sort_unread_first' => [
        'vi' => 'Chưa đọc trước',
        'en' => 'Unread First',
        'group' => 'conversations'
    ],
    'conversations.index.conversations_list' => [
        'vi' => 'Danh sách cuộc hội thoại',
        'en' => 'Conversations List',
        'group' => 'conversations'
    ],
    'conversations.index.participants' => [
        'vi' => 'người tham gia',
        'en' => 'participants',
        'group' => 'conversations'
    ],
    'conversations.index.view' => [
        'vi' => 'Xem',
        'en' => 'View',
        'group' => 'conversations'
    ],
    'conversations.index.no_conversations' => [
        'vi' => 'Chưa có cuộc hội thoại nào',
        'en' => 'No conversations yet',
        'group' => 'conversations'
    ],
    'conversations.index.start_conversation' => [
        'vi' => 'Bắt đầu cuộc hội thoại đầu tiên của bạn',
        'en' => 'Start your first conversation',
        'group' => 'conversations'
    ],

    // Conversation Detail Keys
    'conversations.show.title' => [
        'vi' => 'Chi tiết cuộc hội thoại',
        'en' => 'Conversation Details',
        'group' => 'conversations'
    ],
    'conversations.show.type_message' => [
        'vi' => 'Nhập tin nhắn...',
        'en' => 'Type a message...',
        'group' => 'conversations'
    ],
    'conversations.show.send' => [
        'vi' => 'Gửi',
        'en' => 'Send',
        'group' => 'conversations'
    ],
    'conversations.show.online' => [
        'vi' => 'Đang online',
        'en' => 'Online',
        'group' => 'conversations'
    ],
    'conversations.show.offline' => [
        'vi' => 'Offline',
        'en' => 'Offline',
        'group' => 'conversations'
    ],

    // Create Conversation Keys
    'conversations.create.title' => [
        'vi' => 'Tạo cuộc hội thoại mới',
        'en' => 'Create New Conversation',
        'group' => 'conversations'
    ],
    'conversations.create.select_users' => [
        'vi' => 'Chọn người dùng',
        'en' => 'Select Users',
        'group' => 'conversations'
    ],
    'conversations.create.select_users_desc' => [
        'vi' => 'Bạn có thể nhập nhiều tên ở đây.',
        'en' => 'You may enter multiple names here.',
        'group' => 'conversations'
    ],
    'conversations.create.select_user' => [
        'vi' => 'Chọn một người dùng',
        'en' => 'Select a user',
        'group' => 'conversations'
    ],
    'conversations.create.subject' => [
        'vi' => 'Chủ đề',
        'en' => 'Subject',
        'group' => 'conversations'
    ],
    'conversations.create.subject_placeholder' => [
        'vi' => 'Tiêu đề cuộc hội thoại...',
        'en' => 'Conversation title...',
        'group' => 'conversations'
    ],
    'conversations.create.message' => [
        'vi' => 'Tin nhắn đầu tiên',
        'en' => 'First Message',
        'group' => 'conversations'
    ],
    'conversations.create.message_placeholder' => [
        'vi' => 'Tin nhắn của bạn...',
        'en' => 'Your message...',
        'group' => 'conversations'
    ],
    'conversations.create.allow_invite' => [
        'vi' => 'Cho phép bất kỳ ai trong cuộc hội thoại mời người khác',
        'en' => 'Allow anyone in the conversation to invite others',
        'group' => 'conversations'
    ],
    'conversations.create.lock_conversation' => [
        'vi' => 'Khóa cuộc hội thoại (không cho phép phản hồi)',
        'en' => 'Lock conversation (no responses will be allowed)',
        'group' => 'conversations'
    ],
    'conversations.create.create_button' => [
        'vi' => 'Tạo cuộc hội thoại',
        'en' => 'Create Conversation',
        'group' => 'conversations'
    ],

    // Additional UI Keys
    'ui.sort' => [
        'vi' => 'Sắp xếp',
        'en' => 'Sort',
        'group' => 'ui'
    ],
    'ui.filter' => [
        'vi' => 'Bộ lọc',
        'en' => 'Filter',
        'group' => 'ui'
    ],
    'ui.search' => [
        'vi' => 'Tìm kiếm',
        'en' => 'Search',
        'group' => 'ui'
    ],
    'ui.sort_alphabetical' => [
        'vi' => 'Theo tên A-Z',
        'en' => 'Alphabetical A-Z',
        'group' => 'ui'
    ],
    'ui.search_error' => [
        'vi' => 'Lỗi tìm kiếm',
        'en' => 'Search error',
        'group' => 'ui'
    ],
    'ui.no_results' => [
        'vi' => 'Không tìm thấy kết quả',
        'en' => 'No results found',
        'group' => 'ui'
    ],

    // Additional Conversation Keys
    'conversations.index.started_by_me' => [
        'vi' => 'Tôi bắt đầu',
        'en' => 'Started by me',
        'group' => 'conversations'
    ],
    'conversations.index.recently_active' => [
        'vi' => 'Hoạt động gần đây',
        'en' => 'Recently active',
        'group' => 'conversations'
    ],
    'conversations.conversation' => [
        'vi' => 'Cuộc hội thoại',
        'en' => 'Conversation',
        'group' => 'conversations'
    ],
    'conversations.you' => [
        'vi' => 'Bạn',
        'en' => 'You',
        'group' => 'conversations'
    ],
    'conversations.no_messages_yet' => [
        'vi' => 'Chưa có tin nhắn',
        'en' => 'No messages yet',
        'group' => 'conversations'
    ],
];

$totalKeys = count($translationKeys);
$totalAdded = 0;
$totalSkipped = 0;
$totalErrors = 0;

echo "📊 Tổng số keys cần xử lý: {$totalKeys}\n\n";

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

    try {
        // Check if key already exists
        $existingVi = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'vi')
            ->first();

        $existingEn = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'en')
            ->first();

        if ($existingVi && $existingEn) {
            echo "   ⏭️ Skipped: Key already exists\n";
            $totalSkipped++;
            continue;
        }

        // Add Vietnamese translation
        if (!$existingVi) {
            DB::table('translations')->insert([
                'key' => $key,
                'content' => $data['vi'],
                'locale' => 'vi',
                'group_name' => $data['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Added VI: {$data['vi']}\n";
        }

        // Add English translation
        if (!$existingEn) {
            DB::table('translations')->insert([
                'key' => $key,
                'content' => $data['en'],
                'locale' => 'en',
                'group_name' => $data['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Added EN: {$data['en']}\n";
        }

        $totalAdded++;

    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
        $totalErrors++;
    }

    echo "\n";
}

echo "=" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Keys added: {$totalAdded}\n";
echo "   ⏭️ Keys skipped: {$totalSkipped}\n";
echo "   ❌ Errors: {$totalErrors}\n";
echo "   📝 Total processed: " . ($totalAdded + $totalSkipped + $totalErrors) . "\n";
echo "\n🎉 Missing translation keys setup completed!\n";
