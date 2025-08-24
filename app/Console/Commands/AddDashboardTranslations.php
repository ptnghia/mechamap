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
    protected $signature = 'translations:add-dashboard {--dry-run : Show what would be added without actually adding}';

    /**
     * The console command description.
     */
    protected $description = 'Add dashboard translation keys for Vietnamese and English';

    /**
     * Translation keys to add
     */
    protected $translations = [
        // Orders translations
        'orders.index.title' => [
            'vi' => 'Quản lý đơn hàng',
            'en' => 'Order Management'
        ],
        'orders.index.heading' => [
            'vi' => 'Đơn hàng của tôi',
            'en' => 'My Orders'
        ],
        'orders.index.description' => [
            'vi' => 'Quản lý và theo dõi tất cả đơn hàng của bạn',
            'en' => 'Manage and track all your orders'
        ],
        'orders.index.browse_products' => [
            'vi' => 'Duyệt sản phẩm',
            'en' => 'Browse Products'
        ],
        'orders.index.total_orders' => [
            'vi' => 'Tổng đơn hàng',
            'en' => 'Total Orders'
        ],
        'orders.index.completed' => [
            'vi' => 'Hoàn thành',
            'en' => 'Completed'
        ],
        'orders.index.pending' => [
            'vi' => 'Chờ xử lý',
            'en' => 'Pending'
        ],
        'orders.index.total_spent' => [
            'vi' => 'Tổng chi tiêu',
            'en' => 'Total Spent'
        ],
        'orders.index.search_placeholder' => [
            'vi' => 'Tìm kiếm đơn hàng...',
            'en' => 'Search orders...'
        ],
        'orders.index.all_status' => [
            'vi' => 'Tất cả trạng thái',
            'en' => 'All Status'
        ],
        'orders.index.confirmed' => [
            'vi' => 'Đã xác nhận',
            'en' => 'Confirmed'
        ],
        'orders.index.processing' => [
            'vi' => 'Đang xử lý',
            'en' => 'Processing'
        ],
        'orders.index.shipped' => [
            'vi' => 'Đã gửi hàng',
            'en' => 'Shipped'
        ],
        'orders.index.delivered' => [
            'vi' => 'Đã giao hàng',
            'en' => 'Delivered'
        ],
        'orders.index.cancelled' => [
            'vi' => 'Đã hủy',
            'en' => 'Cancelled'
        ],
        'orders.index.date_from' => [
            'vi' => 'Từ ngày',
            'en' => 'From Date'
        ],
        'orders.index.date_to' => [
            'vi' => 'Đến ngày',
            'en' => 'To Date'
        ],
        'orders.index.filter' => [
            'vi' => 'Lọc',
            'en' => 'Filter'
        ],
        'orders.index.clear' => [
            'vi' => 'Xóa bộ lọc',
            'en' => 'Clear Filters'
        ],
        'orders.index.orders_list' => [
            'vi' => 'Danh sách đơn hàng',
            'en' => 'Orders List'
        ],
        'orders.index.order_number' => [
            'vi' => 'Mã đơn hàng',
            'en' => 'Order Number'
        ],
        'orders.index.order_items' => [
            'vi' => 'Sản phẩm trong đơn hàng',
            'en' => 'Order Items'
        ],
        'orders.index.seller' => [
            'vi' => 'Người bán',
            'en' => 'Seller'
        ],
        'orders.index.payment_method' => [
            'vi' => 'Phương thức thanh toán',
            'en' => 'Payment Method'
        ],
        'orders.index.notes' => [
            'vi' => 'Ghi chú',
            'en' => 'Notes'
        ],
        'orders.index.view' => [
            'vi' => 'Xem',
            'en' => 'View'
        ],
        'orders.index.pay_now' => [
            'vi' => 'Thanh toán ngay',
            'en' => 'Pay Now'
        ],
        'orders.index.cancel' => [
            'vi' => 'Hủy đơn',
            'en' => 'Cancel'
        ],
        'orders.index.download' => [
            'vi' => 'Tải xuống',
            'en' => 'Download'
        ],
        'orders.index.paid' => [
            'vi' => 'Đã thanh toán',
            'en' => 'Paid'
        ],
        'orders.index.payment_pending' => [
            'vi' => 'Chờ thanh toán',
            'en' => 'Payment Pending'
        ],
        'orders.index.payment_failed' => [
            'vi' => 'Thanh toán thất bại',
            'en' => 'Payment Failed'
        ],
        'orders.index.no_orders' => [
            'vi' => 'Chưa có đơn hàng nào',
            'en' => 'No orders yet'
        ],
        'orders.index.no_orders_desc' => [
            'vi' => 'Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!',
            'en' => 'You haven\'t placed any orders yet. Start shopping now!'
        ],
        'orders.index.start_shopping' => [
            'vi' => 'Bắt đầu mua sắm',
            'en' => 'Start Shopping'
        ],
        'orders.index.cancel_order' => [
            'vi' => 'Hủy đơn hàng',
            'en' => 'Cancel Order'
        ],
        'orders.index.cancel_confirm' => [
            'vi' => 'Bạn có chắc chắn muốn hủy đơn hàng này không?',
            'en' => 'Are you sure you want to cancel this order?'
        ],
        'orders.index.cancel_reason' => [
            'vi' => 'Lý do hủy đơn',
            'en' => 'Cancellation Reason'
        ],
        'orders.index.cancel_reason_placeholder' => [
            'vi' => 'Nhập lý do hủy đơn hàng (tùy chọn)...',
            'en' => 'Enter cancellation reason (optional)...'
        ],
        'orders.index.close' => [
            'vi' => 'Đóng',
            'en' => 'Close'
        ],
        'orders.index.confirm_cancel' => [
            'vi' => 'Xác nhận hủy',
            'en' => 'Confirm Cancel'
        ],

        // Dashboard common translations
        'dashboard.welcome' => [
            'vi' => 'Chào mừng trở lại',
            'en' => 'Welcome back'
        ],
        'dashboard.overview' => [
            'vi' => 'Tổng quan',
            'en' => 'Overview'
        ],
        'dashboard.recent_activity' => [
            'vi' => 'Hoạt động gần đây',
            'en' => 'Recent Activity'
        ],
        'dashboard.quick_actions' => [
            'vi' => 'Thao tác nhanh',
            'en' => 'Quick Actions'
        ],
        'dashboard.statistics' => [
            'vi' => 'Thống kê',
            'en' => 'Statistics'
        ],
        'dashboard.my_profile' => [
            'vi' => 'Hồ sơ của tôi',
            'en' => 'My Profile'
        ],
        'dashboard.settings' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'dashboard.logout' => [
            'vi' => 'Đăng xuất',
            'en' => 'Logout'
        ],

        // Profile translations
        'profile.edit.title' => [
            'vi' => 'Chỉnh sửa hồ sơ',
            'en' => 'Edit Profile'
        ],
        'profile.edit.heading' => [
            'vi' => 'Thông tin cá nhân',
            'en' => 'Personal Information'
        ],
        'profile.edit.description' => [
            'vi' => 'Cập nhật thông tin cá nhân và cài đặt tài khoản của bạn',
            'en' => 'Update your personal information and account settings'
        ],

        // Activity translations
        'activity.index.title' => [
            'vi' => 'Hoạt động của tôi',
            'en' => 'My Activity'
        ],
        'activity.index.heading' => [
            'vi' => 'Lịch sử hoạt động',
            'en' => 'Activity History'
        ],
        'activity.index.description' => [
            'vi' => 'Theo dõi tất cả hoạt động và tương tác của bạn trên hệ thống',
            'en' => 'Track all your activities and interactions on the system'
        ],

        // Notifications translations
        'notifications.index.title' => [
            'vi' => 'Thông báo',
            'en' => 'Notifications'
        ],
        'notifications.index.heading' => [
            'vi' => 'Trung tâm thông báo',
            'en' => 'Notification Center'
        ],
        'notifications.index.description' => [
            'vi' => 'Quản lý tất cả thông báo và cập nhật quan trọng',
            'en' => 'Manage all your notifications and important updates'
        ],
        'notifications.index.unread_notifications' => [
            'vi' => 'Chưa đọc',
            'en' => 'Unread'
        ],
        'notifications.index.read_notifications' => [
            'vi' => 'Đã đọc',
            'en' => 'Read'
        ],
        'notifications.index.filter_type' => [
            'vi' => 'Lọc theo loại',
            'en' => 'Filter by Type'
        ],
        'notifications.index.filter_status' => [
            'vi' => 'Lọc theo trạng thái',
            'en' => 'Filter by Status'
        ],
        'notifications.index.filter_priority' => [
            'vi' => 'Lọc theo độ ưu tiên',
            'en' => 'Filter by Priority'
        ],
        'notifications.index.search' => [
            'vi' => 'Tìm kiếm',
            'en' => 'Search'
        ],
        'notifications.index.search_placeholder' => [
            'vi' => 'Tìm kiếm thông báo...',
            'en' => 'Search notifications...'
        ],
        'notifications.index.notifications_list' => [
            'vi' => 'Danh sách thông báo',
            'en' => 'Notifications List'
        ],
        'notifications.index.thread_notifications' => [
            'vi' => 'Thông báo bài viết',
            'en' => 'Thread Notifications'
        ],
        'notifications.index.comment_notifications' => [
            'vi' => 'Thông báo bình luận',
            'en' => 'Comment Notifications'
        ],
        'notifications.index.system_notifications' => [
            'vi' => 'Thông báo hệ thống',
            'en' => 'System Notifications'
        ],
        'notifications.index.marketplace_notifications' => [
            'vi' => 'Thông báo thị trường',
            'en' => 'Marketplace Notifications'
        ],
        'notifications.index.all_status' => [
            'vi' => 'Tất cả trạng thái',
            'en' => 'All Status'
        ],
        'notifications.index.unread_only' => [
            'vi' => 'Chỉ chưa đọc',
            'en' => 'Unread Only'
        ],
        'notifications.index.read_only' => [
            'vi' => 'Chỉ đã đọc',
            'en' => 'Read Only'
        ],
        'notifications.index.all_priorities' => [
            'vi' => 'Tất cả độ ưu tiên',
            'en' => 'All Priorities'
        ],
        'notifications.index.urgent' => [
            'vi' => 'Khẩn cấp',
            'en' => 'Urgent'
        ],
        'notifications.index.high' => [
            'vi' => 'Cao',
            'en' => 'High'
        ],
        'notifications.index.normal' => [
            'vi' => 'Bình thường',
            'en' => 'Normal'
        ],
        'notifications.index.low' => [
            'vi' => 'Thấp',
            'en' => 'Low'
        ],
        'notifications.types.new_message' => [
            'vi' => 'Tin nhắn mới',
            'en' => 'New Message'
        ],
        'notifications.messages.new_message_from' => [
            'vi' => 'Tin nhắn mới từ :username: ":preview"',
            'en' => 'New message from :username: ":preview"'
        ],
        'notifications.index.new' => [
            'vi' => 'Mới',
            'en' => 'New'
        ],
        'notifications.index.delete' => [
            'vi' => 'Xóa',
            'en' => 'Delete'
        ],
        'notifications.index.view_details' => [
            'vi' => 'Xem chi tiết',
            'en' => 'View Details'
        ],
        'notifications.index.confirm_mark_all_read' => [
            'vi' => 'Bạn có chắc chắn muốn đánh dấu tất cả thông báo là đã đọc?',
            'en' => 'Are you sure you want to mark all notifications as read?'
        ],
        'notifications.index.error_occurred' => [
            'vi' => 'Đã xảy ra lỗi',
            'en' => 'An error occurred'
        ],
        'notifications.index.confirm_delete' => [
            'vi' => 'Bạn có chắc chắn muốn xóa thông báo này?',
            'en' => 'Are you sure you want to delete this notification?'
        ],

        // Settings translations
        'settings.index.title' => [
            'vi' => 'Cài đặt tài khoản',
            'en' => 'Account Settings'
        ],
        'settings.index.heading' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'settings.index.description' => [
            'vi' => 'Quản lý cài đặt tài khoản và tùy chọn cá nhân',
            'en' => 'Manage your account settings and personal preferences'
        ],

        // Community translations
        'community.threads.title' => [
            'vi' => 'Bài viết của tôi',
            'en' => 'My Threads'
        ],
        'community.bookmarks.title' => [
            'vi' => 'Bookmark của tôi',
            'en' => 'My Bookmarks'
        ],
        'community.showcases.title' => [
            'vi' => 'Showcase của tôi',
            'en' => 'My Showcases'
        ],
        'community.comments.title' => [
            'vi' => 'Bình luận của tôi',
            'en' => 'My Comments'
        ],

        // Dashboard main page translations
        'dashboard.welcome_back' => [
            'vi' => 'Chào mừng trở lại',
            'en' => 'Welcome back'
        ],
        'dashboard.welcome_description' => [
            'vi' => 'Đây là những gì đang diễn ra với tài khoản của bạn hôm nay.',
            'en' => 'Here\'s what\'s happening with your account today.'
        ],
        'dashboard.new_thread' => [
            'vi' => 'Bài viết mới',
            'en' => 'New Thread'
        ],
        'dashboard.add_product' => [
            'vi' => 'Thêm sản phẩm',
            'en' => 'Add Product'
        ],
        'dashboard.view_all' => [
            'vi' => 'Xem tất cả',
            'en' => 'View All'
        ],
        'dashboard.no_recent_activity' => [
            'vi' => 'Không có hoạt động gần đây để hiển thị.',
            'en' => 'No recent activity to show.'
        ],
        'dashboard.create_first_thread' => [
            'vi' => 'Tạo bài viết đầu tiên',
            'en' => 'Create Your First Thread'
        ],
        'dashboard.my_recent_threads' => [
            'vi' => 'Bài viết gần đây của tôi',
            'en' => 'My Recent Threads'
        ],
        'dashboard.comments' => [
            'vi' => 'bình luận',
            'en' => 'comments'
        ],
        'dashboard.likes' => [
            'vi' => 'lượt thích',
            'en' => 'likes'
        ],
        'dashboard.community' => [
            'vi' => 'Cộng đồng',
            'en' => 'Community'
        ],
        'dashboard.account' => [
            'vi' => 'Tài khoản',
            'en' => 'Account'
        ],
        'dashboard.support' => [
            'vi' => 'Hỗ trợ',
            'en' => 'Support'
        ],
        'dashboard.create_new_thread' => [
            'vi' => 'Tạo bài viết mới',
            'en' => 'Create New Thread'
        ],
        'dashboard.create_showcase' => [
            'vi' => 'Tạo Showcase',
            'en' => 'Create Showcase'
        ],
        'dashboard.browse_forums' => [
            'vi' => 'Duyệt diễn đàn',
            'en' => 'Browse Forums'
        ],
        'dashboard.edit_profile' => [
            'vi' => 'Chỉnh sửa hồ sơ',
            'en' => 'Edit Profile'
        ],
        'dashboard.notifications' => [
            'vi' => 'Thông báo',
            'en' => 'Notifications'
        ],
        'dashboard.help_support' => [
            'vi' => 'Trợ giúp & Hỗ trợ',
            'en' => 'Help & Support'
        ],
        'dashboard.documentation' => [
            'vi' => 'Tài liệu',
            'en' => 'Documentation'
        ],
        'dashboard.contact_support' => [
            'vi' => 'Liên hệ hỗ trợ',
            'en' => 'Contact Support'
        ],
        'dashboard.faq' => [
            'vi' => 'Câu hỏi thường gặp',
            'en' => 'FAQ'
        ],
        'dashboard.vs_last_period' => [
            'vi' => 'so với kỳ trước',
            'en' => 'vs last period'
        ],
        'dashboard.view_details' => [
            'vi' => 'Xem chi tiết',
            'en' => 'View Details'
        ],

        // Threads translations
        'threads.index.title' => [
            'vi' => 'Bài viết của tôi',
            'en' => 'My Threads'
        ],
        'threads.index.heading' => [
            'vi' => 'Quản lý bài viết',
            'en' => 'Manage Threads'
        ],
        'threads.index.description' => [
            'vi' => 'Quản lý và theo dõi tất cả bài viết của bạn',
            'en' => 'Manage and track all your threads'
        ],
        'threads.index.create_thread' => [
            'vi' => 'Tạo bài viết',
            'en' => 'Create Thread'
        ],
        'threads.index.total_threads' => [
            'vi' => 'Tổng bài viết',
            'en' => 'Total Threads'
        ],
        'threads.index.published' => [
            'vi' => 'Đã xuất bản',
            'en' => 'Published'
        ],
        'threads.index.draft' => [
            'vi' => 'Bản nháp',
            'en' => 'Draft'
        ],
        'threads.index.total_views' => [
            'vi' => 'Tổng lượt xem',
            'en' => 'Total Views'
        ],
        'threads.index.search_placeholder' => [
            'vi' => 'Tìm kiếm bài viết...',
            'en' => 'Search threads...'
        ],
        'threads.index.all_status' => [
            'vi' => 'Tất cả trạng thái',
            'en' => 'All Status'
        ],
        'threads.index.all_forums' => [
            'vi' => 'Tất cả diễn đàn',
            'en' => 'All Forums'
        ],
        'threads.index.filter' => [
            'vi' => 'Lọc',
            'en' => 'Filter'
        ],
        'threads.index.clear' => [
            'vi' => 'Xóa bộ lọc',
            'en' => 'Clear Filters'
        ],
        'threads.index.threads_list' => [
            'vi' => 'Danh sách bài viết',
            'en' => 'Threads List'
        ],
        'threads.index.forum' => [
            'vi' => 'Diễn đàn',
            'en' => 'Forum'
        ],
        'threads.index.views' => [
            'vi' => 'lượt xem',
            'en' => 'views'
        ],
        'threads.index.replies' => [
            'vi' => 'trả lời',
            'en' => 'replies'
        ],
        'threads.index.edit' => [
            'vi' => 'Chỉnh sửa',
            'en' => 'Edit'
        ],
        'threads.index.delete' => [
            'vi' => 'Xóa',
            'en' => 'Delete'
        ],
        'threads.index.no_threads' => [
            'vi' => 'Chưa có bài viết nào',
            'en' => 'No threads yet'
        ],
        'threads.index.no_threads_desc' => [
            'vi' => 'Bạn chưa tạo bài viết nào. Hãy bắt đầu chia sẻ kiến thức!',
            'en' => 'You haven\'t created any threads yet. Start sharing your knowledge!'
        ],
        'threads.index.start_writing' => [
            'vi' => 'Bắt đầu viết',
            'en' => 'Start Writing'
        ],

        // Comments translations
        'comments.index.title' => [
            'vi' => 'Bình luận của tôi',
            'en' => 'My Comments'
        ],
        'comments.index.heading' => [
            'vi' => 'Quản lý bình luận',
            'en' => 'Manage Comments'
        ],
        'comments.index.description' => [
            'vi' => 'Quản lý và theo dõi tất cả bình luận của bạn',
            'en' => 'Manage and track all your comments'
        ],
        'comments.index.total_comments' => [
            'vi' => 'Tổng bình luận',
            'en' => 'Total Comments'
        ],
        'comments.index.verified' => [
            'vi' => 'Đã xác minh',
            'en' => 'Verified'
        ],
        'comments.index.helpful' => [
            'vi' => 'Hữu ích',
            'en' => 'Helpful'
        ],
        'comments.index.total_likes' => [
            'vi' => 'Tổng lượt thích',
            'en' => 'Total Likes'
        ],
        'comments.index.search_placeholder' => [
            'vi' => 'Tìm kiếm bình luận...',
            'en' => 'Search comments...'
        ],
        'comments.index.all_types' => [
            'vi' => 'Tất cả loại',
            'en' => 'All Types'
        ],
        'comments.index.solution' => [
            'vi' => 'Giải pháp',
            'en' => 'Solution'
        ],
        'comments.index.question' => [
            'vi' => 'Câu hỏi',
            'en' => 'Question'
        ],
        'comments.index.discussion' => [
            'vi' => 'Thảo luận',
            'en' => 'Discussion'
        ],
        'comments.index.comments_list' => [
            'vi' => 'Danh sách bình luận',
            'en' => 'Comments List'
        ],
        'comments.index.thread' => [
            'vi' => 'Bài viết',
            'en' => 'Thread'
        ],
        'comments.index.view' => [
            'vi' => 'Xem',
            'en' => 'View'
        ],
        'comments.index.no_comments' => [
            'vi' => 'Chưa có bình luận nào',
            'en' => 'No comments yet'
        ],
        'comments.index.no_comments_desc' => [
            'vi' => 'Bạn chưa có bình luận nào. Hãy tham gia thảo luận!',
            'en' => 'You haven\'t made any comments yet. Join the discussion!'
        ],
        'comments.index.start_commenting' => [
            'vi' => 'Bắt đầu bình luận',
            'en' => 'Start Commenting'
        ],

        // Additional threads translations
        'threads.index.status' => [
            'vi' => 'Trạng thái',
            'en' => 'Status'
        ],
        'threads.index.stats' => [
            'vi' => 'Thống kê',
            'en' => 'Stats'
        ],
        'threads.index.created' => [
            'vi' => 'Tạo lúc',
            'en' => 'Created'
        ],
        'threads.index.actions' => [
            'vi' => 'Thao tác',
            'en' => 'Actions'
        ],
        'threads.index.rejected' => [
            'vi' => 'Bị từ chối',
            'en' => 'Rejected'
        ],
        'threads.index.all_solved' => [
            'vi' => 'Tất cả trạng thái giải quyết',
            'en' => 'All Solved Status'
        ],
        'threads.index.solved' => [
            'vi' => 'Đã giải quyết',
            'en' => 'Solved'
        ],
        'threads.index.unsolved' => [
            'vi' => 'Chưa giải quyết',
            'en' => 'Unsolved'
        ],
        'threads.index.delete_thread' => [
            'vi' => 'Xóa bài viết',
            'en' => 'Delete Thread'
        ],
        'threads.index.delete_confirm' => [
            'vi' => 'Bạn có chắc chắn muốn xóa bài viết này không?',
            'en' => 'Are you sure you want to delete this thread?'
        ],
        'threads.index.cancel' => [
            'vi' => 'Hủy',
            'en' => 'Cancel'
        ],

        // Sidebar translations
        'sidebar.dashboard' => [
            'vi' => 'Dashboard',
            'en' => 'Dashboard'
        ],
        'sidebar.profile' => [
            'vi' => 'Hồ sơ',
            'en' => 'Profile'
        ],
        'sidebar.activity' => [
            'vi' => 'Hoạt động',
            'en' => 'Activity'
        ],
        'sidebar.notifications' => [
            'vi' => 'Thông báo',
            'en' => 'Notifications'
        ],
        'sidebar.settings' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'sidebar.community' => [
            'vi' => 'Cộng đồng',
            'en' => 'Community'
        ],
        'sidebar.my_threads' => [
            'vi' => 'Bài viết của tôi',
            'en' => 'My Threads'
        ],
        'sidebar.bookmarks' => [
            'vi' => 'Bookmark',
            'en' => 'Bookmarks'
        ],
        'sidebar.showcases' => [
            'vi' => 'Showcase',
            'en' => 'Showcases'
        ],
        'sidebar.marketplace' => [
            'vi' => 'Thị trường',
            'en' => 'Marketplace'
        ],
        'sidebar.orders' => [
            'vi' => 'Đơn hàng',
            'en' => 'Orders'
        ],
        'sidebar.downloads' => [
            'vi' => 'Tải xuống',
            'en' => 'Downloads'
        ],
        'sidebar.wishlist' => [
            'vi' => 'Danh sách yêu thích',
            'en' => 'Wishlist'
        ],
        'sidebar.seller_dashboard' => [
            'vi' => 'Dashboard người bán',
            'en' => 'Seller Dashboard'
        ],
        'sidebar.quick_actions' => [
            'vi' => 'Thao tác nhanh',
            'en' => 'Quick Actions'
        ],
        'sidebar.new_thread' => [
            'vi' => 'Bài viết mới',
            'en' => 'New Thread'
        ],
        'sidebar.browse_products' => [
            'vi' => 'Duyệt sản phẩm',
            'en' => 'Browse Products'
        ],
        'sidebar.add_product' => [
            'vi' => 'Thêm sản phẩm',
            'en' => 'Add Product'
        ],
        'sidebar.create_showcase' => [
            'vi' => 'Tạo Showcase',
            'en' => 'Create Showcase'
        ],

        // Profile translations
        'profile.edit.title' => [
            'vi' => 'Chỉnh sửa hồ sơ',
            'en' => 'Edit Profile'
        ],
        'profile.edit.description' => [
            'vi' => 'Cập nhật thông tin hồ sơ và cài đặt tài khoản của bạn',
            'en' => 'Update your profile information and account settings'
        ],
        'profile.edit.profile_information' => [
            'vi' => 'Thông tin hồ sơ',
            'en' => 'Profile Information'
        ],
        'profile.edit.update_password' => [
            'vi' => 'Cập nhật mật khẩu',
            'en' => 'Update Password'
        ],
        'profile.edit.delete_account' => [
            'vi' => 'Xóa tài khoản',
            'en' => 'Delete Account'
        ],
        'profile.edit.name' => [
            'vi' => 'Tên',
            'en' => 'Name'
        ],
        'profile.edit.email' => [
            'vi' => 'Email',
            'en' => 'Email'
        ],
        'profile.edit.current_password' => [
            'vi' => 'Mật khẩu hiện tại',
            'en' => 'Current Password'
        ],
        'profile.edit.new_password' => [
            'vi' => 'Mật khẩu mới',
            'en' => 'New Password'
        ],
        'profile.edit.confirm_password' => [
            'vi' => 'Xác nhận mật khẩu',
            'en' => 'Confirm Password'
        ],
        'profile.edit.save' => [
            'vi' => 'Lưu',
            'en' => 'Save'
        ],
        'profile.edit.saved' => [
            'vi' => 'Đã lưu',
            'en' => 'Saved'
        ],
        'profile.edit.delete_warning' => [
            'vi' => 'Khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu sẽ bị xóa vĩnh viễn.',
            'en' => 'Once your account is deleted, all of its resources and data will be permanently deleted.'
        ],
        'profile.edit.delete_confirm' => [
            'vi' => 'Bạn có chắc chắn muốn xóa tài khoản của mình không?',
            'en' => 'Are you sure you want to delete your account?'
        ],
        'profile.edit.cancel' => [
            'vi' => 'Hủy',
            'en' => 'Cancel'
        ],
        'profile.edit.delete' => [
            'vi' => 'Xóa tài khoản',
            'en' => 'Delete Account'
        ],

        // Comments management translations
        'comments.index.title' => [
            'vi' => 'Quản lý bình luận',
            'en' => 'Manage Comments'
        ],
        'comments.index.description' => [
            'vi' => 'Quản lý và theo dõi tất cả bình luận của bạn',
            'en' => 'Manage and track all your comments'
        ],
        'comments.index.my_comments' => [
            'vi' => 'Bình luận của tôi',
            'en' => 'My Comments'
        ],
        'comments.index.total_comments' => [
            'vi' => 'Tổng bình luận',
            'en' => 'Total Comments'
        ],
        'comments.index.approved' => [
            'vi' => 'Đã duyệt',
            'en' => 'Approved'
        ],
        'comments.index.pending' => [
            'vi' => 'Chờ duyệt',
            'en' => 'Pending'
        ],
        'comments.index.rejected' => [
            'vi' => 'Bị từ chối',
            'en' => 'Rejected'
        ],
        'comments.index.search_placeholder' => [
            'vi' => 'Tìm kiếm bình luận...',
            'en' => 'Search comments...'
        ],
        'comments.index.all_threads' => [
            'vi' => 'Tất cả bài viết',
            'en' => 'All Threads'
        ],
        'comments.index.all_status' => [
            'vi' => 'Tất cả trạng thái',
            'en' => 'All Status'
        ],
        'comments.index.filter' => [
            'vi' => 'Lọc',
            'en' => 'Filter'
        ],
        'comments.index.clear' => [
            'vi' => 'Xóa bộ lọc',
            'en' => 'Clear Filters'
        ],
        'comments.index.comment' => [
            'vi' => 'Bình luận',
            'en' => 'Comment'
        ],
        'comments.index.thread' => [
            'vi' => 'Bài viết',
            'en' => 'Thread'
        ],
        'comments.index.status' => [
            'vi' => 'Trạng thái',
            'en' => 'Status'
        ],
        'comments.index.created' => [
            'vi' => 'Tạo lúc',
            'en' => 'Created'
        ],
        'comments.index.actions' => [
            'vi' => 'Thao tác',
            'en' => 'Actions'
        ],
        'comments.index.edit' => [
            'vi' => 'Sửa',
            'en' => 'Edit'
        ],
        'comments.index.delete' => [
            'vi' => 'Xóa',
            'en' => 'Delete'
        ],
        'comments.index.no_comments' => [
            'vi' => 'Chưa có bình luận nào',
            'en' => 'No comments yet'
        ],

        // Settings translations
        'settings.index.title' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'settings.index.description' => [
            'vi' => 'Quản lý cài đặt tài khoản và tùy chọn của bạn',
            'en' => 'Manage your account settings and preferences'
        ],
        'settings.general.title' => [
            'vi' => 'Cài đặt chung',
            'en' => 'General Settings'
        ],
        'settings.notifications.title' => [
            'vi' => 'Cài đặt thông báo',
            'en' => 'Notification Settings'
        ],
        'settings.privacy.title' => [
            'vi' => 'Cài đặt riêng tư',
            'en' => 'Privacy Settings'
        ],
        'settings.language' => [
            'vi' => 'Ngôn ngữ',
            'en' => 'Language'
        ],
        'settings.timezone' => [
            'vi' => 'Múi giờ',
            'en' => 'Timezone'
        ],
        'settings.email_notifications' => [
            'vi' => 'Thông báo email',
            'en' => 'Email Notifications'
        ],
        'settings.push_notifications' => [
            'vi' => 'Thông báo đẩy',
            'en' => 'Push Notifications'
        ],
        'settings.profile_visibility' => [
            'vi' => 'Hiển thị hồ sơ',
            'en' => 'Profile Visibility'
        ],
        'settings.save' => [
            'vi' => 'Lưu cài đặt',
            'en' => 'Save Settings'
        ],
        'settings.saved' => [
            'vi' => 'Đã lưu cài đặt',
            'en' => 'Settings saved'
        ],

        // Marketplace management translations
        'marketplace.seller.title' => [
            'vi' => 'Dashboard người bán',
            'en' => 'Seller Dashboard'
        ],
        'marketplace.seller.description' => [
            'vi' => 'Quản lý sản phẩm và đơn hàng của bạn',
            'en' => 'Manage your products and orders'
        ],
        'marketplace.products.title' => [
            'vi' => 'Sản phẩm của tôi',
            'en' => 'My Products'
        ],
        'marketplace.products.add' => [
            'vi' => 'Thêm sản phẩm',
            'en' => 'Add Product'
        ],
        'marketplace.products.edit' => [
            'vi' => 'Sửa sản phẩm',
            'en' => 'Edit Product'
        ],
        'marketplace.products.delete' => [
            'vi' => 'Xóa sản phẩm',
            'en' => 'Delete Product'
        ],
        'marketplace.products.status' => [
            'vi' => 'Trạng thái',
            'en' => 'Status'
        ],
        'marketplace.products.price' => [
            'vi' => 'Giá',
            'en' => 'Price'
        ],
        'marketplace.products.stock' => [
            'vi' => 'Tồn kho',
            'en' => 'Stock'
        ],
        'marketplace.products.sales' => [
            'vi' => 'Đã bán',
            'en' => 'Sales'
        ],
        'marketplace.products.views' => [
            'vi' => 'Lượt xem',
            'en' => 'Views'
        ],
        'marketplace.orders.title' => [
            'vi' => 'Đơn hàng bán',
            'en' => 'Sales Orders'
        ],
        'marketplace.earnings.title' => [
            'vi' => 'Thu nhập',
            'en' => 'Earnings'
        ],
        'marketplace.analytics.title' => [
            'vi' => 'Phân tích',
            'en' => 'Analytics'
        ],

        // Missing notifications.index translations
        'notifications.index.all_priorities' => [
            'vi' => 'Tất cả độ ưu tiên',
            'en' => 'All Priorities'
        ],
        'notifications.index.all_senders' => [
            'vi' => 'Tất cả người gửi',
            'en' => 'All Senders'
        ],
        'notifications.index.all_time' => [
            'vi' => 'Tất cả thời gian',
            'en' => 'All Time'
        ],
        'notifications.index.sender' => [
            'vi' => 'Người gửi',
            'en' => 'Sender'
        ],
        'notifications.index.date_range' => [
            'vi' => 'Khoảng thời gian',
            'en' => 'Date Range'
        ],
        'notifications.index.clear_filters' => [
            'vi' => 'Xóa bộ lọc',
            'en' => 'Clear Filters'
        ],
        'notifications.index.apply_filters' => [
            'vi' => 'Áp dụng bộ lọc',
            'en' => 'Apply Filters'
        ],
        'notifications.index.today' => [
            'vi' => 'Hôm nay',
            'en' => 'Today'
        ],
        'notifications.index.yesterday' => [
            'vi' => 'Hôm qua',
            'en' => 'Yesterday'
        ],
        'notifications.index.this_week' => [
            'vi' => 'Tuần này',
            'en' => 'This Week'
        ],
        'notifications.index.last_week' => [
            'vi' => 'Tuần trước',
            'en' => 'Last Week'
        ],
        'notifications.index.this_month' => [
            'vi' => 'Tháng này',
            'en' => 'This Month'
        ],
        'notifications.index.last_month' => [
            'vi' => 'Tháng trước',
            'en' => 'Last Month'
        ],
        'notifications.index.archived' => [
            'vi' => 'Đã lưu trữ',
            'en' => 'Archived'
        ],
        'notifications.index.requires_action' => [
            'vi' => 'Cần hành động',
            'en' => 'Requires Action'
        ],

        // Missing UI translations
        'ui.back_to_site' => [
            'vi' => 'Về trang chủ',
            'en' => 'Back to Site'
        ],
        'ui.settings' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'ui.help' => [
            'vi' => 'Trợ giúp',
            'en' => 'Help'
        ],
        'ui.close' => [
            'vi' => 'Đóng',
            'en' => 'Close'
        ],
        'ui.breadcrumb' => [
            'vi' => 'Đường dẫn',
            'en' => 'Breadcrumb'
        ],

        // Missing dashboard layout translations
        'dashboard.layout.title' => [
            'vi' => 'Bảng điều khiển',
            'en' => 'Dashboard'
        ],
        'dashboard.layout.help_support' => [
            'vi' => 'Trợ giúp & Hỗ trợ',
            'en' => 'Help & Support'
        ],
        'dashboard.layout.documentation' => [
            'vi' => 'Tài liệu hướng dẫn',
            'en' => 'Documentation'
        ],
        'dashboard.layout.contact_support' => [
            'vi' => 'Liên hệ hỗ trợ',
            'en' => 'Contact Support'
        ],
        'dashboard.layout.faq' => [
            'vi' => 'Câu hỏi thường gặp',
            'en' => 'Frequently Asked Questions'
        ],
        'dashboard.layout.user_guide' => [
            'vi' => 'Hướng dẫn sử dụng',
            'en' => 'User Guide'
        ],

        // Missing common translations
        'common.actions.view' => [
            'vi' => 'Xem',
            'en' => 'View'
        ],
        'common.actions.edit' => [
            'vi' => 'Sửa',
            'en' => 'Edit'
        ],
        'common.actions.delete' => [
            'vi' => 'Xóa',
            'en' => 'Delete'
        ],
        'common.actions.save' => [
            'vi' => 'Lưu',
            'en' => 'Save'
        ],
        'common.actions.cancel' => [
            'vi' => 'Hủy',
            'en' => 'Cancel'
        ],
        'common.actions.confirm' => [
            'vi' => 'Xác nhận',
            'en' => 'Confirm'
        ],
        'common.status.active' => [
            'vi' => 'Hoạt động',
            'en' => 'Active'
        ],
        'common.status.inactive' => [
            'vi' => 'Không hoạt động',
            'en' => 'Inactive'
        ],
        'common.status.pending' => [
            'vi' => 'Chờ xử lý',
            'en' => 'Pending'
        ],
        'common.status.approved' => [
            'vi' => 'Đã duyệt',
            'en' => 'Approved'
        ],
        'common.status.rejected' => [
            'vi' => 'Bị từ chối',
            'en' => 'Rejected'
        ],
        'common.time.just_now' => [
            'vi' => 'Vừa xong',
            'en' => 'Just now'
        ],
        'common.time.minutes_ago' => [
            'vi' => ':count phút trước',
            'en' => ':count minutes ago'
        ],
        'common.time.hours_ago' => [
            'vi' => ':count giờ trước',
            'en' => ':count hours ago'
        ],
        'common.time.days_ago' => [
            'vi' => ':count ngày trước',
            'en' => ':count days ago'
        ],

        // Missing notifications status and priority translations
        'notifications.index.status_all' => [
            'vi' => 'Tất cả thông báo',
            'en' => 'All Notifications'
        ],
        'notifications.index.status_unread' => [
            'vi' => 'Chưa đọc',
            'en' => 'Unread Only'
        ],
        'notifications.index.status_read' => [
            'vi' => 'Đã đọc',
            'en' => 'Read Only'
        ],
        'notifications.index.priority_urgent' => [
            'vi' => 'Khẩn cấp',
            'en' => 'Urgent'
        ],
        'notifications.index.priority_high' => [
            'vi' => 'Cao',
            'en' => 'High'
        ],
        'notifications.index.priority_normal' => [
            'vi' => 'Bình thường',
            'en' => 'Normal'
        ],
        'notifications.index.priority_low' => [
            'vi' => 'Thấp',
            'en' => 'Low'
        ],

        // Bulk operations translations
        'notifications.index.bulk_mark_read' => [
            'vi' => 'Đánh dấu đã đọc',
            'en' => 'Mark as Read'
        ],
        'notifications.index.bulk_archive' => [
            'vi' => 'Lưu trữ',
            'en' => 'Archive'
        ],
        'notifications.index.bulk_delete' => [
            'vi' => 'Xóa',
            'en' => 'Delete'
        ],
        'notifications.index.select_all' => [
            'vi' => 'Chọn tất cả',
            'en' => 'Select All'
        ],
        'notifications.index.selected_count' => [
            'vi' => ':count đã chọn',
            'en' => ':count selected'
        ],
        'notifications.index.bulk_actions' => [
            'vi' => 'Thao tác hàng loạt',
            'en' => 'Bulk Actions'
        ],
        'notifications.index.confirm_bulk_delete' => [
            'vi' => 'Bạn có chắc chắn muốn xóa :count thông báo đã chọn?',
            'en' => 'Are you sure you want to delete :count selected notifications?'
        ],
        'notifications.index.confirm_bulk_archive' => [
            'vi' => 'Bạn có chắc chắn muốn lưu trữ :count thông báo đã chọn?',
            'en' => 'Are you sure you want to archive :count selected notifications?'
        ],
        'notifications.index.bulk_success' => [
            'vi' => 'Đã thực hiện thành công cho :count thông báo',
            'en' => 'Successfully processed :count notifications'
        ],
        'notifications.index.no_notifications_selected' => [
            'vi' => 'Vui lòng chọn ít nhất một thông báo',
            'en' => 'Please select at least one notification'
        ],

        // Archive functionality
        'notifications.index.archived' => [
            'vi' => 'Đã lưu trữ',
            'en' => 'Archived'
        ],
        'notifications.index.archived_tooltip' => [
            'vi' => 'Xem thông báo đã lưu trữ',
            'en' => 'View archived notifications'
        ],
        'notifications.index.archive_tooltip' => [
            'vi' => 'Lưu trữ thông báo',
            'en' => 'Archive notification'
        ],
        'notifications.archive.restore' => [
            'vi' => 'Khôi phục',
            'en' => 'Restore'
        ],
        'notifications.archive.restore_tooltip' => [
            'vi' => 'Khôi phục thông báo từ lưu trữ',
            'en' => 'Restore notification from archive'
        ],
        'notifications.archive.confirm_restore' => [
            'vi' => 'Bạn có chắc muốn khôi phục thông báo này?',
            'en' => 'Are you sure you want to restore this notification?'
        ],
        'notifications.archive.success_restore' => [
            'vi' => 'Đã khôi phục thông báo thành công.',
            'en' => 'Successfully restored notification.'
        ],
        'notifications.archive.bulk_restore' => [
            'vi' => 'Khôi phục đã chọn',
            'en' => 'Restore Selected'
        ],
        'notifications.archive.confirm_bulk_restore' => [
            'vi' => 'Bạn có chắc muốn khôi phục các thông báo đã chọn?',
            'en' => 'Are you sure you want to restore the selected notifications?'
        ],
        'notifications.archive.success_bulk_restore' => [
            'vi' => 'Đã khôi phục thành công các thông báo đã chọn.',
            'en' => 'Successfully restored selected notifications.'
        ],
        'notifications.archive.empty_message' => [
            'vi' => 'Không có thông báo nào trong lưu trữ.',
            'en' => 'No notifications in archive.'
        ],
        'notifications.archive.auto_archive_info' => [
            'vi' => 'Thông báo cũ hơn 30 ngày sẽ được tự động lưu trữ.',
            'en' => 'Notifications older than 30 days will be automatically archived.'
        ],

        // Archive Page
        'notifications.archive.title' => [
            'vi' => 'Thông báo đã lưu trữ',
            'en' => 'Archived Notifications'
        ],
        'notifications.archive.heading' => [
            'vi' => 'Thông báo đã lưu trữ',
            'en' => 'Archived Notifications'
        ],
        'notifications.archive.description' => [
            'vi' => 'Quản lý và khôi phục thông báo đã lưu trữ',
            'en' => 'Manage and restore archived notifications'
        ],
        'notifications.archive.back_to_notifications' => [
            'vi' => 'Quay lại thông báo',
            'en' => 'Back to Notifications'
        ],
        'notifications.archive.restore_all' => [
            'vi' => 'Khôi phục tất cả',
            'en' => 'Restore All'
        ],
        'notifications.archive.delete_all' => [
            'vi' => 'Xóa tất cả',
            'en' => 'Delete All'
        ],
        'notifications.archive.total_archived' => [
            'vi' => 'Tổng đã lưu trữ',
            'en' => 'Total Archived'
        ],
        'notifications.archive.this_month' => [
            'vi' => 'Tháng này',
            'en' => 'This Month'
        ],
        'notifications.archive.older_than_30_days' => [
            'vi' => 'Cũ hơn 30 ngày',
            'en' => 'Older than 30 days'
        ],
        'notifications.archive.storage_saved' => [
            'vi' => 'Dung lượng tiết kiệm',
            'en' => 'Storage Saved'
        ],
        'notifications.archive.filter_category' => [
            'vi' => 'Lọc theo danh mục',
            'en' => 'Filter by Category'
        ],
        'notifications.archive.all_categories' => [
            'vi' => 'Tất cả danh mục',
            'en' => 'All Categories'
        ],
        'notifications.archive.filter_date_archived' => [
            'vi' => 'Lọc theo ngày lưu trữ',
            'en' => 'Filter by Date Archived'
        ],
        'notifications.archive.all_time' => [
            'vi' => 'Tất cả thời gian',
            'en' => 'All Time'
        ],
        'notifications.archive.today' => [
            'vi' => 'Hôm nay',
            'en' => 'Today'
        ],
        'notifications.archive.this_week' => [
            'vi' => 'Tuần này',
            'en' => 'This Week'
        ],
        'notifications.archive.last_3_months' => [
            'vi' => '3 tháng qua',
            'en' => 'Last 3 Months'
        ],
        'notifications.archive.last_6_months' => [
            'vi' => '6 tháng qua',
            'en' => 'Last 6 Months'
        ],
        'notifications.archive.this_year' => [
            'vi' => 'Năm nay',
            'en' => 'This Year'
        ],
        'notifications.archive.search' => [
            'vi' => 'Tìm kiếm',
            'en' => 'Search'
        ],
        'notifications.archive.search_placeholder' => [
            'vi' => 'Tìm kiếm trong thông báo đã lưu trữ...',
            'en' => 'Search in archived notifications...'
        ],
        'notifications.archive.apply_filters' => [
            'vi' => 'Áp dụng bộ lọc',
            'en' => 'Apply Filters'
        ],
        'notifications.archive.select_all' => [
            'vi' => 'Chọn tất cả',
            'en' => 'Select All'
        ],
        'notifications.archive.selected' => [
            'vi' => 'đã chọn',
            'en' => 'selected'
        ],
        'notifications.archive.restore_selected' => [
            'vi' => 'Khôi phục đã chọn',
            'en' => 'Restore Selected'
        ],
        'notifications.archive.delete_selected' => [
            'vi' => 'Xóa đã chọn',
            'en' => 'Delete Selected'
        ],
        'notifications.archive.archived' => [
            'vi' => 'Đã lưu trữ',
            'en' => 'Archived'
        ],
        'notifications.archive.restore' => [
            'vi' => 'Khôi phục',
            'en' => 'Restore'
        ],
        'notifications.archive.delete_permanently' => [
            'vi' => 'Xóa vĩnh viễn',
            'en' => 'Delete Permanently'
        ],
        'notifications.archive.no_archived_notifications' => [
            'vi' => 'Không có thông báo đã lưu trữ',
            'en' => 'No Archived Notifications'
        ],
        'notifications.archive.no_archived_description' => [
            'vi' => 'Bạn chưa có thông báo nào được lưu trữ.',
            'en' => 'You don\'t have any archived notifications yet.'
        ],
        'notifications.archive.view_active_notifications' => [
            'vi' => 'Xem thông báo hoạt động',
            'en' => 'View Active Notifications'
        ],
        'notifications.archive.auto_archive_title' => [
            'vi' => 'Tự động lưu trữ',
            'en' => 'Auto Archive'
        ],

        // Settings - Notification Preferences
        'settings.notifications.title' => [
            'vi' => 'Tùy chọn thông báo',
            'en' => 'Notification Preferences'
        ],
        'settings.notifications.description' => [
            'vi' => 'Quản lý cách bạn nhận thông báo từ MechaMap',
            'en' => 'Manage how you receive notifications from MechaMap'
        ],
        'settings.notifications.updated_successfully' => [
            'vi' => 'Cài đặt thông báo đã được cập nhật thành công',
            'en' => 'Notification settings updated successfully'
        ],
        'settings.notifications.global_settings' => [
            'vi' => 'Cài đặt chung',
            'en' => 'Global Settings'
        ],
        'settings.notifications.category_preferences' => [
            'vi' => 'Tùy chọn theo danh mục',
            'en' => 'Category Preferences'
        ],
        'settings.notifications.delivery_settings' => [
            'vi' => 'Cài đặt gửi thông báo',
            'en' => 'Delivery Settings'
        ],
        'settings.notifications.notification_type' => [
            'vi' => 'Loại thông báo',
            'en' => 'Notification Type'
        ],
        'settings.notifications.enable_category' => [
            'vi' => 'Bật thông báo :category',
            'en' => 'Enable :category notifications'
        ],
        'settings.notifications.frequency' => [
            'vi' => 'Tần suất',
            'en' => 'Frequency'
        ],
        'settings.notifications.quiet_hours' => [
            'vi' => 'Giờ im lặng',
            'en' => 'Quiet Hours'
        ],
        'settings.notifications.start_time' => [
            'vi' => 'Bắt đầu',
            'en' => 'Start Time'
        ],
        'settings.notifications.end_time' => [
            'vi' => 'Kết thúc',
            'en' => 'End Time'
        ],
        'settings.notifications.quick_actions' => [
            'vi' => 'Thao tác nhanh',
            'en' => 'Quick Actions'
        ],
        'settings.notifications.enable_all' => [
            'vi' => 'Bật tất cả',
            'en' => 'Enable All'
        ],
        'settings.notifications.disable_all' => [
            'vi' => 'Tắt tất cả',
            'en' => 'Disable All'
        ],
        'settings.notifications.reset_defaults' => [
            'vi' => 'Đặt lại mặc định',
            'en' => 'Reset to Defaults'
        ],
        'settings.notifications.save_preferences' => [
            'vi' => 'Lưu tùy chọn',
            'en' => 'Save Preferences'
        ],

        // Settings - General
        'settings.index.title' => [
            'vi' => 'Cài đặt',
            'en' => 'Settings'
        ],
        'settings.index.general' => [
            'vi' => 'Cài đặt chung',
            'en' => 'General Settings'
        ],
        'settings.index.language' => [
            'vi' => 'Ngôn ngữ',
            'en' => 'Language'
        ],
        'settings.index.timezone' => [
            'vi' => 'Múi giờ',
            'en' => 'Timezone'
        ],
        'settings.index.theme' => [
            'vi' => 'Giao diện',
            'en' => 'Theme'
        ],
        'settings.index.light' => [
            'vi' => 'Sáng',
            'en' => 'Light'
        ],
        'settings.index.dark' => [
            'vi' => 'Tối',
            'en' => 'Dark'
        ],
        'settings.index.auto' => [
            'vi' => 'Tự động',
            'en' => 'Auto'
        ],
        'settings.index.notifications' => [
            'vi' => 'Thông báo',
            'en' => 'Notifications'
        ],
        'settings.index.privacy' => [
            'vi' => 'Quyền riêng tư',
            'en' => 'Privacy'
        ],
        'settings.index.save' => [
            'vi' => 'Lưu',
            'en' => 'Save'
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $added = 0;
        $skipped = 0;
        $errors = [];

        $this->info('🚀 Starting dashboard translations import...');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        DB::beginTransaction();

        try {
            foreach ($this->translations as $key => $locales) {
                foreach ($locales as $locale => $content) {
                    // Check if translation already exists
                    $existing = Translation::where('key', $key)
                        ->where('locale', $locale)
                        ->first();

                    if ($existing) {
                        $this->line("⏭️  Skipped: {$key} ({$locale}) - already exists");
                        $skipped++;
                        continue;
                    }

                    if (!$dryRun) {
                        // Create new translation
                        Translation::create([
                            'key' => $key,
                            'content' => $content,
                            'locale' => $locale,
                            'group_name' => explode('.', $key)[0],
                            'is_active' => true,
                            'created_by' => 1, // System user
                        ]);
                    }

                    $this->line("✅ Added: {$key} ({$locale}) = {$content}");
                    $added++;
                }
            }

            if (!$dryRun) {
                DB::commit();
                $this->info('💾 Changes committed to database');
            } else {
                DB::rollBack();
                $this->info('🔄 Transaction rolled back (dry run)');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        // Summary
        $this->newLine();
        $this->info('📊 SUMMARY:');
        $this->info("✅ Added: {$added} translations");
        $this->info("⏭️  Skipped: {$skipped} translations (already exist)");

        if (!empty($errors)) {
            $this->error("❌ Errors: " . count($errors));
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }

        if (!$dryRun) {
            $this->info('🎉 Dashboard translations imported successfully!');
            $this->info('💡 Clear cache with: php artisan cache:clear');
        }

        return 0;
    }
}
