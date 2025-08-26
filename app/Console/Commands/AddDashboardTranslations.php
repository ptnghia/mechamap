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
        // ===== UI BUTTONS TRANSLATIONS =====
        'ui.buttons.add_to_cart' => [
            'vi' => 'Thêm vào giỏ',
            'en' => 'Add to Cart'
        ],
        'ui.buttons.add_to_wishlist' => [
            'vi' => 'Thêm vào yêu thích',
            'en' => 'Add to Wishlist'
        ],

        // ===== SHOWCASE FORM TRANSLATIONS =====
        'showcase.form.title' => [
            'vi' => 'Tiêu đề showcase',
            'en' => 'Showcase Title'
        ],
        'showcase.form.title_placeholder' => [
            'vi' => 'Tên dự án hoặc sản phẩm bạn muốn showcase',
            'en' => 'Project or product name you want to showcase'
        ],
        'showcase.form.description' => [
            'vi' => 'Mô tả chi tiết',
            'en' => 'Detailed Description'
        ],
        'showcase.form.description_placeholder' => [
            'vi' => 'Mô tả chi tiết về dự án: mục tiêu, công nghệ sử dụng, đặc điểm nổi bật, kết quả đạt được...',
            'en' => 'Detailed project description: objectives, technologies used, key features, results achieved...'
        ],
        'showcase.form.description_help' => [
            'vi' => 'Cung cấp thông tin chi tiết về dự án, công nghệ và kết quả. Hỗ trợ định dạng văn bản, hình ảnh và liên kết.',
            'en' => 'Provide detailed information about the project, technology and results. Supports text formatting, images and links.'
        ],
        'showcase.form.location' => [
            'vi' => 'Địa điểm',
            'en' => 'Location'
        ],
        'showcase.form.usage' => [
            'vi' => 'Lĩnh vực ứng dụng',
            'en' => 'Application Field'
        ],
        'showcase.form.cover_image' => [
            'vi' => 'Hình ảnh đại diện',
            'en' => 'Cover Image'
        ],
        'showcase.form.cover_image_help' => [
            'vi' => 'Hình ảnh đại diện cho showcase của bạn',
            'en' => 'Representative image for your showcase'
        ],
        'showcase.form.multiple_images' => [
            'vi' => 'Thư viện hình ảnh',
            'en' => 'Image Gallery'
        ],
        'showcase.form.multiple_images_help' => [
            'vi' => 'Thêm nhiều hình ảnh để showcase dự án của bạn một cách chi tiết',
            'en' => 'Add multiple images to showcase your project in detail'
        ],
        'showcase.form.file_attachments' => [
            'vi' => 'File Attachments',
            'en' => 'File Attachments'
        ],
        'showcase.form.file_attachments_help' => [
            'vi' => 'Thêm các file đính kèm như CAD files, bản vẽ kỹ thuật, tài liệu tính toán, báo cáo',
            'en' => 'Add attachments like CAD files, technical drawings, calculation documents, reports'
        ],
        'showcase.form.software_used' => [
            'vi' => 'Phần mềm sử dụng',
            'en' => 'Software Used'
        ],
        'showcase.form.software_used_help' => [
            'vi' => 'Chọn các phần mềm đã sử dụng trong dự án (có thể chọn nhiều)',
            'en' => 'Select software used in the project (multiple selection allowed)'
        ],
        'showcase.form.materials' => [
            'vi' => 'Vật liệu chính',
            'en' => 'Main Materials'
        ],
        'showcase.form.materials_help' => [
            'vi' => 'Nhập vật liệu chính hoặc chọn từ gợi ý. Có thể nhập nhiều vật liệu, cách nhau bằng dấu phẩy',
            'en' => 'Enter main materials or select from suggestions. Multiple materials can be entered, separated by commas'
        ],
        'showcase.form.manufacturing_process' => [
            'vi' => 'Quy trình sản xuất',
            'en' => 'Manufacturing Process'
        ],
        'showcase.form.manufacturing_process_help' => [
            'vi' => 'Chọn quy trình sản xuất chính được sử dụng trong dự án',
            'en' => 'Select the main manufacturing process used in the project'
        ],
        'showcase.form.complexity_level' => [
            'vi' => 'Mức độ phức tạp',
            'en' => 'Complexity Level'
        ],
        'showcase.form.complexity_level_help' => [
            'vi' => 'Chọn mức độ phức tạp phù hợp với dự án của bạn',
            'en' => 'Select the complexity level appropriate for your project'
        ],
        'showcase.form.industry_application' => [
            'vi' => 'Ứng dụng ngành',
            'en' => 'Industry Application'
        ],
        'showcase.form.industry_application_help' => [
            'vi' => 'Ngành công nghiệp ứng dụng dự án',
            'en' => 'Industry application of the project'
        ],
        'showcase.form.project_scale' => [
            'vi' => 'Quy mô/Cấp độ',
            'en' => 'Scale/Level'
        ],
        'showcase.form.category' => [
            'vi' => 'Danh mục kỹ thuật',
            'en' => 'Technical Category'
        ],

        // ===== SHOWCASE FORM SECTIONS =====
        'showcase.sections.basic_info' => [
            'vi' => 'Thông Tin Cơ Bản',
            'en' => 'Basic Information'
        ],
        'showcase.sections.technical_info' => [
            'vi' => 'Thông Tin Kỹ Thuật',
            'en' => 'Technical Information'
        ],
        'showcase.sections.project_features' => [
            'vi' => 'Tính năng dự án',
            'en' => 'Project Features'
        ],
        'showcase.sections.project_features_help' => [
            'vi' => 'Chọn các tính năng có trong dự án của bạn',
            'en' => 'Select features available in your project'
        ],
        'showcase.sections.sharing_settings' => [
            'vi' => 'Cài đặt chia sẻ',
            'en' => 'Sharing Settings'
        ],
        'showcase.sections.sharing_settings_help' => [
            'vi' => 'Thiết lập quyền truy cập và tương tác với dự án',
            'en' => 'Set access permissions and interaction with the project'
        ],

        // ===== SHOWCASE FORM FEATURES =====
        'showcase.features.has_tutorial' => [
            'vi' => 'Hướng dẫn step-by-step',
            'en' => 'Step-by-step Tutorial'
        ],
        'showcase.features.has_tutorial_help' => [
            'vi' => 'Dự án có kèm hướng dẫn chi tiết từng bước',
            'en' => 'Project includes detailed step-by-step instructions'
        ],
        'showcase.features.has_calculations' => [
            'vi' => 'Tính toán kỹ thuật',
            'en' => 'Technical Calculations'
        ],
        'showcase.features.has_calculations_help' => [
            'vi' => 'Bao gồm các tính toán và phân tích chi tiết',
            'en' => 'Includes detailed calculations and analysis'
        ],
        'showcase.features.has_cad_files' => [
            'vi' => 'File CAD đính kèm',
            'en' => 'CAD Files Attached'
        ],
        'showcase.features.has_cad_files_help' => [
            'vi' => 'File 3D, bản vẽ kỹ thuật có thể tải xuống',
            'en' => '3D files, technical drawings available for download'
        ],

        // ===== SHOWCASE FORM SHARING =====
        'showcase.sharing.is_public' => [
            'vi' => 'Công khai',
            'en' => 'Public'
        ],
        'showcase.sharing.is_public_help' => [
            'vi' => 'Cho phép mọi người xem và tìm kiếm dự án',
            'en' => 'Allow everyone to view and search the project'
        ],
        'showcase.sharing.allow_downloads' => [
            'vi' => 'Cho phép tải xuống',
            'en' => 'Allow Downloads'
        ],
        'showcase.sharing.allow_downloads_help' => [
            'vi' => 'Người dùng có thể tải file đính kèm',
            'en' => 'Users can download attached files'
        ],
        'showcase.sharing.allow_comments' => [
            'vi' => 'Cho phép bình luận',
            'en' => 'Allow Comments'
        ],
        'showcase.sharing.allow_comments_help' => [
            'vi' => 'Người dùng có thể bình luận và thảo luận',
            'en' => 'Users can comment and discuss'
        ],

        // ===== SHOWCASE VALIDATION MESSAGES =====
        'showcase.validation.title_required' => [
            'vi' => 'Tiêu đề showcase là bắt buộc.',
            'en' => 'Showcase title is required.'
        ],
        'showcase.validation.title_min' => [
            'vi' => 'Tiêu đề phải có ít nhất 5 ký tự.',
            'en' => 'Title must be at least 5 characters.'
        ],
        'showcase.validation.title_max' => [
            'vi' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'en' => 'Title must not exceed 255 characters.'
        ],
        'showcase.validation.title_regex' => [
            'vi' => 'Tiêu đề chỉ được chứa chữ cái, số, dấu gạch ngang và khoảng trắng.',
            'en' => 'Title may only contain letters, numbers, hyphens and spaces.'
        ],
        'showcase.validation.description_required' => [
            'vi' => 'Mô tả chi tiết là bắt buộc.',
            'en' => 'Detailed description is required.'
        ],
        'showcase.validation.description_min' => [
            'vi' => 'Mô tả phải có ít nhất 50 ký tự để cung cấp thông tin đầy đủ.',
            'en' => 'Description must be at least 50 characters to provide complete information.'
        ],
        'showcase.validation.description_min_words' => [
            'vi' => 'Mô tả cần có ít nhất 20 từ để cung cấp thông tin đầy đủ.',
            'en' => 'Description needs at least 20 words to provide complete information.'
        ],
        'showcase.validation.description_max' => [
            'vi' => 'Mô tả không được vượt quá 5000 ký tự.',
            'en' => 'Description must not exceed 5000 characters.'
        ],
        'showcase.validation.cover_image_required' => [
            'vi' => 'Hình ảnh đại diện là bắt buộc.',
            'en' => 'Cover image is required.'
        ],
        'showcase.validation.cover_image_image' => [
            'vi' => 'File phải là hình ảnh.',
            'en' => 'File must be an image.'
        ],
        'showcase.validation.cover_image_mimes' => [
            'vi' => 'Hình ảnh phải có định dạng: JPEG, PNG, JPG, GIF, WebP.',
            'en' => 'Image must be in format: JPEG, PNG, JPG, GIF, WebP.'
        ],
        'showcase.validation.cover_image_max' => [
            'vi' => 'Hình ảnh không được vượt quá 5MB.',
            'en' => 'Image must not exceed 5MB.'
        ],
        'showcase.validation.cover_image_dimensions' => [
            'vi' => 'Hình ảnh phải có kích thước tối thiểu 400x300px và tối đa 4000x4000px.',
            'en' => 'Image must have minimum dimensions of 400x300px and maximum 4000x4000px.'
        ],
        'showcase.validation.location_max' => [
            'vi' => 'Địa điểm không được vượt quá 255 ký tự.',
            'en' => 'Location must not exceed 255 characters.'
        ],
        'showcase.validation.location_regex' => [
            'vi' => 'Địa điểm chứa ký tự không hợp lệ.',
            'en' => 'Location contains invalid characters.'
        ],
        'showcase.validation.usage_max' => [
            'vi' => 'Lĩnh vực ứng dụng không được vượt quá 500 ký tự.',
            'en' => 'Application field must not exceed 500 characters.'
        ],
        'showcase.validation.software_used_array' => [
            'vi' => 'Phần mềm sử dụng phải là danh sách.',
            'en' => 'Software used must be a list.'
        ],
        'showcase.validation.software_used_max' => [
            'vi' => 'Chỉ được chọn tối đa 10 phần mềm.',
            'en' => 'Maximum 10 software can be selected.'
        ],
        'showcase.validation.software_used_in' => [
            'vi' => 'Phần mềm được chọn không hợp lệ.',
            'en' => 'Selected software is invalid.'
        ],
        'showcase.validation.materials_max' => [
            'vi' => 'Vật liệu không được vượt quá 1000 ký tự.',
            'en' => 'Materials must not exceed 1000 characters.'
        ],
        'showcase.validation.materials_regex' => [
            'vi' => 'Vật liệu chứa ký tự không hợp lệ.',
            'en' => 'Materials contain invalid characters.'
        ],
        'showcase.validation.manufacturing_process_in' => [
            'vi' => 'Quy trình sản xuất được chọn không hợp lệ.',
            'en' => 'Selected manufacturing process is invalid.'
        ],
        'showcase.validation.complexity_level_in' => [
            'vi' => 'Mức độ phức tạp được chọn không hợp lệ.',
            'en' => 'Selected complexity level is invalid.'
        ],
        'showcase.validation.industry_application_max' => [
            'vi' => 'Ứng dụng ngành không được vượt quá 500 ký tự.',
            'en' => 'Industry application must not exceed 500 characters.'
        ],
        'showcase.validation.industry_application_regex' => [
            'vi' => 'Ứng dụng ngành chứa ký tự không hợp lệ.',
            'en' => 'Industry application contains invalid characters.'
        ],
        'showcase.validation.floors_in' => [
            'vi' => 'Quy mô dự án được chọn không hợp lệ.',
            'en' => 'Selected project scale is invalid.'
        ],
        'showcase.validation.category_in' => [
            'vi' => 'Danh mục được chọn không hợp lệ.',
            'en' => 'Selected category is invalid.'
        ],
        'showcase.validation.multiple_images_array' => [
            'vi' => 'Hình ảnh phải là danh sách.',
            'en' => 'Images must be a list.'
        ],
        'showcase.validation.multiple_images_max' => [
            'vi' => 'Chỉ được upload tối đa 10 hình ảnh.',
            'en' => 'Maximum 10 images can be uploaded.'
        ],
        'showcase.validation.multiple_images_image' => [
            'vi' => 'Tất cả file phải là hình ảnh.',
            'en' => 'All files must be images.'
        ],
        'showcase.validation.multiple_images_mimes' => [
            'vi' => 'Hình ảnh phải có định dạng: JPEG, PNG, JPG, GIF, WebP.',
            'en' => 'Images must be in format: JPEG, PNG, JPG, GIF, WebP.'
        ],
        'showcase.validation.multiple_images_max_size' => [
            'vi' => 'Mỗi hình ảnh không được vượt quá 10MB.',
            'en' => 'Each image must not exceed 10MB.'
        ],
        'showcase.validation.multiple_images_dimensions' => [
            'vi' => 'Hình ảnh phải có kích thước tối thiểu 200x200px và tối đa 4000x4000px.',
            'en' => 'Images must have minimum dimensions of 200x200px and maximum 4000x4000px.'
        ],
        'showcase.validation.file_attachments_array' => [
            'vi' => 'File đính kèm phải là danh sách.',
            'en' => 'File attachments must be a list.'
        ],
        'showcase.validation.file_attachments_max' => [
            'vi' => 'Chỉ được upload tối đa 10 file đính kèm.',
            'en' => 'Maximum 10 file attachments can be uploaded.'
        ],
        'showcase.validation.file_attachments_file' => [
            'vi' => 'Tất cả phải là file hợp lệ.',
            'en' => 'All must be valid files.'
        ],
        'showcase.validation.file_attachments_max_size' => [
            'vi' => 'Mỗi file không được vượt quá 50MB.',
            'en' => 'Each file must not exceed 50MB.'
        ],
        'showcase.validation.file_attachments_mimes' => [
            'vi' => 'File phải có định dạng: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, DWG, STEP, STP, IGES, IGS, JPG, JPEG, PNG, GIF, ZIP, RAR, 7Z.',
            'en' => 'Files must be in format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, DWG, STEP, STP, IGES, IGS, JPG, JPEG, PNG, GIF, ZIP, RAR, 7Z.'
        ],
        'showcase.validation.technical_info_required' => [
            'vi' => 'Vui lòng điền ít nhất một thông tin kỹ thuật (phần mềm, vật liệu hoặc quy trình sản xuất).',
            'en' => 'Please fill in at least one technical information (software, materials or manufacturing process).'
        ],
        'showcase.validation.cad_files_consistency' => [
            'vi' => 'Bạn đã chọn "File CAD đính kèm" nhưng chưa upload file nào.',
            'en' => 'You selected "CAD Files Attached" but have not uploaded any files.'
        ],

        // ===== SHOWCASE UPLOAD MESSAGES =====
        'showcase.upload.drag_drop_cover' => [
            'vi' => 'Kéo thả file hoặc click để chọn',
            'en' => 'Drag and drop file or click to select'
        ],
        'showcase.upload.drag_drop_multiple' => [
            'vi' => 'Kéo thả nhiều file hoặc click để chọn',
            'en' => 'Drag and drop multiple files or click to select'
        ],
        'showcase.upload.drag_drop_attachments' => [
            'vi' => 'Kéo thả file hoặc click để chọn',
            'en' => 'Drag and drop files or click to select'
        ],
        'showcase.upload.cover_formats' => [
            'vi' => 'JPG, PNG, WebP (tối đa 5MB)',
            'en' => 'JPG, PNG, WebP (max 5MB)'
        ],
        'showcase.upload.multiple_formats' => [
            'vi' => 'JPG, PNG, WebP (tối đa 10MB mỗi file, tối đa 10 files)',
            'en' => 'JPG, PNG, WebP (max 10MB per file, max 10 files)'
        ],
        'showcase.upload.attachments_formats' => [
            'vi' => 'CAD files (DWG, STEP, IGES), Documents (PDF, DOC), Spreadsheets (XLS), Images (JPG, PNG)',
            'en' => 'CAD files (DWG, STEP, IGES), Documents (PDF, DOC), Spreadsheets (XLS), Images (JPG, PNG)'
        ],
        'showcase.upload.attachments_limits' => [
            'vi' => 'Tối đa 10 files, mỗi file tối đa 50MB',
            'en' => 'Maximum 10 files, 50MB per file'
        ],
        'showcase.upload.attachments_supported' => [
            'vi' => 'Hỗ trợ các định dạng: CAD files (DWG, STEP, IGES), Documents (PDF, DOC, XLS), Images, Archives (ZIP, RAR)',
            'en' => 'Supported formats: CAD files (DWG, STEP, IGES), Documents (PDF, DOC, XLS), Images, Archives (ZIP, RAR)'
        ],
        'showcase.upload.image_auto_compress' => [
            'vi' => 'Hình ảnh sẽ được tự động nén để tối ưu tốc độ tải',
            'en' => 'Images will be automatically compressed for optimal loading speed'
        ],
        'showcase.upload.image_gallery_help' => [
            'vi' => 'Thêm nhiều góc nhìn, chi tiết kỹ thuật, và quá trình thực hiện dự án.',
            'en' => 'Add multiple views, technical details, and project implementation process.'
        ],
        'showcase.upload.image_optimization' => [
            'vi' => 'Hình ảnh sẽ được tự động nén xuống 1920x1080px để tối ưu hiệu suất mà vẫn giữ chất lượng tốt.',
            'en' => 'Images will be automatically compressed to 1920x1080px for optimal performance while maintaining good quality.'
        ],

        // ===== SIDEBAR USER DASHBOARD - MISSING TRANSLATIONS =====

        'sidebar.user_dashboard.profile' => [
            'vi' => 'Hồ sơ',
            'en' => 'Profile'
        ],
        'sidebar.user_dashboard.notifications' => [
            'vi' => 'Thông báo',
            'en' => 'Notifications'
        ],
        'sidebar.user_dashboard.messages' => [
            'vi' => 'Tin nhắn',
            'en' => 'Messages'
        ],
        'sidebar.user_dashboard.settings' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'sidebar.user_dashboard.showcases' => [
            'vi' => 'Showcase của tôi',
            'en' => 'My Showcases'
        ],
        'sidebar.user_dashboard.all_messages' => [
            'vi' => 'Tất cả tin nhắn',
            'en' => 'All Messages'
        ],
        'sidebar.user_dashboard.group_conversations' => [
            'vi' => 'Nhóm chat',
            'en' => 'Group Conversations'
        ],
        'sidebar.user_dashboard.create_group' => [
            'vi' => 'Tạo nhóm',
            'en' => 'Create Group'
        ],
        'sidebar.user_dashboard.new_message' => [
            'vi' => 'Tin nhắn mới',
            'en' => 'New Message'
        ],

        // ===== ADDITIONAL DASHBOARD SIDEBAR TRANSLATIONS =====

        'sidebar.user_dashboard.activity' => [
            'vi' => 'Hoạt động',
            'en' => 'Activity'
        ],
        'sidebar.user_dashboard.bookmarks' => [
            'vi' => 'Đã lưu',
            'en' => 'Bookmarks'
        ],
        'sidebar.user_dashboard.threads' => [
            'vi' => 'Bài viết của tôi',
            'en' => 'My Threads'
        ],
        'sidebar.user_dashboard.comments' => [
            'vi' => 'Bình luận',
            'en' => 'Comments'
        ],
        'sidebar.user_dashboard.following' => [
            'vi' => 'Đang theo dõi',
            'en' => 'Following'
        ],

        // ===== SECTION HEADERS =====

        'sidebar.sections.dashboard' => [
            'vi' => 'Bảng điều khiển',
            'en' => 'Dashboard'
        ],
        'sidebar.sections.community' => [
            'vi' => 'Cộng đồng',
            'en' => 'Community'
        ],
        'sidebar.sections.messages' => [
            'vi' => 'Tin nhắn',
            'en' => 'Messages'
        ],
        'sidebar.sections.quick_actions' => [
            'vi' => 'Thao tác nhanh',
            'en' => 'Quick Actions'
        ],

        // ===== QUICK ACTIONS =====

        'sidebar.quick_actions.new_thread' => [
            'vi' => 'Bài viết mới',
            'en' => 'New Thread'
        ],
        'sidebar.quick_actions.browse_marketplace' => [
            'vi' => 'Duyệt sản phẩm',
            'en' => 'Browse Products'
        ],
        'sidebar.quick_actions.create_showcase' => [
            'vi' => 'Tạo Showcase',
            'en' => 'Create Showcase'
        ],
        'sidebar.quick_actions.browse_forums' => [
            'vi' => 'Duyệt diễn đàn',
            'en' => 'Browse Forums'
        ],

        // ===== HELP & SUPPORT =====

        'sidebar.help.documentation' => [
            'vi' => 'Tài liệu',
            'en' => 'Documentation'
        ],
        'sidebar.help.contact_support' => [
            'vi' => 'Liên hệ hỗ trợ',
            'en' => 'Contact Support'
        ],
        'sidebar.help.faq' => [
            'vi' => 'FAQ',
            'en' => 'FAQ'
        ],
        'sidebar.help.help_support' => [
            'vi' => 'Trợ giúp & Hỗ trợ',
            'en' => 'Help & Support'
        ],

        // ===== SHOWCASE EDIT FORM TRANSLATIONS =====
        'showcase.edit.category' => [
            'vi' => 'Danh mục',
            'en' => 'Category'
        ],
        'showcase.edit.select_category' => [
            'vi' => 'Chọn danh mục',
            'en' => 'Select Category'
        ],
        'showcase.edit.current_status' => [
            'vi' => 'Trạng thái hiện tại',
            'en' => 'Current Status'
        ],
        'showcase.edit.status_help' => [
            'vi' => 'Trạng thái này được quản lý bởi admin và không thể thay đổi',
            'en' => 'This status is managed by admin and cannot be changed'
        ],
        'showcase.edit.description' => [
            'vi' => 'Mô tả chi tiết',
            'en' => 'Detailed Description'
        ],
        'showcase.edit.additional_details' => [
            'vi' => 'Thông tin bổ sung',
            'en' => 'Additional Details'
        ],
        'showcase.edit.tags' => [
            'vi' => 'Tags',
            'en' => 'Tags'
        ],
        'showcase.edit.add_tags' => [
            'vi' => 'Thêm tags (cách nhau bằng dấu phẩy)',
            'en' => 'Add tags (separated by commas)'
        ],
        'showcase.edit.tags_help' => [
            'vi' => 'Thêm các từ khóa để giúp người khác tìm thấy showcase của bạn dễ dàng hơn',
            'en' => 'Add keywords to help others find your showcase more easily'
        ],
        'showcase.edit.featured' => [
            'vi' => 'Đánh dấu nổi bật',
            'en' => 'Mark as Featured'
        ],
        'showcase.edit.featured_help' => [
            'vi' => 'Showcase nổi bật sẽ được hiển thị ưu tiên trong danh sách',
            'en' => 'Featured showcases will be displayed with priority in listings'
        ],
        'showcase.edit.drag_images' => [
            'vi' => 'Kéo thả hình ảnh vào đây',
            'en' => 'Drag and drop images here'
        ],
        'showcase.edit.image_formats' => [
            'vi' => 'Hỗ trợ: JPG, PNG, WebP (tối đa 10MB mỗi file)',
            'en' => 'Supported: JPG, PNG, WebP (max 10MB per file)'
        ],
        'showcase.edit.select_images' => [
            'vi' => 'Chọn hình ảnh',
            'en' => 'Select Images'
        ],
        'showcase.edit.drag_files' => [
            'vi' => 'Kéo thả file vào đây',
            'en' => 'Drag and drop files here'
        ],
        'showcase.edit.file_formats' => [
            'vi' => 'Hỗ trợ: CAD files, PDF, DOC, XLS, ZIP (tối đa 50MB mỗi file)',
            'en' => 'Supported: CAD files, PDF, DOC, XLS, ZIP (max 50MB per file)'
        ],
        'showcase.edit.select_files' => [
            'vi' => 'Chọn file',
            'en' => 'Select Files'
        ],

        // ===== THREAD CREATE FORM TRANSLATIONS =====

        // Upload area
        'thread.upload.drag_drop_title' => [
            'vi' => 'Kéo thả hình ảnh vào đây',
            'en' => 'Drag and drop images here'
        ],
        'thread.upload.or_select' => [
            'vi' => 'hoặc',
            'en' => 'or'
        ],
        'thread.upload.select_files' => [
            'vi' => 'chọn file',
            'en' => 'select files'
        ],
        'thread.upload.from_computer' => [
            'vi' => 'từ máy tính',
            'en' => 'from computer'
        ],
        'thread.upload.help_text' => [
            'vi' => 'Tối đa 10 hình ảnh, mỗi file không quá 5MB. Hỗ trợ: JPG, PNG, GIF, WebP',
            'en' => 'Maximum 10 images, each file no more than 5MB. Supported: JPG, PNG, GIF, WebP'
        ],
        'thread.upload.image_label' => [
            'vi' => 'Tải Lên Hình Ảnh',
            'en' => 'Upload Images'
        ],

        // Navigation buttons
        'thread.nav.previous' => [
            'vi' => 'Trước',
            'en' => 'Previous'
        ],
        'thread.nav.next' => [
            'vi' => 'Tiếp Theo',
            'en' => 'Next'
        ],
        'thread.nav.creating' => [
            'vi' => 'Đang tạo...',
            'en' => 'Creating...'
        ],

        // Poll section
        'thread.poll.title' => [
            'vi' => 'Khảo Sát (Tùy Chọn)',
            'en' => 'Poll (Optional)'
        ],
        'thread.poll.subtitle' => [
            'vi' => 'Tạo một cuộc khảo sát để thu thập ý kiến từ cộng đồng',
            'en' => 'Create a poll to gather opinions from the community'
        ],
        'thread.poll.enable_title' => [
            'vi' => 'Thêm khảo sát vào chủ đề này',
            'en' => 'Add poll to this thread'
        ],
        'thread.poll.enable_subtitle' => [
            'vi' => 'Cho phép thành viên bình chọn và thể hiện ý kiến',
            'en' => 'Allow members to vote and express opinions'
        ],
        'thread.poll.question_label' => [
            'vi' => 'Câu Hỏi Khảo Sát',
            'en' => 'Poll Question'
        ],
        'thread.poll.options_label' => [
            'vi' => 'Các Lựa Chọn',
            'en' => 'Options'
        ],
        'thread.poll.add_option' => [
            'vi' => 'Thêm Lựa Chọn',
            'en' => 'Add Option'
        ],
        'thread.poll.max_options_title' => [
            'vi' => 'Số lựa chọn tối đa',
            'en' => 'Maximum options'
        ],
        'thread.poll.single_choice' => [
            'vi' => 'Chỉ một lựa chọn',
            'en' => 'Single choice only'
        ],
        'thread.poll.unlimited_choice' => [
            'vi' => 'Không giới hạn',
            'en' => 'Unlimited'
        ],
        'thread.poll.settings_title' => [
            'vi' => 'Tùy chọn khảo sát',
            'en' => 'Poll settings'
        ],
        'thread.poll.allow_change_vote' => [
            'vi' => 'Cho phép thay đổi lựa chọn',
            'en' => 'Allow changing votes'
        ],
        'thread.poll.show_results_public' => [
            'vi' => 'Hiển thị kết quả công khai',
            'en' => 'Show results publicly'
        ],
        'thread.poll.view_without_vote' => [
            'vi' => 'Cho phép xem kết quả mà không cần bình chọn',
            'en' => 'Allow viewing results without voting'
        ],
        'thread.poll.close_after_title' => [
            'vi' => 'Thời gian đóng khảo sát',
            'en' => 'Poll closing time'
        ],
        'thread.poll.close_after' => [
            'vi' => 'Đóng khảo sát sau',
            'en' => 'Close poll after'
        ],

        // Validation messages
        'thread.validation.title_required' => [
            'vi' => 'Vui lòng nhập tiêu đề',
            'en' => 'Please enter title'
        ],
        'thread.validation.category_required' => [
            'vi' => 'Vui lòng chọn danh mục',
            'en' => 'Please select category'
        ],
        'thread.validation.forum_required' => [
            'vi' => 'Vui lòng chọn diễn đàn',
            'en' => 'Please select forum'
        ],
        'thread.validation.content_required' => [
            'vi' => 'Vui lòng nhập nội dung cho chủ đề',
            'en' => 'Please enter content for the thread'
        ],
        'thread.validation.poll_question_required' => [
            'vi' => 'Vui lòng nhập câu hỏi khảo sát',
            'en' => 'Please enter poll question'
        ],
        'thread.validation.poll_min_options' => [
            'vi' => 'Khảo sát cần ít nhất 2 lựa chọn',
            'en' => 'Poll needs at least 2 options'
        ],
        'thread.validation.check_info' => [
            'vi' => 'Vui lòng kiểm tra lại các thông tin đã nhập',
            'en' => 'Please check the information entered'
        ],
        'thread.validation.select_existing_showcase' => [
            'vi' => 'Vui lòng chọn showcase có sẵn',
            'en' => 'Please select existing showcase'
        ],

        // Review section
        'thread.review.no_images' => [
            'vi' => 'Không có hình ảnh',
            'en' => 'No images'
        ],
        'thread.review.images_count' => [
            'vi' => 'hình ảnh',
            'en' => 'images'
        ],
        'thread.review.no_poll' => [
            'vi' => 'Không có khảo sát',
            'en' => 'No poll'
        ],
        'thread.review.has_poll' => [
            'vi' => 'Có khảo sát',
            'en' => 'Has poll'
        ],
        'thread.review.poll_options' => [
            'vi' => 'lựa chọn',
            'en' => 'options'
        ],
        'thread.review.no_showcase' => [
            'vi' => 'Không có showcase',
            'en' => 'No showcase'
        ],
        'thread.review.new_showcase' => [
            'vi' => 'Showcase mới',
            'en' => 'New showcase'
        ],
        'thread.review.attach_existing' => [
            'vi' => 'Đính kèm',
            'en' => 'Attach'
        ],
        'thread.review.existing_showcase' => [
            'vi' => 'Showcase có sẵn',
            'en' => 'Existing showcase'
        ],

        // Showcase section
        'thread.showcase.type_new_subtitle' => [
            'vi' => 'Tạo showcase mới từ chủ đề này',
            'en' => 'Create new showcase from this thread'
        ],
        'thread.showcase.type_existing_subtitle' => [
            'vi' => 'Đính kèm showcase đã có sẵn',
            'en' => 'Attach existing showcase'
        ],
        'thread.showcase.select_existing_placeholder' => [
            'vi' => 'Chọn showcase',
            'en' => 'Select showcase'
        ],
        'thread.showcase.complexity_placeholder' => [
            'vi' => 'Chọn độ phức tạp',
            'en' => 'Select complexity'
        ],
        'thread.showcase.complexity_basic' => [
            'vi' => 'Cơ bản',
            'en' => 'Basic'
        ],
        'thread.showcase.complexity_intermediate' => [
            'vi' => 'Trung bình',
            'en' => 'Intermediate'
        ],
        'thread.showcase.complexity_advanced' => [
            'vi' => 'Nâng cao',
            'en' => 'Advanced'
        ],
        'thread.showcase.complexity_expert' => [
            'vi' => 'Chuyên gia',
            'en' => 'Expert'
        ],

        // File upload
        'thread.file.max_files_error' => [
            'vi' => 'Tối đa 10 file được phép tải lên',
            'en' => 'Maximum 10 files allowed to upload'
        ],
        'thread.file.size_error' => [
            'vi' => 'quá lớn. Tối đa 50MB.',
            'en' => 'is too large. Maximum 50MB.'
        ],
        'thread.file.type_error' => [
            'vi' => 'không được hỗ trợ.',
            'en' => 'is not supported.'
        ],
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
