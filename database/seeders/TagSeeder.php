<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo một số tags mẫu
        $tags = [
            'Skyscraper',
            'Megatall',
            'Supertall',
            'Residential',
            'Office',
            'Mixed-use',
            'Hotel',
            'Retail',
            'Under Construction',
            'Proposed',
            'Completed',
            'On Hold',
            'Cancelled',
            'New York',
            'Chicago',
            'Los Angeles',
            'San Francisco',
            'Miami',
            'Houston',
            'Dallas',
            'Boston',
            'Seattle',
            'Philadelphia',
            'Washington DC',
            'Atlanta',
            'Denver',
            'Las Vegas',
            'Phoenix',
            'Portland',
            'San Diego',
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }

        // Gán tags cho các bài đăng hiện có
        $threads = Thread::all();
        $tagIds = Tag::pluck('id')->toArray();

        foreach ($threads as $thread) {
            // Gán ngẫu nhiên 2-5 tags cho mỗi bài đăng
            $randomTagIds = array_rand(array_flip($tagIds), rand(2, 5));
            $thread->tags()->attach($randomTagIds);
        }
    }
}
