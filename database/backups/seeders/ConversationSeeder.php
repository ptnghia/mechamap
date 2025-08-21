<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        Message::truncate();
        ConversationParticipant::truncate();
        Conversation::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get some users for testing
        $users = User::limit(10)->get();
        
        if ($users->count() < 2) {
            $this->command->warn('Cần ít nhất 2 users để tạo conversations. Hãy chạy UserSeeder trước.');
            return;
        }

        $this->command->info('Tạo conversations mẫu...');

        // Create sample conversations
        $conversationData = [
            [
                'title' => null,
                'participants' => [$users[0]->id, $users[1]->id],
                'messages' => [
                    ['user_id' => $users[0]->id, 'content' => 'Chào bạn! Tôi có thể hỏi về dự án CAD của bạn không?'],
                    ['user_id' => $users[1]->id, 'content' => 'Chào! Tất nhiên rồi, bạn muốn hỏi gì?'],
                    ['user_id' => $users[0]->id, 'content' => 'Bạn sử dụng phần mềm nào để thiết kế? SolidWorks hay AutoCAD?'],
                    ['user_id' => $users[1]->id, 'content' => 'Tôi chủ yếu dùng SolidWorks cho 3D và AutoCAD cho 2D. Bạn thì sao?'],
                ]
            ],
            [
                'title' => null,
                'participants' => [$users[0]->id, $users[2]->id],
                'messages' => [
                    ['user_id' => $users[2]->id, 'content' => 'Xin chào! Tôi thấy bạn có kinh nghiệm về CNC machining. Có thể tư vấn giúp tôi không?'],
                    ['user_id' => $users[0]->id, 'content' => 'Chào bạn! Tôi rất sẵn lòng giúp đỡ. Bạn gặp vấn đề gì?'],
                    ['user_id' => $users[2]->id, 'content' => 'Tôi đang lập trình G-code cho máy phay CNC nhưng bị lỗi tool path. Bạn có thể xem giúp không?'],
                ]
            ],
            [
                'title' => null,
                'participants' => [$users[1]->id, $users[3]->id],
                'messages' => [
                    ['user_id' => $users[1]->id, 'content' => 'Hi! Tôi thấy bạn post về robot ABB rất hay. Có thể chia sẻ thêm không?'],
                    ['user_id' => $users[3]->id, 'content' => 'Cảm ơn bạn! Tôi đang làm dự án tích hợp robot vào dây chuyền sản xuất. Bạn quan tâm phần nào?'],
                    ['user_id' => $users[1]->id, 'content' => 'Tôi muốn tìm hiểu về programming robot và safety system. Có tài liệu nào recommend không?'],
                    ['user_id' => $users[3]->id, 'content' => 'Có! Tôi sẽ gửi cho bạn một số tài liệu ABB RobotStudio và safety guidelines.'],
                ]
            ],
            [
                'title' => null,
                'participants' => [$users[0]->id, $users[4]->id],
                'messages' => [
                    ['user_id' => $users[4]->id, 'content' => 'Chào anh! Em là sinh viên cơ khí, có thể xin lời khuyên về career path không ạ?'],
                    ['user_id' => $users[0]->id, 'content' => 'Chào em! Anh rất vui được chia sẻ. Em đang quan tâm lĩnh vực nào trong cơ khí?'],
                    ['user_id' => $users[4]->id, 'content' => 'Em thích thiết kế và automation ạ. Nhưng không biết nên focus vào CAD hay PLC programming?'],
                    ['user_id' => $users[0]->id, 'content' => 'Cả hai đều rất quan trọng! Anh suggest em nên học cả hai, nhưng có thể bắt đầu với CAD trước.'],
                    ['user_id' => $users[4]->id, 'content' => 'Cảm ơn anh! Em sẽ bắt đầu với SolidWorks và sau đó học thêm PLC.'],
                ]
            ],
            [
                'title' => null,
                'participants' => [$users[2]->id, $users[5]->id],
                'messages' => [
                    ['user_id' => $users[2]->id, 'content' => 'Bạn có kinh nghiệm về FEA analysis không? Tôi đang gặp khó khăn với ANSYS.'],
                    ['user_id' => $users[5]->id, 'content' => 'Có! Tôi dùng ANSYS khá nhiều. Bạn gặp vấn đề gì cụ thể?'],
                    ['user_id' => $users[2]->id, 'content' => 'Tôi đang phân tích stress trên một bracket nhưng kết quả không hợp lý. Mesh có vấn đề không nhỉ?'],
                ]
            ]
        ];

        foreach ($conversationData as $index => $data) {
            // Create conversation
            $conversation = Conversation::create([
                'title' => $data['title'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]);

            // Add participants
            foreach ($data['participants'] as $userId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                    'last_read_at' => rand(0, 1) ? now()->subMinutes(rand(5, 60)) : null,
                ]);
            }

            // Add messages
            foreach ($data['messages'] as $messageIndex => $messageData) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $messageData['user_id'],
                    'content' => $messageData['content'],
                    'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes($messageIndex * 5),
                ]);
            }

            // Update conversation timestamp to match last message
            $lastMessage = $conversation->messages()->latest()->first();
            if ($lastMessage) {
                $conversation->update(['updated_at' => $lastMessage->created_at]);
            }

            $this->command->info("✅ Tạo conversation " . ($index + 1) . " với " . count($data['messages']) . " messages");
        }

        // Create some additional conversations with different users
        if ($users->count() >= 8) {
            $additionalConversations = [
                [
                    'participants' => [$users[6]->id, $users[7]->id],
                    'messages' => [
                        ['user_id' => $users[6]->id, 'content' => 'Bạn có file CAD nào về gear box không? Tôi đang cần tham khảo.'],
                        ['user_id' => $users[7]->id, 'content' => 'Có! Tôi có một số file SolidWorks về planetary gearbox. Bạn cần loại nào?'],
                    ]
                ],
                [
                    'participants' => [$users[5]->id, $users[8]->id],
                    'messages' => [
                        ['user_id' => $users[8]->id, 'content' => 'Chào bạn! Tôi thấy bạn có post về material selection rất hay.'],
                        ['user_id' => $users[5]->id, 'content' => 'Cảm ơn! Bạn đang làm dự án gì mà cần chọn vật liệu?'],
                        ['user_id' => $users[8]->id, 'content' => 'Tôi đang thiết kế một pressure vessel, cần chọn giữa carbon steel và stainless steel.'],
                        ['user_id' => $users[5]->id, 'content' => 'Tùy vào môi trường làm việc và budget. Nếu có corrosive environment thì nên dùng stainless steel.'],
                    ]
                ]
            ];

            foreach ($additionalConversations as $index => $data) {
                $conversation = Conversation::create([
                    'title' => null,
                    'created_at' => now()->subDays(rand(1, 15)),
                    'updated_at' => now()->subHours(rand(1, 12)),
                ]);

                foreach ($data['participants'] as $userId) {
                    ConversationParticipant::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $userId,
                        'last_read_at' => rand(0, 1) ? now()->subMinutes(rand(10, 120)) : null,
                    ]);
                }

                foreach ($data['messages'] as $messageIndex => $messageData) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $messageData['user_id'],
                        'content' => $messageData['content'],
                        'created_at' => now()->subDays(rand(0, 3))->subHours(rand(0, 12))->subMinutes($messageIndex * 3),
                    ]);
                }

                $lastMessage = $conversation->messages()->latest()->first();
                if ($lastMessage) {
                    $conversation->update(['updated_at' => $lastMessage->created_at]);
                }

                $this->command->info("✅ Tạo additional conversation " . ($index + 1));
            }
        }

        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        
        $this->command->info("🎉 Hoàn thành! Đã tạo {$totalConversations} conversations với {$totalMessages} messages.");
        $this->command->info("💡 Bây giờ bạn có thể test chat widget bằng cách đăng nhập vào website.");
    }
}
