<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Thiết lập ngôn ngữ thành tiếng Việt
app()->setLocale('vi');

// Test bản dịch
echo "Testing localization...\n";
echo "Sticky: " . __('messages.thread_status.sticky') . "\n";
echo "Locked: " . __('messages.thread_status.locked') . "\n";
echo "Looking for Replies: " . __('messages.looking_for_replies') . "\n";
echo "Threads Looking for Replies: " . __('messages.threads_looking_for_replies') . "\n";
echo "Started by: " . __('messages.started_by') . "\n";
echo "Replies: " . __('messages.replies') . "\n";
echo "Started by me: " . __('messages.started_by_me') . "\n";
echo "Most Replies: " . __('messages.most_replies') . "\n";

// Kiểm tra xem có trả về key gốc không (nghĩa là không tìm thấy bản dịch)
$translations = [
    'sticky' => __('messages.thread_status.sticky'),
    'locked' => __('messages.thread_status.locked'),
    'looking_for_replies' => __('messages.looking_for_replies'),
    'threads_looking_for_replies' => __('messages.threads_looking_for_replies'),
    'started_by' => __('messages.started_by'),
    'replies' => __('messages.replies'),
    'started_by_me' => __('messages.started_by_me'),
    'most_replies' => __('messages.most_replies'),
];

echo "\n=== VALIDATION RESULTS ===\n";
foreach ($translations as $key => $value) {
    $expectedKey = "messages.$key";
    if (str_contains($key, '.')) {
        $expectedKey = "messages.$key";
    } else {
        $expectedKey = "messages.$key";
    }
    
    if ($value === $expectedKey || str_contains($value, 'messages.')) {
        echo "❌ ERROR: $key translation not found! (returned: $value)\n";
    } else {
        echo "✅ SUCCESS: $key = $value\n";
    }
}

echo "\nLocalization test completed.\n";
