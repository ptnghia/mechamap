<?php

/**
 * Browser-based Thread Creation Test
 *
 * Script để test quy trình tạo threads thông qua browser automation
 *
 * @author MechaMap Team
 * @version 1.0
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use App\Models\Thread;
use Illuminate\Support\Facades\Hash;

echo "🧪 === BROWSER THREAD CREATION TEST ===" . PHP_EOL;
echo "Thời gian: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// Bootstrap Laravel
try {
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo "✅ Laravel application đã được khởi tạo thành công" . PHP_EOL . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Lỗi khởi tạo Laravel: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// 1. KIỂM TRA USER TEST
echo "🔍 1. KIỂM TRA USER TEST" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

try {
    // Tìm hoặc tạo user test
    $testUser = User::where('email', 'test@mechamap.com')->first();

    if (!$testUser) {
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@mechamap.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Đã tạo test user mới: {$testUser->email}" . PHP_EOL;
    } else {
        echo "✅ Test user đã tồn tại: {$testUser->email}" . PHP_EOL;
    }

    echo "👤 User ID: {$testUser->id}" . PHP_EOL;
    echo "📧 Email: {$testUser->email}" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Lỗi kiểm tra user: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL;

// 2. KIỂM TRA FORUM VÀ CATEGORY
echo "🔍 2. KIỂM TRA FORUM VÀ CATEGORY" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

try {
    $testForum = Forum::first();
    $testCategory = Category::first();

    if (!$testForum || !$testCategory) {
        echo "❌ Không có forum hoặc category để test" . PHP_EOL;
        exit(1);
    }

    echo "🏛️  Test Forum: {$testForum->name} (ID: {$testForum->id})" . PHP_EOL;
    echo "📂 Test Category: {$testCategory->name} (ID: {$testCategory->id})" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Lỗi kiểm tra forum/category: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL;

// 3. TEST SCENARIOS
echo "🔍 3. TEST SCENARIOS" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$testScenarios = [
    [
        'name' => 'Tạo thread cơ bản',
        'data' => [
            'title' => 'Test Thread - ' . date('Y-m-d H:i:s'),
            'content' => 'Đây là nội dung test thread được tạo tự động.',
            'forum_id' => $testForum->id,
            'category_id' => $testCategory->id,
            'status' => 'Đề xuất'
        ]
    ],
    [
        'name' => 'Tạo thread với poll',
        'data' => [
            'title' => 'Test Thread With Poll - ' . date('Y-m-d H:i:s'),
            'content' => 'Thread test với poll.',
            'forum_id' => $testForum->id,
            'category_id' => $testCategory->id,
            'status' => 'Đề xuất',
            'has_poll' => true,
            'poll_question' => 'Bạn thích option nào?',
            'poll_options' => ['Option 1', 'Option 2', 'Option 3'],
            'poll_max_options' => 1,
            'poll_allow_change_vote' => true,
            'poll_show_votes_publicly' => true,
            'poll_allow_view_without_vote' => false
        ]
    ]
];

echo "📋 Tổng cộng " . count($testScenarios) . " scenarios:" . PHP_EOL;

foreach ($testScenarios as $index => $scenario) {
    echo PHP_EOL . "  " . ($index + 1) . ". {$scenario['name']}" . PHP_EOL;

    // Test validation data
    $requiredFields = ['title', 'content', 'forum_id', 'category_id'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!isset($scenario['data'][$field]) || empty($scenario['data'][$field])) {
            $missingFields[] = $field;
        }
    }

    if (empty($missingFields)) {
        echo "     ✅ Tất cả required fields có sẵn" . PHP_EOL;
    } else {
        echo "     ❌ Missing fields: " . implode(', ', $missingFields) . PHP_EOL;
    }

    // Simulate validation
    $validationRules = [
        'title' => 'max:255',
        'content' => 'required',
        'forum_id' => 'exists:forums,id',
        'category_id' => 'exists:categories,id'
    ];

    foreach ($validationRules as $field => $rule) {
        if (isset($scenario['data'][$field])) {
            $value = $scenario['data'][$field];

            switch ($rule) {
                case 'max:255':
                    if (strlen($value) <= 255) {
                        echo "     ✅ $field length valid" . PHP_EOL;
                    } else {
                        echo "     ❌ $field too long (" . strlen($value) . " chars)" . PHP_EOL;
                    }
                    break;

                case 'required':
                    if (!empty($value)) {
                        echo "     ✅ $field is not empty" . PHP_EOL;
                    } else {
                        echo "     ❌ $field is empty" . PHP_EOL;
                    }
                    break;

                case 'exists:forums,id':
                    if (Forum::find($value)) {
                        echo "     ✅ forum_id exists in database" . PHP_EOL;
                    } else {
                        echo "     ❌ forum_id does not exist" . PHP_EOL;
                    }
                    break;

                case 'exists:categories,id':
                    if (Category::find($value)) {
                        echo "     ✅ category_id exists in database" . PHP_EOL;
                    } else {
                        echo "     ❌ category_id does not exist" . PHP_EOL;
                    }
                    break;
            }
        }
    }

    // Poll validation nếu có
    if (isset($scenario['data']['has_poll']) && $scenario['data']['has_poll']) {
        echo "     🗳️  Poll validation:" . PHP_EOL;

        $pollFields = ['poll_question', 'poll_options', 'poll_max_options'];
        foreach ($pollFields as $field) {
            if (isset($scenario['data'][$field]) && !empty($scenario['data'][$field])) {
                echo "       ✅ $field" . PHP_EOL;
            } else {
                echo "       ❌ Missing: $field" . PHP_EOL;
            }
        }

        if (isset($scenario['data']['poll_options']) && is_array($scenario['data']['poll_options'])) {
            $optionCount = count($scenario['data']['poll_options']);
            if ($optionCount >= 2) {
                echo "       ✅ Poll has $optionCount options (minimum 2)" . PHP_EOL;
            } else {
                echo "       ❌ Poll needs at least 2 options (has $optionCount)" . PHP_EOL;
            }
        }
    }
}

echo PHP_EOL;

// 4. BROWSER TESTING URLS
echo "🔍 4. BROWSER TESTING URLS" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$baseUrl = 'https://mechamap.test';

$testUrls = [
    'Homepage' => $baseUrl,
    'Thread Index' => $baseUrl . '/threads',
    'Login Page' => $baseUrl . '/login',
    'Register Page' => $baseUrl . '/register',
    'Create Thread (no forum)' => $baseUrl . '/threads/create',
    'Create Thread (with forum)' => $baseUrl . '/threads/create?forum_id=' . $testForum->id,
    'Forum Page' => $baseUrl . '/forums/' . $testForum->slug,
    'Category Page' => $baseUrl . '/categories/' . $testCategory->slug,
];

echo "🌐 Test URLs to verify manually:" . PHP_EOL;
foreach ($testUrls as $name => $url) {
    echo "  📄 $name:" . PHP_EOL;
    echo "     $url" . PHP_EOL;
}

echo PHP_EOL;

// 5. FORM FIELDS TO CHECK
echo "🔍 5. FORM FIELDS TO CHECK" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

echo "📝 Required form fields trong /threads/create:" . PHP_EOL;

$formFields = [
    'title' => [
        'type' => 'input[type=text]',
        'required' => true,
        'validation' => 'required|string|max:255'
    ],
    'content' => [
        'type' => 'textarea',
        'required' => true,
        'validation' => 'required|string'
    ],
    'forum_id' => [
        'type' => 'select',
        'required' => true,
        'validation' => 'required|exists:forums,id'
    ],
    'category_id' => [
        'type' => 'select',
        'required' => true,
        'validation' => 'required|exists:categories,id'
    ],
    'status' => [
        'type' => 'input[type=text]',
        'required' => false,
        'validation' => 'nullable|string|max:255'
    ],
    'images' => [
        'type' => 'input[type=file]',
        'required' => false,
        'validation' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:5120'
    ],
    '_token' => [
        'type' => 'input[type=hidden]',
        'required' => true,
        'validation' => 'CSRF protection'
    ]
];

foreach ($formFields as $fieldName => $fieldInfo) {
    $required = $fieldInfo['required'] ? '✅ Required' : '⚪ Optional';
    echo "  📄 $fieldName ($fieldInfo[type]) - $required" . PHP_EOL;
    echo "     Validation: $fieldInfo[validation]" . PHP_EOL;
}

echo PHP_EOL;

// 6. POLL FIELDS
echo "🔍 6. POLL FIELDS (Optional)" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$pollFields = [
    'has_poll' => 'boolean',
    'poll_question' => 'required_if:has_poll,1|string|max:255',
    'poll_options[]' => 'required_if:has_poll,1|array|min:2',
    'poll_max_options' => 'required_if:has_poll,1|integer|min:1',
    'poll_allow_change_vote' => 'boolean',
    'poll_show_votes_publicly' => 'boolean',
    'poll_allow_view_without_vote' => 'boolean',
    'poll_close_after_days' => 'nullable|integer|min:1'
];

echo "🗳️  Poll form fields:" . PHP_EOL;
foreach ($pollFields as $field => $validation) {
    echo "  📊 $field" . PHP_EOL;
    echo "     Validation: $validation" . PHP_EOL;
}

echo PHP_EOL;

// 7. MANUAL TESTING CHECKLIST
echo "🔍 7. MANUAL TESTING CHECKLIST" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$testChecklist = [
    'Authentication' => [
        '🔐 User chưa login → redirect to /login',
        '✅ User đã login → có thể access /threads/create',
        '🔑 CSRF token được include trong form',
        '⚠️  Session timeout handling'
    ],
    'Form Validation' => [
        '❌ Submit form trống → hiển thị validation errors',
        '❌ Title quá dài (>255 chars) → validation error',
        '❌ Content trống → validation error',
        '❌ Forum/Category không tồn tại → validation error',
        '✅ Valid data → tạo thread thành công'
    ],
    'File Upload' => [
        '📄 Upload hình ảnh hợp lệ → success',
        '❌ Upload file không phải hình → validation error',
        '❌ Upload file quá lớn (>5MB) → validation error',
        '📁 Hình được lưu đúng thư mục storage/app/public/thread-images'
    ],
    'Poll Creation' => [
        '✅ Checkbox "Có poll" → hiển thị poll fields',
        '❌ Poll question trống → validation error',
        '❌ Poll options < 2 → validation error',
        '✅ Poll valid → tạo poll và poll_options'
    ],
    'Success Flow' => [
        '✅ Thread được tạo trong database',
        '✅ Slug được generate tự động',
        '✅ Media được link với thread',
        '✅ Activity log được tạo',
        '✅ Redirect đến thread view page',
        '✅ Success message được hiển thị'
    ],
    'Error Handling' => [
        '❌ Database error → rollback transaction',
        '❌ File upload error → cleanup và show error',
        '❌ Permission denied → 403 error',
        '⚠️  Server error → 500 error page'
    ]
];

foreach ($testChecklist as $category => $checks) {
    echo "📋 $category:" . PHP_EOL;
    foreach ($checks as $check) {
        echo "  $check" . PHP_EOL;
    }
    echo PHP_EOL;
}

// 8. TESTING CREDENTIALS
echo "🔍 8. TESTING CREDENTIALS" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

echo "🔑 Test User Credentials:" . PHP_EOL;
echo "  📧 Email: {$testUser->email}" . PHP_EOL;
echo "  🔐 Password: password123" . PHP_EOL;
echo "  👤 Name: {$testUser->name}" . PHP_EOL;

if (User::where('email', 'admin@mechamap.com')->exists()) {
    echo PHP_EOL . "👑 Admin Credentials:" . PHP_EOL;
    echo "  📧 Email: admin@mechamap.com" . PHP_EOL;
    echo "  🔐 Password: (check seeder hoặc .env)" . PHP_EOL;
}

echo PHP_EOL;

// 9. EXPECTED RESULTS
echo "🔍 9. EXPECTED RESULTS" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

echo "✅ Khi test thành công, kết quả mong đợi:" . PHP_EOL;
echo "  📝 Thread mới xuất hiện trong /threads" . PHP_EOL;
echo "  🔗 Thread có URL: /threads/{slug}" . PHP_EOL;
echo "  👤 Thread hiển thị đúng author" . PHP_EOL;
echo "  🏛️  Thread thuộc đúng forum" . PHP_EOL;
echo "  📂 Thread thuộc đúng category" . PHP_EOL;
echo "  📊 Thread có đúng status" . PHP_EOL;
echo "  🖼️  Media files hiển thị đúng" . PHP_EOL;
echo "  🗳️  Poll hoạt động (nếu có)" . PHP_EOL;
echo "  📈 View count = 0 ban đầu" . PHP_EOL;
echo "  💝 Interaction buttons hiển thị" . PHP_EOL;

echo PHP_EOL;

// SUMMARY
echo "📊 === SUMMARY ===" . PHP_EOL;
echo str_repeat("=", 50) . PHP_EOL;
echo "✅ Browser testing guide đã được tạo" . PHP_EOL;
echo "🔑 Test user credentials đã sẵn sàng" . PHP_EOL;
echo "📋 Manual testing checklist đã được chuẩn bị" . PHP_EOL;
echo "🌐 Test URLs đã được list ra" . PHP_EOL;
echo PHP_EOL;
echo "🚀 Bước tiếp theo:" . PHP_EOL;
echo "  1. Mở browser đến https://mechamap.test/login" . PHP_EOL;
echo "  2. Login với test@mechamap.com / password123" . PHP_EOL;
echo "  3. Đến https://mechamap.test/threads/create?forum_id=1" . PHP_EOL;
echo "  4. Test theo checklist ở trên" . PHP_EOL;
echo PHP_EOL;
echo "🕒 Hoàn thành lúc: " . date('Y-m-d H:i:s') . PHP_EOL;
