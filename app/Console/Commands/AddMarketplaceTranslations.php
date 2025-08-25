<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMarketplaceTranslations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:marketplace
                            {--dry-run : Preview changes without applying them}
                            {--force : Force update existing translations}';

    /**
     * The console command description.
     */
    protected $description = 'Import marketplace product translations';

    /**
     * Translation keys for marketplace products
     */
    protected $translations = [
        // ===== MARKETPLACE PRODUCTS TRANSLATIONS =====
        'ui.marketplace.products.create_product' => [
            'vi' => 'Tạo sản phẩm',
            'en' => 'Create Product'
        ],
        'ui.marketplace.products.create_description' => [
            'vi' => 'Tạo sản phẩm mới để bán trên marketplace',
            'en' => 'Create a new product to sell on marketplace'
        ],
        'ui.marketplace.products.product_information' => [
            'vi' => 'Thông tin sản phẩm',
            'en' => 'Product Information'
        ],
        'ui.marketplace.products.name' => [
            'vi' => 'Tên sản phẩm',
            'en' => 'Product Name'
        ],
        'ui.marketplace.products.short_description' => [
            'vi' => 'Mô tả ngắn',
            'en' => 'Short Description'
        ],
        'ui.marketplace.products.description' => [
            'vi' => 'Mô tả chi tiết',
            'en' => 'Description'
        ],
        'ui.marketplace.products.product_type' => [
            'vi' => 'Loại sản phẩm',
            'en' => 'Product Type'
        ],
        'ui.marketplace.products.types.digital' => [
            'vi' => 'Sản phẩm số',
            'en' => 'Digital Product'
        ],
        'ui.marketplace.products.types.new_product' => [
            'vi' => 'Sản phẩm mới',
            'en' => 'New Product'
        ],
        'ui.marketplace.products.types.used_product' => [
            'vi' => 'Sản phẩm đã qua sử dụng',
            'en' => 'Used Product'
        ],
        'ui.marketplace.products.category' => [
            'vi' => 'Danh mục',
            'en' => 'Category'
        ],
        'ui.marketplace.products.price' => [
            'vi' => 'Giá bán',
            'en' => 'Price'
        ],
        'ui.marketplace.products.sale_price' => [
            'vi' => 'Giá khuyến mãi',
            'en' => 'Sale Price'
        ],
        'ui.marketplace.products.sale_price_help' => [
            'vi' => 'Để trống nếu không có khuyến mãi',
            'en' => 'Leave empty if no sale price'
        ],
        'ui.marketplace.products.images' => [
            'vi' => 'Hình ảnh',
            'en' => 'Images'
        ],
        'ui.marketplace.products.featured_image' => [
            'vi' => 'Hình ảnh đại diện',
            'en' => 'Featured Image'
        ],
        'ui.marketplace.products.featured_image_help' => [
            'vi' => 'Hình ảnh chính của sản phẩm',
            'en' => 'Main product image'
        ],
        'ui.marketplace.products.additional_images' => [
            'vi' => 'Hình ảnh bổ sung',
            'en' => 'Additional Images'
        ],
        'ui.marketplace.products.additional_images_help' => [
            'vi' => 'Có thể chọn nhiều hình ảnh',
            'en' => 'You can select multiple images'
        ],
        'ui.marketplace.products.digital_files' => [
            'vi' => 'File sản phẩm số',
            'en' => 'Digital Files'
        ],
        'ui.marketplace.products.upload_files' => [
            'vi' => 'Tải lên file',
            'en' => 'Upload Files'
        ],
        'ui.marketplace.products.digital_files_help' => [
            'vi' => 'File sẽ được gửi cho khách hàng sau khi mua',
            'en' => 'Files will be sent to customers after purchase'
        ],
        'ui.marketplace.products.stock_management' => [
            'vi' => 'Quản lý kho',
            'en' => 'Stock Management'
        ],
        'ui.marketplace.products.stock_quantity' => [
            'vi' => 'Số lượng tồn kho',
            'en' => 'Stock Quantity'
        ],
        'ui.marketplace.products.low_stock_threshold' => [
            'vi' => 'Ngưỡng cảnh báo hết hàng',
            'en' => 'Low Stock Threshold'
        ],
        'ui.marketplace.products.tags' => [
            'vi' => 'Thẻ tag',
            'en' => 'Tags'
        ],
        'ui.marketplace.products.tags_placeholder' => [
            'vi' => 'Nhập các tag, cách nhau bằng dấu phẩy',
            'en' => 'Enter tags separated by commas'
        ],
        'ui.marketplace.products.tags_help' => [
            'vi' => 'Giúp khách hàng tìm kiếm sản phẩm dễ dàng hơn',
            'en' => 'Help customers find your product easier'
        ],
        'ui.marketplace.products.save_as_draft' => [
            'vi' => 'Lưu nháp',
            'en' => 'Save as Draft'
        ],
        'ui.marketplace.products.submit_for_review' => [
            'vi' => 'Gửi duyệt',
            'en' => 'Submit for Review'
        ],
        'ui.common.select_option' => [
            'vi' => 'Chọn một tùy chọn',
            'en' => 'Select an option'
        ],
        'ui.common.back' => [
            'vi' => 'Quay lại',
            'en' => 'Back'
        ],
        'ui.common.cancel' => [
            'vi' => 'Hủy',
            'en' => 'Cancel'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $forceUpdate = $this->option('force');

        $stats = [
            'added' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $this->info('🚀 Importing Marketplace Product Translations');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        foreach ($this->translations as $key => $locales) {
            foreach ($locales as $locale => $value) {
                try {
                    $existing = Translation::where('key', $key)
                        ->where('locale', $locale)
                        ->first();

                    if ($existing) {
                        if ($forceUpdate && $existing->content !== $value) {
                            if (!$dryRun) {
                                $existing->update(['content' => $value]);
                            }
                            $stats['updated']++;
                            $this->line("✅ Updated: {$key} ({$locale})");
                        } else {
                            $stats['skipped']++;
                            $this->line("⏭️  Skipped: {$key} ({$locale}) - already exists");
                        }
                    } else {
                        if (!$dryRun) {
                            // Parse group from key
                            $keyParts = explode('.', $key);
                            $group = $keyParts[0] ?? 'general';

                            Translation::create([
                                'key' => $key,
                                'locale' => $locale,
                                'content' => $value,
                                'group_name' => $group,
                                'is_active' => true
                            ]);
                        }
                        $stats['added']++;
                        $this->line("➕ Added: {$key} ({$locale})");
                    }
                } catch (\Exception $e) {
                    $stats['errors'][] = "Error with {$key} ({$locale}): " . $e->getMessage();
                    $this->error("❌ Error: {$key} ({$locale}) - " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info('📊 Import Summary:');
        $this->line("   Added: {$stats['added']}");
        $this->line("   Updated: {$stats['updated']}");
        $this->line("   Skipped: {$stats['skipped']}");
        $this->line("   Errors: " . count($stats['errors']));

        if (!empty($stats['errors'])) {
            $this->newLine();
            $this->error('❌ Errors encountered:');
            foreach ($stats['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }

        if (!$dryRun && ($stats['added'] > 0 || $stats['updated'] > 0)) {
            $this->newLine();
            $this->info('🎉 Marketplace translations imported successfully!');
        }

        return 0;
    }
}
