<?php

/**
 * MechaMap Report Generator Template
 * Location: scripts/generate-report-template.php
 *
 * Use this template to create new report generation scripts
 */

echo "📊 MechaMap Report Generator\n";
echo "============================\n\n";

// Configuration
$reportTitle = "Project Status Report";
$reportDate = date('Y-m-d H:i:s');
$outputDir = 'docs/reports/';
$reportFileName = strtoupper(str_replace(' ', '_', $reportTitle)) . '_' . date('Y_m_d') . '.md';
$reportPath = $outputDir . $reportFileName;

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "✅ Created directory: $outputDir\n";
}

// Report content template
$reportContent = "# $reportTitle\n";
$reportContent .= "*Generated: $reportDate*\n\n";

$reportContent .= "## 📋 **Overview**\n";
$reportContent .= "Brief description of what this report covers.\n\n";

$reportContent .= "## ✅ **Completed Items**\n";
$reportContent .= "- Item 1\n";
$reportContent .= "- Item 2\n\n";

$reportContent .= "## ⚠️ **Issues Found**\n";
$reportContent .= "- Issue 1\n";
$reportContent .= "- Issue 2\n\n";

$reportContent .= "## 📈 **Metrics**\n";
$reportContent .= "| Metric | Value |\n";
$reportContent .= "|--------|-------|\n";
$reportContent .= "| Example | 100% |\n\n";

$reportContent .= "## 🎯 **Recommendations**\n";
$reportContent .= "1. Recommendation 1\n";
$reportContent .= "2. Recommendation 2\n\n";

$reportContent .= "## 📝 **Next Steps**\n";
$reportContent .= "- [ ] Step 1\n";
$reportContent .= "- [ ] Step 2\n\n";

$reportContent .= "---\n";
$reportContent .= "*MechaMap Project - $reportDate*\n";

// Write report
if (file_put_contents($reportPath, $reportContent)) {
    echo "✅ Report generated: $reportPath\n";
    echo "📁 Report location: $reportPath\n";
} else {
    echo "❌ Failed to generate report\n";
}

echo "\n🎯 Report template generated successfully!\n";
echo "Edit the content variables above to customize your report.\n";
