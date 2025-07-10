<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * 🔬 Advanced Thread Creation Flow Tester
 *
 * Script này sẽ test toàn bộ quy trình tạo thread một cách chi tiết,
 * bao gồm authentication, forum selection, và form submission.
 */

class AdvancedThreadCreationTester
{
    private $baseUrl;
    private $cookieJar;
    private $csrfToken;
    private $sessionCookies = [];

    public function __construct($baseUrl = 'http://127.0.0.1:8000')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'cookies');
    }

    public function runCompleteTest()
    {
        echo "🚀 Bắt đầu Advanced Thread Creation Test\n";
        echo "=" . str_repeat("=", 50) . "\n\n";

        try {
            // Step 1: Get CSRF token and session
            $this->step1_getInitialSession();

            // Step 2: Login
            $this->step2_login();

            // Step 3: Test forum selection
            $this->step3_testForumSelection();

            // Step 4: Test thread creation form
            $this->step4_testThreadCreationForm();

            // Step 5: Submit new thread
            $this->step5_submitThread();

            echo "\n✅ Tất cả tests hoàn thành thành công!\n";
        } catch (Exception $e) {
            echo "\n❌ Test thất bại: " . $e->getMessage() . "\n";
            echo "📍 Line: " . $e->getLine() . "\n";
        } finally {
            // Clean up
            if (file_exists($this->cookieJar)) {
                unlink($this->cookieJar);
            }
        }
    }

    private function step1_getInitialSession()
    {
        echo "📋 Step 1: Lấy session và CSRF token...\n";

        $response = $this->makeRequest('GET', '/login');

        if ($response['http_code'] !== 200) {
            throw new Exception("Không thể truy cập login page. HTTP Code: " . $response['http_code']);
        }

        // Extract CSRF token
        if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response['body'], $matches)) {
            $this->csrfToken = $matches[1];
            echo "   ✅ CSRF Token: " . substr($this->csrfToken, 0, 10) . "...\n";
        } else {
            throw new Exception("Không tìm thấy CSRF token trong login page");
        }

        echo "   ✅ Session cookies được thiết lập\n\n";
    }

    private function step2_login()
    {
        echo "🔑 Step 2: Đăng nhập...\n";

        $loginData = [
            '_token' => $this->csrfToken,
            'email' => 'leminh.cnc@gmail.com',
            'password' => 'password123',
            'remember' => '0'
        ];

        $response = $this->makeRequest('POST', '/login', $loginData);

        // Check for redirect to dashboard/home
        if ($response['http_code'] === 302) {
            $location = $response['redirect_location'] ?? '';
            echo "   ✅ Đăng nhập thành công! Redirect to: $location\n";
        } else {
            throw new Exception("Đăng nhập thất bại. HTTP Code: " . $response['http_code']);
        }

        echo "\n";
    }

    private function step3_testForumSelection()
    {
        echo "📂 Step 3: Test forum selection...\n";

        $response = $this->makeRequest('GET', '/create-thread');

        if ($response['http_code'] !== 200) {
            throw new Exception("Không thể truy cập forum selection. HTTP Code: " . $response['http_code']);
        }

        // Check if page contains forum list
        if (strpos($response['body'], 'forum') !== false || strpos($response['body'], 'Chọn') !== false) {
            echo "   ✅ Forum selection page hiển thị đúng\n";
        } else {
            echo "   ⚠️  Cảnh báo: Không tìm thấy forum selection content\n";
        }

        // Extract available forums
        if (preg_match_all('/value="(\d+)"[^>]*>([^<]+)</i', $response['body'], $matches, PREG_SET_ORDER)) {
            echo "   📋 Available forums:\n";
            foreach ($matches as $match) {
                echo "      - Forum ID: {$match[1]} - {$match[2]}\n";
            }
        }

        echo "\n";
    }

    private function step4_testThreadCreationForm()
    {
        echo "📝 Step 4: Test thread creation form...\n";

        // Test với forum_id
        $response = $this->makeRequest('GET', '/threads/create?forum_id=1');

        if ($response['http_code'] !== 200) {
            throw new Exception("Không thể truy cập thread creation form. HTTP Code: " . $response['http_code']);
        }

        // Check for form elements
        $hasTitle = strpos($response['body'], 'name="title"') !== false;
        $hasContent = strpos($response['body'], 'name="content"') !== false;
        $hasSubmit = strpos($response['body'], 'type="submit"') !== false;

        echo "   📋 Form elements check:\n";
        echo "      - Title field: " . ($hasTitle ? "✅" : "❌") . "\n";
        echo "      - Content field: " . ($hasContent ? "✅" : "❌") . "\n";
        echo "      - Submit button: " . ($hasSubmit ? "✅" : "❌") . "\n";

        if (!$hasTitle || !$hasContent || !$hasSubmit) {
            throw new Exception("Thread creation form thiếu các elements cần thiết");
        }

        // Extract new CSRF token from form
        if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response['body'], $matches)) {
            $this->csrfToken = $matches[1];
        }

        echo "\n";
    }

    private function step5_submitThread()
    {
        echo "💾 Step 5: Submit thread...\n";

        $threadData = [
            '_token' => $this->csrfToken,
            'title' => 'Test Thread từ Advanced Tester - ' . date('Y-m-d H:i:s'),
            'content' => 'Đây là nội dung test thread được tạo bởi Advanced Thread Creation Tester. ' .
                'Thời gian tạo: ' . date('Y-m-d H:i:s') . '. ' .
                'Mục đích: Kiểm tra quy trình tạo thread hoàn chỉnh.',
            'forum_id' => '1',
            'tags' => 'test,automation,mechamap',
            'is_pinned' => '0',
            'is_locked' => '0',
            'visibility' => 'public'
        ];

        $response = $this->makeRequest('POST', '/threads', $threadData);

        // Check response
        if ($response['http_code'] === 302) {
            $location = $response['redirect_location'] ?? '';
            echo "   ✅ Thread được tạo thành công!\n";
            echo "   🔗 Redirect to: $location\n";

            // Extract thread ID from redirect URL
            if (preg_match('/threads\/(\d+)/', $location, $matches)) {
                $threadId = $matches[1];
                echo "   🆔 Thread ID: $threadId\n";

                // Verify thread exists
                $this->verifyThreadExists($threadId);
            }
        } elseif ($response['http_code'] === 422) {
            echo "   ❌ Validation errors:\n";
            $this->parseValidationErrors($response['body']);
        } else {
            throw new Exception("Thread creation failed. HTTP Code: " . $response['http_code']);
        }

        echo "\n";
    }

    private function verifyThreadExists($threadId)
    {
        echo "🔍 Verifying thread exists...\n";

        $response = $this->makeRequest('GET', "/threads/$threadId");

        if ($response['http_code'] === 200) {
            echo "   ✅ Thread hiển thị thành công!\n";

            // Check for thread content
            if (strpos($response['body'], 'Advanced Tester') !== false) {
                echo "   ✅ Thread content chính xác\n";
            }
        } else {
            echo "   ❌ Không thể xem thread. HTTP Code: " . $response['http_code'] . "\n";
        }
    }

    private function parseValidationErrors($html)
    {
        // Try to extract validation errors from response
        if (preg_match_all('/<div[^>]*class="[^"]*alert[^"]*"[^>]*>([^<]+)<\/div>/', $html, $matches)) {
            foreach ($matches[1] as $error) {
                echo "      - " . trim(strip_tags($error)) . "\n";
            }
        } else {
            echo "      - Không thể parse validation errors\n";
        }
    }

    private function makeRequest($method, $url, $data = null)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_COOKIEJAR => $this->cookieJar,
            CURLOPT_COOKIEFILE => $this->cookieJar,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => 'AdvancedThreadTester/1.0',
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        if (curl_error($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        // Extract redirect location
        $redirectLocation = null;
        if (preg_match('/Location: (.+)/', $headers, $matches)) {
            $redirectLocation = trim($matches[1]);
        }

        return [
            'http_code' => $httpCode,
            'headers' => $headers,
            'body' => $body,
            'redirect_location' => $redirectLocation
        ];
    }
}

// Run the test
$tester = new AdvancedThreadCreationTester();
$tester->runCompleteTest();

echo "\n🎯 Test hoàn thành! Kiểm tra kết quả ở trên.\n";
echo "📝 Để test thủ công, mở browser và làm theo hướng dẫn trong manual_browser_test_guide.md\n";
