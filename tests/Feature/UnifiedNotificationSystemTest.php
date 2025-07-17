<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\UnifiedNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class UnifiedNotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function it_can_send_unified_notification()
    {
        $result = UnifiedNotificationService::send(
            $this->user,
            'test_notification',
            'Test Title',
            'Test Message',
            ['test' => true],
            ['database']
        );

        $this->assertTrue($result);
        
        // Check custom notification was created
        $this->assertDatabaseHas('custom_notifications', [
            'user_id' => $this->user->id,
            'type' => 'test_notification',
            'title' => 'Test Title',
            'message' => 'Test Message',
        ]);
    }

    /** @test */
    public function it_can_get_user_notifications()
    {
        // Create test notifications
        UnifiedNotificationService::send(
            $this->user,
            'test_type_1',
            'Title 1',
            'Message 1',
            ['data' => 1]
        );

        UnifiedNotificationService::send(
            $this->user,
            'test_type_2',
            'Title 2',
            'Message 2',
            ['data' => 2]
        );

        $notifications = UnifiedNotificationService::getUserNotifications($this->user, 1, 10);

        $this->assertCount(2, $notifications);
        $this->assertEquals('Title 2', $notifications->first()['title']); // Latest first
        $this->assertEquals('custom', $notifications->first()['source']);
    }

    /** @test */
    public function it_can_get_unread_count()
    {
        // Create unread notifications
        UnifiedNotificationService::send($this->user, 'test1', 'Title 1', 'Message 1');
        UnifiedNotificationService::send($this->user, 'test2', 'Title 2', 'Message 2');

        $count = UnifiedNotificationService::getUnreadCount($this->user);

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        UnifiedNotificationService::send($this->user, 'test', 'Title', 'Message');
        
        $notification = $this->user->userNotifications()->first();
        $this->assertFalse($notification->is_read);

        $result = UnifiedNotificationService::markAsRead($this->user, $notification->id, 'custom');

        $this->assertTrue($result);
        $this->assertTrue($notification->fresh()->is_read);
    }

    /** @test */
    public function it_can_mark_all_notifications_as_read()
    {
        // Create multiple unread notifications
        UnifiedNotificationService::send($this->user, 'test1', 'Title 1', 'Message 1');
        UnifiedNotificationService::send($this->user, 'test2', 'Title 2', 'Message 2');
        UnifiedNotificationService::send($this->user, 'test3', 'Title 3', 'Message 3');

        $this->assertEquals(3, UnifiedNotificationService::getUnreadCount($this->user));

        $result = UnifiedNotificationService::markAllAsRead($this->user);

        $this->assertTrue($result);
        $this->assertEquals(0, UnifiedNotificationService::getUnreadCount($this->user));
    }

    /** @test */
    public function it_can_get_notification_stats()
    {
        // Create test notifications
        UnifiedNotificationService::send($this->user, 'test1', 'Title 1', 'Message 1');
        UnifiedNotificationService::send($this->user, 'test2', 'Title 2', 'Message 2');

        $stats = UnifiedNotificationService::getStats($this->user);

        $this->assertArrayHasKey('custom', $stats);
        $this->assertArrayHasKey('laravel', $stats);
        $this->assertArrayHasKey('unified', $stats);
        
        $this->assertEquals(2, $stats['custom']['total']);
        $this->assertEquals(2, $stats['custom']['unread']);
        $this->assertEquals(2, $stats['unified']['total']);
    }

    /** @test */
    public function it_logs_notifications()
    {
        UnifiedNotificationService::send(
            $this->user,
            'test_logged',
            'Logged Title',
            'Logged Message',
            ['logged' => true],
            ['database']
        );

        $this->assertDatabaseHas('notification_logs', [
            'notifiable_id' => $this->user->id,
            'type' => 'test_logged',
            'channel' => 'database',
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function api_count_endpoint_works()
    {
        $this->actingAs($this->user);

        UnifiedNotificationService::send($this->user, 'test', 'Title', 'Message');

        $response = $this->getJson('/api/v1/unified-notifications/count');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'count' => 1,
                    'unread_count' => 1,
                ]);
    }

    /** @test */
    public function api_recent_endpoint_works()
    {
        $this->actingAs($this->user);

        UnifiedNotificationService::send($this->user, 'test', 'Test Title', 'Test Message');

        $response = $this->getJson('/api/v1/unified-notifications/recent?limit=5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'notifications' => [
                        '*' => [
                            'id',
                            'type',
                            'title',
                            'message',
                            'data',
                            'is_read',
                            'created_at',
                            'source'
                        ]
                    ],
                    'total_unread'
                ]);
    }

    /** @test */
    public function api_stats_endpoint_works()
    {
        $this->actingAs($this->user);

        UnifiedNotificationService::send($this->user, 'test', 'Title', 'Message');

        $response = $this->getJson('/api/v1/unified-notifications/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'custom' => ['total', 'unread'],
                        'laravel' => ['total', 'unread'],
                        'unified' => ['total', 'unread'],
                    ]
                ]);
    }

    /** @test */
    public function api_mark_as_read_endpoint_works()
    {
        $this->actingAs($this->user);

        UnifiedNotificationService::send($this->user, 'test', 'Title', 'Message');
        $notification = $this->user->userNotifications()->first();

        $response = $this->postJson('/api/v1/unified-notifications/mark-as-read', [
            'notification_id' => $notification->id,
            'source' => 'custom'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Notification marked as read successfully'
                ]);

        $this->assertTrue($notification->fresh()->is_read);
    }

    /** @test */
    public function api_mark_all_as_read_endpoint_works()
    {
        $this->actingAs($this->user);

        UnifiedNotificationService::send($this->user, 'test1', 'Title 1', 'Message 1');
        UnifiedNotificationService::send($this->user, 'test2', 'Title 2', 'Message 2');

        $response = $this->postJson('/api/v1/unified-notifications/mark-all-as-read');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'All notifications marked as read successfully'
                ]);

        $this->assertEquals(0, UnifiedNotificationService::getUnreadCount($this->user));
    }

    /** @test */
    public function api_send_test_endpoint_works()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/unified-notifications/send-test', [
            'type' => 'api_test',
            'title' => 'API Test Title',
            'message' => 'API Test Message',
            'channels' => ['database']
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Test notification sent successfully'
                ]);

        $this->assertDatabaseHas('custom_notifications', [
            'user_id' => $this->user->id,
            'type' => 'api_test',
            'title' => 'API Test Title',
        ]);
    }
}
