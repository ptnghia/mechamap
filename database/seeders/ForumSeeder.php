<?php

namespace Database\Seeders;

use App\Models\Forum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main categories
        $categories = [
            [
                'name' => 'General Discussion',
                'description' => 'General discussion about various topics',
            ],
            [
                'name' => 'Technology',
                'description' => 'Discussions about technology, gadgets, and software',
            ],
            [
                'name' => 'Community',
                'description' => 'Community events, announcements, and feedback',
            ],
        ];

        foreach ($categories as $index => $category) {
            $forum = Forum::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'order' => $index,
            ]);

            // Create subforums for each category
            if ($forum->name === 'General Discussion') {
                $this->createSubforums($forum, [
                    [
                        'name' => 'Introductions',
                        'description' => 'Introduce yourself to the community',
                    ],
                    [
                        'name' => 'Off-Topic',
                        'description' => 'Discussions that don\'t fit in other categories',
                    ],
                    [
                        'name' => 'News & Announcements',
                        'description' => 'Latest news and announcements',
                    ],
                ]);
            } elseif ($forum->name === 'Technology') {
                $this->createSubforums($forum, [
                    [
                        'name' => 'Hardware',
                        'description' => 'Discussions about computer hardware and components',
                    ],
                    [
                        'name' => 'Software',
                        'description' => 'Discussions about software and applications',
                    ],
                    [
                        'name' => 'Programming',
                        'description' => 'Programming languages, development, and coding',
                    ],
                    [
                        'name' => 'Mobile',
                        'description' => 'Mobile devices, apps, and technologies',
                    ],
                ]);
            } elseif ($forum->name === 'Community') {
                $this->createSubforums($forum, [
                    [
                        'name' => 'Events',
                        'description' => 'Community events and meetups',
                    ],
                    [
                        'name' => 'Feedback',
                        'description' => 'Provide feedback about the community',
                    ],
                    [
                        'name' => 'Help & Support',
                        'description' => 'Get help and support from the community',
                    ],
                ]);
            }
        }
    }

    /**
     * Create subforums for a parent forum.
     */
    private function createSubforums(Forum $parent, array $subforums): void
    {
        foreach ($subforums as $index => $subforum) {
            Forum::create([
                'name' => $subforum['name'],
                'slug' => Str::slug($subforum['name']),
                'description' => $subforum['description'],
                'parent_id' => $parent->id,
                'order' => $index,
            ]);
        }
    }
}
