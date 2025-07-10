<?php

/**
 * Debug Cart Update Issue
 */

echo "🔍 Debug Cart Update Issue\n";
echo "Base URL: http://mechamap.test/api/v1\n\n";

$baseUrl = 'http://mechamap.test/api/v1';

function makeRequest($method, $endpoint, $data = null, $token = '') {
    global $baseUrl;

    $url = $baseUrl . $endpoint;
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    if ($token) {
        $headers[] = "Authorization: Bearer {$token}";
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'data' => json_decode($response, true),
        'raw' => $response
    ];
}

// 1. Login first
echo "🔐 1. Logging in...\n";
$loginData = [
    'email' => 'test.engineer@mechamap.com',
    'password' => 'password123'
];

$loginResponse = makeRequest('POST', '/auth/login', $loginData);
if ($loginResponse['status'] == 200 && $loginResponse['data']['success']) {
    $authToken = $loginResponse['data']['data']['tokens']['access_token'] ?? '';
    echo "   ✅ Login successful\n";
    echo "   🔑 Token: " . substr($authToken, 0, 20) . "...\n";
} else {
    echo "   ❌ Login failed: HTTP {$loginResponse['status']}\n";
    echo "   Response: " . json_encode($loginResponse['data']) . "\n";
    exit(1);
}

// 2. Get current cart
echo "\n🛒 2. Getting current cart...\n";
$cartResponse = makeRequest('GET', '/cart', null, $authToken);
echo "   Status: {$cartResponse['status']}\n";
if ($cartResponse['status'] == 200) {
    $cartItems = $cartResponse['data']['data']['items'] ?? [];
    echo "   ✅ Cart items: " . count($cartItems) . "\n";

    if (!empty($cartItems)) {
        $firstItem = $cartItems[0];
        echo "   📦 First item ID: {$firstItem['id']}\n";
        echo "   📦 Product: {$firstItem['product']['title']}\n";
        echo "   📦 Quantity: {$firstItem['quantity']}\n";
        echo "   📦 License: {$firstItem['license_type']}\n";

        // 3. Try to update this cart item
        echo "\n🔄 3. Updating cart item {$firstItem['id']}...\n";
        $updateData = [
            'quantity' => ($firstItem['quantity'] + 1),
            'license_type' => 'extended'
        ];

        $updateResponse = makeRequest('PUT', "/cart/{$firstItem['id']}", $updateData, $authToken);
        echo "   Status: {$updateResponse['status']}\n";
        echo "   Response: " . json_encode($updateResponse['data']) . "\n";

        if ($updateResponse['status'] != 200) {
            echo "   Raw response: {$updateResponse['raw']}\n";
        }
    } else {
        echo "   ⚠️ No cart items to update\n";
    }
} else {
    echo "   ❌ Get cart failed\n";
    echo "   Response: " . json_encode($cartResponse['data']) . "\n";
    echo "   Raw: {$cartResponse['raw']}\n";
}

echo "\n🏁 Debug completed!\n";
