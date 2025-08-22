<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceMessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔔 Adding marketplace message notification templates...\n";

        $templates = [
            [
                'type' => 'seller_message',
                'name' => 'Seller Message',
                'description' => 'Notification when receiving a message from seller',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Tin nhắn từ người bán',
                    'message' => 'Bạn có tin nhắn mới từ người bán {sender_name}: {message_preview}',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Tin nhắn mới từ người bán - {sender_name}',
                    'template' => 'emails.seller-message',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url']
                ]),
                'is_active' => true
            ],
            [
                'type' => 'buyer_message',
                'name' => 'Buyer Message',
                'description' => 'Notification when receiving a message from buyer',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Tin nhắn từ người mua',
                    'message' => 'Bạn có tin nhắn mới từ người mua {sender_name}: {message_preview}',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Tin nhắn mới từ người mua - {sender_name}',
                    'template' => 'emails.buyer-message',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url']
                ]),
                'is_active' => true
            ],
            [
                'type' => 'marketplace_message_received',
                'name' => 'Marketplace Message Received',
                'description' => 'General marketplace message notification',
                'channels' => json_encode(['database', 'email']),
                'database_template' => json_encode([
                    'title' => 'Tin nhắn marketplace',
                    'message' => 'Bạn có tin nhắn mới liên quan đến marketplace từ {sender_name}: {message_preview}',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url', 'sender_role']
                ]),
                'email_template' => json_encode([
                    'subject' => 'Tin nhắn marketplace mới - {sender_name}',
                    'template' => 'emails.marketplace-message',
                    'variables' => ['sender_name', 'message_preview', 'conversation_url', 'sender_role']
                ]),
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['type' => $template['type']],
                array_merge($template, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
            echo "  ✅ Created/updated template: {$template['type']}\n";
        }

        echo "📊 Marketplace message templates added successfully!\n";
    }
}
