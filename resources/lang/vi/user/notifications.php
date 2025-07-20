<?php

/**
 * Vietnamese translations for user/notifications
 * Enhanced migration with source integration
 * 
 * Structure: user.notifications.*
 * Enhanced: 2025-07-20 02:51:02
 * Source keys found: 28
 * Total keys: 95
 */

return [
    'default' => [
        'title' => 'Thông báo mới',
        'message' => 'Bạn có một thông báo mới.',
    ],
    'thread_created' => [
        'title' => 'Thread mới trong diễn đàn',
        'message' => ':user_name đã tạo thread mới \":thread_title\" trong diễn đàn :forum_name.',
    ],
    'thread_replied' => [
        'title' => 'Có phản hồi mới',
        'message' => ':user_name đã phản hồi trong thread \":thread_title\".',
    ],
    'comment_mentioned' => [
        'title' => 'Bạn được nhắc đến',
        'message' => ':user_name đã nhắc đến bạn trong một bình luận.',
    ],
    'user_followed' => [
        'title' => 'Người theo dõi mới',
        'message' => ':follower_name đã bắt đầu theo dõi bạn.',
    ],
    'achievement_unlocked' => [
        'title' => 'Thành tích mới!',
        'message' => 'Chúc mừng! Bạn đã đạt được thành tích \":achievement_name\".',
    ],
    'product_out_of_stock' => [
        'title' => 'Sản phẩm hết hàng',
        'message' => 'Sản phẩm \":product_name\" đã hết hàng.',
    ],
    'price_drop_alert' => [
        'title' => 'Giá sản phẩm giảm',
        'message' => 'Sản phẩm \":product_name\" đã giảm giá từ :old_price xuống :new_price.',
    ],
    'wishlist_available' => [
        'title' => 'Sản phẩm trong wishlist có sẵn',
        'message' => 'Sản phẩm \":product_name\" trong danh sách yêu thích của bạn đã có sẵn.',
    ],
    'order_status_updated' => [
        'title' => 'Cập nhật đơn hàng',
        'message' => 'Đơn hàng #:order_id của bạn đã được cập nhật trạng thái: :status.',
    ],
    'review_received' => [
        'title' => 'Đánh giá mới',
        'message' => 'Sản phẩm \":product_name\" của bạn đã nhận được đánh giá :rating sao.',
    ],
    'seller_message' => [
        'title' => 'Tin nhắn từ người bán',
        'message' => ':seller_name đã gửi tin nhắn cho bạn về sản phẩm \":product_name\".',
    ],
    'login_from_new_device' => [
        'title' => 'Đăng nhập từ thiết bị mới',
        'message' => 'Tài khoản của bạn đã được đăng nhập từ thiết bị mới: :device_info tại :location.',
    ],
    'password_changed' => [
        'title' => 'Mật khẩu đã được thay đổi',
        'message' => 'Mật khẩu tài khoản của bạn đã được thay đổi thành công vào lúc :time.',
    ],
    'business_verified' => [
        'title' => 'Doanh nghiệp đã được xác minh',
        'message' => 'Tài khoản doanh nghiệp của bạn đã được xác minh thành công.',
    ],
    'business_rejected' => [
        'title' => 'Doanh nghiệp bị từ chối',
        'message' => 'Yêu cầu xác minh doanh nghiệp của bạn đã bị từ chối. Lý do: :reason.',
    ],
    'weekly_digest' => [
        'title' => 'Tóm tắt tuần',
        'message' => 'Đây là tóm tắt hoạt động của bạn trong tuần qua.',
    ],
    'system_announcement' => [
        'title' => 'Thông báo hệ thống',
        'message' => ':message',
    ],
    'maintenance_scheduled' => [
        'title' => 'Bảo trì hệ thống',
        'message' => 'Hệ thống sẽ được bảo trì từ :start_time đến :end_time.',
    ],
    'actions' => [
        'view_thread' => 'Xem thread',
        'view_comment' => 'Xem bình luận',
        'view_profile' => 'Xem hồ sơ',
        'view_achievement' => 'Xem thành tích',
        'view_product' => 'Xem sản phẩm',
        'view_order' => 'Xem đơn hàng',
        'view_review' => 'Xem đánh giá',
        'view_message' => 'Xem tin nhắn',
        'view_devices' => 'Quản lý thiết bị',
        'view_security' => 'Cài đặt bảo mật',
        'view_digest' => 'Xem chi tiết',
        'view_details' => 'Xem chi tiết',
        'take_action' => 'Thực hiện',
        'dismiss' => 'Bỏ qua',
    ],
    'categories' => [
        'forum' => 'Diễn đàn',
        'social' => 'Xã hội',
        'marketplace' => 'Thị trường',
        'security' => 'Bảo mật',
        'system' => 'Hệ thống',
        'business' => 'Doanh nghiệp',
    ],
    'time' => [
        'just_now' => 'vừa xong',
        'minutes_ago' => ':count phút trước',
        'hours_ago' => ':count giờ trước',
        'days_ago' => ':count ngày trước',
        'weeks_ago' => ':count tuần trước',
        'months_ago' => ':count tháng trước',
        'years_ago' => ':count năm trước',
    ],
    'preferences' => [
        'email_notifications' => 'Thông báo qua email',
        'browser_notifications' => 'Thông báo trình duyệt',
        'mobile_notifications' => 'Thông báo di động',
        'digest_frequency' => 'Tần suất tóm tắt',
        'notification_types' => 'Loại thông báo',
    ],
    'frequency' => [
        'immediately' => 'Ngay lập tức',
        'hourly' => 'Mỗi giờ',
        'daily' => 'Hàng ngày',
        'weekly' => 'Hàng tuần',
        'never' => 'Không bao giờ',
    ],
    'status' => [
        'unread' => 'Chưa đọc',
        'read' => 'Đã đọc',
        'archived' => 'Đã lưu trữ',
        'deleted' => 'Đã xóa',
    ],
    'bulk_actions' => [
        'mark_all_read' => 'Đánh dấu tất cả đã đọc',
        'mark_selected_read' => 'Đánh dấu đã chọn đã đọc',
        'delete_all' => 'Xóa tất cả',
        'delete_selected' => 'Xóa đã chọn',
        'archive_all' => 'Lưu trữ tất cả',
        'archive_selected' => 'Lưu trữ đã chọn',
    ],
    'errors' => [
        'notification_not_found' => 'Không tìm thấy thông báo.',
        'permission_denied' => 'Bạn không có quyền thực hiện hành động này.',
        'invalid_notification_type' => 'Loại thông báo không hợp lệ.',
        'failed_to_send' => 'Không thể gửi thông báo.',
        'failed_to_update' => 'Không thể cập nhật thông báo.',
    ],
    'success' => [
        'notification_sent' => 'Thông báo đã được gửi thành công.',
        'notification_read' => 'Thông báo đã được đánh dấu đã đọc.',
        'notification_deleted' => 'Thông báo đã được xóa.',
        'preferences_updated' => 'Cài đặt thông báo đã được cập nhật.',
        'all_marked_read' => 'Tất cả thông báo đã được đánh dấu đã đọc.',
    ],
];
