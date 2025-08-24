<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\ConversationType;
use App\Models\GroupRequest;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use App\Enums\GroupRole;
use App\Enums\GroupRequestStatus;

class SampleGroupConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get approved group request
        $approvedRequest = GroupRequest::where('status', GroupRequestStatus::APPROVED)->first();

        if (!$approvedRequest) {
            $this->command->warn('⚠️ No approved group request found');
            return;
        }

        // Create group conversation from approved request
        $groupConversation = Conversation::create([
            'title' => $approvedRequest->title,
            'conversation_type_id' => $approvedRequest->conversation_type_id,
            'is_group' => true,
            'group_request_id' => $approvedRequest->id,
            'max_members' => $approvedRequest->conversationType->max_members,
            'is_public' => false,
            'group_description' => $approvedRequest->description,
            'group_rules' => 'Quy tắc nhóm:
1. Tôn trọng ý kiến của các thành viên khác
2. Chia sẻ kiến thức một cách tích cực
3. Không spam hoặc quảng cáo không liên quan
4. Sử dụng ngôn ngữ lịch sự và chuyên nghiệp
5. Tập trung vào chủ đề chính của nhóm',
        ]);

        // Add creator as group creator
        GroupMember::create([
            'conversation_id' => $groupConversation->id,
            'user_id' => $approvedRequest->creator_id,
            'role' => GroupRole::CREATOR,
            'joined_at' => now(),
            'is_active' => true,
        ]);

        // Add some sample members
        $sampleUsers = User::whereIn('role', ['member', 'senior_member', 'verified_partner'])
                          ->where('id', '!=', $approvedRequest->creator_id)
                          ->take(5)
                          ->get();

        foreach ($sampleUsers as $index => $user) {
            $role = match($index) {
                0 => GroupRole::ADMIN,
                1 => GroupRole::MODERATOR,
                default => GroupRole::MEMBER,
            };

            GroupMember::create([
                'conversation_id' => $groupConversation->id,
                'user_id' => $user->id,
                'role' => $role,
                'joined_at' => now()->subDays(rand(1, 3)),
                'invited_by' => $approvedRequest->creator_id,
                'invitation_accepted_at' => now()->subDays(rand(0, 2)),
                'is_active' => true,
            ]);
        }

        // Create system message for group creation
        Message::createSystemMessage(
            $groupConversation->id,
            'group_created',
            "🎉 Nhóm '{$groupConversation->title}' đã được tạo thành công!",
            [
                'group_request_id' => $approvedRequest->id,
                'creator_id' => $approvedRequest->creator_id,
                'created_at' => now()->toISOString(),
            ],
            $approvedRequest->creator_id
        );

        // Add some sample messages
        $sampleMessages = [
            [
                'user_id' => $approvedRequest->creator_id,
                'content' => 'Chào mừng mọi người đến với nhóm! Hãy cùng nhau chia sẻ kiến thức và kinh nghiệm về robot tự động hóa. 🤖',
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $sampleUsers[0]->id,
                'content' => 'Cảm ơn bạn đã tạo nhóm! Tôi rất hứng thú với dự án này. Chúng ta sẽ bắt đầu từ đâu? 🚀',
                'created_at' => now()->subHours(1),
            ],
            [
                'user_id' => $sampleUsers[1]->id,
                'content' => 'Tôi có kinh nghiệm về PLC programming và HMI design. Sẵn sàng đóng góp cho dự án! 💪',
                'created_at' => now()->subMinutes(30),
            ],
        ];

        foreach ($sampleMessages as $messageData) {
            Message::create([
                'conversation_id' => $groupConversation->id,
                'user_id' => $messageData['user_id'],
                'content' => $messageData['content'],
                'is_system_message' => false,
                'created_at' => $messageData['created_at'],
                'updated_at' => $messageData['created_at'],
            ]);
        }

        // Add member join system messages
        foreach ($sampleUsers->take(3) as $user) {
            Message::createSystemMessage(
                $groupConversation->id,
                'member_joined',
                "{$user->name} đã tham gia nhóm",
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'joined_at' => now()->subDays(rand(1, 2))->toISOString(),
                ],
                $user->id // Add user_id parameter
            );
        }

        $this->command->info('✅ Created sample group conversation with ' . ($sampleUsers->count() + 1) . ' members');
    }
}
