<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Forum;
use App\Models\Category;
use App\Models\ThreadFollow;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForumNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * Test thread created notification
     */
    public function test_thread_created_notification_sent_to_forum_followers(): void
    {
        // Create users
        $threadCreator = User::factory()->create();
        $forumFollower = User::factory()->create();

        // Create forum and category
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);

        // Create an existing thread that the follower follows
        $existingThread = Thread::factory()->create([
            'forum_id' => $forum->id,
            'category_id' => $category->id,
            'user_id' => $forumFollower->id
        ]);

        // Make user follow the existing thread (simulating forum interest)
        ThreadFollow::create([
            'user_id' => $forumFollower->id,
            'thread_id' => $existingThread->id
        ]);

        // Create new thread by different user
        $newThread = Thread::factory()->create([
            'forum_id' => $forum->id,
            'category_id' => $category->id,
            'user_id' => $threadCreator->id
        ]);

        // Send notification
        $result = NotificationService::sendThreadCreatedNotification($newThread);

        // Assert notification was sent successfully
        $this->assertTrue($result);

        // Assert notification was created in database
        $this->assertDatabaseHas('notifications', [
            'user_id' => $forumFollower->id,
            'type' => 'thread_created',
            'title' => 'Thread mới trong forum bạn quan tâm'
        ]);

        // Assert notification data contains correct information
        $notification = Notification::where('user_id', $forumFollower->id)
            ->where('type', 'thread_created')
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals($newThread->id, $notification->data['thread_id']);
        $this->assertEquals($forum->id, $notification->data['forum_id']);
        $this->assertEquals($threadCreator->id, $notification->data['author_id']);
    }

    /**
     * Test thread replied notification
     */
    public function test_thread_replied_notification_sent_to_thread_followers(): void
    {
        // Create users
        $threadCreator = User::factory()->create();
        $threadFollower = User::factory()->create();
        $commenter = User::factory()->create();

        // Create forum and category
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);

        // Create thread
        $thread = Thread::factory()->create([
            'forum_id' => $forum->id,
            'category_id' => $category->id,
            'user_id' => $threadCreator->id
        ]);

        // Make user follow the thread
        ThreadFollow::create([
            'user_id' => $threadFollower->id,
            'thread_id' => $thread->id
        ]);

        // Create comment
        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $commenter->id,
            'content' => 'This is a test comment'
        ]);

        // Send notification
        $result = NotificationService::sendThreadRepliedNotification($comment);

        // Assert notification was sent successfully
        $this->assertTrue($result);

        // Assert notification was created in database
        $this->assertDatabaseHas('notifications', [
            'user_id' => $threadFollower->id,
            'type' => 'thread_replied'
        ]);

        // Assert commenter doesn't get notification
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $commenter->id,
            'type' => 'thread_replied'
        ]);
    }

    /**
     * Test comment mention notification
     */
    public function test_comment_mention_notification_sent_to_mentioned_users(): void
    {
        // Create users
        $commenter = User::factory()->create();
        $mentionedUser = User::factory()->create(['username' => 'testuser']);

        // Create forum and category
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);

        // Create thread
        $thread = Thread::factory()->create([
            'forum_id' => $forum->id,
            'category_id' => $category->id,
            'user_id' => $commenter->id
        ]);

        // Create comment with mention
        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $commenter->id,
            'content' => 'Hey @testuser, what do you think about this?'
        ]);

        // Extract mentions and send notification
        $mentions = NotificationService::extractMentions($comment->content);
        $result = NotificationService::sendCommentMentionNotification($comment, $mentions);

        // Assert notification was sent successfully
        $this->assertTrue($result);

        // Assert mention was extracted correctly
        $this->assertContains('testuser', $mentions);

        // Assert notification was created in database
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mentionedUser->id,
            'type' => 'comment_mention',
            'priority' => 'high'
        ]);

        // Assert commenter doesn't get notification for their own mention
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $commenter->id,
            'type' => 'comment_mention'
        ]);
    }

    /**
     * Test mention extraction
     */
    public function test_mention_extraction_works_correctly(): void
    {
        $content1 = 'Hello @john and @jane, how are you?';
        $mentions1 = NotificationService::extractMentions($content1);
        $this->assertEquals(['john', 'jane'], $mentions1);

        $content2 = 'No mentions here';
        $mentions2 = NotificationService::extractMentions($content2);
        $this->assertEquals([], $mentions2);

        $content3 = '@user123 and @another_user with @special-chars';
        $mentions3 = NotificationService::extractMentions($content3);
        $this->assertEquals(['user123', 'another_user'], $mentions3);
    }
}
