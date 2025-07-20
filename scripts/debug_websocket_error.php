<?php
/**
 * Debug WebSocket Connection Error
 * Error: Authentication failed: Request failed with status code 405/400
 */

echo "🔍 Debugging WebSocket Connection Error...\n\n";

// Read Laravel .env
$laravelEnv = '.env';
$laravelConfig = [];
if (file_exists($laravelEnv)) {
    $envContent = file_get_contents($laravelEnv);
    $envLines = explode("\n", $envContent);

    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
            [$key, $value] = explode('=', $line, 2);
            $laravelConfig[trim($key)] = trim($value);
        }
    }
}

// Read Realtime .env.production
$realtimeEnv = 'realtime-server/.env.production';
$realtimeConfig = [];
if (file_exists($realtimeEnv)) {
    $envContent = file_get_contents($realtimeEnv);
    $envLines = explode("\n", $envContent);

    foreach ($envLines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
            [$key, $value] = explode('=', $line, 2);
            $realtimeConfig[trim($key)] = trim($value);
        }
    }
}

echo "📊 Current Configuration Analysis:\n\n";

// 1. Check frontend URL vs WebSocket URL
$frontendUrl = $laravelConfig['FRONTEND_URL'] ?? 'NOT_SET';
$websocketUrl = $laravelConfig['WEBSOCKET_SERVER_URL'] ?? 'NOT_SET';

echo "🌐 URL Configuration:\n";
echo "Frontend URL: $frontendUrl\n";
echo "WebSocket URL: $websocketUrl\n";

if ($frontendUrl === 'https://mechamap.test' && $websocketUrl === 'https://realtime.mechamap.com') {
    echo "❌ MISMATCH: Development frontend trying to connect to production WebSocket!\n";
    echo "   This will cause CORS and authentication issues.\n\n";
} else {
    echo "✅ URL configuration looks consistent\n\n";
}

// 2. Check CORS configuration
$corsOrigins = $laravelConfig['CORS_ALLOWED_ORIGINS'] ?? '';
$realtimeCors = $realtimeConfig['CORS_ORIGIN'] ?? '';

echo "🔒 CORS Configuration:\n";
echo "Laravel CORS: $corsOrigins\n";
echo "Realtime CORS: $realtimeCors\n";

if (strpos($realtimeCors, 'mechamap.test') === false && $frontendUrl === 'https://mechamap.test') {
    echo "❌ CORS ISSUE: Realtime server doesn't allow mechamap.test domain!\n";
    echo "   Frontend: mechamap.test\n";
    echo "   Realtime allows: $realtimeCors\n\n";
} else {
    echo "✅ CORS configuration looks good\n\n";
}

// 3. Check API Key Hash
$laravelApiKeyHash = $laravelConfig['WEBSOCKET_API_KEY_HASH'] ?? '';
$realtimeApiKey = $realtimeConfig['LARAVEL_API_KEY'] ?? '';

echo "🔑 API Key Configuration:\n";
echo "Laravel API Key Hash: " . substr($laravelApiKeyHash, 0, 20) . "...\n";
echo "Realtime API Key: " . substr($realtimeApiKey, 0, 20) . "...\n";

// Verify hash
if (!empty($realtimeApiKey) && !empty($laravelApiKeyHash)) {
    $calculatedHash = hash('sha256', $realtimeApiKey);
    if ($calculatedHash === $laravelApiKeyHash) {
        echo "✅ API Key Hash matches\n\n";
    } else {
        echo "❌ API Key Hash MISMATCH!\n";
        echo "   Expected: $laravelApiKeyHash\n";
        echo "   Calculated: $calculatedHash\n\n";
    }
} else {
    echo "❌ API Key or Hash missing\n\n";
}

// 4. Check JWT Secret
$laravelJWT = $laravelConfig['JWT_SECRET'] ?? '';
$realtimeJWT = $realtimeConfig['JWT_SECRET'] ?? '';

echo "🎫 JWT Configuration:\n";
echo "Laravel JWT: " . substr($laravelJWT, 0, 20) . "...\n";
echo "Realtime JWT: " . substr($realtimeJWT, 0, 20) . "...\n";

if ($laravelJWT === $realtimeJWT && !empty($laravelJWT)) {
    echo "✅ JWT secrets match\n\n";
} else {
    echo "❌ JWT secrets don't match or missing\n\n";
}

// 5. Check if realtime server is running
echo "🔌 Server Status Check:\n";

$realtimeHost = $realtimeConfig['HOST'] ?? 'localhost';
$realtimePort = $realtimeConfig['PORT'] ?? '3000';

echo "Checking if realtime server is running on $realtimeHost:$realtimePort...\n";

$connection = @fsockopen($realtimeHost, $realtimePort, $errno, $errstr, 5);
if ($connection) {
    echo "✅ Realtime server is running on $realtimeHost:$realtimePort\n";
    fclose($connection);
} else {
    echo "❌ Realtime server is NOT running on $realtimeHost:$realtimePort\n";
    echo "   Error: $errstr ($errno)\n";
}

// Test external URL
echo "\nTesting external URL: $websocketUrl...\n";
$headers = @get_headers($websocketUrl . '/health', 1);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "✅ External WebSocket URL is accessible\n\n";
} else {
    echo "❌ External WebSocket URL is NOT accessible\n";
    echo "   This could be a network/DNS issue\n\n";
}

// Recommendations
echo str_repeat("=", 60) . "\n";
echo "🔧 RECOMMENDATIONS\n";
echo str_repeat("=", 60) . "\n";

if ($frontendUrl === 'https://mechamap.test' && $websocketUrl === 'https://realtime.mechamap.com') {
    echo "\n❗ MAIN ISSUE: Development/Production Mismatch\n";
    echo "\n🔧 SOLUTION OPTIONS:\n\n";

    echo "OPTION 1: Use Local Development Setup\n";
    echo "- Start local realtime server: npm run dev\n";
    echo "- Update Laravel .env:\n";
    echo "  WEBSOCKET_SERVER_URL=http://localhost:3000\n";
    echo "  WEBSOCKET_SERVER_HOST=localhost\n";
    echo "  WEBSOCKET_SERVER_PORT=3000\n";
    echo "  WEBSOCKET_SERVER_SECURE=false\n\n";

    echo "OPTION 2: Configure Production Server for Development\n";
    echo "- Update realtime server CORS to include mechamap.test\n";
    echo "- Add to realtime .env.production:\n";
    echo "  CORS_ORIGIN=https://mechamap.com,https://www.mechamap.com,https://mechamap.test\n\n";

    echo "OPTION 3: Use Production Frontend\n";
    echo "- Access via https://mechamap.com instead of mechamap.test\n";
    echo "- Update Laravel .env:\n";
    echo "  FRONTEND_URL=https://mechamap.com\n";
    echo "  SESSION_DOMAIN=.mechamap.com\n\n";
}

if (strpos($realtimeCors, 'mechamap.test') === false) {
    echo "🔧 CORS Fix for Development:\n";
    echo "Add mechamap.test to realtime server CORS:\n";
    echo "CORS_ORIGIN=https://mechamap.com,https://www.mechamap.com,https://mechamap.test,http://mechamap.test\n\n";
}

echo "🧪 TESTING COMMANDS:\n";
echo "1. Test realtime server health:\n";
echo "   curl http://localhost:3000/health\n";
echo "   curl https://realtime.mechamap.com/health\n\n";

echo "2. Test CORS headers:\n";
echo "   curl -H \"Origin: https://mechamap.test\" -I https://realtime.mechamap.com\n\n";

echo "3. Check WebSocket endpoint:\n";
echo "   curl -I https://realtime.mechamap.com/socket.io/\n\n";

echo "4. Start local development server:\n";
echo "   cd realtime-server\n";
echo "   npm run dev\n\n";

echo "✅ Debug analysis completed!\n";
