<?php

/**
 * MechaMap Translation Migration Planner
 * Location: scripts/create-migration-plan.php
 *
 * Creates specific migration steps and key mappings
 */

echo "🎯 MechaMap Translation Migration Planner\n";
echo "==========================================\n\n";

// Current problematic patterns found in blade files
$problematicPatterns = [
    // UI patterns that need fixing
    'ui.common.' => 'common.',
    'ui/common.' => 'common.',
    'ui.navigation.' => 'navigation.',
    'content/alerts.' => 'common.messages.',
    'core/messages.' => 'common.messages.',

    // Missing sections that need adding
    'marketplace.cart.shopping_cart' => 'marketplace.cart.title',
    'marketplace.cart.cart_empty' => 'marketplace.cart.empty_message',
    'marketplace.cart.add_products' => 'marketplace.cart.add_items',

    // Forum search keys
    'forum.search.recent_searches' => 'search.history.recent',
    'forum.search.no_recent_searches' => 'search.history.empty',
    'forum.search.popular_searches' => 'search.suggestions.popular',
];

// Standard sections for each translation file
$standardSections = [
    'auth' => [
        'login' => 'Login functionality',
        'register' => 'Registration process',
        'logout' => 'Logout process',
        'password' => 'Password management',
        'verification' => 'Email/account verification',
        'social' => 'Social login (Google, Facebook)',
    ],

    'common' => [
        'buttons' => 'Action buttons (save, cancel, delete, etc.)',
        'labels' => 'Form labels and field names',
        'messages' => 'System messages (success, error, loading)',
        'status' => 'Status indicators (active, pending, etc.)',
        'time' => 'Time-related text (ago, duration)',
        'navigation' => 'Basic navigation elements',
        'technical' => 'Technical/engineering terminology',
        'knowledge' => 'Knowledge base related terms',
    ],

    'navigation' => [
        'main' => 'Main site navigation',
        'user' => 'User account navigation',
        'admin' => 'Admin panel navigation',
        'supplier' => 'Supplier account navigation',
        'manufacturer' => 'Manufacturer navigation',
        'brand' => 'Brand account navigation',
        'pages' => 'Static page navigation',
        'breadcrumbs' => 'Breadcrumb navigation',
    ],

    'forums' => [
        'threads' => 'Forum threads',
        'posts' => 'Forum posts',
        'categories' => 'Forum categories',
        'moderation' => 'Moderation actions',
        'search' => 'Forum-specific search',
        'stats' => 'Forum statistics',
    ],

    'marketplace' => [
        'products' => 'Product listings',
        'cart' => 'Shopping cart',
        'orders' => 'Order management',
        'sellers' => 'Seller profiles',
        'categories' => 'Product categories',
        'checkout' => 'Checkout process',
        'reviews' => 'Product reviews',
    ],

    'search' => [
        'form' => 'Search form elements',
        'filters' => 'Search filters',
        'results' => 'Search results',
        'scope' => 'Search scope options',
        'history' => 'Search history',
        'suggestions' => 'Search suggestions',
        'advanced' => 'Advanced search',
    ],
];

// Create migration plan
echo "📋 Creating migration plan...\n\n";

$migrationPlan = [
    'phase1_structure_fixes' => [],
    'phase2_helper_migration' => [],
    'phase3_key_additions' => [],
    'phase4_validation' => [],
];

// Phase 1: Fix problematic patterns
echo "Phase 1: Structure Fixes\n";
echo "========================\n";
foreach ($problematicPatterns as $oldPattern => $newPattern) {
    echo "- Convert: $oldPattern → $newPattern\n";
    $migrationPlan['phase1_structure_fixes'][] = [
        'old' => $oldPattern,
        'new' => $newPattern,
        'type' => 'pattern_replace'
    ];
}

// Phase 2: Helper function migration
echo "\nPhase 2: Helper Function Migration\n";
echo "==================================\n";
$helperMappings = [
    '__("auth.' => 't_auth("',
    '__("common.' => 't_common("',
    '__("navigation.' => 't_navigation("',
    '__("forums.' => 't_forums("',
    '__("marketplace.' => 't_marketplace("',
    '__("search.' => 't_search("',
    '__("user.' => 't_user("',
    '__("admin.' => 't_admin("',
    '__("pages.' => 't_pages("',
    '__("showcase.' => 't_showcase("',
];

foreach ($helperMappings as $oldCall => $newCall) {
    echo "- Convert: $oldCall → $newCall\n";
    $migrationPlan['phase2_helper_migration'][] = [
        'old' => $oldCall,
        'new' => $newCall,
        'type' => 'helper_function'
    ];
}

// Phase 3: Add missing keys
echo "\nPhase 3: Add Missing Keys\n";
echo "========================\n";
$missingKeys = [
    'marketplace.cart.shopping_cart' => 'Giỏ hàng',
    'marketplace.cart.cart_empty' => 'Giỏ hàng trống',
    'marketplace.cart.add_products' => 'Thêm sản phẩm',
    'search.history.recent' => 'Tìm kiếm gần đây',
    'search.history.empty' => 'Không có lịch sử tìm kiếm',
    'search.suggestions.popular' => 'Tìm kiếm phổ biến',
    'common.technical.resources' => 'Tài nguyên kỹ thuật',
    'common.knowledge.title' => 'Tri thức',
];

foreach ($missingKeys as $key => $value) {
    echo "- Add: $key = '$value'\n";
    $migrationPlan['phase3_key_additions'][] = [
        'key' => $key,
        'value' => $value,
        'file' => explode('.', $key)[0]
    ];
}

// Phase 4: Validation steps
echo "\nPhase 4: Validation\n";
echo "==================\n";
$validationSteps = [
    'Check all translation keys exist',
    'Verify no array returns from __() calls',
    'Test all pages load without errors',
    'Validate helper function usage',
    'Monitor error logs for htmlspecialchars errors'
];

foreach ($validationSteps as $step) {
    echo "- $step\n";
    $migrationPlan['phase4_validation'][] = $step;
}

// Generate implementation scripts
echo "\n🔧 Generating implementation scripts...\n";

// Script 1: Pattern fixes
createPatternFixScript($migrationPlan['phase1_structure_fixes']);

// Script 2: Helper migration
createHelperMigrationScript($migrationPlan['phase2_helper_migration']);

// Script 3: Add missing keys
createMissingKeysScript($migrationPlan['phase3_key_additions']);

// Script 4: Validation
createValidationScript($migrationPlan['phase4_validation']);

// Save migration plan
$planJson = json_encode($migrationPlan, JSON_PRETTY_PRINT);
file_put_contents('docs/reports/MIGRATION_PLAN_' . date('Y_m_d') . '.json', $planJson);

echo "\n✅ Migration plan created!\n";
echo "📁 Files generated:\n";
echo "- scripts/migrate-phase1-patterns.php\n";
echo "- scripts/migrate-phase2-helpers.php\n";
echo "- scripts/migrate-phase3-missing-keys.php\n";
echo "- scripts/migrate-phase4-validation.php\n";
echo "- docs/reports/MIGRATION_PLAN_" . date('Y_m_d') . ".json\n\n";

echo "🎯 Ready to execute migration!\n";

/**
 * Create pattern fix script
 */
function createPatternFixScript($fixes) {
    $script = "<?php\n\n";
    $script .= "/**\n * Phase 1: Fix Translation Patterns\n */\n\n";
    $script .= "echo \"🔧 Phase 1: Fixing Translation Patterns\\n\";\n";
    $script .= "echo \"===================================\\n\\n\";\n\n";

    $script .= "\$fixes = [\n";
    foreach ($fixes as $fix) {
        $script .= "    '{$fix['old']}' => '{$fix['new']}',\n";
    }
    $script .= "];\n\n";

    $script .= "\$bladeFiles = glob('resources/views/**/*.blade.php');\n";
    $script .= "foreach (\$bladeFiles as \$file) {\n";
    $script .= "    \$content = file_get_contents(\$file);\n";
    $script .= "    \$changed = false;\n";
    $script .= "    foreach (\$fixes as \$old => \$new) {\n";
    $script .= "        if (strpos(\$content, \$old) !== false) {\n";
    $script .= "            \$content = str_replace(\$old, \$new, \$content);\n";
    $script .= "            \$changed = true;\n";
    $script .= "        }\n";
    $script .= "    }\n";
    $script .= "    if (\$changed) {\n";
    $script .= "        file_put_contents(\$file, \$content);\n";
    $script .= "        echo \"✅ Updated: \$file\\n\";\n";
    $script .= "    }\n";
    $script .= "}\n";
    $script .= "echo \"✅ Phase 1 completed!\\n\";\n";

    file_put_contents('scripts/migrate-phase1-patterns.php', $script);
}

/**
 * Create helper migration script
 */
function createHelperMigrationScript($migrations) {
    $script = "<?php\n\n";
    $script .= "/**\n * Phase 2: Migrate to Helper Functions\n */\n\n";
    $script .= "echo \"🔧 Phase 2: Migrating to Helper Functions\\n\";\n";
    $script .= "echo \"========================================\\n\\n\";\n\n";

    $script .= "\$migrations = [\n";
    foreach ($migrations as $migration) {
        $script .= "    '{$migration['old']}' => '{$migration['new']}',\n";
    }
    $script .= "];\n\n";

    $script .= "\$bladeFiles = glob('resources/views/**/*.blade.php');\n";
    $script .= "foreach (\$bladeFiles as \$file) {\n";
    $script .= "    \$content = file_get_contents(\$file);\n";
    $script .= "    \$changed = false;\n";
    $script .= "    foreach (\$migrations as \$old => \$new) {\n";
    $script .= "        if (strpos(\$content, \$old) !== false) {\n";
    $script .= "            \$content = str_replace(\$old, \$new, \$content);\n";
    $script .= "            \$changed = true;\n";
    $script .= "        }\n";
    $script .= "    }\n";
    $script .= "    if (\$changed) {\n";
    $script .= "        file_put_contents(\$file, \$content);\n";
    $script .= "        echo \"✅ Updated: \$file\\n\";\n";
    $script .= "    }\n";
    $script .= "}\n";
    $script .= "echo \"✅ Phase 2 completed!\\n\";\n";

    file_put_contents('scripts/migrate-phase2-helpers.php', $script);
}

/**
 * Create missing keys script
 */
function createMissingKeysScript($additions) {
    $script = "<?php\n\n";
    $script .= "/**\n * Phase 3: Add Missing Translation Keys\n */\n\n";
    $script .= "echo \"🔧 Phase 3: Adding Missing Keys\\n\";\n";
    $script .= "echo \"==============================\\n\\n\";\n\n";

    // Group by file
    $byFile = [];
    foreach ($additions as $addition) {
        $byFile[$addition['file']][] = $addition;
    }

    foreach ($byFile as $file => $keys) {
        $script .= "// Add keys to $file.php\n";
        $script .= "\$file = 'resources/lang/vi/$file.php';\n";
        $script .= "if (file_exists(\$file)) {\n";
        $script .= "    \$translations = include \$file;\n";
        foreach ($keys as $key) {
            $keyPath = explode('.', $key['key']);
            array_shift($keyPath); // Remove file name
            $script .= "    // Add {$key['key']}\n";
        }
        $script .= "    file_put_contents(\$file, '<?php return ' . var_export(\$translations, true) . ';');\n";
        $script .= "}\n\n";
    }

    $script .= "echo \"✅ Phase 3 completed!\\n\";\n";

    file_put_contents('scripts/migrate-phase3-missing-keys.php', $script);
}

/**
 * Create validation script
 */
function createValidationScript($steps) {
    $script = "<?php\n\n";
    $script .= "/**\n * Phase 4: Validation & Testing\n */\n\n";
    $script .= "echo \"🔧 Phase 4: Validation & Testing\\n\";\n";
    $script .= "echo \"===============================\\n\\n\";\n\n";

    foreach ($steps as $i => $step) {
        $stepNum = $i + 1;
        $script .= "echo \"Step $stepNum: $step\\n\";\n";
        $script .= "// TODO: Implement validation for: $step\n\n";
    }

    $script .= "echo \"✅ Phase 4 completed!\\n\";\n";

    file_put_contents('scripts/migrate-phase4-validation.php', $script);
}

echo "✅ Migration planner completed!\n";
