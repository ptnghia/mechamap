<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\CommentDislike;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EnhancedThreadStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('Updating existing threads với enhanced states...');

        $threads = Thread::with(['comments', 'user'])->get();
        $users = User::all();

        if ($threads->isEmpty()) {
            $this->command->warn('Không có threads để update.');
            return;
        }

        $updatedThreads = 0;
        $solvedThreads = 0;
        $flaggedThreads = 0;
        $archivedThreads = 0;

        foreach ($threads as $thread) {
            $updateData = [];

            // 1. Update lifecycle states
            $createdDaysAgo = $thread->created_at->diffInDays(now());

            // 5% threads archived (chỉ những thread cũ)
            if ($createdDaysAgo > 180 && $faker->boolean(5)) {
                $updateData['archived_at'] = $faker->dateTimeBetween($thread->created_at->addDays(90), 'now');
                $updateData['archived_reason'] = $faker->randomElement([
                    'Thread quá cũ, không còn relevant',
                    'Thông tin đã outdated',
                    'Moved to new category',
                    'Project đã completed'
                ]);
                $archivedThreads++;
            }

            // 2% threads hidden (moderation)
            if ($faker->boolean(2)) {
                $updateData['hidden_at'] = $faker->dateTimeBetween($thread->created_at, 'now');
                $updateData['hidden_reason'] = $faker->randomElement([
                    'Spam content detected',
                    'Inappropriate language',
                    'Off-topic discussion',
                    'Duplicate content'
                ]);
            }

            // 2. Update moderation states
            // 3% threads flagged
            if ($faker->boolean(3)) {
                $updateData['is_flagged'] = true;
                $updateData['flagged_at'] = $faker->dateTimeBetween($thread->created_at, 'now');
                $updateData['flagged_by'] = $users->random()->id;
                $updateData['reports_count'] = $faker->numberBetween(1, 5);
                $updateData['moderation_notes'] = $faker->randomElement([
                    'Multiple user reports received',
                    'Questionable content requires review',
                    'Potential copyright violation',
                    'Needs moderator attention'
                ]);
                $flaggedThreads++;
            }

            // 1% spam (very low percentage)
            if ($faker->boolean(1)) {
                $updateData['is_spam'] = true;
                $updateData['moderation_status'] = 'spam'; // Changed from 'rejected' to 'spam'
            } else {
                $updateData['moderation_status'] = 'approved';
            }

            // 3. Update quality states
            // Thread types distribution
            $threadTypes = [
                'tutorial' => 25,      // 25%
                'discussion' => 30,    // 30%
                'showcase' => 15,      // 15%
                'question' => 20,      // 20%
                'announcement' => 5,   // 5%
                'project' => 5,        // 5% - Changed from 'review' to 'project'
            ];

            $threadType = $faker->randomElement(array_keys($threadTypes));
            $updateData['thread_type'] = $threadType;

            // 15% threads solved (mainly questions and discussions)
            if (in_array($threadType, ['question', 'discussion']) && $faker->boolean(15)) {
                $comments = $thread->comments;
                if ($comments->isNotEmpty()) {
                    $solutionComment = $comments->random();

                    $updateData['is_solved'] = true;
                    $updateData['solved_at'] = $faker->dateTimeBetween($solutionComment->created_at, 'now');
                    $updateData['solution_comment_id'] = $solutionComment->id;
                    $updateData['solved_by'] = $thread->user_id; // Author marks as solved

                    $solvedThreads++;
                }
            }

            // Quality score based on thread age, comments, and type
            $commentsCount = $thread->comments->count();
            $viewsCount = $thread->view_count ?? 0;

            $qualityScore = 50; // Base score

            // Adjustments based on engagement
            $qualityScore += min($commentsCount * 2, 20); // Max +20 for comments
            $qualityScore += min($viewsCount / 10, 15);   // Max +15 for views

            // Type bonuses
            $typeBonus = [
                'tutorial' => 10,
                'showcase' => 8,
                'announcement' => 5,
                'question' => 3,
                'discussion' => 2,
                'review' => 6,
            ];
            $qualityScore += $typeBonus[$threadType] ?? 0;

            // Random variation
            $qualityScore += $faker->numberBetween(-10, 10);
            $qualityScore = max(0, min(100, $qualityScore)); // Clamp 0-100

            $updateData['quality_score'] = $qualityScore;

            // 4. Update activity tracking
            $lastComment = $thread->comments()->latest()->first();
            if ($lastComment) {
                $updateData['last_activity_at'] = $lastComment->created_at;
                $updateData['last_comment_at'] = $lastComment->created_at;
                $updateData['last_comment_by'] = $lastComment->user_id;
            } else {
                $updateData['last_activity_at'] = $thread->created_at;
            }

            // Bump count (some threads get bumped)
            if ($faker->boolean(20)) {
                $updateData['bump_count'] = $faker->numberBetween(1, 5);
                $updateData['last_bump_at'] = $faker->dateTimeBetween($thread->created_at, 'now');
            }

            // Share count
            $updateData['share_count'] = $faker->numberBetween(0, 20);

            // Cached counters
            $updateData['cached_comments_count'] = $commentsCount;
            $updateData['cached_participants_count'] = $thread->comments()
                ->select('user_id')
                ->distinct()
                ->count() + 1;

            // 5. SEO and content enhancements
            $updateData['meta_description'] = $faker->text(150);
            $updateData['read_time'] = max(1, strlen($thread->content) / 200); // ~200 words per minute

            // Search keywords based on content
            $contentWords = str_word_count($thread->title . ' ' . $thread->content, 1);
            $keywords = collect($contentWords)
                ->filter(fn($word) => strlen($word) > 4)
                ->take(10)
                ->toArray();
            $updateData['search_keywords'] = $keywords;

            // Update thread
            $thread->update($updateData);
            $updatedThreads++;
        }

        $this->command->info("✅ Updated {$updatedThreads} threads với enhanced states");
        $this->command->info("   - Solved threads: {$solvedThreads}");
        $this->command->info("   - Flagged threads: {$flaggedThreads}");
        $this->command->info("   - Archived threads: {$archivedThreads}");

        // Update comment states
        $this->updateCommentStates($faker);
    }

    private function updateCommentStates($faker)
    {
        $this->command->info('Updating comment states...');

        $comments = Comment::with(['user'])->get();
        $users = User::all();

        $updatedComments = 0;
        $flaggedComments = 0;
        $dislikesCreated = 0;

        foreach ($comments as $comment) {
            $updateData = [];

            // 10% comments được edit
            if ($faker->boolean(10)) {
                $updateData['edited_at'] = $faker->dateTimeBetween($comment->created_at, 'now');
                $updateData['edited_by'] = $comment->user_id;
                $updateData['edit_count'] = $faker->numberBetween(1, 3);
                $updateData['edit_reason'] = $faker->randomElement([
                    'Grammar correction',
                    'Added more details',
                    'Fixed typo',
                    'Updated information',
                    'Clarification'
                ]);
            }

            // 2% comments flagged
            if ($faker->boolean(2)) {
                $updateData['is_flagged'] = true;
                $flaggedComments++;
            }

            // 1% spam comments
            if ($faker->boolean(1)) {
                $updateData['is_spam'] = true;
            }

            // Quality score for comments
            $likes = $comment->like_count ?? 0;
            $qualityScore = 50 + ($likes * 5) + $faker->numberBetween(-15, 15);
            $updateData['quality_score'] = max(0, min(100, $qualityScore));

            // Dislikes count
            $dislikesCount = $faker->numberBetween(0, max(1, $likes / 2));
            $updateData['dislikes_count'] = $dislikesCount;

            // Update comment
            $comment->update($updateData);
            $updatedComments++;

            // Create comment dislikes
            if ($dislikesCount > 0 && $users->count() >= $dislikesCount) {
                $dislikedUsers = $users->random($dislikesCount);
                foreach ($dislikedUsers as $user) {
                    if ($user->id !== $comment->user_id) { // User can't dislike own comment
                        CommentDislike::firstOrCreate([
                            'comment_id' => $comment->id,
                            'user_id' => $user->id,
                        ]);
                        $dislikesCreated++;
                    }
                }
            }
        }

        $this->command->info("✅ Updated {$updatedComments} comments");
        $this->command->info("✅ Created {$dislikesCreated} comment dislikes");
    }
}
