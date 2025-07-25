<?php

namespace App\Observers;

use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        try {
            // Get conversation and participants
            $conversation = $message->conversation;
            if (!$conversation) {
                Log::warning('Conversation not found for message', ['message_id' => $message->id]);
                return;
            }

            $sender = $message->user;
            if (!$sender) {
                Log::warning('Sender not found for message', ['message_id' => $message->id]);
                return;
            }

            // Get all participants except the sender
            $recipients = $conversation->participants()
                ->where('user_id', '!=', $message->user_id)
                ->with('user')
                ->get();

            if ($recipients->isEmpty()) {
                Log::info('No recipients found for message notification', [
                    'message_id' => $message->id,
                    'conversation_id' => $conversation->id
                ]);
                return;
            }

            Log::info('New message created', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'recipients_count' => $recipients->count()
            ]);

            // Send notification to each recipient
            foreach ($recipients as $participant) {
                $recipient = $participant->user;
                if (!$recipient) {
                    continue;
                }

                $this->sendMessageNotification($message, $sender, $recipient, $conversation);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle message creation notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send message notification to recipient
     */
    private function sendMessageNotification(Message $message, $sender, $recipient, $conversation): void
    {
        try {
            // Check if this is a marketplace-related conversation
            $isMarketplaceMessage = $this->isMarketplaceConversation($conversation, $sender, $recipient);
            
            // Determine notification type and content
            if ($isMarketplaceMessage) {
                $this->sendMarketplaceMessageNotification($message, $sender, $recipient, $conversation);
            } else {
                $this->sendGeneralMessageNotification($message, $sender, $recipient, $conversation);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send message notification', [
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send marketplace-specific message notification
     */
    private function sendMarketplaceMessageNotification(Message $message, $sender, $recipient, $conversation): void
    {
        // Determine if sender is a seller
        $isSenderSeller = $this->isUserSeller($sender);
        $isRecipientSeller = $this->isUserSeller($recipient);

        if ($isSenderSeller && !$isRecipientSeller) {
            // Seller messaging buyer
            $title = 'Tin nhắn từ người bán';
            $notificationType = 'seller_message';
            $senderRole = 'người bán';
        } elseif (!$isSenderSeller && $isRecipientSeller) {
            // Buyer messaging seller
            $title = 'Tin nhắn từ khách hàng';
            $notificationType = 'buyer_message';
            $senderRole = 'khách hàng';
        } else {
            // Both are sellers or both are buyers
            $title = 'Tin nhắn mới';
            $notificationType = 'marketplace_message';
            $senderRole = 'người dùng';
        }

        $messagePreview = $this->getMessagePreview($message->content);
        $messageText = "Bạn có tin nhắn mới từ {$senderRole} {$sender->name}: \"{$messagePreview}\"";

        $data = [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'sender_role' => $senderRole,
            'message_preview' => $messagePreview,
            'message_content' => $message->content,
            'is_marketplace_message' => true,
            'action_url' => route('conversations.show', $conversation->id),
        ];

        // Send email for marketplace messages
        $result = NotificationService::send(
            $recipient,
            $notificationType,
            $title,
            $messageText,
            $data,
            true // Send email for marketplace messages
        );

        if ($result) {
            Log::info('Marketplace message notification sent', [
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'notification_type' => $notificationType
            ]);
        }
    }

    /**
     * Send general message notification
     */
    private function sendGeneralMessageNotification(Message $message, $sender, $recipient, $conversation): void
    {
        $title = 'Tin nhắn mới';
        $messagePreview = $this->getMessagePreview($message->content);
        $messageText = "Bạn có tin nhắn mới từ {$sender->name}: \"{$messagePreview}\"";

        $data = [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_preview' => $messagePreview,
            'message_content' => $message->content,
            'is_marketplace_message' => false,
            'action_url' => route('conversations.show', $conversation->id),
        ];

        // Don't send email for general messages, just in-app notification
        $result = NotificationService::send(
            $recipient,
            'message_received',
            $title,
            $messageText,
            $data,
            false
        );

        if ($result) {
            Log::info('General message notification sent', [
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id
            ]);
        }
    }

    /**
     * Check if conversation is marketplace-related
     */
    private function isMarketplaceConversation($conversation, $sender, $recipient): bool
    {
        // Check if either participant is a seller
        return $this->isUserSeller($sender) || $this->isUserSeller($recipient);
    }

    /**
     * Check if user is a seller
     */
    private function isUserSeller($user): bool
    {
        // Check if user has seller role or has marketplace seller profile
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole(['Supplier', 'Manufacturer'])) {
                return true;
            }
        }

        // Check if user has marketplace seller profile
        return $user->marketplaceSeller()->exists();
    }

    /**
     * Get message preview (first 100 characters)
     */
    private function getMessagePreview(string $content): string
    {
        return \Str::limit(strip_tags($content), 100);
    }

    /**
     * Check if recipient should receive notification
     */
    private function shouldNotifyRecipient($recipient, $conversation): bool
    {
        // Check if recipient has muted this conversation
        $participant = $conversation->participants()
            ->where('user_id', $recipient->id)
            ->first();

        if ($participant && isset($participant->settings['muted']) && $participant->settings['muted']) {
            return false;
        }

        // Check user's notification preferences
        $preferences = $recipient->notification_preferences ?? [];
        
        // Check if user has disabled message notifications
        if (isset($preferences['message_received']['enabled']) && !$preferences['message_received']['enabled']) {
            return false;
        }

        if (isset($preferences['seller_message']['enabled']) && !$preferences['seller_message']['enabled']) {
            return false;
        }

        return true;
    }
}
