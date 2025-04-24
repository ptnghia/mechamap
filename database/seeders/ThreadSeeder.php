<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Forum;
use App\Models\Media;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Thread;
use App\Models\ThreadFollow;
use App\Models\ThreadLike;
use App\Models\ThreadSave;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $categories = Category::all();
        $forums = Forum::all();
        
        // Tạo dữ liệu demo cho các loại threads khác nhau
        $this->createNewsThreads($faker, $users, $categories, $forums);
        $this->createProjectThreads($faker, $users, $categories, $forums);
        $this->createArchitectureThreads($faker, $users, $categories, $forums);
        $this->createUrbanPlanningThreads($faker, $users, $categories, $forums);
        $this->createQAThreads($faker, $users, $categories, $forums);
        $this->createShowcaseThreads($faker, $users, $categories, $forums);
    }

    /**
     * Tạo threads tin tức
     */
    private function createNewsThreads($faker, $users, $categories, $forums): void
    {
        $newsCategory = Category::where('slug', 'tin-tuc')->first();
        if (!$newsCategory) {
            $newsCategory = $categories->first();
        }
        
        $newsForum = Forum::where('name', 'News & Announcements')->first();
        if (!$newsForum) {
            $newsForum = $forums->first();
        }
        
        for ($i = 1; $i <= 5; $i++) {
            $title = "Tin tức: " . $faker->sentence(6);
            $user = $users->random();
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $faker->paragraphs(5, true),
                'user_id' => $user->id,
                'category_id' => $newsCategory->id,
                'forum_id' => $newsForum->id,
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(50, 2000),
                'participant_count' => 0,
            ]);
            
            // Tạo comments
            $this->createComments($faker, $thread, $users);
            
            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
        }
    }

    /**
     * Tạo threads dự án
     */
    private function createProjectThreads($faker, $users, $categories, $forums): void
    {
        $projectCategory = Category::where('slug', 'du-an')->first();
        if (!$projectCategory) {
            $projectCategory = $categories->first();
        }
        
        $projectForums = [];
        $hardware = Forum::where('name', 'Hardware')->first();
        $software = Forum::where('name', 'Software')->first();
        
        if ($hardware) {
            $projectForums[] = $hardware;
        }
        
        if ($software) {
            $projectForums[] = $software;
        }
        
        if (empty($projectForums)) {
            $projectForums = [$forums->first()];
        }
        
        $locations = ['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Nha Trang'];
        $usages = ['Residential', 'Commercial', 'Mixed-Use', 'Office', 'Retail', 'Hotel'];
        $statuses = ['Proposed', 'Approved', 'Under Construction', 'Completed', 'On Hold', 'Cancelled'];
        
        for ($i = 1; $i <= 10; $i++) {
            $title = "Dự án: " . $faker->company() . " " . $faker->words(3, true);
            $user = $users->random();
            $forum = $faker->randomElement($projectForums);
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $faker->paragraphs(5, true),
                'user_id' => $user->id,
                'category_id' => $projectCategory->id,
                'forum_id' => $forum->id,
                'location' => $faker->randomElement($locations),
                'usage' => $faker->randomElement($usages),
                'floors' => $faker->numberBetween(5, 100),
                'status' => $faker->randomElement($statuses),
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(100, 5000),
                'participant_count' => 0,
            ]);
            
            // Tạo comments
            $this->createComments($faker, $thread, $users);
            
            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
            
            // Tạo media
            $this->createMedia($faker, $thread, $user);
            
            // Tạo poll
            if ($faker->boolean(30)) {
                $this->createPoll($faker, $thread, $users);
            }
        }
    }

    /**
     * Tạo threads kiến trúc
     */
    private function createArchitectureThreads($faker, $users, $categories, $forums): void
    {
        $architectureCategory = Category::where('slug', 'kien-truc')->first();
        if (!$architectureCategory) {
            $architectureCategory = $categories->first();
        }
        
        $architectureForums = [];
        $events = Forum::where('name', 'Events')->first();
        $feedback = Forum::where('name', 'Feedback')->first();
        
        if ($events) {
            $architectureForums[] = $events;
        }
        
        if ($feedback) {
            $architectureForums[] = $feedback;
        }
        
        if (empty($architectureForums)) {
            $architectureForums = [$forums->first()];
        }
        
        for ($i = 1; $i <= 5; $i++) {
            $title = "Kiến trúc: " . $faker->sentence(6);
            $user = $users->random();
            $forum = $faker->randomElement($architectureForums);
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $faker->paragraphs(5, true),
                'user_id' => $user->id,
                'category_id' => $architectureCategory->id,
                'forum_id' => $forum->id,
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(50, 2000),
                'participant_count' => 0,
            ]);
            
            // Tạo comments
            $this->createComments($faker, $thread, $users);
            
            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
            
            // Tạo media
            $this->createMedia($faker, $thread, $user);
        }
    }

    /**
     * Tạo threads quy hoạch đô thị
     */
    private function createUrbanPlanningThreads($faker, $users, $categories, $forums): void
    {
        $urbanPlanningCategory = Category::where('slug', 'quy-hoach-do-thi')->first();
        if (!$urbanPlanningCategory) {
            $urbanPlanningCategory = $categories->first();
        }
        
        $urbanPlanningForums = [];
        $programming = Forum::where('name', 'Programming')->first();
        $mobile = Forum::where('name', 'Mobile')->first();
        
        if ($programming) {
            $urbanPlanningForums[] = $programming;
        }
        
        if ($mobile) {
            $urbanPlanningForums[] = $mobile;
        }
        
        if (empty($urbanPlanningForums)) {
            $urbanPlanningForums = [$forums->first()];
        }
        
        for ($i = 1; $i <= 5; $i++) {
            $title = "Quy hoạch: " . $faker->sentence(6);
            $user = $users->random();
            $forum = $faker->randomElement($urbanPlanningForums);
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $faker->paragraphs(5, true),
                'user_id' => $user->id,
                'category_id' => $urbanPlanningCategory->id,
                'forum_id' => $forum->id,
                'location' => $faker->city,
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(30),
                'view_count' => $faker->numberBetween(50, 2000),
                'participant_count' => 0,
            ]);
            
            // Tạo comments
            $this->createComments($faker, $thread, $users);
            
            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
            
            // Tạo poll
            if ($faker->boolean(50)) {
                $this->createPoll($faker, $thread, $users);
            }
        }
    }

    /**
     * Tạo threads hỏi đáp
     */
    private function createQAThreads($faker, $users, $categories, $forums): void
    {
        $qaCategory = Category::where('slug', 'hoi-dap')->first();
        if (!$qaCategory) {
            $qaCategory = $categories->first();
        }
        
        $qaForum = Forum::where('name', 'Help & Support')->first();
        if (!$qaForum) {
            $qaForum = $forums->first();
        }
        
        for ($i = 1; $i <= 5; $i++) {
            $title = "Hỏi đáp: " . $faker->sentence(6) . "?";
            $user = $users->random();
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $faker->paragraphs(3, true),
                'user_id' => $user->id,
                'category_id' => $qaCategory->id,
                'forum_id' => $qaForum->id,
                'is_sticky' => $faker->boolean(10),
                'is_locked' => $faker->boolean(10),
                'is_featured' => $faker->boolean(10),
                'view_count' => $faker->numberBetween(20, 500),
                'participant_count' => 0,
            ]);
            
            // Tạo comments
            $this->createComments($faker, $thread, $users);
            
            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
        }
    }

    /**
     * Tạo threads showcase
     */
    private function createShowcaseThreads($faker, $users, $categories, $forums): void
    {
        $showcaseCategory = Category::where('slug', 'du-an')->first();
        if (!$showcaseCategory) {
            $showcaseCategory = $categories->first();
        }
        
        $showcaseForum = Forum::where('name', 'Introductions')->first();
        if (!$showcaseForum) {
            $showcaseForum = $forums->first();
        }
        
        $locations = ['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Nha Trang'];
        $usages = ['Residential', 'Commercial', 'Mixed-Use', 'Office', 'Retail', 'Hotel'];
        $statuses = ['Proposed', 'Approved', 'Under Construction', 'Completed', 'On Hold', 'Cancelled'];
        
        for ($i = 1; $i <= 5; $i++) {
            $title = "Showcase: " . $faker->company() . " " . $faker->words(3, true);
            $user = $users->random();
            
            $thread = Thread::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'content' => $faker->paragraphs(5, true),
                'user_id' => $user->id,
                'category_id' => $showcaseCategory->id,
                'forum_id' => $showcaseForum->id,
                'location' => $faker->randomElement($locations),
                'usage' => $faker->randomElement($usages),
                'floors' => $faker->numberBetween(5, 100),
                'status' => $faker->randomElement($statuses),
                'is_sticky' => $faker->boolean(20),
                'is_locked' => $faker->boolean(10),
                'is_featured' => true,
                'view_count' => $faker->numberBetween(500, 10000),
                'participant_count' => 0,
            ]);
            
            // Tạo comments
            $this->createComments($faker, $thread, $users);
            
            // Tạo likes, saves, follows
            $this->createInteractions($faker, $thread, $users);
            
            // Tạo media
            $this->createMedia($faker, $thread, $user, 3);
        }
    }

    /**
     * Tạo comments cho thread
     */
    private function createComments($faker, $thread, $users): void
    {
        $commentCount = $faker->numberBetween(3, 15);
        $commentUsers = [];
        
        for ($i = 1; $i <= $commentCount; $i++) {
            $user = $users->random();
            $commentUsers[$user->id] = true;
            
            $comment = Comment::create([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'content' => $faker->paragraphs($faker->numberBetween(1, 3), true),
            ]);
            
            // Tạo likes cho comment
            $likeCount = $faker->numberBetween(0, 10);
            for ($j = 0; $j < $likeCount; $j++) {
                $likeUser = $users->random();
                
                CommentLike::firstOrCreate([
                    'comment_id' => $comment->id,
                    'user_id' => $likeUser->id,
                ]);
            }
            
            // Cập nhật like_count
            $comment->like_count = $comment->likes()->count();
            $comment->save();
            
            // Tạo replies cho comment (30% cơ hội)
            if ($faker->boolean(30)) {
                $replyCount = $faker->numberBetween(1, 5);
                
                for ($k = 0; $k < $replyCount; $k++) {
                    $replyUser = $users->random();
                    $commentUsers[$replyUser->id] = true;
                    
                    $reply = Comment::create([
                        'thread_id' => $thread->id,
                        'user_id' => $replyUser->id,
                        'parent_id' => $comment->id,
                        'content' => $faker->paragraphs($faker->numberBetween(1, 2), true),
                    ]);
                    
                    // Tạo likes cho reply
                    $replyLikeCount = $faker->numberBetween(0, 5);
                    for ($l = 0; $l < $replyLikeCount; $l++) {
                        $likeUser = $users->random();
                        
                        CommentLike::firstOrCreate([
                            'comment_id' => $reply->id,
                            'user_id' => $likeUser->id,
                        ]);
                    }
                    
                    // Cập nhật like_count
                    $reply->like_count = $reply->likes()->count();
                    $reply->save();
                }
            }
        }
        
        // Cập nhật participant_count
        $thread->participant_count = count($commentUsers);
        $thread->save();
    }

    /**
     * Tạo likes, saves, follows cho thread
     */
    private function createInteractions($faker, $thread, $users): void
    {
        // Tạo likes
        $likeCount = $faker->numberBetween(5, 30);
        for ($i = 0; $i < $likeCount; $i++) {
            $user = $users->random();
            
            ThreadLike::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
            ]);
        }
        
        // Tạo saves
        $saveCount = $faker->numberBetween(2, 15);
        for ($i = 0; $i < $saveCount; $i++) {
            $user = $users->random();
            
            ThreadSave::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
            ]);
        }
        
        // Tạo follows
        $followCount = $faker->numberBetween(3, 20);
        for ($i = 0; $i < $followCount; $i++) {
            $user = $users->random();
            
            ThreadFollow::firstOrCreate([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Tạo media cho thread
     */
    private function createMedia($faker, $thread, $user, $count = 1): void
    {
        $imageTypes = ['jpg', 'png'];
        
        for ($i = 0; $i < $count; $i++) {
            $type = $faker->randomElement($imageTypes);
            $path = "thread-images/thread-{$thread->id}-image-{$i}.{$type}";
            
            Media::create([
                'user_id' => $user->id,
                'thread_id' => $thread->id,
                'file_name' => "thread-{$thread->id}-image-{$i}.{$type}",
                'file_path' => $path,
                'file_type' => "image/{$type}",
                'file_size' => $faker->numberBetween(100000, 5000000),
                'title' => $faker->sentence(4),
                'description' => $faker->sentence(10),
            ]);
        }
    }

    /**
     * Tạo poll cho thread
     */
    private function createPoll($faker, $thread, $users): void
    {
        $poll = Poll::create([
            'thread_id' => $thread->id,
            'question' => $faker->sentence(6) . '?',
            'max_options' => $faker->randomElement([1, 2, 3]),
            'allow_change_vote' => $faker->boolean(70),
            'show_votes_publicly' => $faker->boolean(60),
            'allow_view_without_vote' => $faker->boolean(80),
            'close_at' => $faker->boolean(30) ? $faker->dateTimeBetween('+1 week', '+1 month') : null,
        ]);
        
        // Tạo options
        $optionCount = $faker->numberBetween(2, 6);
        $options = [];
        
        for ($i = 0; $i < $optionCount; $i++) {
            $option = PollOption::create([
                'poll_id' => $poll->id,
                'text' => $faker->sentence(3),
            ]);
            
            $options[] = $option;
        }
        
        // Tạo votes
        $voteCount = $faker->numberBetween(5, 30);
        $votedUsers = [];
        
        for ($i = 0; $i < $voteCount; $i++) {
            $user = $users->random();
            
            // Đảm bảo mỗi user chỉ vote một lần
            if (isset($votedUsers[$user->id])) {
                continue;
            }
            
            $votedUsers[$user->id] = true;
            $option = $faker->randomElement($options);
            
            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $option->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
