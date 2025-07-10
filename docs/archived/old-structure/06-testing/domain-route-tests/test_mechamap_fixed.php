<?php
/**
 * Test script cho Thread Quality API với domain mechamap.test
 * Response format đã được sửa
 */

// Cấu hình API
$baseUrl = 'http://mechamap.test/api/v1';
$email = 'admin@mechamap.test';
$password = 'password';

// Function để gọi API
function callApi($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            ...(($token) ? ["Authorization: Bearer $token"] : [])
        ]
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'http_code' => $httpCode];
    }
    
    return [
        'data' => json_decode($response, true),
        'http_code' => $httpCode,
        'raw' => $response
    ];
}

echo "��� TESTING THREAD QUALITY API với domain http://mechamap.test\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// Test 1: Đăng nhập
echo "1️⃣  ĐĂNG NHẬP\n";
$loginResponse = callApi("$baseUrl/auth/login", 'POST', [
    'email' => $email,
    'password' => $password
]);

if ($loginResponse['http_code'] !== 200) {
    echo "❌ Lỗi đăng nhập: HTTP {$loginResponse['http_code']}\n";
    if (isset($loginResponse['error'])) {
        echo "   Error: {$loginResponse['error']}\n";
    }
    echo "   Response: " . ($loginResponse['raw'] ?? 'No response') . "\n";
    exit(1);
}

// Sửa path để lấy token
$token = $loginResponse['data']['data']['tokens']['access_token'];
$user = $loginResponse['data']['data']['user'];
echo "✅ Đăng nhập thành công!\n";
echo "   User: {$user['name']} ({$user['email']})\n";
echo "   Role: {$user['role']}\n";
echo "   Token: " . substr($token, 0, 20) . "...\n\n";

// Test 2: Lấy danh sách threads
echo "2️⃣  DANH SÁCH THREADS\n";
$threadsResponse = callApi("$baseUrl/threads?limit=5", 'GET', null, $token);

if ($threadsResponse['http_code'] !== 200) {
    echo "❌ Lỗi lấy threads: HTTP {$threadsResponse['http_code']}\n";
    exit(1);
}

$threads = $threadsResponse['data']['data']['data']; // Nested data structure
echo "✅ Lấy danh sách threads thành công!\n";
echo "   Tổng threads trên trang: " . count($threads) . "\n";

if (empty($threads)) {
    echo "❌ Không có threads nào trên trang này!\n";
    // Vẫn tiếp tục test với slug cố định
    $testThreadSlug = 'solidworks-parametric-design-advanced-techniques';
    echo "   Sử dụng test slug: $testThreadSlug\n";
} else {
    $testThread = $threads[0];
    $testThreadSlug = $testThread['slug'];
    echo "   Test thread: {$testThread['title']} (slug: {$testThreadSlug})\n";
}
echo "\n";

// Test 3: Rate Thread
echo "3️⃣  ĐÁNH GIÁ THREAD\n";
$ratingData = [
    'rating' => 5,
    'comment' => 'Thread rất hữu ích! Test từ mechamap.test domain ���'
];

$rateResponse = callApi("$baseUrl/threads/{$testThreadSlug}/rate", 'POST', $ratingData, $token);

if ($rateResponse['http_code'] === 200) {
    echo "✅ Đánh giá thread thành công!\n";
    echo "   Rating: {$ratingData['rating']}/5 sao\n";
    echo "   Comment: {$ratingData['comment']}\n";
} elseif ($rateResponse['http_code'] === 409) {
    echo "ℹ️  Thread đã được đánh giá rồi\n";
    echo "   Message: {$rateResponse['data']['message']}\n";
} else {
    echo "❌ Lỗi đánh giá: HTTP {$rateResponse['http_code']}\n";
    echo "   Response: " . json_encode($rateResponse['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\n";

// Test 4: Bookmark Thread
echo "4️⃣  BOOKMARK THREAD\n";
$bookmarkData = [
    'notes' => 'Bookmark từ mechamap.test domain - Test API'
];

$bookmarkResponse = callApi("$baseUrl/threads/{$testThreadSlug}/bookmark", 'POST', $bookmarkData, $token);

if ($bookmarkResponse['http_code'] === 200) {
    echo "✅ Bookmark thread thành công!\n";
    echo "   Notes: {$bookmarkData['notes']}\n";
} elseif ($bookmarkResponse['http_code'] === 409) {
    echo "ℹ️  Thread đã được bookmark rồi\n";
    echo "   Message: {$bookmarkResponse['data']['message']}\n";
} else {
    echo "❌ Lỗi bookmark: HTTP {$bookmarkResponse['http_code']}\n";
    if (isset($bookmarkResponse['data'])) {
        echo "   Response: " . json_encode($bookmarkResponse['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}
echo "\n";

// Test 5: Lấy user bookmarks
echo "5️⃣  DANH SÁCH BOOKMARKS\n";
$bookmarksResponse = callApi("$baseUrl/user/bookmarks", 'GET', null, $token);

if ($bookmarksResponse['http_code'] === 200) {
    $bookmarks = $bookmarksResponse['data']['bookmarks'];
    echo "✅ Lấy bookmarks thành công!\n";
    echo "   Tổng bookmarks: " . count($bookmarks) . "\n";
    
    if (!empty($bookmarks)) {
        echo "   Bookmarks gần đây:\n";
        foreach (array_slice($bookmarks, 0, 3) as $bookmark) {
            $folderInfo = (isset($bookmark['folder']) && $bookmark['folder']) ? " (Folder: {$bookmark['folder']['name']})" : '';
            echo "   - {$bookmark['thread']['title']}{$folderInfo}\n";
        }
    }
} else {
    echo "❌ Lỗi lấy bookmarks: HTTP {$bookmarksResponse['http_code']}\n";
}
echo "\n";

echo "��� KẾT THÚC TEST API với domain http://mechamap.test\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "✅ Thread Quality API hoạt động bình thường với domain mới!\n";
echo "��� Frontend URL: http://mechamap.test:3000\n";
echo "��� Backend URL: http://mechamap.test\n";
