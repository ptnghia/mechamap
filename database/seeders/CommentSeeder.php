<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Seed comments với nội dung chuyên ngành cơ khí
     * Tạo comments thực tế cho các threads đã có
     */
    public function run(): void
    {
        $this->command->info('💬 Bắt đầu seed comments với nội dung chuyên ngành...');

        // Lấy dữ liệu cần thiết
        $threads = Thread::with('forum')->get();
        $users = User::all();

        if ($threads->isEmpty()) {
            $this->command->error('❌ Không có threads! Chạy ThreadSeeder trước.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('❌ Không có users! Chạy UserSeeder trước.');
            return;
        }

        // Tạo comments cho từng thread
        foreach ($threads as $thread) {
            $this->createCommentsForThread($thread, $users);
        }

        // Cập nhật comment counts cho threads
        $this->updateThreadCommentCounts();

        $this->command->info('✅ Hoàn thành seed comments!');
    }

    private function createCommentsForThread(Thread $thread, $users): void
    {
        // Số lượng comments ngẫu nhiên cho mỗi thread (1-8 comments)
        $commentCount = rand(1, 8);

        // Lấy nội dung comments dựa vào thread title và forum
        $commentData = $this->getCommentsForThread($thread);

        // Tạo main comments
        $mainComments = [];
        for ($i = 0; $i < min($commentCount, count($commentData)); $i++) {
            $author = $users->random();
            $commentInfo = $commentData[$i];

            $comment = Comment::create([
                'thread_id' => $thread->id,
                'user_id' => $author->id,
                'parent_id' => null,
                'content' => $commentInfo['content'],
                'has_media' => $commentInfo['has_media'] ?? false,
                'has_code_snippet' => $commentInfo['has_code_snippet'] ?? false,
                'has_formula' => $commentInfo['has_formula'] ?? false,
                'formula_content' => $commentInfo['formula_content'] ?? null,
                'like_count' => rand(0, 25),
                'dislikes_count' => rand(0, 5),
                'helpful_count' => rand(0, 15),
                'expert_endorsements' => rand(0, 3),
                'quality_score' => rand(300, 500) / 100, // 3.0 - 5.0
                'technical_accuracy_score' => rand(350, 500) / 100, // 3.5 - 5.0
                'verification_status' => $this->getVerificationStatus(),
                'verified_by' => $this->getVerifiedBy($users),
                'verified_at' => $this->getVerifiedAt(),
                'technical_tags' => json_encode($commentInfo['technical_tags'] ?? []),
                'answer_type' => $commentInfo['answer_type'] ?? 'general',
                'is_flagged' => false,
                'is_spam' => false,
                'is_solution' => $commentInfo['is_solution'] ?? false,
                'reports_count' => 0,
                'edit_count' => rand(0, 2),
                'created_at' => now()->subDays(rand(0, 25)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            $mainComments[] = $comment;

            $this->command->line("   💬 Tạo comment cho thread: {$thread->title}");
        }

        // Tạo replies cho một số comments (30% chance)
        foreach ($mainComments as $mainComment) {
            if (rand(1, 100) <= 30) {
                $this->createReplyComment($mainComment, $thread, $users);
            }
        }
    }

    private function createReplyComment(Comment $parentComment, Thread $thread, $users): void
    {
        $author = $users->random();
        $replyContent = $this->getReplyContent($parentComment->content);

        Comment::create([
            'thread_id' => $thread->id,
            'user_id' => $author->id,
            'parent_id' => $parentComment->id,
            'content' => $replyContent,
            'has_media' => false,
            'has_code_snippet' => rand(0, 1),
            'has_formula' => false,
            'like_count' => rand(0, 10),
            'dislikes_count' => rand(0, 2),
            'helpful_count' => rand(0, 8),
            'expert_endorsements' => rand(0, 1),
            'quality_score' => rand(250, 450) / 100, // 2.5 - 4.5
            'technical_accuracy_score' => rand(300, 450) / 100, // 3.0 - 4.5
            'verification_status' => 'unverified',
            'technical_tags' => json_encode(['discussion', 'follow-up']),
            'answer_type' => 'general',
            'is_flagged' => false,
            'is_spam' => false,
            'is_solution' => false,
            'reports_count' => 0,
            'edit_count' => 0,
            'created_at' => $parentComment->created_at->addMinutes(rand(10, 1440)),
            'updated_at' => now()->subDays(rand(0, 3)),
        ]);
    }

    private function getCommentsForThread(Thread $thread): array
    {
        $threadTitle = strtolower($thread->title);
        $forumName = strtolower($thread->forum->name ?? '');

        // SolidWorks related comments
        if (str_contains($threadTitle, 'solidworks')) {
            return $this->getSolidWorksComments();
        }

        // CNC related comments
        if (str_contains($threadTitle, 'cnc') || str_contains($threadTitle, 'mastercam')) {
            return $this->getCNCComments();
        }

        // FEA/Analysis related comments
        if (str_contains($threadTitle, 'fea') || str_contains($threadTitle, 'ansys')) {
            return $this->getFEAComments();
        }

        // PLC related comments
        if (str_contains($threadTitle, 'plc') || str_contains($threadTitle, 'siemens')) {
            return $this->getPLCComments();
        }

        // Robot related comments
        if (str_contains($threadTitle, 'robot') || str_contains($threadTitle, 'abb')) {
            return $this->getRobotComments();
        }

        // Material related comments
        if (str_contains($threadTitle, 'thép') || str_contains($threadTitle, 'aluminum') || str_contains($threadTitle, 'vật liệu')) {
            return $this->getMaterialComments();
        }

        // Default general comments
        return $this->getGeneralComments();
    }

    private function getSolidWorksComments(): array
    {
        return [
            [
                'content' => "Cảm ơn bạn đã chia sẻ! Mình cũng gặp vấn đề tương tự với large assemblies. Tip về SpeedPak rất hữu ích, đã thử và thấy performance cải thiện đáng kể.\n\nThêm một tip nữa: sử dụng **Lightweight mode** cho các components không cần edit thường xuyên. Có thể tiết kiệm đến 60% memory usage.",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['solidworks', 'performance', 'assembly'],
                'answer_type' => 'experience',
                'is_solution' => false
            ],
            [
                'content' => "Về vấn đề **Graphics Settings**, mình khuyên nên:\n\n1. **Image Quality**: Giảm xuống Medium cho assemblies > 500 parts\n2. **RealView**: Chỉ bật khi cần render presentation\n3. **Anti-aliasing**: Tắt hoàn toàn khi modeling\n\nVới setup này, máy i7-8700K + GTX 1060 của mình handle được assembly 2000+ parts khá mượt.",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['solidworks', 'graphics', 'optimization'],
                'answer_type' => 'tutorial',
                'is_solution' => true
            ],
            [
                'content' => "Bổ sung thêm về **Hardware Optimization**:\n\n- **RAM**: 32GB là sweet spot, 64GB chỉ cần thiết cho assemblies > 5000 parts\n- **Storage**: NVMe SSD cho OS + SolidWorks, SATA SSD cho project files\n- **Graphics**: Quadro RTX 4000 trở lên cho professional work\n\nĐặc biệt chú ý **Page File** settings - nên set manual 1.5x RAM size.",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['hardware', 'performance', 'system'],
                'answer_type' => 'reference',
                'is_solution' => false
            ]
        ];
    }

    private function getCNCComments(): array
    {
        return [
            [
                'content' => "Kinh nghiệm hay! Về **Dynamic Mill**, mình thường set:\n\n```\nStock to leave: 0.2mm cho roughing\nMin toolpath radius: 65% tool diameter\nOptimal load: 15-20% cho aluminum\nMax stepdown: 3x tool diameter\n```\n\nVới aluminum 6061, speeds/feeds này work rất tốt:\n- 12mm end mill: 8000 RPM, 2000 mm/min\n- Coolant: Flood hoặc mist đều OK",
                'has_code_snippet' => true,
                'has_formula' => false,
                'technical_tags' => ['cnc', 'mastercam', 'aluminum'],
                'answer_type' => 'calculation',
                'is_solution' => true
            ],
            [
                'content' => "Về **Tool Selection**, có thể tham khảo bảng này:\n\n**Aluminum 6061:**\n- Roughing: Uncoated carbide, 3 flutes\n- Finishing: Polished carbide, 2 flutes\n- Coating: Tránh TiN (dễ stick)\n\n**Steel 1045:**\n- Roughing: TiAlN coated, 4 flutes\n- Finishing: TiN coated, 2-3 flutes\n- Coolant: Bắt buộc phải có\n\nChipload formula: **Feed = RPM × Flutes × Chipload**",
                'has_code_snippet' => false,
                'has_formula' => true,
                'formula_content' => "Feed = RPM \\times Flutes \\times Chipload",
                'technical_tags' => ['tooling', 'feeds-speeds', 'materials'],
                'answer_type' => 'reference',
                'is_solution' => false
            ]
        ];
    }

    // Thêm các methods khác...
    private function getFEAComments(): array
    {
        return [
            [
                'content' => "Excellent tips! Về **Mesh Quality**, mình thường check:\n\n1. **Aspect Ratio** < 3:1 cho structural analysis\n2. **Skewness** < 0.7 (tốt nhất < 0.5)\n3. **Jacobian** > 0.6\n\nVới ANSYS Mechanical, **Patch Conforming Method** cho geometry phức tạp, **Patch Independent** cho simple parts.",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['fea', 'meshing', 'ansys'],
                'answer_type' => 'tutorial',
                'is_solution' => true
            ],
            [
                'content' => "Về **Boundary Conditions**, một số lưu ý:\n\n- **Fixed Support**: Chỉ dùng khi thực sự cần thiết\n- **Remote Displacement**: Tốt hơn cho bolt connections\n- **Contact**: Bonded vs Frictional vs Frictionless\n\n**Von Mises Stress** công thức:\nσ_vm = √[(σ₁-σ₂)² + (σ₂-σ₃)² + (σ₃-σ₁)²]/√2",
                'has_code_snippet' => false,
                'has_formula' => true,
                'formula_content' => "\\sigma_{vm} = \\sqrt{\\frac{(\\sigma_1-\\sigma_2)^2 + (\\sigma_2-\\sigma_3)^2 + (\\sigma_3-\\sigma_1)^2}{2}}",
                'technical_tags' => ['stress-analysis', 'boundary-conditions', 'theory'],
                'answer_type' => 'calculation',
                'is_solution' => false
            ]
        ];
    }

    private function getPLCComments(): array
    {
        return [
            [
                'content' => "Code ladder rất clear! Thêm một số tips cho **S7-1200**:\n\n```ladder\n// Memory optimization\nNetwork 1: Pulse Generator\n+--[CLK]--+--[TON]--+--( )--+\n|  M0.0   |   T1    |  M0.1 |\n+--------+  PT:500ms +-------+\n\n// Edge detection\nNetwork 2: Rising Edge\n+--[P]---+--( )--+\n|  I0.0  |  M1.0 |\n+--------+-------+\n```\n\n**Addressing tips:**\n- I0.0-I0.7: Digital inputs\n- Q0.0-Q0.7: Digital outputs  \n- M0.0-M255.7: Memory bits",
                'has_code_snippet' => true,
                'has_formula' => false,
                'technical_tags' => ['plc', 'ladder', 'siemens'],
                'answer_type' => 'tutorial',
                'is_solution' => true
            ],
            [
                'content' => "Về **TIA Portal**, một số shortcuts hữu ích:\n\n- **Ctrl+1**: Switch to Portal view\n- **Ctrl+2**: Switch to Project view\n- **F7**: Start simulation\n- **F8**: Download to PLC\n\n**Best practices:**\n1. Luôn comment networks\n2. Sử dụng symbolic addressing\n3. Organize code bằng FCs và FBs\n4. Regular backup projects",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['tia-portal', 'programming', 'best-practices'],
                'answer_type' => 'experience',
                'is_solution' => false
            ]
        ];
    }

    private function getRobotComments(): array
    {
        return [
            [
                'content' => "Great case study! Về **ABB RAPID programming**, một số tips:\n\n```rapid\n! Optimized move sequence\nMoveJ pHome, v1000, fine, tool0;\nMoveL Offs(pTarget, 0, 0, 50), v500, z10, tool0;\nMoveL pTarget, v100, fine, tool0;\n\n! Error handling\nIF DOutput(doGripper) = 0 THEN\n    TPWrite \"Gripper failed!\";\n    Stop;\nENDIF\n```\n\n**Cycle time optimization:**\n- Sử dụng **zone data** thay vì fine\n- **Concurrent I/O** operations\n- **Path blending** cho smooth motion",
                'has_code_snippet' => true,
                'has_formula' => false,
                'technical_tags' => ['abb', 'rapid', 'programming'],
                'answer_type' => 'tutorial',
                'is_solution' => true
            ],
            [
                'content' => "**ROI calculation** rất impressive! Thêm một số factors khác:\n\n**Hidden costs:**\n- Training: $5,000-10,000\n- Maintenance: $8,000/year\n- Spare parts inventory: $15,000\n- Safety upgrades: $20,000\n\n**Additional benefits:**\n- Reduced insurance costs\n- Improved workplace safety\n- 24/7 operation capability\n- Consistent quality\n\n**Payback formula:** \nPayback = Initial Investment / (Annual Savings - Annual Costs)",
                'has_code_snippet' => false,
                'has_formula' => true,
                'formula_content' => "Payback = \\frac{Initial\\ Investment}{Annual\\ Savings - Annual\\ Costs}",
                'technical_tags' => ['roi', 'economics', 'business-case'],
                'answer_type' => 'calculation',
                'is_solution' => false
            ]
        ];
    }

    private function getMaterialComments(): array
    {
        return [
            [
                'content' => "Thông tin về **heat treatment** rất chi tiết! Bổ sung về **quenching media:**\n\n**Water quenching:**\n- Cooling rate: ~600°C/s\n- Risk: High distortion, cracking\n- Use: Simple geometry, low carbon steel\n\n**Oil quenching:**\n- Cooling rate: ~150°C/s  \n- Better: Less distortion\n- Use: Complex parts, medium carbon\n\n**Polymer quenching:**\n- Cooling rate: Variable (100-400°C/s)\n- Advantage: Controllable cooling curve\n- Cost: Higher than oil/water",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['heat-treatment', 'quenching', 'steel'],
                'answer_type' => 'reference',
                'is_solution' => false
            ],
            [
                'content' => "Về **7075-T6 aluminum**, thêm thông tin machining:\n\n**Cutting parameters:**\n```\nSpeed: 300-500 m/min\nFeed: 0.15-0.25 mm/tooth\nDepth: 2-8 mm\nCoolant: Flood recommended\n```\n\n**Tool selection:**\n- Uncoated carbide preferred\n- Sharp cutting edges essential\n- Positive rake angle: 15-20°\n- Helix angle: 45° minimum\n\n**Surface finish:** Ra 0.8-1.6 μm achievable with proper setup",
                'has_code_snippet' => true,
                'has_formula' => false,
                'technical_tags' => ['aluminum', 'machining', 'aerospace'],
                'answer_type' => 'tutorial',
                'is_solution' => true
            ]
        ];
    }

    private function getGeneralComments(): array
    {
        return [
            [
                'content' => "Cảm ơn bạn đã chia sẻ! Thông tin rất hữu ích cho những người mới bắt đầu như mình. Có thể bạn chia sẻ thêm về practical applications không?",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['discussion', 'general'],
                'answer_type' => 'general',
                'is_solution' => false
            ],
            [
                'content' => "Mình đã áp dụng theo hướng dẫn này và thấy kết quả khá tốt. Tuy nhiên có một số điểm cần lưu ý thêm:\n\n1. Cần kiểm tra compatibility với hệ thống hiện tại\n2. Training cho operators là rất quan trọng\n3. Maintenance schedule phải được tuân thủ nghiêm ngặt\n\nOverall, đây là approach đáng thử!",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['implementation', 'best-practices'],
                'answer_type' => 'experience',
                'is_solution' => false
            ],
            [
                'content' => "Excellent explanation! Mình có thêm một số resources hữu ích:\n\n- Standards: ISO 9001, ASME Y14.5\n- Software: Miễn phí alternatives\n- Training: Online courses và certifications\n- Community: Forums và professional groups\n\nAi cần thêm thông tin có thể PM mình!",
                'has_code_snippet' => false,
                'has_formula' => false,
                'technical_tags' => ['resources', 'standards', 'learning'],
                'answer_type' => 'reference',
                'is_solution' => false
            ]
        ];
    }

    private function getReplyContent(string $parentContent): string
    {
        $replies = [
            "Cảm ơn bạn đã chia sẻ! Mình sẽ thử approach này.",
            "Thông tin rất hữu ích. Có thể bạn elaborate thêm về phần implementation không?",
            "Mình đã thử method này và thấy kết quả khá tốt. Recommend!",
            "Interesting approach! Có case study nào cụ thể không?",
            "Thanks for the detailed explanation. Saved cho reference sau này.",
            "Bổ sung thêm: cần chú ý về safety requirements khi implement.",
            "Agree với points này. Đặc biệt là phần về cost-benefit analysis.",
            "Có alternative solution nào khác không? Current approach hơi complex.",
            "Perfect timing! Mình đang research về topic này.",
            "Upvoted! Đây chính xác là thông tin mình cần."
        ];

        return $replies[array_rand($replies)];
    }

    private function getVerificationStatus(): string
    {
        $statuses = ['unverified', 'pending', 'verified', 'disputed'];
        $weights = [60, 20, 15, 5]; // 60% unverified, 20% pending, 15% verified, 5% disputed

        $random = rand(1, 100);
        $cumulative = 0;

        for ($i = 0; $i < count($weights); $i++) {
            $cumulative += $weights[$i];
            if ($random <= $cumulative) {
                return $statuses[$i];
            }
        }

        return 'unverified';
    }

    private function getVerifiedBy($users): ?int
    {
        // 20% chance có verified_by
        if (rand(1, 100) <= 20) {
            return $users->random()->id;
        }
        return null;
    }

    private function getVerifiedAt(): ?string
    {
        // 15% chance có verified_at
        if (rand(1, 100) <= 15) {
            return now()->subDays(rand(1, 10));
        }
        return null;
    }

    private function updateThreadCommentCounts(): void
    {
        $this->command->info('🔄 Cập nhật comment counts cho threads...');

        // Update comment counts cho tất cả threads
        DB::statement("
            UPDATE threads
            SET replies = (
                SELECT COUNT(*)
                FROM comments
                WHERE comments.thread_id = threads.id
                AND comments.deleted_at IS NULL
            )
        ");

        // Update last_activity_at
        DB::statement("
            UPDATE threads
            SET last_activity_at = (
                SELECT MAX(comments.created_at)
                FROM comments
                WHERE comments.thread_id = threads.id
                AND comments.deleted_at IS NULL
            )
            WHERE EXISTS (
                SELECT 1 FROM comments
                WHERE comments.thread_id = threads.id
                AND comments.deleted_at IS NULL
            )
        ");
    }
}
