<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

/**
 * Test comment attachments workflow
 */
class TestCommentAttachments extends Command
{
    protected $signature = 'test:comment-attachments';
    protected $description = 'Test comment attachments workflow';

    public function handle()
    {
        $this->info('Testing Comment Attachments Workflow...');

        // 1. Tìm một thread để test
        $thread = Thread::first();
        if (!$thread) {
            $this->error('No threads found!');
            return;
        }

        // 2. Tìm user để test
        $user = User::first();
        if (!$user) {
            $this->error('No users found!');
            return;
        }

        $this->info("Using Thread: {$thread->title}");
        $this->info("Using User: {$user->name}");

        // 3. Tạo comment mới với attachment giả lập
        $comment = new Comment([
            'content' => 'Test comment với hình ảnh đính kèm được tạo từ artisan command.',
            'user_id' => $user->id,
            'has_media' => true
        ]);

        $thread->comments()->save($comment);

        // 4. Tạo media attachment giả lập
        $testImageUrl = 'https://picsum.photos/800/600';
        $media = $comment->attachments()->create([
            'user_id' => $user->id,
            'file_path' => $testImageUrl,
            'file_name' => 'test_image_from_command.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 150000,
        ]);

        $this->info("Created comment ID: {$comment->id}");
        $this->info("Created media ID: {$media->id}");

        // 5. Test view conditions
        $this->info("\n=== Testing View Conditions ===");
        $this->info("has_media: " . ($comment->has_media ? 'true' : 'false'));
        $this->info("attachments count: " . $comment->attachments->count());
        $this->info("View condition passes: " . ($comment->has_media && $comment->attachments->count() > 0 ? 'YES' : 'NO'));

        // 6. Test URL generation
        $attachment = $comment->attachments->first();
        $this->info("\n=== Testing URL Generation ===");
        $this->info("File path: {$attachment->file_path}");
        $this->info("Generated URL: {$attachment->url}");
        $this->info("Is valid URL: " . (filter_var($attachment->url, FILTER_VALIDATE_URL) ? 'YES' : 'NO'));

        // 7. Show thread URL for manual testing
        $this->info("\n=== Manual Testing ===");
        $this->info("Visit this URL to see the comment with attachment:");
        $this->info(config('app.url') . "/threads/{$thread->id}#comment-{$comment->id}");

        $this->info('Test completed successfully!');
    }
}
