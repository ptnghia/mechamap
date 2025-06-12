<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;
use App\Models\User;
use App\Models\ThreadRating;
use Faker\Factory as Faker;

class ThreadRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy tất cả threads và users
        $threads = Thread::with(['user'])->limit(50)->get();
        $users = User::all();

        if ($threads->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Không có threads hoặc users để tạo ratings. Hãy chạy ThreadSeeder và UserSeeder trước.');
            return;
        }

        $this->command->info('Tạo thread ratings...');

        $ratingDescriptions = [
            5 => [
                'Excellent thread! Rất hữu ích và chi tiết.',
                'Outstanding quality! Giải thích rất rõ ràng.',
                'Perfect! Đây là exactly những gì tôi cần.',
                'Amazing work! Documentation rất đầy đủ.',
                'Superb! Design rất professional và modern.',
            ],
            4 => [
                'Very good! Chỉ thiếu một số details nhỏ.',
                'Great content! Implementation rất solid.',
                'Good quality! Có thể improve một chút UI.',
                'Nice work! Documentation khá đầy đủ.',
                'Well done! Performance tốt và stable.',
            ],
            3 => [
                'Average quality. Cần improve thêm.',
                'Okay. Có potential nhưng chưa hoàn thiện.',
                'Decent work. Một số phần cần rework.',
                'Fair. Code quality có thể better.',
                'Standard level. Nothing special.',
            ],
            2 => [
                'Below average. Nhiều issues cần fix.',
                'Poor quality. Documentation thiếu.',
                'Needs work. Performance chưa tốt.',
                'Unsatisfactory. UI/UX cần redesign.',
                'Limited functionality. Cần expand features.',
            ],
            1 => [
                'Very poor quality. Nhiều bugs.',
                'Terrible implementation. Không work.',
                'Awful design. User experience rất kém.',
                'Broken functionality. Cần rewrite.',
                'Unacceptable quality. Total rework needed.',
            ],
        ];

        $ratingsCreated = 0;
        $totalRatings = min(300, $threads->count() * 6); // Max 6 ratings per thread

        foreach ($threads as $thread) {
            if ($ratingsCreated >= $totalRatings) break;

            // Random number of ratings per thread (1-6)
            $ratingsCount = $faker->numberBetween(1, 6);

            // Ensure thread author doesn't rate their own thread
            $availableUsers = $users->where('id', '!=', $thread->user_id);

            if ($availableUsers->isEmpty()) continue;

            $ratedUsers = collect();

            for ($i = 0; $i < $ratingsCount && $ratingsCreated < $totalRatings; $i++) {
                // Select user who hasn't rated this thread yet
                $availableUsersForRating = $availableUsers->whereNotIn('id', $ratedUsers->pluck('id'));

                if ($availableUsersForRating->isEmpty()) break;

                $user = $availableUsersForRating->random();

                // Check if this user already rated this thread (extra safety)
                $existingRating = ThreadRating::where('thread_id', $thread->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($existingRating) {
                    continue; // Skip this iteration
                }

                $ratedUsers->push($user);

                // Generate realistic rating distribution (skewed positive)
                $rating = $faker->randomElement([
                    5,
                    5,
                    5,
                    4,
                    4,
                    4,
                    4,
                    3,
                    3,
                    2,
                    1  // More 4-5 stars
                ]);

                // 70% chance to include review text
                $review = null;
                if ($faker->boolean(70)) {
                    $reviewTexts = $ratingDescriptions[$rating];
                    $review = $faker->randomElement($reviewTexts);

                    // Sometimes add more specific technical feedback
                    if ($faker->boolean(30)) {
                        $technicalComments = [
                            'Code structure rất clean và maintainable.',
                            'Performance optimization khá tốt.',
                            'Security practices được implement properly.',
                            'Error handling cần improve thêm.',
                            'Database design khá efficient.',
                            'API documentation rất clear.',
                            'Unit tests coverage đầy đủ.',
                            'Responsive design work tốt trên mobile.',
                            'Accessibility standards được follow.',
                            'CI/CD pipeline setup professional.',
                        ];
                        $review .= ' ' . $faker->randomElement($technicalComments);
                    }
                }

                try {
                    ThreadRating::create([
                        'thread_id' => $thread->id,
                        'user_id' => $user->id,
                        'rating' => $rating,
                        'review' => $review,
                        'created_at' => $faker->dateTimeBetween($thread->created_at, 'now'),
                    ]);

                    $ratingsCreated++;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle duplicate entry gracefully
                    if ($e->errorInfo[1] == 1062) { // Duplicate entry error code
                        $this->command->warn("Duplicate rating skipped for thread {$thread->id} and user {$user->id}");
                        continue;
                    }
                    throw $e; // Re-throw if it's a different error
                }
            }
        }

        $this->command->info("✅ Đã tạo {$ratingsCreated} thread ratings");

        // Recalculate all thread ratings
        $this->command->info('Đang recalculate average ratings...');

        $threadsWithRatings = Thread::whereHas('ratings')->get();
        foreach ($threadsWithRatings as $thread) {
            $thread->recalculateRatings();
        }

        $this->command->info("✅ Đã recalculate ratings cho {$threadsWithRatings->count()} threads");
    }
}
