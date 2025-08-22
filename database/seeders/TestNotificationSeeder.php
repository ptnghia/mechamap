<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class TestNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy user ID 16 (Member 04) hoặc user đầu tiên
        $user = User::find(16) ?: User::first();

        if (!$user) {
            echo "❌ Không tìm thấy user nào trong database. Vui lòng tạo user trước.\n";
            return;
        }

        echo "🔔 Tạo thông báo test cho user: {$user->name} (ID: {$user->id})\n";

        // Xóa thông báo cũ của user này
        Notification::where('user_id', $user->id)->delete();

        $notifications = [
            [
                'type' => 'system_announcement',
                'title' => 'Chào mừng đến với MechaMap!',
                'message' => 'Cảm ơn bạn đã tham gia cộng đồng kỹ sư cơ khí Việt Nam.',
                'priority' => 'high',
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'type' => 'thread_replied',
                'title' => 'Có phản hồi mới trong thread của bạn',
                'message' => 'Nguyễn Văn A đã trả lời thread "Thiết kế máy CNC" của bạn.',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(15),
            ],
            [
                'type' => 'comment_mention',
                'title' => 'Bạn được nhắc đến trong bình luận',
                'message' => 'Trần Thị B đã nhắc đến bạn trong bình luận về "Vật liệu thép không gỉ".',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(30),
            ],
            [
                'type' => 'product_approved',
                'title' => 'Sản phẩm được duyệt',
                'message' => 'Sản phẩm "Máy tiện CNC" của bạn đã được duyệt và hiển thị trên marketplace.',
                'priority' => 'high',
                'is_read' => true,
                'created_at' => Carbon::now()->subHours(1),
            ],
            [
                'type' => 'order_update',
                'title' => 'Cập nhật đơn hàng #12345',
                'message' => 'Đơn hàng của bạn đang được vận chuyển và sẽ đến trong 2-3 ngày.',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'type' => 'user_followed',
                'title' => 'Có người theo dõi mới',
                'message' => 'Lê Văn C đã bắt đầu theo dõi bạn.',
                'priority' => 'low',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'type' => 'business_verified',
                'title' => 'Doanh nghiệp được xác minh',
                'message' => 'Doanh nghiệp của bạn đã được xác minh thành công.',
                'priority' => 'high',
                'is_read' => true,
                'created_at' => Carbon::now()->subHours(4),
            ],
            [
                'type' => 'forum_activity',
                'title' => 'Hoạt động diễn đàn mới',
                'message' => 'Có 5 thread mới trong chuyên mục "Thiết kế cơ khí".',
                'priority' => 'low',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'type' => 'achievement_unlocked',
                'title' => 'Mở khóa thành tựu mới!',
                'message' => 'Bạn đã đạt thành tựu "Chuyên gia tư vấn" với 50 câu trả lời hữu ích.',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'type' => 'price_drop_alert',
                'title' => 'Giảm giá sản phẩm yêu thích',
                'message' => 'Sản phẩm "Máy phay CNC" trong wishlist của bạn đã giảm giá 20%.',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(8),
            ],
            [
                'type' => 'login_from_new_device',
                'title' => 'Đăng nhập từ thiết bị mới',
                'message' => 'Tài khoản của bạn đã được đăng nhập từ thiết bị mới vào lúc ' . Carbon::now()->subHours(12)->format('H:i d/m/Y'),
                'priority' => 'urgent',
                'is_read' => true,
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'type' => 'weekly_digest',
                'title' => 'Tổng hợp tuần này',
                'message' => 'Xem những hoạt động nổi bật trong tuần qua trên MechaMap.',
                'priority' => 'low',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'type' => 'review_received',
                'title' => 'Nhận đánh giá mới',
                'message' => 'Bạn đã nhận được đánh giá 5 sao cho sản phẩm "Máy tiện mini".',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'type' => 'commission_paid',
                'title' => 'Hoa hồng đã được thanh toán',
                'message' => 'Hoa hồng 500,000 VNĐ từ đơn hàng #12340 đã được chuyển vào tài khoản.',
                'priority' => 'high',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'type' => 'quote_request',
                'title' => 'Yêu cầu báo giá mới',
                'message' => 'Công ty ABC đã gửi yêu cầu báo giá cho sản phẩm "Máy cắt laser".',
                'priority' => 'high',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'type' => 'marketplace_activity',
                'title' => 'Hoạt động marketplace',
                'message' => 'Có 10 sản phẩm mới được thêm vào danh mục "Máy công cụ".',
                'priority' => 'low',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'type' => 'thread_created',
                'title' => 'Thread mới trong chuyên mục theo dõi',
                'message' => 'Thread "Tối ưu hóa quy trình sản xuất" đã được tạo trong chuyên mục bạn theo dõi.',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'type' => 'seller_message',
                'title' => 'Tin nhắn từ người bán',
                'message' => 'Người bán đã gửi tin nhắn về đơn hàng #12338 của bạn.',
                'priority' => 'normal',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'type' => 'product_out_of_stock',
                'title' => 'Sản phẩm hết hàng',
                'message' => 'Sản phẩm "Dao phay HSS" trong wishlist của bạn đã hết hàng.',
                'priority' => 'low',
                'is_read' => true,
                'created_at' => Carbon::now()->subWeek(),
            ],
            [
                'type' => 'role_changed',
                'title' => 'Vai trò được cập nhật',
                'message' => 'Vai trò của bạn đã được nâng cấp lên "Chuyên gia kỹ thuật".',
                'priority' => 'high',
                'is_read' => false,
                'created_at' => Carbon::now()->subWeeks(2),
            ]
        ];

        foreach ($notifications as $notificationData) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $notificationData['type'],
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'priority' => $notificationData['priority'],
                'is_read' => $notificationData['is_read'],
                'read_at' => $notificationData['is_read'] ? $notificationData['created_at'] : null,
                'created_at' => $notificationData['created_at'],
                'updated_at' => $notificationData['created_at'],
            ]);
        }

        $totalCount = count($notifications);
        $unreadCount = collect($notifications)->where('is_read', false)->count();

        echo "✅ Đã tạo {$totalCount} thông báo test ({$unreadCount} chưa đọc, " . ($totalCount - $unreadCount) . " đã đọc)\n";
    }
}
