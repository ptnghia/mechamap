<?php

require_once 'vendor/autoload.php';

// Database connection
$host = 'localhost';
$dbname = 'mechamap_backend';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

echo "=== MECHAMAP DATABASE COMPREHENSIVE AUDIT REPORT ===\n\n";

$tables = [
    'users' => 'User Management',
    'roles' => 'Role System', 
    'permissions' => 'Permission System',
    'model_has_roles' => 'User Role Assignments',
    'categories' => 'Forum Categories',
    'forums' => 'Forum Structure',
    'threads' => 'Forum Threads',
    'comments' => 'Forum Comments',
    'marketplace_products' => 'Marketplace Products',
    'marketplace_sellers' => 'Marketplace Sellers',
    'marketplace_orders' => 'Marketplace Orders',
    'marketplace_order_items' => 'Marketplace Order Items',
    'products' => 'Products',
    'product_categories' => 'Product Categories',
    'technical_products' => 'Technical Products',
    'orders' => 'Technical Orders',
    'order_items' => 'Technical Order Items',
    'payment_transactions' => 'Payment Transactions',
    'user_activities' => 'User Activities',
    'showcases' => 'User Showcases',
    'cad_files' => 'CAD Files',
    'technical_drawings' => 'Technical Drawings',
    'materials' => 'Materials Database',
    'engineering_standards' => 'Engineering Standards',
    'manufacturing_processes' => 'Manufacturing Processes',
    'countries' => 'Geographic Data',
    'regions' => 'Regional Data',
    'settings' => 'System Settings',
    'seo_settings' => 'SEO Configuration'
];

$totalRecords = 0;
$populatedTables = 0;
$emptyTables = 0;
$missingTables = 0;

echo "TABLE DATA AUDIT:\n";
echo str_repeat("-", 60) . "\n";

foreach($tables as $table => $description) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        $totalRecords += $count;
        
        if ($count > 0) {
            echo sprintf("✅ %-25s: %6s records\n", $description, number_format($count));
            $populatedTables++;
        } else {
            echo sprintf("❌ %-25s: %6s\n", $description, "EMPTY");
            $emptyTables++;
        }
    } catch (PDOException $e) {
        echo sprintf("⚠️  %-25s: %6s\n", $description, "NOT EXISTS");
        $missingTables++;
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "SUMMARY STATISTICS:\n";
echo str_repeat("-", 60) . "\n";
echo sprintf("Total Tables Checked: %d\n", count($tables));
echo sprintf("Tables with Data: %d\n", $populatedTables);
echo sprintf("Empty Tables: %d\n", $emptyTables);
echo sprintf("Missing Tables: %d\n", $missingTables);
echo sprintf("Total Records: %s\n", number_format($totalRecords));
echo sprintf("Data Coverage: %.1f%%\n", ($populatedTables / count($tables)) * 100);

// Check critical relationships
echo "\n" . str_repeat("=", 60) . "\n";
echo "RELATIONSHIP VALIDATION:\n";
echo str_repeat("-", 60) . "\n";

// Users with roles
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM model_has_roles");
    $userRoles = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();
    echo sprintf("✅ Users with Roles: %d/%d (%.1f%%)\n", $userRoles, $totalUsers, ($userRoles/$totalUsers)*100);
} catch (PDOException $e) {
    echo "❌ User-Role relationship: ERROR\n";
}

// Forums with categories
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM forums WHERE category_id IS NOT NULL");
    $forumsWithCat = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM forums");
    $totalForums = $stmt->fetchColumn();
    echo sprintf("✅ Forums with Categories: %d/%d (%.1f%%)\n", $forumsWithCat, $totalForums, ($forumsWithCat/$totalForums)*100);
} catch (PDOException $e) {
    echo "❌ Forum-Category relationship: ERROR\n";
}

// Threads with forums
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM threads WHERE forum_id IS NOT NULL");
    $threadsWithForum = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM threads");
    $totalThreads = $stmt->fetchColumn();
    echo sprintf("✅ Threads with Forums: %d/%d (%.1f%%)\n", $threadsWithForum, $totalThreads, ($threadsWithForum/$totalThreads)*100);
} catch (PDOException $e) {
    echo "❌ Thread-Forum relationship: ERROR\n";
}

// Comments with threads
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE thread_id IS NOT NULL");
    $commentsWithThread = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM comments");
    $totalComments = $stmt->fetchColumn();
    echo sprintf("✅ Comments with Threads: %d/%d (%.1f%%)\n", $commentsWithThread, $totalComments, ($commentsWithThread/$totalComments)*100);
} catch (PDOException $e) {
    echo "❌ Comment-Thread relationship: ERROR\n";
}

// System readiness assessment
echo "\n" . str_repeat("=", 60) . "\n";
echo "SYSTEM READINESS ASSESSMENT:\n";
echo str_repeat("-", 60) . "\n";

$coreSystemScore = 0;
$businessSystemScore = 0;
$contentSystemScore = 0;

// Core System (User, Auth, Permissions)
$coreComponents = ['users', 'roles', 'permissions', 'model_has_roles'];
$corePopulated = 0;
foreach($coreComponents as $component) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$component`");
        if ($stmt->fetchColumn() > 0) $corePopulated++;
    } catch (PDOException $e) {}
}
$coreSystemScore = ($corePopulated / count($coreComponents)) * 100;

// Business System (Marketplace, Orders, Products)
$businessComponents = ['marketplace_products', 'marketplace_sellers', 'marketplace_orders', 'products', 'orders', 'payment_transactions'];
$businessPopulated = 0;
foreach($businessComponents as $component) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$component`");
        if ($stmt->fetchColumn() > 0) $businessPopulated++;
    } catch (PDOException $e) {}
}
$businessSystemScore = ($businessPopulated / count($businessComponents)) * 100;

// Content System (Forums, Threads, Comments)
$contentComponents = ['categories', 'forums', 'threads', 'comments', 'user_activities'];
$contentPopulated = 0;
foreach($contentComponents as $component) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$component`");
        if ($stmt->fetchColumn() > 0) $contentPopulated++;
    } catch (PDOException $e) {}
}
$contentSystemScore = ($contentPopulated / count($contentComponents)) * 100;

echo sprintf("Core System (Auth/Users): %.1f%% Ready\n", $coreSystemScore);
echo sprintf("Business System (Marketplace): %.1f%% Ready\n", $businessSystemScore);
echo sprintf("Content System (Forums): %.1f%% Ready\n", $contentSystemScore);

$overallScore = ($coreSystemScore + $businessSystemScore + $contentSystemScore) / 3;
echo sprintf("\nOVERALL SYSTEM READINESS: %.1f%%\n", $overallScore);

if ($overallScore >= 80) {
    echo "🎉 EXCELLENT - System ready for production testing\n";
} elseif ($overallScore >= 60) {
    echo "✅ GOOD - System ready for development testing\n";
} elseif ($overallScore >= 40) {
    echo "⚠️  FAIR - System needs more data for proper testing\n";
} else {
    echo "❌ POOR - System requires significant data population\n";
}

// Recommendations
echo "\n" . str_repeat("=", 60) . "\n";
echo "RECOMMENDATIONS:\n";
echo str_repeat("-", 60) . "\n";

if ($emptyTables > 0) {
    echo "1. Populate " . $emptyTables . " empty tables with sample data\n";
}

if ($userRoles == 0) {
    echo "2. Assign roles to users for proper permission testing\n";
}

if ($businessSystemScore < 50) {
    echo "3. Create marketplace sample data for e-commerce testing\n";
}

if ($overallScore < 80) {
    echo "4. Run comprehensive seeders to achieve production-ready data\n";
}

echo "5. Verify all foreign key relationships are properly maintained\n";
echo "6. Test user journeys with populated data\n";

echo "\n" . str_repeat("=", 60) . "\n";
echo "AUDIT COMPLETED: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 60) . "\n";
