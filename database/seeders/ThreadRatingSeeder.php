<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Support\Facades\DB;

class ThreadRatingSeeder extends Seeder
{
    /**
     * Seed thread ratings với đánh giá thực tế
     * Tạo ratings cho threads dựa vào quality
     */
    public function run(): void
    {
        $this->command->info('⭐ Bắt đầu seed thread ratings...');

        // Lấy dữ liệu cần thiết
        $users = User::all();
        $threads = Thread::all();

        if ($users->isEmpty() || $threads->isEmpty()) {
            $this->command->error('❌ Cần có users và threads trước khi seed ratings!');
            return;
        }

        // Tạo ratings cho threads
        $this->createThreadRatings($users, $threads);

        $this->command->info('✅ Hoàn thành seed thread ratings!');
    }

    private function createThreadRatings($users, $threads): void
    {
        $ratings = [];
        
        foreach ($threads as $thread) {
            // Số lượng ratings dựa vào thread quality và type
            $ratingCount = $this->getRatingCount($thread);
            
            if ($ratingCount > 0) {
                $ratingUsers = $users->random(min($ratingCount, $users->count()));
                
                foreach ($ratingUsers as $user) {
                    // Tránh self-rating
                    if ($user->id !== $thread->user_id) {
                        $rating = $this->generateRating($thread, $user);
                        
                        $ratings[] = [
                            'user_id' => $user->id,
                            'thread_id' => $thread->id,
                            'rating' => $rating['rating'],
                            'review' => $rating['review'],
                            'created_at' => $thread->created_at->addDays(rand(1, 15)),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Batch insert
        $chunks = array_chunk($ratings, 500);
        foreach ($chunks as $chunk) {
            DB::table('thread_ratings')->insert($chunk);
        }

        $this->command->line("   ⭐ Tạo " . count($ratings) . " thread ratings");
    }

    private function getRatingCount($thread): int
    {
        $baseCount = 2; // Base rating count
        
        // Bonus cho featured threads
        if ($thread->is_featured) {
            $baseCount += 8;
        }
        
        // Bonus cho solved threads
        if ($thread->is_solved) {
            $baseCount += 5;
        }
        
        // Bonus cho tutorial threads
        if ($thread->thread_type === 'tutorial') {
            $baseCount += 6;
        }
        
        // Bonus cho high quality threads
        if ($thread->quality_score >= 8.0) {
            $baseCount += 4;
        }
        
        // Bonus cho threads với nhiều replies
        if ($thread->replies >= 5) {
            $baseCount += 3;
        }
        
        // Bonus cho expert verified threads
        if ($thread->expert_verified) {
            $baseCount += 5;
        }
        
        return min($baseCount + rand(-2, 3), 15); // Cap at 15 ratings
    }

    private function generateRating($thread, $user): array
    {
        // Base rating dựa vào thread quality
        $baseRating = 3.5;
        
        // Adjust dựa vào thread characteristics
        if ($thread->is_featured) $baseRating += 0.8;
        if ($thread->is_solved) $baseRating += 0.6;
        if ($thread->thread_type === 'tutorial') $baseRating += 0.5;
        if ($thread->quality_score >= 8.0) $baseRating += 0.4;
        if ($thread->expert_verified) $baseRating += 0.7;
        if ($thread->has_calculations) $baseRating += 0.3;
        if ($thread->has_3d_models) $baseRating += 0.3;
        
        // Add some randomness
        $rating = $baseRating + (rand(-50, 50) / 100); // ±0.5 variation
        $rating = max(1, min(5, $rating)); // Clamp between 1-5
        $rating = round($rating * 2) / 2; // Round to nearest 0.5
        
        // Generate review based on rating
        $review = $this->generateReview($thread, $rating);
        
        return [
            'rating' => $rating,
            'review' => $review
        ];
    }

    private function generateReview($thread, $rating): ?string
    {
        // 30% chance không có review
        if (rand(1, 100) <= 30) {
            return null;
        }
        
        $threadTitle = strtolower($thread->title);
        
        if ($rating >= 4.5) {
            $excellentReviews = [
                "Excellent tutorial! Very detailed và easy to follow. Đã apply successfully vào project của mình.",
                "Outstanding content! Technical depth rất impressive. Recommend cho mọi người trong field này.",
                "Perfect explanation! Exactly những gì mình cần. Thanks for sharing!",
                "Brilliant work! Approach này rất innovative và practical. Đã save để reference sau này.",
                "Amazing quality! Documentation rất thorough và professional. 5 stars deserved!",
                "Exceptional content! Mình đã học được rất nhiều từ thread này. Keep up the great work!",
                "Top-notch tutorial! Step-by-step instructions rất clear. Highly recommended!",
                "Fantastic resource! Technical accuracy rất cao và presentation excellent."
            ];
            
            if (str_contains($threadTitle, 'solidworks')) {
                return "Excellent SolidWorks tutorial! Techniques này rất useful cho advanced modeling. Thanks for sharing!";
            } elseif (str_contains($threadTitle, 'cnc')) {
                return "Outstanding CNC programming guide! Toolpath strategies rất effective. Đã test và results tuyệt vời!";
            } elseif (str_contains($threadTitle, 'fea') || str_contains($threadTitle, 'analysis')) {
                return "Brilliant FEA analysis! Methodology rất sound và results convincing. Professional quality work!";
            }
            
            return $excellentReviews[array_rand($excellentReviews)];
        }
        
        if ($rating >= 3.5) {
            $goodReviews = [
                "Good content! Helpful information nhưng có thể elaborate thêm một số points.",
                "Solid tutorial! Easy to understand và practical. Thanks for sharing!",
                "Nice work! Approach này interesting nhưng mình có một số questions về implementation.",
                "Good explanation! Mình đã apply một phần và thấy effective. Recommend!",
                "Helpful thread! Information rất useful cho beginners như mình.",
                "Well written! Technical content accurate và presentation clear.",
                "Good resource! Đã bookmark để reference sau này. Thanks!",
                "Useful information! Approach này có potential nhưng cần more testing."
            ];
            
            return $goodReviews[array_rand($goodReviews)];
        }
        
        if ($rating >= 2.5) {
            $averageReviews = [
                "Decent content nhưng có thể improve thêm. Some points chưa clear lắm.",
                "OK tutorial nhưng missing một số important details. Overall still helpful.",
                "Average quality. Information basic nhưng good starting point cho beginners.",
                "Not bad nhưng có thể add more examples và practical applications.",
                "Acceptable content. Có một số useful points nhưng presentation có thể better.",
                "Fair explanation. Covers basics nhưng lacks depth cho advanced users.",
                "Mediocre quality. Information correct nhưng presentation có thể improve."
            ];
            
            return $averageReviews[array_rand($averageReviews)];
        }
        
        // Rating < 2.5
        $poorReviews = [
            "Content chưa đủ detailed. Cần add more explanations và examples.",
            "Information basic quá. Không có much value cho experienced users.",
            "Presentation chưa clear. Có thể restructure để easier to follow.",
            "Missing important details. Thread này cần more comprehensive coverage.",
            "Content accuracy questionable. Cần verify lại một số technical points.",
            "Too brief và lacks practical examples. Cần expand significantly.",
            "Not very helpful. Information too general và không specific enough."
        ];
        
        return $poorReviews[array_rand($poorReviews)];
    }
}
