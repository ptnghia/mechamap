<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use Carbon\Carbon;

class RealisticTimelineSeeder extends Seeder
{
    /**
     * Cập nhật timeline thực tế cho MechaMap
     * - User created_at, last_seen_at
     * - Thread created_at, updated_at
     * - Comment created_at
     * - Interaction timestamps
     */
    public function run(): void
    {
        $this->command->info('📅 Bắt đầu cập nhật timeline thực tế...');

        // Timeline: 6 tháng trước đến hiện tại
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Cập nhật users timeline
        $this->updateUsersTimeline($startDate, $endDate);
        
        // Cập nhật threads timeline
        $this->updateThreadsTimeline($startDate, $endDate);
        
        // Cập nhật comments timeline
        $this->updateCommentsTimeline($startDate, $endDate);
        
        // Cập nhật interactions timeline
        $this->updateInteractionsTimeline($startDate, $endDate);

        $this->command->info('✅ Hoàn thành cập nhật timeline!');
    }

    /**
     * Cập nhật timeline cho users
     */
    private function updateUsersTimeline($startDate, $endDate): void
    {
        $this->command->info('👥 Cập nhật users timeline...');
        
        $users = User::all();
        
        foreach ($users as $user) {
            // Ngày đăng ký: phân bố đều trong 6 tháng
            $createdAt = $this->randomDateBetween($startDate, $endDate->copy()->subDays(7));
            
            // Last seen: từ ngày đăng ký đến hiện tại
            $lastSeenAt = $this->randomDateBetween($createdAt, $endDate);
            
            // Một số user inactive (10%)
            if (rand(1, 100) <= 10) {
                $lastSeenAt = $this->randomDateBetween($createdAt, $createdAt->copy()->addDays(30));
            }
            
            $user->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'last_seen_at' => $lastSeenAt,
            ]);
        }
        
        $this->command->info("   ✓ Cập nhật " . $users->count() . " users");
    }

    /**
     * Cập nhật timeline cho threads
     */
    private function updateThreadsTimeline($startDate, $endDate): void
    {
        $this->command->info('📝 Cập nhật threads timeline...');
        
        $threads = Thread::with('user')->get();
        
        foreach ($threads as $thread) {
            // Thread được tạo sau khi user đăng ký
            $userCreatedAt = Carbon::parse($thread->user->created_at);
            $threadStartDate = $userCreatedAt->copy()->addDays(rand(1, 30));
            
            // Đảm bảo không vượt quá endDate
            if ($threadStartDate->gt($endDate)) {
                $threadStartDate = $endDate->copy()->subDays(rand(1, 7));
            }
            
            $createdAt = $this->randomDateBetween($threadStartDate, $endDate);
            
            // Updated_at có thể sau created_at (edit)
            $updatedAt = $createdAt->copy();
            if (rand(1, 100) <= 30) { // 30% chance được edit
                $updatedAt = $this->randomDateBetween($createdAt, $endDate);
            }
            
            $thread->update([
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }
        
        $this->command->info("   ✓ Cập nhật " . $threads->count() . " threads");
    }

    /**
     * Cập nhật timeline cho comments
     */
    private function updateCommentsTimeline($startDate, $endDate): void
    {
        $this->command->info('💬 Cập nhật comments timeline...');
        
        $comments = Comment::with(['user', 'thread'])->get();
        
        foreach ($comments as $comment) {
            // Comment được tạo sau thread
            $threadCreatedAt = Carbon::parse($comment->thread->created_at);
            $userCreatedAt = Carbon::parse($comment->user->created_at);
            
            // Bắt đầu từ thời điểm muộn hơn
            $commentStartDate = $threadCreatedAt->gt($userCreatedAt) 
                ? $threadCreatedAt->copy()->addMinutes(rand(30, 1440)) // 30 phút đến 1 ngày sau thread
                : $userCreatedAt->copy()->addDays(rand(1, 7));
            
            if ($commentStartDate->gt($endDate)) {
                $commentStartDate = $endDate->copy()->subHours(rand(1, 24));
            }
            
            $createdAt = $this->randomDateBetween($commentStartDate, $endDate);
            
            $comment->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
        
        $this->command->info("   ✓ Cập nhật " . $comments->count() . " comments");
    }

    /**
     * Cập nhật timeline cho interactions
     */
    private function updateInteractionsTimeline($startDate, $endDate): void
    {
        $this->command->info('❤️ Cập nhật interactions timeline...');
        
        // Thread likes
        $threadLikes = DB::table('thread_likes')->get();
        foreach ($threadLikes as $like) {
            $thread = Thread::find($like->thread_id);
            if ($thread) {
                $threadCreatedAt = Carbon::parse($thread->created_at);
                $likeDate = $this->randomDateBetween($threadCreatedAt, $endDate);
                
                DB::table('thread_likes')
                    ->where('id', $like->id)
                    ->update([
                        'created_at' => $likeDate,
                        'updated_at' => $likeDate,
                    ]);
            }
        }
        
        // Thread follows
        $threadFollows = DB::table('thread_follows')->get();
        foreach ($threadFollows as $follow) {
            $thread = Thread::find($follow->thread_id);
            if ($thread) {
                $threadCreatedAt = Carbon::parse($thread->created_at);
                $followDate = $this->randomDateBetween($threadCreatedAt, $endDate);
                
                DB::table('thread_follows')
                    ->where('id', $follow->id)
                    ->update([
                        'created_at' => $followDate,
                        'updated_at' => $followDate,
                    ]);
            }
        }
        
        // Thread saves
        $threadSaves = DB::table('thread_saves')->get();
        foreach ($threadSaves as $save) {
            $thread = Thread::find($save->thread_id);
            if ($thread) {
                $threadCreatedAt = Carbon::parse($thread->created_at);
                $saveDate = $this->randomDateBetween($threadCreatedAt, $endDate);
                
                DB::table('thread_saves')
                    ->where('id', $save->id)
                    ->update([
                        'created_at' => $saveDate,
                        'updated_at' => $saveDate,
                    ]);
            }
        }
        
        // Comment likes
        $commentLikes = DB::table('comment_likes')->get();
        foreach ($commentLikes as $like) {
            $comment = Comment::find($like->comment_id);
            if ($comment) {
                $commentCreatedAt = Carbon::parse($comment->created_at);
                $likeDate = $this->randomDateBetween($commentCreatedAt, $endDate);
                
                DB::table('comment_likes')
                    ->where('id', $like->id)
                    ->update([
                        'created_at' => $likeDate,
                        'updated_at' => $likeDate,
                    ]);
            }
        }
        
        $this->command->info("   ✓ Cập nhật interactions timeline");
    }

    /**
     * Tạo ngày random giữa 2 mốc thời gian
     */
    private function randomDateBetween($startDate, $endDate)
    {
        $start = $startDate->timestamp;
        $end = $endDate->timestamp;
        
        $randomTimestamp = rand($start, $end);
        
        return Carbon::createFromTimestamp($randomTimestamp);
    }
}
