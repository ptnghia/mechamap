<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagingSeeder extends Seeder
{
    /**
     * Seed messaging system với conversations thực tế
     * Tạo private messages giữa users
     */
    public function run(): void
    {
        $this->command->info('💬 Bắt đầu seed messaging system...');

        // Lấy dữ liệu cần thiết
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->error('❌ Cần ít nhất 2 users để tạo conversations!');
            return;
        }

        // Tạo conversations và messages
        $this->createConversations($users);

        $this->command->info('✅ Hoàn thành seed messaging system!');
    }

    private function createConversations($users): void
    {
        // Tạo 15-25 conversations
        $conversationCount = rand(15, 25);

        for ($i = 0; $i < $conversationCount; $i++) {
            // Random 2 users để tạo conversation
            $participants = $users->random(2);
            $user1 = $participants->first();
            $user2 = $participants->last();

            // Tạo conversation
            $conversation = Conversation::create([
                'title' => $this->getConversationTitle(),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // Thêm participants
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user1->id,
                'last_read_at' => $conversation->created_at,
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user2->id,
                'last_read_at' => $conversation->created_at,
            ]);

            // Tạo messages cho conversation này
            $this->createMessages($conversation, [$user1, $user2]);

            $this->command->line("   💬 Tạo conversation: {$conversation->title}");
        }
    }

    private function createMessages($conversation, $participants): void
    {
        // Mỗi conversation có 3-12 messages
        $messageCount = rand(3, 12);

        $messageData = $this->getMessageContents();

        for ($i = 0; $i < $messageCount; $i++) {
            // Alternate giữa 2 participants
            $sender = $participants[$i % 2];

            // Random message content
            $content = $messageData[array_rand($messageData)];

            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $sender->id,
                'content' => $content,
                'created_at' => $conversation->created_at->addMinutes($i * rand(30, 180)),
                'updated_at' => now(),
            ]);
        }
    }

    private function getConversationTitle(): ?string
    {
        $titles = [
            'Hỏi về SolidWorks',
            'Chia sẻ kinh nghiệm CNC',
            'Thảo luận dự án',
            'Tư vấn vật liệu',
            'Hợp tác project',
            null, // Một số conversation không có title
            null,
            'Câu hỏi về FEA',
            'Chia sẻ tài liệu',
            'Thảo luận kỹ thuật',
        ];

        return $titles[array_rand($titles)];
    }

    private function getMessageContents(): array
    {
        return [
            "Chào bạn! Mình có thể hỏi về kinh nghiệm làm việc với SolidWorks không?",
            "Hi! Tất nhiên rồi, bạn cần hỗ trợ gì về SolidWorks?",
            "Mình đang gặp vấn đề với assembly constraints. Bạn có tips nào không?",
            "Ah, assembly constraints! Mình thường dùng Coincident và Concentric nhiều nhất. Bạn đang làm gì mà bị stuck?",
            "Mình đang thiết kế một cơ cấu 4 thanh, nhưng over-defined hoài.",
            "Over-defined thường do redundant constraints. Bạn thử xóa một số constraints không cần thiết xem sao.",
            "Cảm ơn bạn! Mình sẽ thử. Btw, bạn có kinh nghiệm với Simulation không?",
            "Có đấy! Mình hay dùng Static Study và Modal Analysis. Bạn cần analyze cái gì?",
            "Mình cần check stress distribution trên một bracket. Có tutorial nào recommend không?",
            "Mình có một số tài liệu hay về FEA. Để mình share cho bạn nhé.",
            "Thanks a lot! Bạn thật sự helpful.",
            "Không có gì! Cộng đồng kỹ sư phải support nhau mà 😊",

            "Bạn ơi, cho mình hỏi về CNC programming được không?",
            "Sure! Bạn đang dùng phần mềm gì? Mastercam, PowerMill hay gì khác?",
            "Mình đang học Mastercam. Có tips nào cho roughing strategies không?",
            "Với Mastercam, mình recommend Dynamic Mill cho roughing. Efficient và tool life tốt.",
            "Dynamic Mill có khác gì với conventional roughing không?",
            "Dynamic Mill dùng trochoidal toolpath, giảm radial engagement nhưng tăng axial. Tool ít bị nóng hơn.",
            "Sounds interesting! Bạn có example nào không?",
            "Mình có một số project files. Để mình clean up rồi share cho bạn.",
            "Awesome! Mình đang cần học thêm về advanced strategies.",
            "No problem! Học CNC cần practice nhiều. Bạn có access máy CNC không?",

            "Hi! Mình thấy bạn post về robot programming. Có thể chat thêm không?",
            "Chào bạn! Tất nhiên, bạn quan tâm về robot nào? ABB, KUKA hay Fanuc?",
            "Mình đang làm với ABB IRB 1600. Cần tối ưu cycle time.",
            "IRB 1600 là robot tốt! Bạn đã thử optimize path planning chưa?",
            "Chưa, mình chỉ mới học RAPID programming thôi.",
            "Path planning rất quan trọng! Bạn có thể dùng RobotStudio để simulate và optimize.",
            "RobotStudio có free version không bạn?",
            "Có trial version 30 ngày. Đủ để bạn học cơ bản rồi.",
            "Great! Bạn có recommend course nào không?",
            "ABB có official training courses. Hoặc bạn có thể tự học qua documentation.",

            "Bạn có kinh nghiệm với vật liệu composite không?",
            "Có một chút! Bạn đang làm project gì với composite?",
            "Mình đang research carbon fiber cho automotive application.",
            "Carbon fiber thì properties tuyệt vời nhưng cost cao. Bạn có consider alternatives không?",
            "Mình cũng đang look into glass fiber và natural fiber composites.",
            "Natural fiber đang trending đấy! Hemp fiber và flax fiber có potential lớn.",
            "Sustainability aspect rất quan trọng nowadays.",
            "Exactly! OEMs đang push hard cho sustainable materials.",
            "Bạn có papers nào về topic này không?",
            "Mình có bookmark một số research papers. Để mình compile list gửi bạn.",

            "Chào bạn! Mình cần advice về career path trong mechanical engineering.",
            "Hi! Bạn đang ở stage nào của career? Fresh grad hay experienced?",
            "Mình fresh grad, đang confused giữa design và manufacturing.",
            "Both are good paths! Design creative hơn, manufacturing practical hơn. Bạn thích gì?",
            "Mình thích hands-on work nhưng cũng muốn creative freedom.",
            "Sounds like manufacturing engineering hoặc process engineering suit bạn đấy!",
            "Process engineering có scope như thế nào bạn?",
            "Process engineering focus vào optimize manufacturing processes, reduce waste, improve efficiency.",
            "Interesting! Có certifications nào recommend không?",
            "Six Sigma, Lean Manufacturing, và PMP là must-have cho process engineers.",
            "Thanks for the guidance! Mình sẽ research thêm.",
            "Good luck! Feel free to reach out nếu có questions thêm nhé!"
        ];
    }
}
