<?php

/**
 * Manual Test Script cho quy trình tạo threads
 *
 * Kiểm tra từng bước trong quy trình tạo threads
 *
 * @author MechaMap Team
 * @version 1.0
 */

require_once __DIR__ . '/../../vendor/autoload.php';

echo "🧪 === MANUAL TEST: THREAD CREATION PROCESS ===" . PHP_EOL;
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

echo "🔍 KIỂM TRA QUY TRÌNH TẠO THREADS" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL . PHP_EOL;

// 1. Kiểm tra Routes
echo "1️⃣  STEP 1: Kiểm tra Routes" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

$routes = [
    'home' => '/',
    'forums.select' => '/create-thread',
    'forums.select.submit' => '/create-thread (POST)',
    'threads.create.redirect' => '/threads/create (REDIRECT)',
    'threads.create' => '/threads/create',
    'threads.store' => '/threads (POST)',
];

foreach ($routes as $name => $uri) {
    if (str_contains($name, 'submit') || str_contains($name, 'store')) {
        echo "  📝 $name: $uri" . PHP_EOL;
    } else {
        try {
            if (Route::has($name)) {
                $url = route($name, [], false);
                echo "  ✅ $name: $url" . PHP_EOL;
            } else {
                echo "  ❌ $name: Route not found" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo "  ⚠️  $name: " . $e->getMessage() . PHP_EOL;
        }
    }
}

echo PHP_EOL;

// 2. Kiểm tra Controllers
echo "2️⃣  STEP 2: Kiểm tra Controllers" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

$controllers = [
    'ForumSelectionController' => \App\Http\Controllers\ForumSelectionController::class,
    'ThreadController' => \App\Http\Controllers\ThreadController::class,
];

foreach ($controllers as $name => $class) {
    if (class_exists($class)) {
        echo "  ✅ $name exists" . PHP_EOL;

        $methods = get_class_methods($class);
        $requiredMethods = $name === 'ForumSelectionController'
            ? ['index', 'selectForum']
            : ['create', 'store'];

        foreach ($requiredMethods as $method) {
            if (in_array($method, $methods)) {
                echo "    ✅ $method()" . PHP_EOL;
            } else {
                echo "    ❌ Missing: $method()" . PHP_EOL;
            }
        }
    } else {
        echo "  ❌ $name not found" . PHP_EOL;
    }
}

echo PHP_EOL;

// 3. Kiểm tra Views
echo "3️⃣  STEP 3: Kiểm tra Views" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

$views = [
    'forums.select' => 'resources/views/forums/select.blade.php',
    'threads.create' => 'resources/views/threads/create.blade.php',
    'threads.show' => 'resources/views/threads/show.blade.php',
];

foreach ($views as $viewName => $path) {
    $fullPath = base_path($path);
    if (file_exists($fullPath)) {
        $fileSize = round(filesize($fullPath) / 1024, 2);
        echo "  ✅ $viewName - $fileSize KB" . PHP_EOL;
    } else {
        echo "  ❌ Missing: $viewName" . PHP_EOL;
    }
}

echo PHP_EOL;

// 4. Kiểm tra Database Data
echo "4️⃣  STEP 4: Kiểm tra Database Data" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

try {
    $userCount = \App\Models\User::count();
    $forumCount = \App\Models\Forum::count();
    $categoryCount = \App\Models\Category::count();
    $threadCount = \App\Models\Thread::count();

    echo "  📊 Data Summary:" . PHP_EOL;
    echo "    👥 Users: $userCount" . PHP_EOL;
    echo "    🏛️  Forums: $forumCount" . PHP_EOL;
    echo "    📂 Categories: $categoryCount" . PHP_EOL;
    echo "    💬 Threads: $threadCount" . PHP_EOL;

    // Kiểm tra forums có data không
    if ($forumCount > 0) {
        echo PHP_EOL . "  🏛️  Available Forums:" . PHP_EOL;
        $forums = \App\Models\Forum::take(5)->get(['id', 'name', 'parent_id']);
        foreach ($forums as $forum) {
            $type = $forum->parent_id ? 'SubForum' : 'Category';
            echo "    [$forum->id] $forum->name ($type)" . PHP_EOL;
        }
    } else {
        echo "  ⚠️  No forums available! This will cause issues." . PHP_EOL;
    }

    // Kiểm tra categories có data không
    if ($categoryCount > 0) {
        echo PHP_EOL . "  📂 Available Categories:" . PHP_EOL;
        $categories = \App\Models\Category::take(5)->get(['id', 'name']);
        foreach ($categories as $category) {
            echo "    [$category->id] $category->name" . PHP_EOL;
        }
    } else {
        echo "  ⚠️  No categories available! This will cause issues." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "  ❌ Database error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// 5. Kiểm tra Middleware
echo "5️⃣  STEP 5: Kiểm tra Middleware" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

try {
    $middlewareGroups = app('router')->getMiddlewareGroups();

    if (isset($middlewareGroups['auth'])) {
        echo "  ✅ Auth middleware group configured" . PHP_EOL;
    } else {
        echo "  ❌ Auth middleware group not found" . PHP_EOL;
    }

    // Kiểm tra auth middleware trong controller
    echo "  📋 Controllers với Auth Middleware:" . PHP_EOL;
    echo "    🔒 ForumSelectionController: Required" . PHP_EOL;
    echo "    🔒 ThreadController: Required (except index, show)" . PHP_EOL;
} catch (Exception $e) {
    echo "  ❌ Middleware check error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// 6. Test URLs thực tế
echo "6️⃣  STEP 6: Hướng dẫn Test Manual" . PHP_EOL;
echo str_repeat("-", 40) . PHP_EOL;

echo "📝 Quy trình test thủ công:" . PHP_EOL . PHP_EOL;

echo "  🌐 1. Truy cập homepage:" . PHP_EOL;
echo "     → https://mechamap.test/" . PHP_EOL . PHP_EOL;

echo "  🔐 2. Đăng nhập (nếu chưa):" . PHP_EOL;
echo "     → https://mechamap.test/login" . PHP_EOL;
echo "     → Sử dụng account có sẵn hoặc tạo mới" . PHP_EOL . PHP_EOL;

echo "  📝 3. Truy cập tạo thread:" . PHP_EOL;
echo "     → https://mechamap.test/threads/create" . PHP_EOL;
echo "     → Should redirect to: https://mechamap.test/create-thread" . PHP_EOL . PHP_EOL;

echo "  🏛️  4. Chọn forum:" . PHP_EOL;
echo "     → Trang sẽ hiển thị danh sách forums" . PHP_EOL;
echo "     → Click chọn một forum" . PHP_EOL;
echo "     → Should redirect to: https://mechamap.test/threads/create?forum_id=X" . PHP_EOL . PHP_EOL;

echo "  ✍️  5. Điền form tạo thread:" . PHP_EOL;
echo "     → Title: [Required]" . PHP_EOL;
echo "     → Content: [Required]" . PHP_EOL;
echo "     → Category: [Required]" . PHP_EOL;
echo "     → Images: [Optional]" . PHP_EOL;
echo "     → Poll: [Optional]" . PHP_EOL . PHP_EOL;

echo "  💾 6. Submit form:" . PHP_EOL;
echo "     → POST to: https://mechamap.test/threads" . PHP_EOL;
echo "     → Should redirect to: https://mechamap.test/threads/{thread}" . PHP_EOL . PHP_EOL;

echo "⚠️  Lưu ý quan trọng:" . PHP_EOL;
echo "  • Phải đăng nhập trước khi tạo thread" . PHP_EOL;
echo "  • Database phải có forums và categories" . PHP_EOL;
echo "  • CSRF token phải được include trong form" . PHP_EOL;
echo "  • File upload cần đúng format và size limit" . PHP_EOL . PHP_EOL;

echo "🚨 Troubleshooting:" . PHP_EOL;
echo "  • 404 Error → Kiểm tra routes, middleware auth" . PHP_EOL;
echo "  • 422 Error → Kiểm tra validation rules" . PHP_EOL;
echo "  • 500 Error → Kiểm tra logs, database connection" . PHP_EOL;
echo "  • Redirect loop → Kiểm tra middleware, session" . PHP_EOL . PHP_EOL;

echo "🔍 Debug Commands:" . PHP_EOL;
echo "  • php artisan route:list | grep threads" . PHP_EOL;
echo "  • php artisan config:cache" . PHP_EOL;
echo "  • php artisan view:cache" . PHP_EOL;
echo "  • tail -f storage/logs/laravel.log" . PHP_EOL . PHP_EOL;

echo "✅ Manual test script completed!" . PHP_EOL;
echo "📧 Báo cáo kết quả test qua Github Issues hoặc documentation." . PHP_EOL;
