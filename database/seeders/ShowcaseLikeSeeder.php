<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShowcaseLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample showcase likes
        $likes = [
            [
                'showcase_id' => 1,
                'user_id' => 2,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'showcase_id' => 1,
                'user_id' => 3,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'showcase_id' => 1,
                'user_id' => 4,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'showcase_id' => 2,
                'user_id' => 1,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'showcase_id' => 2,
                'user_id' => 3,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'showcase_id' => 2,
                'user_id' => 4,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 2,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 4,
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            // Additional likes for popularity simulation
            [
                'showcase_id' => 1,
                'user_id' => 5,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'showcase_id' => 2,
                'user_id' => 5,
                'created_at' => Carbon::now()->subHours(18),
                'updated_at' => Carbon::now()->subHours(18),
            ],
            [
                'showcase_id' => 3,
                'user_id' => 5,
                'created_at' => Carbon::now()->subHours(3),
                'updated_at' => Carbon::now()->subHours(3),
            ],
        ];

        DB::table('showcase_likes')->insert($likes);
    }
}
