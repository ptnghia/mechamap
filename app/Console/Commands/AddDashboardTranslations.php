<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddDashboardTranslations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'translations:import-batch
                            {--dry-run : Preview changes without applying them}
                            {--group= : Import specific group only}
                            {--force : Force update existing translations}
                            {--locale= : Import specific locale only (vi|en)}';

    /**
     * The console command description.
     */
    protected $description = 'Import translation keys in batch with advanced options and validation';

    /**
     * Translation keys to import - Configure as needed
     *
     * Structure: 'key' => ['vi' => 'Vietnamese', 'en' => 'English']
     * Groups are auto-detected from key prefix (before first dot)
     */
    protected $translations = [
        // ===== MARKETPLACE DIGITAL PRODUCTS TRANSLATIONS =====

        // Digital Products Create Page
        'marketplace.digital_products.create_title' => [
            'vi' => 'Tạo Sản Phẩm Số',
            'en' => 'Create Digital Product'
        ],
        'marketplace.digital_products.create' => [
            'vi' => 'Tạo sản phẩm số',
            'en' => 'Create digital product'
        ],
        'marketplace.digital_products.info_title' => [
            'vi' => 'Thông tin sản phẩm số',
            'en' => 'Digital Product Information'
        ],
        'marketplace.digital_products.info_description' => [
            'vi' => 'Sản phẩm số bao gồm file CAD, bản vẽ kỹ thuật, tài liệu và phần mềm có thể tải xuống.',
            'en' => 'Digital products include CAD files, technical drawings, documents and downloadable software.'
        ],

        // Basic Information
        'marketplace.products.basic_info' => [
            'vi' => 'Thông tin cơ bản',
            'en' => 'Basic Information'
        ],
        'marketplace.products.name' => [
            'vi' => 'Tên sản phẩm',
            'en' => 'Product Name'
        ],
        'marketplace.digital_products.name_help' => [
            'vi' => 'Nhập tên mô tả rõ ràng cho sản phẩm số của bạn',
            'en' => 'Enter a clear descriptive name for your digital product'
        ],
        'marketplace.products.short_description' => [
            'vi' => 'Mô tả ngắn',
            'en' => 'Short Description'
        ],
        'marketplace.products.short_description_help' => [
            'vi' => 'Mô tả ngắn gọn về sản phẩm (tối đa 500 ký tự)',
            'en' => 'Brief description of the product (max 500 characters)'
        ],
        'marketplace.products.description' => [
            'vi' => 'Mô tả chi tiết',
            'en' => 'Detailed Description'
        ],
        'marketplace.digital_products.description_help' => [
            'vi' => 'Mô tả chi tiết về sản phẩm, bao gồm tính năng và ứng dụng',
            'en' => 'Detailed description of the product, including features and applications'
        ],

        // Digital Files
        'marketplace.digital_products.files' => [
            'vi' => 'File Số',
            'en' => 'Digital Files'
        ],
        'marketplace.digital_products.upload_files' => [
            'vi' => 'Tải lên file',
            'en' => 'Upload Files'
        ],
        'marketplace.digital_products.files_help' => [
            'vi' => 'Chọn các file số để bán. Hỗ trợ nhiều file cùng lúc.',
            'en' => 'Select digital files to sell. Multiple files supported.'
        ],
        'marketplace.digital_products.supported_formats' => [
            'vi' => 'Định dạng hỗ trợ',
            'en' => 'Supported Formats'
        ],
        'marketplace.digital_products.max_size' => [
            'vi' => 'Kích thước tối đa',
            'en' => 'Maximum Size'
        ],
        'marketplace.digital_products.selected_files' => [
            'vi' => 'File đã chọn',
            'en' => 'Selected Files'
        ],

        // Technical Specifications
        'marketplace.digital_products.technical_specs' => [
            'vi' => 'Thông số kỹ thuật',
            'en' => 'Technical Specifications'
        ],
        'marketplace.digital_products.file_formats' => [
            'vi' => 'Định dạng file',
            'en' => 'File Formats'
        ],
        'marketplace.digital_products.file_formats_help' => [
            'vi' => 'Nhập các định dạng file, cách nhau bằng dấu phẩy hoặc Enter',
            'en' => 'Enter file formats, separated by comma or Enter'
        ],
        'marketplace.digital_products.software_compatibility' => [
            'vi' => 'Tương thích phần mềm',
            'en' => 'Software Compatibility'
        ],
        'marketplace.digital_products.software_help' => [
            'vi' => 'Nhập các phần mềm tương thích, cách nhau bằng dấu phẩy hoặc Enter',
            'en' => 'Enter compatible software, separated by comma or Enter'
        ],
        'marketplace.digital_products.download_limit' => [
            'vi' => 'Giới hạn tải xuống',
            'en' => 'Download Limit'
        ],
        'marketplace.digital_products.download_limit_help' => [
            'vi' => 'Số lần tối đa khách hàng có thể tải xuống sau khi mua',
            'en' => 'Maximum number of times customer can download after purchase'
        ],

        // Images
        'marketplace.products.images' => [
            'vi' => 'Hình ảnh',
            'en' => 'Images'
        ],
        'marketplace.products.featured_image' => [
            'vi' => 'Hình ảnh đại diện',
            'en' => 'Featured Image'
        ],
        'marketplace.products.featured_image_help' => [
            'vi' => 'Chọn hình ảnh chính cho sản phẩm',
            'en' => 'Select main image for the product'
        ],
        'marketplace.products.gallery_images' => [
            'vi' => 'Thư viện ảnh',
            'en' => 'Gallery Images'
        ],
        'marketplace.products.gallery_images_help' => [
            'vi' => 'Chọn nhiều hình ảnh để hiển thị trong thư viện',
            'en' => 'Select multiple images to display in gallery'
        ],

        // Pricing
        'marketplace.products.pricing' => [
            'vi' => 'Giá cả',
            'en' => 'Pricing'
        ],
        'marketplace.products.price' => [
            'vi' => 'Giá bán',
            'en' => 'Price'
        ],
        'marketplace.products.sale_price' => [
            'vi' => 'Giá khuyến mãi',
            'en' => 'Sale Price'
        ],
        'marketplace.products.sale_price_help' => [
            'vi' => 'Giá khuyến mãi (tùy chọn)',
            'en' => 'Sale price (optional)'
        ],

        // Category
        'marketplace.products.category' => [
            'vi' => 'Danh mục',
            'en' => 'Category'
        ],
        'marketplace.products.select_category' => [
            'vi' => 'Chọn danh mục',
            'en' => 'Select Category'
        ],
        'marketplace.products.choose_category' => [
            'vi' => 'Chọn danh mục...',
            'en' => 'Choose category...'
        ],

        // SEO & Tags
        'marketplace.products.seo_tags' => [
            'vi' => 'SEO & Tags',
            'en' => 'SEO & Tags'
        ],
        'marketplace.products.tags' => [
            'vi' => 'Tags',
            'en' => 'Tags'
        ],
        'marketplace.products.tags_help' => [
            'vi' => 'Nhập các từ khóa, cách nhau bằng dấu phẩy',
            'en' => 'Enter keywords, separated by commas'
        ],
        'marketplace.products.meta_title' => [
            'vi' => 'Meta Title',
            'en' => 'Meta Title'
        ],
        'marketplace.products.meta_description' => [
            'vi' => 'Meta Description',
            'en' => 'Meta Description'
        ],

        // Actions
        'marketplace.digital_products.create_product' => [
            'vi' => 'Tạo sản phẩm',
            'en' => 'Create Product'
        ],
        'common.cancel' => [
            'vi' => 'Hủy',
            'en' => 'Cancel'
        ],
        'common.dashboard' => [
            'vi' => 'Dashboard',
            'en' => 'Dashboard'
        ],
        'marketplace.products.title' => [
            'vi' => 'Sản phẩm',
            'en' => 'Products'
        ],
        'common.error' => [
            'vi' => 'Lỗi',
            'en' => 'Error'
        ],

        // Sidebar translations
        't_sidebar.marketplace.digital_product' => [
            'vi' => 'Sản phẩm số',
            'en' => 'Digital Product'
        ],
        't_sidebar.marketplace.physical_product' => [
            'vi' => 'Sản phẩm vật lý',
            'en' => 'Physical Product'
        ],

        // ===== PHYSICAL PRODUCTS TRANSLATIONS =====

        // Physical Products Create Page
        'marketplace.physical_products.create_title' => [
            'vi' => 'Tạo Sản Phẩm Vật Lý',
            'en' => 'Create Physical Product'
        ],
        'marketplace.physical_products.create' => [
            'vi' => 'Tạo sản phẩm vật lý',
            'en' => 'Create physical product'
        ],
        'marketplace.physical_products.info_title' => [
            'vi' => 'Thông tin sản phẩm vật lý',
            'en' => 'Physical Product Information'
        ],
        'marketplace.physical_products.info_description' => [
            'vi' => 'Sản phẩm vật lý bao gồm thiết bị, linh kiện, máy móc và vật liệu kỹ thuật có thể vận chuyển.',
            'en' => 'Physical products include equipment, components, machinery and technical materials that can be shipped.'
        ],
        'marketplace.physical_products.name_help' => [
            'vi' => 'Nhập tên mô tả rõ ràng cho sản phẩm vật lý của bạn',
            'en' => 'Enter a clear descriptive name for your physical product'
        ],
        'marketplace.physical_products.description_help' => [
            'vi' => 'Mô tả chi tiết về sản phẩm, tình trạng và ứng dụng',
            'en' => 'Detailed description of the product, condition and applications'
        ],

        // Product Type & Condition
        'marketplace.physical_products.type_condition' => [
            'vi' => 'Loại sản phẩm & Tình trạng',
            'en' => 'Product Type & Condition'
        ],
        'marketplace.physical_products.product_type' => [
            'vi' => 'Loại sản phẩm',
            'en' => 'Product Type'
        ],
        'marketplace.physical_products.choose_type' => [
            'vi' => 'Chọn loại sản phẩm...',
            'en' => 'Choose product type...'
        ],
        'marketplace.physical_products.new_product' => [
            'vi' => 'Sản phẩm mới',
            'en' => 'New Product'
        ],
        'marketplace.physical_products.used_product' => [
            'vi' => 'Sản phẩm đã qua sử dụng',
            'en' => 'Used Product'
        ],
        'marketplace.physical_products.condition' => [
            'vi' => 'Tình trạng',
            'en' => 'Condition'
        ],
        'marketplace.physical_products.choose_condition' => [
            'vi' => 'Chọn tình trạng...',
            'en' => 'Choose condition...'
        ],
        'marketplace.physical_products.condition_new' => [
            'vi' => 'Mới (chưa sử dụng)',
            'en' => 'New (unused)'
        ],
        'marketplace.physical_products.condition_like_new' => [
            'vi' => 'Như mới',
            'en' => 'Like New'
        ],
        'marketplace.physical_products.condition_good' => [
            'vi' => 'Tốt',
            'en' => 'Good'
        ],
        'marketplace.physical_products.condition_fair' => [
            'vi' => 'Khá',
            'en' => 'Fair'
        ],
        'marketplace.physical_products.condition_poor' => [
            'vi' => 'Kém',
            'en' => 'Poor'
        ],

        // Physical Specifications
        'marketplace.physical_products.physical_specs' => [
            'vi' => 'Thông số vật lý',
            'en' => 'Physical Specifications'
        ],
        'marketplace.physical_products.weight' => [
            'vi' => 'Trọng lượng',
            'en' => 'Weight'
        ],
        'marketplace.physical_products.length' => [
            'vi' => 'Chiều dài',
            'en' => 'Length'
        ],
        'marketplace.physical_products.width' => [
            'vi' => 'Chiều rộng',
            'en' => 'Width'
        ],
        'marketplace.physical_products.height' => [
            'vi' => 'Chiều cao',
            'en' => 'Height'
        ],
        'marketplace.physical_products.material' => [
            'vi' => 'Chất liệu',
            'en' => 'Material'
        ],
        'marketplace.physical_products.material_help' => [
            'vi' => 'Ví dụ: Thép không gỉ, Nhôm, Nhựa ABS',
            'en' => 'Example: Stainless steel, Aluminum, ABS plastic'
        ],

        // Stock Management
        'marketplace.physical_products.stock_quantity' => [
            'vi' => 'Số lượng tồn kho',
            'en' => 'Stock Quantity'
        ],
        'marketplace.physical_products.stock_help' => [
            'vi' => 'Số lượng sản phẩm có sẵn để bán',
            'en' => 'Number of products available for sale'
        ],

        // Actions
        'marketplace.physical_products.create_product' => [
            'vi' => 'Tạo sản phẩm',
            'en' => 'Create Product'
        ],
        'marketplace.physical_products.created_successfully' => [
            'vi' => 'Sản phẩm vật lý đã được tạo thành công!',
            'en' => 'Physical product created successfully!'
        ],
        'marketplace.physical_products.creation_failed' => [
            'vi' => 'Không thể tạo sản phẩm vật lý',
            'en' => 'Failed to create physical product'
        ],

        // ===== EDIT PRODUCTS TRANSLATIONS =====

        // Digital Products Edit
        'marketplace.digital_products.edit_title' => [
            'vi' => 'Chỉnh Sửa Sản Phẩm Số',
            'en' => 'Edit Digital Product'
        ],
        'marketplace.digital_products.edit' => [
            'vi' => 'Chỉnh sửa sản phẩm số',
            'en' => 'Edit digital product'
        ],
        'marketplace.digital_products.edit_info_title' => [
            'vi' => 'Chỉnh sửa thông tin sản phẩm số',
            'en' => 'Edit Digital Product Information'
        ],
        'marketplace.digital_products.edit_info_description' => [
            'vi' => 'Cập nhật thông tin, file và hình ảnh cho sản phẩm số của bạn.',
            'en' => 'Update information, files and images for your digital product.'
        ],
        'marketplace.digital_products.current_files' => [
            'vi' => 'File hiện tại',
            'en' => 'Current Files'
        ],
        'marketplace.digital_products.upload_new_files' => [
            'vi' => 'Tải lên file mới',
            'en' => 'Upload New Files'
        ],
        'marketplace.digital_products.per_file' => [
            'vi' => 'mỗi file',
            'en' => 'per file'
        ],
        'marketplace.digital_products.confirm_remove_file' => [
            'vi' => 'Bạn có chắc chắn muốn xóa file này?',
            'en' => 'Are you sure you want to remove this file?'
        ],
        'marketplace.digital_products.update_product' => [
            'vi' => 'Cập nhật sản phẩm',
            'en' => 'Update Product'
        ],
        'marketplace.digital_products.updated_successfully' => [
            'vi' => 'Sản phẩm số đã được cập nhật thành công!',
            'en' => 'Digital product updated successfully!'
        ],
        'marketplace.digital_products.update_failed' => [
            'vi' => 'Không thể cập nhật sản phẩm số',
            'en' => 'Failed to update digital product'
        ],

        // Physical Products Edit
        'marketplace.physical_products.edit_title' => [
            'vi' => 'Chỉnh Sửa Sản Phẩm Vật Lý',
            'en' => 'Edit Physical Product'
        ],
        'marketplace.physical_products.edit' => [
            'vi' => 'Chỉnh sửa sản phẩm vật lý',
            'en' => 'Edit physical product'
        ],
        'marketplace.physical_products.edit_info_title' => [
            'vi' => 'Chỉnh sửa thông tin sản phẩm vật lý',
            'en' => 'Edit Physical Product Information'
        ],
        'marketplace.physical_products.edit_info_description' => [
            'vi' => 'Cập nhật thông tin, thông số và hình ảnh cho sản phẩm vật lý của bạn.',
            'en' => 'Update information, specifications and images for your physical product.'
        ],
        'marketplace.physical_products.update_product' => [
            'vi' => 'Cập nhật sản phẩm',
            'en' => 'Update Product'
        ],
        'marketplace.physical_products.updated_successfully' => [
            'vi' => 'Sản phẩm vật lý đã được cập nhật thành công!',
            'en' => 'Physical product updated successfully!'
        ],
        'marketplace.physical_products.update_failed' => [
            'vi' => 'Không thể cập nhật sản phẩm vật lý',
            'en' => 'Failed to update physical product'
        ],

        // Common Product Fields
        'marketplace.products.status' => [
            'vi' => 'Trạng thái',
            'en' => 'Status'
        ],
        'marketplace.products.status_active' => [
            'vi' => 'Đang hoạt động',
            'en' => 'Active'
        ],
        'marketplace.products.status_pending' => [
            'vi' => 'Chờ duyệt',
            'en' => 'Pending'
        ],
        'marketplace.products.status_inactive' => [
            'vi' => 'Tạm dừng',
            'en' => 'Inactive'
        ],
        'marketplace.products.created' => [
            'vi' => 'Ngày tạo',
            'en' => 'Created'
        ],
        'marketplace.products.updated' => [
            'vi' => 'Cập nhật',
            'en' => 'Updated'
        ],
        'marketplace.products.current_featured_image' => [
            'vi' => 'Hình ảnh đại diện hiện tại',
            'en' => 'Current Featured Image'
        ],
        'marketplace.products.remove_featured_image' => [
            'vi' => 'Xóa hình ảnh đại diện',
            'en' => 'Remove featured image'
        ],
        'marketplace.products.current_gallery_images' => [
            'vi' => 'Hình ảnh thư viện hiện tại',
            'en' => 'Current Gallery Images'
        ],
        'marketplace.products.confirm_remove_image' => [
            'vi' => 'Bạn có chắc chắn muốn xóa hình ảnh này?',
            'en' => 'Are you sure you want to remove this image?'
        ],
        'marketplace.products.sale_price_validation' => [
            'vi' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'en' => 'Sale price must be less than regular price'
        ],

        // ===== PRODUCTS INDEX PAGE TRANSLATIONS =====

        'marketplace.products.title' => [
            'vi' => 'Sản Phẩm Của Tôi',
            'en' => 'My Products'
        ],
        'marketplace.products.total_products' => [
            'vi' => 'Tổng sản phẩm',
            'en' => 'Total Products'
        ],
        'marketplace.products.active_products' => [
            'vi' => 'Sản phẩm đang bán',
            'en' => 'Active Products'
        ],
        'marketplace.products.total_views' => [
            'vi' => 'Tổng lượt xem',
            'en' => 'Total Views'
        ],
        'marketplace.products.total_sales' => [
            'vi' => 'Tổng lượt bán',
            'en' => 'Total Sales'
        ],
        'marketplace.products.this_month' => [
            'vi' => 'tháng này',
            'en' => 'this month'
        ],
        'marketplace.products.of_total' => [
            'vi' => 'của tổng số',
            'en' => 'of total'
        ],
        'marketplace.products.avg_per_product' => [
            'vi' => 'trung bình/sản phẩm',
            'en' => 'avg per product'
        ],
        'marketplace.products.product_types' => [
            'vi' => 'Loại sản phẩm',
            'en' => 'Product Types'
        ],
        'marketplace.products.type_digital' => [
            'vi' => 'Sản phẩm số',
            'en' => 'Digital Products'
        ],
        'marketplace.products.type_physical' => [
            'vi' => 'Sản phẩm vật lý',
            'en' => 'Physical Products'
        ],
        'marketplace.products.no_products_yet' => [
            'vi' => 'Chưa có sản phẩm nào',
            'en' => 'No products yet'
        ],
        'marketplace.products.quick_actions' => [
            'vi' => 'Thao tác nhanh',
            'en' => 'Quick Actions'
        ],
        'marketplace.products.status_overview' => [
            'vi' => 'Tổng quan trạng thái',
            'en' => 'Status Overview'
        ],
        'marketplace.products.draft' => [
            'vi' => 'Bản nháp',
            'en' => 'Draft'
        ],
        'marketplace.products.pending' => [
            'vi' => 'Chờ duyệt',
            'en' => 'Pending'
        ],
        'marketplace.products.active' => [
            'vi' => 'Đang bán',
            'en' => 'Active'
        ],
        'marketplace.products.rejected' => [
            'vi' => 'Bị từ chối',
            'en' => 'Rejected'
        ],
        'marketplace.products.my_products' => [
            'vi' => 'Sản phẩm của tôi',
            'en' => 'My Products'
        ],
        'marketplace.products.filter' => [
            'vi' => 'Lọc',
            'en' => 'Filter'
        ],
        'marketplace.products.all_status' => [
            'vi' => 'Tất cả trạng thái',
            'en' => 'All Status'
        ],
        'marketplace.products.product' => [
            'vi' => 'Sản phẩm',
            'en' => 'Product'
        ],
        'marketplace.products.type' => [
            'vi' => 'Loại',
            'en' => 'Type'
        ],
        'marketplace.products.stats' => [
            'vi' => 'Thống kê',
            'en' => 'Stats'
        ],
        'marketplace.products.uncategorized' => [
            'vi' => 'Chưa phân loại',
            'en' => 'Uncategorized'
        ],
        'marketplace.products.status_approved' => [
            'vi' => 'Đã duyệt',
            'en' => 'Approved'
        ],
        'marketplace.products.status_draft' => [
            'vi' => 'Bản nháp',
            'en' => 'Draft'
        ],
        'marketplace.products.status_pending' => [
            'vi' => 'Chờ duyệt',
            'en' => 'Pending'
        ],
        'marketplace.products.status_rejected' => [
            'vi' => 'Bị từ chối',
            'en' => 'Rejected'
        ],
        'marketplace.products.no_products' => [
            'vi' => 'Chưa có sản phẩm nào',
            'en' => 'No products found'
        ],
        'marketplace.products.no_products_description' => [
            'vi' => 'Bạn chưa tạo sản phẩm nào. Hãy bắt đầu bằng cách tạo sản phẩm đầu tiên của bạn.',
            'en' => 'You haven\'t created any products yet. Start by creating your first product.'
        ],
        'marketplace.products.confirm_delete' => [
            'vi' => 'Xác nhận xóa sản phẩm',
            'en' => 'Confirm Delete Product'
        ],
        'marketplace.products.confirm_delete_message' => [
            'vi' => 'Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.',
            'en' => 'Are you sure you want to delete this product? This action cannot be undone.'
        ],
        'marketplace.products.type_new_product' => [
            'vi' => 'Sản phẩm mới',
            'en' => 'New Product'
        ],
        'marketplace.products.type_used_product' => [
            'vi' => 'Sản phẩm cũ',
            'en' => 'Used Product'
        ],

        // ===== PHYSICAL PRODUCTS EDIT TRANSLATIONS =====

        'marketplace.physical_products.edit_title' => [
            'vi' => 'Chỉnh Sửa Sản Phẩm Vật Lý',
            'en' => 'Edit Physical Product'
        ],
        'marketplace.physical_products.edit' => [
            'vi' => 'Chỉnh sửa sản phẩm vật lý',
            'en' => 'Edit physical product'
        ],
        'marketplace.physical_products.edit_info_title' => [
            'vi' => 'Chỉnh sửa sản phẩm vật lý',
            'en' => 'Edit Physical Product'
        ],
        'marketplace.physical_products.edit_info_description' => [
            'vi' => 'Cập nhật thông tin sản phẩm vật lý của bạn. Hãy đảm bảo thông tin chính xác để thu hút khách hàng.',
            'en' => 'Update your physical product information. Make sure the information is accurate to attract customers.'
        ],
        'marketplace.physical_products.update_product' => [
            'vi' => 'Cập Nhật Sản Phẩm',
            'en' => 'Update Product'
        ],
        'marketplace.products.current_images' => [
            'vi' => 'Hình ảnh hiện tại',
            'en' => 'Current Images'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $groupFilter = $this->option('group');
        $localeFilter = $this->option('locale');
        $forceUpdate = $this->option('force');

        $stats = [
            'added' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $this->displayHeader($dryRun, $groupFilter, $localeFilter, $forceUpdate);

        // Validate options
        if ($localeFilter && !in_array($localeFilter, ['vi', 'en'])) {
            $this->error('❌ Invalid locale. Use: vi or en');
            return 1;
        }

        // Filter translations based on options
        $filteredTranslations = $this->filterTranslations($groupFilter, $localeFilter);

        if (empty($filteredTranslations)) {
            $this->warn('⚠️  No translations to process with current filters');
            return 0;
        }

        $this->info("� Processing " . count($filteredTranslations) . " translation keys...");

        DB::beginTransaction();

        try {
            foreach ($filteredTranslations as $key => $locales) {
                $this->processTranslationKey($key, $locales, $dryRun, $forceUpdate, $stats);
            }

            if (!$dryRun) {
                DB::commit();
                $this->info('💾 Changes committed to database');
                $this->clearTranslationCache();
            } else {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('📍 Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->displaySummary($stats, $dryRun);
        return 0;
    }

    /**
     * Display command header with options
     */
    private function displayHeader(bool $dryRun, ?string $group, ?string $locale, bool $force): void
    {
        $this->info('🚀 Translation Batch Import Tool');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        if ($group) {
            $this->info("🎯 Group filter: {$group}");
        }

        if ($locale) {
            $this->info("🌐 Locale filter: {$locale}");
        }

        if ($force) {
            $this->warn('⚡ Force mode: Will update existing translations');
        }

        $this->newLine();
    }

    /**
     * Filter translations based on options
     */
    private function filterTranslations(?string $groupFilter, ?string $localeFilter): array
    {
        $filtered = [];

        foreach ($this->translations as $key => $locales) {
            // Filter by group
            if ($groupFilter) {
                $keyGroup = explode('.', $key)[0];
                if ($keyGroup !== $groupFilter) {
                    continue;
                }
            }

            // Filter by locale
            if ($localeFilter) {
                $locales = array_filter($locales, function($locale) use ($localeFilter) {
                    return $locale === $localeFilter;
                }, ARRAY_FILTER_USE_KEY);

                if (empty($locales)) {
                    continue;
                }
            }

            $filtered[$key] = $locales;
        }

        return $filtered;
    }

    /**
     * Process a single translation key
     */
    private function processTranslationKey(string $key, array $locales, bool $dryRun, bool $forceUpdate, array &$stats): void
    {
        foreach ($locales as $locale => $content) {
            $existing = Translation::where('key', $key)
                ->where('locale', $locale)
                ->first();

            if ($existing && !$forceUpdate) {
                $this->line("⏭️  Skipped: {$key} ({$locale}) - already exists");
                $stats['skipped']++;
                continue;
            }

            if (!$dryRun) {
                if ($existing && $forceUpdate) {
                    // Update existing
                    $existing->update([
                        'content' => $content,
                        'updated_by' => 1,
                        'updated_at' => now(),
                    ]);
                    $this->line("🔄 Updated: {$key} ({$locale}) = {$content}");
                    $stats['updated']++;
                } else {
                    // Create new
                    Translation::create([
                        'key' => $key,
                        'content' => $content,
                        'locale' => $locale,
                        'group_name' => explode('.', $key)[0],
                        'is_active' => true,
                        'created_by' => 1,
                    ]);
                    $this->line("✅ Added: {$key} ({$locale}) = {$content}");
                    $stats['added']++;
                }
            } else {
                if ($existing && $forceUpdate) {
                    $this->line("🔄 Would update: {$key} ({$locale}) = {$content}");
                    $stats['updated']++;
                } else {
                    $this->line("✅ Would add: {$key} ({$locale}) = {$content}");
                    $stats['added']++;
                }
            }
        }
    }

    /**
     * Clear translation cache
     */
    private function clearTranslationCache(): void
    {
        try {
            cache()->tags(['translations'])->flush();
            cache()->forget('translations.*');
            $this->info('🗑️  Translation cache cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Display summary statistics
     */
    private function displaySummary(array $stats, bool $dryRun): void
    {
        $this->newLine();
        $this->info('📊 SUMMARY:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($stats['added'] > 0) {
            $this->info("✅ Added: {$stats['added']} translations");
        }

        if ($stats['updated'] > 0) {
            $this->info("🔄 Updated: {$stats['updated']} translations");
        }

        if ($stats['skipped'] > 0) {
            $this->info("⏭️  Skipped: {$stats['skipped']} translations");
        }

        if (!empty($stats['errors'])) {
            $this->error("❌ Errors: " . count($stats['errors']));
            foreach ($stats['errors'] as $error) {
                $this->error("   - {$error}");
            }
        }

        if (!$dryRun && ($stats['added'] > 0 || $stats['updated'] > 0)) {
            $this->newLine();
            $this->info('🎉 Translation import completed successfully!');
            $this->info('💡 Recommendations:');
            $this->line('   • Test translations on frontend');
            $this->line('   • Check translation management UI');
            $this->line('   • Verify cache is working properly');
        }
    }
}
