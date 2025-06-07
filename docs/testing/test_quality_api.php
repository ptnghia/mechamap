<?php

/**
 * Test script cho Thread Quality API endpoints
 * Chạy: php test_quality_api.php
 */

$baseUrl = 'http://127.0.0.1:8000/api/v1';

// Function để gửi HTTP request
function sendRequest($method, $url, $data = null, $headers = [])
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Function để test authentication với user có bookmarks
function getAuthToken()
{
    global $baseUrl;

    echo "=== Đăng nhập để lấy auth token ===\n";

    $response = sendRequest('POST', "$baseUrl/auth/login", [
        'email' => 'admin@mechamap.test',
        'password' => 'password'
    ]);

    if ($response['status'] === 200 && isset($response['data']['data']['tokens']['access_token'])) {
        echo "✅ Đăng nhập thành công\n";
        return $response['data']['data']['tokens']['access_token'];
    }

    echo "❌ Đăng nhập thất bại: " . json_encode($response) . "\n";
    return null;
}

// Function để lấy user có bookmarks
function getUserWithBookmarks()
{
    echo "\n=== Tìm user có bookmarks ===\n";

    // Sử dụng tinker để lấy user có bookmarks
    $command = 'cd /d d:/xampp/htdocs/laravel/mechamap_backend && php artisan tinker --execute="$user = App\\Models\\User::whereHas(\'bookmarks\')->first(); if($user) echo json_encode([\'id\' => $user->id, \'email\' => $user->email]); else echo \'null\';"';
    $output = shell_exec($command);

    if ($output && $output !== 'null') {
        $userData = json_decode(trim($output), true);
        if ($userData && isset($userData['id'])) {
            echo "✅ Tìm thấy user có bookmarks: {$userData['email']} (ID: {$userData['id']})\n";
            return $userData;
        }
    }

    echo "❌ Không tìm thấy user có bookmarks\n";
    return null;
}

// Function để test rating thread
function testRateThread($token, $threadSlug)
{
    global $baseUrl;

    echo "\n=== Test Rate Thread ===\n";
    echo "Thread Slug: $threadSlug\n";

    $response = sendRequest('POST', "$baseUrl/threads/$threadSlug/rate", [
        'rating' => 4,
        'review' => 'Great thread! Very helpful for mechanical engineering students.'
    ], [
        "Authorization: Bearer $token"
    ]);

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n";

    return $response['status'] === 200;
}

// Function để test bookmark thread
function testBookmarkThread($token, $threadSlug)
{
    global $baseUrl;

    echo "\n=== Test Bookmark Thread ===\n";
    echo "Thread Slug: $threadSlug\n";

    $response = sendRequest('POST', "$baseUrl/threads/$threadSlug/bookmark", [
        'folder' => 'API Testing',
        'notes' => 'Bookmarked during API testing - useful CAD tutorial'
    ], [
        "Authorization: Bearer $token"
    ]);

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n";

    return $response['status'] === 200;
}

// Function để test get user bookmarks
function testGetBookmarks($token)
{
    global $baseUrl;

    echo "\n=== Test Get User Bookmarks ===\n";

    $response = sendRequest('GET', "$baseUrl/user/bookmarks", null, [
        "Authorization: Bearer $token"
    ]);

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n";

    return $response['data'] ?? [];
}

// Function để test remove bookmark
function testRemoveBookmark($token, $threadSlug)
{
    global $baseUrl;

    echo "\n=== Test Remove Bookmark ===\n";
    echo "Thread Slug: $threadSlug\n";

    $response = sendRequest('DELETE', "$baseUrl/threads/$threadSlug/bookmark", null, [
        "Authorization: Bearer $token"
    ]);

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n";

    return $response['status'] === 200;
}

// Main testing flow
echo "🚀 Bắt đầu test Thread Quality API endpoints\n";
echo "==========================================\n";

// 1. Lấy auth token
$token = getAuthToken();
if (!$token) {
    echo "❌ Không thể lấy auth token. Dừng test.\n";
    exit(1);
}

// 2. Lấy thread slug để test
echo "\n=== Lấy thread để test ===\n";
$command = 'cd /d d:/xampp/htdocs/laravel/mechamap_backend && php artisan tinker --execute="$thread = App\\Models\\Thread::first(); if($thread) echo $thread->slug; else echo \'null\';"';
$threadSlug = trim(shell_exec($command));

if (!$threadSlug || $threadSlug === 'null') {
    echo "❌ Không tìm thấy thread để test\n";
    exit(1);
}

echo "✅ Sử dụng Thread Slug: $threadSlug\n";

// 3. Test rating thread
testRateThread($token, $threadSlug);

// 4. Test bookmark thread
testBookmarkThread($token, $threadSlug);

// 5. Test get bookmarks
$bookmarks = testGetBookmarks($token);

// 6. Test remove bookmark - sử dụng cùng thread đã bookmark ở bước 4
testRemoveBookmark($token, $threadSlug);

// 7. Test get bookmarks sau khi remove
echo "\n=== Test Get Bookmarks Sau Khi Remove ===\n";
testGetBookmarks($token);

echo "\n🎉 Hoàn thành test Thread Quality API endpoints!\n";
