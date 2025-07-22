<?php

/**
 * Phase 2: Migrate to Helper Functions
 */

echo "🔧 Phase 2: Migrating to Helper Functions\n";
echo "========================================\n\n";

$migrations = [
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

$bladeFiles = glob('resources/views/**/*.blade.php');
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $changed = false;
    foreach ($migrations as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $changed = true;
        }
    }
    if ($changed) {
        file_put_contents($file, $content);
        echo "✅ Updated: $file\n";
    }
}
echo "✅ Phase 2 completed!\n";
