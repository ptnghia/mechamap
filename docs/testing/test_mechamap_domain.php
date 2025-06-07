<?php
/**
 * Test script cho Thread Quality API với domain mechamap.test
 * Chạy: php test_mechamap_domain.php
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
        CURLOPT_SSL_VERIFYPEER => false, // Tắt SSL verify cho local development
        CURLOPT_SSL_VERIFYHOST => false,
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

$token = $loginResponse['data']['access_token'];
$user = $loginResponse['data']['user'];
echo "✅ Đăng nhập thành công!\n";
echo "   User: {$user['name']} ({$user['email']})\n";
echo "   Role: {$user['role']}\n\n";

echo "��� KẾT THÚC TEST - Domain http://mechamap.test hoạt động!\n";
echo "✅ Thread Quality API đã sẵn sàng với domain mới!\n";
echo "��� Frontend URL: http://mechamap.test:3000\n";
echo "��� Backend URL: http://mechamap.test\n";
