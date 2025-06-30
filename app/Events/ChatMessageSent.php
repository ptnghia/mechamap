<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

/**
 * Chat Message Sent Event
 * Broadcasts chat messages in real-time
 */
class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;
    public $message;
    public $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(int $senderId, int $receiverId, array $message)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->message = $message;
        $this->prepareBroadcastData();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Private channel for the receiver
        $channels[] = new PrivateChannel('user.' . $this->receiverId);

        // Private channel for the sender (for multi-device sync)
        $channels[] = new PrivateChannel('user.' . $this->senderId);

        // Conversation channel for both participants
        $conversationId = $this->getConversationId();
        $channels[] = new PrivateChannel('conversation.' . $conversationId);

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->broadcastData;
    }

    /**
     * Determine if the event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        // Don't broadcast if receiver has blocked sender
        if ($this->isUserBlocked()) {
            return false;
        }

        // Don't broadcast if message is flagged as spam
        if ($this->isSpamMessage()) {
            return false;
        }

        return true;
    }

    /**
     * Prepare broadcast data
     */
    private function prepareBroadcastData(): void
    {
        $sender = User::find($this->senderId);
        $receiver = User::find($this->receiverId);

        $this->broadcastData = [
            'id' => $this->message['id'] ?? uniqid('msg_'),
            'conversation_id' => $this->getConversationId(),
            'sender' => [
                'id' => $this->senderId,
                'name' => $sender?->name ?? 'Unknown User',
                'username' => $sender?->username ?? 'unknown',
                'avatar' => $sender?->avatar ?? null,
                'role' => $sender?->role ?? 'member',
                'is_online' => $this->isUserOnline($this->senderId),
            ],
            'receiver' => [
                'id' => $this->receiverId,
                'name' => $receiver?->name ?? 'Unknown User',
                'username' => $receiver?->username ?? 'unknown',
                'avatar' => $receiver?->avatar ?? null,
                'role' => $receiver?->role ?? 'member',
                'is_online' => $this->isUserOnline($this->receiverId),
            ],
            'message' => [
                'content' => $this->sanitizeMessage($this->message['content'] ?? ''),
                'type' => $this->message['type'] ?? 'text',
                'attachments' => $this->processAttachments($this->message['attachments'] ?? []),
                'metadata' => $this->message['metadata'] ?? [],
            ],
            'timestamp' => $this->message['timestamp'] ?? now()->toISOString(),
            'read_at' => null,
            'delivered_at' => now()->toISOString(),
            'status' => 'sent',
            'reply_to' => $this->message['reply_to'] ?? null,
            'edited' => false,
            'deleted' => false,
        ];

        // Add encryption info if message is encrypted
        if ($this->isEncryptedMessage()) {
            $this->broadcastData['encrypted'] = true;
            $this->broadcastData['encryption_key_id'] = $this->message['encryption_key_id'] ?? null;
        }

        // Add priority for urgent messages
        if ($this->isUrgentMessage()) {
            $this->broadcastData['priority'] = 'urgent';
            $this->broadcastData['urgent_reason'] = $this->message['urgent_reason'] ?? null;
        }
    }

    /**
     * Get conversation ID for the two users
     */
    private function getConversationId(): string
    {
        $ids = [$this->senderId, $this->receiverId];
        sort($ids);
        return implode('_', $ids);
    }

    /**
     * Sanitize message content
     */
    private function sanitizeMessage(string $content): string
    {
        // Remove potentially harmful content
        $content = strip_tags($content, '<b><i><u><em><strong><a><br>');
        
        // Limit message length
        if (strlen($content) > 5000) {
            $content = substr($content, 0, 5000) . '...';
        }

        // Replace URLs with safe links
        $content = preg_replace_callback(
            '/(https?:\/\/[^\s]+)/',
            function ($matches) {
                return '<a href="' . htmlspecialchars($matches[1]) . '" target="_blank" rel="noopener">' . 
                       htmlspecialchars($matches[1]) . '</a>';
            },
            $content
        );

        return $content;
    }

    /**
     * Process message attachments
     */
    private function processAttachments(array $attachments): array
    {
        $processed = [];

        foreach ($attachments as $attachment) {
            if ($this->isValidAttachment($attachment)) {
                $processed[] = [
                    'id' => $attachment['id'] ?? uniqid('att_'),
                    'name' => $attachment['name'] ?? 'Unknown File',
                    'type' => $attachment['type'] ?? 'file',
                    'size' => $attachment['size'] ?? 0,
                    'url' => $attachment['url'] ?? null,
                    'thumbnail' => $attachment['thumbnail'] ?? null,
                    'mime_type' => $attachment['mime_type'] ?? 'application/octet-stream',
                    'is_safe' => $this->isAttachmentSafe($attachment),
                ];
            }
        }

        return $processed;
    }

    /**
     * Check if attachment is valid
     */
    private function isValidAttachment(array $attachment): bool
    {
        // Check file size (max 50MB)
        if (($attachment['size'] ?? 0) > 50 * 1024 * 1024) {
            return false;
        }

        // Check allowed file types
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return in_array($attachment['mime_type'] ?? '', $allowedTypes);
    }

    /**
     * Check if attachment is safe
     */
    private function isAttachmentSafe(array $attachment): bool
    {
        // Implement virus scanning or other security checks
        // For now, return true for allowed types
        return $this->isValidAttachment($attachment);
    }

    /**
     * Check if user is online
     */
    private function isUserOnline(int $userId): bool
    {
        return cache()->get("user_online_{$userId}", false);
    }

    /**
     * Check if receiver has blocked sender
     */
    private function isUserBlocked(): bool
    {
        // Check if receiver has blocked sender
        return cache()->get("user_blocked_{$this->receiverId}_{$this->senderId}", false);
    }

    /**
     * Check if message is spam
     */
    private function isSpamMessage(): bool
    {
        $content = $this->message['content'] ?? '';
        
        // Simple spam detection
        $spamKeywords = ['spam', 'scam', 'free money', 'click here', 'urgent'];
        
        foreach ($spamKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                return true;
            }
        }

        // Check message frequency (rate limiting)
        $recentMessages = cache()->get("user_message_count_{$this->senderId}", 0);
        if ($recentMessages > 10) { // More than 10 messages per minute
            return true;
        }

        return false;
    }

    /**
     * Check if message is encrypted
     */
    private function isEncryptedMessage(): bool
    {
        return isset($this->message['encrypted']) && $this->message['encrypted'] === true;
    }

    /**
     * Check if message is urgent
     */
    private function isUrgentMessage(): bool
    {
        return isset($this->message['priority']) && $this->message['priority'] === 'urgent';
    }

    /**
     * Get message delivery options
     */
    public function getDeliveryOptions(): array
    {
        return [
            'real_time' => true,
            'push_notification' => $this->shouldSendPushNotification(),
            'email_notification' => $this->shouldSendEmailNotification(),
            'sms_notification' => $this->shouldSendSMSNotification(),
            'store_offline' => true, // Always store for offline users
        ];
    }

    /**
     * Determine if push notification should be sent
     */
    private function shouldSendPushNotification(): bool
    {
        $receiver = User::find($this->receiverId);
        
        if (!$receiver) {
            return false;
        }

        // Send push if user is offline or has push notifications enabled
        return !$this->isUserOnline($this->receiverId) || 
               ($receiver->notification_preferences['chat_push'] ?? true);
    }

    /**
     * Determine if email notification should be sent
     */
    private function shouldSendEmailNotification(): bool
    {
        $receiver = User::find($this->receiverId);
        
        if (!$receiver) {
            return false;
        }

        // Send email for urgent messages or if user has email notifications enabled
        return $this->isUrgentMessage() || 
               ($receiver->notification_preferences['chat_email'] ?? false);
    }

    /**
     * Determine if SMS notification should be sent
     */
    private function shouldSendSMSNotification(): bool
    {
        $receiver = User::find($this->receiverId);
        
        if (!$receiver || !$receiver->phone) {
            return false;
        }

        // Only send SMS for urgent messages
        return $this->isUrgentMessage() && 
               ($receiver->notification_preferences['chat_sms'] ?? false);
    }

    /**
     * Get message metadata
     */
    public function getMetadata(): array
    {
        return [
            'event_type' => 'chat_message_sent',
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
            'conversation_id' => $this->getConversationId(),
            'message_type' => $this->message['type'] ?? 'text',
            'has_attachments' => !empty($this->message['attachments']),
            'is_encrypted' => $this->isEncryptedMessage(),
            'is_urgent' => $this->isUrgentMessage(),
            'delivery_options' => $this->getDeliveryOptions(),
            'broadcast_channels' => count($this->broadcastOn()),
            'created_at' => now()->toISOString(),
        ];
    }
}
