<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔗 SOCIAL ACCOUNTS TABLE TEST\n";
echo "============================\n\n";

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\SocialAccount;
use App\Models\User;

// 1. SCHEMA VALIDATION
echo "📊 1. SCHEMA VALIDATION\n";
echo "======================\n";

$tableExists = Schema::hasTable('social_accounts');
echo "- social_accounts table: " . ($tableExists ? '✅ EXISTS' : '❌ MISSING') . "\n";

if ($tableExists) {
    $columns = Schema::getColumnListing('social_accounts');
    echo "  Columns: " . implode(', ', $columns) . "\n";

    // Check specific column types and constraints
    $expectedColumns = ['id', 'user_id', 'provider', 'provider_id', 'provider_avatar', 'provider_token', 'provider_refresh_token', 'created_at', 'updated_at'];
    $missingColumns = array_diff($expectedColumns, $columns);

    if (empty($missingColumns)) {
        echo "✅ All required columns present\n";
    } else {
        echo "❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
    }
}

// 2. MODEL RELATIONSHIP TEST
echo "\n🔗 2. MODEL RELATIONSHIP TEST\n";
echo "============================\n";

try {
    // Get a test user (or create one)
    $testUser = User::first();
    if (!$testUser) {
        $testUser = User::create([
            'name' => 'Test Engineer OAuth',
            'email' => 'oauth-test@mechamap.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        echo "✅ Created test user: {$testUser->name}\n";
    } else {
        echo "✅ Using existing user: {$testUser->name}\n";
    }

    // 3. SAMPLE DATA CREATION - PROFESSIONAL OAUTH PROVIDERS
    echo "\n🧪 3. ENGINEERING PROFESSIONAL OAUTH PROVIDERS\n";
    echo "=============================================\n";

    $engineeringProviders = [
        [
            'provider' => 'linkedin',
            'provider_id' => 'eng_12345',
            'provider_avatar' => 'https://media.licdn.com/profile/engineer_avatar.jpg',
            'provider_token' => 'linkedin_access_token_' . bin2hex(random_bytes(16)),
            'provider_refresh_token' => 'linkedin_refresh_token_' . bin2hex(random_bytes(16))
        ],
        [
            'provider' => 'github',
            'provider_id' => 'cad_developer_789',
            'provider_avatar' => 'https://github.com/avatars/cad_developer.png',
            'provider_token' => 'github_access_token_' . bin2hex(random_bytes(16)),
            'provider_refresh_token' => 'github_refresh_token_' . bin2hex(random_bytes(16))
        ],
        [
            'provider' => 'google',
            'provider_id' => 'mech_engineer_456',
            'provider_avatar' => 'https://lh3.googleusercontent.com/mech_engineer.jpg',
            'provider_token' => 'google_access_token_' . bin2hex(random_bytes(16)),
            'provider_refresh_token' => 'google_refresh_token_' . bin2hex(random_bytes(16))
        ]
    ];

    echo "Creating professional OAuth accounts for engineering:\n";
    foreach ($engineeringProviders as $providerData) {
        $socialAccount = SocialAccount::updateOrCreate(
            [
                'user_id' => $testUser->id,
                'provider' => $providerData['provider']
            ],
            $providerData
        );

        echo "✅ {$providerData['provider']}: {$providerData['provider_id']}\n";
    }

    // 4. RELATIONSHIP TESTING
    echo "\n🔗 4. RELATIONSHIP TESTING\n";
    echo "=========================\n";

    // Test User -> SocialAccounts relationship
    $userSocialAccounts = $testUser->socialAccounts ?? collect();
    echo "✅ User has " . $userSocialAccounts->count() . " social accounts\n";

    // Test SocialAccount -> User relationship
    $firstSocialAccount = SocialAccount::where('user_id', $testUser->id)->first();
    if ($firstSocialAccount) {
        $accountUser = $firstSocialAccount->user;
        echo "✅ Social account belongs to: {$accountUser->name}\n";
    }

    // 5. OAUTH INTEGRATION SCENARIOS
    echo "\n🎯 5. PROFESSIONAL OAUTH SCENARIOS\n";
    echo "==================================\n";

    // LinkedIn Professional Profile
    $linkedinAccount = SocialAccount::where('provider', 'linkedin')->first();
    if ($linkedinAccount) {
        echo "✅ LinkedIn Professional Integration:\n";
        echo "  - Provider ID: {$linkedinAccount->provider_id}\n";
        echo "  - User: {$linkedinAccount->user->name}\n";
        echo "  - Avatar: " . (str_contains($linkedinAccount->provider_avatar, 'licdn.com') ? 'Professional LinkedIn' : 'Standard') . "\n";
    }

    // GitHub Developer Integration
    $githubAccount = SocialAccount::where('provider', 'github')->first();
    if ($githubAccount) {
        echo "✅ GitHub Developer Integration:\n";
        echo "  - Provider ID: {$githubAccount->provider_id}\n";
        echo "  - User: {$githubAccount->user->name}\n";
        echo "  - Purpose: CAD script sharing, engineering tools\n";
    }

    // Google Professional Account
    $googleAccount = SocialAccount::where('provider', 'google')->first();
    if ($googleAccount) {
        echo "✅ Google Professional Integration:\n";
        echo "  - Provider ID: {$googleAccount->provider_id}\n";
        echo "  - User: {$googleAccount->user->name}\n";
        echo "  - Purpose: Engineering collaboration, document sharing\n";
    }

    // 6. PERFORMANCE TESTING
    echo "\n⚡ 6. PERFORMANCE TESTING\n";
    echo "========================\n";

    $start = microtime(true);

    // Test social account lookup performance
    for ($i = 0; $i < 50; $i++) {
        SocialAccount::where('user_id', $testUser->id)->get();
    }

    $socialAccountQueryTime = (microtime(true) - $start) * 1000;
    echo "✅ 50 social account queries: " . round($socialAccountQueryTime, 2) . "ms\n";

    // Test user with social accounts query
    $start = microtime(true);

    for ($i = 0; $i < 50; $i++) {
        User::with('socialAccounts')->find($testUser->id);
    }

    $userWithSocialTime = (microtime(true) - $start) * 1000;
    echo "✅ 50 user+social queries: " . round($userWithSocialTime, 2) . "ms\n";

    // Test OAuth provider lookup
    $start = microtime(true);

    for ($i = 0; $i < 50; $i++) {
        SocialAccount::where('provider', 'linkedin')->where('provider_id', 'eng_12345')->first();
    }

    $providerLookupTime = (microtime(true) - $start) * 1000;
    echo "✅ 50 provider lookups: " . round($providerLookupTime, 2) . "ms\n";

    // 7. DATA SUMMARY
    echo "\n📊 7. DATA SUMMARY\n";
    echo "=================\n";
    echo "✅ Total Social Accounts: " . SocialAccount::count() . "\n";
    echo "✅ Users with OAuth: " . User::whereHas('socialAccounts')->count() . "\n";
    echo "✅ LinkedIn Accounts: " . SocialAccount::where('provider', 'linkedin')->count() . "\n";
    echo "✅ GitHub Accounts: " . SocialAccount::where('provider', 'github')->count() . "\n";
    echo "✅ Google Accounts: " . SocialAccount::where('provider', 'google')->count() . "\n";

    $avgPerformance = ($socialAccountQueryTime + $userWithSocialTime + $providerLookupTime) / 3;
    echo "\n🎯 AVERAGE PERFORMANCE: " . round($avgPerformance, 2) . "ms\n";
    echo "🎯 TARGET: <20ms - " . ($avgPerformance < 20 ? '✅ PASSED' : '❌ NEEDS OPTIMIZATION') . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎉 SOCIAL ACCOUNTS TABLE TEST COMPLETED!\n";
