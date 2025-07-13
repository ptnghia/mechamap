<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Filesystem\Filesystem;

class HardcodedTextFixer
{
    private $filesystem;
    private $basePath;
    private $processed = 0;
    private $replacements = [];

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->basePath = realpath(__DIR__ . '/../../');
        $this->initializeReplacements();
    }

    private function initializeReplacements()
    {
        // Hardcoded Vietnamese text to translation keys
        $this->replacements = [
            // Marketplace
            "{{ __('Cửa hàng') }}" => "{{ __('marketplace.shop') }}",
            "{{ __('Tất cả sản phẩm') }}" => "{{ __('marketplace.products.all') }}",
            "{{ __('Danh mục') }}" => "{{ __('marketplace.categories.title') }}",
            "{{ __('Nhà cung cấp') }}" => "{{ __('marketplace.suppliers.title') }}",
            "{{ __('Sản phẩm nổi bật') }}" => "{{ __('marketplace.products.featured') }}",
            "{{ __('Công cụ kinh doanh') }}" => "{{ __('marketplace.business_tools') }}",
            "{{ __('Yêu cầu báo giá') }}" => "{{ __('marketplace.rfq.title') }}",
            "{{ __('Đơn hàng số lượng lớn') }}" => "{{ __('marketplace.bulk_orders') }}",
            "{{ __('Trở thành người bán') }}" => "{{ __('marketplace.become_seller') }}",
            "{{ __('Tài khoản của tôi') }}" => "{{ __('marketplace.my_account') }}",
            "{{ __('Đơn hàng của tôi') }}" => "{{ __('marketplace.my_orders') }}",
            "{{ __('Giỏ hàng') }}" => "{{ __('marketplace.cart.title') }}",
            "{{ __('Tải xuống') }}" => "{{ __('marketplace.downloads') }}",

            // Content keys
            "{{ __('content.all_content') }}" => "{{ __('search.all_content') }}",
            "{{ __('content.search_in_thread') }}" => "{{ __('search.search_in_thread') }}",
            "{{ __('content.search_in_forum') }}" => "{{ __('search.search_in_forum') }}",
            "{{ __('content.advanced_search') }}" => "{{ __('search.advanced') }}",

            // User keys
            "{{ __('user.profile') }}" => "{{ __('nav.user.profile') }}",
            "{{ __('user.settings') }}" => "{{ __('nav.user.settings') }}",
        ];
    }

    public function run()
    {
        echo "🔧 FIXING HARDCODED TEXT IN VIEWS\n";
        echo "=================================\n\n";

        $this->fixHeaderComponent();
        $this->addMissingKeys();
        $this->printSummary();
    }

    private function fixHeaderComponent()
    {
        echo "🎯 Fixing Header Component...\n";
        
        $headerPath = $this->basePath . '/resources/views/components/header.blade.php';
        $content = file_get_contents($headerPath);
        $originalContent = $content;

        foreach ($this->replacements as $search => $replace) {
            if (strpos($content, $search) !== false) {
                $content = str_replace($search, $replace, $content);
                echo "  ✅ Replaced: $search → $replace\n";
                $this->processed++;
            }
        }

        if ($content !== $originalContent) {
            file_put_contents($headerPath, $content);
            echo "  💾 Header component updated\n";
        }
        echo "\n";
    }

    private function addMissingKeys()
    {
        echo "📝 Adding Missing Language Keys...\n";

        // Add marketplace keys to Vietnamese
        $this->addMarketplaceKeysVi();
        
        // Add marketplace keys to English
        $this->addMarketplaceKeysEn();
        
        // Add search keys
        $this->addSearchKeys();
        
        echo "\n";
    }

    private function addMarketplaceKeysVi()
    {
        $marketplaceVi = $this->basePath . '/resources/lang/vi/marketplace.php';
        $content = file_get_contents($marketplaceVi);
        
        $newKeys = "
    // Shop
    'shop' => 'Cửa hàng',
    'business_tools' => 'Công cụ kinh doanh',
    'become_seller' => 'Trở thành người bán',
    'my_account' => 'Tài khoản của tôi',
    'my_orders' => 'Đơn hàng của tôi',
    'downloads' => 'Tải xuống',
    'bulk_orders' => 'Đơn hàng số lượng lớn',

    // RFQ
    'rfq' => [
        'title' => 'Yêu cầu báo giá',
    ],";

        // Insert before the closing bracket
        $content = str_replace('];', $newKeys . "\n];", $content);
        file_put_contents($marketplaceVi, $content);
        echo "  ✅ Added marketplace keys to Vietnamese\n";
    }

    private function addMarketplaceKeysEn()
    {
        $marketplaceEn = $this->basePath . '/resources/lang/en/marketplace.php';
        $content = file_get_contents($marketplaceEn);
        
        $newKeys = "
    // Shop
    'shop' => 'Shop',
    'business_tools' => 'Business Tools',
    'become_seller' => 'Become a Seller',
    'my_account' => 'My Account',
    'my_orders' => 'My Orders',
    'downloads' => 'Downloads',
    'bulk_orders' => 'Bulk Orders',

    // RFQ
    'rfq' => [
        'title' => 'Request for Quote',
    ],";

        // Insert before the closing bracket
        $content = str_replace('];', $newKeys . "\n];", $content);
        file_put_contents($marketplaceEn, $content);
        echo "  ✅ Added marketplace keys to English\n";
    }

    private function addSearchKeys()
    {
        // Vietnamese search keys
        $searchVi = $this->basePath . '/resources/lang/vi/search.php';
        if (!file_exists($searchVi)) {
            $searchContent = "<?php\n\nreturn [\n    'all_content' => 'Tất cả nội dung',\n    'search_in_thread' => 'Tìm trong chủ đề',\n    'search_in_forum' => 'Tìm trong diễn đàn',\n    'advanced' => 'Tìm kiếm nâng cao',\n];";
            file_put_contents($searchVi, $searchContent);
            echo "  ✅ Created search.php for Vietnamese\n";
        }

        // English search keys
        $searchEn = $this->basePath . '/resources/lang/en/search.php';
        if (!file_exists($searchEn)) {
            $searchContent = "<?php\n\nreturn [\n    'all_content' => 'All Content',\n    'search_in_thread' => 'Search in Thread',\n    'search_in_forum' => 'Search in Forum',\n    'advanced' => 'Advanced Search',\n];";
            file_put_contents($searchEn, $searchContent);
            echo "  ✅ Created search.php for English\n";
        }
    }

    private function printSummary()
    {
        echo "📊 HARDCODED TEXT FIX SUMMARY\n";
        echo "=============================\n\n";
        echo "✅ Replacements made: {$this->processed}\n";
        echo "✅ Language files updated\n";
        echo "✅ New translation keys added\n\n";
        
        echo "💡 Next steps:\n";
        echo "   1. Clear Laravel cache\n";
        echo "   2. Test header navigation\n";
        echo "   3. Verify language switching\n";
    }
}

$fixer = new HardcodedTextFixer();
$fixer->run();
