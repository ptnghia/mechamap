<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;

class ShowcaseSortTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // New sort options that need translations
            [
                'key' => 'showcase.sort_most_likes',
                'vi' => 'Thích nhiều nhất',
                'en' => 'Most Likes'
            ],
            [
                'key' => 'showcase.sort_most_comments',
                'vi' => 'Bình luận nhiều nhất',
                'en' => 'Most Comments'
            ],
            [
                'key' => 'showcase.sort_most_bookmarks',
                'vi' => 'Đánh dấu nhiều nhất',
                'en' => 'Most Bookmarks'
            ],
            [
                'key' => 'showcase.sort_recently_updated',
                'vi' => 'Cập nhật gần đây',
                'en' => 'Recently Updated'
            ],
            [
                'key' => 'showcase.sort_alphabetical_az',
                'vi' => 'Tên A-Z',
                'en' => 'Name A-Z'
            ],
            [
                'key' => 'showcase.sort_alphabetical_za',
                'vi' => 'Tên Z-A',
                'en' => 'Name Z-A'
            ],
            [
                'key' => 'showcase.sort_most_featured',
                'vi' => 'Nổi bật nhất',
                'en' => 'Most Featured'
            ],
            [
                'key' => 'showcase.sort_trending',
                'vi' => 'Xu hướng',
                'en' => 'Trending'
            ],
            [
                'key' => 'showcase.sort_complexity_low_high',
                'vi' => 'Độ phức tạp: Thấp → Cao',
                'en' => 'Complexity: Low → High'
            ],
            [
                'key' => 'showcase.sort_complexity_high_low',
                'vi' => 'Độ phức tạp: Cao → Thấp',
                'en' => 'Complexity: High → Low'
            ]
        ];

        foreach ($translations as $translation) {
            // Check if translation already exists
            $existingTranslation = Translation::where('key', $translation['key'])->first();

            if (!$existingTranslation) {
                // Create Vietnamese translation
                Translation::create([
                    'key' => $translation['key'],
                    'content' => $translation['vi'],
                    'locale' => 'vi',
                    'group_name' => 'showcase',
                    'is_active' => true
                ]);

                // Create English translation
                Translation::create([
                    'key' => $translation['key'],
                    'content' => $translation['en'],
                    'locale' => 'en',
                    'group_name' => 'showcase',
                    'is_active' => true
                ]);

                $this->command->info("Added translation: {$translation['key']}");
            } else {
                $this->command->info("Translation already exists: {$translation['key']}");
            }
        }

        $this->command->info('Showcase sort translations seeded successfully!');
    }
}
