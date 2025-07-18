<?php
/**
 * Setup Database Tables for Sessions and Queues
 * Run this when switching from Redis to Database drivers
 */

echo "🔧 Setting up database tables for sessions and queues...\n\n";

// Check if we're in Laravel project
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from Laravel project root directory\n";
    exit(1);
}

// Commands to run
$commands = [
    // Create sessions table
    [
        'command' => 'php artisan session:table',
        'description' => 'Creating sessions table migration'
    ],
    
    // Create jobs table
    [
        'command' => 'php artisan queue:table',
        'description' => 'Creating jobs table migration'
    ],
    
    // Create failed jobs table
    [
        'command' => 'php artisan queue:failed-table',
        'description' => 'Creating failed jobs table migration'
    ],
    
    // Run migrations
    [
        'command' => 'php artisan migrate',
        'description' => 'Running database migrations'
    ]
];

foreach ($commands as $cmd) {
    echo "📝 {$cmd['description']}...\n";
    
    $output = [];
    $returnCode = 0;
    
    exec($cmd['command'] . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Success: " . implode("\n", $output) . "\n\n";
    } else {
        echo "⚠️  Output: " . implode("\n", $output) . "\n\n";
    }
}

echo "🎉 Database setup completed!\n\n";

echo "📋 NEXT STEPS:\n";
echo "1. Update .env file with database drivers:\n";
echo "   SESSION_DRIVER=database\n";
echo "   QUEUE_CONNECTION=database\n";
echo "   CACHE_STORE=file\n\n";

echo "2. Clear config cache:\n";
echo "   php artisan config:clear\n";
echo "   php artisan config:cache\n\n";

echo "3. Test the configuration:\n";
echo "   php artisan queue:work database --once\n\n";

echo "✅ Ready for production without Redis!\n";
