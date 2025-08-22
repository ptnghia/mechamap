<?php

namespace App\Services;

use App\Models\User;
use App\Models\Showcase;
use App\Models\ShowcaseRating;
use App\Models\ShowcaseRatingReply;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WebSocket Notification Service
 *
 * Tích hợp với Node.js WebSocket server để gửi thông báo real-time
 * cho các tương tác showcase (ratings, replies, likes)
 */
class WebSocketNotificationService
{
    private string $websocketServerUrl;
    private string $serverSecret;

    public function __construct()
    {
        $this->websocketServerUrl = config('websocket.server_url', 'http://localhost:3000');
        $this->serverSecret = config('websocket.server_secret', env('WEBSOCKET_SERVER_SECRET'));
    }

    /**
     * Gửi notification khi có rating mới
     */
    public function sendRatingNotification(Showcase $showcase, ShowcaseRating $rating, string $action = 'created'): void
    {
        try {
            $notificationData = [
                'type' => 'showcase_rating_' . $action,
                'showcase_id' => $showcase->id,
                'showcase_title' => $showcase->title,
                'rating_id' => $rating->id,
                'action' => $action,
                'user' => [
                    'id' => $rating->user->id,
                    'name' => $rating->user->display_name,
                    'avatar_url' => $rating->user->getAvatarUrl(),
                ],
                'rating' => [
                    'overall_rating' => $rating->overall_rating,
                    'review' => $rating->review ? substr($rating->review, 0, 100) . '...' : null,
                ],
                'timestamp' => now()->toISOString(),
            ];

            // Gửi đến tác giả showcase (nếu không phải chính họ đánh giá)
            if ($showcase->user_id !== $rating->user_id) {
                $this->sendToUser($showcase->user_id, $notificationData);
            }

            // Gửi đến followers của showcase
            $this->sendToShowcaseFollowers($showcase, $notificationData, $rating->user_id);

            // Broadcast đến channel showcase cho real-time updates
            $this->broadcastToChannel("showcase.{$showcase->id}", $notificationData);

        } catch (\Exception $e) {
            Log::error('Error sending rating notification: ' . $e->getMessage(), [
                'showcase_id' => $showcase->id,
                'rating_id' => $rating->id,
                'action' => $action,
            ]);
        }
    }

    /**
     * Gửi notification khi có reply mới
     */
    public function sendReplyNotification(ShowcaseRating $rating, ShowcaseRatingReply $reply, string $action = 'created'): void
    {
        try {
            $notificationData = [
                'type' => 'rating_reply_' . $action,
                'showcase_id' => $rating->showcase_id,
                'rating_id' => $rating->id,
                'reply_id' => $reply->id,
                'action' => $action,
                'user' => [
                    'id' => $reply->user->id,
                    'name' => $reply->user->display_name,
                    'avatar_url' => $reply->user->getAvatarUrl(),
                ],
                'reply' => [
                    'content' => substr($reply->content, 0, 100) . '...',
                    'parent_id' => $reply->parent_id,
                ],
                'timestamp' => now()->toISOString(),
            ];

            // Gửi đến tác giả đánh giá (nếu không phải chính họ reply)
            if ($rating->user_id !== $reply->user_id) {
                $this->sendToUser($rating->user_id, $notificationData);
            }

            // Gửi đến tác giả showcase (nếu khác với tác giả đánh giá và người reply)
            if ($rating->showcase->user_id !== $reply->user_id &&
                $rating->showcase->user_id !== $rating->user_id) {
                $this->sendToUser($rating->showcase->user_id, $notificationData);
            }

            // Broadcast đến channel showcase
            $this->broadcastToChannel("showcase.{$rating->showcase_id}", $notificationData);

        } catch (\Exception $e) {
            Log::error('Error sending reply notification: ' . $e->getMessage(), [
                'rating_id' => $rating->id,
                'reply_id' => $reply->id,
                'action' => $action,
            ]);
        }
    }

    /**
     * Gửi notification khi có like
     */
    public function sendLikeNotification(string $type, int $targetId, User $liker, array $context = []): void
    {
        try {
            $notificationData = [
                'type' => $type, // 'rating_liked' hoặc 'reply_liked'
                'target_id' => $targetId,
                'liker' => [
                    'id' => $liker->id,
                    'name' => $liker->display_name,
                    'avatar_url' => $liker->getAvatarUrl(),
                ],
                'context' => $context,
                'timestamp' => now()->toISOString(),
            ];

            // Gửi đến tác giả của item được like
            if (isset($context['author_id']) && $context['author_id'] !== $liker->id) {
                $this->sendToUser($context['author_id'], $notificationData);
            }

            // Broadcast đến channel showcase nếu có
            if (isset($context['showcase_id'])) {
                $this->broadcastToChannel("showcase.{$context['showcase_id']}", $notificationData);
            }

        } catch (\Exception $e) {
            Log::error('Error sending like notification: ' . $e->getMessage(), [
                'type' => $type,
                'target_id' => $targetId,
                'liker_id' => $liker->id,
            ]);
        }
    }

    /**
     * Gửi notification đến một user cụ thể
     */
    public function sendToUser(int $userId, array $data): void
    {
        $this->sendWebSocketMessage([
            'action' => 'send_to_user',
            'user_id' => $userId,
            'data' => $data,
        ]);
    }

    /**
     * Broadcast đến một channel
     */
    public function broadcastToChannel(string $channel, array $data): void
    {
        $this->sendWebSocketMessage([
            'action' => 'broadcast_to_channel',
            'channel' => $channel,
            'data' => $data,
        ]);
    }

    /**
     * Gửi đến followers của showcase
     */
    private function sendToShowcaseFollowers(Showcase $showcase, array $data, ?int $excludeUserId = null): void
    {
        try {
            // Lấy danh sách followers (giả sử có relationship)
            // $followers = $showcase->followers()->pluck('user_id');

            // Tạm thời skip vì chưa có follower system hoàn chỉnh
            // foreach ($followers as $followerId) {
            //     if ($followerId !== $excludeUserId) {
            //         $this->sendToUser($followerId, $data);
            //     }
            // }

        } catch (\Exception $e) {
            Log::error('Error sending to showcase followers: ' . $e->getMessage());
        }
    }

    /**
     * Gửi message đến WebSocket server
     */
    private function sendWebSocketMessage(array $message): void
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serverSecret,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->websocketServerUrl . '/api/notify', $message);

            if (!$response->successful()) {
                Log::warning('WebSocket server returned error: ' . $response->status(), [
                    'response' => $response->body(),
                    'message' => $message,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send WebSocket message: ' . $e->getMessage(), [
                'message' => $message,
            ]);
        }
    }

    /**
     * Test connection đến WebSocket server
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->serverSecret,
                ])
                ->get($this->websocketServerUrl . '/api/health');

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('WebSocket server connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi notification tổng hợp về hoạt động showcase
     */
    public function sendShowcaseActivitySummary(Showcase $showcase): void
    {
        try {
            $stats = [
                'total_ratings' => $showcase->ratings()->count(),
                'average_rating' => $showcase->ratings()->avg('overall_rating'),
                'total_replies' => $showcase->ratings()->withCount('replies')->get()->sum('replies_count'),
                'recent_activity_count' => $showcase->ratings()
                    ->where('created_at', '>=', now()->subHours(24))
                    ->count(),
            ];

            $notificationData = [
                'type' => 'showcase_activity_summary',
                'showcase_id' => $showcase->id,
                'showcase_title' => $showcase->title,
                'stats' => $stats,
                'timestamp' => now()->toISOString(),
            ];

            // Gửi đến tác giả showcase
            $this->sendToUser($showcase->user_id, $notificationData);

        } catch (\Exception $e) {
            Log::error('Error sending showcase activity summary: ' . $e->getMessage(), [
                'showcase_id' => $showcase->id,
            ]);
        }
    }

    /**
     * Gửi notification khi showcase được featured
     */
    public function sendShowcaseFeaturedNotification(Showcase $showcase): void
    {
        try {
            $notificationData = [
                'type' => 'showcase_featured',
                'showcase_id' => $showcase->id,
                'showcase_title' => $showcase->title,
                'message' => 'Showcase của bạn đã được đưa vào danh sách nổi bật!',
                'timestamp' => now()->toISOString(),
            ];

            $this->sendToUser($showcase->user_id, $notificationData);

        } catch (\Exception $e) {
            Log::error('Error sending showcase featured notification: ' . $e->getMessage(), [
                'showcase_id' => $showcase->id,
            ]);
        }
    }
}
