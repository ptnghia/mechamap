<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShowcaseFollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample user follows (user relationships)
        $follows = [
            [
                'follower_id' => 2, // User 2 follows User 1
                'following_id' => 1,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'follower_id' => 3, // User 3 follows User 1
                'following_id' => 1,
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'follower_id' => 4, // User 4 follows User 1
                'following_id' => 1,
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'follower_id' => 1, // User 1 follows User 2
                'following_id' => 2,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'follower_id' => 3, // User 3 follows User 2
                'following_id' => 2,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'follower_id' => 1, // User 1 follows User 3
                'following_id' => 3,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'follower_id' => 2, // User 2 follows User 3
                'following_id' => 3,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'follower_id' => 4, // User 4 follows User 3
                'following_id' => 3,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'follower_id' => 1, // User 1 follows User 4
                'following_id' => 4,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'follower_id' => 2, // User 2 follows User 4
                'following_id' => 4,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'follower_id' => 5, // User 5 follows User 1
                'following_id' => 1,
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            [
                'follower_id' => 5, // User 5 follows User 2
                'following_id' => 2,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'follower_id' => 5, // User 5 follows User 3
                'following_id' => 3,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ];

        DB::table('showcase_follows')->insert($follows);
    }
}
