<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showcase;
use App\Models\User;
use App\Models\ShowcaseRating;
use App\Models\ShowcaseRatingReply;
use App\Models\ShowcaseRatingLike;
use App\Models\ShowcaseRatingReplyLike;

class ShowcaseRatingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Tạo dữ liệu test cho hệ thống rating & comment tích hợp:
     * - Ratings với media và reviews
     * - Replies cho ratings
     * - Nested replies
     * - Like system
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Showcase Rating System...');

        // Get sample showcases and users
        $showcases = Showcase::take(3)->get();
        $users = User::take(10)->get();

        if ($showcases->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️  No showcases or users found. Please seed them first.');
            return;
        }

        foreach ($showcases as $showcase) {
            $this->seedRatingsForShowcase($showcase, $users);
        }

        $this->command->info('✅ Showcase Rating System seeded successfully!');
    }

    /**
     * Seed ratings for a specific showcase.
     */
    private function seedRatingsForShowcase(Showcase $showcase, $users): void
    {
        $this->command->info("📝 Seeding ratings for showcase: {$showcase->title}");

        // Create 5-8 ratings per showcase
        $ratingsCount = rand(5, 8);
        $createdRatings = [];

        for ($i = 0; $i < $ratingsCount; $i++) {
            $user = $users->random();
            
            // Skip if user already rated this showcase
            if ($showcase->ratings()->where('user_id', $user->id)->exists()) {
                continue;
            }

            $rating = $this->createRating($showcase, $user);
            $createdRatings[] = $rating;

            // Add some likes to ratings (30% chance)
            if (rand(1, 100) <= 30) {
                $this->addLikesToRating($rating, $users);
            }

            // Add replies to some ratings (40% chance)
            if (rand(1, 100) <= 40) {
                $this->addRepliesToRating($rating, $users);
            }
        }

        $this->command->info("   ✓ Created {$ratingsCount} ratings");
    }

    /**
     * Create a single rating with optional media.
     */
    private function createRating(Showcase $showcase, User $user): ShowcaseRating
    {
        $hasMedia = rand(1, 100) <= 25; // 25% chance of having media
        $hasReview = rand(1, 100) <= 70; // 70% chance of having review

        $images = [];
        if ($hasMedia) {
            // Simulate 1-3 images
            $imageCount = rand(1, 3);
            for ($j = 0; $j < $imageCount; $j++) {
                $images[] = "ratings/sample-image-{$j}.jpg";
            }
        }

        $reviews = [
            'Dự án rất chất lượng và hữu ích cho cộng đồng kỹ thuật. Tài liệu chi tiết và dễ hiểu.',
            'Thiết kế sáng tạo và áp dụng được trong thực tế. Cảm ơn tác giả đã chia sẻ.',
            'Phân tích kỹ thuật rất chi tiết, giúp tôi hiểu rõ hơn về quy trình thiết kế.',
            'Dự án hay nhưng cần bổ sung thêm tài liệu hướng dẫn chi tiết.',
            'Ứng dụng tốt các phần mềm CAD/CAM. Kết quả đạt yêu cầu kỹ thuật.',
            'Cách tiếp cận vấn đề rất logic và khoa học. Đáng học hỏi.',
            'Dự án có tính ứng dụng cao trong công nghiệp. Chúc mừng tác giả!',
            'Thiết kế tối ưu và hiệu quả. Có thể áp dụng cho nhiều trường hợp khác.',
        ];

        return ShowcaseRating::create([
            'showcase_id' => $showcase->id,
            'user_id' => $user->id,
            'technical_quality' => rand(3, 5),
            'innovation' => rand(2, 5),
            'usefulness' => rand(3, 5),
            'documentation' => rand(2, 5),
            'review' => $hasReview ? $reviews[array_rand($reviews)] : null,
            'has_media' => $hasMedia,
            'images' => $hasMedia ? $images : null,
            'like_count' => 0, // Will be updated by likes
        ]);
    }

    /**
     * Add likes to a rating.
     */
    private function addLikesToRating(ShowcaseRating $rating, $users): void
    {
        $likesCount = rand(1, 5);
        $likedUsers = $users->random($likesCount);

        foreach ($likedUsers as $user) {
            // Skip if user is the rating author
            if ($user->id === $rating->user_id) {
                continue;
            }

            ShowcaseRatingLike::create([
                'rating_id' => $rating->id,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Add replies to a rating.
     */
    private function addRepliesToRating(ShowcaseRating $rating, $users): void
    {
        $repliesCount = rand(1, 4);
        $createdReplies = [];

        for ($i = 0; $i < $repliesCount; $i++) {
            $user = $users->random();
            
            // Skip if user is the rating author for first reply
            if ($i === 0 && $user->id === $rating->user_id) {
                $user = $users->where('id', '!=', $rating->user_id)->random();
            }

            $reply = $this->createReply($rating, $user);
            $createdReplies[] = $reply;

            // Add likes to some replies (20% chance)
            if (rand(1, 100) <= 20) {
                $this->addLikesToReply($reply, $users);
            }

            // Add nested reply (30% chance)
            if (rand(1, 100) <= 30 && count($createdReplies) > 0) {
                $parentReply = $createdReplies[array_rand($createdReplies)];
                $nestedUser = $users->random();
                
                $this->createNestedReply($rating, $parentReply, $nestedUser);
            }
        }
    }

    /**
     * Create a single reply.
     */
    private function createReply(ShowcaseRating $rating, User $user, ShowcaseRatingReply $parent = null): ShowcaseRatingReply
    {
        $hasMedia = rand(1, 100) <= 15; // 15% chance of having media in replies

        $images = [];
        if ($hasMedia) {
            // Simulate 1-2 images for replies
            $imageCount = rand(1, 2);
            for ($j = 0; $j < $imageCount; $j++) {
                $images[] = "replies/sample-reply-image-{$j}.jpg";
            }
        }

        $replyContents = [
            'Cảm ơn bạn đã chia sẻ đánh giá chi tiết!',
            'Tôi cũng có cùng ý kiến về dự án này.',
            'Có thể bạn chia sẻ thêm về phần kỹ thuật được không?',
            'Dự án này đã giúp tôi rất nhiều trong công việc.',
            'Bạn có thể hướng dẫn thêm về cách sử dụng không?',
            'Tôi đã thử áp dụng và kết quả rất tốt.',
            'Cần bổ sung thêm tài liệu về phần này.',
            'Rất hữu ích cho người mới bắt đầu như tôi.',
        ];

        return ShowcaseRatingReply::create([
            'rating_id' => $rating->id,
            'user_id' => $user->id,
            'parent_id' => $parent ? $parent->id : null,
            'content' => $replyContents[array_rand($replyContents)],
            'has_media' => $hasMedia,
            'images' => $hasMedia ? $images : null,
            'like_count' => 0, // Will be updated by likes
        ]);
    }

    /**
     * Create a nested reply.
     */
    private function createNestedReply(ShowcaseRating $rating, ShowcaseRatingReply $parent, User $user): ShowcaseRatingReply
    {
        return $this->createReply($rating, $user, $parent);
    }

    /**
     * Add likes to a reply.
     */
    private function addLikesToReply(ShowcaseRatingReply $reply, $users): void
    {
        $likesCount = rand(1, 3);
        $likedUsers = $users->random($likesCount);

        foreach ($likedUsers as $user) {
            // Skip if user is the reply author
            if ($user->id === $reply->user_id) {
                continue;
            }

            ShowcaseRatingReplyLike::create([
                'reply_id' => $reply->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
