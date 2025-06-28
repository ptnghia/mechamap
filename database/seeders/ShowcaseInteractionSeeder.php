<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showcase;
use App\Models\ShowcaseComment;
use App\Models\ShowcaseLike;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShowcaseInteractionSeeder extends Seeder
{
    /**
     * Seed showcase interactions: comments, likes
     * Tạo interactions cho showcases
     */
    public function run(): void
    {
        $this->command->info('🎨 Bắt đầu seed showcase interactions...');

        // Lấy dữ liệu cần thiết
        $showcases = Showcase::all();
        $users = User::all();

        if ($showcases->isEmpty() || $users->isEmpty()) {
            $this->command->error('❌ Cần có showcases và users trước khi seed interactions!');
            return;
        }

        // Seed theo thứ tự
        $this->seedShowcaseComments($showcases, $users);
        $this->seedShowcaseLikes($showcases, $users);

        // Cập nhật cached counters
        $this->updateShowcaseCounts();

        $this->command->info('✅ Hoàn thành seed showcase interactions!');
    }

    private function seedShowcaseComments($showcases, $users): void
    {
        $this->command->info('💬 Seeding showcase comments...');
        
        foreach ($showcases as $showcase) {
            // Mỗi showcase có 2-6 comments
            $commentCount = rand(2, 6);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $author = $users->random();
                $commentContent = $this->getShowcaseComment($showcase, $i);
                
                ShowcaseComment::create([
                    'showcase_id' => $showcase->id,
                    'user_id' => $author->id,
                    'parent_id' => null,
                    'comment' => $commentContent,
                    'like_count' => rand(0, 15),
                    'created_at' => $showcase->created_at->addDays(rand(1, 10)),
                    'updated_at' => now()->subDays(rand(0, 3)),
                ]);
            }
            
            $this->command->line("   💬 Tạo comments cho showcase: {$showcase->title}");
        }
    }

    private function seedShowcaseLikes($showcases, $users): void
    {
        $this->command->info('👍 Seeding showcase likes...');
        
        $showcaseLikes = [];
        $processedPairs = [];

        foreach ($showcases as $showcase) {
            // Showcase likes dựa vào quality và featured status
            $likePercentage = $this->getShowcaseLikePercentage($showcase);
            $likeCount = ceil($users->count() * $likePercentage / 100);
            
            // Random users để like showcase này
            $likingUsers = $users->random(min($likeCount, $users->count()));
            
            foreach ($likingUsers as $user) {
                $pairKey = $user->id . '-' . $showcase->id;
                
                // Tránh duplicate và self-like
                if (!in_array($pairKey, $processedPairs) && $user->id !== $showcase->user_id) {
                    $showcaseLikes[] = [
                        'showcase_id' => $showcase->id,
                        'user_id' => $user->id,
                        'created_at' => $showcase->created_at->addDays(rand(1, 15)),
                        'updated_at' => now(),
                    ];
                    
                    $processedPairs[] = $pairKey;
                }
            }
        }

        // Batch insert
        $chunks = array_chunk($showcaseLikes, 500);
        foreach ($chunks as $chunk) {
            ShowcaseLike::insert($chunk);
        }

        $this->command->line("   ✅ Tạo " . count($showcaseLikes) . " showcase likes");
    }

    private function getShowcaseComment($showcase, $index): string
    {
        $comments = [
            "Dự án rất ấn tượng! Phần phân tích FEA đặc biệt chi tiết. Có thể bạn chia sẻ thêm về boundary conditions không?",
            "Excellent work! Kết quả tối ưu hóa 15% weight reduction rất impressive. Bạn có consider material alternatives không?",
            "Thiết kế rất professional. Mình cũng đang làm project tương tự, có thể tham khảo approach của bạn được không?",
            "Great documentation! Phần calculations rất clear và easy to follow. Thanks for sharing!",
            "Impressive results! Cycle time 45 seconds là rất competitive. Safety considerations có được implement chưa?",
            "Tuyệt vời! Phần CFD analysis rất detailed. Mesh quality như thế nào và convergence criteria bạn dùng gì?",
            "Professional quality work! Có plan nào để scale up solution này cho production không?",
            "Amazing project! Learning objectives rất clear, perfect cho educational purposes.",
            "Solid engineering approach! Cost analysis có được include trong scope không?",
            "Outstanding work! Có thể elaborate thêm về manufacturing constraints không?"
        ];

        // Specific comments dựa vào project type
        if (str_contains(strtolower($showcase->title), 'robot')) {
            $robotComments = [
                "Robot programming rất smooth! RAPID code có optimize cho cycle time chưa?",
                "Vision system integration impressive! Accuracy như thế nào trong production?",
                "Safety implementation rất thorough. Có test với different part variations chưa?",
                "Welding quality control excellent! Có real-time monitoring không?"
            ];
            return $robotComments[array_rand($robotComments)];
        }

        if (str_contains(strtolower($showcase->title), 'cfd') || str_contains(strtolower($showcase->title), 'cooling')) {
            $cfdComments = [
                "CFD results rất convincing! Mesh independence study có được thực hiện chưa?",
                "Heat transfer analysis detailed! Experimental validation có available không?",
                "Flow optimization impressive! Pressure drop reduction 20% là significant.",
                "Turbulence model selection appropriate! Y+ values trong acceptable range chưa?"
            ];
            return $cfdComments[array_rand($cfdComments)];
        }

        if (str_contains(strtolower($showcase->title), 'cnc') || str_contains(strtolower($showcase->title), 'toolpath')) {
            $cncComments = [
                "Toolpath optimization excellent! Surface finish Ra 0.8 là rất impressive.",
                "Adaptive milling strategy smart choice! Tool life improvement 60% là outstanding.",
                "High-speed machining parameters well optimized! Có test với different materials chưa?",
                "Material removal rate 120 cm³/min rất competitive! Spindle load như thế nào?"
            ];
            return $cncComments[array_rand($cncComments)];
        }

        return $comments[array_rand($comments)];
    }

    private function getShowcaseLikePercentage($showcase): int
    {
        $basePercentage = 25; // Base 25%
        
        // Bonus cho featured showcases
        if ($showcase->status === 'featured') {
            $basePercentage += 20;
        }
        
        // Bonus cho high technical quality
        if ($showcase->technical_quality_score >= 4.5) {
            $basePercentage += 15;
        }
        
        // Bonus cho high rating
        if ($showcase->rating_average >= 4.5) {
            $basePercentage += 10;
        }
        
        // Bonus cho educational content
        if ($showcase->has_tutorial && $showcase->has_calculations) {
            $basePercentage += 12;
        }
        
        // Bonus cho downloadable content
        if ($showcase->has_cad_files && $showcase->allow_downloads) {
            $basePercentage += 8;
        }
        
        // Bonus cho advanced complexity
        if ($showcase->complexity_level === 'advanced') {
            $basePercentage += 8;
        }
        
        // Bonus cho specific industries
        if (in_array($showcase->industry_application, ['aerospace', 'automotive'])) {
            $basePercentage += 6;
        }
        
        return min($basePercentage, 70); // Cap at 70%
    }

    private function updateShowcaseCounts(): void
    {
        $this->command->info('🔄 Cập nhật showcase counts...');
        
        // Update showcase like counts
        DB::statement("
            UPDATE showcases 
            SET like_count = (
                SELECT COUNT(*) 
                FROM showcase_likes 
                WHERE showcase_likes.showcase_id = showcases.id
            )
        ");
        
        $this->command->line("   ✅ Cập nhật showcase counts hoàn thành");
    }
}
