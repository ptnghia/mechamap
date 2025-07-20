<?php
/**
 * Test WebSocket Connection to Production Realtime Server
 * Tests: https://realtime.mechamap.com
 */

echo "🔌 Testing WebSocket Connection to Production Server...\n\n";

$realtimeUrl = 'https://realtime.mechamap.com';
$tests = [];

// Test 1: Basic HTTP connectivity
echo "🌐 Test 1: Basic HTTP connectivity...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $realtimeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'MechaMap-Test/1.0');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Basic connectivity: OK (HTTP $httpCode)\n";
    $tests['basic_connectivity'] = true;
} else {
    echo "❌ Basic connectivity: FAILED (HTTP $httpCode)\n";
    if ($error) echo "   Error: $error\n";
    $tests['basic_connectivity'] = false;
}

// Test 2: Health endpoint
echo "\n🏥 Test 2: Health endpoint...\n";
$healthUrl = $realtimeUrl . '/health';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $healthUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Health endpoint: OK (HTTP $httpCode)\n";
    $healthData = json_decode($response, true);
    if ($healthData) {
        echo "   Status: " . ($healthData['status'] ?? 'unknown') . "\n";
        echo "   Uptime: " . ($healthData['uptime'] ?? 'unknown') . "\n";
    }
    $tests['health_endpoint'] = true;
} else {
    echo "❌ Health endpoint: FAILED (HTTP $httpCode)\n";
    $tests['health_endpoint'] = false;
}

// Test 3: Socket.IO endpoint
echo "\n🔌 Test 3: Socket.IO endpoint...\n";
$socketUrl = $realtimeUrl . '/socket.io/';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $socketUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 || $httpCode === 400) {
    echo "✅ Socket.IO endpoint: OK (HTTP $httpCode)\n";
    if (strpos($response, 'socket.io') !== false) {
        echo "   Socket.IO detected in response\n";
    }
    $tests['socketio_endpoint'] = true;
} else {
    echo "❌ Socket.IO endpoint: FAILED (HTTP $httpCode)\n";
    $tests['socketio_endpoint'] = false;
}

// Test 4: CORS headers
echo "\n🌍 Test 4: CORS headers...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $realtimeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Origin: https://mechamap.com',
    'Access-Control-Request-Method: GET',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (strpos($response, 'Access-Control-Allow-Origin') !== false) {
    echo "✅ CORS headers: Present\n";
    $tests['cors_headers'] = true;
} else {
    echo "⚠️  CORS headers: Not detected (may cause browser issues)\n";
    $tests['cors_headers'] = false;
}

// Test 5: SSL certificate
echo "\n🔒 Test 5: SSL certificate...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $realtimeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);
$sslError = curl_error($ch);
curl_close($ch);

if (empty($sslError)) {
    echo "✅ SSL certificate: Valid\n";
    $tests['ssl_certificate'] = true;
} else {
    echo "⚠️  SSL certificate: Issues detected\n";
    echo "   Error: $sslError\n";
    $tests['ssl_certificate'] = false;
}

// Test 6: WebSocket upgrade support
echo "\n⬆️  Test 6: WebSocket upgrade support...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $realtimeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Connection: Upgrade',
    'Upgrade: websocket',
    'Sec-WebSocket-Version: 13',
    'Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 101 || strpos($response, 'websocket') !== false) {
    echo "✅ WebSocket upgrade: Supported\n";
    $tests['websocket_upgrade'] = true;
} else {
    echo "⚠️  WebSocket upgrade: May not be supported (HTTP $httpCode)\n";
    $tests['websocket_upgrade'] = false;
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 TEST RESULTS SUMMARY\n";
echo str_repeat("=", 60) . "\n";

$passed = 0;
$total = count($tests);

foreach ($tests as $test => $result) {
    $status = $result ? '✅ PASS' : '❌ FAIL';
    $testName = str_replace('_', ' ', ucwords($test));
    echo sprintf("%-25s: %s\n", $testName, $status);
    if ($result) $passed++;
}

echo "\nOverall: $passed/$total tests passed\n";

if ($passed === $total) {
    echo "\n🎉 ALL TESTS PASSED!\n";
    echo "Realtime server is ready for production use.\n";
} elseif ($passed >= $total * 0.8) {
    echo "\n⚠️  MOSTLY WORKING\n";
    echo "Some issues detected but should work in most cases.\n";
} else {
    echo "\n❌ SIGNIFICANT ISSUES\n";
    echo "Multiple problems detected. WebSocket may not work properly.\n";
}

// Recommendations
echo "\n💡 RECOMMENDATIONS:\n";

if (!$tests['basic_connectivity']) {
    echo "- Check if realtime server is running\n";
    echo "- Verify domain DNS settings\n";
    echo "- Check firewall settings\n";
}

if (!$tests['cors_headers']) {
    echo "- Configure CORS to allow https://mechamap.com\n";
    echo "- Add proper Access-Control-Allow-Origin headers\n";
}

if (!$tests['ssl_certificate']) {
    echo "- Check SSL certificate validity\n";
    echo "- Ensure certificate covers realtime.mechamap.com\n";
}

if (!$tests['websocket_upgrade']) {
    echo "- Check if hosting supports WebSocket upgrades\n";
    echo "- Verify proxy/load balancer WebSocket configuration\n";
}

echo "\n🔧 NEXT STEPS:\n";
echo "1. Fix any failed tests above\n";
echo "2. Test WebSocket in browser:\n";
echo "   - Open https://mechamap.com\n";
echo "   - Login to any user account\n";
echo "   - Open Developer Tools (F12)\n";
echo "   - Check Console for WebSocket connection logs\n";
echo "3. Look for these messages:\n";
echo "   - 'MechaMap WebSocket: Connecting to https://realtime.mechamap.com'\n";
echo "   - 'WebSocket connected successfully'\n";

echo "\n📞 TROUBLESHOOTING:\n";
echo "If WebSocket still doesn't work:\n";
echo "1. Check browser console for errors\n";
echo "2. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "3. Check realtime server logs\n";
echo "4. Test with different browsers\n";
echo "5. Test from different networks\n";

echo "\n✅ WebSocket connectivity test completed!\n";
