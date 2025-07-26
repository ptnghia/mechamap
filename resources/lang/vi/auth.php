<?php

/**
 * Authentication Language Lines
 *
 * The following language lines are used during authentication for various
 * messages that we need to display to the user. You are free to modify
 * these language lines according to your application's requirements.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Thông tin đăng nhập không chính xác.',
    'password' => 'Mật khẩu không đúng.',
    'throttle' => 'Quá nhiều lần đăng nhập. Vui lòng thử lại sau :seconds giây.',

    // Login
    'login' => [
        'title' => 'Đăng nhập',
        'welcome_back' => 'Chào mừng trở lại',
        'email_or_username' => 'Email hoặc tên đăng nhập',
        'password' => 'Mật khẩu',
        'remember' => 'Ghi nhớ đăng nhập',
        'submit' => 'Đăng nhập',
        'forgot_password' => 'Quên mật khẩu?',
        'no_account' => 'Chưa có tài khoản?',
        'register_now' => 'Đăng ký ngay',
        'or_login_with' => 'Hoặc đăng nhập bằng',
        'google' => 'Đăng nhập với Google',
        'facebook' => 'Đăng nhập với Facebook',
    ],

    // Register
    'register' => [
        'title' => 'Đăng ký',
        'name' => 'Họ và tên',
        'email' => 'Địa chỉ email',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
        'submit' => 'Đăng ký',
        'have_account' => 'Đã có tài khoản?',
        'login_now' => 'Đăng nhập ngay',
        'agree_terms' => 'Tôi đồng ý với',
        'terms_of_service' => 'Điều khoản dịch vụ',
        'privacy_policy' => 'Chính sách bảo mật',
        'join_community' => 'Tham gia cộng đồng MechaMap',
        'create_account' => 'Tạo tài khoản mới',

        // Additional register keys
        'account_type_placeholder' => 'Chọn loại tài khoản',
        'community_member_title' => 'Thành viên cộng đồng',
        'member_role' => 'Thành viên',
        'member_role_desc' => 'Quyền truy cập đầy đủ diễn đàn, tạo bài viết và tham gia thảo luận',
        'business_partner_title' => 'Đối tác kinh doanh',
        'manufacturer_role' => 'Nhà sản xuất',
        'manufacturer_role_desc' => 'Sản xuất và bán sản phẩm cơ khí, thiết bị công nghiệp',
        'supplier_role' => 'Nhà cung cấp',
        'supplier_role_desc' => 'Cung cấp linh kiện, vật liệu và dịch vụ hỗ trợ',
        'brand_role' => 'Thương hiệu',
        'brand_role_desc' => 'Quảng bá thương hiệu và sản phẩm trên marketplace',
        'account_type_help' => 'Chọn loại tài khoản phù hợp với mục đích sử dụng của bạn',
        'terms_agreement' => 'Tôi đồng ý với <a href="/terms" target="_blank">Điều khoản dịch vụ</a> và <a href="/privacy" target="_blank">Chính sách bảo mật</a>',
        'already_have_account' => 'Đã có tài khoản?',
        'sign_in' => 'Đăng nhập',

        // Wizard keys
        'step1_title' => 'Bước 1: Thông tin cá nhân',
        'wizard_title' => 'Đăng ký tài khoản Mechamapp',
        'step1_subtitle' => 'Tạo tài khoản và chọn loại thành viên',
        'continue_button' => 'Tiếp tục',
        'personal_info_title' => 'Thông tin cá nhân',
        'personal_info_description' => 'Nhập thông tin cá nhân để tạo tài khoản của bạn',
        'name_valid' => 'Tên hợp lệ',
        'username_available' => 'Tên đăng nhập có sẵn',
        'email_valid' => 'Email hợp lệ',
        'email_help' => 'Chúng tôi sẽ gửi email xác thực đến địa chỉ này',
        'account_type_title' => 'Chọn loại tài khoản',
        'account_type_description' => 'Chọn loại tài khoản phù hợp nhất với mục đích sử dụng của bạn',
        'community_member_description' => 'Tham gia cộng đồng để học hỏi, chia sẻ và kết nối',
        'recommended' => 'Khuyến nghị',
        'guest_role' => 'Khách',
        'guest_role_desc' => 'Quyền truy cập chỉ xem, không thể tạo bài viết hoặc bình luận',
        'note_community' => 'Bạn có thể nâng cấp tài khoản sau khi đăng ký.',
        'business_partner_description' => 'Dành cho doanh nghiệp muốn bán sản phẩm hoặc dịch vụ',
        'note_business' => 'Tài khoản doanh nghiệp cần xác thực trước khi truy cập đầy đủ tính năng.',

        // Additional wizard keys
        'step_indicator' => 'Bước :current/:total',
        'step1_label' => 'Thông tin cá nhân',
        'step2_label' => 'Xác thực',
        'progress_complete' => ':percent% hoàn thành',
        'security_note' => 'Thông tin của bạn được bảo mật và mã hóa',
        'auto_saving' => 'Tự động lưu...',
        'step_default' => 'Bước :number',
        'errors_occurred' => 'Có lỗi xảy ra',
    ],

    // Password Reset
    'forgot_password' => [
        'title' => 'Quên mật khẩu',
        'description' => 'Nhập email để nhận liên kết đặt lại mật khẩu',
        'email' => 'Địa chỉ email',
        'submit' => 'Gửi liên kết',
        'back_to_login' => 'Quay lại đăng nhập',
        'reset_sent' => 'Liên kết đặt lại mật khẩu đã được gửi!',
    ],

    'reset_password' => [
        'title' => 'Đặt lại mật khẩu',
        'subtitle' => 'Tạo mật khẩu mới cho tài khoản của bạn',
        'heading' => 'Tạo mật khẩu mới',
        'description' => 'Vui lòng nhập mật khẩu mới cho tài khoản của bạn',
        'email' => 'Địa chỉ email',
        'password' => 'Mật khẩu mới',
        'new_password' => 'Mật khẩu mới',
        'password_confirmation' => 'Xác nhận mật khẩu mới',
        'confirm_password' => 'Xác nhận mật khẩu mới',
        'password_placeholder' => 'Nhập mật khẩu mới',
        'confirm_placeholder' => 'Nhập lại mật khẩu mới',
        'password_hint' => 'Sử dụng ít nhất 8 ký tự với chữ cái, số và ký hiệu',
        'submit' => 'Đặt lại mật khẩu',
        'update_password' => 'Cập nhật mật khẩu',
        'success' => 'Mật khẩu đã được đặt lại thành công!',
        'password_match' => 'Mật khẩu khớp',
        'password_mismatch' => 'Mật khẩu không khớp',
        'tips' => [
            'strong_title' => 'Mật khẩu mạnh',
            'strong_desc' => 'Sử dụng ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký hiệu',
            'avoid_personal_title' => 'Tránh thông tin cá nhân',
            'avoid_personal_desc' => 'Không sử dụng tên, ngày sinh, số điện thoại trong mật khẩu',
            'unique_title' => 'Mật khẩu duy nhất',
            'unique_desc' => 'Không sử dụng lại mật khẩu từ các tài khoản khác',
        ],
    ],

    // Logout
    'logout' => [
        'title' => 'Đăng xuất',
        'confirm' => 'Bạn có chắc muốn đăng xuất?',
        'success' => 'Đã đăng xuất thành công',
    ],

    // Email Verification
    'verification' => [
        'title' => 'Xác thực email',
        'description' => 'Vui lòng kiểm tra email và nhấp vào liên kết xác thực',
        'resend' => 'Gửi lại email xác thực',
        'verified' => 'Email đã được xác thực thành công!',
        'not_verified' => 'Email chưa được xác thực',
    ],

    // User Roles
    'roles' => [
        'admin' => 'Quản trị viên',
        'moderator' => 'Điều hành viên',
        'senior_member' => 'Thành viên cao cấp',
        'member' => 'Thành viên',
        'guest' => 'Khách',
        'verified_partner' => 'Đối tác xác thực',
        'manufacturer' => 'Nhà sản xuất',
        'supplier' => 'Nhà cung cấp',
        'brand' => 'Thương hiệu',
    ],

    // Messages
    'messages' => [
        'login_success' => 'Đăng nhập thành công!',
        'login_failed' => 'Thông tin đăng nhập không chính xác',
        'register_success' => 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản',
        'logout_success' => 'Đăng xuất thành công',
        'password_reset_sent' => 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn',
        'password_reset_success' => 'Mật khẩu đã được đặt lại thành công',
        'email_verified' => 'Email đã được xác thực thành công',
        'account_locked' => 'Tài khoản đã bị khóa',
        'too_many_attempts' => 'Quá nhiều lần thử. Vui lòng thử lại sau',
    ],

    // Password Confirmation
    'confirm_password' => 'Xác nhận mật khẩu',
    'confirm' => 'Xác nhận',
    'secure_area_message' => 'Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu trước khi tiếp tục.',

    // Additional keys for Blade compatibility
    'email_or_username_label' => 'Email hoặc tên đăng nhập',
    'password_label' => 'Mật khẩu',
    'remember_login' => 'Ghi nhớ đăng nhập',
    'forgot_password_link' => 'Quên mật khẩu?',
    'login_button' => 'Đăng nhập',
    'or_login_with' => 'hoặc đăng nhập với',
    'login_with_google' => 'Đăng nhập với Google',
    'login_with_facebook' => 'Đăng nhập với Facebook',
    'no_account' => 'Chưa có tài khoản?',
    'register_now' => 'Đăng ký ngay',

    // Community features
    'connect_engineers' => 'Kết nối với kỹ sư',
    'join_discussions' => 'Tham gia thảo luận',
    'share_experience' => 'Chia sẻ kinh nghiệm',
    'marketplace_products' => 'Sản phẩm marketplace',

    // Registration additional keys
    'create_new_account' => 'Tạo tài khoản mới',
    'welcome_to_mechamap' => 'Chào mừng đến với MechaMap',
    'create_account_journey' => 'Tạo tài khoản để bắt đầu hành trình kỹ thuật của bạn',
    'full_name_label' => 'Họ và tên',
    'full_name_placeholder' => 'Nhập họ và tên của bạn',
    'username_label' => 'Tên đăng nhập',
    'username_placeholder' => 'Chọn tên đăng nhập',
    'username_help' => 'Tên đăng nhập chỉ chứa chữ cái, số và dấu gạch dưới',
    'email_label' => 'Địa chỉ email',
    'email_placeholder' => 'Nhập địa chỉ email của bạn',
    'password_placeholder' => 'Tạo mật khẩu mạnh',
    'password_help' => 'Mật khẩu phải có ít nhất 8 ký tự với chữ hoa, chữ thường và số',
    'confirm_password_label' => 'Xác nhận mật khẩu',
    'confirm_password_placeholder' => 'Nhập lại mật khẩu của bạn',
    'account_type_label' => 'Loại tài khoản',

    // Password strength
    'password_strength' => [
        'length' => 'ít nhất 8 ký tự',
        'uppercase' => 'chữ hoa',
        'lowercase' => 'chữ thường',
        'number' => 'số',
        'special' => 'ký tự đặc biệt',
        'weak' => 'Yếu - Cần',
        'medium' => 'Trung bình - Cần',
        'strong' => 'Mạnh - Mật khẩu tốt',
    ],

    // Registration wizard
    'register' => [
        // Step 2 - Business Information
        'step2_title' => 'Thông tin doanh nghiệp',
        'step2_subtitle' => 'Hoàn thành thông tin doanh nghiệp để xác minh tài khoản',

        // Company Information
        'company_info_title' => 'Thông tin công ty',
        'company_info_description' => 'Vui lòng cung cấp thông tin chi tiết về công ty của bạn',
        'company_name_label' => 'Tên công ty',
        'company_name_help' => 'Tên đầy đủ của công ty như trong giấy phép kinh doanh',
        'business_license_label' => 'Số giấy phép kinh doanh',
        'tax_code_label' => 'Mã số thuế',
        'tax_code_help' => 'Mã số thuế của công ty (10-13 chữ số)',
        'company_description_label' => 'Mô tả công ty',
        'company_description_help' => 'Mô tả ngắn gọn về hoạt động kinh doanh của công ty',
        'business_field_label' => 'Lĩnh vực kinh doanh',
        'business_field_help' => 'Chọn các lĩnh vực kinh doanh chính của công ty (có thể chọn nhiều)',

        // Contact Information
        'contact_info_title' => 'Thông tin liên hệ',
        'contact_info_description' => 'Thông tin liên hệ chính thức của công ty',
        'company_phone' => 'Số điện thoại công ty',
        'company_email_label' => 'Email công ty',
        'company_email_help' => 'Email chính thức của công ty (khác với email cá nhân)',
        'company_address' => 'Địa chỉ công ty',

        // Verification Documents
        'verification_docs_title' => 'Tài liệu xác minh',
        'verification_docs_description' => 'Tải lên các tài liệu cần thiết để xác minh doanh nghiệp',
        'file_upload_title' => 'Tải lên tài liệu',
        'file_upload_support' => 'Hỗ trợ: PDF, JPG, PNG. ',
        'file_upload_size' => 'Tối đa 5MB mỗi file.',
        'choose_documents' => 'Chọn tài liệu',
        'document_suggestions' => 'Gợi ý: Giấy phép kinh doanh, Giấy chứng nhận đăng ký thuế, Hợp đồng thuê văn phòng',

        // Important Notes
        'important_notes_title' => 'Lưu ý quan trọng',
        'note_verification_required' => 'Tài khoản cần được xác minh trước khi có thể sử dụng đầy đủ tính năng',
        'note_verification_time' => 'Quá trình xác minh có thể mất 1-3 ngày làm việc',
        'note_email_notification' => 'Bạn sẽ nhận được email thông báo khi xác minh hoàn tất',
        'note_pending_access' => 'Trong thời gian chờ xác minh, bạn có thể sử dụng các tính năng cơ bản',

        // Buttons
        'back_button' => 'Quay lại',
        'complete_button' => 'Hoàn thành đăng ký',

        // Success Messages
        'step1_completed' => 'Thông tin cơ bản đã được lưu. Vui lòng hoàn thành thông tin doanh nghiệp.',

        // Business categories (kept for backward compatibility)
        'business_categories' => [
            'manufacturing' => 'Sản xuất & Chế tạo',
            'automotive' => 'Ô tô & Xe máy',
            'aerospace' => 'Hàng không & Vũ trụ',
            'energy' => 'Năng lượng & Điện lực',
            'construction' => 'Xây dựng & Hạ tầng',
            'electronics' => 'Điện tử & Viễn thông',
            'medical' => 'Y tế & Thiết bị y tế',
            'food_beverage' => 'Thực phẩm & Đồ uống',
            'textile' => 'Dệt may & Thời trang',
            'chemical' => 'Hóa chất & Dược phẩm',
            'mining' => 'Khai thác & Khoáng sản',
            'marine' => 'Hàng hải & Đóng tàu',
            'agriculture' => 'Nông nghiệp & Thủy sản',
            'packaging' => 'Bao bì & In ấn',
            'consulting' => 'Tư vấn & Dịch vụ kỹ thuật',
            'education' => 'Giáo dục & Đào tạo',
            'research' => 'Nghiên cứu & Phát triển',
            'other' => 'Khác',
        ],
    ],

];
