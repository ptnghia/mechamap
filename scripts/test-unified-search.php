<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Controllers\Api\UnifiedSearchController;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Create a test request
    $request = new Request(['q' => 'test', 'limit' => 5]);
    
    // Create controller instance
    $controller = new UnifiedSearchController();
    
    // Call search method
    $response = $controller->search($request);
    
    // Get response data
    $data = $response->getData(true);
    
    echo "=== Unified Search Test Results ===\n";
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "Query: " . $data['results']['meta']['query'] . "\n";
    echo "Total Results: " . $data['results']['meta']['total'] . "\n";
    
    echo "\nResults by Category:\n";
    foreach ($data['results']['meta']['categories'] as $category => $count) {
        echo "- {$category}: {$count} results\n";
    }
    
    if (!empty($data['results']['threads'])) {
        echo "\nSample Thread:\n";
        $thread = $data['results']['threads'][0];
        echo "- Title: " . $thread['title'] . "\n";
        echo "- Author: " . $thread['author']['name'] . "\n";
        echo "- URL: " . $thread['url'] . "\n";
    }
    
    if (!empty($data['results']['showcases'])) {
        echo "\nSample Showcase:\n";
        $showcase = $data['results']['showcases'][0];
        echo "- Title: " . $showcase['title'] . "\n";
        echo "- Author: " . $showcase['author']['name'] . "\n";
        echo "- URL: " . $showcase['url'] . "\n";
    }
    
    if (!empty($data['results']['products'])) {
        echo "\nSample Product:\n";
        $product = $data['results']['products'][0];
        echo "- Title: " . $product['title'] . "\n";
        echo "- Price: " . $product['price']['formatted'] . "\n";
        echo "- URL: " . $product['url'] . "\n";
    }
    
    if (!empty($data['results']['users'])) {
        echo "\nSample User:\n";
        $user = $data['results']['users'][0];
        echo "- Name: " . $user['name'] . "\n";
        echo "- Username: " . $user['username'] . "\n";
        echo "- URL: " . $user['url'] . "\n";
    }
    
    echo "\nAdvanced Search URL: " . $data['advanced_search_url'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
