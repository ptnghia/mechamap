<?php

/**
 * Fix UI/Common Keys in Header
 * Replace ui/common.* keys with common.* keys in header.blade.php
 */

echo "🔧 Fixing UI/Common Keys in Header\n";
echo "==================================\n\n";

$file = 'resources/views/components/header.blade.php';

if (!file_exists($file)) {
    echo "❌ File not found: {$file}\n";
    exit;
}

echo "🔍 Processing: {$file}\n";

// Backup
$backupFile = $file . '.backup.' . date('Y-m-d-H-i-s');
copy($file, $backupFile);
echo "💾 Backup created: {$backupFile}\n";

$content = file_get_contents($file);
$originalContent = $content;

// Specific replacements for ui/common keys
$replacements = [
    // Technical resources
    "__('ui/common.technical_resources')" => "__('common.technical.resources')",
    "__('ui/common.technical_database')" => "__('common.technical.database')",
    "__('ui/common.materials_database')" => "__('common.technical.materials_database')",
    "__('ui/common.engineering_standards')" => "__('common.technical.engineering_standards')",
    "__('ui/common.manufacturing_processes')" => "__('common.technical.manufacturing_processes')",
    "__('ui/common.design_resources')" => "__('common.technical.design_resources')",
    "__('ui/common.cad_library')" => "__('common.technical.cad_library')",
    "__('ui/common.technical_drawings')" => "__('common.technical.technical_drawings')",
    "__('ui/common.tools_calculators')" => "__('common.technical.tools_calculators')",
    "__('ui/common.material_cost_calculator')" => "__('common.technical.material_cost_calculator')",
    "__('ui/common.process_selector')" => "__('common.technical.process_selector')",
    "__('ui/common.standards_compliance')" => "__('common.technical.standards_compliance')",

    // Knowledge
    "__('ui/common.knowledge')" => "__('common.knowledge.title')",
    "__('ui/common.learning_resources')" => "__('common.knowledge.learning_resources')",
    "__('ui/common.knowledge_base')" => "__('common.knowledge.knowledge_base')",
    "__('ui/common.tutorials_guides')" => "__('common.knowledge.tutorials_guides')",
    "__('ui/common.technical_documentation')" => "__('common.knowledge.technical_documentation')",
    "__('ui/common.industry_updates')" => "__('common.knowledge.industry_updates')",
    "__('ui/common.industry_news')" => "__('common.knowledge.industry_news')",
    "__('ui/common.whats_new')" => "__('common.knowledge.whats_new')",
    "__('ui/common.industry_reports')" => "__('common.knowledge.industry_reports')",
];

$changeCount = 0;
foreach ($replacements as $search => $replace) {
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        $changeCount++;
        echo "  ✅ Replaced: {$search}\n";
    }
}

// Write back
if ($content !== $originalContent && $changeCount > 0) {
    file_put_contents($file, $content);
    echo "\n✅ Made {$changeCount} replacements in {$file}\n";
} else {
    echo "\n➖ No changes needed\n";
    unlink($backupFile);
}

echo "\n🎯 Next: Add missing keys to common.php files\n";

?>
