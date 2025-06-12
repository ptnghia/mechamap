<?php
/**
 * Debug Order Details Endpoint
 * Test để xác định vấn đề với GET order details
 */

require_once __DIR__ . '/vendor/autoload.php';

$baseUrl = 'http://mechamap.test/api/v1';

function makeRequest($method, $url, $data = null, $token = null) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_filter([
            'Content-Type: application/json',
            'Accept: application/json',
            $token ? "Authorization: Bearer $token" : null
        ]),
        CURLOPT_POSTFIELDS => $data ? json_encode($data) : null,
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        'status_code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

function printResult($title, $result) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🔍 $title\n";
    echo str_repeat("=", 50) . "\n";
    echo "Status Code: " . $result['status_code'] . "\n";
    echo "Response: " . json_encode($result['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

echo "🚀 DEBUGGING ORDER DETAILS ENDPOINT\n";
echo "===================================\n";

// Step 1: Login để lấy token
echo "📝 Step 1: User Login...\n";
$loginResult = makeRequest('POST', "$baseUrl/auth/login", [
    'email' => 'clemens83@example.com',
    'password' => 'password'
]);

if ($loginResult['status_code'] !== 200) {
    echo "❌ Login failed!\n";
    printResult("Login Result", $loginResult);
    exit(1);
}

$token = $loginResult['body']['data']['tokens']['access_token'] ?? null;
if (!$token) {
    echo "❌ No access token in response!\n";
    printResult("Login Result", $loginResult);
    exit(1);
}

echo "✅ Login successful! Token: " . substr($token, 0, 20) . "...\n";

// Step 2: Tạo một order để test
echo "\n📝 Step 2: Creating test order...\n";

// Add item to cart first
$addCartResult = makeRequest('POST', "$baseUrl/cart", [
    'product_id' => 1,
    'license_type' => 'standard'
], $token);

if ($addCartResult['status_code'] === 200 || $addCartResult['status_code'] === 201) {
    echo "✅ Added item to cart\n";

    // Update cart prices if needed
    echo "📝 Updating cart prices...\n";
    $updatePricesResult = makeRequest('POST', "$baseUrl/cart/update-prices", [], $token);
    if ($updatePricesResult['status_code'] === 200) {
        echo "✅ Cart prices updated\n";
    } else {
        echo "⚠️  Price update result: " . $updatePricesResult['status_code'] . "\n";
    }
} else {
    echo "⚠️  Cart add result: " . $addCartResult['status_code'] . "\n";
}

// Create order
$orderResult = makeRequest('POST', "$baseUrl/orders", [
    'billing_info' => [
        'full_name' => 'Test User',
        'email' => 'user@mechamap.com',
        'phone' => '0123456789',
        'address' => '123 Test Street',
        'city' => 'Ho Chi Minh City',
        'country_code' => 'VN',
        'postal_code' => '70000'
    ],
    'notes' => 'Test order for debugging'
], $token);

printResult("Create Order", $orderResult);

if ($orderResult['status_code'] !== 201) {
    echo "❌ Failed to create order, testing with existing order...\n";
    $orderId = 1; // Fallback to order ID 1
} else {
    $orderId = $orderResult['body']['data']['order']['id'];
    echo "✅ Created order with ID: $orderId\n";
}

// Step 3: Test GET order details
echo "\n📝 Step 3: Testing GET order details...\n";

$getOrderResult = makeRequest('GET', "$baseUrl/orders/$orderId", null, $token);
printResult("Get Order Details", $getOrderResult);

// Step 4: Test với different order IDs
echo "\n📝 Step 4: Testing with different order IDs...\n";

for ($testOrderId = 1; $testOrderId <= 3; $testOrderId++) {
    echo "\nTesting Order ID: $testOrderId\n";
    $testResult = makeRequest('GET', "$baseUrl/orders/$testOrderId", null, $token);
    echo "Status: " . $testResult['status_code'];

    if ($testResult['status_code'] === 200) {
        echo " ✅ SUCCESS";
        $orderData = $testResult['body']['data']['order'] ?? null;
        if ($orderData) {
            echo " - Order #{$orderData['order_number']}, Status: {$orderData['status']}";
            echo ", Items: " . count($orderData['items'] ?? []);
        }
    } elseif ($testResult['status_code'] === 404) {
        echo " ⚠️  NOT FOUND";
    } else {
        echo " ❌ ERROR";
        if ($testResult['body']['message'] ?? null) {
            echo " - " . $testResult['body']['message'];
        }
    }
    echo "\n";
}

// Step 5: Test get user's orders list
echo "\n📝 Step 5: Testing get orders list...\n";

$ordersListResult = makeRequest('GET', "$baseUrl/orders", null, $token);
printResult("Get Orders List", $ordersListResult);

if ($ordersListResult['status_code'] === 200) {
    $orders = $ordersListResult['body']['data']['data'] ?? [];
    echo "\n📊 Found " . count($orders) . " orders for user\n";

    foreach ($orders as $order) {
        echo "- Order #{$order['order_number']} (ID: {$order['id']}) - Status: {$order['status']}\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 DEBUGGING COMPLETED\n";
echo str_repeat("=", 50) . "\n";
