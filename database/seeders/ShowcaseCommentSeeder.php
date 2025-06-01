<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShowcaseCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample showcase comments
        $comments = [
            [
                'showcase_id' => 1,
                'user_id' => 2,
                'comment' => 'Amazing work! The graphics look incredible.',
                'parent_id' => null,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'showcase_id' => 1,
                'user_id' => 3,
                'comment' => 'How did you achieve this level of detail? Great job!',
                'parent_id' => null,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'showcase_id' => 1,
                'user_id' => 4,
                'comment' => 'Thank you! Used advanced shading techniques.',
                'parent_id' => 2,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'showcase_id' => 2,
                'user_id' => 1,
                'comment' => 'This is exactly what I was looking for. Bookmarked!',
                'parent_id' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'showcase_id' => 2,
                'user_id' => 3,
                'comment' => 'Perfect for my next project. Thanks for sharing!',
                'parent_id' => null,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 2,
                'comment' => 'Outstanding creativity and execution!',
                'parent_id' => null,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 4,
                'comment' => 'Could you share the workflow you used?',
                'parent_id' => null,
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 1,
                'comment' => 'Sure! Will post a tutorial soon.',
                'parent_id' => 7,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
        ];

        DB::table('showcase_comments')->insert($comments);
    }
}
