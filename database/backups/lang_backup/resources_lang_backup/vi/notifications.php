<?php

/**
 * Notifications Translation File - Vietnamese
 * Complete translation coverage for notifications functionality
 */

return [
  'new_message' => 'Tin nhắn mới',
  'new_comment' => 'Bình luận mới',
  'new_reply' => 'Phản hồi mới',
  'new_follower' => 'Người theo dõi mới',
  'new_like' => 'Lượt thích mới',
  'new_share' => 'Chia sẻ mới',
  'new_mention' => 'Nhắc đến mới',
  'new_invitation' => 'Lời mời mới',
  'new_request' => 'Yêu cầu mới',
  'system_update' => 'Cập nhật hệ thống',
  'maintenance_notice' => 'Thông báo bảo trì',
  'security_alert' => 'Cảnh báo bảo mật',
  'account_verified' => 'Tài khoản đã xác thực',
  'password_changed' => 'Mật khẩu đã thay đổi',
  'email_verified' => 'Email đã xác thực',
  'profile_updated' => 'Hồ sơ đã cập nhật',
  'settings_saved' => 'Cài đặt đã lưu',
  'subscription_expired' => 'Đăng ký đã hết hạn',
  'payment_received' => 'Đã nhận thanh toán',
  'order_confirmed' => 'Đơn hàng đã xác nhận',

  // UI Elements
  'ui' => [
    'header' => 'Thông báo',
    'mark_all_read' => 'Đánh dấu tất cả đã đọc',
    'mark_as_read' => 'Đánh dấu đã đọc',
    'view_all' => 'Xem tất cả',
    'manage' => 'Quản lý',
    'view' => 'Xem',
    'messages' => 'Tin nhắn',
    'no_notifications' => 'Không có thông báo nào',
    'loading' => 'Đang tải...',
    'error' => 'Có lỗi xảy ra khi tải thông báo',
  ],

  // Stats
  'stats' => [
    'total' => 'Tổng cộng',
    'unread' => 'Chưa đọc',
    'today' => 'Hôm nay',
    'this_week' => 'Tuần này',
    'this_month' => 'Tháng này',
  ],

  // Time formats
  'time' => [
    'just_now' => 'Vừa xong',
    'minutes_ago' => ':count phút trước',
    'hours_ago' => ':count giờ trước',
    'days_ago' => ':count ngày trước',
    'weeks_ago' => ':count tuần trước',
    'months_ago' => ':count tháng trước',
    'years_ago' => ':count năm trước',
  ],

  // Notification Types
  'types' => [
    'new_message' => 'Tin nhắn mới',
    'message_reply' => 'Phản hồi tin nhắn',
    'thread_created' => 'Thread mới',
    'thread_replied' => 'Phản hồi thread',
    'comment_mention' => 'Được nhắc đến',
    'thread_liked' => 'Thread được thích',
    'comment_liked' => 'Bình luận được thích',
    'product_approved' => 'Sản phẩm được duyệt',
    'product_rejected' => 'Sản phẩm bị từ chối',
    'order_update' => 'Cập nhật đơn hàng',
    'payment_received' => 'Đã nhận thanh toán',
    'commission_paid' => 'Hoa hồng đã trả',
    'system_announcement' => 'Thông báo hệ thống',
    'role_changed' => 'Thay đổi vai trò',
    'account_verified' => 'Tài khoản đã xác thực',
    'password_changed' => 'Mật khẩu đã thay đổi',
    'login_from_new_device' => 'Đăng nhập từ thiết bị mới',
    'business_verified' => 'Doanh nghiệp đã xác thực',
    'business_rejected' => 'Doanh nghiệp bị từ chối',
    'quote_request' => 'Yêu cầu báo giá',
    'user_registered' => 'Người dùng mới',
    'profile_updated' => 'Hồ sơ đã cập nhật',
    'follow_user' => 'Người theo dõi mới',
  ],

  // Message Templates
  'messages' => [
    'new_message' => 'Bạn có tin nhắn mới từ :sender: :preview',
    'message_reply' => ':sender đã phản hồi tin nhắn của bạn: :preview',
    'thread_created' => ':user đã tạo thread mới ":title" trong :forum',
    'thread_replied' => ':user đã phản hồi thread ":title"',
    'comment_mention' => ':user đã nhắc đến bạn trong bình luận',
    'thread_liked' => ':user đã thích thread ":title" của bạn',
    'comment_liked' => ':user đã thích bình luận của bạn',
    'product_approved' => 'Sản phẩm ":product" của bạn đã được duyệt',
    'product_rejected' => 'Sản phẩm ":product" của bạn bị từ chối: :reason',
    'order_update' => 'Đơn hàng #:order_id đã được cập nhật: :status',
    'payment_received' => 'Đã nhận thanh toán :amount cho đơn hàng #:order_id',
    'commission_paid' => 'Hoa hồng :amount đã được chuyển vào tài khoản',
    'system_announcement' => ':title: :content',
    'role_changed' => 'Vai trò của bạn đã được thay đổi thành :role',
    'account_verified' => 'Tài khoản của bạn đã được xác thực thành công',
    'password_changed' => 'Mật khẩu tài khoản đã được thay đổi lúc :time',
    'login_from_new_device' => 'Tài khoản được đăng nhập từ thiết bị mới: :device tại :location',
    'business_verified' => 'Doanh nghiệp ":business" đã được xác thực',
    'business_rejected' => 'Doanh nghiệp ":business" bị từ chối: :reason',
    'quote_request' => 'Yêu cầu báo giá mới cho sản phẩm ":product" từ :customer',
    'user_registered' => 'Chào mừng :name đến với MechaMap!',
    'profile_updated' => 'Hồ sơ của bạn đã được cập nhật thành công',
    'follow_user' => ':user đã bắt đầu theo dõi bạn',
  ],

  // Actions
  'actions' => [
    'view_message' => 'Xem tin nhắn',
    'view_thread' => 'Xem thread',
    'view_product' => 'Xem sản phẩm',
    'view_order' => 'Xem đơn hàng',
    'view_profile' => 'Xem hồ sơ',
    'view_business' => 'Xem doanh nghiệp',
    'view_quote' => 'Xem báo giá',
    'manage_account' => 'Quản lý tài khoản',
    'change_password' => 'Đổi mật khẩu',
    'view_devices' => 'Xem thiết bị',
  ],

  // Status messages
  'status' => [
    'marked_as_read' => 'Đã đánh dấu là đã đọc',
    'marked_all_as_read' => 'Đã đánh dấu tất cả là đã đọc',
    'notification_not_found' => 'Không tìm thấy thông báo',
    'failed_to_mark_read' => 'Không thể đánh dấu đã đọc',
    'failed_to_load' => 'Không thể tải thông báo',
  ],

  // Index page (max 3 levels)
  'index' => [
    'title' => 'Quản lý thông báo',
    'heading' => 'Quản lý thông báo',
    'description' => 'Xem và quản lý tất cả thông báo của bạn',
    'mark_all_read' => 'Đánh dấu tất cả đã đọc',
    'delete_read' => 'Xóa đã đọc',
    'total_notifications' => 'Tổng thông báo',
    'unread_count' => 'Chưa đọc',
    'read_count' => 'Đã đọc',
    'notification_type' => 'Loại thông báo',
    'all_types' => 'Tất cả loại',
    'status_all' => 'Tất cả',
    'status_unread' => 'Chưa đọc',
    'status_read' => 'Đã đọc',
    'notification_list' => 'Danh sách thông báo',
    'mark_unread' => 'Đánh dấu chưa đọc',
    'mark_read' => 'Đánh dấu đã đọc',
    'delete_notification' => 'Xóa thông báo',
    'no_notifications' => 'Không có thông báo nào',
    'no_notifications_type' => 'Không tìm thấy thông báo loại',
    'no_read_notifications' => 'Không có thông báo đã đọc',
    'no_unread_notifications' => 'Không có thông báo chưa đọc',
    'no_notifications_desc' => 'Bạn chưa có thông báo nào. Thông báo sẽ xuất hiện ở đây khi có hoạt động mới.',
    'view_all' => 'Xem tất cả thông báo',
    'confirm_mark_all' => 'Đánh dấu tất cả thông báo là đã đọc?',
    'confirm_delete' => 'Xóa thông báo này?',
  ],
];
