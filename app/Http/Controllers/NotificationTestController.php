<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationTestController extends Controller
{
    /**
     * Create test notifications for a user
     * POST /api/test/notifications/create
     */
    public function createTestNotifications(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'count' => 'integer|min:1|max:20',
            'types' => 'array',
            'types.*' => 'in:comment,reply,mention,follow,like,system,thread_created,showcase_featured'
        ]);

        $userId = $request->user_id;
        $count = $request->get('count', 5);
        $types = $request->get('types', ['comment', 'reply', 'mention', 'follow', 'like', 'system']);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $notifications = [];
        $createdNotifications = [];

        // Define notification templates
        $templates = [
            'comment' => [
                'title' => 'notifications.types.comment',
                'message' => '{user} đã bình luận về bài viết của bạn: "{content}"',
                'icon' => 'fas fa-comment',
                'color' => 'primary',
                'action_url' => '/threads/{thread_id}#comment-{comment_id}'
            ],
            'reply' => [
                'title' => 'notifications.types.reply',
                'message' => '{user} đã trả lời bình luận của bạn: "{content}"',
                'icon' => 'fas fa-reply',
                'color' => 'info',
                'action_url' => '/threads/{thread_id}#comment-{comment_id}'
            ],
            'mention' => [
                'title' => 'notifications.types.mention',
                'message' => '{user} đã nhắc đến bạn trong một bình luận',
                'icon' => 'fas fa-at',
                'color' => 'warning',
                'action_url' => '/threads/{thread_id}#comment-{comment_id}'
            ],
            'follow' => [
                'title' => 'notifications.types.follow',
                'message' => '{user} đã bắt đầu theo dõi bạn',
                'icon' => 'fas fa-user-plus',
                'color' => 'success',
                'action_url' => '/users/{user_id}'
            ],
            'like' => [
                'title' => 'notifications.types.like',
                'message' => '{user} đã thích bài viết của bạn: "{content}"',
                'icon' => 'fas fa-heart',
                'color' => 'danger',
                'action_url' => '/threads/{thread_id}'
            ],
            'system' => [
                'title' => 'notifications.types.system',
                'message' => 'Hệ thống đã cập nhật chính sách bảo mật mới. Vui lòng xem chi tiết.',
                'icon' => 'fas fa-cog',
                'color' => 'secondary',
                'action_url' => '/settings/privacy'
            ],
            'thread_created' => [
                'title' => 'Thảo luận mới',
                'message' => '{user} đã tạo thảo luận mới: "{content}"',
                'icon' => 'fas fa-plus-circle',
                'color' => 'success',
                'action_url' => '/threads/{thread_id}'
            ],
            'showcase_featured' => [
                'title' => 'Showcase nổi bật',
                'message' => 'Showcase "{content}" của bạn đã được chọn làm nổi bật!',
                'icon' => 'fas fa-star',
                'color' => 'warning',
                'action_url' => '/showcase/{showcase_id}'
            ]
        ];

        // Get some sample users and threads for realistic data
        $sampleUsers = User::where('id', '!=', $userId)->limit(10)->get();
        $sampleThreads = Thread::limit(10)->get();

        for ($i = 0; $i < $count; $i++) {
            $type = $types[array_rand($types)];
            $template = $templates[$type];

            // Random user for notification
            $fromUser = $sampleUsers->random();
            $thread = $sampleThreads->random();

            // Create realistic content
            $sampleContents = [
                'Thiết kế cầu trục 5 tấn cho nhà máy',
                'Phân tích FEA cho chi tiết máy',
                'Tối ưu hóa quy trình gia công CNC',
                'Nghiên cứu vật liệu composite',
                'Thiết kế hệ thống truyền động'
            ];

            $content = $sampleContents[array_rand($sampleContents)];

            // Replace placeholders
            $message = str_replace(['{user}', '{content}', '{thread_id}', '{comment_id}', '{user_id}', '{showcase_id}'],
                                 [$fromUser->display_name, $content, $thread->id, rand(1, 100), $fromUser->id, rand(1, 50)],
                                 $template['message']);

            $actionUrl = str_replace(['{thread_id}', '{comment_id}', '{user_id}', '{showcase_id}'],
                                   [$thread->id, rand(1, 100), $fromUser->id, rand(1, 50)],
                                   $template['action_url']);

            // Random read status (70% unread for testing)
            $isRead = rand(1, 10) <= 3;
            $readAt = $isRead ? now()->subMinutes(rand(1, 1440)) : null;

            $notificationData = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $template['title'],
                'message' => $message,
                'data' => json_encode([
                    'icon' => $template['icon'],
                    'color' => $template['color'],
                    'action_url' => $actionUrl,
                    'from_user_id' => $fromUser->id,
                    'from_user_name' => $fromUser->display_name,
                    'thread_id' => $thread->id ?? null,
                    'content_preview' => $content
                ]),
                'priority' => 'normal',
                'is_read' => $isRead ? 1 : 0,
                'read_at' => $readAt,
                'created_at' => now()->subMinutes(rand(1, 10080)), // Random time within last week
                'updated_at' => now()
            ];

            DB::table('custom_notifications')->insert($notificationData);
            $createdNotifications[] = $notificationData;
        }

        return response()->json([
            'success' => true,
            'message' => "Created {$count} test notifications for user {$user->display_name}",
            'user' => [
                'id' => $user->id,
                'name' => $user->display_name,
                'email' => $user->email
            ],
            'notifications_created' => count($createdNotifications),
            'types_used' => $types,
            'sample_notifications' => array_slice($createdNotifications, 0, 3) // Show first 3 as sample
        ]);
    }

    /**
     * Clear all notifications for a user
     * DELETE /api/test/notifications/clear
     */
    public function clearTestNotifications(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $userId = $request->user_id;
        $user = User::find($userId);

        $deletedCount = DB::table('custom_notifications')
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deletedCount} notifications for user {$user->display_name}",
            'user' => [
                'id' => $user->id,
                'name' => $user->display_name
            ],
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get notification statistics for a user
     * GET /api/test/notifications/stats
     */
    public function getNotificationStats(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $userId = $request->user_id;
        $user = User::find($userId);

        $stats = [
            'total' => DB::table('custom_notifications')->where('user_id', $userId)->count(),
            'unread' => DB::table('custom_notifications')->where('user_id', $userId)->where('is_read', 0)->count(),
            'read' => DB::table('custom_notifications')->where('user_id', $userId)->where('is_read', 1)->count(),
        ];

        // Get type breakdown
        $typeStats = DB::table('custom_notifications')
            ->where('user_id', $userId)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->display_name
            ],
            'stats' => $stats,
            'type_breakdown' => $typeStats,
            'recent_notifications' => DB::table('custom_notifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'is_read' => $notification->is_read == 1,
                        'created_at' => $notification->created_at
                    ];
                })
        ]);
    }

    /**
     * Simulate real-time notification
     * POST /api/test/notifications/realtime
     */
    public function simulateRealtimeNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:comment,reply,mention,follow,like,system',
            'message' => 'string|max:255'
        ]);

        $userId = $request->user_id;
        $type = $request->type;
        $customMessage = $request->get('message');

        $user = User::find($userId);
        $fromUser = User::where('id', '!=', $userId)->inRandomOrder()->first();

        $templates = [
            'comment' => ['icon' => 'fas fa-comment', 'color' => 'primary'],
            'reply' => ['icon' => 'fas fa-reply', 'color' => 'info'],
            'mention' => ['icon' => 'fas fa-at', 'color' => 'warning'],
            'follow' => ['icon' => 'fas fa-user-plus', 'color' => 'success'],
            'like' => ['icon' => 'fas fa-heart', 'color' => 'danger'],
            'system' => ['icon' => 'fas fa-cog', 'color' => 'secondary']
        ];

        $template = $templates[$type];
        $message = $customMessage ?: "{$fromUser->display_name} đã {$type} bạn";

        $notificationData = [
            'user_id' => $userId,
            'type' => $type,
            'title' => "notifications.types.{$type}",
            'message' => $message,
            'data' => json_encode([
                'icon' => $template['icon'],
                'color' => $template['color'],
                'action_url' => '/notifications',
                'from_user_id' => $fromUser->id,
                'from_user_name' => $fromUser->display_name,
                'realtime' => true
            ]),
            'priority' => 'normal',
            'is_read' => 0,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('custom_notifications')->insert($notificationData);

        // Here you would trigger WebSocket event for real-time notification
        // event(new NotificationCreated($user, $notificationData));

        return response()->json([
            'success' => true,
            'message' => 'Real-time notification created and sent',
            'notification' => $notificationData,
            'websocket_event' => 'notification.created',
            'target_user' => [
                'id' => $user->id,
                'name' => $user->display_name
            ]
        ]);
    }
}
