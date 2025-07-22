<?php

/**
 * Quick Fix for htmlspecialchars() errors
 * This will temporarily resolve the most critical issues
 */

echo "🚀 Applying Quick Fixes...\n";

// Quick fix 1: Replace problematic navigation array access
$navFilesEn = "resources/lang/en/navigation.php";
$navFilesVi = "resources/lang/vi/navigation.php";

if (file_exists($navFilesEn)) {
    $content = file_get_contents($navFilesEn);
    // Comment out problematic array structures temporarily
    $content = str_replace(
        "'add_menu' => [",
        "// 'add_menu' => [ // TEMPORARILY DISABLED",
        $content
    );
    file_put_contents($navFilesEn, $content);
    echo "✅ Temporarily fixed EN navigation\n";
}

if (file_exists($navFilesVi)) {
    $content = file_get_contents($navFilesVi);
    $content = str_replace(
        "'add_menu' => [",
        "// 'add_menu' => [ // TEMPORARILY DISABLED",
        $content
    );
    file_put_contents($navFilesVi, $content);
    echo "✅ Temporarily fixed VI navigation\n";
}

echo "\n🎯 Quick fixes applied! Try refreshing your website now.\n";
echo "📝 Note: These are temporary fixes. Full restructuring needed later.\n";

?>