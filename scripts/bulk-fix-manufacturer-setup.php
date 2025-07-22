<?php

echo "\n🔧 BULK FIX: manufacturer/setup-required.blade.php\n";
echo "======================================================\n\n";

$file_path = 'resources/views/manufacturer/setup-required.blade.php';
$content = file_get_contents($file_path);

if (!$content) {
    echo "❌ Error: Cannot read $file_path\n";
    exit;
}

echo "🔍 Original file analysis:\n";
echo "- Path: $file_path\n";
echo "- Size: " . strlen($content) . " bytes\n\n";

// Định nghĩa mapping cho các key cần thay thế
$replacements = [
    // Page titles
    "__('messages.setup_required')" => "__('manufacturer.setup_required')",
    "__('messages.manufacturer_setup_required')" => "__('manufacturer.manufacturer_setup_required')",
    "__('messages.manufacturer_setup_description')" => "__('manufacturer.manufacturer_setup_description')",

    // Setup steps
    "__('messages.business_profile')" => "__('manufacturer.business_profile')",
    "__('messages.complete_business_info')" => "__('manufacturer.complete_business_info')",
    "__('messages.verification_documents')" => "__('manufacturer.verification_documents')",
    "__('messages.upload_business_documents')" => "__('manufacturer.upload_business_documents')",
    "__('messages.marketplace_setup')" => "__('manufacturer.marketplace_setup')",
    "__('messages.configure_seller_profile')" => "__('manufacturer.configure_seller_profile')",

    // Actions
    "__('messages.start_setup')" => "__('manufacturer.start_setup')",
    "__('messages.back_to_home')" => "__('manufacturer.back_to_home')",
    "__('messages.need_help')" => "__('manufacturer.need_help')",

    // Benefits
    "__('messages.manufacturer_benefits')" => "__('manufacturer.manufacturer_benefits')",
    "__('messages.sell_technical_products')" => "__('manufacturer.sell_technical_products')",
    "__('messages.access_b2b_marketplace')" => "__('manufacturer.access_b2b_marketplace')",
    "__('messages.connect_with_suppliers')" => "__('manufacturer.connect_with_suppliers')",
    "__('messages.verified_business_badge')" => "__('manufacturer.verified_business_badge')",

    // Requirements
    "__('messages.setup_requirements')" => "__('manufacturer.setup_requirements')",
    "__('messages.business_registration')" => "__('manufacturer.business_registration')",
    "__('messages.tax_identification')" => "__('manufacturer.tax_identification')",
    "__('messages.company_address')" => "__('manufacturer.company_address')",
    "__('messages.contact_information')" => "__('manufacturer.contact_information')",
];

echo "🔄 Performing bulk replacements:\n";
$replacement_count = 0;

foreach ($replacements as $old => $new) {
    $old_content = $content;
    $content = str_replace($old, $new, $content);

    if ($content !== $old_content) {
        $count = substr_count($old_content, $old);
        echo "✅ $old → $new ($count occurrences)\n";
        $replacement_count += $count;
    }
}

echo "\n📊 Replacement Summary:\n";
echo "Total replacements made: $replacement_count\n\n";

// Lưu file đã được sửa
file_put_contents($file_path, $content);
echo "💾 File saved: $file_path\n";

echo "\n✅ Bulk replacement completed!\n";

?>
