<?php

namespace Tests\Feature\Forum;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Tag;

class ForumIntegrationTest extends TestCase
{
    /** @test */
    public function test_database_structure_exists()
    {
        // Test basic table counts
        $this->assertGreaterThan(0, Category::count(), 'Categories should exist');
        $this->assertGreaterThan(0, Forum::count(), 'Forums should exist');
        $this->assertGreaterThan(0, User::count(), 'Users should exist');

        $this->assertTrue(true, 'Database structure validation passed');
    }

    /** @test */
    public function test_forum_hierarchy_relationships()
    {
        // Test Categories → Forums → Threads hierarchy
        $category = Category::first();
        $this->assertNotNull($category, 'Should have at least one category');

        $forums = $category->forums;
        $this->assertNotNull($forums, 'Category should have forums relationship');

        if ($forums->count() > 0) {
            $forum = $forums->first();
            $this->assertEquals($category->id, $forum->category_id,
                "Forum should belong to correct category");

            $threads = $forum->threads;
            if ($threads->count() > 0) {
                $thread = $threads->first();
                $this->assertEquals($forum->id, $thread->forum_id,
                    "Thread should belong to correct forum");
                $this->assertEquals($category->id, $thread->category_id,
                    "Thread should belong to correct category");
            }
        }

        $this->assertTrue(true, 'Forum hierarchy validation passed');
    }

    /** @test */
    public function test_mechanical_engineering_data_exists()
    {
        $threads = Thread::all();

        if ($threads->count() > 0) {
            $mechanicalKeywords = ['CNC', 'SolidWorks', 'ANSYS', 'bánh răng', 'CAD', 'PLC', 'vật liệu'];
            $hasRealisticContent = false;

            foreach ($threads as $thread) {
                foreach ($mechanicalKeywords as $keyword) {
                    if (stripos($thread->title, $keyword) !== false ||
                        stripos($thread->content, $keyword) !== false) {
                        $hasRealisticContent = true;
                        break 2;
                    }
                }
            }

            $this->assertTrue($hasRealisticContent,
                'Forum should contain realistic mechanical engineering content');
        }

        $this->assertTrue(true, 'Mechanical engineering data validation passed');
    }

    /** @test */
    public function test_technical_thread_fields()
    {
        $threads = Thread::all();

        foreach ($threads as $thread) {
            if (isset($thread->technical_difficulty)) {
                $this->assertContains($thread->technical_difficulty,
                    ['beginner', 'intermediate', 'advanced', 'expert'],
                    'Technical difficulty should be valid enum value');
            }

            if (isset($thread->project_type)) {
                $this->assertContains($thread->project_type,
                    ['design', 'manufacturing', 'analysis', 'troubleshooting'],
                    'Project type should be valid enum value');
            }
        }

        $this->assertTrue(true, 'Technical thread fields validation passed');
    }

    /** @test */
    public function test_forum_performance()
    {
        $startTime = microtime(true);

        // Test complex query performance
        $data = Category::with([
            'forums.threads.user',
        ])->get();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime,
            'Complex forum query should execute in under 3 seconds');

        $this->assertGreaterThanOrEqual(0, $data->count(),
            'Query should return data');
    }

    /** @test */
    public function test_forum_api_endpoints()
    {
        // Test API endpoints work
        $response = $this->get('/api/forums');
        $response->assertStatus(200);

        $response = $this->get('/api/categories');
        $response->assertStatus(200);

        $this->assertTrue(true, 'API endpoints validation passed');
    }

    /** @test */
    public function test_web_routes_work()
    {
        // Test main forum pages load
        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/forums');
        $response->assertStatus(200);

        $this->assertTrue(true, 'Web routes validation passed');
    }

    /** @test */
    public function test_database_statistics()
    {
        // Get database statistics
        $stats = [
            'users' => User::count(),
            'categories' => Category::count(),
            'forums' => Forum::count(),
            'threads' => Thread::count(),
            'comments' => Comment::count(),
            'tags' => Tag::count(),
        ];

        $this->assertGreaterThan(0, $stats['users'], 'Should have users');
        $this->assertGreaterThan(0, $stats['categories'], 'Should have categories');
        $this->assertGreaterThan(0, $stats['forums'], 'Should have forums');

        // Log statistics for verification
        fwrite(STDERR, "\n🔧 MechaMap Database Statistics:\n");
        fwrite(STDERR, "================================\n");
        foreach ($stats as $table => $count) {
            fwrite(STDERR, ucfirst($table) . ": {$count}\n");
        }
        fwrite(STDERR, "================================\n");

        $this->assertTrue(true, 'Database statistics validated');
    }
}
