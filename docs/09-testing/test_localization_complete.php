<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Thiết lập ngôn ngữ thành tiếng Việt
app()->setLocale('vi');

// Test bản dịch đầy đủ
echo "Testing all localizations...\n";
echo "Sticky: " . __('messages.thread_status.sticky') . "\n";
echo "Locked: " . __('messages.thread_status.locked') . "\n";
echo "Looking for Replies: " . __('messages.looking_for_replies') . "\n";
echo "Threads Looking for Replies: " . __('messages.threads_looking_for_replies') . "\n";
echo "Started by: " . __('messages.started_by') . "\n";
echo "Replies: " . __('messages.replies') . "\n";
echo "Started by me: " . __('messages.started_by_me') . "\n";
echo "Most Replies: " . __('messages.most_replies') . "\n";
echo "New Threads: " . __('messages.new_threads') . "\n";
echo "New Posts: " . __('messages.new_posts') . "\n";
echo "New Media: " . __('messages.new_media') . "\n";
echo "Popular: " . __('messages.popular') . "\n";
echo "Popular Threads: " . __('messages.popular_threads') . "\n";

echo "\n=== VALIDATION RESULTS ===\n";

// Test tất cả translations
$tests = [
    'sticky' => ['messages.thread_status.sticky', 'Ghim'],
    'locked' => ['messages.thread_status.locked', 'Khóa'],
    'looking_for_replies' => ['messages.looking_for_replies', 'Tìm Phản Hồi'],
    'threads_looking_for_replies' => ['messages.threads_looking_for_replies', 'Các Thread Cần Phản Hồi'],
    'started_by' => ['messages.started_by', 'Bắt đầu bởi'],
    'replies' => ['messages.replies', 'phản hồi'],
    'started_by_me' => ['messages.started_by_me', 'Tôi tạo'],
    'most_replies' => ['messages.most_replies', 'Nhiều Phản Hồi Nhất'],
    'new_threads' => ['messages.new_threads', 'Thread Mới'],
    'new_posts' => ['messages.new_posts', 'Bài Viết Mới'],
    'new_media' => ['messages.new_media', 'Media Mới'],
    'popular' => ['messages.popular', 'Phổ Biến'],
    'popular_threads' => ['messages.popular_threads', 'Thread Phổ Biến'],
];

$passed = 0;
$total = count($tests);

foreach ($tests as $key => $test) {
    $actual = __($test[0]);
    $expected = $test[1];

    if ($actual === $expected) {
        echo "✅ SUCCESS: $key = $actual\n";
        $passed++;
    } else {
        echo "❌ FAILED: $key = '$actual' (expected '$expected')\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Passed: $passed/$total tests\n";

if ($passed === $total) {
    echo "🎉 ALL TRANSLATIONS WORKING PERFECTLY!\n";
} else {
    echo "⚠️  Some translations need attention.\n";
}

echo "\nComplete localization test finished.\n";
