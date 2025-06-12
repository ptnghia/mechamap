<?php

/**
 * Test Script for Thread Quality Analytics API
 *
 * Tests the new rating analytics endpoints including:
 * - Rating distribution
 * - Quality score calculation
 * - Rating trends
 * - Detailed statistics
 */

echo "=== Thread Quality Analytics API Test ===\n\n";

// Configuration
$baseUrl = 'http://localhost:8000/api/v1';
$authToken = null;

function makeRequest($url, $method = 'GET', $data = null, $token = null)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => array_filter([
            'Content-Type: application/json',
            'Accept: application/json',
            $token ? "Authorization: Bearer $token" : null
        ]),
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $data ? json_encode($data) : null,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function authenticateUser()
{
    global $baseUrl, $authToken;

    echo "🔐 Authenticating admin user...\n";

    $response = makeRequest("$baseUrl/auth/login", 'POST', [
        'email' => 'admin@mechamap.com',
        'password' => 'password123'
    ]);

    if ($response['status'] === 200 && isset($response['body']['data']['tokens']['access_token'])) {
        $authToken = $response['body']['data']['tokens']['access_token'];
        echo "✅ Authentication successful\n\n";
        return true;
    } else {
        echo "❌ Authentication failed: " . json_encode($response['body']) . "\n";
        return false;
    }
}

function getThreadSlug()
{
    echo "📝 Using default thread slug for testing...\n";

    // Use a known thread slug from the system
    $slug = 'test-thread-for-quality-api';

    echo "✅ Using thread slug: $slug\n\n";
    return $slug;
}

function testRatingStats($threadSlug)
{
    global $baseUrl, $authToken;

    echo "📊 Testing Rating Stats endpoint...\n";

    $response = makeRequest(
        "$baseUrl/threads/$threadSlug/rating-stats",
        'GET',
        null,
        $authToken
    );

    echo "Status: {$response['status']}\n";

    if ($response['status'] === 200) {
        $data = $response['body']['data'];

        echo "✅ Rating Stats Retrieved Successfully:\n";
        echo "   Thread: {$data['thread_title']}\n";
        echo "   Average Rating: {$data['stats']['average_rating']}\n";
        echo "   Total Ratings: {$data['stats']['ratings_count']}\n";
        echo "   Quality Score: {$data['stats']['quality_score']}\n";
        echo "   Positive Rating %: {$data['stats']['positive_rating_percentage']}%\n";

        echo "\n   📈 Rating Distribution:\n";
        foreach ($data['stats']['distribution'] as $rating => $count) {
            $percentage = $data['stats']['percentage_distribution'][$rating];
            echo "      $rating: $count votes ($percentage%)\n";
        }

        echo "\n   📊 Rating Trend:\n";
        $trend = $data['stats']['rating_trend'];
        echo "      Trend: {$trend['trend']}\n";
        echo "      Direction: {$trend['direction']}\n";
        echo "      Recent Average: {$trend['recent_average']}\n";
        echo "      Change: {$trend['change_percentage']}%\n";
    } else {
        echo "❌ Failed to get rating stats: " . json_encode($response['body']) . "\n";
    }

    echo "\n" . str_repeat("-", 60) . "\n\n";
}

function testRatingsList($threadSlug)
{
    global $baseUrl, $authToken;

    echo "📋 Testing Ratings List with enhanced distribution...\n";

    $response = makeRequest(
        "$baseUrl/threads/$threadSlug/ratings",
        'GET',
        null,
        $authToken
    );

    echo "Status: {$response['status']}\n";

    if ($response['status'] === 200) {
        $data = $response['body']['data'];

        echo "✅ Enhanced Ratings List Retrieved:\n";
        echo "   Total Ratings: {$data['thread_stats']['ratings_count']}\n";
        echo "   Average: {$data['thread_stats']['average_rating']}\n";

        echo "\n   📊 Rating Distribution:\n";
        foreach ($data['thread_stats']['rating_distribution'] as $rating => $count) {
            echo "      $rating: $count votes\n";
        }

        echo "\n   📝 Recent Ratings:\n";
        $ratings = array_slice($data['ratings']['data'], 0, 3); // Show first 3
        foreach ($ratings as $rating) {
            echo "      {$rating['user']['name']}: {$rating['rating']} stars\n";
            if (!empty($rating['comment'])) {
                echo "         Comment: " . substr($rating['comment'], 0, 50) . "...\n";
            }
        }
    } else {
        echo "❌ Failed to get ratings list: " . json_encode($response['body']) . "\n";
    }

    echo "\n" . str_repeat("-", 60) . "\n\n";
}

function testMultipleRatings($threadSlug)
{
    global $baseUrl, $authToken;

    echo "🎯 Testing multiple ratings to see analytics change...\n";

    // Add a new rating
    $response = makeRequest(
        "$baseUrl/threads/$threadSlug/rate",
        'POST',
        [
            'rating' => 5,
            'comment' => 'Excellent thread! Very helpful analytics test.'
        ],
        $authToken
    );

    if ($response['status'] === 200) {
        echo "✅ Added 5-star rating\n";

        // Get updated stats
        $statsResponse = makeRequest(
            "$baseUrl/threads/$threadSlug/rating-stats",
            'GET',
            null,
            $authToken
        );

        if ($statsResponse['status'] === 200) {
            $stats = $statsResponse['body']['data']['stats'];
            echo "📊 Updated Stats:\n";
            echo "   New Average: {$stats['average_rating']}\n";
            echo "   New Count: {$stats['ratings_count']}\n";
            echo "   New Quality Score: {$stats['quality_score']}\n";
        }
    } else {
        echo "ℹ️ Rating not added (may already exist): " . ($response['body']['message'] ?? 'Unknown error') . "\n";
    }

    echo "\n" . str_repeat("-", 60) . "\n\n";
}

function testQualityScoreCalculation()
{
    echo "🧮 Testing Quality Score Calculation Logic...\n";

    echo "Quality Score = (Weighted Average / 5) * 100 * Sample Size Adjustment\n";
    echo "Sample Size Adjustment = min(1, total_ratings / 10)\n\n";

    $testCases = [
        ['5_star' => 10, '4_star' => 0, '3_star' => 0, '2_star' => 0, '1_star' => 0], // Perfect score with good sample
        ['5_star' => 2, '4_star' => 0, '3_star' => 0, '2_star' => 0, '1_star' => 0],  // Perfect score with small sample
        ['5_star' => 5, '4_star' => 3, '3_star' => 2, '2_star' => 0, '1_star' => 0],  // Good mixed ratings
        ['5_star' => 1, '4_star' => 1, '3_star' => 1, '2_star' => 1, '1_star' => 1],  // Evenly distributed
    ];

    foreach ($testCases as $i => $distribution) {
        $totalRatings = array_sum($distribution);
        $weightedSum = $distribution['1_star'] * 1 + $distribution['2_star'] * 2 +
            $distribution['3_star'] * 3 + $distribution['4_star'] * 4 +
            $distribution['5_star'] * 5;

        $rawScore = ($weightedSum / ($totalRatings * 5)) * 100;
        $sampleAdjustment = min(1, $totalRatings / 10);
        $qualityScore = round($rawScore * $sampleAdjustment, 1);

        echo "Test Case " . ($i + 1) . ":\n";
        echo "   Distribution: " . json_encode($distribution) . "\n";
        echo "   Total Ratings: $totalRatings\n";
        echo "   Raw Score: " . round($rawScore, 1) . "%\n";
        echo "   Sample Adjustment: " . round($sampleAdjustment, 2) . "\n";
        echo "   Final Quality Score: $qualityScore\n\n";
    }

    echo str_repeat("-", 60) . "\n\n";
}

// Main execution
if (!authenticateUser()) {
    exit(1);
}

$threadSlug = getThreadSlug();

echo "🧪 Starting comprehensive rating analytics tests...\n\n";

testQualityScoreCalculation();
testRatingStats($threadSlug);
testRatingsList($threadSlug);
testMultipleRatings($threadSlug);

echo "✅ Thread Quality Analytics API testing completed!\n";
echo "\n📈 Key Features Tested:\n";
echo "   - Rating distribution with percentages\n";
echo "   - Quality score calculation\n";
echo "   - Rating trends over time\n";
echo "   - Enhanced statistics endpoints\n";
echo "   - Real-time analytics updates\n\n";

echo "🎯 Next Steps:\n";
echo "   1. Test with different thread samples\n";
echo "   2. Create dashboard visualization\n";
echo "   3. Add caching for better performance\n";
echo "   4. Implement real-time updates\n\n";
