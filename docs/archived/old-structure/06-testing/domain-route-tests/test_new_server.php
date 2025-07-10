<?php

/**
 * Test Thread Creation Process trên Server Port 8001
 * Kiểm tra quy trình tạo threads sau khi restart server
 */

echo "🔍 Test Thread Creation trên Server Mới (Port 8001)\n";
echo "====================================================\n\n";

$baseUrl = 'http://127.0.0.1:8001';

// Test các URL quan trọng
$testUrls = [
    'Server Root' => $baseUrl,
    'Login Page' => $baseUrl . '/login',
    'Forum Selection' => $baseUrl . '/create-thread',
    'Threads Index' => $baseUrl . '/threads',
    'Threads Create' => $baseUrl . '/threads/create',
    'Threads Create with Forum' => $baseUrl . '/threads/create?forum_id=1',
];

foreach ($testUrls as $name => $url) {
    echo "🧪 Test: $name\n";
    echo "   URL: $url\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MechaMap Testing Bot');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

    if (curl_errno($ch)) {
        echo "   ❌ Error: " . curl_error($ch) . "\n";
    } else {
        switch ($httpCode) {
            case 200:
                echo "   ✅ HTTP 200 - OK\n";
                // Kiểm tra có content cụ thể không
                if (strpos($response, 'Laravel') !== false) {
                    echo "   📋 Laravel app detected\n";
                }
                if (strpos($response, 'Tạo Thread') !== false) {
                    echo "   📝 Thread creation form found\n";
                }
                if (strpos($response, 'Chọn Forum') !== false) {
                    echo "   🏷️  Forum selection form found\n";
                }
                break;
            case 302:
                echo "   ↩️  HTTP 302 - Redirect\n";
                if ($redirectUrl) {
                    echo "   🔗 Redirect to: $redirectUrl\n";
                }
                break;
            case 404:
                echo "   ❌ HTTP 404 - Not Found\n";
                break;
            case 500:
                echo "   💥 HTTP 500 - Server Error\n";
                break;
            default:
                echo "   ⚠️  HTTP $httpCode\n";
        }
    }

    curl_close($ch);
    echo "\n";
}

// Test với session/authentication
echo "🔐 Test Authentication Flow\n";
echo "==========================\n\n";

// 1. Get login page để lấy CSRF token
echo "📋 Step 1: Get login form...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/mechamap_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/mechamap_cookies.txt');

$loginPage = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "   ✅ Login page loaded\n";

    // Extract CSRF token
    if (preg_match('/<meta name="csrf-token" content="([^"]*)"/', $loginPage, $matches)) {
        $csrfToken = $matches[1];
        echo "   🔑 CSRF token found: " . substr($csrfToken, 0, 20) . "...\n";

        // 2. Attempt login
        echo "\n📋 Step 2: Attempt login...\n";
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            '_token' => $csrfToken,
            'email' => 'leminh.cnc@gmail.com',
            'password' => 'password123',
            'remember' => '1'
        ]));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $loginResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

        if ($httpCode === 302 && strpos($redirectUrl, '/dashboard') !== false) {
            echo "   ✅ Login successful - redirected to dashboard\n";

            // 3. Test protected routes
            echo "\n📋 Step 3: Test protected routes...\n";

            $protectedUrls = [
                'Dashboard' => $baseUrl . '/dashboard',
                'Create Thread Form' => $baseUrl . '/threads/create',
                'Forum Selection' => $baseUrl . '/create-thread',
            ];

            foreach ($protectedUrls as $name => $url) {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                echo "   🧪 $name: ";
                switch ($httpCode) {
                    case 200:
                        echo "✅ HTTP 200 - Accessible\n";
                        break;
                    case 302:
                        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
                        echo "↩️ HTTP 302 - Redirect to: $redirectUrl\n";
                        break;
                    case 404:
                        echo "❌ HTTP 404 - Not Found\n";
                        break;
                    default:
                        echo "⚠️ HTTP $httpCode\n";
                }
            }
        } else {
            echo "   ❌ Login failed - HTTP $httpCode\n";
            if ($redirectUrl) {
                echo "   🔗 Redirect: $redirectUrl\n";
            }
        }
    } else {
        echo "   ❌ CSRF token not found\n";
    }
} else {
    echo "   ❌ Cannot load login page - HTTP $httpCode\n";
}

curl_close($ch);

echo "\n📊 Summary\n";
echo "==========\n";
echo "✅ Để test manual, truy cập:\n";
echo "   🌐 http://127.0.0.1:8001\n";
echo "   🔐 Login: leminh.cnc@gmail.com / password123\n";
echo "   📝 Create Thread: http://127.0.0.1:8001/create-thread\n";
echo "   🎯 Direct Route: http://127.0.0.1:8001/threads/create\n\n";

echo "🚀 Test hoàn thành!\n";
